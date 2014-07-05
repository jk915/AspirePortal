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

// Define website name
define("WEBSITE_NAME", "ASPIRE Advisor Network");

// Define the default page to load on the site.
define("DEFAULT_PAGE", "home");
define("SIDEBAR_TEMPLATE_PAGEID", 23);
define("PRIVACY_PAGEID", 12);

// Set the absolute path to the installation.
define("ABSOLUTE_PATH", FCPATH);

// Define the number of items to show on listing pages.
define('PARTNERS_PER_PAGE', 20);
define('ITEMS_PER_PAGE', 50);
define('ARTICLES_PER_PAGE', 10);
define('NEWS_PER_PAGE', 10);
define('PROPERTY_DESIGNS_PER_PAGE', 15);
define('DEVELOPMENTS_PER_PAGE', 10);
define('RESOURCES_PER_PAGE', 3);
define('MEDIA_PER_PAGE', 20);
define('STOCKLIST_PER_PAGE', 21);
define('TASKS_PER_PAGE', 20);
define('SUMMARIES_PER_PAGE', 20);

/*Porperty Types*/
define("HOUSE_TYPE",1);
define("HOUSE_AND_LAND_TYPE",2);
define("APARTMENT_TYPE",3);
define("TOWNHOUSE_TYPE",4);
define("UNIT_TYPE",5);
define("DUPLEX_TYPE",6);
define("FONZIE_TYPE",7);
define("STUDIO_TYPE",8);

//Define Property
define("MAX_PROPERTY_IMAGES",9);
define('PROPERTY_LISTING_PAGE',10);
define("PROPERTY_FILES_FOLDER","property/");
define("QUOTE_FILES_FOLDER","quote/");
define("THUMB_PROPERTY_WIDTH","265");
define("THUMB_PROPERTY_HEIGHT","190");
define("STANDARD_PROPERTY_WIDTH","465");
define("STANDARD_PROPERTY_HEIGHT","390");
define("ZOOM_PROPERTY_WIDTH","865");
define("ZOOM_PROPERTY_HEIGHT","790");

//Define User
define("USER_FILES_FOLDER","user_files/");
define("THUMB_USER_LOGO_WIDTH","265");
define("THUMB_USER_LOGO_HEIGHT","160");

//Define Project
define("PROJECT_FILES_FOLDER","project_files/");
define("THUMB_PROJECT_WIDTH","265");
define("THUMB_PROJECT_HEIGHT","160");

//Define Area
define("AREA_FILES_FOLDER","area_files/");
define("THUMB_AREA_WIDTH","265");
define("THUMB_AREA_HEIGHT","190");

//Define State
define("STATE_FILES_FOLDER","state_files/");
define("THUMB_STATE_WIDTH","265");
define("THUMB_STATE_HEIGHT","190");

//Define Region
define("REGION_FILES_FOLDER","region_files/");
define("THUMB_REGION_WIDTH","265");
define("THUMB_REGION_HEIGHT","190");

//Define Australia
define("AUSTRALIA_FILES_FOLDER","australia_files/");
define("THUMB_AUSTRALIA_WIDTH","265");
define("THUMB_AUSTRALIA_HEIGHT","190");

//Define Builder
define("BUILDER_FILES_FOLDER","builder_files/");
define("THUMB_BUILDER_WIDTH","265");
define("THUMB_BUILDER_HEIGHT","190");

//Define Stage
define("STAGE_FILES_FOLDER","stage_files/");
define("THUMB_STAGE_WIDTH","265");
define("THUMB_STAGE_HEIGHT","190");

define("THUMB_DOCUMENT","_thumb.jpg");
define("THUMB_DOCUMENT_WIDTH","170");
define("THUMB_DOCUMENT_HEIGHT","100");

define("THUMB_PREFIX","thumb_");
define("THUMB_MEDIUM_PREFIX","medium_");
define("THUMB_SMALL_PREFIX","small_");
define("THUMB_IMAGE_PREFIX","_thumb");
define("THUMB_MEDIUM_WIDTH","520");
define("THUMB_MEDIUM_HEIGHT","300");
define("THUMB_SMALL_WIDTH","268");
define("THUMB_SMALL_HEIGHT","205");

// Define where article and product uploads will be stored.
define("ARTICLE_FILES_FOLDER","article_files/");
define("CONTRACT_FILES_FOLDER","contract_files/");
define("INCLUSION_FILES_FOLDER","inclusion_files/");
define("PLAN_FILES_FOLDER","plan_files/");
define("PRODUCT_FILES_FOLDER","product_files/");

// Define path to imagemagick
define("MAGICKPATH", "/usr/bin/");
define("COPYPATH", "/bin/cp");

define("USER_TYPE_ADMIN", 						1);
define("USER_TYPE_STAFF", 						2);
define("USER_TYPE_ADVISOR", 3);
define("USER_TYPE_SUPPLIER", 4);
define("USER_TYPE_PARTNER", 5);
define("USER_TYPE_INVESTOR", 6);
define("USER_TYPE_LEAD", 7);
//define("USER_TYPE_EDITOR", 						3);

// Define which controllers editors have access to.
define("EDITOR_CONTROLLERS", "admin_articlemanager,admin_blockmanager,admin_pagemanager,admin_filemanager");

// Define admin contact email address
define("CONTACT_EMAIL", "andy@simb.com.au");

// broadcast
define( 'BROADCAST_STATUS_SENT_ID',				2 );

define("MENU_MAIN", 1);
define("MENU_LOGGEDIN", 2);
define("MENU_FOOTER", 3);

define("BLOCK_COPYRIGHT", 1);
define("BLOCK_SUBSCRIBE", 8);

define("CATEGORY_NEWS", 2);
define("CATEGORY_RESOURCES", 3);
define("CATEGORY_SERVICES", 4);
define("CATEGORY_MEDIA", 8);
define("CATEGORY_ASSETS", 41);
define("CATEGORY_DISCLAIMERS", 42);

define("CATEGORY_IMPORTANT_INFO", 25);
define("RECREATE_THUMBS", true);


// Define EWAY script constants
define('REAL_TIME', 'REAL-TIME');
define('REAL_TIME_CVN', 'REAL-TIME-CVN');
define('GEO_IP_ANTI_FRAUD', 'GEO-IP-ANTI-FRAUD');

// MailChimp
define("MAILCHIMP_APIKEY", "cb38931ab53db27a6936f31921d22b4c-us6");
define("MAILCHIMP_LISTID", "2d416e9bc9");

// Define default values for EWAY
define('EWAY_DEFAULT_CUSTOMER_ID','87654321'); //test customer ID, please set on Global Manager this field
define('EWAY_DEFAULT_PAYMENT_METHOD', REAL_TIME); // possible values are: REAL_TIME, REAL_TIME_CVN, GEO_IP_ANTI_FRAUD
define('EWAY_DEFAULT_LIVE_GATEWAY', false); //<false> sets to testing mode, <true> to live mode

// Define URLs for payment gateway
define('EWAY_PAYMENT_LIVE_REAL_TIME', 'https://www.eway.com.au/gateway/xmlpayment.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/xmltest/testpage.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp');
define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD', 'https://www.eway.com.au/gateway_beagle/xmlbeagle.asp');
define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD_TESTING_MODE', 'https://www.eway.com.au/gateway_beagle/test/xmlbeagle_test.asp'); //in testing mode process with REAL-TIME
define('EWAY_PAYMENT_HOSTED_REAL_TIME', 'https://www.eway.com.au/gateway/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/payment.asp');

// Define GST/Tax amount
define('GST', 0.1);

define("GOOGLE_APIKEY", "AIzaSyBM73ZEIu1FRSh-DCTfNorwwIR4SMi_hG0");
define('RECAPTCHA_PUBLIC_KEY','6LeG9swSAAAAAOC2RCEDU6WlLWvmY0IZdxJNycq6');
define('RECAPTCHA_PRIVATE_KEY','6LeG9swSAAAAAD_-Lm6DPu7MiNRpDkyO8VKWPpGO');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
