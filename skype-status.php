<?php
/*
Plugin Name: Skype Online Status
Plugin URI: http://4visions.nl/en/index.php?section=55
Description: Checks your Skype Online Status and allows you to add multiple, highly customizable and accessible Skype buttons to your blog. Based on the plugin Skype Button 2.01 by Anti Veeranna. Documentation and configuration options on the <a href="./options-general.php?page=skype-status.php">Skype Online Status Settings</a> page.  
Version: 2.6.1.0
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
		- When updating from version 1.7 or previous: please deactivate plugin and remove the old directories and files before uploading! 
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

// load other plugin files
require_once(ABSPATH . 'wp-content/plugins/skype-online-status/skype-admin.php');
require_once(ABSPATH . 'wp-content/plugins/skype-online-status/skype-widget.php');

// --- settings ---

// Plugin version number. Don't edit unless you are sure.
define('SOSVERSION', '2.6.1.0');
define('SOSVERSION_DATE', '2008-06-04');

// The values below are the default settings
// Edit these if you like but they can all be customized on the Options > Skype Status page :)
function skype_default_values() {
	$value = array(
		"skype_id" => "echo123", 			// Skype ID to replace {skypeid} in template files
		"user_name" => "Skype Test Call", 		// User name to replace {username} in template files
		"button_theme" => "transparent_dropdown", 	// Theme to be used, value must match a filename (without extention) from the /plugins/skype-online-status/templates/ directory or leave blank
		"button_template" => "", 			// Will hold template loaded from user-selected template file
		"add_text" => "Add me to Skype", 		// Text to replace {add} in template files
		"call_text" => "Call me", 			// Text to replace {call} in template files
		"chat_text" => "Chat with me", 			// Text to replace {chat} in template files
		"sendfile_text" => "Send me a file", 		// Text to replace {sendfile} in template files
		"my_status_text" => "My status is ", 		// Text to replace {statustxt} in template files
		"userinfo_text" => "View my profile",		// Text to replace {userinfo} in template files
		"voicemail_text" => "Leave me voicemail",	// Text to replace {voicemail} in template files
		"use_voicemail" => "", 				// Wether to use the voicemail invitation ("on") or not (""), set to "on" if you have a SkypeIn account
		"use_function" => "on", 			// Wether to replace the tags {add/call/chat/userinfo/voicemail/sendfile} ("on") or not ("")
		"use_status" => "en", 				// Wether to replace the tag {status} with your custom texts ("custom") or Skype default according to language (e.g. "en" for english) or nothing ("", use this when allow_url_fopen is not enabled on your server!)
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
		"status_6_text" => "Invisible", 		// Text to replace {status} in template files when status is invisible (6)
		"status_7_text" => "Skype me!", 		// Text to replace {status} in template files when status is skype me! (7)
		"use_getskype" => "on", 			// Wether to show the Download Skype now! link
		"getskype_newline" => "",			// Put the Download Skype now! link on a new line ("on") or not ("")
		"getskype_text" => "&raquo; Get Skype now!", 	// Text to use for the Download Skype now! link
		"getskype_link" => "",				// What link to use for download: the default will generate some revenue for me (thanks! :-) ), "skype_mainpage" for skype main page, "skype_downloadpage" for skype download page
		"getskype_custom_link" => "",			// put your own customized link here
		"skype_status_version" => SOSVERSION,
		"upgraded" => FALSE,
	);	
	return $value;
}

// --- advanced settings ---

// Print all Skype settings from the database at the bottom of the settings page for debugging (normally, leave to FALSE)
define('SOSDATADUMP', FALSE);

// Checks wether fopen_wrappers are enabled on your server so the remote Skype status file can be read
// Comment-out (with //) the if..else statements if you want to force this setting in spite of server settings
if (ini_get('allow_url_fopen'))
	define('SOSALLOWURLFOPEN', TRUE);
else
	define('SOSALLOWURLFOPEN', FALSE);

//todo: internationalization
//load_plugin_textdomain('skype_status'); // NLS

// load database options
$skype_status_config = get_option('skype_status');

// activate wisywig button
if ($skype_status_config['use_buttonsnap']=="on") {
	require_once(ABSPATH . 'wp-content/plugins/skype-online-status/editor.php');
	add_action('init', 'skype_button_init');
	if ( $wp_db_version < 6846 ) // next action only when before wp2.5
		add_action('marker_css', 'skype_button_css');
}

// -- initialization functions --
function skype_status_install() {
	$value = skype_default_values();
	add_option("skype_status", $value, "Skype Online Status and Skype Button settings");
}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'skype_status_install');
}

// check if database update after plugin version upgrade is needed
if ($skype_status_config['skype_status_version'] !== SOSVERSION) {
	// merge new default into old settings
	$skype_default_values = skype_default_values();
	$skype_status_config = array_merge ($skype_default_values, $skype_status_config);
	// update: populate db with missing values and set upgraded flag to true
	$skype_status_config['skype_status_version'] = SOSVERSION;
	$skype_status_config['upgraded'] = TRUE;
	update_option("skype_status",$skype_status_config);
}

// admin hook
function skype_status_add_option() {
	if (function_exists('add_options_page')) {
		add_options_page('Skype Online Status','Skype Status',2,basename(__FILE__),'skype_status_options');
	}
}

// online status checker function
// needs allow_url_fopen to be enabled on your server (if not, see default settings)
function skype_status_check($skypeid, $format=".txt") {
	$str = "error";
	if (SOSALLOWURLFOPEN && $skypeid) { 
		if (function_exists('file_get_contents')) 
			$tmp = file_get_contents('http://mystatus.skype.com/'.$skypeid.$format);
		else $tmp = implode('', file('http://mystatus.skype.com/'.$skypeid.$format));
		if ($tmp!="") $str = str_replace("\n", "", $tmp);
	}
	return $str;
}

// helper functions to make sure that only valid data gets into database
function skype_status_valid_id($id) {
	return preg_match("/^(\w|\.)*$/",$id);
}

function skype_status_valid_theme($theme) {
	return !preg_match("/\W/",$theme);
}

function skype_parse_theme($config) {
	// get online status to replace {status} tag
	if ($config['use_status']=="custom") {
		$num = skype_status_check($config['skype_id'], ".num");
		$status = $config['status_'.$num.'_text'];
	} else if ($config['use_status']=="") {
		$status = "";
		$config['my_status_text'] = "";
		$config['seperator2_text'] = "";
	} else {
		$status = skype_status_check($config['skype_id'], ".txt.".$config['use_status']);
	}

	// build arrays with tags and replacement values
	$tags = array(
			"{skypeid}",
			"{status}",
			"{statustxt}",
			"{username}",
			"{sep1}",
			"{sep2}",
			"{add}",
			"{call}",
			"{chat}",
			"{sendfile}",
			"{userinfo}",
			"{voicemail}"
		);
	if ($config['use_function']=="on") {
		$values = array(
			$config['skype_id'],
			$status,
			$config['my_status_text'],
			$config['user_name'],
			$config['seperator1_text'],
			$config['seperator2_text'],
			$config['add_text'],
			$config['call_text'],
			$config['chat_text'],
			$config['sendfile_text'],
			$config['userinfo_text'],
			$config['voicemail_text']
			);
	} else {
		$values = array(
			$config['skype_id'],
			$status,
			$config['my_status_text'],
			$config['user_name'],
			$config['seperator1_text'],
			$config['seperator2_text'],
			"","","","","","");
	}

	// delete voicemail lines if not needed else append arrays with tags and replacement values
	if ($config['use_voicemail']!="on") {
		$config['button_template'] = preg_replace("|<!-- voicemail_start (.*) voicemail_end -->|","",$config['button_template']);
	} else {
		$tags[] = "<!-- voicemail_start -->";
		$tags[] = "<!-- voicemail_end -->";
		$values[] = "";
		$values[] = "";
	}

	// after that, delete from first line <!-- (.*) -->
	$theme_output = preg_replace("|<!-- (.*) - http://www.skype.com/go/skypebuttons -->|","",$config['button_template']);

	// replace all tags with values
	$theme_output = str_replace($tags,$values,$theme_output);

	if ($config['use_getskype'] == "on") { 
		if ($config['getskype_newline'] == "on") 
			$theme_output .= "<br />";

		if ($config['getskype_link'] == "skype_mainpage")
			$theme_output .= " <a href=\"http://www.skype.com\" title= \"".$config['getskype_text']."\">".$config['getskype_text']."</a>";
		elseif ($config['getskype_link'] == "skype_downloadpage")
			$theme_output .= " <a href=\"http://www.skype.com/go/downloading\" title= \"".$config['getskype_text']."\">".$config['getskype_text']."</a>";
		elseif ($config['getskype_link'] == "custom_link" && $config['getskype_custom_link'] != "" )
			$theme_output .= $config['getskype_custom_link'];
		else
			$theme_output .= " <a href=\"http://www.jdoqocy.com/click-3049686-10386659\" title=\"".$config['getskype_text']."\">".$config['getskype_text']."</a><img src=\"http://www.ftjcfx.com/image-3049686-10386659\" alt=\"\" style=\"width:0;height0;border:0\" />";
		}

	return str_replace(array("\r\n", "\n\r", "\n", "\r", "%0D%0A", "%0A%0D", "%0D", "%0A"), "", $theme_output);
}

function skype_get_template_file($filename) { // check template file existence and return content
	$buttondir = dirname(__FILE__)."/templates/";
	if ($filename != "" && file_exists($buttondir.$filename.".html")) 
		return file_get_contents($buttondir.$filename.".html");
	else 
		return "";
}

// template tag hook
function get_skype_status($args = '') {
	parse_str($args, $r);
	echo skype_status($r['skype_id'], $r['user_name'], $r['button_theme'], $r['use_voicemail']);
}

// main function
function skype_status($skype_id = FALSE, $user_name = FALSE, $button_theme = FALSE, $use_voicemail = FALSE, $button_template = FALSE) {
	global $skype_status_config;
	$r = $skype_status_config;

	// check and override predefined config with args
	if ($skype_id) $r['skype_id'] = $skype_id;
	if ($user_name) $r['user_name'] = $user_name;
	if ($use_voicemail) $r['use_voicemail'] = $use_voicemail;
	if ($button_template) $r['button_template'] = $button_template;

	// if alternate theme is set, get it from template file and override
	if ($button_theme) 
		$r['button_template'] = skype_get_template_file($button_theme);

	// make sure there is a template from database or file else revert to basic plain-text fallback template
	if ($r['button_template'] == "") 
		$r['button_template'] = '<a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{call}{sep1}{username}{sep2}{status}">{username}{sep2}{status}</a>';		
	
	return skype_parse_theme($r); 
}

// script in header
function skype_status_script() {
	print '
	<!-- Skype script used for Skype Online Status plugin by RavanH - http://4visions.nl/ -->
	<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
	<!-- end Skype script -->
	';
}

// wrapper function which calls the Skype Status button
function skype_status_callback($content) {
	if(strpos($content,'-skype status-')) {
		$content = preg_replace('/(<p.*>)?(<!-|\[)?-skype status-(->|\])?(<\/p>)?/', skype_status(), $content);
	}
	return $content;
}

// create WP hooks
add_action('wp_head', 'skype_status_script');
add_action('admin_menu', 'skype_status_add_option');
add_filter('the_content', 'skype_status_callback', 10);
add_action('init', 'skype_add_widget');

?>
