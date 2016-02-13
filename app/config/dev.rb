set :deploy_to, "/home/ec2-user/sites/erp-dev.tsk.com"

server "tskaws", :app, :web, :primary => false
