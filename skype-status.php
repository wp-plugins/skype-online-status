<?php
/*
Plugin Name: Skype Online Status
Plugin URI: http://4visions.nl/en/wordpress-plugins/skype-online-status/
Description: Add multiple, highly customizable and accessible Skype buttons to post/page content (quick-tags), sidebar (unlimited number of widgets) or anywhere else (template code). Find documentation and advanced configuration options on the <a href="./options-general.php?page=skype-online-status">Skype Online Status Settings</a> page or just go straight to your <a href="widgets.php">Widgets</a> page and Skype away...  
Version: 2.8.4
Author: RavanH
Author URI: http://4visions.nl/
*/

/*  Copyright 2009  RavanH  (email : skype-status@4visions.nl)

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

// Plugin version number and date
define('SOSVERSION', '2.8.4');
define('SOSVERSION_DATE', '2011-02-22');

if (file_exists(dirname(__FILE__).'/skype-online-status'))
	$skype_mu_dir = "/skype-online-status";
		
// Plugin constants
define('SOSPLUGINURL', plugins_url($skype_mu_dir, __FILE__));
define('SOSPLUGINDIR', dirname(__FILE__).$skype_mu_dir);
define('SOSPLUGINFILE', 'skype-online-status'); // plugin_basename(__FILE__)

// Checks whether your server is capable and allowing the remote Skype status file to be read
if (function_exists('curl_exec') || ini_get('allow_url_fopen'))
	define('SOSREMOTE', TRUE);
else 
	define('SOSREMOTE', FALSE);

// Print all Skype settings from the database at the bottom of the settings page for debugging (normally, leave to FALSE)
define('SOSDATADUMP', FALSE);


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
	"use_status" => "custom",			// Wether to replace the tag {status} with your custom texts ("custom") or Skype default according to language (e.g. "en" for english) or nothing ("" - use this when remote file access is disabled on your server!)
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


// Available status messages as provided by Skype to replace {status} in template files
$skype_avail_statusmsg = array ( 
	"0" => __('Unknown', 'skype-online-status'), 		// when status is unknown (0)
	"1" => __('Offline', 'skype-online-status'), 		// when status is offline (1)
	"2" => __('Online', 'skype-online-status'), 		// when status is online (2)
	"3" => __('Away', 'skype-online-status'), 		// when status is away (3)
	"4" => __('Not available', 'skype-online-status'), 	// when status is not available (4)
	"5" => __('Do not disturb', 'skype-online-status'),	// when status is do not disturb (5)
	//"6" => __('Invisible', 'skype-online-status'), 	// when status is invisible (6)
	"7" => __('Skype me!', 'skype-online-status'), 		// when status is skype me! (7)
);

// Available status message languages as provided by Skype,
// e.g. http://mystatus.skype.com/yourusername.txt.pt-br will
// show your online status message in Brazilian Portuguese. If
// there are new languages available, they can be added to this
// array to make them optional on the Skype Settings page.
$skype_avail_languages = array ( 
	"en" => __('English', 'skype-online-status'),
	"fr" => __('French', 'skype-online-status'),
	"de" => __('German', 'skype-online-status'),
	"ja" => __('Japanese', 'skype-online-status'),
	"zh-tw" => __('Taiwanese', 'skype-online-status'),
	"zh" => __('Chinese', 'skype-online-status'),
	"pt-br" => __('Brazilian', 'skype-online-status'),
	"pt" => __('Portuguese', 'skype-online-status'),
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

$soswhatsnew_this = "
* Updated POT file for translators<br />
* Japanese translation.
";
$soswhatsnew_recent = "
* Live Support for contributors<br />
* Skype button shortcode in posts and pages can now handle options like skype_id to override default settings<br />
* new Windows7/IE8 compatible skypeCheck script; loads only when needed.";


////////-----------------------------------------.oO//\\Oo.-----------------------------------------\\\\\\\\
// Stop editing here!

// load functions
require_once(SOSPLUGINDIR . '/skype-functions.php');

// load database options
$skype_status_config = get_option('skype_status');

function skype_status_init() {
	global $skype_status_config;

	// if no array present, load defaults (into db OR BETTER NOT?)
	if (!is_array($skype_status_config)) {
		$skype_status_config = skype_default_values();
		$skype_status_config['installed'] = TRUE;
		//update_option('skype_status',$skype_status_config);
	}

	// do stuff for admin ONLY when on the backend
	if ( is_admin() ) {
		//load admin page function
		require_once(SOSPLUGINDIR . '/skype-admin.php');

		// Internationalization
		load_plugin_textdomain('skype-online-status', false, dirname(plugin_basename( __FILE__ )).'/languages');

		// check for plugin upgrade
		if ($skype_status_config['skype_status_version'] != "" && $skype_status_config['skype_status_version'] !== SOSVERSION) {
			// merge new default into old settings
			$skype_status_config = array_merge (skype_default_values(), $skype_status_config);
			// update: populate db with missing values and set upgraded flag to true
			$skype_status_config['skype_status_version'] = SOSVERSION;
			$skype_status_config['upgraded'] = TRUE;
			update_option('skype_status',$skype_status_config);
		}

		// Quicktag button
		if ($skype_status_config['use_buttonsnap']=="on" && current_user_can('edit_posts') && current_user_can('edit_pages')) {
			add_filter('mce_external_plugins', 'sos_mce3_plugin');
			add_filter('mce_buttons', 'sos_mce3_button', 99);
		}

		// create WP hooks
		add_action('admin_menu', 'skype_status_add_menu');
		add_filter('plugin_action_links', 'skype_status_add_action_link', 10, 2);
	}

	
	add_filter('the_content', 'skype_status_callback');
	add_shortcode('skype-status', 'skype_status_shortcode_callback');

	// http://scribu.net/wordpress/optimal-script-loading.html (the Jedi Knight way)
	add_action('wp_footer', 'skype_status_script');

	// add widget
	//add_action('widgets_init', 'skype_widget_register');

}


add_action('init', 'skype_status_init');
add_action('widgets_init', create_function('', 'return register_widget("Skype_Status_Widget");'));
