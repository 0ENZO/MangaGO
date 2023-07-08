# ECS_PHP
resource "aws_ecr_repository" "php" {
  name = "ecr_php"
  image_tag_mutability = "MUTABLE"
  image_scanning_configuration {
    scan_on_push = true
  }
}

resource "aws_ecr_repository_policy" "php" {
  repository = aws_ecr_repository.php.name
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

resource "aws_ecs_cluster" "php" {
  name = "ecs_cluster_php"
  depends_on = [
    aws_subnet.main,
    aws_security_group.php
  ]
}

resource "aws_ecs_service" "php" {
  name            = "ecs-service-php"
  cluster         = aws_ecs_cluster.php.id
  task_definition = aws_ecs_task_definition.php.arn
  launch_type     = "FARGATE"
  desired_count   = 1
  network_configuration {
      subnets          = [aws_subnet.main.id]
      security_groups  = [aws_security_group.php.id]
      assign_public_ip = true
  }
}

resource "aws_ecs_task_definition" "php" {
  family                = "ecs_task_php"
  requires_compatibilities = ["FARGATE"]
  network_mode         = var.ecs_network_mode
  cpu                  = var.ecs_cpu
  memory               = var.ecs_memory
  execution_role_arn   = aws_iam_role.ecs_execution_role.arn
  task_role_arn        = aws_iam_role.ecs_execution_role.arn
  container_definitions = jsonencode([
    {
      name  = "ecs_container_php"
      image = "${aws_ecr_repository.php.repository_url}:latest"

      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
          protocol      = "tcp"
        }
      ]

      environment = [
        {
          name  = "API_ADRESSE"
          value = "${aws_lb.api.dns_name}:${var.ecs_port_api}"
        },
        {
          name  = "S3_BUCKET_PATH"
          value = aws_s3_bucket.s3.bucket_regional_domain_name
        },
        {
          name  = "AWS_REGION"
          value = var.aws_region
        },
        {
          name  = "AWS_ACCES_KEY"
          value = var.access_key
        },
        {
          name  = "AWS_SECRET_KEY"
          value = var.secret_key
        },
        {
          name  = "AWS_NAME_BUCKET"
          value = var.name_bucket_s3
        }
      ]
    }
  ])

  depends_on = [null_resource.build_and_push_docker_image_php, aws_lb.api, aws_s3_bucket.s3]
}

resource "aws_security_group" "php" {
  name_prefix = "security_group_php"
  description = "Example security group for outgoing HTTP and HTTPS traffic for php"
  vpc_id      = aws_vpc.main.id

  egress {
    from_port       = 0
    to_port         = 0
    protocol        = "-1"
    cidr_blocks     = ["0.0.0.0/0"]
    description     = "Allow all outbound traffic"
  }

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Autoriser le trafic HTTP entrant"
  }

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
    description = "Autoriser le trafic HTTPS entrant"
  }
}

resource "null_resource" "build_and_push_docker_image_php" {
  provisioner "local-exec" {
    command = var.command_build_php
  }
}