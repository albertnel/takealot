# Takealot Assessment

### Installation

For the sake of completion, the ``/vendor`` directory is included in the submission as well as the git repo, but this should never be the case for a production application. If the folder does not exist or is empty, please install using the following in the root directory of the project:
``composer install``

### Note on included files

Ideally you would have a ``.gitignore`` file to exclude the ``/vendor``, ``/src/templates/cache`` and so forth. For the sake of having a working project without the need of further technologies such as Composer, I have submitted everything to this repo. For production solutions a ``.gitignore`` file is absolutely mandatory.

### Running the application

Please navigate to the ``/public`` folder and start your server from this directory. With PHP it's as easy as: ``php -S localhost:8000`` or similar configuration.

### Unit testing

First please verify that PHPUnit is successfully installed via composer. Check that the ``/vendor/bin/phpunit`` symbolic link exists. If not, please re-install PHPUnit as follows:
``composer require phpunit/phpunit``

I have added a ``phpunit.xml`` file in the root of the project that sets up the colour output and test paths. If you want to change the path to tests, you need to change this line:
``<directory>./src/tests/</directory>``

Otherwise, just leave it as is to use the existing tests.

To run the unit tests, from the root of the project run: ``vendor/bin/phpunit``.

You should see output similar to this:

```
PHPUnit 4.8.25 by Sebastian Bergmann and contributors.

..

Time: 53 ms, Memory: 4.00MB

OK (2 tests, 2 assertions)
```

### Technologies used

* FastRoute for routing. https://github.com/nikic/FastRoute
* Twig templating engine. http://twig.sensiolabs.org/
* PHPUnit unit testing. https://phpunit.de
