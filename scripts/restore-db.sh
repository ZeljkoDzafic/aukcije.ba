#!/bin/bash

# ===================================
# AUKCIJSKA PLATFORMA - DATABASE RESTORE
# ===================================
# Restore PostgreSQL database from backup
# Usage: ./scripts/restore-db.sh [backup_file]

set -e

# Configuration
BACKUP_DIR="${BACKUP_DIR:-./backups/postgres}"
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

# Check if backup file is provided
if [ -z "$1" ]; then
    # Find latest backup
    BACKUP_FILE=$(ls -t "$BACKUP_DIR"/aukcije_db_*.sql.gz* 2>/dev/null | head -1)
    
    if [ -z "$BACKUP_FILE" ]; then
        log_error "No backup file found in $BACKUP_DIR"
        exit 1
    fi
    
    log_info "Using latest backup: $BACKUP_FILE"
else
    BACKUP_FILE="$1"
fi

# Verify backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    log_error "Backup file not found: $BACKUP_FILE"
    exit 1
fi

# Confirm restore
log_warning "WARNING: This will DROP all tables and restore from backup!"
log_warning "Target database: ${DB_DATABASE:-aukcije}"
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    log_info "Restore cancelled"
    exit 0
fi

# Decrypt backup if encrypted
if [[ "$BACKUP_FILE" == *.gpg ]]; then
    if [ -z "$ENCRYPTION_KEY" ]; then
        log_error "Encryption key required but not provided!"
        exit 1
    fi
    
    log_info "Decrypting backup..."
    DECRYPTED_FILE="${BACKUP_FILE%.gpg}"
    gpg --decrypt --batch --yes \
        --passphrase "$ENCRYPTION_KEY" \
        --output "$DECRYPTED_FILE" "$BACKUP_FILE"
    BACKUP_FILE="$DECRYPTED_FILE"
fi

# Check if running in Docker
if command -v docker &> /dev/null && docker ps | grep -q aukcije_postgres; then
    log_info "Restoring from Docker container..."
    
    # Copy backup to container
    docker cp "$BACKUP_FILE" aukcije_postgres:/tmp/backup.sql.gz
    
    # Restore inside container
    docker compose exec -T postgres sh -c "
        dropdb -U aukcije aukcije 2>/dev/null || true
        createdb -U aukcije aukcije
        gunzip -c /tmp/backup.sql.gz | psql -U aukcije -d aukcije
    "
else
    log_info "Restoring directly..."
    
    # Drop and recreate database
    dropdb -h "${DB_HOST:-127.0.0.1}" \
           -U "${DB_USERNAME:-aukcije}" \
           "${DB_DATABASE:-aukcije}" 2>/dev/null || true
    
    createdb -h "${DB_HOST:-127.0.0.1}" \
             -U "${DB_USERNAME:-aukcije}" \
             "${DB_DATABASE:-aukcije}"
    
    # Restore
    gunzip -c "$BACKUP_FILE" | psql -h "${DB_HOST:-127.0.0.1}" \
                                    -U "${DB_USERNAME:-aukcije}" \
                                    -d "${DB_DATABASE:-aukcije}"
fi

log_info "Database restored successfully!"

# Cleanup decrypted file
if [ -n "$DECRYPTED_FILE" ] && [ -f "$DECRYPTED_FILE" ]; then
    rm "$DECRYPTED_FILE"
fi

# Verify restore
log_info "Verifying restore..."
TABLE_COUNT=$(psql -h "${DB_HOST:-127.0.0.1}" \
                   -U "${DB_USERNAME:-aukcije}" \
                   -d "${DB_DATABASE:-aukcije}" \
                   -t -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public';")

log_info "Tables restored: $TABLE_COUNT"

echo ""
echo "======================================"
echo "  RESTORE SUMMARY"
echo "======================================"
echo "  Backup: $(basename $BACKUP_FILE)"
echo "  Database: ${DB_DATABASE:-aukcije}"
echo "  Tables: $TABLE_COUNT"
echo "======================================"
