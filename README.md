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
  * instructor1@stevens.edu / instructor
  * instructor2@stevens.edu / instructor
  * instructor3@stevens.edu / instructor
  * student@stevens.edu / student

* **Courses**:
 * 22 Courses with Instructors 1,2, and 3 assigned as coordinator (i.e. Instructor) for various courses

* **Queues**:
 * Add/Drop Queue for each course

* **Queue Elements**:
 * Add/Drop Form Element Required for each of the Add/Drop Course Queues

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

## Security
The application allows new users to register. No functionality other than registration is available to guest users. Once registerd, users can authenticate by logging in with their credentials. By default, new users are registered without any special roles. Special roles are ADMIN, INSTRUCTOR, and ADVISOR. These can be assigned by an Administrator when creating a new user or updating an existing user's profile. All users can create submissions to active queues, track their submissions, and create comments for their submissions.

Specialize role functionality includes:
 * ADMIN
 *  * Application wide access and functionality
 *  * Manage Users and Courses
 * INSTRUCTOR
 *  * Manage queues for courses that they are assigned
 *  * Manage all submissions for courses that they are assigned
 * ADVISOR
 *  * Manage personal queues that they create
 *  * Manage all submissions for their personal queue
