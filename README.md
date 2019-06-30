<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></a></p>

<p align="center">
<a href="https://travis-ci.org/larapie/larapie"><img src="https://travis-ci.org/larapie/larapie.svg?branch=master" alt="Build Status"></a>
<a href="https://scrutinizer-ci.com/g/larapie/larapie/"><img src="https://scrutinizer-ci.com/g/larapie/larapie/badges/coverage.png?b=master" alt="Code Coverage"></a>
<a href="https://scrutinizer-ci.com/g/larapie/larapie/"><img src="https://scrutinizer-ci.com/g/larapie/larapie/badges/quality-score.png?b=master" alt="Code Quality"></a>
<a href="https://github.styleci.io/repos/193496646"><img src="https://github.styleci.io/repos/193496646/shield?branch=master" alt="StyleCI"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

# Larapie

##### Installation:
```php
composer create-project larapie/larapie
composer install
php artisan larapie:install
```

Open the .env file and enter your database details then run:
```php
php artisan db:reset --seed
```

##### WARNING:
This repository is currently in alpha. Heavy development is going on and things are possibly going to break. If you want to use it please tag use a tagged release instead of the master since these are tested and should be working. For now please do not use this in production environments. An announcement will be made when it is considered production ready.


### What is Larapie
Larapie is a framework for easily building scalable and testable api's with laravel & PHP.

It is heavily inspired on apiato wich is unfortunately abondoned and not up to date anymore at the moment. We will reuse a lot of documentation from apiato throughout this project to speed up the writing. Larapie aims to be more intuitive, performant and maintainable than apiato. It is designed to help you build scalable API's faster, by providing tools and functionalities that facilitates the development of any API-Centric App.

Why!? Because setting up a solid API from scratch is time consuming (and of course, time is money!). Apiato gives you the core features of robust API's fully documented, for free; so you can focus on writing your business logic, thus deliver faster to your clients.

### Requirements
- Php >= 7.1
- PHP Extensions: OpenSSL, PDO, Mbstring, Tokenizer
- Composer
- Webserver (nginx, apache, lightspeed, ..)
- Database (mysql, mariadb, postgres, sqllite)

##### Optional:
- In Memory & Queue Database (Redis) 

### Features
- Auto registration of resources (commands, configs, event listeners, module schedules, factories, policies, observers, routes, ..).
- Action (validation, authorization & business logic) driven design.
- Modular structure (splitted in foundation, modules & packages)
- Seperate Api domain or route.
- Api versioning.
- Code generator (very early stages)
- Preconfigured usefull Packages (Cors, Horizon, Idehelper, Sentry, Telescope)
- Authorization (permissions & roles)
- Authentication (Auth0) 
- Transformers. Laravel api resources with fractal functionality. (larapie/transformer)
- Repositories. (very early stages)
- Guards. Fluent reusable objects to throw exceptions based on conditions. (larapie/guard)

