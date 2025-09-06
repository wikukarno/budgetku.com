#!/usr/bin/env bash
set -euo pipefail

# Simple dev runner: starts Laravel server and Vite together.
# Usage: bash scripts/dev.sh

# Choose port for Laravel (default 8000)
LARAVEL_PORT=${LARAVEL_PORT:-8000}
LARAVEL_HOST=${LARAVEL_HOST:-127.0.0.1}

echo "[dev] Starting Laravel on http://${LARAVEL_HOST}:${LARAVEL_PORT}"
php artisan serve --host="${LARAVEL_HOST}" --port="${LARAVEL_PORT}" &
PHP_PID=$!

cleanup() {
  echo "\n[dev] Shutting down..."
  if kill -0 "$PHP_PID" >/dev/null 2>&1; then
    kill "$PHP_PID" >/dev/null 2>&1 || true
    wait "$PHP_PID" >/dev/null 2>&1 || true
  fi
}
trap cleanup EXIT INT TERM

PM="npm"
if [ -f pnpm-lock.yaml ]; then
  PM="pnpm"
elif [ -f yarn.lock ]; then
  PM="yarn"
fi

ARGS=()
if [ "${VITE_HOST:-}" != "" ]; then
  ARGS+=(-- --host="${VITE_HOST}")
fi

echo "[dev] Starting Vite dev server with $PM"
$PM run dev "${ARGS[@]:-}"
