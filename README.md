MARK.ME
=======

Online bookmark manager
-----------------------

## author : M.Paraiso

inspired by bookmark.ly : http://bookmarkly.com/

### DEMO : http://markme.alwaysdata.net

+ Bookmark sites fast
+ Add description and tags
+ Import and Export your bookmarks from and to popular browsers
+ Search and filter through you bookmarks
+ Access your bookmarks anywhere!

#### INSTALLATION

##### requirements

+ an apache webserver
+ php 5.3.*
+ mysql database

##### configuration

+ set up a virtualhost on the server

+ declare the following envirronment variables ( in a .htaccess file for instance ):

    + MARKME_DB_DRIVER ( should be pdo_mysql )  
    + MARKME_DB_DATABASE_NAME (database name)
    + MARKME_DB_HOST ( exemple : locahost )
    + MARKME_DB_USERNAME (database username )
    + MARKME_DB_PASSWORD (database password )

+ the webroot is the www folder

+ get composer
    + http://getcomposer.org/
    + in the repository folder , install composer packages : 
        php /path-to-composer/composer.phar install

###### Why

+ Help learn Silex Framework : http://silex.sensiolabs.org
+ Help learn AngularJS Framework : http://angularjs.org/
+ Help learn AngularJS / Twitter Bootstrap integration : http://twitter.github.com/bootstrap/



    