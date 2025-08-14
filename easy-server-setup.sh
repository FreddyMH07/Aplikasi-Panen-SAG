#!/bin/bash

# Easy Server Setup Script for Sistem Panen Sawit
# Domain: reportpanen.freddypmsag.my.id
# Run this script on your EC2 instance after uploading files

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="reportpanen.freddypmsag.my.id"
PROJECT_NAME="sistem-panen-sawit"
PROJECT_PATH="/var/www/html/$PROJECT_NAME"

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

echo "🌴 Easy Server Setup - Sistem Panen Sawit"
echo "Domain: $DOMAIN"
echo "=========================================="

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    log_error "Please don't run this script as root. Run as ubuntu user."
    exit 1
fi

update_system() {
    log_info "Updating system packages..."
    sudo apt update && sudo apt upgrade -y
    log_success "System updated!"
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
        php8.2-sqlite3 libapache2-mod-php8.2 -y
    
    # Configure PHP for production
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

configure_apache() {
    log_info "Configuring Apache..."
    
    # Enable required modules
    sudo a2enmod rewrite
    sudo a2enmod ssl
    sudo a2enmod headers
    sudo a2enmod php8.2
    
    # Create virtual host configuration
    sudo tee /etc/apache2/sites-available/$PROJECT_NAME.conf > /dev/null << EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $PROJECT_PATH/public
    
    <Directory $PROJECT_PATH/public>
        AllowOverride All
        Require all granted
        Options -Indexes
    </Directory>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    
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

setup_project() {
    log_info "Setting up Laravel project..."
    
    # Check if project directory exists
    if [ ! -d "$PROJECT_PATH" ]; then
        log_error "Project directory $PROJECT_PATH not found!"
        log_info "Please upload your project files via WinSCP first."
        exit 1
    fi
    
    cd $PROJECT_PATH
    
    # Set permissions
    sudo chown -R www-data:www-data .
    sudo chmod -R 755 .
    sudo chmod -R 775 storage bootstrap/cache
    
    # Install Composer dependencies
    log_info "Installing Composer dependencies..."
    composer install --optimize-autoloader --no-dev --no-interaction
    
    # Install NPM dependencies and build
    log_info "Installing NPM dependencies and building assets..."
    npm install
    npm run build
    
    # Setup environment
    if [ ! -f .env ]; then
        cp .env.example .env
        log_info "Created .env file from .env.example"
    fi
    
    # Generate application key
    php artisan key:generate --force
    
    # Setup SQLite database
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
        chmod 664 database/database.sqlite
        log_info "Created SQLite database file"
    fi
    
    # Run migrations
    php artisan migrate --force
    
    # Seed database if seeder exists
    if [ -f database/seeders/DatabaseSeeder.php ]; then
        php artisan db:seed --force
        log_info "Database seeded"
    fi
    
    # Optimize Laravel
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Final permission fix
    sudo chown -R www-data:www-data .
    sudo chmod -R 755 .
    sudo chmod -R 775 storage bootstrap/cache database
    
    log_success "Laravel project setup completed!"
}

install_certbot() {
    log_info "Installing Certbot for SSL certificates..."
    sudo apt install certbot python3-certbot-apache -y
    log_success "Certbot installed!"
}

setup_firewall() {
    log_info "Configuring UFW firewall..."
    sudo ufw --force enable
    sudo ufw allow ssh
    sudo ufw allow 'Apache Full'
    sudo ufw status
    log_success "Firewall configured!"
}

display_next_steps() {
    log_success "Server setup completed!"
    echo ""
    echo "🎯 NEXT STEPS:"
    echo "=============="
    echo ""
    echo "1. 📋 Configure DNS at DapurHosting:"
    echo "   - Add A record: reportpanen → $(curl -s ifconfig.me)"
    echo "   - Add CNAME record: www.reportpanen → reportpanen.freddypmsag.my.id"
    echo ""
    echo "2. 🔒 Setup SSL certificate (after DNS propagation):"
    echo "   sudo certbot --apache -d $DOMAIN -d www.$DOMAIN"
    echo ""
    echo "3. 🌐 Test your website:"
    echo "   http://$DOMAIN (should work immediately)"
    echo "   https://$DOMAIN (after SSL setup)"
    echo ""
    echo "4. 📊 Monitor your application:"
    echo "   - Apache logs: sudo tail -f /var/log/apache2/$PROJECT_NAME-error.log"
    echo "   - Laravel logs: tail -f $PROJECT_PATH/storage/logs/laravel.log"
    echo ""
    echo "5. 🔧 Default login (if seeded):"
    echo "   - URL: https://$DOMAIN/login"
    echo "   - Check database or seeders for credentials"
    echo ""
    echo "📖 For troubleshooting, see: EASY_DEPLOYMENT_GUIDE.md"
    echo ""
}

# Main execution
main() {
    echo ""
    log_info "Starting easy server setup..."
    echo ""
    
    update_system
    install_apache
    install_php
    install_composer
    install_nodejs
    configure_apache
    setup_project
    install_certbot
    setup_firewall
    display_next_steps
    
    echo ""
    log_success "🎉 Easy server setup completed successfully!"
    echo ""
    log_info "Your website should be accessible at: http://$DOMAIN"
    echo ""
}

# Run main function
main "$@"
