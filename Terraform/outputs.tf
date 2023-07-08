output "load_balancer_api" {
    value = aws_lb.api.dns_name
}

output "DB_HOST" {
  value = aws_db_instance.rds.address
}

output "S3_BUCKET_ENDPOINT" {
  value = aws_s3_bucket.s3.bucket_regional_domain_name
}