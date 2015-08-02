# DocumentQueue

Description of application goes here

## Dependencies

1. Apache `mod_rewrite`
2. PHP >=5.6
3. Mysql

## Installation

Create database using `/app/sql/documentmanager.sql`

Default database creates the following:
* **Roles:**
  * ADMIN
  * ADVISOR
  * INSTRUCTOR

* **Users** (email/password):
  * admin@stevens.edu / admin
  * advisor@stevens.edu / advisor
  * instructor@stevens.edu / instructor
  * student@stevens.edu / student


## Configuration

By default, the `/app/config` folder contains two configuration files (development.php and production.php). You can create as many for each of your environments as needed. The application is initially configured to use the `/app/config/development.php` configuration file. To specify which configuration file to use, modify the `/mode.php` configuration file to use.

Modify the values in these files to fit your specific environment. At a minimum, you will need to ensure the following values are correct for your environment:

* `app.url`: Fully qualified URL to the location of the `public` folder (e.g. *http://YOURDOMAIN OR IP/APP PATH/public*)
* `db`: Settings for your database
* `mail`: Settings for your SMTP server
