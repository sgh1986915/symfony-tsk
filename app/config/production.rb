set :deploy_to, "/home/ec2-user/sites/erp.tsk.com"

server "tskaws", :app, :web, :primary => true
