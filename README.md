Advanced Internet Technologies - Project
=====
The following project simulates a shopping Web site for Open Data.  The process of validating and processing the credit
card information is not implemented since this is a third-party service.

Our project has been developed using the following technologies:
* Apache Web Server v2.4.7
* PHP v5.5.1
* MySQL database v14.14
* jQuery for client-scripting

Framework
-----
The project is based on a custom MVC framework.

**DIRECTORY STRUCTURE**

```
config              Configuration files used globally for the framework
controllers         Controllers used for the project
datasets            Storage of files used as datasets
docs                Project documentation
    db/             SQL for creation and insertion of initial data
    mockups/        Initial mockups for HTML design
framework           Core of the framework
libs                Libraries used for common methods on the project
models              Translation of database tables into ORM.  These files inherit from framework/BaseModel
views               List of views used for each controller.  Each folder represents the list of views that can be accessed by each controller.
    layout/         Layouts containing the structure of each view
web                 Folder containing resources for Web pages.
    css/            Cascade-style sheets
    img/            Images to be accessed by the views
    js/             Javascript files for client-side development
```

Apache configuration
-----
```
<VirtualHost *:80>
        ServerAlias <host_name>
        ServerAdmin webmaster@localhost
        DocumentRoot /<path_to_project>/web
        <Directory /<path_to_project>/web>
                Options FollowSymLinks
                AllowOverride All
                Order deny,allow

                RewriteEngine on
                RewriteCond %{REQUEST_FILENAME} !-f
                RewriteCond %{REQUEST_FILENAME} !-d
                RewriteRule . index.php
        </Directory>
</VirtualHost>
```

"Rewrite" module must be active in Apache.  To activate it (Ubuntu), use the following commands:
```
$ sudo a2enmod rewrite
$ sudo service apache2 restart
```

Database configuration
-----
To connect to a database, it is necessary to create the file config/db.php with the following settings:
```
<?php
return [
    'driver' => 'mysql',
    'port' => '3306',
    'servername' => 'localhost',
    'database' => '<database_name>',
    'username' => '<username>',
    'password' => '<password>',
    'charset' => 'utf8mb4'
];
```

Permissions
------
For future implementations (document mantainance), folder datasets/ must be configured with read/write permissions.