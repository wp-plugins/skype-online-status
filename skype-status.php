<?php
/*
Plugin Name: Skype Online Status
Plugin URI: http://4visions.nl/en/index.php?section=55
Description: Add multiple, highly customizable and accessible Skype buttons to post/page content (quick-tags), sidebar (unlimited number of widgets) or anywhere else (template code). Find documentation and advanced configuration options on the <a href="./options-general.php?page=skype-status.php">Skype Online Status Settings</a> page or just go straight to your <a href="widgets.php">Widgets</a> page and Skype away...  
Version: 2.6.9.0
Author: RavanH
Author URI: http://4visions.nl/
*/

/*  Copyright 2006  RavanH  (email : skype-status@4visions.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
    For Installation instructions, usage, revision history and other info: see readme.txt included in this package
*/

$sosplugindir = basename(dirname(__FILE__));

// Plugin version number and date
define('SOSVERSION', '2.6.9.0');
define('SOSVERSION_DATE', '2009-02-26');
define('SOSPLUGINURL', get_option('siteurl') . '/wp-content/plugins/'.$sosplugindir.'/');

// Internationalization
load_plugin_textdomain('skype-online-status','wp-content/plugins/'.$sosplugindir.'/languages/');

////////-----------------------------------------.oO\\//Oo.-----------------------------------------\\\\\\\\
// The values below are the default settings
// Edit these if you like but they can all be customized on the Options > Skype Status page :)

$skype_default_values = array(
	"skype_id" => "echo123", 			// Skype ID to replace {skypeid} in template files
	"user_name" => __('Skype Test Call', 'skype-online-status'), 		// User name to replace {username} in template files
	"button_theme" => "transparent_dropdown", 	// Theme to be used, value must match a filename (without extention) from the /plugins/skype-online-status/templates/ directory or leave blank
	"button_template" => "", 			// Will hold template loaded from user-selected template file
	"button_function" => "call",			// Function to replace {function} in template files
	"use_voicemail" => "", 				// Wether to use the voicemail invitation ("on") or not (""), set to "on" if you have a SkypeIn account
	"use_function" => "on", 			// Wether to replace the tags {add/call/chat/userinfo/voicemail/sendfile} ("on") or not ("")
	"use_status" => "custom",			// Wether to replace the tag {status} with your custom texts ("custom") or Skype default according to language (e.g. "en" for english) or nothing ("", use this when allow_url_fopen is not enabled on your server!)
	"use_buttonsnap" => "on", 			// Wether to display a Skype Status quicktag button in RTE for posts ("on") or not ("")
	"seperator1_text" => __(' - ', 'skype-online-status'), 			// Text to replace {sep1} in template files
	"seperator2_text" => __(': ', 'skype-online-status'), 			// Text to replace {sep2} in template files
	"use_getskype" => "on", 			// Wether to show the Download Skype now! link
	"getskype_newline" => "on",			// Put the Download Skype now! link on a new line ("on") or not ("")
	"getskype_text" => __('&raquo; Get Skype, call free!', 'skype-online-status'), 	// Text to use for the Download Skype now! link
	"getskype_link" => "",				// What link to use for download: the default ("") will generate some revenue for me (thanks! :-) ), "skype_mainpage" for skype.com main page, "skype_downloadpage" for skype.com download page
	"getskype_custom_link" => "",			// put your own customized link here
	"skype_status_version" => SOSVERSION,
	"upgraded" => FALSE,
	"installed" => FALSE,
	"my_status_text" => __('My status is', 'skype-online-status') . " ", 		// Text to replace {statustxt} in template files
	"status_error_text" => __('Unknown', 'skype-online-status'), 		// Text to replace {status} in template files when status could not be checked
);

$skype_widget_default_values = array ( 
	"title" => __('Skype Online Status', 'skype-online-status'),	// Widget title
	"skype_id" => "",			// Skype ID to replace {skypeid} in template files
	"user_name" => "",			// User name to replace {username} in template files
	"button_theme" => "",			// Theme to be used, value must match a filename (without extention) from the /plugins/skype_status/templates/ directory or leave blank
	"button_template" => "",		// Template of the theme loaded
	"use_voicemail" => "",			// Wether to use the voicemail invitation ("on") or not ("off") or leave to default ("")
	"before" => "",				// text that should go before the button
	"after" => "",				// text that should go after the button
);

// Available status messages as provided by Skype
$skype_avail_statusmsg = array ( 	"0" => __('Unknown', 'skype-online-status'), 			// Text to replace {status} in template files when status is unknown (0)
	"1" => __('Offline', 'skype-online-status'), 			// Text to replace {status} in template files when status is offline (1)
	"2" => __('Online', 'skype-online-status'), 			// Text to replace {status} in template files when status is online (2)
	"3" => __('Away', 'skype-online-status'), 			// Text to replace {status} in template files when status is away (3)
	"4" => __('Not available', 'skype-online-status'), 		// Text to replace {status} in template files when status is not available (4)
	"5" => __('Do not disturb', 'skype-online-status'),		// Text to replace {status} in template files when status is do not disturb (5)
	//"6" => __('Invisible', 'skype-online-status'), 		// Text to replace {status} in template files when status is invisible (6)
	"7" => __('Skype me!', 'skype-online-status'), 		// Text to replace {status} in template files when status is skype me! (7)
);

// Available status message languages as provided by Skype, e.g. http://mystatus.skype.com/yourusername.txt.pt-br will show your online status message in Brazilian portuguese.
// If there are new languages available, they can be added to this array to make them optional on the Skype Settings page.
$skype_avail_languages = array ( 
	"en" => __('English', 'skype-online-status'),
	"fr" => __('French', 'skype-online-status'),
	"de" => __('German', 'skype-online-status'),
	"ja" => __('Japanese', 'skype-online-status'),
	"zh" => __('Chinese', 'skype-online-status'),
	"zh-tw" => __('Taiwanese', 'skype-online-status'),
	"pt" => __('Portuguese', 'skype-online-status'),
	"pt-br" => __('Brazilian', 'skype-online-status'),
	"it" => __('Italian', 'skype-online-status'),
	"es" => __('Spanish', 'skype-online-status'),
	"pl" => __('Polish', 'skype-online-status'),
	"se" => __('Swedish', 'skype-online-status'),
);

$skype_avail_functions = array (
	"call" => __('Call me!', 'skype-online-status'),
	"add" => __('Add me to Skype', 'skype-online-status'),
	"chat" => __('Chat with me', 'skype-online-status'),
	"userinfo" => __('View my profile', 'skype-online-status'),
	"voicemail" => __('Leave me voicemail', 'skype-online-status'),
	"sendfile" => __('Send me a file', 'skype-online-status'),
);

// Print all Skype settings from the database at the bottom of the settings page for debugging (normally, leave to FALSE)
define('SOSDATADUMP', FALSE);
// and some flags for datadump purposes
define('SOSREMOVEFLAG', FALSE);
define('SOSBUTTONSNAPFLAG', FALSE);
define('SOSCURLFLAG', FALSE);

// Checks wether fopen_wrappers are enabled on your server so the remote Skype status file can be read
// If you want to force this setting in spite of server settings, comment-out (with //) all the lines in the if..else statement
// except the wanted define-value line.
if (ini_get('allow_url_fopen'))
	define('SOSALLOWURLFOPEN', TRUE);
else
	define('SOSALLOWURLFOPEN', FALSE);

// Checks wether cURL functions are available on your server so the remote Skype status file can be read using cURL.
// If you want to force this setting in spite of server settings, comment-out (with //) all the lines in the if..else statement
// except the wanted define-value line.
if (function_exists('curl_exec')) 
	define('SOSUSECURL', TRUE);
else 
	define('SOSUSECURL', FALSE);

$soswhatsnew_this = "
	Removal of good old Buttonsnap Library to avoid showstopper error in WP 2.7 + adaptation of settings page to fit the new WP 2.7 backend. Dropped support for WP versions below 2.1";
$soswhatsnew_recent = "
	2.6.4.1: Added Italian language and small language fixes<br />
	2.6.4.0: Internationalization! If your language is not available, and you would like to contribute to this plugin, your translation (.mo file) will be MUCH appreciated :)<br />
	2.6.3.1: Use cURL (if available) for remote status reading<br />
	2.6.3.0: Multiple Widgets (if upgrading from version 2.6.2.9, <strong>please VERIFY YOUR <a href=\"widgets.php\">WIDGET SETTINGS</a>!!</strong>)<br />
	+ Major admin page layout changes<br />
	2.6.1.2: Blog language detection for online status messages in English, French, German, Japanese, Chinese, Taiwanese, Portuguese, Brazilian, Italian, Spanish, Polish, Swedish";


////////-----------------------------------------.oO//\\Oo.-----------------------------------------\\\\\\\\
// Stop editing here!

// load database options
$skype_status_config = get_option('skype_status');
if (!is_array($skype_status_config))
	$skype_status_config = $skype_default_values;

// load other plugin files
require_once(dirname(__FILE__) . '/skype-functions.php');
if ( $wp_db_version >= 9872 ) 
	require_once(dirname(__FILE__) . '/skype-admin2.7.php');
else
	require_once(dirname(__FILE__) . '/skype-admin.php');
require_once(dirname(__FILE__) . '/skype-widget.php');

// activate wisywig button
if ($skype_status_config['use_buttonsnap']=="on") {
	require_once(dirname(__FILE__) . '/editor.php');
	add_action('init', 'skype_button_init');
	if ( $wp_db_version < 6846 ) // next action only when before wp2.5
		add_action('marker_css', 'skype_button_css');
}

// check options or revert to default when activated
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	skype_status_install();
}

// create WP hooks
add_action('wp_head', 'skype_status_script');
add_action('admin_menu', 'skype_status_add_option');
add_filter('the_content', 'skype_status_callback', 10);
if ( $wp_db_version < 6846 ) // next action only when before wp2.5
	add_action('init', 'skype_add_widget');
else
	add_action('widgets_init', 'skype_widget_register');

// check for plugin upgrade
if ($skype_status_config['skype_status_version'] != "" && $skype_status_config['skype_status_version'] !== SOSVERSION) {
	// merge new default into old settings
	$skype_status_config = array_merge (skype_default_values(), $skype_status_config);
	// update: populate db with missing values and set upgraded flag to true
	$skype_status_config['skype_status_version'] = SOSVERSION;
	$skype_status_config['upgraded'] = TRUE;
	update_option('skype_status',$skype_status_config);
}

// admin hook
function skype_status_add_option() {
	if (function_exists('add_options_page')) {
		add_options_page(__('Skype Online Status', 'skype-online-status'),__('Skype Status', 'skype-online-status'),2,basename(__FILE__),'skype_status_options');
	}
}

// initialization
function skype_status_install() {
	global $skype_status_config;
	if (!is_array($skype_status_config)) {
		$skype_status_config = skype_default_values();
		$skype_status_config['installed'] = TRUE;
		add_option('skype_status',$skype_status_config);
	}
}

?>
