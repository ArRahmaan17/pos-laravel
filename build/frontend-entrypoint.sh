#!/bin/sh
set -eu

manifest_hash="$(sha256sum package.json | cut -d ' ' -f 1)"
installed_hash="$(cat node_modules/.package-manifest.sha256 2>/dev/null || true)"

if [ ! -x node_modules/.bin/vite ] || [ "$manifest_hash" != "$installed_hash" ]; then
    echo "Synchronizing frontend dependencies..."
    npm install --prefer-offline
    printf '%s\n' "$manifest_hash" > node_modules/.package-manifest.sha256
fi

exec npm run dev -- --host 0.0.0.0
