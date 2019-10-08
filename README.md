# api.sahdo.me

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

intricately-api.sahdo.me is an api rest developed in PHP with Lumen 5.6.4 microframework. He is responsible for the entire intricately.sahdo.me backend.
To test the application go to [intricately.sahdo.me] (http://intricately.sahdo.me)

## Project Tech Stack 

- Linux Server Ubuntu 18.04 (Digital ocean)
- nginx
- Lumen 5.6.4
- php 7.2
- mongodb

## Install PHP

    sudo apt-get install php7.2 php7.2-soap php7.2-fpm php7.2-xml php7.2-bcmath php7.2-mbstring php7.2-mysql php7.2-curl php2-mongodb

If a dependency is missing, composer will point later, and then you must install the dependency manually.

## MongoDB

The connection to mongo may be local, but I have already left the project pointing to my remote server, so it is not necessary to configure it locally. but it is optional. If you want to configure in the local environment you will need to create the database "intricately" and change the settings in .env in the project root.

## Setup

To simulate the project in a local environment you will need to set up a nginx or apache server. I won't go into too much detail, but I will provide the setup I used with nginx.

The first step is to download the project and run the following command:

    composer update
        
This command will install all composer dependencies in the Lumen vendor folder.

That done you will need to create a .env configuration file in the project root.

Create the .env file and paste the following configuration:

    APP_ENV=local
    APP_DEBUG=true
    APP_KEY=
    APP_TIMEZONE=UTC
    
    LOG_CHANNEL=stack
    LOG_SLACK_WEBHOOK_URL=
    
    DB_CONNECTION=mongodb
    DB_HOST=mongodb://165.227.190.249:27777
    DB_PORT=3306
    DB_DATABASE=sahdo_me
    
    JWT_SECRET=JhbGciOiJIUzI1N0eXAiOiJKV1QiLC
    
We will need to change some permissions, first type:
    
    sudo chgrp -R www-data storage

Then:

    sudo chmod -R ug+rwx storage
                  
Now, as I said earlier I'll show you how I set up virtualhost on my nginx server:

    server {
        listen 80;
        listen [::]:80;
    
        root /var/www/intricately-api.sahdo.me/public;
    
        # Add index.php to the list if you are using PHP
        index index.html index.php index.htm index.nginx-debian.html;
    
        server_name intricately-api.sahdo.me;
    
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
    
        # Execute PHP scripts
        location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
           fastcgi_split_path_info ^(.+\.php)(/.*)$;
           include fastcgi_params;
           fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
           fastcgi_param  HTTPS              off;
        }
    
        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        location ~ /\.ht {
            deny all;
        }
    
        location ~* \.(eot|ttf|woff|woff2)$ {
           add_header Access-Control-Allow-Origin *;
        }
    }
    
