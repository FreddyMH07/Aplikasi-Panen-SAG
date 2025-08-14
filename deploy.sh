#!/bin/bash

# Sistem Panen Sawit - Automated Deployment Script
# Author: AWS Q Assistant
# Domain: freddypmsag.my.id

set -e

echo "🌴 Sistem Panen Sawit - Automated Deployment Script"
echo "=================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="sistem-panen-sawit"
DOMAIN="freddypmsag.my.id"
AWS_REGION="ap-southeast-1"
GITHUB_REPO="YOUR_USERNAME/sistem-panen-sawit"

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_prerequisites() {
    log_info "Checking prerequisites..."
    
    # Check if AWS CLI is installed
    if ! command -v aws &> /dev/null; then
        log_error "AWS CLI is not installed. Please install it first."
        exit 1
    fi
    
    # Check if git is installed
    if ! command -v git &> /dev/null; then
        log_error "Git is not installed. Please install it first."
        exit 1
    fi
    
    # Check if composer is installed
    if ! command -v composer &> /dev/null; then
        log_error "Composer is not installed. Please install it first."
        exit 1
    fi
    
    # Check if npm is installed
    if ! command -v npm &> /dev/null; then
        log_error "NPM is not installed. Please install it first."
        exit 1
    fi
    
    log_success "All prerequisites are met!"
}

prepare_project() {
    log_info "Preparing Laravel project..."
    
    # Install dependencies
    composer install --optimize-autoloader --no-dev
    
    # Generate app key if not exists
    if [ ! -f .env ]; then
        cp .env.example .env
        php artisan key:generate
    fi
    
    # Clear and cache config
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    # Install npm dependencies
    npm ci
    npm run build
    
    log_success "Project prepared successfully!"
}

create_deployment_files() {
    log_info "Creating deployment files..."
    
    # Create scripts directory
    mkdir -p scripts
    
    # Create install_dependencies.sh
    cat > scripts/install_dependencies.sh << 'EOF'
#!/bin/bash
cd /var/www/html/sistem-panen-sawit

# Install Composer dependencies
composer install --optimize-autoloader --no-dev --no-interaction

# Install Node.js dependencies and build assets
npm ci
npm run build

# Set permissions
chown -R www-data:www-data /var/www/html/sistem-panen-sawit
chmod -R 755 /var/www/html/sistem-panen-sawit
chmod -R 775 /var/www/html/sistem-panen-sawit/storage
chmod -R 775 /var/www/html/sistem-panen-sawit/bootstrap/cache

# Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
EOF

    # Create start_server.sh
    cat > scripts/start_server.sh << 'EOF'
#!/bin/bash
systemctl start apache2
systemctl enable apache2
EOF

    # Create stop_server.sh
    cat > scripts/stop_server.sh << 'EOF'
#!/bin/bash
systemctl stop apache2
EOF

    # Make scripts executable
    chmod +x scripts/*.sh
    
    # Create appspec.yml
    cat > appspec.yml << 'EOF'
version: 0.0
os: linux
files:
  - source: /
    destination: /var/www/html/sistem-panen-sawit
    overwrite: yes
permissions:
  - object: /var/www/html/sistem-panen-sawit
    owner: www-data
    group: www-data
    mode: 755
  - object: /var/www/html/sistem-panen-sawit/storage
    owner: www-data
    group: www-data
    mode: 775
  - object: /var/www/html/sistem-panen-sawit/bootstrap/cache
    owner: www-data
    group: www-data
    mode: 775
hooks:
  BeforeInstall:
    - location: scripts/install_dependencies.sh
      timeout: 300
      runas: root
  ApplicationStart:
    - location: scripts/start_server.sh
      timeout: 300
      runas: root
  ApplicationStop:
    - location: scripts/stop_server.sh
      timeout: 300
      runas: root
EOF

    # Create Apache configuration
    mkdir -p apache-config
    cat > apache-config/sistem-panen-sawit.conf << EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    DocumentRoot /var/www/html/sistem-panen-sawit/public
    
    <Directory /var/www/html/sistem-panen-sawit/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/sistem-panen-sawit-error.log
    CustomLog \${APACHE_LOG_DIR}/sistem-panen-sawit-access.log combined
    
    # Redirect HTTP to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName $DOMAIN
    DocumentRoot /var/www/html/sistem-panen-sawit/public
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/$DOMAIN/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/$DOMAIN/privkey.pem
    
    <Directory /var/www/html/sistem-panen-sawit/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/sistem-panen-sawit-ssl-error.log
    CustomLog \${APACHE_LOG_DIR}/sistem-panen-sawit-ssl-access.log combined
</VirtualHost>
EOF

    log_success "Deployment files created successfully!"
}

setup_github() {
    log_info "Setting up GitHub repository..."
    
    # Initialize git if not already done
    if [ ! -d .git ]; then
        git init
    fi
    
    # Add all files
    git add .
    
    # Commit
    git commit -m "Deployment ready: Added AWS deployment configuration"
    
    # Ask for GitHub repository URL
    echo -n "Enter your GitHub repository URL (e.g., https://github.com/username/sistem-panen-sawit.git): "
    read GITHUB_URL
    
    # Add remote origin
    git remote add origin $GITHUB_URL 2>/dev/null || git remote set-url origin $GITHUB_URL
    
    # Push to GitHub
    git push -u origin main
    
    log_success "Code pushed to GitHub successfully!"
}

create_aws_resources() {
    log_info "Creating AWS resources..."
    
    # Check AWS credentials
    if ! aws sts get-caller-identity &> /dev/null; then
        log_error "AWS credentials not configured. Please run 'aws configure' first."
        exit 1
    fi
    
    # Create S3 bucket
    log_info "Creating S3 bucket..."
    aws s3 mb s3://$PROJECT_NAME-storage --region $AWS_REGION || log_warning "S3 bucket might already exist"
    
    # Enable S3 versioning
    aws s3api put-bucket-versioning --bucket $PROJECT_NAME-storage --versioning-configuration Status=Enabled
    
    # Create key pair
    log_info "Creating EC2 key pair..."
    aws ec2 create-key-pair --key-name $PROJECT_NAME-key --query 'KeyMaterial' --output text > $PROJECT_NAME-key.pem 2>/dev/null || log_warning "Key pair might already exist"
    chmod 400 $PROJECT_NAME-key.pem 2>/dev/null
    
    # Create security groups
    log_info "Creating security groups..."
    
    # EC2 Security Group
    EC2_SG_ID=$(aws ec2 create-security-group \
        --group-name $PROJECT_NAME-ec2-sg \
        --description "Security group for $PROJECT_NAME EC2" \
        --query 'GroupId' --output text 2>/dev/null || echo "existing")
    
    if [ "$EC2_SG_ID" != "existing" ]; then
        # Add rules for EC2 security group
        aws ec2 authorize-security-group-ingress --group-id $EC2_SG_ID --protocol tcp --port 22 --cidr 0.0.0.0/0
        aws ec2 authorize-security-group-ingress --group-id $EC2_SG_ID --protocol tcp --port 80 --cidr 0.0.0.0/0
        aws ec2 authorize-security-group-ingress --group-id $EC2_SG_ID --protocol tcp --port 443 --cidr 0.0.0.0/0
    fi
    
    log_success "AWS resources created successfully!"
}

launch_ec2_instance() {
    log_info "Launching EC2 instance..."
    
    # Get latest Ubuntu AMI ID
    AMI_ID=$(aws ec2 describe-images \
        --owners 099720109477 \
        --filters "Name=name,Values=ubuntu/images/hvm-ssd/ubuntu-jammy-22.04-amd64-server-*" \
        --query 'Images | sort_by(@, &CreationDate) | [-1].ImageId' \
        --output text)
    
    # Launch instance
    INSTANCE_ID=$(aws ec2 run-instances \
        --image-id $AMI_ID \
        --count 1 \
        --instance-type t2.micro \
        --key-name $PROJECT_NAME-key \
        --security-groups $PROJECT_NAME-ec2-sg \
        --tag-specifications "ResourceType=instance,Tags=[{Key=Name,Value=$PROJECT_NAME-server}]" \
        --query 'Instances[0].InstanceId' \
        --output text)
    
    log_info "Instance launched with ID: $INSTANCE_ID"
    log_info "Waiting for instance to be running..."
    
    aws ec2 wait instance-running --instance-ids $INSTANCE_ID
    
    # Get public IP
    PUBLIC_IP=$(aws ec2 describe-instances \
        --instance-ids $INSTANCE_ID \
        --query 'Reservations[0].Instances[0].PublicIpAddress' \
        --output text)
    
    log_success "EC2 instance is running at IP: $PUBLIC_IP"
    
    # Save instance details
    echo "INSTANCE_ID=$INSTANCE_ID" > .aws-deployment-info
    echo "PUBLIC_IP=$PUBLIC_IP" >> .aws-deployment-info
    
    log_info "Instance details saved to .aws-deployment-info"
}

display_next_steps() {
    log_success "Deployment preparation completed!"
    echo ""
    echo "🎯 NEXT STEPS:"
    echo "=============="
    echo ""
    echo "1. 📋 Update your DNS settings at DapurHosting:"
    echo "   - Type: A, Name: @, Value: $PUBLIC_IP"
    echo "   - Type: A, Name: www, Value: $PUBLIC_IP"
    echo ""
    echo "2. 🔐 SSH to your server:"
    echo "   ssh -i $PROJECT_NAME-key.pem ubuntu@$PUBLIC_IP"
    echo ""
    echo "3. 🛠️  Run the server setup script on your EC2 instance:"
    echo "   (Copy and paste the commands from DEPLOYMENT_GUIDE_COMPLETE.md section 3.2)"
    echo ""
    echo "4. 🔒 Setup SSL certificate:"
    echo "   sudo certbot --apache -d $DOMAIN"
    echo ""
    echo "5. 🚀 Deploy your application using AWS CodeDeploy"
    echo ""
    echo "📖 For detailed instructions, see: DEPLOYMENT_GUIDE_COMPLETE.md"
    echo ""
    log_warning "Don't forget to configure your .env file with production settings!"
}

# Main execution
main() {
    echo ""
    log_info "Starting deployment preparation..."
    echo ""
    
    check_prerequisites
    prepare_project
    create_deployment_files
    setup_github
    create_aws_resources
    launch_ec2_instance
    display_next_steps
    
    echo ""
    log_success "🎉 Deployment preparation completed successfully!"
    echo ""
}

# Run main function
main "$@"
