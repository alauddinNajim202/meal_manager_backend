#!/bin/bash

APP_NAME=$1
BASE_PATH=/var/www/$APP_NAME
RELEASE_NAME=$(date +"%Y%m%d_%H%M%S")

cd $BASE_PATH

mkdir -p releases/$RELEASE_NAME
cp -r $BUILD_SOURCESDIRECTORY/* releases/$RELEASE_NAME/

mkdir -p shared/uploads
chmod -R 777 shared/uploads
ln -sfn shared/.env releases/$RELEASE_NAME/.env
ln -sfn shared/storage releases/$RELEASE_NAME/storage
mkdir -p releases/$RELEASE_NAME/public
ln -sfn $BASE_PATH/shared/uploads releases/$RELEASE_NAME/public/uploads
chmod -R 777 releases/$RELEASE_NAME/storage

cd releases/$RELEASE_NAME

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

ln -sfn releases/$RELEASE_NAME current

sudo systemctl reload nginx
