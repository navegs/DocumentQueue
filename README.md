# DocumentQueue

Description of application goes here

## Dependencies

1. Apache `mod_rewrite`, `php5_module`
2. PHP >=5.6
3. Mysql

## Installation

### Database
* Create database using `/app/sql/documentmanager.sql`
* Create database user using your preferred method and ensure the user has permissions to the database 

By default, the script creates the `document_manager` database, required tables and the following seed data:
* **Roles:**
  * ADMIN
  * ADVISOR
  * INSTRUCTOR

* **Users** (email/password):
  * admin@stevens.edu / admin
  * advisor@stevens.edu / advisor
  * instructor@stevens.edu / instructor
  * student@stevens.edu / student

### Web
* For a quick, unsecure development or testing installation, place the contents of this application in a folder of your choice. For example, create a folder called doc_manager under you web server's document root and place all files within this folder. Note: This type of setup should not be used for production.
* For security reasons, the only folder that should be accessable from the web is the `/public` folder. Otherwise, sensistive configuration and application files will be accessable from the web.
* For a production environment, ensure only the `/public` folder is accessable from the web. Using Apache web server, the `Directory`, `Alias`, or `Virtual Hosts` directives can be used to achieve this. 

## Configuration

By default, the `/app/config` folder contains two configuration files (development.php and production.php). You can create as many for each of your environments as needed. The application is initially configured to use the `/app/config/development.php` configuration file. To specify which configuration file to use, modify the `/mode.php` to specify which configuration file to use.

Modify the values in these files to fit your specific environment. At a minimum, you will need to ensure the following values are correct for your environment:

* `app.url`: Fully qualified URL to the location of the `public` folder (e.g. *http://YOURDOMAIN OR IP/APP PATH/public*)
* `db`: Settings for your database
* `mail`: Settings for your SMTP server
