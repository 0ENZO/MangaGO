data "aws_iam_policy_document" "s3_policy" {
  statement {
    actions   = ["s3:GetObject"]
    resources = ["arn:aws:s3:::bucket-mangago-esgi/scan_brut/*"]
  }

  statement {
    actions   = ["s3:PutObject"]
    resources = ["arn:aws:s3:::bucket-mangago-esgi/scan_preprocess/*"]
  }

  statement {
    effect = "Allow"
    actions = [
      "logs:CreateLogGroup",
      "logs:CreateLogStream",
      "logs:PutLogEvents"
    ]
    resources = ["arn:aws:logs:*:*:*"]
  }
}

resource "aws_iam_role" "iam_for_lambda" {
  name = "iam_for_lambda"

  assume_role_policy = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Action = "sts:AssumeRole",
        Effect = "Allow",
        Principal = {
          Service = "lambda.amazonaws.com"
        }
      }
    ]
  })
}

resource "aws_iam_role_policy" "iam_for_lambda_policy" {
  name = "iam_for_lambda_policy"
  role = aws_iam_role.iam_for_lambda.id

  policy = data.aws_iam_policy_document.s3_policy.json
}

resource "aws_ecr_repository" "lambda" {
  name = "ecr_lambda"
  image_tag_mutability = "MUTABLE"
  image_scanning_configuration {
    scan_on_push = true
  }
}

resource "aws_ecr_repository_policy" "lambda" {
  repository = aws_ecr_repository.lambda.name
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

resource "null_resource" "build_and_push_docker_image_lambda" {
  provisioner "local-exec" {
    command = "chmod 755 build_and_push_lambda.sh && ./build_and_push_lambda.sh"
  }
}

resource "aws_lambda_function" "lambda_function" {
  function_name = "tesseract_image_processing"
  role          = aws_iam_role.iam_for_lambda.arn
  image_uri     = "${aws_ecr_repository.lambda.repository_url}:latest"
  timeout       = 240
  memory_size   = 3008
  package_type  = "Image"

  environment {
    variables = {
      NAME_BUCKET_S3  = var.name_bucket_s3
      URL_API = "${aws_lb.api.dns_name}:${var.ecs_port_api}"
    }
  }

  depends_on = [null_resource.build_and_push_docker_image_lambda, aws_s3_bucket.s3]
}

resource "aws_s3_bucket_notification" "bucket_notification" {
  bucket = "bucket-mangago-esgi"

  lambda_function {
    lambda_function_arn = aws_lambda_function.lambda_function.arn
    events              = ["s3:ObjectCreated:*"]
    filter_prefix       = "scan_brut/"
  }

  depends_on = [aws_lambda_permission.allow_bucket]
}

resource "aws_lambda_permission" "allow_bucket" {
  statement_id  = "AllowExecutionFromS3Bucket"
  action        = "lambda:InvokeFunction"
  function_name = aws_lambda_function.lambda_function.function_name
  principal     = "s3.amazonaws.com"
  source_arn    = "arn:aws:s3:::bucket-mangago-esgi"
}