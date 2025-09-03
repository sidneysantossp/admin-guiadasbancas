#!/bin/bash

# Vercel Build Script for Laravel Project
# This script optimizes the build process to stay under 250MB limit

echo "Starting optimized build process..."

# Install only production dependencies
echo "Installing production dependencies only..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Clear all caches
echo "Clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Remove unnecessary files
echo "Removing unnecessary files..."
rm -rf storage/logs/*.log
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*.php

# Remove development files from vendor
echo "Cleaning vendor directory..."
find vendor -name "*.md" -delete
find vendor -name "*.txt" -delete
find vendor -name "tests" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name "test" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name "docs" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name "examples" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name ".git" -type d -exec rm -rf {} + 2>/dev/null || true

echo "Build optimization completed!"
echo "Checking final size..."
du -sh vendor/ || true