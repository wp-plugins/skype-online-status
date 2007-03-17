<?php
/*
Plugin Name: Skype Online Status
Plugin URI: http://4visions.nl/en/index.php?section=55
Description: Checks your Skype Online Status and allows you to add multiple, highly customizable and accessible Skype buttons to your blog. Based on the plugin 'Skype Button 2.01' by Anti Veeranna. Documentation and configuration options on the <a href="./options-general.php?page=skype_status.php">Skype Online Status Settings page</a>.  
Version: 1.2
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
	Wish List version 2 and beyond :)
		- Extend parameters get_skype_status('options')
		- Online theme editor
		- Create post hook <!--skypestatus-->
		- Collection of default settings at top of skype_status.php
		- 'Revert to Skype default' button 
		- Make multiple Skype ID's with own settings possible
		- Write documentation
		- Internationalization
		- Get XML online status
	
	Revision History
		version 1.2: minor bugfixes
			1. inconsistent options page form-labels 
			2. skype_status_check not defaulting to status_error_txt when mystatus.skype.com is off-line 
		version 1.1: added new text template file
		version 1.0: wrote instructions (quick guide)
		version 0.9: added skype user name
		version 0.8: added customizability for get_skype_status('options')
		version 0.7: redesign admin interface
		version 0.3 - 0.6: added lot's of new settings and template tags
		version 0.2: added function skype_parse_theme() and skype_status_check()
		version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)
*/

// -- initialization functions --
function skype_status_install() {
	$value = array(
		"skype_id" => "",
		"user_name" => "",
		"button_theme" => "",
		"add_text" => "Add me to Skype",
		"call_text" => "Call me",
		"chat_text" => "Chat with me",
		"sendfile_text" => "Send me a file",
		"my_status_text" => "My status",
		"userinfo_text" => "View my profile",
		"voicemail_text" => "Leave me voicemail",
		"use_function" => "",
		"use_status" => "on",
		"use_voicemail" => "",
		"seperator_text" => " - ",
		"status_error_text" => "Error",
		"status_0_text" => "Unknown",
		"status_1_text" => "Offline",
		"status_2_text" => "Online",
		"status_3_text" => "Away",
		"status_4_text" => "Not available",
		"status_5_text" => "Do not disturb",
		"status_6_text" => "Invisible",
		"status_7_text" => "Skype me!",
	);
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
	$seperator = "";
	$status = "";
	$num = skype_status_check($config['skype_id']);
	
	if ($config['use_status']=="on" && ($config['use_function']=="name" || $config['use_function']=="function")) {
		$seperator = $config['seperator_text'];
	}
	if ($config['use_status']=="on") {
		$status = $config['status_'.$num.'_text'];
	}
	
	// replace triggers with values and return result
	if ($config['use_voicemail']!="on") {
		$fc = preg_replace("|<!-- voicemail_start (.*) voicemail_end -->|ms","",$fc);
	}
	$theme_output = str_replace("{skypeid}",$config['skype_id'],$fc);
	if ($config['use_function']=="function") {
		$theme_output = str_replace("{add}",$config['add_text'],$theme_output);	
		$theme_output = str_replace("{call}",$config['call_text'],$theme_output);	
		$theme_output = str_replace("{chat}",$config['chat_text'],$theme_output);	
		$theme_output = str_replace("{sendfile}",$config['sendfile_text'],$theme_output);	
		$theme_output = str_replace("{userinfo}",$config['userinfo_text'],$theme_output);	
		$theme_output = str_replace("{voicemail}",$config['voicemail_text'],$theme_output);	
		$theme_output = str_replace("{status_txt}",$config['my_status_text'],$theme_output);	
	} else if ($config['use_function']=="name") {
		$theme_output = str_replace("{add}",$config['user_name'],$theme_output);	
		$theme_output = str_replace("{call}",$config['user_name'],$theme_output);	
		$theme_output = str_replace("{chat}",$config['user_name'],$theme_output);	
		$theme_output = str_replace("{sendfile}",$config['user_name'],$theme_output);	
		$theme_output = str_replace("{userinfo}",$config['user_name'],$theme_output);	
		$theme_output = str_replace("{voicemail}",$config['user_name'],$theme_output);	
		$theme_output = str_replace("{status_txt}",$config['user_name'],$theme_output);	
	} else {
		$theme_output = str_replace("{add}","",$theme_output);	
		$theme_output = str_replace("{call}","",$theme_output);	
		$theme_output = str_replace("{chat}","",$theme_output);	
		$theme_output = str_replace("{sendfile}","",$theme_output);	
		$theme_output = str_replace("{userinfo}","",$theme_output);	
		$theme_output = str_replace("{voicemail}","",$theme_output);	
		$theme_output = str_replace("{status_txt}","",$theme_output);	
	}
	$theme_output = str_replace("{sep}",$seperator,$theme_output);	
	$theme_output = str_replace("{status}",$status,$theme_output);	
	return $theme_output;
}

function skype_status_options() {
	// update the option if form is saved
	if (isset($_POST['skype_status_update'])) {
		if (skype_status_valid_id($_POST['skype_id']) &&
			skype_status_valid_theme($_POST['button_theme'])) {
		
			$value = array(
				"skype_id" => $_POST['skype_id'],
				"user_name" => $_POST['user_name'],
				"button_theme" => $_POST['button_theme'],
				"use_function" => $_POST['use_function'],
				"use_status" => $_POST['use_status'],
				"use_voicemail" => $_POST['use_voicemail'],
				"seperator_text" => $_POST['seperator_text'],
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
		};
	};
	$option = skype_status_get_config();
	?>

	<div class="wrap" style="min-height: 800px;">
  	<h2>Skype Online Status Settings</h2>
	
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
							$display = "none"; // preview layer
							
							if ($theme_name == $option['button_theme']) {
								$selected = 'selected';
								$display = 'block';
							};
		
							// attempt to get the human readable name from the first line of the file
							$fc = file_get_contents($fname);
							preg_match("|<!-- (.*) - http://www.skype.com/go/skypebuttons|ms",$fc,$matches);
							
							print "<option onmouseover=\"ChangeStyle(this);\" value=\"$theme_name\" $selected >$matches[1]</option>";
							
								$prvw_output = skype_parse_theme($fc, $option);
								$previews .= "<div id=\"$theme_name\" style=\"display: $display;\">$prvw_output</div>";
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
			<p>These settings define which options should be used to replace their respective tag (if present) in the template file. If unchecked, the tags will be blanked out. The {sep} tag value will only be used when both 'User name/Function text' and 'Status text' are active.</p> 
			<ul>
				<li>Replace tags {add/call/chat/userinfo/voicemail/sendfile} with:<br />
					<label><input type="radio" value="name" name="use_function"<?php if ( $option['use_function'] == "name" ) { print " checked"; } ?> /> the <strong>User name</strong> as defined above or;</label><br />
					<label><input type="radio" value="function" name="use_function"<?php if ( $option['use_function'] == "function" ) { print " checked"; } ?> /> their respective <strong>Function text</strong> as defined below or;</label><br />
					<label><input type="radio" value="nothing" name="use_function"<?php if ( $option['use_function'] != "name" && $option['use_function'] != "function" ) { print " checked"; } ?> /> nothing.</label></li>
				<li><label><input type="checkbox" name="use_status"<?php if ( $option['use_status'] == "on" ) { print " checked"; } ?> /> Use <strong>Status text</strong> (as defined below) for the {status} tag.</label></li>
				<li><label>Seperate User name or Function text and Status text with {sep} tag value <input name="seperator_text" value="<?php echo $option['seperator_text'] ?>"style="width: 50px" />.</label></li>
				<li><label><input type="checkbox" name="use_voicemail"<?php if ( $option['use_voicemail'] == "on" ) { print " checked"; } ?> /> Use <strong>Leave a voicemail</strong> in dropdown button. Leave unchecked if you do not have a SkypeIn account or SkypeVoicemail.</label></li>
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
	<div style="clear: both;">
	</div>
	<div style="float: left; width: 48%;">
		<fieldset class="options"><legend>Function text</legend>
			<br />
			<table>
				<tr>
					<th>Function</th>
					<th>Text</th>
				</tr>
				<tr>
					<th><label for="add_text">Add me to Skype: </label></th>
					<td><input type="text" name="add_text" id="add_text" value="<?php echo $option['add_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="call_text">Call me!: </label></th>
					<td><input type="text" name="call_text" id="call_text" value="<?php echo $option['call_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="chat_text">Chat with me: </label></th>
					<td><input type="text" name="chat_text" id="chat_text" value="<?php echo $option['chat_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="sendfile_text">Send me a file: </label></th>
					<td><input type="text" name="sendfile_text" id="sendfile_text" value="<?php echo $option['sendfile_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="userinfo_text">View my profile: </label></th>
					<td><input type="text" name="userinfo_text" id="userinfo_text" value="<?php echo $option['userinfo_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="voicemail_text">Leave me voicemail: </label></th>
					<td><input type="text" name="voicemail_text" id="voicemail_text" value="<?php echo $option['voicemail_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="my_status_text">My status: </label></th>
					<td><input type="text" name="my_status_text" id="my_status_text" value="<?php echo $option['my_status_text']; ?>" /></td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div style="float: right; width: 48%;">
		<fieldset class="options"><legend>Status text</legend>
			<br />
			<table>
				<tr>
					<th>Status (value)</th>
					<th>Text</th>
				</tr>
				<tr>
					<th><label for="status_error_text">Error (none): </label></th>
					<td><input type="text" name="status_error_text" id="status_error_text" value="<?php echo $option['status_error_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_0_text">Unknown (0): </label></th>
					<td><input type="text" name="status_0_text" id="status_0_text" value="<?php echo $option['status_0_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_1_text">Offline (1): </label></th>
					<td><input type="text" name="status_1_text" id="status_1_text" value="<?php echo $option['status_1_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_2_text">Online (2): </label></th>
					<td><input type="text" name="status_2_text" id="status_2_text" value="<?php echo $option['status_2_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_3_text">Away (3): </label></th>
					<td><input type="text" name="status_3_text" id="status_3_text" value="<?php echo $option['status_3_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_4_text">Not available (4): </label></th>
					<td><input type="text" name="status_4_text" id="status_4_text" value="<?php echo $option['status_4_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_5_text">Do not disturb (5): </label></th>
					<td><input type="text" name="status_5_text" id="status_5_text" value="<?php echo $option['status_5_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_6_text">Invisible (6): </label></th>
					<td><input type="text" name="status_6_text" id="status_6_text" value="<?php echo $option['status_6_text']; ?>" /></td>
				</tr>
				<tr>
					<th><label for="status_7_text">Skype me (7): </label></th>
					<td><input type="text" name="status_7_text" id="status_7_text" value="<?php echo $option['status_7_text']; ?>" /></td>
				</tr>
			</table>
		</fieldset>
	</div>
	<p class="submit" style="clear: both;">
        <input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
        </p>
	<input type="hidden" name="skype_status_update" value="1"/>
	</form>
	<h2>Quick guide</h2>
	<h3>Basic use</h3>
	<p>Put <strong>&lt;?php if (function_exists(get_skype_status)) { get_skype_status(''); } else { echo "Skype button disabled"; } ?&gt;</strong> in your sidebar.php or other WordPress template files to display a Skype Button with Online Status information on your blog pages. Your predefined default settings (above) will be used.</p><p>The 'function_exists' check is there to prevent an error when the plugin is disabled. In this case the echo text is displayed. You can define another alternative action or remove 'else { ... }' to display nothing at all.</p>
	<h3>Advanced</h3>
	<p>Use the syntax <strong>get_skype_status('options');</strong> to get a different look or even using other Skype ID's for multiple Skype buttons on your pages.</p>
	<p>For example get_skype_status('skype_id=echo123&user_name=Skype test&button_theme=callme_mini'); will generate a Skype button with all the predefined settings but for Skype user 'echo123' (the Skype voice test user) and using template file 'callme_mini.html'.</p>
	<p>It is possible to use multiple Skype buttons for different people and with different themes across your blog pages.</p>
	<h4>Parameters</h4>
		<dl><dt>skype_id</dt><dd>Alternative Skype ID</dd>
			<dt>user_name</dt><dd>Define the Skype user or screen name.</dd>
			<dt>button_theme</dt><dd>Define the theme template file to use for the button. Value must match a filename from the /plugins/skype_status/templates/ directory or the predefined theme template will be used.</dd>
			<dt>use_voicemail</dt><dd>Set to 'on' if you want to display the 'Leave a voicemail' link in the Dropdown themes. Use this only if you have a SkypeIn account or SkypeVoicemail.</dd>
		</dl>
	<h2>Notes</h2>
	<h3>Pricing and Licensing</h3>
	<p>This plugin is released under GP so you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a small donation for my efforts and the time I&#8217;ve spent writing and supporting this plugin. <br />
	I appreciate every donation you leave me, no matter if it&#8217;s two or twenty euro or any other amount.</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="margin:0; padding:0;">
	<fieldset><legend>Donate</legend>
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
			<input type="hidden" name="bn" value="PP-DonationsBF"/>
			<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"/></p></fieldset>
	</form>

	<h3>Credits</h3>
	<p>This plugin was built upon the neat little plugin <a href="http://anti.masendav.com/skype-button-for-wordpress/">Skype Button v2.01</a> by Anti Veeranna. Many thanks!</p>
	<h3>Revision History</h3>
	<ul>
		<li>version 1.2: minor bugfixes
			<ul><li>1. inconsistent options page form-labels </li>
				<li>2. status text not defaulting to the error value when mystatus.skype.com is off-line </li></ul></li>
		<li>version 1.1: added new text template file</li>
		<li>version 1.0: wrote instructions (quick guide)</li>
		<li>version 0.9: added skype user name</li>
		<li>version 0.8: added customizability for get_skype_status('options')</li>
		<li>version 0.7: redesign admin interface</li>
		<li>version 0.3 - 0.6: added lot's of new settings and template tags</li>
		<li>version 0.2: added function skype_parse_theme() and skype_status_check()</li>
		<li>version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)</li>
	</ul>

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
				<dt>my_status_text</dt><dd>Function text for 'My status'. Replaces {status_txt} in template files.</dd>
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
		
	<h3>Wish List version 2 and beyond</h3>
	<ul>
		<li>Online theme editor</li>
		<li>Create post hook: &lt;!--skypestatus--&gt; </li>
		<li>Create collection default settings at top of skype_status.php and a 'Revert to Skype default' button</li>
		<li>Make multiple Skype ID's with own settings possible</li>
		<li>Internationalization and use XML to get online status values</li>
	</ul>
	</div>
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
	$config = skype_status_get_config();
	if (empty($config['skype_id'])) {
		return "";
	};
	$r = $config;
	// check args against config
	if ( $skype_id != "" ) {
		$r['skype_id'] = $skype_id;
		$r['user_name'] = $user_name;
	}
	if ( $button_theme != "" )
		$r['button_theme'] = $button_theme;
	if ( $use_voicemail != "" )
		$r['use_voicemail'] = $use_voicemail;
	
	// get template file
	$buttondir = dirname(__FILE__)."/templates/";
	$file = $buttondir . $r['button_theme'] . ".html";
	if (!file_exists($file)) {
		$file = $buttondir . $config['button_theme'] . ".html";
	}
	$fc = file_get_contents($file);
	$btn_output = skype_parse_theme($fc, $r);	
	
	print $btn_output;
}

function skype_status_page_header() {
	$config = skype_status_get_config();
	if (empty($config['skype_id'])) {
		return "";
	};
	print '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>';
}

add_action('wp_head', 'skype_status_page_header');
add_action('admin_menu', 'skype_status_add_option');
?>
