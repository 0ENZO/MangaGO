resource "aws_db_subnet_group" "rds" {
  name       = "rds-subnet-group"
  subnet_ids = [aws_subnet.main.id, aws_subnet.secondary.id]

  tags = {
    Name = "rds-subnet-group"
  }
}

resource "aws_security_group" "rds" {
  name        = "rds_security_group"
  description = "Security group for RDS instance"
  vpc_id      = aws_vpc.main.id

  ingress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "MySQL ingress rule"
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Allow all outbound traffic"
  }
}

resource "aws_db_instance" "rds" {
  identifier           = "rds-mangago"
  engine               = var.engine
  engine_version       = var.engine_version
  instance_class       = var.instance_class
  storage_type         = var.storage_type
  allocated_storage    = var.storage_rds
  name                 = var.name_db
  username             = var.login_db
  password             = var.mdp_db
  port                 = var.port_db
  skip_final_snapshot  = true
  vpc_security_group_ids = [aws_security_group.rds.id]
  db_subnet_group_name = aws_db_subnet_group.rds.name
  multi_az             = false
  publicly_accessible  = true
  deletion_protection  = false

  provisioner "local-exec" {
    command = "chmod 755 run_sql_commands.sh && ./run_sql_commands.sh ${aws_db_instance.rds.address} ${aws_db_instance.rds.username} ${aws_db_instance.rds.password}  ${aws_db_instance.rds.name}"
  }
  
  tags = {
    Name = "RDS_MangaGO"
  }
}
