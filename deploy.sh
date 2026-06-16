#!/bin/bash
# ============================================
# GudangJateng - Deployment Script
# Untuk VPS dengan Docker
# ============================================

set -e

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN} GudangJateng Deployment Script${NC}"
echo -e "${GREEN}========================================${NC}"

# Cek Docker
if ! command -v docker &> /dev/null; then
    echo -e "${YELLOW}Docker belum terinstall. Menginstall...${NC}"
    curl -fsSL https://get.docker.com | sh
    systemctl enable docker
    systemctl start docker
    echo -e "${GREEN}Docker berhasil diinstall!${NC}"
fi

# Cek Docker Compose
if ! docker compose version &> /dev/null; then
    echo -e "${RED}Docker Compose tidak ditemukan!${NC}"
    exit 1
fi

# Cek file .env
if [ ! -f .env ]; then
    echo -e "${YELLOW}File .env belum ada. Menyalin dari .env.production...${NC}"
    cp .env.production .env

    # Generate APP_KEY
    APP_KEY=$(docker run --rm php:8.4-alpine php -r "echo 'base64:'.base64_encode(random_bytes(32));")
    sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    echo -e "${GREEN}APP_KEY berhasil di-generate!${NC}"
fi

echo ""
echo -e "${YELLOW}[1/4] Building Docker image...${NC}"
docker compose build --no-cache

echo ""
echo -e "${YELLOW}[2/4] Starting containers...${NC}"
docker compose up -d

echo ""
echo -e "${YELLOW}[3/4] Running migrations...${NC}"
docker compose exec app php artisan migrate --force

echo ""
echo -e "${YELLOW}[4/4] Optimizing Laravel...${NC}"
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan storage:link 2>/dev/null || true

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN} Deploy berhasil! 🎉${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Container status:"
docker compose ps
echo ""
echo -e "Akses aplikasi di: ${YELLOW}http://$(hostname -I | awk '{print $1}')${NC}"
