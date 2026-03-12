#!/bin/bash

# ===================================
# AUKCIJSKA PLATFORMA - CONFIG BACKUP
# ===================================
# Backup configuration files to S3
# Usage: ./scripts/backup-config.sh

set -e

# Configuration
BACKUP_DIR="${BACKUP_DIR:-./backups/config}"
S3_BUCKET="${AWS_BUCKET:-aukcije-backups}"
S3_PATH="config/$(date +%Y/%m/%d)"
RETENTION_DAYS="${BACKUP_RETENTION_DAYS:-30}"
ENCRYPTION_KEY="${BACKUP_ENCRYPTION_KEY:-}"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Generate backup filename
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/aukcije_config_$TIMESTAMP.tar.gz"

log_info "Starting configuration backup..."

# Files to backup
CONFIG_FILES=(
    ".env"
    "config/app.php"
    "config/database.php"
    "config/cache.php"
    "config/queue.php"
    "config/mail.php"
    "config/services.php"
    "config/auction.php"
    "config/escrow.php"
    "config/tiers.php"
    "config/payment.php"
    "config/shipping.php"
    "config/localization.php"
    "docker-compose.yml"
    "docker-compose.prod.yml"
    "nginx/default.conf"
    "nginx/nginx.conf"
)

# Create tar archive
log_info "Creating archive..."
tar -czf "$BACKUP_FILE" "${CONFIG_FILES[@]}" 2>/dev/null || {
    log_warning "Some files could not be included in the archive"
}

# Verify backup
if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log_info "Backup created successfully: $BACKUP_SIZE"
else
    log_error "Backup failed!"
    exit 1
fi

# Encrypt backup if encryption key is provided
if [ -n "$ENCRYPTION_KEY" ]; then
    log_info "Encrypting backup..."
    gpg --symmetric --cipher-algo AES256 --batch --yes \
        --passphrase "$ENCRYPTION_KEY" \
        --output "$BACKUP_FILE.gpg" "$BACKUP_FILE"
    
    rm "$BACKUP_FILE"
    BACKUP_FILE="$BACKUP_FILE.gpg"
    log_info "Backup encrypted"
fi

# Upload to S3
if command -v aws &> /dev/null && [ -n "$AWS_ACCESS_KEY_ID" ]; then
    log_info "Uploading to S3..."
    aws s3 cp "$BACKUP_FILE" "s3://$S3_BUCKET/$S3_PATH/" \
        --storage-class STANDARD_IA
    
    log_info "Backup uploaded to s3://$S3_BUCKET/$S3_PATH/"
elif command -v mc &> /dev/null && [ -n "$MINIO_ACCESS_KEY" ]; then
    log_info "Uploading to MinIO..."
    mc cp "$BACKUP_FILE" "myminio/$S3_BUCKET/$S3_PATH/"
    log_info "Backup uploaded to MinIO"
else
    log_warning "No S3/MinIO configured. Backup stored locally."
fi

# Cleanup old backups
log_info "Cleaning up backups older than $RETENTION_DAYS days..."
find "$BACKUP_DIR" -name "aukcije_config_*.tar.gz*" -mtime +$RETENTION_DAYS -delete

log_info "Configuration backup completed successfully!"

# Output backup info
echo ""
echo "======================================"
echo "  CONFIG BACKUP SUMMARY"
echo "======================================"
echo "  File: $(basename $BACKUP_FILE)"
echo "  Size: $BACKUP_SIZE"
echo "  Location: $BACKUP_DIR"
echo "  S3 Path: s3://$S3_BUCKET/$S3_PATH/"
echo "  Retention: $RETENTION_DAYS days"
echo "======================================"

# Verify backup can be extracted (optional)
if [ "${VERIFY_BACKUP:-false}" = "true" ]; then
    log_info "Verifying backup integrity..."
    if [[ "$BACKUP_FILE" == *.gpg ]]; then
        gpg --decrypt --batch --yes "$BACKUP_FILE" | tar -tz > /dev/null
    else
        tar -tzf "$BACKUP_FILE" > /dev/null
    fi
    log_info "Backup integrity verified!"
fi
