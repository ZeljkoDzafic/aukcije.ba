#!/bin/bash

# ===================================
# AUKCIJSKA PLATFORMA - LOCAL SETUP
# ===================================
# One-command setup for local development
# Usage: ./scripts/setup-local.sh

set -e

echo "======================================"
echo "  AUKCIJSKA PLATFORMA - LOCAL SETUP"
echo "======================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker compose &> /dev/null; then
    echo -e "${RED}Docker Compose is not installed. Please install Docker Compose first.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Docker and Docker Compose are installed${NC}"
echo ""

# Copy environment file
if [ ! -f .env ]; then
    echo "Creating .env file from .env.docker..."
    cp .env.docker .env
    echo -e "${GREEN}✓ .env file created${NC}"
else
    echo -e "${YELLOW}! .env file already exists, skipping...${NC}"
fi

# Generate APP_KEY if not set
if grep -q "APP_KEY=$" .env || ! grep -q "APP_KEY=" .env; then
    echo "Generating APP_KEY..."
    docker run --rm -v "$(pwd)":/var/www/html -w /var/www/html php:8.3-cli php artisan key:generate --show
    echo ""
    echo -e "${YELLOW}! Please copy the above key to your .env file${NC}"
else
    echo -e "${GREEN}✓ APP_KEY is already set${NC}"
fi

# Start Docker containers
echo ""
echo "Starting Docker containers..."
docker compose up -d

# Wait for services to be ready
echo ""
echo "Waiting for services to be ready..."
sleep 10

# Check if PostgreSQL is ready
echo "Waiting for PostgreSQL..."
while ! docker compose exec -T postgres pg_isready -U aukcije > /dev/null 2>&1; do
    sleep 1
done
echo -e "${GREEN}✓ PostgreSQL is ready${NC}"

# Check if Redis is ready
echo "Waiting for Redis..."
while ! docker compose exec -T redis redis-cli ping > /dev/null 2>&1; do
    sleep 1
done
echo -e "${GREEN}✓ Redis is ready${NC}"

# Install PHP dependencies
echo ""
echo "Installing PHP dependencies (Composer)..."
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader
echo -e "${GREEN}✓ Composer dependencies installed${NC}"

# Install Node.js dependencies
echo ""
echo "Installing Node.js dependencies (npm)..."
docker compose exec -T app npm install
echo -e "${GREEN}✓ Node.js dependencies installed${NC}"

# Run database migrations
echo ""
echo "Running database migrations..."
docker compose exec -T app php artisan migrate --force
echo -e "${GREEN}✓ Database migrations completed${NC}"

# Seed the database
echo ""
echo "Seeding database with demo data..."
docker compose exec -T app php artisan db:seed --force
echo -e "${GREEN}✓ Database seeded${NC}"

# Generate IDE helper files (optional)
echo ""
echo "Generating IDE helper files..."
docker compose exec -T app php artisan ide-helper:generate 2>/dev/null || true
docker compose exec -T app php artisan ide-helper:models --nowrite 2>/dev/null || true
echo -e "${GREEN}✓ IDE helpers generated${NC}"

# Build frontend assets
echo ""
echo "Building frontend assets (Vite)..."
docker compose exec -T app npm run build
echo -e "${GREEN}✓ Frontend assets built${NC}"

# Clear and cache config (optional for dev)
echo ""
echo "Clearing caches..."
docker compose exec -T app php artisan optimize:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

# Create storage link
echo ""
echo "Creating storage symlink..."
docker compose exec -T app php artisan storage:link
echo -e "${GREEN}✓ Storage symlink created${NC}"

# Final summary
echo ""
echo "======================================"
echo -e "${GREEN}  SETUP COMPLETE!${NC}"
echo "======================================"
echo ""
echo "Access the application:"
echo "  - Application: http://localhost"
echo "  - Mailpit:     http://localhost:8025"
echo "  - Meilisearch: http://localhost:7700"
echo "  - MinIO:       http://localhost:9000 (minioadmin/minioadmin)"
echo ""
echo "Start development servers:"
echo "  docker compose exec app php artisan serve --host=0.0.0.0 --port=8000"
echo "  docker compose exec app npm run dev"
echo ""
echo "Useful commands:"
echo "  docker compose down              # Stop all containers"
echo "  docker compose logs -f app       # View app logs"
echo "  docker compose exec app php artisan tinker  # Laravel tinker"
echo ""
echo -e "${GREEN}Happy coding!${NC}"
