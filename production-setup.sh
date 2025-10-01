#!/bin/bash

# Production PostgreSQL Migration Setup Script
# Run this on your production server

echo "🚀 Production PostgreSQL Migration Setup"
echo "======================================="

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo "❌ Please run as root or with sudo"
    exit 1
fi

# Update package list
echo "📦 Updating package list..."
apt update

# Install PostgreSQL
echo "🐘 Installing PostgreSQL..."
apt install -y postgresql postgresql-contrib

# Install PHP PostgreSQL extension
echo "🐘 Installing PHP PostgreSQL extension..."
apt install -y php-pgsql

# Start PostgreSQL service
echo "🔄 Starting PostgreSQL service..."
systemctl start postgresql
systemctl enable postgresql

# Create database and user
echo "👤 Setting up PostgreSQL database..."
sudo -u postgres psql << EOF
CREATE DATABASE budgetku_postgres;
CREATE USER budgetku_user WITH PASSWORD 'your_secure_password_here';
GRANT ALL PRIVILEGES ON DATABASE budgetku_postgres TO budgetku_user;
ALTER USER budgetku_user CREATEDB;
\q
EOF

# Create backup directory
echo "💾 Creating backup directory..."
mkdir -p /var/backups/mysql
chmod 755 /var/backups/mysql

# Install monitoring tools
echo "📊 Installing monitoring tools..."
apt install -y htop iotop

# Configure PostgreSQL for production
echo "⚙️ Configuring PostgreSQL..."
PG_VERSION=$(sudo -u postgres psql -c "SELECT version();" | grep -oP '(?<=PostgreSQL )\d+')
PG_CONFIG_DIR="/etc/postgresql/${PG_VERSION}/main"

# Backup original config
cp ${PG_CONFIG_DIR}/postgresql.conf ${PG_CONFIG_DIR}/postgresql.conf.backup

# Update PostgreSQL configuration for production
cat >> ${PG_CONFIG_DIR}/postgresql.conf << EOF

# Production optimizations
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 128MB
checkpoint_completion_target = 0.9
wal_buffers = 16MB
max_connections = 100

# Logging
log_min_duration_statement = 1000
log_line_prefix = '%t [%p]: [%l-1] user=%u,db=%d,app=%a,client=%h '
log_checkpoints = on
log_connections = on
log_disconnections = on
log_lock_waits = on

# Performance monitoring
track_activities = on
track_counts = on
track_functions = all
EOF

# Restart PostgreSQL
echo "🔄 Restarting PostgreSQL..."
systemctl restart postgresql

# Test connection
echo "🧪 Testing PostgreSQL connection..."
if sudo -u postgres psql -c "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ PostgreSQL connection successful"
else
    echo "❌ PostgreSQL connection failed"
    exit 1
fi

# Create Laravel environment file template
echo "📝 Creating production .env template..."
cat > /tmp/env-production-template << 'EOF'
# Production PostgreSQL Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=budgetku_postgres
DB_USERNAME=budgetku_user
DB_PASSWORD=your_secure_password_here

# Keep MySQL as backup (rename mysql_old)
MYSQL_HOST=127.0.0.1
MYSQL_PORT=3306
MYSQL_DATABASE=budgetku
MYSQL_USERNAME=root
MYSQL_PASSWORD=your_mysql_password
EOF

echo "✅ Production setup completed!"
echo ""
echo "Next steps:"
echo "1. Update your .env file with PostgreSQL settings"
echo "2. Run: php artisan migrate:production-mysql-to-pgsql --backup-only"
echo "3. Test the backup"
echo "4. Schedule maintenance window"
echo "5. Run full migration: php artisan migrate:production-mysql-to-pgsql"
echo ""
echo "⚠️  IMPORTANT: Change the default passwords in the commands above!"
echo "🔍 Monitor logs: tail -f /var/log/postgresql/postgresql-${PG_VERSION}-main.log"