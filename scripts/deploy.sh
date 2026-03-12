#!/bin/bash

# ===================================
# AUKCIJSKA PLATFORMA - DEPLOY SCRIPT
# ===================================
# Production deployment script
# Usage: ./scripts/deploy.sh [environment]
# Environments: staging, production

set -e

# Configuration
ENVIRONMENT=${1:-production}
APP_NAME="aukcije"
DEPLOY_USER=${DEPLOY_USER:-"deploy"}
DEPLOY_HOST=${DEPLOY_HOST:-""}
DEPLOY_PATH=${DEPLOY_PATH:-"/var/www/aukcije"}

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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
    
    if [ -z "$DEPLOY_HOST" ]; then
        log_error "DEPLOY_HOST environment variable is not set"
        exit 1
    fi
    
    if ! command -v ssh &> /dev/null; then
        log_error "SSH is not installed"
        exit 1
    fi
    
    if ! command -v rsync &> /dev/null; then
        log_error "rsync is not installed"
        exit 1
    fi
    
    log_success "Prerequisites check passed"
}

run_tests() {
    log_info "Running tests..."
    
    # Run PHP lint
    if ! ./vendor/bin/pint --test > /dev/null 2>&1; then
        log_error "PHP lint failed"
        exit 1
    fi
    
    # Run PHPStan
    if ! ./vendor/bin/phpstan analyse --level=6 > /dev/null 2>&1; then
        log_error "PHPStan analysis failed"
        exit 1
    fi
    
    # Run tests
    if ! php artisan test --parallel > /dev/null 2>&1; then
        log_error "Tests failed"
        exit 1
    fi
    
    log_success "All tests passed"
}

build_assets() {
    log_info "Building assets..."
    
    npm install
    npm run build
    
    log_success "Assets built"
}

deploy_to_server() {
    log_info "Deploying to $ENVIRONMENT server ($DEPLOY_HOST)..."
    
    # Create remote directory if it doesn't exist
    ssh $DEPLOY_USER@$DEPLOY_HOST "mkdir -p $DEPLOY_PATH"
    
    # Sync files
    rsync -avz --progress \
        --exclude='.git' \
        --exclude='.github' \
        --exclude='node_modules' \
        --exclude='vendor' \
        --exclude='.env' \
        --exclude='storage/app/public' \
        --exclude='storage/logs/*' \
        --exclude='bootstrap/cache/*' \
        --exclude='.DS_Store' \
        ./ $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/current
    
    log_success "Files synced"
}

remote_deploy() {
    log_info "Running remote deployment tasks..."
    
    ssh $DEPLOY_USER@$DEPLOY_HOST << 'ENDSSH'
        set -e
        cd /var/www/aukcije/current
        
        # Install PHP dependencies
        echo "Installing Composer dependencies..."
        composer install --no-dev --optimize-autoloader --no-interaction
        
        # Install Node dependencies and build
        echo "Building frontend assets..."
        npm ci --only=production
        npm run build
        
        # Run migrations
        echo "Running migrations..."
        php artisan migrate --force
        
        # Clear and cache
        echo "Optimizing application..."
        php artisan optimize:clear
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan event:cache
        
        # Restart services
        echo "Restarting services..."
        sudo systemctl restart php8.3-fpm
        sudo systemctl restart nginx
        
        # Restart Horizon and Reverb
        php artisan horizon:terminate
        
        echo "Deployment completed successfully"
ENDSSH
    
    log_success "Remote deployment completed"
}

health_check() {
    log_info "Running health checks..."
    
    # Check if application is responding
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://$DEPLOY_HOST/health)
    
    if [ "$HTTP_STATUS" != "200" ]; then
        log_error "Health check failed (HTTP $HTTP_STATUS)"
        exit 1
    fi
    
    log_success "Health check passed"
}

cleanup() {
    log_info "Cleaning up old releases..."
    
    ssh $DEPLOY_USER@$DEPLOY_HOST << 'ENDSSH'
        cd /var/www/aukcije
        
        # Keep last 5 releases
        ls -dt releases/* | tail -n +6 | xargs -r rm -rf
        
        echo "Cleanup completed"
ENDSSH
    
    log_success "Cleanup completed"
}

notify() {
    log_info "Sending notifications..."
    
    # Send Slack notification (if configured)
    if [ -n "$SLACK_WEBHOOK_URL" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"✅ Deployment to $ENVIRONMENT completed successfully!\"}" \
            $SLACK_WEBHOOK_URL > /dev/null 2>&1 || true
    fi
    
    log_success "Notifications sent"
}

# Main deployment flow
main() {
    echo "======================================"
    echo "  AUKCIJSKA PLATFORMA - DEPLOYMENT"
    echo "  Environment: $ENVIRONMENT"
    echo "======================================"
    echo ""
    
    check_prerequisites
    # run_tests  # Uncomment for CI/CD
    build_assets
    deploy_to_server
    remote_deploy
    health_check
    cleanup
    notify
    
    echo ""
    echo "======================================"
    echo -e "${GREEN}  DEPLOYMENT SUCCESSFUL!${NC}"
    echo "======================================"
    echo ""
    echo "Application URL: https://$DEPLOY_HOST"
    echo "Horizon Dashboard: https://$DEPLOY_HOST/horizon"
    echo ""
}

# Run main function
main
