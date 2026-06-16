#!/bin/bash
# ============================================
# GudangJateng - Deploy Script untuk aaPanel
# Jalankan script ini di VPS via SSH
# ============================================

set -e

# ====== KONFIGURASI ======
# Ganti path ini sesuai lokasi project di aaPanel
PROJECT_DIR="/www/wwwroot/gudangjateng"

# Warna
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  GudangJateng - aaPanel Deploy Script   ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
echo ""

# Cek apakah jalan sebagai root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Script ini harus dijalankan sebagai root!${NC}"
    echo "Gunakan: sudo bash deploy-aapanel.sh"
    exit 1
fi

# Cek direktori project
if [ ! -d "$PROJECT_DIR" ]; then
    echo -e "${RED}Direktori $PROJECT_DIR tidak ditemukan!${NC}"
    echo -e "${YELLOW}Pastikan project sudah di-upload ke aaPanel.${NC}"
    echo ""
    read -p "Masukkan path project yang benar: " PROJECT_DIR
    if [ ! -d "$PROJECT_DIR" ]; then
        echo -e "${RED}Direktori tetap tidak ditemukan. Batal.${NC}"
        exit 1
    fi
fi

cd "$PROJECT_DIR"
echo -e "${BLUE}➜ Working directory: $PROJECT_DIR${NC}"
echo ""

# ====== STEP 1: Cek PHP ======
echo -e "${YELLOW}[1/7] Mengecek PHP...${NC}"

# Cari PHP 8.4 di aaPanel
PHP_BIN=""
for php_path in /www/server/php/84/bin/php /www/server/php/8.4/bin/php; do
    if [ -f "$php_path" ]; then
        PHP_BIN="$php_path"
        break
    fi
done

if [ -z "$PHP_BIN" ]; then
    echo -e "${RED}PHP 8.4 belum terinstall di aaPanel!${NC}"
    echo -e "${YELLOW}Install PHP 8.4 dulu melalui:${NC}"
    echo "  aaPanel → App Store → PHP 8.4 → Install"
    echo ""
    echo -e "${YELLOW}Extension yang wajib diinstall:${NC}"
    echo "  - pdo_pgsql"
    echo "  - pgsql"
    echo "  - mbstring"
    echo "  - zip"
    echo "  - intl"
    echo "  - gd"
    echo "  - bcmath"
    echo "  - opcache"
    echo "  - fileinfo"
    exit 1
fi

PHP_VERSION=$($PHP_BIN -v | head -n 1)
echo -e "${GREEN}  ✓ $PHP_VERSION${NC}"

# Cek extension yang dibutuhkan
EXTENSIONS=("pdo_pgsql" "pgsql" "mbstring" "zip" "intl" "gd" "bcmath" "opcache")
MISSING_EXT=()

for ext in "${EXTENSIONS[@]}"; do
    if ! $PHP_BIN -m | grep -qi "$ext"; then
        MISSING_EXT+=("$ext")
    fi
done

if [ ${#MISSING_EXT[@]} -gt 0 ]; then
    echo -e "${RED}  ✗ Extension yang belum terinstall:${NC}"
    for ext in "${MISSING_EXT[@]}"; do
        echo "    - $ext"
    done
    echo ""
    echo -e "${YELLOW}  Install melalui: aaPanel → App Store → PHP 8.4 → Install Extensions${NC}"
    exit 1
fi
echo -e "${GREEN}  ✓ Semua extension PHP sudah terinstall${NC}"
echo ""

# ====== STEP 2: Cek Composer ======
echo -e "${YELLOW}[2/7] Mengecek Composer...${NC}"
if ! command -v composer &> /dev/null; then
    echo -e "${YELLOW}  Composer belum ada, menginstall...${NC}"
    curl -sS https://getcomposer.org/installer | $PHP_BIN
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
fi
echo -e "${GREEN}  ✓ $(composer --version)${NC}"
echo ""

# ====== STEP 3: Cek Node.js ======
echo -e "${YELLOW}[3/7] Mengecek Node.js...${NC}"
if ! command -v node &> /dev/null; then
    echo -e "${YELLOW}  Node.js belum ada, menginstall via aaPanel PM2...${NC}"
    # Coba install Node.js 20
    curl -fsSL https://rpm.nodesource.com/setup_20.x | bash - 2>/dev/null || \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    yum install -y nodejs 2>/dev/null || apt-get install -y nodejs
fi
echo -e "${GREEN}  ✓ Node.js $(node --version)${NC}"
echo -e "${GREEN}  ✓ npm $(npm --version)${NC}"
echo ""

# ====== STEP 4: Install Dependencies ======
echo -e "${YELLOW}[4/7] Menginstall dependencies...${NC}"
echo "  → Composer install..."
composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -3

echo "  → npm install..."
npm ci --ignore-scripts 2>&1 | tail -3

echo "  → npm build..."
npm run build 2>&1 | tail -5

echo -e "${GREEN}  ✓ Dependencies terinstall${NC}"
echo ""

# ====== STEP 5: Setup .env ======
echo -e "${YELLOW}[5/7] Mengkonfigurasi .env...${NC}"

if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        cp .env.production .env
        echo -e "${GREEN}  ✓ .env disalin dari .env.production${NC}"
    else
        cp .env.example .env
        echo -e "${GREEN}  ✓ .env disalin dari .env.example${NC}"
    fi

    # Generate APP_KEY
    APP_KEY=$($PHP_BIN -r "echo 'base64:'.base64_encode(random_bytes(32));")
    sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    echo -e "${GREEN}  ✓ APP_KEY di-generate${NC}"

    # Set production values
    sed -i 's|^APP_ENV=.*|APP_ENV=production|' .env
    sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|' .env
else
    echo -e "${YELLOW}  ⚠ File .env sudah ada, tidak ditimpa.${NC}"
    echo -e "${YELLOW}    Pastikan sudah dikonfigurasi dengan benar!${NC}"
fi
echo ""

# ====== STEP 6: Set Permissions ======
echo -e "${YELLOW}[6/7] Mengatur permission...${NC}"
chown -R www:www "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage"
chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
echo -e "${GREEN}  ✓ Permission diatur${NC}"
echo ""

# ====== STEP 7: Optimasi Laravel ======
echo -e "${YELLOW}[7/7] Mengoptimasi Laravel untuk production...${NC}"
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache
$PHP_BIN artisan storage:link 2>/dev/null || true

echo -e "${GREEN}  ✓ Laravel dioptimasi${NC}"
echo ""

# ====== SELESAI ======
echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║         Deploy Berhasil! 🎉             ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}Langkah selanjutnya di aaPanel:${NC}"
echo ""
echo -e "  1. ${YELLOW}Website → Add Site${NC}"
echo "     - Domain: isi dengan domain/IP kamu"
echo "     - Root Directory: $PROJECT_DIR/public"
echo "     - PHP Version: 8.4"
echo ""
echo -e "  2. ${YELLOW}Website → Settings → Configuration${NC}"
echo "     Tambahkan di blok server { }:"
echo "     ┌──────────────────────────────────────┐"
echo "     │ location / {                         │"
echo "     │   try_files \$uri \$uri/               │"
echo "     │     /index.php?\$query_string;        │"
echo "     │ }                                    │"
echo "     └──────────────────────────────────────┘"
echo ""
echo -e "  3. ${YELLOW}Database → PostgreSQL${NC}"
echo "     Pastikan database '$DB_DATABASE' sudah dibuat"
echo ""
echo -e "  4. ${YELLOW}(Opsional) Setup Queue Worker via Supervisor${NC}"
echo "     atau cron: * * * * * $PHP_BIN $PROJECT_DIR/artisan schedule:run"
echo ""
echo -e "${GREEN}Selesai!${NC}"
