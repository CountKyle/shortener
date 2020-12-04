# Simple Link Shortener
Super simple url shortener, written in php7.4 and mysql8.

## Usage
To shorten a long url, make a get request like so:

http://shortlinks.test/shorten?url=http://www.bing.com

This will return a shortcode for you to use.

To access a shortlink,  go to: http://shortlinks.test/qkt880 (for example)

This will increment the hits counter.

## Setup
1. Create a new MySql database. 
2. Create a vhost on apache (using http://shortlinks.test if you wish to test)
2. Copy the .env.sample and populate the database credentials.
3. Run composer install to install dependencies (Guzzle, phpunit and Nette for database abstraction)
4. run php migrate.php to create the table.
5. run vendor/bin/phpunit tests/ShortenerTest.php to test the application. 

Note! The tests will only work locally if the application was set up to be accessed at http://shortlinks.test/ 