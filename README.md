# Room Booking

## Requirements
* PHP (tested on PHP 5.3)
* MySQL
* Apache
* CAS authentication
    * If your organization uses a different login system, you will need to modify `application/controllers/login.php`

## Installation
1. Copy the files on to a folder on a LAMP (Linix - Apache - Mysql - PHP)
1. Create a database, and populate it using the `install.sql` file
1. Make sure the `temp` folder is writable by the web user
1. Configure the application by modifying files in the `application/config/` folder
    1. Remove the `.sample` suffix from all files
    1. `cas.php`, `database.php`, & `email.php` should be a straight forward configuration
    1. `config.php`
        1. Change the `$config['base_url']` to the base web URL of your installation
        1. Change the `$config['encryption_key']` to something unique
        1. Nothing else will need to be changed
    1. `constants.php`
        1. `USER_AGENT` - What other servers will see when connecting to external web services
        1. `SITE_TITLE` - Title of the booking system used for mobile interface
        1. `SITE_LOGO` - Image for the header in the mobile interface
        1. `DEBUG_MODE` - Used for troubleshooting (adding the GET request variable `debug` will show debugging information). Recommended to leave set to `FALSE`
        1. `SITE_ADMIN` - Contact email
        1. `USE_EXTERNAL_HOURS` - Used to pull building hours from external system. Recommended to be set to `FALSE`
        1. `EXTERNAL_HOURS_URL` - URL to use if `USE_EXTERNAL_HOURS` is set the `TRUE`
        1. `USE_LIBSTAFF_LIST` - Used to pull in an external list of Library staff. Recommended to be set to `FALSE`
        1. `LIBSTAFF_URL` - URL used when `USE_LIBSTAFF_LIST` is set to `TRUE`
        1. `EMAIL_SUFFIX` - When sending emails to users, append this email suffix to generate their email address
        1. `USE_ACCESS_CENTRE_LIST` - Used to pull a user attribuite from an external system. Recommended to be set to `FALSE`
        1. `RMS_USERNAME` - Username used to authenticate against webservice when `USE_ACCESS_CENTRE_LIST` is set to `TRUE`
        1. `RMS_PASSWORD` - Password used to authenticate against webservice when `USE_ACCESS_CENTRE_LIST` is set to `TRUE`
        1. `RMS_SERVICE` - URL used to access webservice webservice when `USE_ACCESS_CENTRE_LIST` is set to `TRUE`
        1. `MODERATION_TIME_DELAY` - Force users to book at least this far in the future for moderated rooms (seconds). 
        1. `TIME_DELAY` - Force users to book at least this far in the future for non-moderated rooms (seconds)
        1. `SEND_MODERATION_ACTION_EMAIL` - `TRUE` if a user will recieve an email notification when their moderated booking has been approved/denied. `FALSE` for no notification
        1. `SEND_MODERATION_REQUEST_CONFIRMATION_EMAIL` - `TRUE` if a user will recieve an email letting them know their booking is awaiting moderation. `FALSE` otherwise
