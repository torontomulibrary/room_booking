<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('USER_AGENT',							'LibraryBooking/1.0');

define('DEBUG_MODE',							TRUE);
define('SITE_ADMIN',							'steven.marsden@ryerson.ca');
define('HOURS_URL',								'https://library.cf.ryerson.ca/webapps/hours/view/hours_external.cfm');
define('LIBSTAFF_URL',							'https://library.cf.ryerson.ca/utils/staff_list.cfm?access_code=rhu$PT967Jb');
define('EMAIL_SUFFIX',							'@spi.ryerson.ca');

define('RMS_USERNAME',							'libsysadmin');
define('RMS_PASSWORD',							'&7V!4nBq');
define('RMS_SERVICE',							'https://rms-wsdata.spi.ryerson.ca/owner/resources/{username}/labs?room=accesscentre');

/* End of file constants.php */
/* Location: ./application/config/constants.php */