<?php
function skype_status_widget ($args) {
	$opt = get_option('skype_widget_options');
	extract($args);
	extract($opt);

	if (!$title)
		$title = "Skype Online Status";

	echo $before_widget;
	echo $before_title . $title . $after_title;
	echo "<div class=\"skype-status\">";
	echo stripslashes($before);
	echo skype_status($skype_id,$user_name,"",$use_voicemail,$button_template);
	echo stripslashes($after);
	echo "</div>";
	echo $after_widget;
}

function skype_widget_options () {
	$opt = get_option ('skype_widget_options');
	if (!is_array ($opt)) {
		$opt = array (
			"skype_id" => "",		// Skype ID to replace {skypeid} in template files
			"user_name" => "",		// User name to replace {username} in template files
			"button_theme" => "",		// Theme to be used, value must match a filename (without extention) from the /plugins/skype_status/templates/ directory or leave blank
			"button_template" => "",	// Template of the theme loaded
			"use_voicemail" => "",		// Wether to use the voicemail invitation ("on") or not (""), set to "on" if you have a SkypeIn account
			"before" => "",			// text that should go before the button
			"after" => "",			// text that should go after the button
		);
	}

	// get list of templates
	$buttondir = dirname(__FILE__)."/templates/";
	$select = "<option value=\"\"";
	if ($opt['button_theme'] == "") 
		$select .= " selected=\"selected\"";
	$select .= ">Default</option>";
	if (is_dir($buttondir)) {
		if ($dh = opendir($buttondir)) {
			while (($file = readdir($dh)) !== false) {
				$fname = $buttondir . $file;
				if (is_file($fname) && ".html" == substr($fname,-5)) {

					$theme_name = substr(basename($fname),0,-5);

					$selected = ""; // radio button not selected unless...
					if ($theme_name == $opt['button_theme'])
						$selected = " selected=\"selected\"";

					// attempt to get the human readable name from the first line of the file
					preg_match("|<!-- (.*) - http://www.skype.com/go/skypebuttons|ms",file_get_contents($fname),$matches);
					if (!$matches[1] || $matches[1]=="")
						$matches[1] = $theme_name;

					// collect the options
					$select .= "\n<option value=\"$theme_name\"$selected>$matches[1]</option>";						
				}
			}
			closedir($dh);
		}
	}

	if ($_POST['skype_widget_submit']) {
		if ($_POST['skype_widget_button_theme']!="") { // get template file content to load into db
			$opt['button_template'] = stripslashes( skype_get_template_file($_POST['skype_widget_button_theme']) );
		} else { $opt['button_template'] = ""; }

		$opt['title'] = $_POST['skype_widget_title'];
		$opt['skype_id'] = $_POST['skype_widget_skype_id'];
		$opt['user_name'] = $_POST['skype_widget_user_name'];
		$opt['button_theme'] = $_POST['skype_widget_button_theme'];
		$opt['use_voicemail'] = $_POST['skype_widget_use_voicemail'];
		$opt['before'] = $_POST['skype_widget_before'];
		$opt['after'] = $_POST['skype_widget_after'];

		update_option('skype_widget_options', $opt);
	} ?>

<p style="text-align:left">
<label for="skype_widget_title">Widget Title:</label><br />
<input style="width:100%" type="text" id="skype_widget_title" name="skype_widget_title" value="<?php echo stripslashes(htmlspecialchars($opt['title'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_skype_id">Skype ID*:</label><br />
<input style="width:100%" type="text" id="skype_widget_skype_id" name="skype_widget_skype_id" value="<?php echo stripslashes(htmlspecialchars($opt['skype_id'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_user_name">Username*:</label><br />
<input style="width:100%" type="text" id="skype_widget_user_name" name="skype_widget_user_name" value="<?php echo stripslashes(htmlspecialchars($opt['user_name'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_before">Text before (use &lt;br /&gt; for new line):</label><br />
<input style="width:100%" type="text" id="skype_widget_before" name="skype_widget_before" value="<?php echo stripslashes(htmlspecialchars($opt['before'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_button_theme">Theme:</label> <select name="skype_widget_button_theme" id="skype_widget_button_theme" style="width:100%"><?php echo $select; ?></select>
</p>
<p style="text-align:left">
<label for="skype_widget_use_voicemail">Use Voicemail**:</label> <select name="skype_widget_use_voicemail" id="skype_widget_use_voicemail">
<option value=""<?php if ($opt['use_voicemail'] == "") { print " selected=\"selected\""; } ?>>Default</option>
<option value="on"<?php if ($opt['use_voicemail'] == "on") { print " selected=\"selected\""; } ?>>Always on</option>
<option value="off"<?php if ($opt['use_voicemail'] == "off") { print " selected=\"selected\""; } ?>>Always off</option></select>
</label> 
</p>
<p style="text-align:left">
<label for="skype_widget_after">Text after (use &lt;br /&gt; for new line):</label><br />
<input style="width:100%" type="text" id="skype_widget_after" name="skype_widget_after" value="<?php echo stripslashes(htmlspecialchars($opt['after'])); ?>" />
</p>
<p style="font-size:78%;font-weight:lighter;">* Leave blank to use default options as defined on the Skype Status Options page.<br />
** Leave to <em>Always off</em> if you do not have a SkypeIn account or SkypeVoicemail.</p>
<input type="hidden" id="skype_widget_submit" name="skype_widget_submit" value="1" />

<?php 
}


function skype_add_widget () {
	if (function_exists ('register_sidebar_widget')) {
		register_sidebar_widget ('Skype Status','skype_status_widget');
		register_widget_control ('Skype Status','skype_widget_options');
	}
}
?>