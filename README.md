# Image Gallery
Image Gallery built with symfony 4

## Requirements

- php7.1 + 
- [composer](https://getcomposer.org/)
- mysql5.7 +
- [jpeg support](https://github.com/magiclyde/magiclyde.github.io/blob/master/archives/play-php-on-ubuntu.md#install-jpeg)
- [beanstalkd](https://beanstalkd.github.io/)
- [supervisord](http://supervisord.org/)


## Installation

1. install dependencies with composer

        cd /path/to/gallery  
        composer install

2. configuring the database

		vim .env

		# .env (or override DATABASE_URL in .env.local to avoid committing your changes)

		# customize this line!
		DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

3. create the db_name database and schema

        php bin/console doctrine:database:create
        php bin/console doctrine:migrations:migrate

4. run php script as daemon using supervisord  

        see ./scripts/setup-supervisor.sh


# Refer
https://www.sitepoint.com/building-image-gallery-blog-symfony-flex-setup/   
https://symfony.com/doc/current/index.html#gsc.tab=0  
