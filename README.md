#Projet 8 - TO DO LIST APP

[![CodeFactor](https://www.codefactor.io/repository/github/captainfrak/projet8/badge/tosf4.4)](https://www.codefactor.io/repository/github/captainfrak/projet8/overview/tosf4.4)

##(1) Install

Clone the repository :

    https://github.com/captainfrak/Projet8.git

in terminal run :

    composer install
    
import the database in your phpAdmin

and don't forget to add your config in .env file

    DATABASE_URL=mysql://user:password@db_adress:db_port/symfony

lunch the local server

    php bin/console server:start
    

##(2) Tests

To lunch the tests (globally), run

    php bin/phpunit --coverage-html test-coverage
    
after you can open the index.html in coverage-test folder to see the report