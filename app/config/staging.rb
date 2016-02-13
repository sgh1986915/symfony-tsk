set :deploy_to, "/virtuals/erp.tsk.com"
set :user, "mhill"
set :domain, "mail"
set :webserver_user, "daemon"
set :branch, "costin"

server "mail", :app, :web, :primary => true
