resource "aws_s3_bucket" "s3" {
  bucket = var.name_bucket_s3
  force_destroy = true
}

resource "aws_s3_bucket_public_access_block" "s3" {
  bucket = aws_s3_bucket.s3.id

  block_public_acls       = false
  block_public_policy     = false
  ignore_public_acls      = false
  restrict_public_buckets = false
}

locals {
  directory_path = var.local_path
  all_files = fileset(local.directory_path, "**/*")
  all_files_regex = [for file in local.all_files : file if can(regex("\\.DS_Store$", file)) == false]
}

resource "aws_s3_bucket_object" "s3_files" {
  for_each = { for file in local.all_files_regex : file => file }
  key    = each.value
  source = "${local.directory_path}/${each.value}"
  bucket = aws_s3_bucket.s3.id

  depends_on = [aws_s3_bucket.s3]
}

resource "aws_s3_bucket_policy" "s3" {
  bucket = aws_s3_bucket.s3.id
  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect   = "Allow"
        Principal = "*"
        Action = "s3:GetObject"
        Resource = [
          "arn:aws:s3:::${var.name_bucket_s3}",
          "arn:aws:s3:::${var.name_bucket_s3}/*"
        ]
      }
    ]
  })

  depends_on = [aws_s3_bucket_object.s3_files]
}