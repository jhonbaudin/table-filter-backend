# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Requirement

[Composer](https://getcomposer.org/download/) >= 2.1

[PHP](https://www.php.net/manual/es/install.php) >= 8.0

[Mysql](https://dev.mysql.com/downloads/) >= 8.0

[Git](https://git-scm.com/downloads) >= 2.1


## Instalation

- To clone the repository and install all its dependencies run the following commands:
    ```sh
    git clone https://gitlab.com/jhonbaudin/ic_backend.git ic_backend && cd ic_backend && composer install
    ```
- Next step is to create the mysql database, you can name it as you want, but make sure you match this name with the .env file in your project, also you have to ensure to start the mysql server, then execute the next command:
    ```sh
    mysql -u [MYSQL_USER] -p -e "create database [DATABASE_NAME]"
    ```
- After that, execute the next command:
    ```sh
    cp .env.example .env
    ```
- Open the `.env` file and change this values by yours.
    ```sh
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=homestead
        DB_USERNAME=homestead
        DB_PASSWORD=secret
    ```
- Then we will create the task table and seed data into the table with the next commands:
    ```sh
    php artisan migrate && php artisan db:seed --class="TasksSeeder"
    ```
- And finally you can run the local server with:
    ```sh
    php -S localhost:8000 -t public
    ```
## API Documentation

You can see the API documentation at the following link when your server is running. [localhost:8000/api/documentation](http://localhost:8000/api/documentation).
