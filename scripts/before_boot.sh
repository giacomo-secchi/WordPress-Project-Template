#!/bin/sh

if ! test -f  wp-salts/wp-config.php; then
    cp wp-salts/wp-config-sample.php wp-salts/wp-config.php
fi

if ! test -f .env; then
    cp .env.example .env
fi

if ! test -f wp-cli.local.yml; then
    cp wp-cli.local.yml.example wp-cli.local.yml
fi


