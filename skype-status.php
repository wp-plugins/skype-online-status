<?php
/*
Plugin Name: Skype Online Status
Plugin URI: http://4visions.nl/en/index.php?section=55
Description: Checks your Skype Online Status and allows you to add multiple, highly customizable and accessible Skype buttons to your blog. Based on the plugin Skype Button 2.01 by Anti Veeranna. Documentation and configuration options on the <a href="./options-general.php?page=skype-status.php">Skype Online Status Settings</a> page.  
Version: 2.6.2.0
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
	Installation
		- When updating from version 2.5 or previous: please deactivate plugin and remove the old directories and files before uploading! 
		- Upload files and folders to /plugins/ directory.
		- Activate plugin on the Plug-ins page
		- Configure or update your SkypeID and settings on the Options > Skype Status page
		- Activate the Skype widget, put <?php if (function_exists(get_skype_status)) { get_skype_status(''); } ?> in your sidebar.php or <!--skype status--> in your posts. Read more on the Quick Guide section of the Options > Skype Status page.
		
	Wish List version 3 and beyond :)
		- Skype-like wizard...
		- Add Skypecasts widget
		- Upload your own button
		- Make multiple Skype ID's with own settings possible
		- Internationalization
		- Get XML online status (and local time?)
	
	Revision History
		[2008-06-19] version 2.6.2.0: heaps more themes + added new {function} tag to My Status templates + improved widget with preview
 		[2008-06-16] version 2.6.1.2: automatic blog language detection for status text, some small bugfixes + complete removal button
		[2008-06-04] version 2.6.1.0: 
			- added simple widget
			- removed built-in update checker (redundant since WP2.5 auto-update) 
			- add your own download link
		[2007-04-09] version 2.6.0.9: 
			- improved reg_exp for quicktag replacement (defeating wpautop's wrapping p)
			- minor changes in available settings (newline for download link optional)
			- fixed &-sign in fields causing failed w3c validation
		[2007-02-18] version 2.5: made quicktag work for 2.1+ new TinyMCE button plugin routine
		[2006-11-21] version 2.4.1: added onkeydown action on admin textarea
		[2006-11-03] version 2.4: added backwards compatibility with PHP versions previous to 4.3 ( fallback to file() instead of file_get_contents() ) and a check for allow_url_fopen before remote file reading (used in status check and upgrade check) with dynamic options change
		[2006-09-25] version 2.3: added Download Skype now! link (with option to change text or disable), more template files and an upgrade function
		[2006-09-20] version 2.2.2: moved buttonsnap.php, changes to Quick Guide, template files and Live Support and bugfixes: 
			1. quicktag button not showing
			2. multiple skype buttons in 1 post not showing
		[2006-09-04] version 2.2.1: minor changes to admin page
		[2006-07-28] version 2.2.0: used global string for speed improvement
		[2006-07-05] version 2.1.0: added Skype default status texts in different languages
		[2006-07-04] version 2.0.1: minor bugfix (altered defaulting to fallback template procedure)
		[2006-06-30] version 2.0: added editable template and live support link
		[2006-06-29] version 1.9: added RTE guicktag button for <!--skype status--> hook
		[2006-06-27] version 1.8: improved performance by loading template in database
		[2006-06-23] version 1.7: added post hook <!--skype status--> and appended instructions to quickguide
		[2006-06-23] version 1.6: wrote templating guide and redesigned the Options > Skype Status page
		[2006-06-22] version 1.5: added plain text fallback template to core code
		[2006-06-22] version 1.4: added reset button and default settings
		[2006-06-21] version 1.3: added new template tags {username} {sep1} {sep2}
		[2006-06-20] version 1.2: minor bugfixes
			1. inconsistent options page form-labels 
			2. skype_status_check not defaulting to status_error_txt when mystatus.skype.com is off-line 
		[2006-05-02] version 1.1: added new text template file
		[2006-04-26] version 1.0: added instructions (quick guide)
		[2006-04-20] version 0.9: added skype user name
		[2006-04-12] version 0.8: added customizability for get_skype_status('options')
		[2006-04-10] version 0.7: redesign admin interface
		[2006-03-05] version 0.3 - 0.6: added lot's of new settings and template tags
		[2006-03-03] version 0.2: added function skype_parse_theme() and skype_status_check()
		[2006-03-03] version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)
		
*/

// Plugin version number and date
define('SOSVERSION', '2.6.2.0');
define('SOSVERSION_DATE', '2008-06-19');

////////-----------------------------------------.oO\\//Oo.-----------------------------------------\\\\\\\\
// The values below are the default settings
// Edit these if you like but they can all be customized on the Options > Skype Status page :)

$skype_default_values = array(
	"skype_id" => "echo123", 			// Skype ID to replace {skypeid} in template files
	"user_name" => "Skype Test Call", 		// User name to replace {username} in template files
	"button_theme" => "transparent_dropdown", 	// Theme to be used, value must match a filename (without extention) from the /plugins/skype-online-status/templates/ directory or leave blank
	"button_template" => "", 			// Will hold template loaded from user-selected template file
	"button_function" => "call",			// Function to replace {function} in template files
	"add_text" => "Add me to Skype", 		// Text to replace {add} in template files
	"call_text" => "Call me", 			// Text to replace {call} in template files
	"chat_text" => "Chat with me", 			// Text to replace {chat} in template files
	"sendfile_text" => "Send me a file", 		// Text to replace {sendfile} in template files
	"my_status_text" => "My status is ", 		// Text to replace {statustxt} in template files
	"userinfo_text" => "View my profile",		// Text to replace {userinfo} in template files
	"voicemail_text" => "Leave me voicemail",	// Text to replace {voicemail} in template files
	"use_voicemail" => "", 				// Wether to use the voicemail invitation ("on") or not (""), set to "on" if you have a SkypeIn account
	"use_function" => "on", 			// Wether to replace the tags {add/call/chat/userinfo/voicemail/sendfile} ("on") or not ("")
	"use_status" => "custom",			// Wether to replace the tag {status} with your custom texts ("custom") or Skype default according to language (e.g. "en" for english) or nothing ("", use this when allow_url_fopen is not enabled on your server!)
	"use_buttonsnap" => "on", 			// Wether to display a Skype Status quicktag button in RTE for posts ("on") or not ("")
	"function" => "call", 				// The function for your Skype button (i.e. what happens when clicking the button) > call, chat, add, userinfo, voicemail or sendfile
	"seperator1_text" => " - ", 			// Text to replace {sep1} in template files
	"seperator2_text" => ": ", 			// Text to replace {sep2} in template files
	"status_error_text" => "Unknown", 		// Text to replace {status} in template files when status could not be checked
	"status_0_text" => "Unknown", 			// Text to replace {status} in template files when status is unknown (0)
	"status_1_text" => "Offline", 			// Text to replace {status} in template files when status is offline (1)
	"status_2_text" => "Online", 			// Text to replace {status} in template files when status is online (2)
	"status_3_text" => "Away", 			// Text to replace {status} in template files when status is away (3)
	"status_4_text" => "Not available", 		// Text to replace {status} in template files when status is not available (4)
	"status_5_text" => "Do not disturb",		// Text to replace {status} in template files when status is do not disturb (5)
	"status_6_text" => "Offline", 		// Text to replace {status} in template files when status is invisible (6)
	"status_7_text" => "Skype me!", 		// Text to replace {status} in template files when status is skype me! (7)
	"use_getskype" => "on", 			// Wether to show the Download Skype now! link
	"getskype_newline" => "on",			// Put the Download Skype now! link on a new line ("on") or not ("")
	"getskype_text" => "&raquo; Get Skype now!", 	// Text to use for the Download Skype now! link
	"getskype_link" => "",				// What link to use for download: the default ("") will generate some revenue for me (thanks! :-) ), "skype_mainpage" for skype main page, "skype_downloadpage" for skype download page
	"getskype_custom_link" => "",			// put your own customized link here
	"skype_status_version" => SOSVERSION,
	"upgraded" => FALSE,
	"installed" => FALSE,
);

// Available status message languages as provided by Skype, e.g. http://mystatus.skype.com/yourusername.txt.pt-br will show your online status message in Brazilian portuguese.
// If there are new languages available, they can be added to this array to make them optional on the Skype Settings page.
$skype_avail_languages = array ( 
	"English" => "en",
	"French" => "fr",
	"German" => "de",
	"Japanese" => "ja",
	"Chinese" => "zh",
	"Taiwanese" => "zh-tw",
	"Portuguese" => "pt",
	"Brazilian" => "pt-br",
	"Italian" => "it",
	"Spanish" => "es",
	"Polish" => "pl",
	"Swedish" => "se",
);

$skype_avail_functions = array (
	"Call me!" => "call",
	"Add me to Skype" => "add",
	"Chat with me" => "chat",
	"View my profile" => "userinfo",
	"Leave me voicemail" => "voicemail",
	"Send me a file" => "sendfile",
);

$skype_widget_default_values = array (
	"title" => "Skype Online Status",	// Widget title
	"skype_id" => "",			// Skype ID to replace {skypeid} in template files
	"user_name" => "",			// User name to replace {username} in template files
	"button_theme" => "",			// Theme to be used, value must match a filename (without extention) from the /plugins/skype_status/templates/ directory or leave blank
	"button_template" => "",		// Template of the theme loaded
	"use_voicemail" => "",			// Wether to use the voicemail invitation ("on") or not (""), set to "on" if you have a SkypeIn account
	"before" => "",				// text that should go before the button
	"after" => "",				// text that should go after the button
);

// Print all Skype settings from the database at the bottom of the settings page for debugging (normally, leave to FALSE)
define('SOSDATADUMP', FALSE);

// Checks wether fopen_wrappers are enabled on your server so the remote Skype status file can be read
// Comment-out (with //) the if..else statements if you want to force this setting in spite of server settings
if (ini_get('allow_url_fopen'))
	define('SOSALLOWURLFOPEN', TRUE);
else
	define('SOSALLOWURLFOPEN', FALSE);

////////-----------------------------------------.oO//\\Oo.-----------------------------------------\\\\\\\\
// Stop editing here!

// load database options
$skype_status_config = get_option('skype_status');
$skype_widget_config = get_option('skype_widget_options');

//todo: internationalization
//load_plugin_textdomain('skype_status'); // NLS

// load other plugin files
require_once(ABSPATH . 'wp-content/plugins/skype-online-status/skype-admin.php');
require_once(ABSPATH . 'wp-content/plugins/skype-online-status/skype-functions.php');
require_once(ABSPATH . 'wp-content/plugins/skype-online-status/skype-widget.php');

// activate wisywig button
if ($skype_status_config['use_buttonsnap']=="on") {
	require_once(ABSPATH . 'wp-content/plugins/skype-online-status/editor.php');
	add_action('init', 'skype_button_init');
	if ( $wp_db_version < 6846 ) // next action only when before wp2.5
		add_action('marker_css', 'skype_button_css');
}

// check options or revert to default when activated
if (!is_array($skype_status_config) && isset($_GET['activate']) && $_GET['activate'] == 'true') {
	$skype_status_config = skype_default_values();
	$skype_status_config['installed'] = TRUE;
	add_option('skype_status',$skype_status_config);
	add_option('skype_widget_options',$skype_widget_default_values);
}

// create WP hooks
//add_action('init', 'skype_status_install');
add_action('wp_head', 'skype_status_script');
add_action('admin_menu', 'skype_status_add_option');
add_filter('the_content', 'skype_status_callback', 10);
add_action('init', 'skype_add_widget');

// check if database update after plugin version upgrade is needed
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
		add_options_page('Skype Online Status','Skype Status',2,basename(__FILE__),'skype_status_options');
	}
}

// initialization
function skype_status_install() {
	global $skype_status_config;
	add_option('skype_status', $skype_status_config);
}

?>
