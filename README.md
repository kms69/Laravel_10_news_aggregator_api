### installation

##### First step

 `composer install` 

##### Second step
The Passport service provider registers its own database migration directory with the framework, so you should migrate your database after installing the package. The Passport migrations will create the tables your application needs to store clients and access tokens:

`php artisan migrate`

### Test
Third step run the phpunit test command
`./vendor/bin/phpunit`
