#!/bin/bash

# Script untuk membuat package upload yang siap deploy
# Menghapus file yang tidak perlu dan membuat ZIP

echo "🎁 Creating Upload Package for Sistem Panen Sawit"
echo "================================================="

# Create temporary directory
TEMP_DIR="/tmp/sistem-panen-sawit-upload"
SOURCE_DIR="/root/sistem-panen-sawit"

# Remove existing temp directory
rm -rf $TEMP_DIR

# Copy project to temp directory
cp -r $SOURCE_DIR $TEMP_DIR

cd $TEMP_DIR

echo "📦 Cleaning up unnecessary files..."

# Remove development files
rm -rf node_modules
rm -rf vendor
rm -f .env
rm -f database/database.sqlite
rm -rf storage/logs/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*

# Remove git files
rm -rf .git
rm -f .gitignore

# Remove development scripts
rm -f deploy.sh
rm -f setup-rds.sh
rm -f server-setup.sh
rm -f create-upload-package.sh

# Remove documentation files (keep only essential ones)
rm -f DEPLOYMENT_GUIDE_COMPLETE.md
rm -f DEPLOYMENT_CHECKLIST.md
rm -f DARK_MODE_README.md

# Create necessary directories
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Create empty log file
touch storage/logs/laravel.log

# Set proper permissions for directories
chmod 775 storage -R
chmod 775 bootstrap/cache -R

echo "✅ Package cleaned and prepared!"

# Create ZIP file
cd /tmp
zip -r sistem-panen-sawit-upload.zip sistem-panen-sawit-upload/ -x "*.DS_Store" "*.git*"

echo "📁 Upload package created: /tmp/sistem-panen-sawit-upload.zip"
echo ""
echo "🚀 READY FOR UPLOAD!"
echo "==================="
echo ""
echo "1. Download the ZIP file: /tmp/sistem-panen-sawit-upload.zip"
echo "2. Extract it on your local computer"
echo "3. Upload via WinSCP to: /var/www/html/sistem-panen-sawit"
echo "4. Run the setup script on server: ./easy-server-setup.sh"
echo ""
echo "Package size:"
du -sh /tmp/sistem-panen-sawit-upload.zip
