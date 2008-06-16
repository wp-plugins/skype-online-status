<?php
function skype_status_options() {
	global $skype_status_config, $skype_avail_languages;
	$option = $skype_status_config;

	// check if database has been cleared for removal or else updated after plugin upgrade 
	if (!empty($_POST['skype_status_remove'])) { // hit remove button
		delete_option('skype_status');
		delete_option('skype_widget_options');
		echo "<div class=\"updated fade\"><p><strong>Your Skype Online Status database settings have been cleared from the database for removal of this plugin!</strong><br />You can still resave the old settings shown below to (partly) undo this action but custom widget settings will be reverted to default.<br /><br />If you are sure, you can now <a href=\"plugins.php\">disable this plugin</a> and remove it's files. Please keep in mind that any template file changes you have made, can not be undone through this process. Also, any post quicktags that have been inserted in posts will (harmlessly) remain there. If you change your mind about removing this plugin, just resave the settings NOW (or all your settings will be lost) or revert to default settings at the bottom of this page.</p></div>";
	} elseif ($skype_status_config['upgraded'] == TRUE) {
		$skype_status_config['upgraded'] = FALSE;
		update_option('skype_status',$skype_status_config);
		echo "<div class=\"updated fade\"><p><strong>Skype Online Status plugin has been upgraded to version ".SOSVERSION."!</strong> Please verify your settings now.</p></div>";
	} elseif ($skype_status_config['installed'] == TRUE) {
		$skype_status_config['installed'] = FALSE;
		update_option('skype_status',$skype_status_config);
		echo "<div class=\"updated fade\"><p><strong>Skype Online Status plugin version ".SOSVERSION." has been installed!</strong> Please adapt the default settings to your personal preference so you can start using Skype buttons anywhere on your site. Read the <strong>Quick Guide</strong> section for more instructions.</p></div>";
	}

	// check for new version
	do_action('load-plugins.php');
	$current = get_option('update_plugins');
	if ( isset( $current->response['skype-status/skype-status.php'] ) ) {
		$r = $current->response['skype-status/skype-status.php'];
		echo "<div class=\"updated fade-ff0000\"><p><strong>";
		if ( !current_user_can('edit_plugins') )
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a>.'), "Skype Online Status", $r->url, $r->new_version);
		else if ( empty($r->package) )
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> <em>automatic upgrade unavailable for this plugin</em>.'), "Skype Online Status", $r->url, $r->new_version);
		else
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> or <a href="%4$s">upgrade automatically</a>.'), "Skype Online Status", $r->url, $r->new_version, wp_nonce_url("update.php?action=upgrade-plugin&amp;plugin=$file", 'upgrade-plugin_' . $file) );
		echo "</strong></p></div>";
	}

	// warning about furl
	if (!SOSALLOWURLFOPEN) { 
		echo "<div class=\"updated fade\"><p><strong>We have detected that your server settings might prevent this plugin from reading your online status correctly! Please check if your server INI settings allow_url_fopen is set to ON or ask your server admin / hosting provider to take care of this.</strong></p></div>";
	}

	// update the options if form is saved
	if (!empty($_POST['skype_status_update'])) { // pressed udate button
		if (skype_status_valid_id($_POST['skype_id']) &&
			skype_status_valid_theme($_POST['button_theme'])) {
			
			if ($_POST['button_theme']!="custom_edit") { // get template file content to load into db
				$_POST['button_template'] = skype_get_template_file($_POST['button_theme']);
			}
			
			$option = array(
				"skype_id" => $_POST['skype_id'],
				"user_name" => $_POST['user_name'],
				"button_theme" => $_POST['button_theme'],
				"button_template" => stripslashes($_POST['button_template']),
				"use_function" => $_POST['use_function'],
				"use_status" => $_POST['use_status'],
				"use_voicemail" => $_POST['use_voicemail'],
				"use_buttonsnap" => $_POST['use_buttonsnap'],
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
				"use_getskype" => $_POST['use_getskype'],
				"getskype_newline" => $_POST['getskype_newline'],
				"getskype_text" => $_POST['getskype_text'],
				"getskype_link" => $_POST['getskype_link'],
				"getskype_custom_link" => $_POST['getskype_custom_link'],
			);
			$option = array_merge ($skype_status_config, $option);
			update_option("skype_status",$option);
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>Options updated!</strong></p></div>";
		}
	} else if (!empty($_POST['skype_status_reset'])) { // pressed reset button
			$option = skype_default_values();
			update_option("skype_status",$option);
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>Options reset!</strong></p></div>";
	}

	?>

	<div id="loading" class="updated fade"><p><strong>Please wait while page has loaded completely.<br /> When the Skype server at http://mystatus.skype.com/ is slow or down, this might take a while...</strong></p></div>

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
			padding: 3px 5px; margin: auto 5px;">Notes &amp; Live Support</a> 
	</div>

	<div id="settings" class="wrap" style="min-height: 800px;">
		<h2>Skype Online Status Settings</h2>
		<p>Define all your <em>default</em> Skype Status settings here. Start simply by setting the basics like <strong>Skype ID</strong>, <strong>Full Name</strong> and the button <strong>Theme</strong> you want to show on your blog. Then activate the Skype Status Widget on your <a href="widgets.php">Widgets</a> page or use the Skype Status quicktag button <img src="<?php echo get_settings('siteurl') . '/wp-content/plugins/skype-online-status/skype_button.gif'; ?>" alt="Skype Online Status" style="vertical-align:text-bottom;" /> in the WYSIWYG editor (TinyMCE) to place the Skype Online Status button in any post or page. Later on, you can fine-tune everything until it fits just perfectly on you pages. Please note: Some basic settings may be overridden by Widget settings or when calling the Skype button with a template function.</p>
		<p>Read more about configuring this plugin and more ways to trigger Skype Online Status buttons on your blog in the <strong>Quick Guide</strong> section. If you have any remaining questions, see the <strong>Notes &amp; Live Support</strong> page to get help.</p>
		
		<p align="right"><a href="#wphead">back to top</a></p>

		<h3>Basic Options</h3>
		<form enctype="multipart/form-data" method="post" action="#">

		<fieldset class="options"><legend>Skype ID</legend>
			<p><label for="skype_id">Your Skype ID:</label> <input type="text" name="skype_id" id="skype_id" value="<?php echo $option['skype_id']; ?>" /><br />Simply enter your own Skype ID but... more then one Skype ID <em>is</em> possible if you want the button to invoke a Skype multichat or conference call (seperate with a semi-colon <strong>;</strong>) <em>and</em> you can also enter phone numbers (a regular phone number or even a SkypeOut number, starting with a <strong>+</strong> followed by country code; note that callers need to have SkypeOut to call) all depending on what you want to achieve!</p>
		</fieldset>

		<fieldset class="options"><legend>User name</legend>
			<p><label for="user_name">Your Skype name:</label> <input type="text" style="width: 250px;" name="user_name" id="user_name" value="<?php echo stripslashes(htmlspecialchars($option['user_name'])); ?>" /></p>
		</fieldset>

		<? // routine to get all the select options and their previews
		$buttondir = dirname(__FILE__)."/templates/";
		$option_preview = $option;
		$previews = "";
		$radio = "<ul style=\"list-style: none;\" id=\"radios\">";
	
		if (is_dir($buttondir)) {
			if ($dh = opendir($buttondir)) {
				while (($file = readdir($dh)) !== false) {
					$fname = $buttondir . $file;
					if (is_file($fname) && ".html" == substr($fname,-5)) {
	
						$theme_name = substr(basename($fname),0,-5);
	
						$selected = ""; // radio button not selected unless...
						$display = " none"; // hide preview layers unless...
						if ($theme_name == $option['button_theme']) {
							$selected = " checked=\"checked\"";
							$display = " block";
						}

						// attempt to get the human readable name from the first line of the file
						$option_preview['button_template'] = file_get_contents($fname);
						preg_match("|<!-- (.*) - http://www.skype.com/go/skypebuttons|ms",$option_preview['button_template'],$matches);
						if (!$matches[1] || $matches[1]=="")
							$matches[1] = $theme_name;

						// collect the options
						$radio .= "\n<li><input type=\"radio\" name=\"button_theme\" id=\"radio_$theme_name\" value=\"$theme_name\"$selected onchange=\"ChangeStyle(this);\" onmouseover=\"PreviewStyle(this);\" onmouseout=\"UnPreviewStyle(this);\" /> <label for=\"radio_$theme_name\">$matches[1]</label></li>";
						
						// and collect their previews
						$previews .= "\n<div id=\"$theme_name\" style=\"display:$display;\">".skype_parse_theme($option_preview)."</div>";
					}
				}
				closedir($dh);
			}
		}

		if ($option['button_theme'] == "custom_edit") {
			$selected = " checked=\"checked\"";
			$display = " block";
		} else {
			$selected = "";
			$display = " none";
		}
		// add custom option and preview
		$radio .= "\n<li><input type=\"radio\" name=\"button_theme\" id=\"radio_custom_edit\" value=\"custom_edit\"$selected onchange=\"ChangeStyle(this);\" onmouseover=\"PreviewStyle(this);\" onmouseout=\"UnPreviewStyle(this);\" /> <label for=\"radio_custom_edit\">Customize current view in the textarea below...</label></li>
		</ul>"; 
		$previews .= "\n<div id=\"custom_edit\" style=\"display:$display;\">".skype_parse_theme($option)."</div>";
		?>

		<script type="text/javascript">
		var visible_preview = "<?php echo $option['button_theme']; ?>";

		function ChangeStyle(el) {
			eval("document.getElementById('" + visible_preview + "').style.display='none'");
			eval("document.getElementById('" + el.value + "').style.display='block'");
			visible_preview = el.value;
		}
		
		function PreviewStyle(elmnt) {
			eval("document.getElementById('" + visible_preview + "').style.display='none'");
			eval("document.getElementById('" + elmnt.value + "').style.display='block'");
		}
		
		function UnPreviewStyle(elmnt) {
			eval("document.getElementById('" + elmnt.value + "').style.display='none'");
			eval("document.getElementById('" + visible_preview + "').style.display='block'");
		}
		</script>

		<fieldset class="options"><legend>Theme</legend>
			<p>Start with <strong>selecting one of the predefined theme templates</strong> below, to load into the database. Hover over the radio buttons to see a preview. You might later select <strong>Customize current view...</strong> to edit the template in the text field below.<br />If you cannot find a suitable theme, check out <a href="http://www.skype.com/share/buttons/wizard.html" target="_blank">http://www.skype.com/share/buttons/wizard.html</a>. Select your options there and copy/paste the output into the textarea below.</p>
			<div class="alternate" style="float: right; height: 210px; width: 210px; border: 1px solid #CCCCCC; padding: 5px 5px 0 5px; margin: 5px 5px 0 0;">
				<strong>Preview theme template</strong>
				<style type="text/css"><!-- .no_underline a { border-bottom:none } --></style>
				<div class="no_underline"><?php echo $previews; ?></div>
			</div>

			<p><?php echo $radio; ?></p>
			<p><label for="button_template">Customize currently loaded template*:</label><br />
			<textarea name="button_template" id="button_template" style="width: 100%; height: 240px;" onkeydown="javascript:document.getElementById('radio_custom_edit').checked=true;document.getElementById(visible_preview).style.display='none';document.getElementById('custom_edit').style.display='block';visible_preview='custom_edit';"><?php echo stripslashes(htmlspecialchars($option['button_template'])); ?></textarea><br />
				* <em>Changes to the template will only be loaded when the option <strong>Custom view</strong> under <strong>Theme</strong> is selected.<br />Available tags: {skypeid}, {username}, {action}, {add}, {call}, {chat}, {userinfo}, {voicemail}, {sendfile}, {status}, {statustxt}, {tag1} and {tag2}.<br />Available markers: &lt;!-- voicemail_start --&gt; and &lt;!-- voicemail_end --&gt;. See Quick Guide for more instructions.</em></p>
		</fieldset>

		<p align="right" style="clear: both;"><a href="#wphead">back to top</a></p>

		<h3>Advanced Options</h3>
		<fieldset class="options"><legend>Display options</legend>
			<p>These settings define which options should be used to replace their respective tag (if present) in the selected template file. If unchecked, the tags will be blanked out.</p> 
			<ul style="list-style: square;">
				<li><input type="checkbox" name="use_voicemail" id="use_voicemail"<?php if ( $option['use_voicemail'] == "on" ) { print " checked=\"checked\""; } ?> /> <label for="use_voicemail">Use <strong>Leave a voicemail</strong> in dropdown button. Leave unchecked if you do not have a SkypeIn account or SkypeVoicemail.</label></li>
				<li><input type="checkbox" name="use_function" id="use_function"<?php if ( $option['use_function'] == "on" ) { print " checked=\"checked\""; } ?> /> <label for="use_function">Use <strong>Action text</strong> (as defined below) for {add/call/chat/userinfo/voicemail/sendfile} tags.</label></li>
				<li><input type="checkbox" name="use_buttonsnap" id="use_buttonsnap"<?php if ( $option['use_buttonsnap'] == "on") { print " checked=\"checked\""; } ?> /> <label for="use_buttonsnap">Use <strong>Skype Status quicktag button</strong> in the RTE for posts.</label></li>
				<li><input type="checkbox" name="use_getskype" id="use_getskype"<?php if ( $option['use_getskype'] == "on") { print " checked=\"checked\""; } ?> /> <label for="use_getskype">Use <strong>Download Skype now!</strong> link. </label><br />
					<label for="getskype_newline">Place link <select name="getskype_newline" id="getskype_newline">
					<option value=""<?php if ( $option['getskype_newline'] == "" ) print " selected=\"selected\""; ?>>after</option>
					<option value="on"<?php if ( $option['getskype_newline'] == "on" ) print " selected=\"selected\""; ?>>below</option>
					</select> each Skype Online Status button</label> and <label for="getskype_text">use link text </label><input name="getskype_text" style="width: 250px;" id="getskype_text" value="<?php echo stripslashes(htmlspecialchars($option['getskype_text'])); ?>" /><br />
					Use <label for="getskype_link">link URL*</label> <select name="getskype_link" id="getskype_link">
					<option value=""<?php if ( $option['getskype_link'] == "" ) print " selected=\"selected\""; ?>>Default Skype link</option>
					<option value="skype_mainpage"<?php if ( $option['getskype_link'] == "skype_mainpage" ) print " selected=\"selected\""; ?>>Skype main page</option>
					<option value="skype_downloadpage"<?php if ( $option['getskype_link'] == "skype_downloadpage" ) print " selected=\"selected\""; ?>>Skype download page</option>
					<option value="custom_link"<?php if ( $option['getskype_link'] == "custom_link" ) print " selected=\"selected\""; ?>>Custom link (below)</option>
					</select><br />
					* <em>Leave to Default if you are generous and think downloads should create some small possible revenue for the developer of this plugin -- that's me, thanks! :) -- but if you think open source developers are greedy bastards and should go away -- just kidding, feel free... really! you can always donate on the Notes & Live Support section ;) --, select one of the other options. If you want to create your own link (say you have a Commission Junction, TradeDoubler or ValueCommerce account, read more on http://www.skype.com/partners/affiliate/) to get possible revenue from downloads yourself, select Custom and paste the link code in the textarea under Custom Download Link Code below.</em></li>
				<li><label for="use_status">Use <strong>Status text</strong> for the {status} tag?*</label> <select name="use_status" id="use_status">
						<option value="custom"<?php if ( $option['use_status'] == "custom" ) print " selected=\"selected\""; ?>>Custom (as defined below)</option>
						<?php foreach ($skype_avail_languages as $key => $value) {
						echo '<option value="'.$value.'"';
						if (!SOSALLOWURLFOPEN) echo ' disabled="disabled"'; 
						elseif ( $option['use_status'] == $value ) echo ' selected="selected"';
						echo '>Skype default in '.$key.'</option>
						'; } 
						unset($value); ?> 
						<option value=""<?php if ( $option['use_status'] == "" ) print " selected=\"selected\""; ?>>No</option>
					</select><br />
					* <em>If you select 'No', the tags {status}, {statustxt} and {sep2} will be disabled.<br />When security settings on your server are too tight (<strong>safe_mode</strong> enabled, <strong>open_basedir</strong> resticted or <strong>allow_url_fopen</strong> disabled) and you encounter an error like 'Warning: file_get_contents() [function.file-get-contents]: URL file-access is disabled in the server configuration...', use either 'Custom' or 'No' here.</em></li>
			</ul>
		</fieldset>

		<div style="float: left; width: 48%;">
			<fieldset class="options"><legend>Tags</legend>
				<p>Define texts to replace their respective template tags relating to the Skype button action.</p> 
				<table>
					<tr>
						<th>Action text - {tag}</th>
						<th>Text</th>
					</tr>
					<tr>
						<td><label for="add_text">Add me to Skype - {add}: </label></td>
						<td><input type="text" name="add_text" id="add_text" value="<?php echo stripslashes(htmlspecialchars($option['add_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="call_text">Call me! - {call}: </label></td>
						<td><input type="text" name="call_text" id="call_text" value="<?php echo stripslashes(htmlspecialchars($option['call_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="chat_text">Chat with me - {chat}: </label></td>
						<td><input type="text" name="chat_text" id="chat_text" value="<?php echo stripslashes(htmlspecialchars($option['chat_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="sendfile_text">Send me a file - {sendfile}: </label></td>
						<td><input type="text" name="sendfile_text" id="sendfile_text" value="<?php echo stripslashes(htmlspecialchars($option['sendfile_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="userinfo_text">View my profile - {userinfo}: </label></td>
						<td><input type="text" name="userinfo_text" id="userinfo_text" value="<?php echo stripslashes(htmlspecialchars($option['userinfo_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="voicemail_text">Leave me voicemail - {voicemail}: </label></td>
						<td><input type="text" name="voicemail_text" id="voicemail_text" value="<?php echo stripslashes(htmlspecialchars($option['voicemail_text'])); ?>" /></td>
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
						<td><input type="text" name="my_status_text" id="my_status_text" value="<?php echo stripslashes(htmlspecialchars($option['my_status_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="seperator1_text">First seperator - {sep1}: </label></td>
						<td><input name="seperator1_text" id="seperator1_text" value="<?php echo stripslashes(htmlspecialchars($option['seperator1_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="seperator2_text">Second seperator - {sep2}: </label></td>
						<td><input name="seperator2_text" id="seperator2_text" value="<?php echo stripslashes(htmlspecialchars($option['seperator2_text'])); ?>" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="options"><legend>Custom Download Link</legend>				<table>
				<table>
					<tr>
						<th>Description</th>
						<th>Link/Banner Code</th>
					</tr>
					<tr>
						<td><label for="getskype_custom_link">If you have a Commission Junction, TradeDoubler or ValueCommerce account (read more on http://www.skype.com/partners/affiliate/) and you want to get possible revenue from Skype downloads, select 'Custom link' at the Display Options (above) and paste your link/banner code (HTML/Javascript) here.</label></td>
						<td><textarea name="getskype_custom_link" id="getskype_custom_link" style="width:100%;height:100px;"><?php echo stripslashes(htmlspecialchars($option['getskype_custom_link'])); ?></textarea></td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div style="float: right; width: 48%;">
			<fieldset class="options"><legend>Custom Status texts</legend>
				<p>Text that will replace the {status} template tag depending on actual online status when you select 'Use <strong>Status text</strong>' to 'Custom'. Note: If the security settings on your server are too tight, the status cannot be read from the Skype server and only the Error value will be used.</p>
				<table>
					<tr>
						<th>Status (value)</th>
						<th>Text</th>
					</tr>
					<tr>
						<td><label for="status_error_text">Error (none): </label></td>
						<td><input type="text" name="status_error_text" id="status_error_text" value="<?php echo stripslashes(htmlspecialchars($option['status_error_text'])); ?>" /></td>
					</tr>
					<tr>
						<td><label for="status_0_text">Unknown (0): </label></td>
						<td><input type="text" name="status_0_text" id="status_0_text" value="<?php echo stripslashes(htmlspecialchars($option['status_0_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
					<tr>
						<td><label for="status_1_text">Offline (1): </label></td>
						<td><input type="text" name="status_1_text" id="status_1_text" value="<?php echo stripslashes(htmlspecialchars($option['status_1_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
					<tr>
						<td><label for="status_2_text">Online (2): </label></td>
						<td><input type="text" name="status_2_text" id="status_2_text" value="<?php echo stripslashes(htmlspecialchars($option['status_2_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
					<tr>
						<td><label for="status_3_text">Away (3): </label></td>
						<td><input type="text" name="status_3_text" id="status_3_text" value="<?php echo stripslashes(htmlspecialchars($option['status_3_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
					<tr>
						<td><label for="status_4_text">Not available (4): </label></td>
						<td><input type="text" name="status_4_text" id="status_4_text" value="<?php echo stripslashes(htmlspecialchars($option['status_4_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
					<tr>
						<td><label for="status_5_text">Do not disturb (5): </label></td>
						<td><input type="text" name="status_5_text" id="status_5_text" value="<?php echo stripslashes(htmlspecialchars($option['status_5_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
					<tr>
						<td><label for="status_6_text">Invisible (6): </label></td>
						<td><input type="text" name="status_6_text" id="status_6_text" value="<?php echo stripslashes(htmlspecialchars($option['status_6_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
					<tr>
						<td><label for="status_7_text">Skype me (7): </label></td>
						<td><input type="text" name="status_7_text" id="status_7_text" value="<?php echo stripslashes(htmlspecialchars($option['status_7_text'])); ?>" <?php if (!SOSALLOWURLFOPEN) echo "readonly=\"readonly\" style=\"color:grey;\""; ?>/></td>
					</tr>
				</table>
			</fieldset>
		</div>

		<p align="right" style="clear: both;"><a href="#wphead">back to top</a></p>

		<p class="submit">
			<input type="submit" name="skype_status_update" value="<?php _e('Update Options'); ?> &raquo;" />
			<input type="submit" class="delete" id="deletepost" onclick='return confirm("All your personal settings will be overwritten with the plugin default settings, including Skype ID, User name and Theme. \r\n \r\nDo you really want to reset your configuration?");' name="skype_status_reset" value="<?php _e('Reset'); ?> &raquo;" /> 
			<input type="submit" class="delete" id="deletepost" onclick='return confirm("All your Skype Online Status and widget settings will be cleared from the database so the plugin can be COMPLETELY removed. \r\n \r\nDo you really want to CLEAR your configuration?");' name="skype_status_remove" value="<?php _e('Remove'); ?> &raquo;" />
		<br />&nbsp;</p>
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
		<p>Define all Skype settings such as Skype ID (more then one possible, seperate with a semi-colon <strong>;</strong>), User name and preferred Theme on the Skype Online Status Settings page as default for each Skype Online Status Button on your blog. And use the methodes described below to trigger the default Skype Status button on your blog pages. Under 'Advanced' you can read about ways to override your default settings and create multiple and different Skype buttons across your blog.</p>
		<p>If you want to use templates that display your online status, be sure to enable online status in your Skype settings: open your Skype client, Go to Tools > Options > Privacy, Tick the 'Allow my status to be shown on the web' (or similar in your language) checkbox and 'Save'.</p>
		<p>For conference calls put multiple Skype ID's seperated with a semi-colon (;) in the Skype ID box.</p>
		<h4>Widgets</h4>
		<p>Since version 2.6.1.0 there is a Skype Status Sidebar Widget available. Go to your Design > Widgets page and activate the Skype Status widget. When activated, it defaults to your settings on the Skype Status Options page but you can customize it if you like.</p>
		<h4>In posts and page content</h4>
		<p>It is also possible to trigger a Skype Status button (as predefined on the Skype Online Status Settings page) within posts or page content. Use the quicktag button <img src="<?php echo get_settings('siteurl') . '/wp-content/plugins/skype-online-status/skype_button.gif'; ?>" alt="Skype Online Status" style="vertical-align:text-bottom;" /> or insert manually <strong>&lt;!--skype status--&gt;</strong> ( or <strong>[-skype status-]</strong> ) in the HTML code of your post or page content to display a Skype Online Status button in your post. </p>
		<p>Note: the setting 'Use Skype Status quicktag button' should be checked for the quicktag button <img src="<?php echo get_settings('siteurl') . '/wp-content/plugins/skype-online-status/skype_button.gif'; ?>" alt="Skype Online Status" style="vertical-align:text-bottom;" /> to appear in WordPress's Rich Text Editor (TinyMCE) so you can easily drop the quicktag into the source code.</p>
		<h4>In theme files</h4>
		<p>Put <strong>&lt;?php if (function_exists(get_skype_status)) { get_skype_status(''); } else { echo "Skype button disabled"; } ?&gt;</strong> in your sidebar.php or other WordPress template files to display a Skype Button with Online Status information on your blog pages. Your predefined default settings (above) will be used.</p><p>The 'function_exists'-check is there to prevent an error when the plugin is disabled. In this case the echo text is displayed. You can define another alternative action or remove 'else { ... }' to display nothing at all.</p>

		<p id="adv" align="right"><a href="#wphead">back to top</a></p>
		<h3>Advanced</h3>
		<p>It is also possible to use multiple Skype buttons for different Skype Users and with different themes across your blog pages.</p>
		<h4>Syntax</h4>
		<p>Use the syntax <strong>get_skype_status('parameter1=something&parameter2=something_else');</strong> to get a button that looks different from the predefined settings on the Skype Online Status Settings page, or even using another Skype ID.</p>
		<h4>Parameters</h4>
			<dl><dt>skype_id</dt><dd>Alternative Skype ID</dd>
				<dt>user_name</dt><dd>Define the Skype user or screen name.</dd>
				<dt>button_theme</dt><dd>Define the theme template file to use for the button. Value must match a filename (without extention) from the /plugins/skype-online-status/templates/ directory or the predefined theme template will be used.</dd>
				<dt>use_voicemail</dt><dd>Set to 'on' if you want to display the 'Leave a voicemail' link in the Dropdown themes. Use this only if you have a SkypeIn account or SkypeVoicemail. Set of 'off' if you have a predefined setting 'on' and you want to override it.</dd>
			</dl>
		<h4>Example</h4>
		<p>The php-code <strong>get_skype_status('skype_id=echo123&amp;user_name=Skype voice test&amp;button_theme=callme_mini')</strong> will generate a Skype button with all your predefined settings <em><strong>except</strong></em> for Skype user 'echo123' (the Skype voice test user) with the screen name 'Skype voice test' and using template file 'callme_mini.html':</p>
		<p><?php get_skype_status('skype_id=echo123&user_name=Skype voice test&button_theme=callme_mini'); ?></p>

		<p id="templ" align="right"><a href="#wphead">back to top</a></p>
		<h3>Templates</h3>
		<p>Whenever the options on the Skype Status Options page are saved, the template is read either from the selected template file or the editable textarea (customizable view) and loaded into the database. To change the Skype Online Status button view to your liking you can choose to edit an existing template file, create a new one or edit the preloaded template in the editable textarea on the 'Skype Online Status Settings' page. Remember that after editing a template file, the new content must be reloaded into the database before changes apply.</p>
		<p>All template files can be found in the /plugins/skype-online-status/templates/ directory. You add new or edit existing ones with any simple text editor (like Notepad) or even a WYSIWYG editor (like Dreamweaver) as long as you follow some rules.</p>
		<h4>Template file rules</h4>
		<ol>
			<li>All template files must have a name consisting of only <strong>lowercase letters</strong>, <strong>numbers</strong> and/or <strong>underscores (_)</strong> or <strong>dashes (-)</strong>. Please avoid any other signs, dots or whitespaces. Do not use the name <strong>custom_edit</strong> as is reserved for the customizable view.</li>
			<li>The template files must reside in the <strong>/templates/</strong> subdirectory of this plugin directory.</li>
			<li>The template files must have the <strong>.html</strong> extention. All other extentions in the templates directory will be ignored.</li>
			<li>The first line of any <strong>file</strong> must be something like: <br />
				<strong>&lt;!-- 'Template Name' style description - http://www.skype.com/go/skypebuttons --&gt;</strong><br />
				where the <em>'Template Name' style description</em> part will represent the template name on the Skype Online Status Settings page. Choose a recognizable name and a very short description.</li>
		</ol>
		<h4>Template rules</h4>
		<ol>
			<li>Within each template (file or customizable view) certain tags like <strong>{skypeid}</strong> are used that will be replaced according to their respective configuration on the Skype Status Settings page. See 'Template tags' below for all available tags.</li>
			<li>Everything within the template between <strong>&lt;!-- voicemail_start --&gt;</strong> and <strong>&lt;!-- voicemail_end --&gt;</strong> will be erased when the option 'Use <strong>Leave a voicemail</strong>' on the Skype Online Status Settings page is NOT checked.</li>
		</ol>
		<p>For the rest you are free to put anything you like in the template files.<br />
		To get started see <a href="http://www.skype.com/go/skypebuttons">http://www.skype.com/go/skypebuttons</a> for an interactive form to create new Skype Button code.</p> 
		<h4>Template tags</h4>
		<p>The following tags are available:</p>
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

		<h2>Notes &amp; Live Support</h2>
		<ul>
			<li><a href="#prl">Version, Support, Pricing and Licensing</a></li>
			<li><a href="#live">Live support</a></li>
			<li><a href="#credits">Credits</a></li>
			<li><a href="#revhist">Revision History</a></li>
			<li><a href="#todo">Todo</a></li>
		</ul>

		<p id="prl" align="right"><a href="#wphead">back to top</a></p>
		<h3>Version, Support, Pricing and Licensing</h3>
		<p>This is <strong>version <?php echo SOSVERSION; ?></strong> of the Skype Online Status plugin for WordPress 2+.<br />
			Release date: <?php echo SOSVERSION_DATE; ?>. <br />
			The latest available release is: <strong>version <?php if(isset($r->new_version)) { echo $r->new_version . " <span class=\"updated fade\">PLEASE <a href=\"" . $r->url . "\">UPDATE</a> BEFORE REPORTING BUGS !</span>"; } else { echo SOSVERSION; } ?></strong></p>
			Report bugs, feature requests and user experiences on <a href="http://groups.google.com/group/wp-skype-online-status">Skype Online Status - Google Discussion Group</a>. <br />
		<p>This plugin is in beta testing stage and is released under the <a href="http://www.gnu.org/licenses/gpl.txt">GNU General Public License</a>. You can use it free of charge but at your own risk on your personal or commercial blog.</p>
		<p>If you enjoy this plugin, you can thank me by way of a small donation for my efforts and the time I spend maintaining and developing this plugin and giving <a href="#live">live user support</a> in dutch, english and even a little french and german :).</p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<fieldset><legend>Donate with PayPal</legend>
			<label for="currency_code">Currency:</label> 
				<select name="currency_code" id="currency_code"><option value="EUR">&euro;</option>
				<option value="USD">$</option></select><br /> 
				<label for="amount">Amount:</label> <select name="amount" id="amount"><option value="2.00">2</option>
				<option value="5.00">5</option>
				<option  value="10.00">10</option>
				<option  value="">other</option></select><br /> 
				<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" style="border:none; vertical-align:text-bottom;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"/></fieldset>
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="ravanhagen@gmail.com">
				<input type="hidden" name="item_name" value="Skype Online Status donation"/>
				<input type="hidden" name="item_number" value="<?php echo SOSVERSION; ?>">
				<input type="hidden" name="no_shipping" value="1"/>
				<input type="hidden" name="return" value="http://www.4visions.nl/en/index.php?section=56">
				<input type="hidden" name="cancel_return" value="http://www.4visions.nl/en/index.php?section=55">
				<input type="hidden" name="cn" value="Remarks and suggestions">
				<input type="hidden" name="tax" value="0"/>
				<input type="hidden" name="bn" value="PP-DonationsBF">
				<img alt="" border="0" src="https://www.paypal.com/nl_NL/i/scr/pixel.gif" width="1" height="1" style="border:none; vertical-align:text-bottom;">
		</form>
		<p>I appreciate every contribution, no matter if it&#8217;s two or twenty euro/dollar or any other amount.</p>
		<p>Thanks,<br />
			<em>Ravan</em></p>
	
		<p id="live" align="right"><a href="#wphead">back to top</a></p>

		<h3>Live Support</h3>
		<p>To get live support on this plugin with Skype, simply use the link below. It will state wether I'm online and available for chat with Skype.</p>
		<p>
			Status <?php get_skype_status('skype_id=ravanhagen&user_name=Live Support&button_theme=status_plaintext'); ?><br /><br />
			To Skype-chat with Ravan: <a href="skype:ravanhagen?chat" onclick="return skypeCheck();" title="Live chat">Live chat</a></p>

		<p id="credits" align="right"><a href="#wphead">back to top</a></p>
		
		<h3>Credits</h3>
		<p>This plugin was built by <em>Ravan</em>. It is based upon the neat little plugin <a href="http://anti.masendav.com/skype-button-for-wordpress/">Skype Button v2.01</a> by <em>Anti Veeranna</em>. The plugin makes use of Owen's excellent <a href="http://redalt.com/wiki/ButtonSnap">ButtonSnap library</a>. Many thanks!</p>

		<p id="revhist" align="right"><a href="#wphead">back to top</a></p>
		<h3>Revision History</h3>
		<ul>
			<li>[2008-06-16] version 2.6.1.1: automatic blog language detection for status text, some small bugfixes + complete removal button</li>
			<li>[2008-06-04] version 2.6.1.0: added simple widget and adapted update checker to fit WP2.5 auto-update, some small bugfixes and code improvements, plus 'add your own download link' feature</li>
			<li>[2007-04-09] version 2.6.0.9: improved reg_exp for quicktag replacement, minor changes in available settings (newline for download link optional) and fixed &-sign in fields causing failed w3c validation</li>
			<li>[2007-02-18] version 2.5: made quicktag work for 2.1+ new TinyMCE button plugin routine</li>
			<li>[2006-11-21] version 2.4.1: added onkeydown action on admin textarea</li>
			<li>[2006-11-03] version 2.4: added backwards compatibility with PHP versions previous to 4.3 ( fallback to file() instead of file_get_contents() ) and a check for allow_url_fopen before remote file reading (used in status check and upgrade check) with dynamic options change</li>
			<li>[2006-09-25] version 2.3: added Download Skype now! link (with option to change text or disable) and local upgrade check</li>
			<li>[2006-09-20] version 2.2.2: moved buttonsnap.php, changes to Quick Guide and Live Support</li>
			<li>[2006-09-04] version 2.2.1: minor changes to admin page</li>
			<li>[2006-07-28] version 2.2.0: make use of global string improving speed</li>
			<li>[2006-07-05] version 2.1.0: added Skype default status texts in different languages</li>
			<li>[2006-07-04] version 2.0.1: minor bugfix (altered defaulting to fallback template procedure)</li>
			<li>[2006-06-30] version 2.0: added editable template and live support link</li>
			<li>[2006-06-29] version 1.9: added RTE button for &lt;!--skype status--&gt; hook</li>
			<li>[2006-06-27] version 1.8: improved performance by loading template in database</li>
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
			<li>Make multiple Skype ID's (within the Skype Status Settings) possible</li>
			<li>Internationalization...</li>
			<li>Skype-like wizard...</li>
			<li>Widget compliance</li>
			<li>Add Skypecasts widget</li>
			<li>Upload your own button</li>
		</ul>
		<p align="right"><a href="#wphead">back to top</a></p>
	</div>
	
	<?php
	if (SOSDATADUMP) { 
		echo "<div id=\"dump\" class=\"wrap\"><h3>All Skype Online Status settings</h3>
		<div style=\"width:32%;float:left\"><h4>Old database values</h4><textarea readonly=\"readonly\" style=\"width:100%;height:600px\">";
		foreach ($skype_status_config as $key => $value) {
			echo $key . " => " . stripslashes(htmlspecialchars($value)) . "\r\n";
		}
		unset($value);
		echo "</textarea></div>
		<div style=\"width:32%;margin:0 2%;float:left\"><h4>Updated to</h4><textarea readonly=\"readonly\" style=\"width:100%;height:600px\">";
		if (!empty($_POST['skype_status_update']) || !empty($_POST['skype_status_reset'])) { 
			foreach ($option as $key => $value) {
				echo $key . " => " . stripslashes(htmlspecialchars($value)) . "\r\n";
			}
			unset($value);
		}
		echo "</textarea></div>
		<div style=\"width:32%;float:left\"><h4>Default values</h4><textarea readonly=\"readonly\" style=\"width:100%;height:600px\">";
		$skype_default_values = skype_default_values();
		foreach ($skype_default_values as $key => $value) {
			echo $key . " => " . stripslashes(htmlspecialchars($value)) . "\r\n";
		}
		unset($value);
		echo "</textarea></div><div style=\"clear:both\"></div>
		<div><h4>Pluging global values</h4> 
		<p>SOSDATADUMP=".SOSDATADUMP." (obviously ;-) )<br />SOSBUTTONSNAP=".SOSBUTTONSNAP."<br />SOSVERSION=".SOSVERSION."<br />SOSVERSION_DATE=".SOSVERSION_DATE."<br />SOSALLOWURLFOPEN=".SOSALLOWURLFOPEN."<br />SOSREMOVEFLAG=".SOSREMOVEFLAG."</p>
		</div></div>";	
	}
	?>

	<script type="text/javascript">
		document.getElementById('loading').style.display='none';
		document.getElementById('notes').style.display='none'; 
		document.getElementById('guide').style.display='none'; 
		document.getElementById('settings').style.display='block';
	</script>
	<?php
}
?>