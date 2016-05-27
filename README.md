Advanced Internet Technologies - Project
======================================== 
The following project simulates a shopping Web site for purchasing datasets. The application provides the possibility of registering with basic information, browse among datasets, add them to a shopping basket and buying them.  The process of validating and processing the credit card information is not fully implemented since this is a third-party service.

Our project has been developed using the following technologies:

* Apache Web Server v2.4.7
* PHP v5.5.1
* MySQL database v14.14
* jQuery for client-scripting

User Roles
------
There are two user roles:

* Guest: any user can search and view information about dataset.  Options like adding to cart, removing from cart, etc., won’t be shown unless the user logs into the system.
* Registered user: for this role it is possible to add datasets into the shopping cart, make purchases and download the files.

Site map
--------
```
/site/index                       Home page (with search page for finding datasets by text queries or tags)  
/site/search                      Results page (with form to search more datasets)
/site/overview/id/<document_id>   Page to display details about a dataset (with form to search more datasets)
/site/cart                        Shopping cart page.  Accessible only by registered users
/site/checkout                    Process of checking out the datasets stored in shopping cart.  Accessible only by registered users
/user/register                    Registration form.  Accessible only if no user is logged in
/user/login                       Login form for registered users.  The page is accessible only if no user is logged in
/user/orders                      List of orders and purchased datasets.  Accessible only by registered users
/user/edit                        Page to edit user information.  Accessible only by registered users
/user/logout                      Logout from the system
```

Framework
---------
We developed the system designing a custom Model - View - Controller (MVC) framework.

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
web                 Folder containing resources for Web pages, including index.php file.
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
                
                #Error handling
                ErrorDocument 403 /errors/403.html

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

Folder permissions
------------------
Currently, our project is not able to add / delete datasets dynamically.  However, for future implementations, datasets/ folder must be configured with read/write permissions.