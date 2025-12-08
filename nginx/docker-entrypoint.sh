#!/bin/sh
set -e

# Generate self-signed SSL certificate if it doesn't exist
if [ ! -f "/etc/nginx/certs/cert.pem" ] || [ ! -f "/etc/nginx/certs/key.pem" ]; then
    echo "Generating self-signed SSL certificate..."
    mkdir -p /etc/nginx/certs
    openssl req -x509 -newkey rsa:4096 -nodes \
        -out /etc/nginx/certs/cert.pem \
        -keyout /etc/nginx/certs/key.pem \
        -days 365 \
        -subj "/CN=localhost/O=Train Routing/C=US" 2>/dev/null
    echo "SSL certificate generated"
fi

# Start nginx
exec nginx -g "daemon off;"
