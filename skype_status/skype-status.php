<?php
/*
Plugin Name: Skype Online Status
Plugin URI: http://4visions.nl/en/index.php?section=55
Description: Checks your Skype Online Status and allows you to add multiple, highly customizable and accessible Skype buttons to your blog. Based on the plugin Skype Button 2.01 by Anti Veeranna. Documentation and configuration options on the <a href="./options-general.php?page=skype_status.php">Skype Online Status Settings page</a>.  
Version: 1.7
Author: Ravan
Author URI: http://4visions.nl/
*/

/*  Copyright 2006  Ravan  (email : skype-status@4visions.nl)

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
		- Upload files and folders to /plugins/ directory.
		- Activate plugin on the Plug-ins page
		- Configure your SkypeID and settings on the Options > Skype Status page
		- Put <?php if (function_exists(get_skype_status)) { get_skype_status(''); } ?> in your sidebar.php
		
	Wish List version 2 and beyond :)
		- Extend parameters get_skype_status('options')
		- Online theme editor
		- Make multiple Skype ID's with own settings possible
		- Internationalization
		- Get XML online status
	
	Revision History
		[2006-06-23] version 1.7: added post hook <!--skype status--> and appended instructions to quickguide
		[2006-06-23] version 1.6: wrote templating guide and redesigned the Options > Skype Status page
		[2006-06-22] version 1.5: added plain text fallback template to core code
		[2006-06-22] version 1.4: added reset button and default settings
		[2006-06-21] version 1.3: added new template tags {username} {sep1} {sep2}
		[2006-06-20] version 1.2: minor bugfixes
			1. inconsistent options page form-labels 
			2. skype_status_check not defaulting to status_error_txt when mystatus.skype.com is off-line 
		[2006-05-02] version 1.1: added new text template file
		[2006-04-26] version 1.0: wrote instructions (quick guide)
		[2006-04-20] version 0.9: added skype user name
		[2006-04-12] version 0.8: added customizability for get_skype_status('options')
		[2006-04-10] version 0.7: redesign admin interface
		[2006-03-05] version 0.3 - 0.6: added lot's of new settings and template tags
		[2006-03-03] version 0.2: added function skype_parse_theme() and skype_status_check()
		[2006-03-03] version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)
*/

// The values below are the default settings
// Edit these if you like :)
function skype_default_values() {
	$value = array(
		"skype_id" => "", 							// Skype ID to replace {skypeid} in template files
		"user_name" => "", 							// User name to replace {username} in template files
		"button_theme" => "", 						// Theme to be used, value must match a filename (without extention) from the /plugins/skype_status/templates/ directory or leave blank
		"add_text" => "Add me to Skype", 			// Text to replace {add} in template files
		"call_text" => "Call me", 					// Text to replace {call} in template files
		"chat_text" => "Chat with me", 				// Text to replace {chat} in template files
		"sendfile_text" => "Send me a file", 		// Text to replace {sendfile} in template files
		"my_status_text" => "My status is ", 		// Text to replace {statustxt} in template files
		"userinfo_text" => "View my profile",		// Text to replace {userinfo} in template files
		"voicemail_text" => "Leave me voicemail",	// Text to replace {voicemail} in template files
		"use_voicemail" => "", 						// Wether to use the voicemail invitation ("on") or not (""), set to "on" if you have a SkypeIn account
		"use_function" => "on", 					// Wether to replace the tags {add/call/chat/userinfo/voicemail/sendfile} ("on") or not ("")
		"use_status" => "on", 						// Wether to replace the tags {add/call/chat/userinfo/voicemail/sendfile} ("on") or not ("")
		"seperator1_text" => " - ", 				// Text to replace {sep1} in template files
		"seperator2_text" => ": ", 					// Text to replace {sep2} in template files
		"status_error_text" => "Error", 			// Text to replace {status} in template files when status could not be checked
		"status_0_text" => "Unknown", 				// Text to replace {status} in template files when status is unknown (0)
		"status_1_text" => "Offline", 				// Text to replace {status} in template files when status is offline (1)
		"status_2_text" => "Online", 				// Text to replace {status} in template files when status is online (2)
		"status_3_text" => "Away", 					// Text to replace {status} in template files when status is away (3)
		"status_4_text" => "Not available", 		// Text to replace {status} in template files when status is not available (4)
		"status_5_text" => "Do not disturb", 		// Text to replace {status} in template files when status is do not disturb (5)
		"status_6_text" => "Invisible", 			// Text to replace {status} in template files when status is invisible (6)
		"status_7_text" => "Skype me!", 			// Text to replace {status} in template files when status is skype me! (7)
	);	
	return $value;
}

// -- initialization functions --
function skype_status_install() {
	$value = skype_default_values();
	add_option("skype_status", $value, "Skype Online Status and Skype Button settings");
}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'skype_status_install');
}

// admin hook
function skype_status_add_option() {
	if (function_exists('add_options_page')) {
		add_options_page('Skype Online Status', 'Skype Status',2, basename(__FILE__), 'skype_status_options');
	}
	add_action('admin_menu', 'skype_status_add_option');
}

// online status checker function
function skype_status_check($skypeid) {
	$numstr = file_get_contents('http://mystatus.skype.com/'.$skypeid.'.num');
    $numstr = str_replace("\n", "", $numstr);
	if ($numstr=="")
		$numstr = "error"; 
	return $numstr;
}

// helper functions to make sure that only valid data gets into database
function skype_status_valid_id($id) {
	return preg_match("/^(\w|\.)*$/",$id);
}

function skype_status_valid_theme($theme) {
	return !preg_match("/\W/",$theme);
}

function skype_status_get_config() {
	$opt = get_option("skype_status");
	$buttondir = dirname(__FILE__)."/templates/";
	if (!file_exists($buttondir . $opt['button_theme'] . ".html")) {
		// this is the default
		$opt['button_theme'] = "transparent_dropdown";
	}
	return $opt;
}

function skype_parse_theme($fc, $config) {
	
	// delete voicemail lines if needed
	if ($config['use_voicemail']!="on") {
		$fc = preg_replace("|<!-- voicemail_start (.*) voicemail_end -->|ms","",$fc);
	}
	
	// put skypeid in place
	$theme_output = str_replace("{skypeid}",$config['skype_id'],$fc);
	
	// replace {status} tag
	if ($config['use_status']=="on") {
		$num = skype_status_check($config['skype_id']);
		$status = $config['status_'.$num.'_text'];
	} else {
		$status = "";
	}
	$theme_output = str_replace("{status}",$status,$theme_output);	
	
	// replace function tags with values
	if ($config['use_function']=="on") {
		$theme_output = str_replace("{add}",$config['add_text'],$theme_output);	
		$theme_output = str_replace("{call}",$config['call_text'],$theme_output);	
		$theme_output = str_replace("{chat}",$config['chat_text'],$theme_output);	
		$theme_output = str_replace("{sendfile}",$config['sendfile_text'],$theme_output);	
		$theme_output = str_replace("{userinfo}",$config['userinfo_text'],$theme_output);	
		$theme_output = str_replace("{voicemail}",$config['voicemail_text'],$theme_output);	
	} else {
		$theme_output = str_replace("{add}","",$theme_output);	
		$theme_output = str_replace("{call}","",$theme_output);	
		$theme_output = str_replace("{chat}","",$theme_output);	
		$theme_output = str_replace("{sendfile}","",$theme_output);	
		$theme_output = str_replace("{userinfo}","",$theme_output);	
		$theme_output = str_replace("{voicemail}","",$theme_output);	
	}

	// replace all other tags with values
	$theme_output = str_replace("{statustxt}",$config['my_status_text'],$theme_output);	
	$theme_output = str_replace("{username}",$config['user_name'],$theme_output);	
	$theme_output = str_replace("{sep1}",$config['seperator1_text'],$theme_output);	
	$theme_output = str_replace("{sep2}",$config['seperator2_text'],$theme_output);	

	return $theme_output;
}

function skype_status_options() {
	// update the option if form is saved
	if (!empty($_POST['skype_status_update'])) { // pressed udate button
		if (skype_status_valid_id($_POST['skype_id']) &&
			skype_status_valid_theme($_POST['button_theme'])) {
		
			$value = array(
				"skype_id" => $_POST['skype_id'],
				"user_name" => $_POST['user_name'],
				"button_theme" => $_POST['button_theme'],
				"use_function" => $_POST['use_function'],
				"use_status" => $_POST['use_status'],
				"use_voicemail" => $_POST['use_voicemail'],
				"seperator1_text" => $_POST['seperator1_text'],
				"seperator2_text" => $_POST['seperator2_text'],
				"add_text" => $_POST['add_text'],
				"call_text" => $_POST['call_text'],
				"chat_text" => $_POST['chat_text'],
				"sendfile_text" => $_POST['sendfile_text'],
				"my_status_text" => $_POST['my_status_text'],
				"userinfo_text" => $_POST['userinfo_text'],
				"voicemail_text" => $_POST['voicemail_text'],
				"status_error_text" => $_POST['status_error_text'],
				"status_0_text" => $_POST['status_0_text'],
				"status_1_text" => $_POST['status_1_text'],
				"status_2_text" => $_POST['status_2_text'],
				"status_3_text" => $_POST['status_3_text'],
				"status_4_text" => $_POST['status_4_text'],
				"status_5_text" => $_POST['status_5_text'],
				"status_6_text" => $_POST['status_6_text'],
				"status_7_text" => $_POST['status_7_text'],
			);
			update_option("skype_status",$value);
		}
	} else if (!empty($_POST['skype_status_reset'])) { // pressed reset button
			$value = skype_default_values();
			update_option("skype_status",$value);
		}

	$option = skype_status_get_config();
	?>

	<div id="tabs" class="wrap">
		<a id="settingslink" href="#settings" onclick="javascript:document.getElementById('notes').style.display='none'; 
			document.getElementById('guide').style.display='none'; 
			document.getElementById('settings').style.display='block'; 
			document.getElementById('settingslink').style.background='#6699CC'; 
			document.getElementById('settingslink').style.color='#FFFFFF'; 
			document.getElementById('noteslink').style.background='#FFFFFF'; 
			document.getElementById('noteslink').style.color='#006699'; 
			document.getElementById('guidelink').style.background='#FFFFFF'; 
			document.getElementById('guidelink').style.color='#006699';" 
			style="border-left: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; 
			padding: 3px 5px; margin: auto 5px; background-color: #6699CC; color: #FFFFFF">Settings</a> 
		<a id="guidelink" href="#guide" onclick="javascript:document.getElementById('notes').style.display='none'; 
			document.getElementById('guide').style.display='block'; 
			document.getElementById('settings').style.display='none';  
			document.getElementById('settingslink').style.background='#FFFFFF'; 
			document.getElementById('settingslink').style.color='#006699'; 
			document.getElementById('noteslink').style.background='#FFFFFF'; 
			document.getElementById('noteslink').style.color='#006699'; 
			document.getElementById('guidelink').style.background='#6699CC'; 
			document.getElementById('guidelink').style.color='#FFFFFF';" 
			style="border-left: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; 
			padding: 3px 5px; margin: auto 5px;">Quick Guide</a> 
		<a id="noteslink" href="#notes" onclick="javascript:document.getElementById('notes').style.display='block'; 
			document.getElementById('guide').style.display='none'; 
			document.getElementById('settings').style.display='none'; 
			document.getElementById('settingslink').style.background='#FFFFFF'; 
			document.getElementById('settingslink').style.color='#006699'; 
			document.getElementById('noteslink').style.background='#6699CC'; 
			document.getElementById('noteslink').style.color='#FFFFFF'; 
			document.getElementById('guidelink').style.background='#FFFFFF'; 
			document.getElementById('guidelink').style.color='#006699';" 
			style="border-left: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; 
			padding: 3px 5px; margin: auto 5px;">Notes</a> 
	</div>
	<script type="text/javascript">
		document.write('<div id="loading" class="wrap" stype="display: none;"><strong>Please wait while page is loading...</strong></div>');
		document.getElementById('loading').style.display='block';
	</script>

	<div id="settings" class="wrap" style="min-height: 800px;">
		<h2>Skype Online Status Settings</h2>
		
		<p align="right"><a href="#wphead">back to top</a></p>
		<form method="post" action="#">
		<fieldset class="options"><legend>Skype ID</legend>
			<p><label>Your Skype ID*: <input type="text" name="skype_id" value="<?php echo $option['skype_id']; ?>" /></label><br />
			<em>* leave blank to disable all instances of the Skype online status button on your weblog</em></p>
		</fieldset>
		<div style="float: left; width: 68%;">
			<fieldset class="options"><legend>User name</legend>
				<p><label>Your Skype name: <input type="text" name="user_name" value="<?php echo $option['user_name']; ?>" /></label></p>
			</fieldset>
			<script type="text/javascript">
			var visible_preview = "<?php echo $option['button_theme']; ?>";
			var type = "IE";
			if (navigator.userAgent.indexOf("Opera")!=-1 && document.getElementById) type="OP";		//Opera
			else if (document.all) type="IE";														//Internet Explorer e.g. IE4 upwards
			else if (document.layers) type="NN";													//Netscape Communicator 4
			else if (!document.all && document.getElementById) type="MO";							//Mozila e.g. Netscape 6 upwards
			else type = "IE";		//I assume it will not get here
	
			function ChangeStyle(el) {
				if (type=="IE") {
					eval("document.all." + visible_preview + ".style.display='none'");
					eval("document.all." + el.value + ".style.display='block'");
					}
				if (type=="NN") {
					eval("document." + visible_preview + ".display='none'");
					eval("document." + el.value + ".display='block'");
					}
				if (type=="MO" || type=="OP") {
					eval("document.getElementById('" + visible_preview + "').style.display='none'");
					eval("document.getElementById('" + el.value + "').style.display='block'");
					}
				visible_preview = el.value;
			}
			</script>
			<fieldset class="options"><legend>Theme</legend>
				<p>
				<label for="button_theme">Select a theme template: </label><select name="button_theme" id="button_theme" onchange="ChangeStyle(this.options[this.selectedIndex])">
				<?
			
				$skypeid = $option['skype_id'];
				$buttondir = dirname(__FILE__)."/templates/";
			
				$previews = "";
			
				$themes = array();
			
				if (is_dir($buttondir)) {
					if ($dh = opendir($buttondir)) {
						while (($file = readdir($dh)) !== false) {
							$fname = $buttondir . $file;
							if (is_file($fname) && ".html" == substr($fname,-5)) {
			
								$theme_name = substr(basename($fname),0,-5);
			
								$selected = ""; // radio button
								$display = "none"; // hide preview layer
								
								if ($theme_name == $option['button_theme']) {
									$selected = " selected";
									$display = " block";
								};
			
								// attempt to get the human readable name from the first line of the file
								$fc = file_get_contents($fname);
								preg_match("|<!-- (.*) - http://www.skype.com/go/skypebuttons|ms",$fc,$matches);
								
								print "<option onmouseover=\"ChangeStyle(this);\" value=\"$theme_name\"$selected>$matches[1]</option>";
								
									$prvw_output = skype_parse_theme($fc, $option);
									$previews .= "<div id=\"$theme_name\" style=\"display:$display;\">$prvw_output</div>";
							}
						}
						closedir($dh);
					}
				}
			
				?>
				</select>
				</p>
			</fieldset>
			<fieldset class="options"><legend>Display options</legend>
				<p>These settings define which options should be used to replace their respective tag (if present) in the selected template file. If unchecked, the tags will be blanked out.</p> 
				<ul>
					<li><label><input type="checkbox" name="use_voicemail"<?php if ( $option['use_voicemail'] == "on" ) { print " checked"; } ?> /> Use <strong>Leave a voicemail</strong> in dropdown button. Leave unchecked if you do not have a SkypeIn account or SkypeVoicemail.</label></li>
					<li><label><input type="checkbox" name="use_function"<?php if ( $option['use_function'] == "on" ) { print " checked"; } ?> /> Use <strong>Action text</strong> (as defined below) for {add/call/chat/userinfo/voicemail/sendfile} tags.</label></li>
					<li><label><input type="checkbox" name="use_status"<?php if ( $option['use_status'] == "on" ) { print " checked"; } ?> /> Use <strong>Status text</strong> (as defined below) for the {status} tag.</label></li>
				</ul>
			</fieldset>
		</div>
		<div class="alternate" style="float: right; width: 28%; height: 200px; border: 1px solid #CCCCCC; padding: 5px;">
			<strong>Preview</strong>
			<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/buttons/white_dropdown/dropdown.js"></script>
			<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/buttons/transparent_dropdown/dropdown.js"></script>
			<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
			<?php 
			echo $previews;
			?>
		</div>

		<p align="right" style="clear: both;"><a href="#wphead">back to top</a></p>

		<div style="float: left; width: 49%;">
			<fieldset class="options"><legend>Tags</legend>
				<p>Define texts to replace their respective template tags relating to the Skype button action.</p> 
				<table>
					<tr>
						<th>Action text - {tag}</th>
						<th>Text</th>
					</tr>
					<tr>
						<td><label for="add_text">Add me to Skype - {add}: </label></td>
						<td><input type="text" name="add_text" id="add_text" value="<?php echo $option['add_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="call_text">Call me! - {call}: </label></td>
						<td><input type="text" name="call_text" id="call_text" value="<?php echo $option['call_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="chat_text">Chat with me - {chat}: </label></td>
						<td><input type="text" name="chat_text" id="chat_text" value="<?php echo $option['chat_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="sendfile_text">Send me a file - {sendfile}: </label></td>
						<td><input type="text" name="sendfile_text" id="sendfile_text" value="<?php echo $option['sendfile_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="userinfo_text">View my profile - {userinfo}: </label></td>
						<td><input type="text" name="userinfo_text" id="userinfo_text" value="<?php echo $option['userinfo_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="voicemail_text">Leave me voicemail - {voicemail}: </label></td>
						<td><input type="text" name="voicemail_text" id="voicemail_text" value="<?php echo $option['voicemail_text']; ?>" /></td>
					</tr>
				</table>
				<br />
				<table>
					<tr>
						<th>Other - {tag}</th>
						<th>Text</th>
					</tr>
					<tr>
						<td><label for="my_status_text">My status - {statustxt}: </label></td>
						<td><input type="text" name="my_status_text" id="my_status_text" value="<?php echo $option['my_status_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="seperator1_text">First seperator - {sep1}: </label></td>
						<td><input name="seperator1_text" id="seperator1_text" value="<?php echo $option['seperator1_text'] ?>" /></td>
					</tr>
					<tr>
						<td><label for="seperator2_text">Second seperator - {sep2}: </label></td>
						<td><input name="seperator2_text" id="seperator2_text" value="<?php echo $option['seperator2_text'] ?>" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div style="float: left; width: 49%;">
			<fieldset class="options"><legend>Status text</legend>
				<p>Text that will replace the {status} template tag depending on actual online status.</p>
				<table>
					<tr>
						<th>Status (value)</th>
						<th>Text</th>
					</tr>
					<tr>
						<td><label for="status_error_text">Error (none): </label></td>
						<td><input type="text" name="status_error_text" id="status_error_text" value="<?php echo $option['status_error_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_0_text">Unknown (0): </label></td>
						<td><input type="text" name="status_0_text" id="status_0_text" value="<?php echo $option['status_0_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_1_text">Offline (1): </label></td>
						<td><input type="text" name="status_1_text" id="status_1_text" value="<?php echo $option['status_1_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_2_text">Online (2): </label></td>
						<td><input type="text" name="status_2_text" id="status_2_text" value="<?php echo $option['status_2_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_3_text">Away (3): </label></td>
						<td><input type="text" name="status_3_text" id="status_3_text" value="<?php echo $option['status_3_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_4_text">Not available (4): </label></td>
						<td><input type="text" name="status_4_text" id="status_4_text" value="<?php echo $option['status_4_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_5_text">Do not disturb (5): </label></td>
						<td><input type="text" name="status_5_text" id="status_5_text" value="<?php echo $option['status_5_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_6_text">Invisible (6): </label></td>
						<td><input type="text" name="status_6_text" id="status_6_text" value="<?php echo $option['status_6_text']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_7_text">Skype me (7): </label></td>
						<td><input type="text" name="status_7_text" id="status_7_text" value="<?php echo $option['status_7_text']; ?>" /></td>
					</tr>
				</table>
			</fieldset>
		</div>

		<p align="right" style="clear: both;"><a href="#wphead">back to top</a></p>

		<p class="submit">
			<input type="submit" name="skype_status_update" value="<?php _e('Update options','skype_status') ?> &raquo;" />
			<input type="submit" onclick='return confirm("All your personal settings will be overwritten, including Skype ID, User name and Theme. Do you really want to reset your configuration?");' name="skype_status_reset" value="<?php _e('Reset options','skype_status') ?> &raquo;" /><br />&nbsp;</p>
		</form>

	</div>
	<div id="guide" class="wrap" style="min-height: 800px;">
		<h2>Quick guide</h2>
		<ul>
			<li><a href="#basic">Basic Use</a></li>
			<li><a href="#adv">Advanced</a></li>
			<li><a href="#templ">Templates</a></li>
		</ul>

		<p id="basic" align="right"><a href="#wphead">back to top</a></p>
		<h3>Basic Use</h3>
		<p>Define all Skype settings such as Skype ID, User name and preferred Theme on the Skype Online Status Settings page as your default. And use the methodes described below to trigger the default Skype Status button on your blog pages. Under 'Advanced' you can read about ways to override your default settings and create different Skype buttons across your blog.</p>
		<h4>Syntax</h4>
		<h5>In theme files (like sidebar.php)</h5>
		<p>Put <strong>&lt;?php if (function_exists(get_skype_status)) { get_skype_status(''); } else { echo "Skype button disabled"; } ?&gt;</strong> in your sidebar.php or other WordPress template files to display a Skype Button with Online Status information on your blog pages. Your predefined default settings (above) will be used.</p><p>The 'function_exists'-check is there to prevent an error when the plugin is disabled. In this case the echo text is displayed. You can define another alternative action or remove 'else { ... }' to display nothing at all.</p>
		<h5>In posts and page content</h5>
		<p>As of version 1.7, it is also possible to trigger a Skype Status button (as predefined on the Skype Online Status Settings page) within posts or page content. Use the tag <strong>&lt;!--skype status--&gt;</strong> in the HTML code of your post or page content to do this.</p>

		<p id="adv" align="right"><a href="#wphead">back to top</a></p>
		<h3>Advanced</h3>
		<p>It is also possible to use multiple Skype buttons for different Skype Users and with different themes across your blog pages.</p>
		<h4>Syntax</h4>
		<p>Use the syntax <strong>get_skype_status('parameter1=something&parameter2=something_else');</strong> to get a button that looks different from the predefined settings on the Skype Online Status Settings page, or even using another Skype ID.</p>
		<h4>Parameters</h4>
			<dl><dt>skype_id</dt><dd>Alternative Skype ID</dd>
				<dt>user_name</dt><dd>Define the Skype user or screen name.</dd>
				<dt>button_theme</dt><dd>Define the theme template file to use for the button. Value must match a filename (without extention) from the /plugins/skype_status/templates/ directory or the predefined theme template will be used.</dd>
				<dt>use_voicemail</dt><dd>Set to 'on' if you want to display the 'Leave a voicemail' link in the Dropdown themes. Use this only if you have a SkypeIn account or SkypeVoicemail.</dd>
			</dl>
		<h4>Example</h4>
		<p>For example <strong>get_skype_status('skype_id=echo123&amp;user_name=Skype test&amp;button_theme=callme_mini')</strong> will generate a Skype button with all the predefined settings but for Skype user 'echo123' (the Skype voice test user) and using template file 'callme_mini.html'.</p>

		<p id="templ" align="right"><a href="#wphead">back to top</a></p>
		<h3>Templates</h3>
		<p>All predefined templates can be found in the /plugins/skype_status/templates/ directory. You add new or edit existing ones with any simple text editor (like Notepad) or even a WYSIWYG editor (like Dreamweaver) as long as you follow some rules.
		<h4>Mark-up rules</h4>
		<ol>
			<li>All template files must have a name consisting of only <strong>letters</strong>, <strong>numbers</strong> and/or <strong>underscores (_)</strong> or <strong>dashes (-)</strong>. Please avoid any other signs like dots or whitespaces.</li>
			<li>The files must have the <strong>.html</strong> extention.</li>
			<li>The first line of any file must be like: <br />
				<strong>&lt;!-- 'Template Name' style description - http://www.skype.com/go/skypebuttons --&gt;</strong><br />
				where the 'Template Name' style description part will represent the template name on the Skype Online Status Settings page.</li>
			<li>Within the template files certain tags like <strong>{skypeid}</strong> are used that will be replaced according to their respective configuration on the Skype Status Settings page. See 'Template tags' below for all available tags.</li>
			<li>Everything within the template files between <strong>&lt;!-- voicemail_start --&gt;</strong> and <strong>&lt;!-- voicemail_end --&gt;</strong> will be erased when the option 'Use <strong>Leave a voicemail</strong>' on the Skype Online Status Settings page is NOT checked.</li>
		</ol>
		<p>For the rest you are free to put anything you like in the template files.<br />
			 To get started see <a href="http://www.skype.com/go/skypebuttons">http://www.skype.com/go/skypebuttons</a> for an interactive form to create new Skype Button code.</p> 
		<h4>Template tags</h4>
		<p> The following tags are available:</p>
		<h5>General tags</h5>
		<dl>
			<dt>{skypeid}</dt><dd>Put this where the 'Skype ID' should go. Usually href="skype:{skypeid}?call" but it can also be used elsewhere.</dd>
			<dt>{username}</dt><dd>Put this where you want the 'User name' to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{statustxt}</dt><dd>Put this where you want the <em>static</em> 'My status' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{status}</dt><dd>Put this where you want the <em>dynamic</em> 'Status' texts to appear, such as in title="", alt="" or as link text. The status text (defined on the Skype Status Settings page under 'Status text') depends on the actual online status of the defined Skype user and ranges from 'Unknown' to 'Online'.</dd>
			<dt>{sep1}</dt><dd>Put this where you want the 'First seperator' text to appear, usually between the tags like {call} and {username}.</dd>
			<dt>{sep2}</dt><dd>Put this where you want the 'Second seperator' text to appear, usually between the tags like {username} and {status}.</dd>
		</dl>
		<h5>Action text tags</h5>
		<dl>
			<dt>{add}</dt><dd><dd>Put this where you want the 'Add me to Skype' text to appear, such as in title="", alt="" or as link text.</dd></dd>
			<dt>{call}</dt><dd>Put this where you want the 'Call me!' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{chat}</dt><dd>Put this where you want the 'Chat with me' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{sendfile}</dt><dd>Put this where you want the 'Send me a file' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{userinfo}</dt><dd>Put this where you want the 'View my profile' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{voicemail}</dt><dd>Put this where you want the 'Leave me a voicemail' text to appear, such as in title="", alt="" or as link text.</dd>
		</dl>
		<h4>Examples</h4>
		<p>The classic 'Call me!' button template looks like this:</p>
		<blockquote>&lt;!-- 'Call me!' classic style - http://www.skype.com/go/skypebuttons --&gt;<br />&lt;a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{call}{sep1}{username}{sep2}{status}">&lt;img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="{call}{sep1}{username}{sep2}{status}" /&gt;&lt;/a&gt;</blockquote>
		<p>The template for a simple text link displaying username and online status (seperated by the second seperator tag) could look like this:</p>
		<blockquote>&lt;!-- 'My status' plain text link - http://www.skype.com/go/skypebuttons --&gt;<br />&lt;a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{username}{sep2}{status}">{username}{sep2}{status}&lt;/a&gt;</blockquote>
		<p align="right"><a href="#wphead">back to top</a></p>

	</div>
	<div id="notes" class="wrap" style="min-height: 800px;">
		<h2>Notes</h2>
		<ul>
			<li><a href="#prl">Pricing an Licensing</a></li>
			<li><a href="#credits">Credits</a></li>
			<li><a href="#revhist">Revision History</a></li>
			<li><a href="#todo">Todo</a></li>
		</ul>

		<p id="prl" align="right"><a href="#wphead">back to top</a></p>
		<h3>Pricing and Licensing</h3>
		<p>This plugin is released under GPL so you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a small donation for my efforts and the time I&#8217;ve spent writing and supporting this plugin.</p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<fieldset><legend>I appreciate every contribution, no matter if it&#8217;s two or twenty euro or any other amount. <em>Ravan</em></legend>
			<ul>
				<li><label><input type="radio" name="amount" value="2.00" /> &euro; 2.00</label></li>
				<li><label><input type="radio" name="amount" value="5.00" /> &euro; 5.00</label></li>
				<li><label><input type="radio" name="amount" value="10.00" /> &euro; 10.00</label></li>
				<li><label><input type="radio" name="amount" value="20.00" /> &euro; 20.00</label></li>
				<li><label><input type="radio" name="amount" value="" checked="checked" /> any other amount</label></li>
			</ul>
			<p><input type="hidden" name="cmd" value="_xclick"/>
				<input type="hidden" name="business" value="ravanhagen@zonnet.nl">
				<input type="hidden" name="item_name" value="Skype Status Button plugin"/>
				<input type="hidden" name="no_shipping" value="1"/>
				<input type="hidden" name="return" value="http://www.4visions.nl/en/index.php?section=55" />
				<input type="hidden" name="cancel_return" value="http://www.4visions.nl/en/index.php?section=55"/>
				<input type="hidden" name="currency_code" value="EUR"/>
				<input type="hidden" name="tax" value="0"/>
				<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"/></p></fieldset>
		</form>
	
		<p id="credits" align="right"><a href="#wphead">back to top</a></p>
		<h3>Credits</h3>
		<p>This plugin was built by <em>Ravan</em>. It is based upon the neat little plugin <a href="http://anti.masendav.com/skype-button-for-wordpress/">Skype Button v2.01</a> by <em>Anti Veeranna</em>. Many thanks!</p>

		<p id="revhist" align="right"><a href="#wphead">back to top</a></p>
		<h3>Revision History</h3>
		<ul>
			<li>[2006-06-23] version 1.7: added post hook &lt;!--skype status--&gt; and appended instructions to quickguide</li>
			<li>[2006-06-23] version 1.6: wrote templating guide and redesigned Options > Skype Status page</li>
			<li>[2006-06-22] version 1.5: added a plain text fallback template to the code</li>
			<li>[2006-06-22] version 1.4: added reset button and default settings</li>
			<li>[2006-06-21] version 1.3: added new template tags {username} {sep1} {sep2}</li>
			<li>[2006-06-20] version 1.2: minor bugfixes
				<ol><li>inconsistent options page form-labels </li>
					<li>status text not defaulting to the error value when mystatus.skype.com is off-line </li></ol></li>
			<li>[2006-05-02] version 1.1: added new text template file</li>
			<li>[2006-04-26] version 1.0: wrote instructions (quick guide)</li>
			<li>[2006-04-20] version 0.9: added skype user name</li>
			<li>[2006-04-12] version 0.8: added customizability for get_skype_status('options')</li>
			<li>[2006-04-10] version 0.7: redesign admin interface</li>
			<li>[2006-03-05] version 0.3 - 0.6: added lot's of new settings and template tags</li>
			<li>[2006-03-03] version 0.2: added function skype_parse_theme() and skype_status_check()</li>
			<li>[2006-03-03] version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)</li>
		</ul>

		<p id="todo" align="right"><a href="#wphead">back to top</a></p>
		<h3>Todo</h3>
		<ul>
			<li>Make parameter options available:
				<dl>
					<dt>use_function</dt><dd>Set to 'on' to use Function text in image alt text and link title.</dd>
					<dt>use_status</dt><dd>Set to 'on' to use Status text in image alt text and link title. Replaces {status} in template files.</dd>
					<dt>seperator_text</dt><dd>Text that seperates Function text and Status text in image alt text and link title. This will only be used when both 'use_function' and 'use_status' are 'on' (default)</dd>
					<dt>add_text</dt><dd>Function text for 'Add me to Skype'. Replaces {add} in template files.</dd>
					<dt>call_text</dt><dd>Function text for 'Call me'. Replaces {call} in template files.</dd>
					<dt>chat_text</dt><dd>Function text for 'Chat with me'. Replaces {chat} in template files.</dd>
					<dt>sendfile_text</dt><dd>Function text for 'Send me a file'. Replaces {sendfile} in template files.</dd>
					<dt>my_status_text</dt><dd>Function text for 'My status'. Replaces {statustxt} in template files.</dd>
					<dt>userinfo_text</dt><dd>Function text for 'View my profile'. Replaces {userinfo} in template files.</dd>
					<dt>voicemail_text</dt><dd>Function text for 'Leave me voicemail'. Replaces {voicemail} in template files.</dd>
					<dt>status_error_text</dt><dd>Status text used when status could not be checked. Replaces {status} in template files.</dd>
					<dt>status_0_text</dt><dd>Status text for 'Unknown'. Replaces {status} in template files.</dd>
					<dt>status_1_text</dt><dd>Status text for 'Offline'. Replaces {status} in template files.</dd>
					<dt>status_2_text</dt><dd>Status text for 'Online'. Replaces {status} in template files.</dd>
					<dt>status_3_text</dt><dd>Status text for 'Away'. Replaces {status} in template files.</dd>
					<dt>status_4_text</dt><dd>Status text for 'Not available'. Replaces {status} in template files.</dd>
					<dt>status_5_text</dt><dd>Status text for 'Do not disturb'. Replaces {status} in template files.</dd>
					<dt>status_6_text</dt><dd>Status text for 'Invisible'. Replaces {status} in template files.</dd>
					<dt>status_7_text</dt><dd>Status text for 'Skype me'. Replaces {status} in template files.</dd>
				</dl>
		</ul>
		<p align="right"><a href="#wphead">back to top</a></p>
			
		<h3>Wish List version 2 and beyond</h3>
		<ul>
			<li>Online theme editor</li>
			<li>Make multiple Skype ID's (within the Skype Status Settings) possible</li>
			<li>Use XML to get online status values</li>
			<li>Internationalization</li>
		</ul>
		<p align="right"><a href="#wphead">back to top</a></p>
	</div>
	<script type="text/javascript">
		document.getElementById('loading').style.display='none';
		document.getElementById('notes').style.display='none'; 
		document.getElementById('guide').style.display='none'; 
		document.getElementById('settings').style.display='block';
	</script>
	<?

}

// template tag hooks
function get_skype_status($args = '') {
	parse_str($args, $r);

	if ( !isset($r['skype_id']) )
		$r['skype_id'] = '';
	if ( !isset($r['user_name']) )
		$r['use_function'] = '';
	if ( !isset($r['button_theme']) )
		$r['button_theme'] = '';
	if ( !isset($r['use_voicemail']) )
		$r['use_voicemail'] = '';
		
	skype_status($r['skype_id'], $r['user_name'], $r['button_theme'], $r['use_voicemail']);
}	

function skype_status($skype_id="", $user_name="", $button_theme="", $use_voicemail="") {
	$r = skype_status_get_config();
	
	$buttondir = dirname(__FILE__)."/templates/";
	$file = $buttondir . $r['button_theme'] . ".html";
	
	// check and override predefined config with args
	if ($skype_id != "") {
		$r['skype_id'] = $skype_id;
		$r['user_name'] = $user_name;
	}
	if ($button_theme != "" && file_exists($buttondir.$button_theme.".html")) {
		$file = $buttondir . $button_theme . ".html";
	}
	if ($use_voicemail != "")
		$r['use_voicemail'] = $use_voicemail;
	
	// check: a skypeid or return nothing
	if (empty($r['skype_id'])) {
		return "";
	}
	
	// get template file or revert to plain text fallback template
	if (file_exists($file))
		$fc = file_get_contents($file);
	else
		$fc = '<a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{call}{sep1}{username}{sep2}{status}">{username}{sep2}{status}</a>';
	
	$btn_output = "<!-- start Skype Online Status plugin by Ravan - http://4visions.nl/ -->\n";
	$btn_output .= skype_parse_theme($fc, $r);	
	$btn_output .= "<!-- end Skype Online Status -->\n";
	
	print $btn_output;
}

function skype_status_script() {
	$config = skype_status_get_config();
	if (empty($config['skype_id'])) {
		return "";
	};
	print '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>';
}

// wrapper function which calls the Skype Status button
function skype_status_callback($content) {
	global $wpcf_strings;

	// run the input check
	if(!preg_match('|<!--skype status-->|', $content)) {
		return $content;
	}

	// get button
	$button = skype_status();

	// replace hook
	return str_replace('<!--skype status-->', $button, $content);
}

// create WP hooks
add_action('wp_head', 'skype_status_script');
add_action('admin_menu', 'skype_status_add_option');
add_filter('the_content', 'skype_status_callback');

?>
