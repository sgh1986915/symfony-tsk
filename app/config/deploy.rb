set :application, "TSK App"
# set :deploy_to,   "/home/ec2-user/sites/erp.tsk.com"
set :deploy_to, "/virtuals/erp.tsk.com"
# set :domain,      "tskaws"
set :domain,      "mail"
set :cache_warmup,          false

set :stages,        %w(production staging dev)
set :default_stage, "staging"
set :stage_dir,     "app/config"
require 'capistrano/ext/multistage'

set :php_bin,           "php -dmemory_limit=1G"
# set :app_path,    "app"

set :scm,         :git
set :repository,  "git@bitbucket.org:wkhoury/tsk-erp-system.git"
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain                         # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Symfony2 migrations will run

set  :keep_releases,  3
set  :use_sudo,       false

set :use_composer, true
set :composer_options,      "--verbose --prefer-dist"
set :update_vendors, true

set :writable_dirs,     ["app/cache", "app/logs"]
# set :user, "ec2-user"
set :user, "mhill"
# set :webserver_user, "apache"
set :webserver_user, "daemon"
set :permission_method, :acl
set :use_set_permissions, true
set :dump_assetic_assets, true
set :shared_children, [app_path + "/logs", web_path + "/uploads"]

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL
#
namespace :deploy do
    desc "Symlink Bootstrap"
    task :symlink_bootstrap do
        run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} mopa:bootstrap:symlink:less'"
    end
end
after "deploy:symlink_bootstrap" do
    puts "--> Bootstrap Symlinked!".green
end
before 'symfony:cache:warmup', 'symfony:hard_clear_cache'

# Symfony2 2.1
# before "symfony:vendors:install", "symfony:copy_vendors"
before 'symfony:composer:update', 'symfony:copy_vendors'

namespace :symfony do
  desc "Copy vendors from previous release"
  task :copy_vendors, :except => { :no_release => true } do
    if Capistrano::CLI.ui.agree("Do you want to copy last release vendor dir then do composer install ?: (y/N)")
      capifony_pretty_print "--> Copying vendors from previous release"

      run "cp -a #{previous_release}/vendor #{latest_release}/"
      capifony_puts_ok
    end
  end

  desc "Hard clear cache"
  task :hard_clear_cache, :except => { :no_release => true } do
    capifony_pretty_print "--> Hard clearing the production cache"
    run "#{try_sudo} sh -c 'cd #{latest_release} && rm -rf app/cache/prod/*'"
    capifony_puts_ok
  end
end

# after "deploy:finalize_update", "deploy:symlink_bootstrap"
before 'symfony:assetic:dump', 'deploy:symlink_bootstrap'
