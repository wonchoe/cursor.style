echo "📥 Fetching .env from SSM Parameter Store..."

aws ssm get-parameter \
  --name "/cursor.style/dev/.env" \
  --with-decryption \
  --query "Parameter.Value" \
  --output text > /var/www/cursor.style/.env

echo "✅ .env written to /var/www/cursor.style/.env"