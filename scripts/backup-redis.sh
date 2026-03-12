#!/bin/bash

# ===================================
# AUKCIJSKA PLATFORMA - REDIS BACKUP
# ===================================
# Backup Redis AOF/RDB files to S3
# Usage: ./scripts/backup-redis.sh

set -e

# Configuration
BACKUP_DIR="${BACKUP_DIR:-./backups/redis}"
S3_BUCKET="${AWS_BUCKET:-aukcije-backups}"
S3_PATH="redis/$(date +%Y/%m/%d)"
RETENTION_DAYS="${BACKUP_RETENTION_DAYS:-7}"

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
BACKUP_FILE="$BACKUP_DIR/aukcije_redis_$TIMESTAMP.rdb"

log_info "Starting Redis backup..."

# Check if running in Docker
if command -v docker &> /dev/null && docker ps | grep -q aukcije_redis; then
    # Copy RDB file from Docker container
    docker cp aukcije_redis:/data/dump.rdb "$BACKUP_FILE"
else
    # Copy from local Redis data directory
    REDIS_DATA="${REDIS_DATA:-/var/lib/redis}"
    if [ -f "$REDIS_DATA/dump.rdb" ]; then
        cp "$REDIS_DATA/dump.rdb" "$BACKUP_FILE"
    else
        log_error "Redis dump.rdb not found!"
        exit 1
    fi
fi

# Verify backup
if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log_info "Backup created successfully: $BACKUP_SIZE"
else
    log_error "Backup failed!"
    exit 1
fi

# Compress backup
log_info "Compressing backup..."
gzip "$BACKUP_FILE"
BACKUP_FILE="$BACKUP_FILE.gz"

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
find "$BACKUP_DIR" -name "aukcije_redis_*.rdb.gz" -mtime +$RETENTION_DAYS -delete

log_info "Redis backup completed successfully!"

# Output backup info
echo ""
echo "======================================"
echo "  REDIS BACKUP SUMMARY"
echo "======================================"
echo "  File: $(basename $BACKUP_FILE)"
echo "  Size: $BACKUP_SIZE"
echo "  Location: $BACKUP_DIR"
echo "  S3 Path: s3://$S3_BUCKET/$S3_PATH/"
echo "  Retention: $RETENTION_DAYS days"
echo "======================================"
