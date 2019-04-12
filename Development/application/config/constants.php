<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If setss to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


// Used for phpass_helper
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', FALSE);
// Admin url
define('ADMIN_URL', 'admin');
// CRM server update url
define('UPDATE_URL','https://www.perfexcrm.com/perfex_updates/index.php');
// Get latest version info
define('UPDATE_INFO_URL','https://www.perfexcrm.com/perfex_updates/update_info.php');

// Defined folders
// CRM temporary path
define('TEMP_FOLDER',FCPATH .'temp' . '/');
// Database backups folder
define('BACKUPS_FOLDER',FCPATH.'backups'.'/');
// Customer attachments folder from profile
define('CLIENT_ATTACHMENTS_FOLDER',FCPATH.'uploads/clients'.'/');
// All tickets attachments
define('TICKET_ATTACHMENTS_FOLDER',FCPATH .'uploads/ticket_attachments' . '/');
// Company attachemnts, favicon,logo etc..
define('COMPANY_FILES_FOLDER',FCPATH .'uploads/company' . '/');
// Staff profile images
define('STAFF_PROFILE_IMAGES_FOLDER',FCPATH .'uploads/staff_profile_images' . '/');
// Contact profile images
define('CONTACT_PROFILE_IMAGES_FOLDER',FCPATH .'uploads/client_profile_images' . '/');
// Contact profile images
define('ADDRESSBOOK_PROFILE_IMAGES_FOLDER',FCPATH .'uploads/addressbook_profile_images' . '/');
// Newsfeed attachments
define('NEWSFEED_FOLDER',FCPATH . 'uploads/newsfeed' . '/');
// Contracts attachments
define('CONTRACTS_UPLOADS_FOLDER',FCPATH . 'uploads/contracts' . '/');
// Tasks attachments
define('TASKS_ATTACHMENTS_FOLDER',FCPATH . 'uploads/tasks' . '/');
// Invoice attachments
define('INVOICE_ATTACHMENTS_FOLDER',FCPATH . 'uploads/invoices' . '/');
// Estimate attachments
define('ESTIMATE_ATTACHMENTS_FOLDER',FCPATH . 'uploads/estimates' . '/');
// Proposal attachments
define('PROPOSAL_ATTACHMENTS_FOLDER',FCPATH . 'uploads/proposals' . '/');
// Expenses receipts
define('EXPENSE_ATTACHMENTS_FOLDER',FCPATH . 'uploads/expenses' . '/');
// Lead attachments
define('LEAD_ATTACHMENTS_FOLDER',FCPATH . 'uploads/leads' . '/');
// Project files attachments
define('PROJECT_ATTACHMENTS_FOLDER',FCPATH . 'uploads/projects' . '/');
// Project discussions attachments
define('PROJECT_DISCUSSION_ATTACHMENT_FOLDER',FCPATH . 'uploads/discussions' . '/');
// Lead profile images
define('LEAD_PROFILE_IMAGES_FOLDER',FCPATH .'uploads/lead_profile_images' . '/');
/**
* Added By : Vaidehi
* Dt : 10/13/2017
* for brand images
*/
define('BRAND_IMAGES_FOLDER',FCPATH .'uploads/brands' . '/');
/**
* Added By : Purvi
* Dt : 10/14/2017
* for file images
*/
define('FILE_ATTACHMENTS_FOLDER',FCPATH . 'media' . '/');
/**
* Added By : Purvi
* Dt : 10/14/2017
* for file images
*/
define('MESSAGE_ATTACHMENTS_FOLDER',FCPATH . 'uploads/messages' . '/');
/**
* Added By : Avni
* Dt : 11/29/2017
* for line items images
*/
define('LINE_ITEMS_IMAGES_FOLDER',FCPATH .'uploads/line_item_images' . '/');
/**
* Added By : Avni
* Dt : 12/12/2017
* for proposal templates images
*/
define('PROPOSALTEMPLATE_ATTACHMENTS_FOLDER',FCPATH .'uploads/proposals_images/banner' . '/');
/**
* Added By : Vaidehi
* Dt : 12/20/2017
* for project profile images
*/
define('PROJECT_PROFILE_IMAGES_FOLDER',FCPATH .'uploads/project_profile_images' . '/');

/**
 * Added By : Masud
 * Dt : 02/26/2019
 * for project profile images
 */
define('PROJECT_COVER_IMAGES_FOLDER',FCPATH .'uploads/project_cover_images' . '/');

/**
* Added By : Masud
* Dt : 02/05/2018
* for product services package image
*/
define('PROJECT_SERVICES_PACKAGE_IMAGES_FOLDER',FCPATH .'uploads/product_services_package_images' . '/');

/**
* Added By : Vaidehi
* Dt : 02/14/2018
* for venue logo image
*/
define('VENUE_LOGO_IMAGES_FOLDER',FCPATH .'uploads/venue_logo_images' . '/');

/**
* Added By : Vaidehi
* Dt : 02/14/2018
* for venue cover image
*/
define('VENUE_COVER_IMAGES_FOLDER',FCPATH .'uploads/venue_cover_images' . '/');

/**
 * Added By : Masud
 * Dt : 07/10/2018
 * for venue Loc image
 */
define('VENUE_LOC_IMAGES_FOLDER',FCPATH .'uploads/venue_loc_images' . '/');


/**
* Added By : Vaidehi
* Dt : 02/14/2018
* for venue and/or site location images/folders
*/
define('VENUE_IMAGES_FOLDER',FCPATH .'uploads/venues' . '/');

/**
* Added By : Masud
* Dt : 02/22/2018
* for proposals gallery images/folders
*/
define('PROPOSALTEMPLATE_GALLERY_FOLDER',FCPATH . 'uploads/proposals_images/gallery' . '/');

define('PROPOSALTEMPLATE_FILES_FOLDER',FCPATH . 'uploads/proposals_images/files' . '/');

define('PROPOSALTEMPLATE_BANNER_FOLDER',FCPATH . 'uploads/proposals_images/banner' . '/');

define('PROPOSALTEMPLATE_SIGNATURE_FOLDER',FCPATH . 'uploads/proposals_images/signature' . '/');

define('QUESTIONNAIRE_FOLDER',FCPATH . 'uploads/questionnaire/' . '/');


/**
* Added By : Masud
* Dt : 02/23/2018
* for proposals gallery images max upload size
*/

define('MB', 1048576);
define('GB', 1073741824);
define('APP_MEMORY_LIMIT', 1073741824);
