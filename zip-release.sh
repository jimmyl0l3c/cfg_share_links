#!/bin/bash

# Build frontend
rm -rf js/*
make build-js-production

# Define folder (and archive) name
FOLDER_NAME=cfg_share_links

# Create empty folder in temp
if [ ! -d "/tmp/$FOLDER_NAME" ]
then
  mkdir /tmp/$FOLDER_NAME
else
  rm -rf /tmp/$FOLDER_NAME/*
fi

# Copy files to zip
cp -r appinfo css img js l10n lib templates vendor CHANGELOG.md COPYING README.md /tmp/$FOLDER_NAME

# If zip exists, delete it
[ -f "$FOLDER_NAME.tar.gz" ] && { rm $FOLDER_NAME.tar.gz; }
[ -f "/tmp/$FOLDER_NAME.tar.gz" ] && { rm /tmp/$FOLDER_NAME.tar.gz; }

# Save current dir and cd to /tmp
WORKING_DIR=$(pwd)
cd /tmp || exit 1

# Zip it
tar -czvf $FOLDER_NAME.tar.gz $FOLDER_NAME

# Copy zip to working dir
cp $FOLDER_NAME.tar.gz "$WORKING_DIR"

# Clean
rm -rf /tmp/$FOLDER_NAME*

cd "$WORKING_DIR" || exit 1

# Sign
openssl dgst -sha512 -sign ~/.nextcloud/certificates/cfg_share_links.key $FOLDER_NAME.tar.gz | openssl base64 > release_signature
