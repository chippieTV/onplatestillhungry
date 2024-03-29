<?php

if ( ! defined('EXT')){
exit('Invalid file request');
}

/**
* THIS FILE WILL NEED PERMISSIONS SET TO 400 OR SIMILAR SO EE DOESN'T OVERWRITE IT.
*/

/*
|--------------------------------------------------------------------------
| ExpressionEngine Config Items
|--------------------------------------------------------------------------
|
| The following items are for use with ExpressionEngine.  The rest of
| the config items are for use with CodeIgniter.
|
*/

/* Environmental Variables */

switch ( $_SERVER['SERVER_ADDR'] ) {
    
    // local
    case '127.0.0.1' :
	$db['expressionengine']['hostname'] = "localhost";
	$db['expressionengine']['username'] = "root";
	$db['expressionengine']['password'] = "DjaHhPQm";
	$db['expressionengine']['database'] = "onplatestillhungry";
    break;
    
     /*
// local
    case '127.0.0.1' :
	$db['expressionengine']['hostname'] = "localhost";
	$db['expressionengine']['username'] = "root";
	$db['expressionengine']['password'] = "root";
	$db['expressionengine']['database'] = "onplatestillhungry";
    break;
*/

    // staging
    case '64.207.146.24' :
	$db['expressionengine']['hostname'] = "localhost";
	$db['expressionengine']['username'] = "admin";
	$db['expressionengine']['password'] = "DjaHhPQm";
	$db['expressionengine']['database'] = "onplatestillhungry";
    break;
      
}

/* Universal Variables */

$config['app_version'] = "231";
$config['license_number'] = "6583-4300-4415-6333";
$config['debug'] = "0";
$config['install_lock'] = "";
$config['system_folder'] = "cms";
$config['doc_url'] = "http://expressionengine.com/user_guide/";
$config['is_system_on'] = "y";
$config['cookie_prefix'] = "";
$config['site_name'] = "Protein Network";
$config['allow_extensions'] = "y";

/* General
-------------------------------------------------------------------*/
$config['site_index'] = "";
$config['site_url'] = "http://".$_SERVER['HTTP_HOST']."/";
$config['server_path'] = $_SERVER['DOCUMENT_ROOT'];
$config['cp_url'] = $config['site_url']."".$config['system_folder'];

/* Universal database connection settings
-------------------------------------------------------------------*/
$active_group = 'expressionengine';
$active_record = TRUE;
$db['expressionengine']['dbdriver'] = "mysql";
$db['expressionengine']['dbprefix'] = "exp_";
$db['expressionengine']['pconnect'] = FALSE;
$db['expressionengine']['swap_pre'] = "exp_";
$db['expressionengine']['db_debug'] = FALSE;
$db['expressionengine']['cache_on'] = FALSE;
$db['expressionengine']['autoinit'] = FALSE;
$db['expressionengine']['char_set'] = "utf8";
$db['expressionengine']['dbcollat'] = "utf8_general_ci";
$db['expressionengine']['cachedir'] = $config['server_path'].$config['system_folder']."/expressionengine/cache/db_cache/";

/* Member directory paths and urls
-------------------------------------------------------------------*/
$config['avatar_url'] = $config['site_url']."/images/avatars/";
$config['avatar_path'] = $config['server_path']."/images/avatars/";
$config['photo_url'] = $config['site_url']."/images/member_photos/";
$config['photo_path'] = $config['server_path']."/images/member_photos/";
$config['sig_img_url'] = $config['site_url']."/images/signature_attachments/";
$config['sig_img_path'] = $config['server_path']."/images/signature_attachments/";
$config['prv_msg_upload_path'] = $config['server_path']."/images/pm_attachments/";

/* Misc directory paths and urls
-------------------------------------------------------------------*/
$config['theme_folder_url'] = $config['site_url']."/themes/";
$config['theme_folder_path'] = $config['server_path']."/themes/";

/* Templates Preferences
-------------------------------------------------------------------*/
$config['save_tmpl_files'] = "y";
$config['tmpl_file_basepath'] = $config['server_path']."/cms/expressionengine/templates/";
$config['site_404'] = "404/index";
$config['strict_urls'] = "n";
$config['autosave_interval_seconds'] = '300';

// END EE config items

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
*/
$config['base_url']	= $config['site_url']."";

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = "";

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of "AUTO" works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'AUTO'			Default - auto detects
| 'PATH_INFO'		Uses the PATH_INFO
| 'QUERY_STRING'	Uses the QUERY_STRING
| 'REQUEST_URI'		Uses the REQUEST_URI
| 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
|
*/
$config['uri_protocol']	= "AUTO";

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/urls.html
*/

$config['url_suffix'] = "";

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= "english";

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
$config['charset'] = "UTF-8";

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the "hooks" feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = TRUE;


/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/core_classes.html
| http://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = "EE_";


/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify which characters are permitted within your URLs.
| When someone tries to submit a URL with disallowed characters they will
| get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = "a-z 0-9~%.:_\-";


/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The two other items let you set the query string "words" that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['enable_query_strings'] = FALSE;
$config['directory_trigger'] = "D";
$config['controller_trigger'] = "C";
$config['function_trigger'] = "M";

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to 
| determine what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 0;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/logs/ folder.  Use a full server path with trailing slash.
|
*/
$config['log_path'] = "";

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = "Y-m-d H:i:s";

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/cache/ folder.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = "";

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class or the Sessions class with encryption
| enabled you MUST set an encryption key.  See the user guide for info.
|
*/
$config['encryption_key'] = "513jgTnTf3*,_v(M1>CZ5T=p%;nR|lx|M\“vLKh^u$*S46\bVh.ueWEcC`5of(:";

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
*/
$config['global_xss_filtering'] = FALSE;


/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
|
| Determines whether Cross Site Request Forgery protection is enabled.
| For more info visit the security library page of the user guide
|
*/
$config['csrf_protection'] = FALSE;

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not "echo" any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are "local" or "gmt".  This pref tells the system whether to use
| your server's local time as the master "now" reference, or convert it to
| GMT.  See the "date helper" page of the user guide for information
| regarding date handling.
|
*/
$config['time_reference'] = "local";


/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
*/
$config['rewrite_short_tags'] = TRUE;


/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which CodeIgniter should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = "";


/* End of file config.php */
/* Location: ./system/expressionengine/config/config.php */


/*
|--------------------------------------------------------------------------
| TCPDF Library
|--------------------------------------------------------------------------
|
| Used to create dynamic PDFs
|
*/
$config['eei_tcpdf_lib_path'] = $config['server_path']."/includes/tcpdf"; // NO TRAILING SLASH

/*
|--------------------------------------------------------------------------
| CE Image Basic Config Items
|--------------------------------------------------------------------------
|
| The following items are for use with CE Image. They are all optional,
| as the defaults in the actual plugin will be used if not specified below.
*/
/*
| The *relative path* (to your web root) of the directory to cache images in.
| This path will override the $cache_dir variable in the plugin file,
| and can optionally be overridden via the cache_dir= plugin parameter.
*/
$config['ce_image_cache_dir'] = '/images/made/';
/*
| The ce_image_memory_limit sets the amount of memory (in megabytes) PHP can
| use for the script (64 is generally sufficient).
*/
$config['ce_image_memory_limit'] = 64;
/*
| If the plugin cannot determine the last change date of a remote image,
| wait this long (in minutes) before re-downloading the image:
*/
$config['ce_image_remote_cache_time'] = 1440;
/*
| The default quality to save a jpg/jpeg file. The quality can range from
| 0 (lowest) to 100 (highest) and should be a whole number.
*/
$config['ce_image_quality'] = 90;
// END CE Image basic config items

/*
| By default, CE Image uses the EE instalation's root folder as the base
| path for CE Image. All relative paths and references will build off of
| this path's value. This setting allows you to override the default, and
| can also be overridden in the global_array in your index.php file.
*/
#$config['ce_image_document_root'] = '/var/www/vhosts/prote.in/local.onplatestillhungry.com/uploads/images/';



?>