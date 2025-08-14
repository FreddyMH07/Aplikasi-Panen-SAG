#!/bin/bash

# RDS Setup Script for Sistem Panen Sawit
# This script creates RDS MySQL instance for the application

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="sistem-panen-sawit"
AWS_REGION="ap-southeast-1"
DB_INSTANCE_ID="$PROJECT_NAME-db"
DB_NAME="sistem_panen_sawit"
DB_USERNAME="admin"
DB_PASSWORD="SistemPanenSawit2024!"

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

echo "🗄️  RDS MySQL Setup for Sistem Panen Sawit"
echo "=========================================="

check_aws_cli() {
    log_info "Checking AWS CLI configuration..."
    
    if ! command -v aws &> /dev/null; then
        log_error "AWS CLI is not installed. Please install it first."
        exit 1
    fi
    
    if ! aws sts get-caller-identity &> /dev/null; then
        log_error "AWS credentials not configured. Please run 'aws configure' first."
        exit 1
    fi
    
    log_success "AWS CLI is configured!"
}

get_vpc_info() {
    log_info "Getting VPC information..."
    
    # Get default VPC ID
    VPC_ID=$(aws ec2 describe-vpcs \
        --filters "Name=is-default,Values=true" \
        --query 'Vpcs[0].VpcId' \
        --output text \
        --region $AWS_REGION)
    
    if [ "$VPC_ID" = "None" ] || [ -z "$VPC_ID" ]; then
        log_error "No default VPC found. Please create a VPC first."
        exit 1
    fi
    
    log_info "Using VPC: $VPC_ID"
    
    # Get subnet IDs from different AZs
    SUBNET_IDS=$(aws ec2 describe-subnets \
        --filters "Name=vpc-id,Values=$VPC_ID" \
        --query 'Subnets[*].SubnetId' \
        --output text \
        --region $AWS_REGION)
    
    # Convert to array
    SUBNET_ARRAY=($SUBNET_IDS)
    
    if [ ${#SUBNET_ARRAY[@]} -lt 2 ]; then
        log_error "Need at least 2 subnets in different AZs for RDS. Found: ${#SUBNET_ARRAY[@]}"
        exit 1
    fi
    
    log_info "Found ${#SUBNET_ARRAY[@]} subnets"
}

create_db_subnet_group() {
    log_info "Creating DB subnet group..."
    
    # Use first two subnets
    SUBNET1=${SUBNET_ARRAY[0]}
    SUBNET2=${SUBNET_ARRAY[1]}
    
    aws rds create-db-subnet-group \
        --db-subnet-group-name $PROJECT_NAME-subnet-group \
        --db-subnet-group-description "Subnet group for $PROJECT_NAME database" \
        --subnet-ids $SUBNET1 $SUBNET2 \
        --region $AWS_REGION \
        --tags Key=Project,Value=$PROJECT_NAME 2>/dev/null || log_warning "DB subnet group might already exist"
    
    log_success "DB subnet group created/verified!"
}

create_security_group() {
    log_info "Creating RDS security group..."
    
    # Create security group for RDS
    RDS_SG_ID=$(aws ec2 create-security-group \
        --group-name $PROJECT_NAME-rds-sg \
        --description "Security group for $PROJECT_NAME RDS MySQL" \
        --vpc-id $VPC_ID \
        --query 'GroupId' \
        --output text \
        --region $AWS_REGION 2>/dev/null || echo "existing")
    
    if [ "$RDS_SG_ID" = "existing" ]; then
        # Get existing security group ID
        RDS_SG_ID=$(aws ec2 describe-security-groups \
            --filters "Name=group-name,Values=$PROJECT_NAME-rds-sg" "Name=vpc-id,Values=$VPC_ID" \
            --query 'SecurityGroups[0].GroupId' \
            --output text \
            --region $AWS_REGION)
        log_warning "Using existing RDS security group: $RDS_SG_ID"
    else
        log_success "Created RDS security group: $RDS_SG_ID"
        
        # Get EC2 security group ID
        EC2_SG_ID=$(aws ec2 describe-security-groups \
            --filters "Name=group-name,Values=$PROJECT_NAME-ec2-sg" \
            --query 'SecurityGroups[0].GroupId' \
            --output text \
            --region $AWS_REGION 2>/dev/null || echo "")
        
        if [ -n "$EC2_SG_ID" ] && [ "$EC2_SG_ID" != "None" ]; then
            # Allow MySQL access from EC2 security group
            aws ec2 authorize-security-group-ingress \
                --group-id $RDS_SG_ID \
                --protocol tcp \
                --port 3306 \
                --source-group $EC2_SG_ID \
                --region $AWS_REGION 2>/dev/null || log_warning "Security group rule might already exist"
            
            log_success "Added MySQL access rule from EC2 security group"
        else
            log_warning "EC2 security group not found. You'll need to manually configure RDS access."
        fi
    fi
}

create_rds_instance() {
    log_info "Creating RDS MySQL instance..."
    log_warning "This may take 10-15 minutes..."
    
    # Check if instance already exists
    EXISTING_INSTANCE=$(aws rds describe-db-instances \
        --db-instance-identifier $DB_INSTANCE_ID \
        --query 'DBInstances[0].DBInstanceStatus' \
        --output text \
        --region $AWS_REGION 2>/dev/null || echo "not-found")
    
    if [ "$EXISTING_INSTANCE" != "not-found" ]; then
        log_warning "RDS instance $DB_INSTANCE_ID already exists with status: $EXISTING_INSTANCE"
        return
    fi
    
    aws rds create-db-instance \
        --db-instance-identifier $DB_INSTANCE_ID \
        --db-instance-class db.t3.micro \
        --engine mysql \
        --engine-version 8.0.35 \
        --master-username $DB_USERNAME \
        --master-user-password $DB_PASSWORD \
        --allocated-storage 20 \
        --storage-type gp2 \
        --db-name $DB_NAME \
        --vpc-security-group-ids $RDS_SG_ID \
        --db-subnet-group-name $PROJECT_NAME-subnet-group \
        --backup-retention-period 7 \
        --no-multi-az \
        --no-publicly-accessible \
        --storage-encrypted \
        --deletion-protection \
        --region $AWS_REGION \
        --tags Key=Project,Value=$PROJECT_NAME Key=Environment,Value=production
    
    log_success "RDS instance creation initiated!"
}

wait_for_rds() {
    log_info "Waiting for RDS instance to be available..."
    
    aws rds wait db-instance-available \
        --db-instance-identifier $DB_INSTANCE_ID \
        --region $AWS_REGION
    
    log_success "RDS instance is now available!"
}

get_rds_endpoint() {
    log_info "Getting RDS endpoint..."
    
    RDS_ENDPOINT=$(aws rds describe-db-instances \
        --db-instance-identifier $DB_INSTANCE_ID \
        --query 'DBInstances[0].Endpoint.Address' \
        --output text \
        --region $AWS_REGION)
    
    log_success "RDS endpoint: $RDS_ENDPOINT"
    
    # Save to file for later use
    echo "RDS_ENDPOINT=$RDS_ENDPOINT" > .rds-info
    echo "DB_NAME=$DB_NAME" >> .rds-info
    echo "DB_USERNAME=$DB_USERNAME" >> .rds-info
    echo "DB_PASSWORD=$DB_PASSWORD" >> .rds-info
    
    log_info "RDS information saved to .rds-info file"
}

test_connection() {
    log_info "Testing database connection..."
    
    # Check if mysql client is available
    if command -v mysql &> /dev/null; then
        log_info "Testing connection to RDS..."
        mysql -h $RDS_ENDPOINT -u $DB_USERNAME -p$DB_PASSWORD -e "SELECT VERSION();" 2>/dev/null && \
            log_success "Database connection successful!" || \
            log_warning "Could not test connection. Please verify manually."
    else
        log_warning "MySQL client not installed. Cannot test connection."
        log_info "To test connection later, use:"
        echo "mysql -h $RDS_ENDPOINT -u $DB_USERNAME -p$DB_PASSWORD"
    fi
}

create_env_template() {
    log_info "Creating .env template with RDS configuration..."
    
    # Update .env.production with RDS details
    sed -i "s/DB_HOST=.*/DB_HOST=$RDS_ENDPOINT/" .env.production
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env.production
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env.production
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env.production
    
    log_success ".env.production updated with RDS configuration!"
}

display_summary() {
    log_success "RDS MySQL setup completed!"
    echo ""
    echo "📋 DATABASE INFORMATION:"
    echo "======================="
    echo "Instance ID: $DB_INSTANCE_ID"
    echo "Endpoint: $RDS_ENDPOINT"
    echo "Database: $DB_NAME"
    echo "Username: $DB_USERNAME"
    echo "Password: $DB_PASSWORD"
    echo ""
    echo "🔧 NEXT STEPS:"
    echo "============="
    echo "1. Update your Laravel .env file with the database credentials"
    echo "2. Run migrations: php artisan migrate"
    echo "3. Seed database if needed: php artisan db:seed"
    echo ""
    echo "💡 CONNECTION STRING:"
    echo "mysql -h $RDS_ENDPOINT -u $DB_USERNAME -p$DB_PASSWORD $DB_NAME"
    echo ""
    log_warning "Keep your database credentials secure!"
}

# Main execution
main() {
    echo ""
    log_info "Starting RDS MySQL setup..."
    echo ""
    
    check_aws_cli
    get_vpc_info
    create_db_subnet_group
    create_security_group
    create_rds_instance
    wait_for_rds
    get_rds_endpoint
    test_connection
    create_env_template
    display_summary
    
    echo ""
    log_success "🎉 RDS setup completed successfully!"
    echo ""
}

# Run main function
main "$@"
