# DDD - TEST #

## Documentation ##


### Prerequisite ###

* PHP 8.2


### For Deployment ###

* Clone this repo, and then run Composer in local repo root to pull in dependencies.
    git@github.com:nishantp306/symfony-ddd.git
    cd ddd-test
    composer install

* Generate .pem files for JWT - https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md
* Configure env parameters in .env file
* Then run migrations to create the database tables. 
    php bin/console doctrine:migrations:migrate 
 
* Run symfony serve to run the project


### Usage ###

* To use this project you need to first register a user. User can be regitered using "/register" api with POST method.
    example payload:
        {
            "email": "test@gmail.com",
            "username": "test",
            "password":"test@123"
        }

* After successfully creating the user, hit "/login" api with POST method to generate JWT token, Only loggedin user can access other apis.
    example payload:
        {
              "username": "test",
              "password":"test@123"
        }

* After successfully login user get a JWT token which they have to pass as authorization to access rest apis, like "/api/products".
* Here are the list for product's api:
    - /api/products (Create Product using POST method)
    - /api/products (Get All Product using GET method)
    - /api/products/{id} (Get product by id using GET method)
    - /api/products/{id} (Update product by pasing the id using PUT method)
    - /api/products/{id} (Delete product by id using DELETE method)


### To run the tests ###

* vendor/bin/phpunit
