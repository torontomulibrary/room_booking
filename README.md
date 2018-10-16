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
		1. `DEFAULT_TEMPLATE` - Template to be used if user has no roles (and hence, not permitted to use the system)
		1. `DEFAULT_POLICY_URL` - Policy url to be used if user has no roles (and hence, not permitted to use the system)
        1. `DEBUG_MODE` - Used for troubleshooting (adding the GET request variable `debug` will show debugging information). Recommended to leave set to `FALSE`
		1. `REPLY_EMAIL` - Used as the FROM address when the system sends out emails
		1. `CONTACT_EMAIL` - Email to be used on mobile template
		1. `CONTACT_PHONE` - Telephone number to be used on mobile template		
        1. `SITE_ADMIN` - Email contact of site administrator
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
1. Once configured, the you should be able to open the booking system in your brower. Open the page `<installed path>/admin` to begin configuration of the rooms
1. Select the "Manage Role Types" option to configure roles in the system. Roles are used to group users, and assign them various properties such as maximum hours per week
      1. For the "Login Attributes" field, this is compared against the "activeclasses" attribute provided by CAS via SAML. If an attribute matches, the authenticated user will be assigned that role for the duration of their session.
1. Select the "Manage Buildings" option to create buildings
      1. Buildings are used as a containers for rooms. They get assigned opeing and closing times, which limits the availble booking times for all of its rooms. Users will also be able to filter by buildings
      1. The building hours can be set using the "Manage building hours" option (assuming that `EXTERNAL_HOURS_ADMIN` is set to `FALSE`. This option will be hidden if set to `TRUE`
1. Create room resources using the "Manage Room Resources" option. Room resources are properties that can be assigned to rooms. For example, you might want to create "LCD Screen" or "Whiteboard" as resources available in a room. If desired, these resources can also be filtered on by a person making a booking
1. Create rooms using the "Manage Rooms" option. 
      1. Be sure to select a role under "Bookable By" so that the desired users are able to see the room and make bookings
      1. For "User limit per day", this restricts the room based on the total number of hours a user has already made that day. For example, if a user has already has 3 hours of bookings on a given day, and this setting is limited to 2 hours, then the user will not be able to book this room as they would have exceeded their daily limit
