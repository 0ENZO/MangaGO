# ECS_API
resource "aws_ecr_repository" "api" {
  name = "ecr_api"
  image_tag_mutability = "MUTABLE"
  image_scanning_configuration {
    scan_on_push = true
  }
}

resource "aws_ecr_repository_policy" "api" {
  repository = aws_ecr_repository.api.name
  policy     = jsonencode({
    Version = "2008-10-17"
    Statement = [
      {
        Sid    = "AllowPull"
        Effect = "Allow"
        Principal = "*"
        Action = [
          "ecr:*"
        ]
      }
    ]
  })
}

resource "aws_ecs_cluster" "api" {
  name = "ecs_cluster_api"
  depends_on = [
    aws_subnet.main,
    aws_security_group.api
  ]
}

resource "aws_ecs_service" "api" {
  name            = "ecs-service-api"
  cluster         = aws_ecs_cluster.api.id
  task_definition = aws_ecs_task_definition.api.arn
  launch_type     = "FARGATE"
  desired_count   = 1
  network_configuration {
    subnets          = [aws_subnet.main.id, aws_subnet.secondary.id]
    security_groups  = [aws_security_group.api.id]
    assign_public_ip = true
  }
  load_balancer {
    target_group_arn = aws_lb_target_group.api.arn
    container_name   = "ecs_container_api"
    container_port   = var.ecs_port_api
  }
}

resource "aws_ecs_task_definition" "api" {
  family                = "ecs_task_api"
  requires_compatibilities = ["FARGATE"]
  network_mode         = var.ecs_network_mode
  cpu                  = var.ecs_cpu
  memory               = var.ecs_memory
  execution_role_arn   = aws_iam_role.ecs_execution_role.arn
  task_role_arn        = aws_iam_role.ecs_execution_role.arn
  container_definitions = jsonencode([
    {
      name  = "ecs_container_api"
      image = "${aws_ecr_repository.api.repository_url}:latest"

      portMappings = [
        {
          containerPort = var.ecs_port_api
          hostPort      = var.ecs_port_api
          protocol      = "tcp"
        }
      ]

      environment = [
        {
          name  = "DB_HOST"
          value = aws_db_instance.rds.address
        },
        {
          name  = "DB_USER"
          value = aws_db_instance.rds.username
        },
        {
          name  = "DB_PASSWORD"
          value = aws_db_instance.rds.password
        },
        {
          name  = "DB_NAME"
          value = aws_db_instance.rds.name
        },
        {
            name  = "AWS_NAME_BUCKET"
            value = var.name_bucket_s3
        },
        {
            name  = "S3_BUCKET_PATH"
            value = aws_s3_bucket.s3.bucket_regional_domain_name
        }
      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group" = "/ecs/api"
          "awslogs-region" = var.aws_region
          "awslogs-stream-prefix" = "ecs"
        }
      }
    }
  ])

  depends_on = [null_resource.build_and_push_docker_image_api, aws_db_instance.rds]
}

resource "aws_cloudwatch_log_group" "api" {
  name = "/ecs/api"
  retention_in_days = 7
}

resource "aws_security_group" "api" {
  name_prefix = "security_group_api"
  description = "Example security group for outgoing HTTP and HTTPS traffic for api"
  vpc_id      = aws_vpc.main.id

  egress {
    from_port       = 0
    to_port         = 0
    protocol        = "-1"
    cidr_blocks     = ["0.0.0.0/0"]
    description     = "Allow all outbound traffic"
  }

  ingress {
    from_port       = var.ecs_port_api
    to_port         = var.ecs_port_api
    protocol        = "tcp"
    cidr_blocks     = ["0.0.0.0/0"]
    description     = "API"
  }
}

resource "null_resource" "build_and_push_docker_image_api" {
  provisioner "local-exec" {
    command = var.command_build_api
  }
}

resource "aws_lb" "api" {
  name               = "api-load-balancer"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.alb.id]
  subnets            = [aws_subnet.main.id, aws_subnet.secondary.id]
}

resource "aws_lb_target_group" "api" {
  name        = "api-target-group"
  port        = var.ecs_port_api
  protocol    = "HTTP"
  vpc_id      = aws_vpc.main.id
  target_type = "ip"

  health_check {
    path = var.lb_path_healthcheck
  }
}

resource "aws_lb_listener" "api" {
  load_balancer_arn = aws_lb.api.arn
  port              = var.ecs_port_api
  protocol          = "HTTP"

  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.api.arn
  }
}

resource "aws_security_group" "alb" {
  name        = "alb-security-group"
  description = "Allow inbound traffic to the ALB"
  vpc_id      = aws_vpc.main.id

  ingress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}