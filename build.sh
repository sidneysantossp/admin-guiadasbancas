#!/bin/bash

# Vercel Build Script for Laravel Project
# Optimized to work with Vercel's PHP runtime dependency management

echo "Starting lightweight build process..."

# Create necessary directories
echo "Creating required directories..."
mkdir -p bootstrap/cache
mkdir -p storage/logs
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views

# Set proper permissions
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Remove any existing cache files
echo "Clearing existing caches..."
rm -rf storage/logs/*.log
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*.php

# Create minimal .env if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating minimal .env file..."
    cp .env.example .env || echo "APP_ENV=production" > .env
fi

echo "Lightweight build completed!"
echo "Vercel PHP runtime will handle dependency installation automatically."