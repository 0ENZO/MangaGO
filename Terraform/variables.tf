variable "name_projet" {
    default = "MangaGO"
}

variable "env" {
    default = "dev"
}

variable "aws_region" {
    default = "us-east-1"
}

variable "aws_profile" {
    default = "default"
}

variable "local_path" {
    default = "/Users/fabienbarrios/Desktop/Import/"
}

variable "ecs_cpu" {
    default = 2048 //4096
}

variable "ecs_memory" {
    default = 4096 //8192
}

variable "ecs_network_mode" {
    default = "awsvpc"
}

variable "ecs_port_api" {
    default = 5000
}

variable "lb_path_healthcheck" {
    default = "/healthcheck"
}

variable "storage_rds" {
    default = 20
}

variable "storage_type" {
    default = "gp2"
}

variable "instance_class" {
    default = "db.t2.micro"
}

variable "engine_version" {
    default = "8.0.32"
}

variable "engine" {
    default = "mysql"
}

variable "name_db" {
    default = "MangaGO"
}

variable "port_db" {
    default = 3306
}

variable "command_build_php" {
    default = "chmod 755 build_and_push_php.sh && ./build_and_push_php.sh"
}

variable "command_build_api" {
    default = "chmod 755 build_and_push_api.sh && ./build_and_push_api.sh"
}

variable "name_bucket_s3" {
  default = "bucket-mangago-esgi"
}
