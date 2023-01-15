#!/bin/sh

if ! wp core is-installed; then
 wp core install
fi

if ! wp theme is-active; then
    wp theme activate
fi

wp language core install
wp language theme install --all
wp language plugin install --all

wp language core update
wp language plugin update --all 
wp language theme update --all 

wp site switch-language

wp plugin activate --all

wp config shuffle-salts --path=wp-salts
wp config shuffle-salts WP_CACHE_KEY_SALT --force --path=wp-salts

wp rewrite structure --hard

  
if [ ! -z "$1" ]; then
`command -v chmod` 755 $1
fi
#
# wp @production db export - | wp db import -
