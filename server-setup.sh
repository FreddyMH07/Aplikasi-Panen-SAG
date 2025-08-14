#!/bin/bash

# Server Setup Script for Sistem Panen Sawit
# Run this script on your EC2 instance after SSH connection

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="freddypmsag.my.id"
PROJECT_NAME="sistem-panen-sawit"

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

echo "🌴 Sistem Panen Sawit - Server Setup Script"
echo "==========================================="

update_system() {
    log_info "Updating system packages..."
    sudo apt update && sudo apt upgrade -y
    log_success "System updated successfully!"
}

install_apache() {
    log_info "Installing Apache web server..."
    sudo apt install apache2 -y
    sudo systemctl enable apache2
    sudo systemctl start apache2
    log_success "Apache installed and started!"
}

install_php() {
    log_info "Installing PHP 8.2 and extensions..."
    sudo apt install software-properties-common -y
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt update
    
    sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd \
        php8.2-curl php8.2-mbstring php8.2-zip php8.2-intl php8.2-bcmath \
        php8.2-soap php8.2-redis php8.2-imagick libapache2-mod-php8.2 -y
    
    # Configure PHP
    sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 64M/' /etc/php/8.2/apache2/php.ini
    sudo sed -i 's/post_max_size = 8M/post_max_size = 64M/' /etc/php/8.2/apache2/php.ini
    sudo sed -i 's/max_execution_time = 30/max_execution_time = 300/' /etc/php/8.2/apache2/php.ini
    sudo sed -i 's/memory_limit = 128M/memory_limit = 512M/' /etc/php/8.2/apache2/php.ini
    
    log_success "PHP 8.2 installed and configured!"
}

install_composer() {
    log_info "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    log_success "Composer installed!"
}

install_nodejs() {
    log_info "Installing Node.js and npm..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt install nodejs -y
    log_success "Node.js and npm installed!"
}

install_mysql_client() {
    log_info "Installing MySQL client..."
    sudo apt install mysql-client -y
    log_success "MySQL client installed!"
}

configure_apache() {
    log_info "Configuring Apache..."
    
    # Enable required modules
    sudo a2enmod rewrite
    sudo a2enmod ssl
    sudo a2enmod headers
    sudo a2enmod php8.2
    
    # Create document root
    sudo mkdir -p /var/www/html/$PROJECT_NAME
    sudo chown -R www-data:www-data /var/www/html/$PROJECT_NAME
    
    # Create virtual host configuration
    sudo tee /etc/apache2/sites-available/$PROJECT_NAME.conf > /dev/null << EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot /var/www/html/$PROJECT_NAME/public
    
    <Directory /var/www/html/$PROJECT_NAME/public>
        AllowOverride All
        Require all granted
        Options -Indexes
    </Directory>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; media-src 'self'; object-src 'none'; child-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'self';"
    
    ErrorLog \${APACHE_LOG_DIR}/$PROJECT_NAME-error.log
    CustomLog \${APACHE_LOG_DIR}/$PROJECT_NAME-access.log combined
    
    # Hide Apache version
    ServerTokens Prod
    ServerSignature Off
</VirtualHost>
EOF

    # Enable site and disable default
    sudo a2ensite $PROJECT_NAME.conf
    sudo a2dissite 000-default.conf
    
    # Test configuration
    sudo apache2ctl configtest
    
    # Restart Apache
    sudo systemctl restart apache2
    
    log_success "Apache configured successfully!"
}

install_certbot() {
    log_info "Installing Certbot for SSL certificates..."
    sudo apt install certbot python3-certbot-apache -y
    log_success "Certbot installed!"
}

install_codedeploy_agent() {
    log_info "Installing AWS CodeDeploy agent..."
    sudo apt install ruby wget -y
    cd /home/ubuntu
    wget https://aws-codedeploy-ap-southeast-1.s3.ap-southeast-1.amazonaws.com/latest/install
    chmod +x ./install
    sudo ./install auto
    
    # Start and enable CodeDeploy agent
    sudo systemctl start codedeploy-agent
    sudo systemctl enable codedeploy-agent
    
    # Check status
    sudo systemctl status codedeploy-agent --no-pager
    
    log_success "CodeDeploy agent installed and started!"
}

install_cloudwatch_agent() {
    log_info "Installing CloudWatch agent..."
    wget https://s3.amazonaws.com/amazoncloudwatch-agent/ubuntu/amd64/latest/amazon-cloudwatch-agent.deb
    sudo dpkg -i -E ./amazon-cloudwatch-agent.deb
    rm amazon-cloudwatch-agent.deb
    log_success "CloudWatch agent installed!"
}

setup_firewall() {
    log_info "Configuring UFW firewall..."
    sudo ufw --force enable
    sudo ufw allow ssh
    sudo ufw allow 'Apache Full'
    sudo ufw status
    log_success "Firewall configured!"
}

create_backup_script() {
    log_info "Creating backup script..."
    
    sudo tee /home/ubuntu/backup.sh > /dev/null << 'EOF'
#!/bin/bash

# Backup script for Sistem Panen Sawit
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/tmp/backup_$DATE"
PROJECT_DIR="/var/www/html/sistem-panen-sawit"
S3_BUCKET="sistem-panen-sawit-storage"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup application files (excluding vendor and node_modules)
tar --exclude='vendor' --exclude='node_modules' --exclude='storage/logs/*' \
    -czf $BACKUP_DIR/app_files_$DATE.tar.gz -C /var/www/html sistem-panen-sawit

# Backup storage directory
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz $PROJECT_DIR/storage

# Upload to S3
aws s3 cp $BACKUP_DIR/ s3://$S3_BUCKET/backups/ --recursive

# Cleanup local backup
rm -rf $BACKUP_DIR

echo "Backup completed: $DATE"
EOF

    chmod +x /home/ubuntu/backup.sh
    
    # Setup cron job for daily backup at 2 AM
    (crontab -l 2>/dev/null; echo "0 2 * * * /home/ubuntu/backup.sh >> /var/log/backup.log 2>&1") | crontab -
    
    log_success "Backup script created and scheduled!"
}

setup_log_rotation() {
    log_info "Setting up log rotation..."
    
    sudo tee /etc/logrotate.d/$PROJECT_NAME > /dev/null << EOF
/var/www/html/$PROJECT_NAME/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        systemctl reload apache2 > /dev/null 2>&1 || true
    endscript
}
EOF

    log_success "Log rotation configured!"
}

optimize_system() {
    log_info "Optimizing system performance..."
    
    # Increase file limits
    echo "* soft nofile 65536" | sudo tee -a /etc/security/limits.conf
    echo "* hard nofile 65536" | sudo tee -a /etc/security/limits.conf
    
    # Optimize Apache
    sudo tee -a /etc/apache2/apache2.conf > /dev/null << EOF

# Performance optimizations
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5

# Security optimizations
ServerTokens Prod
ServerSignature Off
EOF

    log_success "System optimized!"
}

display_next_steps() {
    log_success "Server setup completed!"
    echo ""
    echo "🎯 NEXT STEPS:"
    echo "=============="
    echo ""
    echo "1. 🔒 Setup SSL certificate:"
    echo "   sudo certbot --apache -d $DOMAIN -d www.$DOMAIN"
    echo ""
    echo "2. 📋 Configure your DNS at DapurHosting to point to this server"
    echo ""
    echo "3. 🚀 Deploy your application:"
    echo "   - Push your code to GitHub"
    echo "   - Use AWS CodeDeploy to deploy"
    echo ""
    echo "4. 🔧 Configure your Laravel .env file:"
    echo "   sudo nano /var/www/html/$PROJECT_NAME/.env"
    echo ""
    echo "5. 📊 Monitor your application:"
    echo "   - Check logs: sudo tail -f /var/log/apache2/$PROJECT_NAME-error.log"
    echo "   - Check Laravel logs: sudo tail -f /var/www/html/$PROJECT_NAME/storage/logs/laravel.log"
    echo ""
    echo "📖 For detailed instructions, see the deployment guide."
    echo ""
}

# Main execution
main() {
    echo ""
    log_info "Starting server setup..."
    echo ""
    
    update_system
    install_apache
    install_php
    install_composer
    install_nodejs
    install_mysql_client
    configure_apache
    install_certbot
    install_codedeploy_agent
    install_cloudwatch_agent
    setup_firewall
    create_backup_script
    setup_log_rotation
    optimize_system
    display_next_steps
    
    echo ""
    log_success "🎉 Server setup completed successfully!"
    echo ""
    log_warning "Please reboot the server to ensure all changes take effect:"
    echo "sudo reboot"
    echo ""
}

# Run main function
main "$@"
