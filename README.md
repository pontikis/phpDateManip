# phpDateManip

Date manipulation using php DateTime

Copyright Christos Pontikis http://www.pontikis.net

Project page https://github.com/pontikis/phpDateManip

License MIT https://raw.github.com/pontikis/phpDateManip/master/MIT_LICENSE


## Features

1. International dates supported
2. Format DateTime
3. Create date ranges
    * current_year
    * current_month
    * current_week_from_monday
    * current_week_from_sunday
    * current_day
4. Modifies (increase or decrease) a datetime string
5. Utility functions:
    * isValidTimezone()
    * isValidDateTimeString()
6. Multilanguage

## Dependencies

* php DateTime class http://php.net/manual/en/class.datetime.php (PHP 5 >= 5.2.0, PHP 7)

* For international dates support, php intl module (http://php.net/manual/en/book.intl.php) is required. See http://php.net/manual/en/class.intldateformatter.php (PHP 5 >= 5.3.0, PHP 7, PECL intl >= 1.0.0)


## Files
 
1. ``phpDateManip.class.php`` php class


## Documentation

See ``docs/doxygen/html`` for html documentation of ``phpDateManip`` class. 