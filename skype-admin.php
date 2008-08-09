<?php
function skype_status_options() {
	global $skype_status_config, $skype_avail_languages, $skype_avail_functions, $skype_avail_statusmsg, $wp_db_version, $soswhatsnew_this, $soswhatsnew_recent;
	$option = $skype_status_config;
	$plugin_file = "skype-online-status/skype-status.php";

	// check if database has been cleared for removal or else updated after plugin upgrade 
	if (!empty($_POST['skype_status_remove'])) { // hit remove button
		delete_option('skype_status');
		delete_option('skype_widget_options');
		echo "<div class=\"updated fade\"><p><strong>".__('Your Skype Online Status database settings have been cleared from the database for removal of this plugin!', 'skype-online-status')."</strong><br />".__('You can still resave the old settings shown below to (partly) undo this action but custom widget settings will be reverted to default.', 'skype-online-status')."<br /><br />".__('Are you sure?', 'skype-online-status')." ";
		if (function_exists('wp_nonce_url')) 
			echo "<a href=\"" . wp_nonce_url('plugins.php?action=deactivate&amp;plugin='.$plugin_file, 'deactivate-plugin_'.$plugin_file) . "\" title=\"" . __('Deactivate this plugin') . "\" class=\"delete\">" . __('Deactivate') . "</a>.";
		else
			_e('Go to the <a href="plugins.php">Plugins page</a> and deactivate it.', 'skype-online-status');
		echo "<br /><br />".__('Please, keep in mind that any WP theme template file changes you have made, can not be undone through this process. Also, any post quicktags that have been inserted in posts will (harmlessly) remain there. If you changed your mind about removing this plugin, just resave the settings NOW (or all your settings will be lost) or revert to default settings at the bottom of this page.', 'skype-online-status')."</p></div>";
	} elseif ($skype_status_config['upgraded'] == TRUE) {
		$skype_status_config['upgraded'] = FALSE;
		update_option('skype_status',$skype_status_config);
		echo "<div class=\"updated fade\"><p><strong>".__('Skype Online Status plugin has been upgraded to version ', 'skype-online-status').SOSVERSION."</strong><br />".__('Please, verify your settings now.', 'skype-online-status')."</p><p><strong>".__('What\'s new?', 'skype-online-status')."</strong><p><em>".__('Latest:', 'skype-online-status')."</em><br />".$soswhatsnew_this."</p><p><em>".__('Recent:', 'skype-online-status')."</em><br />".$soswhatsnew_recent."</p></div>";
	} elseif ($skype_status_config['installed'] == TRUE) {
		$skype_status_config['installed'] = FALSE;
		update_option('skype_status',$skype_status_config);
		echo "<div class=\"updated fade\"><p><strong>";
		printf(__('Skype Online Status plugin version %s has been installed!','skype_status'),SOSVERSION);
		echo "</strong> ".__('Please, adapt the default settings to your personal preference so you can start using Skype buttons anywhere on your site.','skype_status');
		printf(__('Read the %s section for more instructions.','skype_status'),"<strong>".__('Quick Guide','skype_status')."</strong>");
		echo "</p></div>";
	}

	// check for new version
	do_action('load-plugins.php');
	$current = get_option('update_plugins');
	if ( isset( $current->response[$plugin_file] ) ) {
		$r = $current->response[$plugin_file];
		echo "<div class=\"updated fade-ff0000\"><p><strong>";
		if ( !current_user_can('edit_plugins') )
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a>.'), __('Skype Online Status', 'skype-online-status'), $r->url, $r->new_version);
		else if ( empty($r->package) )
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> <em>automatic upgrade unavailable for this plugin</em>.'), __('Skype Online Status', 'skype-online-status'), $r->url, $r->new_version);
		else
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> or <a href="%4$s">upgrade automatically</a>.'), __('Skype Online Status', 'skype-online-status'), $r->url, $r->new_version, wp_nonce_url("update.php?action=upgrade-plugin&amp;plugin=$plugin_file", 'upgrade-plugin_' . $plugin_file) );
		echo "</strong></p></div>";
	}

	// warning about furl 
	if (!ini_get('allow_url_fopen')) { 
		echo "<div class=\"updated fade\"><p>".__('Your server settings prevent this plugin from reading your online status from the remote Skype server.').__('Please, check if your server INI settings <strong>allow_url_fopen</strong> is set to ON or ask your server admin / hosting provider to take care of this.<br /><br />With <strong>allow_url_fopen</strong> OFF, the plugin will function like normal except for some of the advanced features concerning online status messages. Skype button themes that also show your online status by way of images (that are provided by the same Skype server) will still do so.', 'skype-online-status')."</p></div>";
	} else if (!SOSALLOWURLFOPEN) {  
		echo "<div class=\"updated fade\"><p>".__('Reading your online status from the remote Skype server has been disabled (SOSALLOWURLFOPEN set to FALSE in skype-status.php). As a result, some of the advanced options will not be available.')."</p></div>";
	}

	// update the options if form is saved
	if (!empty($_POST['skype_status_update'])) { // pressed udate button
		if (skype_status_valid_id($_POST['skype_id']) && skype_status_valid_theme($_POST['button_theme'])) {
			if ($_POST['button_theme']!="custom_edit") // get template file content to load into db
				$_POST['button_template'] = skype_get_template_file($_POST['button_theme']);
			$option = array(
				"skype_id" => $_POST['skype_id'],
				"user_name" => $_POST['user_name'],
				"button_theme" => $_POST['button_theme'],
				"button_template" => stripslashes($_POST['button_template']),
				"button_function" => $_POST['button_function'],
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
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>".__('Options updated!', 'skype-online-status')."</strong></p></div>";
		}
	} else if (!empty($_POST['skype_status_reset'])) { // pressed reset button
			$option = skype_default_values();
			update_option("skype_status",$option);
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>".__('Options reset!', 'skype-online-status')."</strong></p></div>";
	}

	// get all the selected options and their previews into an array
	$walk = skype_walk_templates(dirname(__FILE__)."/templates/", $option, "", "");

	// build output
	$previews = "";
	foreach ($walk['previews'] as $key => $value) { 
		$previews .= "<div id=\"$value[0]\" style=\"display:"; 
		if ($value[0] == $option['button_theme']) {
			$previews .= "block"; 
			$current_theme_fullname = $key; 
		} else { 
			$previews .= "none";
		}
		$previews .= "\"><div style=\"height:38px;border-bottom:1px dotted grey;margin:0 0 5px 0\">$key</div>$value[1]</div>"; 
	} 
	unset($value);
	?>

	<div id="loading" class="updated fade"><p><strong><?php _e('Please, wait while page has loaded completely.<br /> When the Skype server at http://mystatus.skype.com/ is slow or down, this might take a while...', 'skype-online-status'); ?></strong></p></div>

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

function SwitchInfoBlock(id){
	var blockElementStyle=document.getElementById(id).style;
	if (blockElementStyle.display=="none"){
	   blockElementStyle.display="block";
	}
	else {
	   blockElementStyle.display="none";
	 }
}
</script>

<div class="wrap">
<h2><?php echo __('Skype Online Status', 'skype-online-status')." ".SOSVERSION; ?></h2>
<form enctype="multipart/form-data" method="post" action="#">
<?php wp_nonce_field('update-options'); ?>
<div id="poststuff">

	<div <?php if ( $wp_db_version >= 6846 ) echo "id=\"submitpost\"  class=\"submitbox\""; else echo "id=\"moremeta\" class=\"dbx-group\""; ?>>
		<?php if ( $wp_db_version >= 6846 ) echo "<div id=\"previewview\"><p><strong><?php _e('Sections','skype_status'); ?></strong> <br /> <br />"; else echo "<fieldset class=\"dbx-box\"> <h3 class=\"dbx-handle\">Sections</h3><div class=\"dbx-content\">"; ?>
			<a style="color:#d54e21" id="settingslink" href="#settings" onclick="javascript:
				document.getElementById('notes').style.display='none'; 
				document.getElementById('guide').style.display='none'; 
				document.getElementById('settings').style.display='block'; 
				document.getElementById('settingslink').style.color='#d54e21'; 
				document.getElementById('noteslink').style.color='#264761'; 
				document.getElementById('guidelink').style.color='#264761';"><?php _e('Options'); ?></a> <br /> <br />
			<a id="guidelink" href="#guide" onclick="javascript:
				document.getElementById('notes').style.display='none'; 
				document.getElementById('guide').style.display='block'; 
				document.getElementById('settings').style.display='none';  
				document.getElementById('settingslink').style.color='#264761'; 
				document.getElementById('noteslink').style.color='#264761'; 
				document.getElementById('guidelink').style.color='#d54e21';"><?php _e('Quick Guide','skype_status'); ?></a> <br /> <br />
			<a id="noteslink" href="#notes" onclick="javascript:
				document.getElementById('notes').style.display='block'; 
				document.getElementById('guide').style.display='none'; 
				document.getElementById('settings').style.display='none'; 
				document.getElementById('settingslink').style.color='#264761'; 
				document.getElementById('noteslink').style.color='#d54e21'; 
				document.getElementById('guidelink').style.color='#264761';"><?php _e('Notes &amp; Live Support','skype_status'); ?></a></p>
		<?php if ( $wp_db_version >= 6846 )
			echo "</div>

		<div id=\"resources\" class=\"side-info\"><h5>"; 
		else
			echo "</div></fieldset>

		<fieldset class=\"dbx-box\"><h3 class=\"dbx-handle\">";
		_e('Resources','skype_status');
		if ( $wp_db_version >= 6846 )
			echo "</h5>";
		else
			echo "</h3><div class=\"dbx-content\">"; ?>
			<ul style="padding-left:12px">
				<li><a href="http://www.skype.com/go/skypebuttons"><?php _e('Skype Buttons','skype_status'); ?></a></li>
				<li><a href="http://www.skype.com/share/buttons/wizard.html" target="_blank"><?php _e('Skype buttons wizard','skype_status'); ?></a></li>
				<li><a href="http://mystatus.skype.com/<?php echo $option['skype_id']; ?>"><?php printf(__('View %s\'s online status on the Skype server','skype_status'),$option['skype_id']); ?></a></li>
				<li><a href="http://www.skype.com/share/buttons/status.html"><?php _e('Edit Privacy Options in your Skype client','skype_status'); ?></a></li>
				<li><a href="http://www.skype.com/partners/affiliate/"><?php _e('Skype Affiliate Program','skype_status'); ?></a></li>
			</ul>
		<?php if ( $wp_db_version >= 6846 ) 
			echo "</div>

		<div id=\"thanks\" class=\"side-info\"><h5>"; 
		else 
			echo "</div></fieldset>

		<fieldset class=\"dbx-box\"><h3 class=\"dbx-handle\">";
		_e('Donations','skype_status');
		if ( $wp_db_version >= 6846 ) 
			echo "</h5>"; 
		else 
			echo "</h3><div class=\"dbx-content\">";
		echo "<p>".__('All donations are much appreciated and will (without objection) be mentioned here as a way of expressing my gratitude.','skype_status')."</p>"; ?>

			<iframe border="0" frameborder="0" scrolling="auto" allowtransparency="yes" style="margin:0;padding:0;border:none;width:100%" src="http://4visions.nl/skype-online-status/donors.htm"><?php _e('Donorlist','skype_status'); ?></iframe>
			<p><?php _e('Do you want your name and/or link up there too? Or just appreciate my work?','skype_status'); ?><br />
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Skype%20Online%20Status&item_number=<?php echo SOSVERSION; ?>&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8" title="<?php _e('Donate with PayPal - it\'s fast, free and secure!','skype_status'); ?>"><img src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" style="border:none; vertical-align:text-bottom;" alt="<?php _e('Donate with PayPal - it\'s fast, free and secure!','skype_status'); ?>"/></a></p>
			<p><?php _e('Thanks!','skype_status'); ?></p>
		<?php if ( $wp_db_version >= 6846 ) { ?></div>

		<div id="tabs" class="inside"><p><strong><?php _e('Current theme','skype_status'); ?></strong><br /><br />
			<?php if ($current_theme_fullname) echo $current_theme_fullname; else _e('Custom...','skype_status'); ?></p>

			<?php echo skype_parse_theme($option);  ?>

		</div><?php } else { echo "</div></fieldset>"; } ?>

		<p class="submit">
			<input type="submit" name="skype_status_update" value="<?php _e('Save Changes'); ?> &raquo;" /> <br />
			<input type="submit" class="submitdelete delete" onclick='return confirm("<?php _e('WARNING !', 'skype-online-status'); ?> \r\n \r\n<?php _e('All your personal settings will be overwritten with the plugin default settings, including Skype ID, User name and Theme.', 'skype-online-status'); ?> \r\n \r\n<?php _e('Are you sure?', 'skype-online-status'); ?>");' name="skype_status_reset" value="<?php _e('Reset'); ?> &raquo;" /> <br />
			<input type="submit" class="submitdelete delete" onclick='return confirm("<?php _e('WARNING !', 'skype-online-status'); ?>  \r\n \r\n<?php _e('All your Skype Online Status AND widget settings will be cleared from the database so the plugin can be COMPLETELY removed. All Skype buttons on your blog will be deactivated.', 'skype-online-status'); ?> \r\n \r\n<?php _e('Are you sure?', 'skype-online-status'); ?>");' name="skype_status_remove" value="<?php _e('Remove'); ?> &raquo;" />

		</p> 

	</div>

	<div id="post-body">
	<div id="settings" style="min-height: 800px;">
		<p><?php _e('Define all your <em>default</em> Skype Status settings here.', 'skype-online-status'); ?> 
		<?php printf(__('Start simply by setting the basics like %1$s, %2$s and the button %3$s you want to show on your blog.', 'skype-online-status'),"<strong>".__('Skype ID', 'skype-online-status')."</strong>","<strong>".__('Full Name', 'skype-online-status')."</strong>","<strong>".__('Theme', 'skype-online-status')."</strong>"); ?> 
		<?php printf(__('Then activate the Skype Status Widget on your <a href="widgets.php">Widgets</a> page or use the Skype Status quicktag button %s in the WYSIWYG editor (TinyMCE) to place the Skype Online Status button in any post or page.', 'skype-online-status'),'<img src="'.SOSPLUGINURL.'skype_button.gif" alt="'.__('Skype Online Status', 'skype-online-status').'" style="vertical-align:text-bottom;" />'); ?> 
		<?php _e('Later on, you can fine-tune everything until it fits just perfectly on you pages.', 'skype-online-status'); ?> 
		<?php _e('Note:', 'skype-online-status'); _e('Some basic settings may be overridden per Widget settings or when calling the Skype button with a template function.', 'skype-online-status'); ?></p>
		<p><?php printf(__('Read more about configuring this plugin and more ways to trigger Skype buttons on your blog in the %1$s section. If you have any remaining questions, see the %2$s page to get help.', 'skype-online-status'),"<strong>".__('Quick Guide', 'skype-online-status')."</strong>","<strong>".__('Notes &amp; Live Support', 'skype-online-status')."</strong>"); ?></p>
		
		<p align="right"><a href="#wphead"><?php _e('back to top', 'skype-online-status'); ?></a></p>

		<h3><?php _e('Basic Options', 'skype-online-status'); ?></h3>

		<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Skype', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
			<p><label for="skype_id"><?php _e('Skype ID', 'skype-online-status'); _e(': ', 'skype-online-status'); ?></label><input type="text" name="skype_id" id="skype_id" value="<?php echo $option['skype_id']; ?>" /><br /><?php _e('Simply enter your Skype ID. Or... If you want the button to invoke a Skype multichat or conference call, enter more then one Skype ID seperated with a semi-colon (<strong>;</strong>). You may also enter a regular phone number (starting with a <strong>+</strong> followed by country code; note that callers need to have SkypeOut to call). It just all depends on what you want to achieve!', 'skype-online-status'); ?></p>
			<p><label for="user_name"><?php _e('Full Name', 'skype-online-status'); _e(': ', 'skype-online-status'); ?></label><input type="text" style="width: 250px;" name="user_name" id="user_name" value="<?php echo stripslashes(htmlspecialchars($option['user_name'])); ?>" /><br /><?php _e('Your full name as you want it to appear in Skype links, link-titles and image alt-texts on your blog.', 'skype-online-status'); ?></p>
		</fieldset>

		<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Function', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
			<p><?php _e('Some of the button themes will show your online status in an icon/image, without having a specific function assigned. This means you can select what the button should do when clicked by a visitor.', 'skype-online-status'); ?><br />
			<label for="button_function"><strong><?php _e('Function', 'skype-online-status'); ?></strong> <?php printf(__('for the %s tag', 'skype-online-status'),"{function}"); _e(': ', 'skype-online-status'); ?></label><select name="button_function" id="button_function">
				<?php foreach ($skype_avail_functions as $key => $value) {
				echo '<option value="'.$key.'"';
				if ( $option['button_function'] == $key ) echo ' selected="selected"';
				echo '>'.$value.'</option>
				'; } 
				unset($value); ?> 
			</select> <a href="#" onclick="javascript:SwitchInfoBlock('function_info');return(false);">?</a></p>
			<blockquote id="function_info" style="display:none"><em><?php _e('Note:', 'skype-online-status'); echo " "; printf(__('This setting will only be used in the \'%1$s\' theme templates, or in your own custom template when the tags %2$s and %3$s are used.', 'skype-online-status'),"My Status","{function}","{functiontxt}");?></em></blockquote>
		</fieldset>

		<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Theme', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
			<div style="float:right;width:250px;border:1px solid #CCCCCC;padding:5px;margin:0 0 0 5px;">
<style type="text/css"><!-- .no_underline a { border-bottom:none } --></style>
				<strong><?php _e('Preview theme template:', 'skype-online-status'); ?></strong>
				<div class="alternate no_underline" style="height:250px;margin:5px 0 0 0;padding:5px">
					<div id="custom_edit" style="display:<?php if ($option['button_theme'] == 'custom_edit') echo 'block'; else echo 'none'; ?>;margin:0;padding:0"><div style="height:38px;border-bottom:1px dotted grey;margin:0 0 5px 0"><?php _e('Custom...', 'skype-online-status'); _e(' (edit under advanced options)', 'skype-online-status'); ?></div><?php echo skype_parse_theme($option);// echo skype_status($skype_id,$user_name,"",$use_voicemail,"",FALSE); ?></div>
					<?php echo $previews; ?>
				</div>
			</div>

			<p><?php printf(__('Start with <strong>selecting one of the predefined theme templates</strong> to load into the database. Hover over the options to see a preview. You might later select %1$s to edit the template in the text field under %2$s.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Custom Template', 'skype-online-status')."</strong>"); ?><br /><br /><?php printf(__('If you cannot find a suitable theme, check out <a href="http://www.skype.com/share/buttons/wizard.html" target="_blank">http://www.skype.com/share/buttons/wizard.html</a>. Select your options there and copy/paste the output into the textarea under %s.', 'skype-online-status'),"<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Display', 'skype-online-status')."</strong>"); ?></p>
			<p><label for="button_theme"><?php echo __('Theme', 'skype-online-status') . __(': ', 'skype-online-status'); ?></label> <select name="button_theme" id="button_theme" onchange="ChangeStyle(this);" onblur="PreviewStyle(this);"><option value="custom_edit"<?php if ($option['button_theme'] == "custom_edit") echo " selected=\"selected\""; ?> onmouseover="PreviewStyle(this);" onmouseout="UnPreviewStyle(this);"><?php _e('Custom...', 'skype-online-status'); ?></option><?php foreach ($walk['select'] as $key => $value) { echo "<option value=\"$value\""; if ($value == $option['button_theme']) { echo " selected=\"selected\""; } echo " onmouseover=\"PreviewStyle(this);\" onmouseout=\"UnPreviewStyle(this);\">$key</option>"; } unset($value); ?> </select> <a href="#" onclick="javascript:SwitchInfoBlock('theme_info');return(false);">?</a></p>
			<blockquote id="theme_info" style="display:none"><em><?php printf(__('When %1$s is selected, you can edit the template to your liking below at %2$s under %3$s. When you make changes to that field but select another theme template here, those changes will be overwriten by the new template!', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Customize currently loaded template', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Custom Template', 'skype-online-status')."</strong>"); ?></em></blockquote>
		</fieldset>

		<p class="submit">
			<input type="submit" name="skype_status_update" value="<?php _e('Save Changes'); ?> &raquo;" />
		</p>

		<p><?php printf(__('If you have your basic settings correct and there is a Skype button visible on blog, you can fine-tune it\'s appearance and function with the advanced settings. Each option is annotated but you can read more in the %s section.', 'skype-online-status'),"<strong>".__('Quick Guide', 'skype-online-status')."</strong>"); ?></p>

		<p align="right" style="clear: both;"><a href="#wphead"><?php _e('back to top', 'skype-online-status'); ?></a></p>



		<h3><?php _e('Advanced Options', 'skype-online-status'); ?></h3>

		<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Post content', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
			<p><?php printf(__('When writing posts you can insert a Skype button with a simple quicktag %1$s or %2$s but to make life even easier, a small button on the WYSIWYG editor can do it for you. Check this option to show %3$s or uncheck to hide it. You may still insert the quicktag  in the HTML code of your post or page content manually.', 'skype-online-status'),"<strong>&lt;!--skype status--&gt;</strong>","<strong>[-skype status-]</strong>","<img src=\"".SOSPLUGINURL."skype_button.gif\" alt=\"".__('Skype Online Status', 'skype-online-status')."\" style=\"vertical-align:text-bottom;\" />"); ?><br /><br />
			<input type="checkbox" name="use_buttonsnap" id="use_buttonsnap"<?php if ( $option['use_buttonsnap'] == "on") { print " checked=\"checked\""; } ?> /> <label for="use_buttonsnap"><?php _e('Use <strong>Skype Status quicktag button</strong> in the RTE for posts.','skype_status'); ?></label></p>
		</fieldset>

		<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Display &amp; Function', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
			<p><?php _e('These settings define which options should be used to replace their respective tag (if present) in the selected template file. If unchecked, the tags will be blanked out.','skype_status'); ?></p> 
			<ul style="list-style: square;">
				<li><input type="checkbox" name="use_voicemail" id="use_voicemail"<?php if ( $option['use_voicemail'] == "on" ) { print " checked=\"checked\""; } ?> /> <label for="use_voicemail"><?php printf(__('Use %s in dropdown button. Leave unchecked if you do not have a SkypeIn account or SkypeVoicemail.', 'skype-online-status'),"<strong>".__('Leave me voicemail', 'skype-online-status')."</strong>"); ?></label></li>
				<li><input type="checkbox" name="use_function" id="use_function"<?php if ( $option['use_function'] == "on" ) { print " checked=\"checked\""; } ?> /> <label for="use_function"><?php printf(__('Use %1$s (define below) for %2$s tags.', 'skype-online-status'),"<strong>".__('Action text', 'skype-online-status')."</strong>","{add/call/chat/userinfo/voicemail/sendfile}"); ?></label></li>
				<li><input type="checkbox" name="use_getskype" id="use_getskype"<?php if ( $option['use_getskype'] == "on") { print " checked=\"checked\""; } ?> /> <label for="use_getskype"><?php printf(__('Use %s link.', 'skype-online-status'),"<strong>".__('Download Skype now!','skype_status')."</strong>"); ?></label>
					<ul>
						<li><input type="checkbox" name="getskype_newline" id="getskype_newline"<?php if ( $option['getskype_newline'] == "on") { print " checked=\"checked\""; } ?> /> <label for="getskype_newline"><?php _e('Place link on a new line.', 'skype-online-status'); ?></label></li>
						<li><label for="getskype_text"><?php _e('Link text', 'skype-online-status'); _e(': ', 'skype-online-status'); ?></label><input name="getskype_text" style="width: 250px;" id="getskype_text" value="<?php echo stripslashes(htmlspecialchars($option['getskype_text'])); ?>" /></li>
						<li><label for="getskype_link"><?php _e('Link URL', 'skype-online-status'); _e(': ', 'skype-online-status'); ?></label>
						<select name="getskype_link" id="getskype_link">
							<option value=""<?php if ( $option['getskype_link'] == "" ) print " selected=\"selected\""; ?>><?php _e('Default Skype link', 'skype-online-status'); ?></option>
							<option value="skype_mainpage"<?php if ( $option['getskype_link'] == "skype_mainpage" ) print " selected=\"selected\""; ?>><?php _e('Skype main page', 'skype-online-status'); ?></option>
							<option value="skype_downloadpage"<?php if ( $option['getskype_link'] == "skype_downloadpage" ) print " selected=\"selected\""; ?>><?php _e('Skype download page', 'skype-online-status'); ?></option>
							<option value="custom_link"<?php if ( $option['getskype_link'] == "custom_link" ) print " selected=\"selected\""; ?>><?php _e('Custom...', 'skype-online-status'); ?></option>
						</select> <a href="#" onclick="javascript:SwitchInfoBlock('linkurl_info');return(false);">?</a> 
						<blockquote id="linkurl_info" style="display:none"><em><?php _e('Leave to Default if you are generous and think downloads should create some small possible revenue for the developer of this plugin -- that\'s me, thanks! :) -- but if you think open source developers are greedy bastards and should go away, select one of the other options -- just kidding, feel free... Really! You can always donate on the Notes & Live Support section ;).', 'skype-online-status'); printf(__('If you want to create your own link (say you have a Commission Junction, TradeDoubler or ValueCommerce account, read more on http://www.skype.com/partners/affiliate/) to get possible revenue from downloads yourself, select %1$s and paste the link code in the textarea under %2$s below.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Custom Download Link', 'skype-online-status')."</strong>"); ?></em></blockquote></li>
					</ul>
				</li>
				<li><label for="use_status"><strong><?php _e('Status texts', 'skype-online-status'); ?></strong> <?php printf(__('for the %s tag', 'skype-online-status'),"{status}"); _e(': ', 'skype-online-status'); ?></label><select name="use_status" id="use_status">
						<option value=""<?php if ( $option['use_status'] == "" ) print " selected=\"selected\""; ?>><?php _e('Disabled', 'skype-online-status'); ?></option>
						<option value="custom"<?php if ( $option['use_status'] == "custom" ) print " selected=\"selected\""; ?>><?php _e('Custom...', 'skype-online-status'); ?></option>
						<?php foreach ($skype_avail_languages as $key => $value) {
						echo '<option value="'.$key.'"';
						if ( $option['use_status'] == $key ) echo ' selected="selected"';
						echo '>Skype default text in '.$value.'</option>
						'; } 
						unset($value); ?> 
					</select> <a href="#" onclick="javascript:SwitchInfoBlock('statustext_info');return(false);">?</a> 
			<blockquote id="statustext_info" style="display:none"><em><?php printf(__('If you select %1$s, the tags %2$s, %3$s and %4$s in the button template will be ignored.', 'skype-online-status'),"<strong>".__('Disabled', 'skype-online-status')."</strong>","{status}","{statustxt}","{sep2}"); 
						echo "<br />"; 
						printf(__('When security settings on your server are too tight (<strong>safe_mode</strong> enabled, <strong>open_basedir</strong> restictions or <strong>allow_url_fopen</strong> being disabled), your status does not show or the Skype button doesn\'t load and you find an error like "Warning: file_get_contents() [function.file-get-contents]: URL file-access is disabled in the server configuration..." in the server log files, use either %1$s or %1$s here.','skype_status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Disabled', 'skype-online-status')."</strong>"); ?></em></blockquote>
					<?php if (!ini_get('allow_url_fopen')) {
						$status_readonly = " readonly=\"readonly\""; 
						$status_style = "color:grey;";
						echo "<br /><span style=\"color:red\">" . __('Note:', 'skype-online-status') . " " . __('The security settings on your server are too tight for the online status to be read from the Skype server.', 'skype-online-status') . " " . sprintf(__('It is advised to set %1$s to %2$s here untill you (or your server admin) change the server INI <strong>allow_url_fopen</strong> and related setting.', 'skype-online-status'),"<strong>".__('Status texts', 'skype-online-status')."</strong>","<strong>".__('Disabled', 'skype-online-status')."</strong>") . sprintf(__('You can also select %1$s but only the %2$s status text will be available.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Error', 'skype-online-status')."</strong>") . "</span>"; } ?>
				</li>
			</ul>
		</fieldset>

		<div style="float:left;width:46%;">
			<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Custom Status texts', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
				<p><?php printf(__('Text that will replace the %s tag depending on actual online status.', 'skype-online-status'),"{status}"); 

	if ($option['use_status'] != "custom") { 
	echo " " . sprintf(__('Please, change %1$s to %2$s under %3$s (above) for these options to become available first.', 'skype-online-status'),"<strong>".__('Status texts', 'skype-online-status')."</strong>","<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Display', 'skype-online-status')."</strong>");
	$status_readonly = " readonly=\"readonly\""; 
	$status_style = "color:grey;"; 
	} 
	?>
				</p>
				<table>
					<tr>
						<th><?php _e('Status', 'skype-online-status'); ?></th>
						<th><?php _e('Value', 'skype-online-status'); ?></th>
						<th><?php _e('Text', 'skype-online-status'); ?></th>
					</tr>
				<?php foreach ($skype_avail_statusmsg as $key => $value) {
				echo '
					<tr>
						<td><label for="status_'.$key.'_text">';
				echo $value.'</label></td>
						<td>('.$key.')</td>
						<td><input type="text" name="status_'.$key.'_text" id="status_'.$key.'_text" value="';
				echo stripslashes(htmlspecialchars($option['status_'.$key.'_text']));
				echo '"'.$status_readonly.' style="'.$status_style.'" /></td>
					</tr>';
				} 
				unset($value); ?> 
					<tr>
						<td><label for="status_error_text"><?php _e('Error', 'skype-online-status'); ?></label></td>
						<td><?php _e('(none)', 'skype-online-status'); ?></td>
						<td><input type="text" name="status_error_text" id="status_error_text" value="<?php echo stripslashes(htmlspecialchars($option['status_error_text'])); ?>" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div style="float: right; width: 50%;">
			<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Tag texts', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
				<p><?php _e('Define texts to replace their respective template tags relating to the Skype button action.', 'skype-online-status'); ?></p> 
				<table>
					<tr>
						<th><?php _e('Action', 'skype-online-status'); ?></th>
						<th><?php _e('Tag', 'skype-online-status'); ?></th>
						<th><?php _e('Text', 'skype-online-status'); ?></th>
					</tr>
				<?php foreach ($skype_avail_functions as $key => $value) {
				echo '
					<tr>
						<td><label for="'.$key.'_text">';
				echo $value.'</label></td>
						<td>{'.$key.'}</td>
						<td><input type="text" name="'.$key.'_text" id="'.$key.'_text" value="';
				echo stripslashes(htmlspecialchars($option[$key.'_text']));
				echo '" /></td>
					</tr>';
				} 
				unset($value); ?> 
				</table>
	<?php 
	if ($option['use_status'] == "") { 
		echo "<p>" . sprintf(__('Please, change %1$s under %3$s (above) to any value other than %2$s for the following options to become available first.', 'skype-online-status'),"<strong>".__('Status texts', 'skype-online-status')."</strong>","<strong>".__('Disabled', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Display', 'skype-online-status')."</strong>") . "</p>";
		$other_readonly = " readonly=\"readonly\"";
		$other_style = "color:grey;"; 
	} else {
		echo "				<br />"; 
	}
	?>
				<table>
					<tr>
						<th><?php _e('Other', 'skype-online-status'); ?></th>
						<th><?php _e('Tag', 'skype-online-status'); ?></th>
						<th><?php _e('Text', 'skype-online-status'); ?></th>
					</tr>
					<tr>
						<td><label for="seperator1_text"><?php _e('First seperator', 'skype-online-status'); ?></label></td>
						<td>{sep1}</td>
						<td><input name="seperator1_text" id="seperator1_text" value="<?php echo stripslashes(htmlspecialchars($option['seperator1_text'])); ?>"<?php echo $other_readonly; ?> style="<?php echo $other_style; ?>" /></td>
					</tr>
					<tr>
						<td><label for="my_status_text"><?php _e('My status', 'skype-online-status'); ?></label></td>
						<td>{statustxt}</td>
						<td><input type="text" name="my_status_text" id="my_status_text" value="<?php echo stripslashes(htmlspecialchars($option['my_status_text'])); ?>"<?php echo $other_readonly; ?> style="<?php echo $other_style; ?>" /></td>
					</tr>
					<tr>
						<td><label for="seperator2_text"><?php _e('Second seperator', 'skype-online-status'); ?></label></td>
						<td>{sep2}</td>
						<td><input name="seperator2_text" id="seperator2_text" value="<?php echo stripslashes(htmlspecialchars($option['seperator2_text'])); ?>"<?php echo $other_readonly; ?> style="<?php echo $other_style; ?>" /></td>
					</tr>
				</table>
			</fieldset>
		</div>

		<fieldset class="options" style="clear:both"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Custom Template', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
			<p><?php printf(__('The currently selected template has been loaded into the database. You can edit it here if you like, but be sure to select %1$s under %2$s or your changes will be lost.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Basic Options', 'skype-online-status')." / ".__('Theme', 'skype-online-status')."</strong>"); ?> <br /><br /><label for="button_template"><?php _e('Customize currently loaded template', 'skype-online-status'); _e(': ', 'skype-online-status'); ?></label> <a href="#" onclick="javascript:SwitchInfoBlock('customtemplate_info');return(false);">?</a><br />
			<textarea name="button_template" id="button_template" style="width:98%;height:240px;" onchange="javascript:document.getElementById('button_theme').options[0].selected=true;document.getElementById(visible_preview).style.display='none';document.getElementById('custom_edit').style.display='block';visible_preview='custom_edit';"><?php echo stripslashes(htmlspecialchars($option['button_template'])); ?></textarea></p>
			<blockquote id="customtemplate_info" style="display:none"><em><?php _e('Available tags:','skype_status'); ?> {skypeid} {username} {function} {functiontxt} {action} {add} {call} {chat} {userinfo} {voicemail} {sendfile} {status} {statustxt} {tag1} {tag2} <br /><?php _e('Available markers:','skype_status'); ?> &lt;!-- voicemail_start --&gt; &lt;!-- voicemail_end --&gt; <br /><?php printf(__('See %s for more instructions.','skype_status'),"<strong>".__('Quick Guide', 'skype-online-status')."</strong>"); ?><br /><span style="color:red"><?php printf(__('Oh, and did I mention this? Changes to the template will only be loaded when the option %1$s under %2$s is selected!', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Basic Options', 'skype-online-status')." / ".__('Theme', 'skype-online-status')."</strong>"); ?></span></em></blockquote>
		</fieldset>

		<fieldset class="options"><?php if ( $wp_db_version >= 6846 ) echo"<h4>"; else echo "<legend>"; _e('Custom Download Link', 'skype-online-status'); if ( $wp_db_version >= 6846 ) echo "</h4>"; else echo "</legend>"; ?>
			<p><?php printf(__('If you are a <a href="http://www.skype.com/partners/affiliate/">Skype Affiliate</a> select %1$s under %2$s (above) and paste your link/banner code (HTML/Javascript) here.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Display &amp; Function', 'skype-online-status')."</strong>"); ?><br /><br /><label for="getskype_custom_link"><strong><?php _e('Link/Banner Code', 'skype-online-status'); _e(': ', 'skype-online-status'); ?></strong></label><br /><textarea name="getskype_custom_link" id="getskype_custom_link" style="width:97%;height:100px;"><?php echo stripslashes(htmlspecialchars($option['getskype_custom_link'])); ?></textarea></p>
		</fieldset>

		<p align="right"><a href="#wphead">back to top</a></p>

		<p class="submit">
			<input type="submit" name="skype_status_update" value="<?php _e('Save Changes'); ?> &raquo;" />
		</p>

	</div>
	<div id="guide" style="min-height: 800px;">
		<h3><?php _e('Quick Guide','skype_status'); ?></h3>
		<ul>
			<li><a href="#basic">Basic Use</a></li>
			<li><a href="#adv">Advanced</a></li>
			<li><a href="#templ">Templates</a></li>
		</ul>

		<p id="basic" align="right"><a href="#wphead">back to top</a></p>
		<h4>Basic Use</h4>
		<p>Define basic Skype settings such as Skype ID (more then one possible, seperate with a semi-colon <strong>;</strong>), User name and preferred Theme on the Skype Online Status Settings page as default for each Skype Online Status Button on your blog. Then use one or more of the methodes described below to trigger the default Skype Status button on your blog pages. Under 'Advanced' you can read about ways to override your default settings and create multiple and different Skype buttons across your blog.</p>
		<p><img src="http://c.skype.com/i/legacy/images/share/buttons/privacy_shot.jpg" alt="" style="float:right" /><strong>Important:</strong> Be sure to enable <strong><em>online status</em></strong> in your personal Skype settings on your PC: open your Skype client, go to Tools > Options > Privacy (or Advanced), tick the 'Allow my status to be shown on the web' (or similar in your language) checkbox and 'Save'.</p>
		<p>To make your Skype button initiate conference calls or multi-chat sessions, put multiple Skype ID's seperated with a semi-colon (;) in the Skype ID box.</p>
		<h5>Widgets</h5>
		<p>Since version 2.6.1.0 there is a Skype Status Sidebar Widget available. Go to your Design > Widgets page and activate the Skype Status widget. When activated, it defaults to your settings on the Skype Status Options page but you can customize it if you like.</p>
		<h5>In posts and page content</h5>
		<p>It is also possible to trigger a Skype Status button (as predefined on the Skype Online Status Settings page) within posts or page content. Use the quicktag button <img src="<?php echo SOSPLUGINURL; ?>skype_button.gif" alt="Skype Online Status" style="vertical-align:text-bottom;" /> or insert manually <strong>&lt;!--skype status--&gt;</strong> ( or <strong>[-skype status-]</strong> ) in the HTML code of your post or page content to display a Skype Online Status button in your post. </p>
		<p>Note: the setting 'Use Skype Status quicktag button' should be checked for the quicktag button <img src="<?php echo SOSPLUGINURL; ?>skype_button.gif" alt="Skype Online Status" style="vertical-align:text-bottom;" /> to appear in WordPress's Rich Text Editor (TinyMCE) so you can easily drop the quicktag into the source code.</p>
		<h5>In theme files</h5>
		<p>Put <strong>&lt;?php if (function_exists(get_skype_status)) { get_skype_status(''); } else { echo "Skype button disabled"; } ?&gt;</strong> in your sidebar.php or other WordPress template files to display a Skype Button with Online Status information on your blog pages. Your predefined default settings (above) will be used.</p><p>The 'function_exists'-check is there to prevent an error when the plugin is disabled. In this case the echo text is displayed. You can define another alternative action or remove 'else { ... }' to display nothing at all.</p>

		<p id="adv" align="right"><a href="#wphead">back to top</a></p>
		<h4>Advanced</h4>
		<p>It is also possible to use multiple Skype buttons for different Skype Users and with different themes across your blog pages.</p>
		<h5>Syntax</h5>
		<p>Use the syntax <strong>get_skype_status('parameter1=something&parameter2=something_else');</strong> to get a button that looks different from the predefined settings on the Skype Online Status Settings page, or even using another Skype ID.</p>
		<h5>Parameters</h5>
			<dl><dt>skype_id</dt><dd>Alternative Skype ID</dd>
				<dt>user_name</dt><dd>Define the Skype user or screen name.</dd>
				<dt>button_theme</dt><dd>Define the theme template file to use for the button. Value must match a filename (without extention) from the /plugins/skype-online-status/templates/ directory or the predefined theme template will be used.</dd>
				<dt>use_voicemail</dt><dd>Set to 'on' if you want to display the 'Leave a voicemail' link in the Dropdown themes. Use this only if you have a SkypeIn account or SkypeVoicemail. Set of 'off' if you have a predefined setting 'on' and you want to override it.</dd>
			</dl>
		<h5>Example</h5>
		<p>The php-code <strong>get_skype_status('skype_id=echo123&amp;user_name=Skype voice test&amp;button_theme=callme_mini')</strong> will generate a Skype button with all your predefined settings <em><strong>except</strong></em> for Skype user 'echo123' (the Skype voice test user) with the screen name 'Skype voice test' and using template file 'callme_mini.html':</p>
		<p><?php get_skype_status('skype_id=echo123&user_name=Skype voice test&button_theme=callme_mini'); ?></p>

		<p id="templ" align="right"><a href="#wphead">back to top</a></p>
		<h4>Templates</h4>
		<p>Whenever the options on the Skype Status Options page are saved, the template is read either from the selected template file or the editable textarea (customizable view) and loaded into the database. To change the Skype Online Status button view to your liking you can choose to edit an existing template file, create a new one or edit the preloaded template in the editable textarea on the 'Skype Online Status Settings' page. Remember that after editing a template file, the new content must be reloaded into the database before changes apply.</p>
		<p>All template files can be found in the /plugins/skype-online-status/templates/ directory. You add new or edit existing ones with any simple text editor (like Notepad) or even a WYSIWYG editor (like Dreamweaver) as long as you follow some rules.</p>
		<h5>Template file rules</h5>
		<ol>
			<li>All template files must have a name consisting of only <strong>lowercase letters</strong>, <strong>numbers</strong> and/or <strong>underscores (_)</strong> or <strong>dashes (-)</strong>. Please, avoid any other signs, dots or whitespaces. Do not use the name <strong>custom_edit</strong> as is reserved for the customizable view.</li>
			<li>The template files must reside in the <strong>/templates/</strong> subdirectory of this plugin directory.</li>
			<li>The template files must have the <strong>.html</strong> extention. All other extentions in the templates directory will be ignored.</li>
			<li>The first line of any <strong>file</strong> must be something like: <br />
				<strong>&lt;!-- 'Template Name' style description - http://www.skype.com/go/skypebuttons --&gt;</strong><br />
				where the <em>'Template Name' style description</em> part will represent the template name on the Skype Online Status Settings page. Choose a recognizable name and a very short description.</li>
		</ol>
		<h5>Template rules</h5>
		<ol>
			<li>Within each template (file or customizable view) certain tags like <strong>{skypeid}</strong> are used that will be replaced according to their respective configuration on the Skype Status Settings page. See 'Template tags' below for all available tags.</li>
			<li>Everything within the template between <strong>&lt;!-- voicemail_start --&gt;</strong> and <strong>&lt;!-- voicemail_end --&gt;</strong> will be erased when the option 'Use <strong>Leave a voicemail</strong>' on the Skype Online Status Settings page is NOT checked.</li>
		</ol>
		<p>For the rest you are free to put anything you like in the template files.<br />
		To get started see <a href="http://www.skype.com/go/skypebuttons">http://www.skype.com/go/skypebuttons</a> for an interactive form to create new Skype Button code.</p> 
		<h5>Template tags</h5>
		<p>The following tags are available:</p>
		<h6>General tags</h6>
		<dl>
			<dt>{skypeid}</dt><dd>Put this where the 'Skype ID' should go. Usually href="skype:{skypeid}?call" but it can also be used elsewhere.</dd>
			<dt>{username}</dt><dd>Put this where you want the 'User name' to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{function}</dt><dd>Put this where you want the <em>preselected</em> Function to appear, such as after href="skype:{skypeid}?... in the link URL. The function can be set on the Skype Status Settings page under 'Function' to options like 'Call me', 'Chat with me' or 'Leave a voicemail'.</dd>
			<dt>{functiontxt}</dt><dd>Put this where you want the <em>corresponding</em> Function text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{status}</dt><dd>Put this where you want the <em>dynamic</em> 'Status' texts to appear, such as in title="", alt="" or as link text. The status text (defined on the Skype Status Settings page under 'Status text') depends on the actual online status of the defined Skype user and ranges from 'Unknown' to 'Online'.</dd>
			<dt>{statustxt}</dt><dd>Put this where you want the <em>static</em> 'My status' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{sep1}</dt><dd>Put this where you want the 'First seperator' text to appear, usually between the tags like {call} and {username}.</dd>
			<dt>{sep2}</dt><dd>Put this where you want the 'Second seperator' text to appear, usually between the tags like {username} and {status}.</dd>
		</dl>
		<h6>Action text tags</h6>
		<dl>
			<dt>{add}</dt><dd><dd>Put this where you want the 'Add me to Skype' text to appear, such as in title="", alt="" or as link text.</dd></dd>
			<dt>{call}</dt><dd>Put this where you want the 'Call me!' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{chat}</dt><dd>Put this where you want the 'Chat with me' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{sendfile}</dt><dd>Put this where you want the 'Send me a file' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{userinfo}</dt><dd>Put this where you want the 'View my profile' text to appear, such as in title="", alt="" or as link text.</dd>
			<dt>{voicemail}</dt><dd>Put this where you want the 'Leave me a voicemail' text to appear, such as in title="", alt="" or as link text.</dd>
		</dl>
		<h5>Examples</h5>
		<p>The classic 'Call me!' button template looks like this:</p>
		<blockquote>&lt;!-- 'Call me!' classic style - http://www.skype.com/go/skypebuttons --&gt;<br />&lt;a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{call}{sep1}{username}{sep2}{status}">&lt;img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="{call}{sep1}{username}{sep2}{status}" /&gt;&lt;/a&gt;</blockquote>
		<p>The template for a simple text link displaying username and online status (seperated by the second seperator tag) could look like this:</p>
		<blockquote>&lt;!-- 'My status' plain text link - http://www.skype.com/go/skypebuttons --&gt;<br />&lt;a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{username}{sep2}{status}">{username}{sep2}{status}&lt;/a&gt;</blockquote>
		<p align="right"><a href="#wphead">back to top</a></p>

	</div>
	<div id="notes" style="min-height: 800px;">

		<h3><?php _e('Notes &amp; Live Support','skype_status'); ?></h3>
		<ul>
			<li><a href="#prl">Version, Support, Pricing and Licensing</a></li>
			<li><a href="#live">Live support</a></li>
			<li><a href="#credits">Credits</a></li>
			<li><a href="#revhist">FAQ's, Revision History, Todo and other notes</a></li>
		</ul>

		<p id="prl" align="right"><a href="#wphead">back to top</a></p>
		<h4>Version, Support, Pricing and Licensing</h4>
		<p>This is <strong>version <?php echo SOSVERSION; ?></strong> of the Skype Online Status plugin for WordPress 2+.<br />
			Release date: <?php echo SOSVERSION_DATE; ?>. <br />
			The latest available release is: <strong>version <?php if(isset($r->new_version)) { echo $r->new_version . " <span class=\"updated fade\">PLEASE, <a href=\"" . $r->url . "\">UPDATE</a> BEFORE REPORTING BUGS !</span>"; } else { echo SOSVERSION; } ?></strong></p>
			Report bugs, feature requests and user experiences on <a href="http://groups.google.com/group/wp-skype-online-status">Skype Online Status - Google Discussion Group</a>. <br />
		<p>This plugin is in beta testing stage and is released under the <a href="http://www.gnu.org/licenses/gpl.txt">GNU General Public License</a>. You can use it free of charge but at your own risk on your personal or commercial blog.</p>
		<p>If you enjoy this plugin, you can thank me by way of a small donation for my efforts and the time I spend maintaining and developing this plugin and giving <a href="#live">live user support</a> in dutch, english and even a little french and german :).</p>
		<p>I appreciate every contribution, no matter if it&#8217;s two or twenty euro/dollar or any other amount. Please, use the link in the sidebar.</p>
		<p>Thanks!<br />
			<em>RavanH</em></p>
	
		<p id="live" align="right"><a href="#wphead">back to top</a></p>

		<h4>Live Support</h4>
		<p>To get live support on this plugin with Skype, simply use the link below. It will state wether I'm online and available for chat with Skype.</p>
		<p>
			Status <?php get_skype_status('skype_id=ravanhagen&user_name=Live Support&button_theme=status_plaintext'); ?><br /><br />
			To Skype-chat with RavanH: <a href="skype:ravanhagen?chat" onclick="return skypeCheck();" title="Live chat">Live chat</a></p>

		<p id="credits" align="right"><a href="#wphead">back to top</a></p>
		
		<h4>Credits</h4>
		<p>This plugin was built by <em>RavanH</em>. It is based upon the neat little plugin <a href="http://anti.masendav.com/skype-button-for-wordpress/">Skype Button v2.01</a> by <em>Anti Veeranna</em>. The plugin makes use of Owen's excellent <a href="http://redalt.com/wiki/ButtonSnap">ButtonSnap library</a>. My continued development of this plugin is supported by donators, mentioned in the sidebar. Many thanks!</p>

		<p id="revhist" align="right"><a href="#wphead">back to top</a></p>
		<h4>FAQ's, Revision History, Todo and other info</h4>
		<p>See the included <a href="<?php echo SOSPLUGINURL; ?>readme.txt">README</a> file:</p>
		<iframe src="<?php echo SOSPLUGINURL; ?>readme.txt" border="0" scrolling="auto" allowtransparency="yes" style="margin:0;padding:0;border:none;width:100%;height:580px"></iframe>
		<p align="right"><a href="#wphead">back to top</a></p>
	</div>
	
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
		<div><h4>Pluging global values and flags</h4> 
		<p>SOSDATADUMP=".SOSDATADUMP." (obviously ;-) )<br />SOSPLUGINURL=".SOSPLUGINURL."<br />SOSBUTTONSNAPFLAG=".SOSBUTTONSNAPFLAG."<br />SOSVERSION=".SOSVERSION."<br />SOSVERSION_DATE=".SOSVERSION_DATE."<br />SOSALLOWURLFOPEN=".SOSALLOWURLFOPEN."<br />SOSREMOVEFLAG=".SOSREMOVEFLAG."<br />SOSUSECURL=".SOSUSECURL."<br />SOSCURLFLAG=".SOSCURLFLAG."
</p>
		</div></div>";	
	}
	?>

	<script type="text/javascript">
		document.getElementById('loading').style.display='none';
		document.getElementById('notes').style.display='none'; 
		document.getElementById('guide').style.display='none'; 
		document.getElementById('settings').style.display='block';
	</script>
</div>
</form>
</div>
	<?php
}

?>
