<?php
function skype_status_options() {
	global $skype_status_config, $skype_avail_languages, $skype_avail_functions, $skype_avail_statusmsg, $soswhatsnew_this, $soswhatsnew_recent;
	$option = $skype_status_config;

//add_thickbox();

?>
<div class="wrap">
	<div id="icon-edit-comments" class="icon32"><br /></div>
<h2><?php echo __('Skype Online Status', 'skype-online-status')." ".SOSVERSION ?></h2>

<?php	// check if database has been cleared for removal or else updated after plugin upgrade 
	if (!empty($_POST['skype_status_remove'])) { // hit remove button
		define('SOSREMOVEFLAG', TRUE);
		delete_option('skype_status');
		delete_option('skype_widget_options');
		echo "<div class=\"error fade\"><p><strong>".__('Your Skype Online Status database settings have been cleared from the database for removal of this plugin!', 'skype-online-status')."</strong><br />".__('You can still resave the old settings shown below to (partly) undo this action but custom widget settings will be reverted to default.', 'skype-online-status')."<br /><br />".__('Are you sure?', 'skype-online-status')." ";
		if (function_exists('wp_nonce_url')) 
			echo "<a href=\"" . wp_nonce_url('plugins.php?action=deactivate&amp;plugin='.SOSPLUGINFILE, 'deactivate-plugin_'.SOSPLUGINFILE) . "\" title=\"" . __('Deactivate this plugin') . "\" class=\"delete\">" . __('Deactivate') . "</a>.";
		else
			_e('Go to the <a href="plugins.php">Plugins page</a> and deactivate it.', 'skype-online-status');
		echo "<br /><br />".__('Please, keep in mind that any WP theme template file changes you have made, can not be undone through this process. Also, any post quicktags that have been inserted in posts will (harmlessly) remain there. If you changed your mind about removing this plugin, just resave the settings NOW (or all your settings will be lost) or revert to default settings at the bottom of this page.', 'skype-online-status')."</p></div>";
	} elseif ($skype_status_config['upgraded'] == TRUE) {
		$skype_status_config['upgraded'] = FALSE;
		update_option('skype_status',$skype_status_config);
		echo "<div class=\"updated fade\"><p><strong>".__('Skype Online Status plugin has been upgraded to version ', 'skype-online-status').SOSVERSION."</strong><br />".__('Please, verify your settings now.', 'skype-online-status')."</p><p><strong>".__('What\'s new?', 'skype-online-status')."</strong><p><em>".__('Latest:', 'skype-online-status')."</em><br />".$soswhatsnew_this."</p><p><em>".__('Recent:', 'skype-online-status')."</em><br />".$soswhatsnew_recent."</p></div>";
	} elseif ($skype_status_config['installed']==TRUE) {
		$skype_status_config['installed'] = FALSE;
		update_option('skype_status',$skype_status_config);
		echo "<div class=\"updated fade\"><p><strong>";
		printf(__('Skype Online Status plugin version %s has been installed!','skype-online-status'),SOSVERSION);
		echo "</strong> ".__('Please, adapt the default settings to your personal preference so you can start using Skype buttons anywhere on your site.','skype-online-status')." ";
		printf(__('Read the %s section for more instructions.','skype-online-status'),"<strong>".__('Quick Guide','skype-online-status')."</strong>");
		echo "</p></div>";
	}

	// check for new version
	do_action('load-plugins.php');
	$current = get_option('update_plugins');
	if ( isset( $current->response[SOSPLUGINFILE] ) ) {
		$r = $current->response[SOSPLUGINFILE];
		echo "<div class=\"updated fade-ff0000\"><p><strong>";
		if ( !current_user_can('edit_plugins') )
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a>.'), __('Skype Online Status', 'skype-online-status'), $r->url, $r->new_version);
		else if ( empty($r->package) )
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> <em>automatic upgrade unavailable for this plugin</em>.'), __('Skype Online Status', 'skype-online-status'), $r->url, $r->new_version);
		else
			printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> or <a href="%4$s">upgrade automatically</a>.'), __('Skype Online Status', 'skype-online-status'), $r->url, $r->new_version, wp_nonce_url("update.php?action=upgrade-plugin&amp;plugin=".SOSPLUGINFILE, 'upgrade-plugin_'.SOSPLUGINFILE) );
		echo "</strong></p></div>";
	}

	// warning about inability to check remote status file 
	if (!SOSREMOTE) {  
		echo "<div class=\"error fade\"><p>".__('The security settings on your server are too tight for the online status to be read from the Skype server.', 'skype-online-status').__('Please, check if one of the following options can be met -or- ask your server admin / hosting provider to take care of this.', 'skype-online-status')."</p><ul><li>".__('Install and activate cURL libraries on the server (preferred)', 'skype-online-status')."</li><li>".__('Upgrade PHP to a version that includes fsockopen (PHP 4+)', 'skype-online-status')."</li><li>".__('Change INI setting <strong>allow_url_fopen</strong> to ON (not advised)', 'skype-online-status')."</li></ul><p>".__('Reading your online status from the remote Skype server has been disabled. As a result, some of the advanced options will not be available. However, Skype button themes that also show your online status by way of images (that are provided by the same Skype server) will still do so.')."</p></div>";
	} 
?>

	<div id="loading" class="error fade"><p><strong><?php _e('Please, wait while page has loaded completely.<br /> When the Skype server at http://mystatus.skype.com/ is slow or down, this might take a while...', 'skype-online-status') ?></strong></p></div>

<?php	// update the options if form is saved
	if (!empty($_POST['skype_status_update'])) { // pressed udate button
		//if (skype_status_valid_id($_POST['skype_id']) && skype_status_valid_theme($_POST['button_theme'])) {
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
			echo "<div id=\"notice\" class=\"updated fade\"><p><strong>".__('Options updated!', 'skype-online-status')."</strong></p></div>";
		//}
	} else if (!empty($_POST['skype_status_reset'])) { // pressed reset button
			$option = skype_default_values();
			update_option("skype_status",$option);
			echo "<div id=\"notice\" class=\"updated fade\"><p><strong>".__('Options reset!', 'skype-online-status')."</strong></p></div>";
	}

	// get all the selected options (except test call id) and their previews into an array
	$preview_options =array("skype_id" => "echo123","user_name" => __('Skype Test Call', 'skype-online-status'));
	$preview_options = wp_parse_args( $preview_options, $option );
	$walk = skype_walk_templates("", $preview_options, "", "", FALSE);

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

<form enctype="multipart/form-data" method="post" action="#">
<?php wp_nonce_field('update-options') ?>

<div id="poststuff" class="metabox-holder has-right-sidebar">

  <div id="side-info-column" class="inner-sidebar">

    <div id="side-sortables" class="meta-box-sortables">
	<div id="submitdiv" class="postbox ">
		<div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Sections','skype-online-status') ?></span></h3>
		<div class="inside">
		  <div class="submitbox" id="submitpost">
		    <div id="minor-publishing">
		      <div class="misc-pub-section">
			<ul><li>
			<a style="color:#d54e21" id="settingslink" href="#settings" onclick="javascript:
				document.getElementById('notes').style.display='none'; 
				document.getElementById('guide').style.display='none'; 
				document.getElementById('settings').style.display='block';
				document.getElementById('settingslink').style.color='#d54e21';
				document.getElementById('noteslink').style.color='#264761'; 
				document.getElementById('guidelink').style.color='#264761';"><?php _e('Options') ?></a>
			</li>
			<li>
			<a id="guidelink" href="#guide" onclick="javascript:
				document.getElementById('notes').style.display='none'; 
				document.getElementById('guide').style.display='block'; 
				document.getElementById('settings').style.display='none';  
				document.getElementById('settingslink').style.color='#264761'; 
				document.getElementById('noteslink').style.color='#264761'; 
				document.getElementById('guidelink').style.color='#d54e21';"><?php _e('Quick Guide', 'skype-online-status') ?></a>
			</li>
			<li>
			<a id="noteslink" href="#notes" onclick="javascript:
				document.getElementById('notes').style.display='block'; 
				document.getElementById('guide').style.display='none'; 
				document.getElementById('settings').style.display='none'; 
				document.getElementById('settingslink').style.color='#264761'; 
				document.getElementById('noteslink').style.color='#d54e21'; 
				document.getElementById('guidelink').style.color='#264761';"><?php _e('Notes &amp; Live Support','skype-online-status') ?></a>
			</li></ul>
		      </div>

		      <div class="misc-pub-section misc-pub-section-last">
			<p><strong><?php _e('Current theme','skype-online-status') ?></strong><br /><br />

			<?php if ($current_theme_fullname) echo $current_theme_fullname; else _e('Custom...','skype-online-status') ?></p>

			<?php if (!$option['skype_id']) echo "<span class=\"error\">" . __('Skype button disabled:', 'skype-online-status') . " " . __('Missing Skype ID.', 'skype-online-status') . "</span>"; else echo skype_status($option);  ?>

			<br />
		      </div>
		    </div>
		    <div id="major-publishing-actions">
			<div id="publishing-action">
				<input type="submit" name="skype_status_update" class="button-primary" value="<?php _e('Save Changes') ?> &raquo;" />
			</div>
			<div id="delete-action">
				<input type="submit" class="submitdelete deletion" onclick='return confirm("<?php _e('WARNING !', 'skype-online-status') ?> \r\n \r\n<?php _e('All your personal settings will be overwritten with the plugin default settings, including Skype ID, User name and Theme.', 'skype-online-status') ?> \r\n \r\n<?php _e('Are you sure?', 'skype-online-status') ?>");' name="skype_status_reset" value="<?php _e('Reset') ?> &raquo;" /> <br />
				<input type="submit" class="submitdelete deletion" onclick='return confirm("<?php _e('WARNING !', 'skype-online-status') ?>  \r\n \r\n<?php _e('All your Skype Online Status AND widget settings will be cleared from the database so the plugin can be COMPLETELY removed. All Skype buttons on your blog will be deactivated.', 'skype-online-status') ?> \r\n \r\n<?php _e('Are you sure?', 'skype-online-status') ?>");' name="skype_status_remove" value="<?php _e('Remove') ?> &raquo;" />
			</div>
			<div class="clear"></div>

		    </div>
		  </div>
		</div>
	</div>

	<div id="previewdiv" class="postbox "><div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Preview theme template:', 'skype-online-status') ?></span></h3><div class="inside">
			<p><?php _e('Note:', 'skype-online-status') ?> <?php _e('The preview button uses the Skype Test Call service to allow testing its function.','skype-online-status') ?></p>
			<div class="alternate no_underline" style="margin:5px 0 0 0;padding:5px;height:210px;">
				<div id="custom_edit" style="display:<?php if ($option['button_theme'] == 'custom_edit') echo 'block'; else echo 'none' ?>;margin:0;padding:0">
					<div style="height:38px;border-bottom:1px dotted grey;margin:0 0 5px 0"><?php _e('Custom...', 'skype-online-status'); _e(' (edit under advanced options)', 'skype-online-status') ?></div>
					<?php echo skype_parse_theme($preview_options,FALSE) ?>
				</div>
				<?php echo $previews ?>
			</div>

		</div>

	</div>

	<div id="donationsdiv" class="postbox "><div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Credits','skype-online-status') ?></span></h3><div class="inside">
			<h4><?php _e('Translations:','skype-online-status') ?></h4>
			<p><?php _e('Translation contributions are highly appreciated. Authors of new translations or updates will be mentioned here.','skype-online-status') ?></p>

			<iframe border="0" frameborder="0" scrolling="vertical" allowtransparency="yes" style="margin:0;padding:0;border:1px solid #ddd;width:100%;height:200px;background-color:#f9f9f9" src="http://4visions.nl/skype-online-status/translators/"><a href="http://4visions.nl/skype-online-status/translators/">http://4visions.nl/skype-online-status/translators/</a></iframe>

			<p><?php _e('Want to make your own translation too? Read the <a href="http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt">translation instructions</a> included with this plugin to get started.','skype-online-status') ?></p>

			<h4><?php _e('Donations','skype-online-status') ?></h4>
			<p><?php _e('All donations are much appreciated and will (without objection) be mentioned here as a way of expressing my gratitude.','skype-online-status') ?></p>

			<iframe border="0" frameborder="0" scrolling="vertical" allowtransparency="yes" style="margin:0;padding:0;border:1px solid #ddd;width:100%;height:180px;background-color:#f9f9f9" src="http://4visions.nl/skype-online-status/donors/"><a href="http://4visions.nl/skype-online-status/donors/">http://4visions.nl/skype-online-status/donors/</a></iframe>

			<p><?php _e('Do you want your name and/or link up there too? Or just appreciate my work?','skype-online-status') ?><br /><br />
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Skype%20Online%20Status&item_number=<?php echo SOSVERSION ?>&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8" title="<?php _e('Donate with PayPal - it\'s fast, free and secure!','skype-online-status') ?>"><img src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" style="border:none; vertical-align:text-bottom;" alt="<?php _e('Donate with PayPal - it\'s fast, free and secure!','skype-online-status') ?>"/></a></p>
			<p><?php _e('Thanks!','skype-online-status') ?><br /><em>RavanH</em></p>
		</div>
	</div>

	<div id="supportdiv" class="postbox "><div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Support','skype-online-status') ?></span></h3>
		<div class="inside">
			<p><?php _e('For all support questions and suggestions, please go to','skype-online-status') ?> <a href="http://wordpress.org/tags/skype-online-status/"><?php _e('WordPress Support','skype-online-status') ?> - <?php _e('Skype Online Status','skype-online-status') ?></a>.</p>
			<p><?php _e('For <strong>feature requests</strong> or general help with <strong>WordPress</strong> or <strong>hosting</strong>, please contact <em>RavanH</em> via e-mail <a href="mailto:ravahagen@gmail.com">ravanhagen@gmail.com</a> or Skype chat:','skype-online-status') ?><br /> <?php get_skype_status('skype_id=ravanhagen&user_name=Live Support&button_theme=chat_smallclassic_blue'); ?></p>
			<p><?php _e('Please <strong>rate this plugin</strong> at <a href="http://wordpress.org/extend/plugins/skype-online-status/">WordPress</a>','skype-online-status') ?></p>

			<br />
			<iframe border="0" frameborder="0" scrolling="no" allowtransparency="yes" style="margin:0;padding:0;border:none;width:100%;height:325px" src="http://4visions.nl/skype-online-status/ads/"></iframe>

		</div>
	</div>

	<div id="discussiondiv" class="postbox "><div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Discussion') ?></span></h3>
		<div class="inside">

			<h4><?php _e('WordPress Support','skype-online-status'); _e(': ','skype-online-status') ?> <?php _e('Skype Online Status','skype-online-status') ?></h4>
			<div class='rss-widget'>
			<?php if(function_exists(wp_widget_rss_output)) {
				wp_widget_rss_output( "http://wordpress.org/support/rss/tags/skype-online-status/", array('show_date' => 1, 'items' => 5) );
				echo "<p align=\"right\"><a href=\"http://wordpress.org/tags/skype-online-status/#latest\">".__("More...")."</a></p>"; }
			else {
				echo "<p><a href=\"http://wordpress.org/support/rss/tags/skype-online-status/\">http://wordpress.org/support/rss/tags/skype-online-status/</a></p>"; } ?>
			</div>
		</div>
	</div>

	<div id="resourcesdiv" class="postbox "><div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Resources','skype-online-status') ?></span></h3>
		<div class="inside">
			<ul>
				<li><a href="http://www.skype.com/go/skypebuttons"><?php _e('Skype Buttons','skype-online-status') ?></a></li>
				<li><a href="http://www.skype.com/share/buttons/wizard.html" target="_blank"><?php _e('Skype buttons wizard','skype-online-status') ?></a></li>
				<li><a href="http://mystatus.skype.com/<?php echo $option['skype_id'] ?>"><?php printf(__('View %s\'s online status on the Skype server','skype-online-status'),$option['skype_id']) ?></a></li>
				<li><a href="http://www.skype.com/share/buttons/status.html"><?php _e('Edit Privacy Options in your Skype client','skype-online-status') ?></a></li>
				<li><a href="http://www.skype.com/partners/affiliate/"><?php _e('Skype Affiliate Program','skype-online-status') ?></a></li>
			</ul>
			<p><a href="http://www.jdoqocy.com/rn68biroiq596AFCEC5769EF78A" onmouseover="window.status='http://www.skype.com';return true;" onmouseout="window.status='';return true;">
<img src="http://www.awltovhc.com/85116bosgmk596AFCEC5769EF78A" alt="" border="0"/></a></p>
		</div>
	</div>

	<div id="morediv" class="postbox "><div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Get more from Skype','skype-online-status') ?></span></h3>
		<div class="inside">
			<ul>
				<li><a href="http://www.anrdoezrs.net/click-3049686-10420859?url=http%3A%2F%2Fwww.skype.com%2Fallfeatures%2Fcallphones%2F%2F%3Fcm_mmc%3Daffiliate%2D%5F%2Dcommission%5Fjunction%2D%5F%2Dlink%2D%5F%2Dbuilder" title="<?php _e('SkypeOut','skype-online-status') ?>" 
onmouseover="window.status='http://www.skype.com';return true;" onmouseout="window.status=' ';return true;"><?php _e('SkypeOut','skype-online-status'); _e(': ','skype-online-status'); _e('Call any phone directly from Skype.','skype-online-status') ?></a></li>
				<li><a href="http://www.anrdoezrs.net/click-3049686-10420859?url=http%3A%2F%2Fwww.skype.com%2Fallfeatures%2Fonlinenumber%2F%2F%3Fcm_mmc%3Daffiliate%2D%5F%2Dcommission%5Fjunction%2D%5F%2Dlink%2D%5F%2Dbuilder" title="<?php _e('SkypeIn','skype-online-status') ?>" 
onmouseover="window.status='http://www.skype.com';return true;" onmouseout="window.status=' ';return true;"><?php _e('SkypeIn','skype-online-status'); _e(': ','skype-online-status'); _e('Your personal online number.','skype-online-status') ?></a></li>
				<li><a href="http://www.anrdoezrs.net/click-3049686-10420859?url=http%3A%2F%2Fwww.skype.com%2Fallfeatures%2Fvoicemail%2F%2F%3Fcm_mmc%3Daffiliate%2D%5F%2Dcommission%5Fjunction%2D%5F%2Dlink%2D%5F%2Dbuilder" title="<?php _e('Skype Voicemail','skype-online-status') ?>" onmouseover="window.status='http://www.skype.com';return true;" onmouseout="window.status=' ';return true;"><?php _e('Skype Voicemail','skype-online-status'); _e(': ','skype-online-status'); _e('Never miss a call!','skype-online-status');?></a></li>
				<li><a href="http://www.anrdoezrs.net/click-3049686-10420859?url=http%3A%2F%2Fwww.skype.com%2Fallfeatures%2Faccessories%2F%2F%3Fcm_mmc%3Daffiliate%2D%5F%2Dcommission%5Fjunction%2D%5F%2Dlink%2D%5F%2Dbuilder" title="Skype Accessories" 
onmouseover="window.status='http://www.skype.com';return true;" onmouseout="window.status=' ';return true;"><?php _e('Accessories: Get the most out of Skype!','skype-online-status') ?></a></a></li>
			</ul>

			<br />
			<iframe border="0" frameborder="0" scrolling="no" allowtransparency="yes" style="margin:0;padding:0;border:none;width:100%;height:270px" src="http://4visions.nl/skype-online-status/ad/"></iframe>

		</div>
	</div>
    </div>
  </div> <!-- side-info-column inner-sidebar -->

  <div id="post-body" class="has-sidebar ">
    <div id="post-body-content" class="has-sidebar-content">

      <div id="normal-sortables" class="meta-box-sortables">
	<div id="settings" style="min-height: 800px;">

	  <p><?php _e('Define all your <em>default</em> Skype Status settings here.', 'skype-online-status') ?> 
		<?php printf(__('Start simply by setting the basics like %1$s, %2$s and the button %3$s you want to show on your blog.', 'skype-online-status'),"<strong>".__('Skype ID', 'skype-online-status')."</strong>","<strong>".__('Full Name', 'skype-online-status')."</strong>","<strong>".__('Theme', 'skype-online-status')."</strong>") ?> 
		<?php printf(__('Then activate the Skype Status Widget on your <a href="widgets.php">Widgets</a> page or use the Skype Status quicktag button %s in the WYSIWYG editor (TinyMCE) to place the Skype Online Status button in any post or page.', 'skype-online-status'),'<img src="'.SOSPLUGINURL.'/skype_button.gif" alt="'.__('Skype Online Status', 'skype-online-status').'" style="vertical-align:text-bottom;" />') ?> 
		<?php _e('Later on, you can fine-tune everything until it fits just perfectly on you pages.', 'skype-online-status') ?><br />
		<?php _e('Note:', 'skype-online-status') ?> <?php _e('Some basic settings may be overridden per Widget settings or when calling the Skype button with a template function.', 'skype-online-status') ?></p>
	  <p><?php printf(__('Read more about configuring this plugin and more ways to trigger Skype buttons on your blog in the %1$s section. If you have any remaining questions, see the %2$s page to get help.', 'skype-online-status'),"<strong>".__('Quick Guide', 'skype-online-status')."</strong>","<strong>".__('Notes &amp; Live Support', 'skype-online-status')."</strong>") ?></p>

	  <div id="basicdiv" class="postbox ">

	    <div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Basic Options', 'skype-online-status') ?></span></h3>

	    <div class="inside">

		<fieldset class="options">
			<h4><?php _e('Skype', 'skype-online-status') ?></h4>
			<p><label for="skype_id"><?php _e('Skype ID', 'skype-online-status'); _e(': ', 'skype-online-status') ?></label><input type="text" name="skype_id" id="skype_id" value="<?php echo $option['skype_id'] ?>" /> <a href="#" onclick="javascript:SwitchInfoBlock('skypeid_info');return(false);">?</a></p>
			<blockquote id="skypeid_info" style="display:none"><em><?php printf(__('Simply enter your Skype ID. Or... If you want the button to invoke a Skype multichat or conference call, enter more then one Skype ID seperated with a semi-colon (<strong>;</strong>). You may also enter a regular phone number (starting with a <strong>+</strong> followed by country code; note that callers need to have %s to call). It just all depends on what you want to achieve!','skype-online-status'),'<a href="http://www.anrdoezrs.net/click-3049686-10420859?url=http%3A%2F%2Fwww.skype.com%2Fallfeatures%2Fcallphones%2F%2F%3Fcm_mmc%3Daffiliate%2D%5F%2Dcommission%5Fjunction%2D%5F%2Dlink%2D%5F%2Dbuilder" title="SkypeOut">'.__('SkypeOut','skype-online-status').'</a>') ?></em></blockquote>

			<p><label for="user_name"><?php _e('Full Name', 'skype-online-status'); _e(': ', 'skype-online-status') ?></label><input type="text" style="width: 250px;" name="user_name" id="user_name" value="<?php echo stripslashes(htmlspecialchars($option['user_name'])) ?>" /> <a href="#" onclick="javascript:SwitchInfoBlock('username_info');return(false);">?</a></p>
			<blockquote id="username_info" style="display:none"><em><?php _e('Your full name as you want it to appear in Skype links, link-titles and image alt-texts on your blog.', 'skype-online-status') ?></em></blockquote>
		</fieldset>

		<fieldset class="options">
			<br /><h4><?php _e('Theme', 'skype-online-status') ?></h4>

			<p><?php printf(__('Start with <strong>selecting one of the predefined theme templates</strong> to load into the database. Hover over the options to see a preview. You might later select %1$s to edit the template in the text field under %2$s.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Custom Template', 'skype-online-status')."</strong>") ?></p>
			<p><label for="button_theme"><?php echo __('Theme', 'skype-online-status') . __(': ', 'skype-online-status') ?></label> <select name="button_theme" id="button_theme" onchange="ChangeStyle(this);" onblur="PreviewStyle(this);"><option value="custom_edit"<?php if ($option['button_theme'] == "custom_edit") echo " selected=\"selected\"" ?> onmouseover="PreviewStyle(this);" onmouseout="UnPreviewStyle(this);"><?php _e('Custom...', 'skype-online-status') ?></option><?php foreach ($walk['select'] as $key => $value) { echo "<option value=\"$value\""; if ($value == $option['button_theme']) { echo " selected=\"selected\""; } echo " onmouseover=\"PreviewStyle(this);\" onmouseout=\"UnPreviewStyle(this);\">$key</option>"; } unset($value) ?> </select> <a href="#" onclick="javascript:SwitchInfoBlock('theme_info');return(false);">?</a></p>
			<blockquote id="theme_info" style="display:none"><em><?php printf(__('When %1$s is selected, you can edit the template to your liking below at %2$s under %3$s. When you make changes to that field but select another theme template here, those changes will be overwriten by the new template!', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Customize currently loaded template', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Custom Template', 'skype-online-status')."</strong>") ?></em></blockquote>
			<p><?php printf(__('If you cannot find a suitable theme, check out <a href="http://www.skype.com/share/buttons/wizard.html" target="_blank">http://www.skype.com/share/buttons/wizard.html</a>. Select your options there and copy/paste the output into the textarea under %s.', 'skype-online-status'),"<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Display', 'skype-online-status')."</strong>") ?></p>
		</fieldset>

		<fieldset class="options">
			<br /><h4><?php _e('Function', 'skype-online-status') ?></h4>
			<p><?php _e('Some of the button themes will show your online status in an icon/image, without having a specific function assigned. This means you can select what the button should do when clicked by a visitor.', 'skype-online-status') ?></p>
			<p><label for="button_function"><?php _e('Function', 'skype-online-status'); _e(': ', 'skype-online-status') ?></label><select name="button_function" id="button_function">
				<?php foreach ($skype_avail_functions as $key => $value) {
				echo '<option value="'.$key.'"';
				if ( $option['button_function'] == $key ) echo ' selected="selected"';
				echo '>'.$value.'</option>
				'; } 
				unset($value) ?> 
			</select> <a href="#" onclick="javascript:SwitchInfoBlock('function_info');return(false);">?</a></p>
			<blockquote id="function_info" style="display:none"><em><?php _e('Note:', 'skype-online-status'); echo " "; printf(__('This setting will only be used in the \'%1$s\' theme templates, or in your own custom template when the tags %2$s and %3$s are used.', 'skype-online-status'),"My Status","{function}","{functiontxt}");?></em></blockquote>
		</fieldset>

		<p class="submit">
			<input type="submit" name="skype_status_update" value="<?php _e('Save Changes') ?> &raquo;" />
		</p>

		<p style="clear:both;padding-top:10px"><?php printf(__('If you have your basic settings correct and there is a Skype button visible on blog, you can fine-tune it\'s appearance and function with the advanced settings. Each option is annotated but you can read more in the %s section.', 'skype-online-status'),"<strong>".__('Quick Guide', 'skype-online-status')."</strong>") ?></p>

		<p style="text-align:right"><a href="#wphead"><?php _e('Top') ?></a></p>

	    </div> <!-- inside -->

	  </div> <!-- postbox -->

	  <div id="advanceddiv" class="postbox ">

	    <div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div><h3 class='hndle'><span><?php _e('Advanced Options', 'skype-online-status') ?></span></h3>

	    <div class="inside">

		<fieldset class="options">
			<h4><?php _e('Post content', 'skype-online-status') ?></h4>
			<p><?php printf(__('When writing posts you can insert a Skype button with a simple quicktag %1$s or %2$s but to make life even easier, a small button on the WYSIWYG editor can do it for you. Check this option to show %3$s or uncheck to hide it. You may still insert the quicktag  in the HTML code of your post or page content manually.', 'skype-online-status'),"<strong>&lt;!--skype status--&gt;</strong>","<strong>[-skype status-]</strong>","<img src=\"".SOSPLUGINURL."/skype_button.gif\" alt=\"".__('Skype Online Status', 'skype-online-status')."\" style=\"vertical-align:text-bottom;\" />") ?><br /><br />
			<input type="checkbox" name="use_buttonsnap" id="use_buttonsnap"<?php if ( $option['use_buttonsnap'] == "on") { print " checked=\"checked\""; } ?> /> <label for="use_buttonsnap"><?php _e('Use <strong>Skype Status quicktag button</strong> in the RTE for posts.','skype-online-status') ?></label></p>
		</fieldset>

		<fieldset class="options">
			<br /><h4><?php _e('Display &amp; Function', 'skype-online-status') ?></h4>
			<p><?php _e('These settings define which options should be used to replace their respective tag (if present) in the selected template file. If unchecked, the tags will be blanked out.','skype-online-status') ?></p> 
			<p><input type="checkbox" name="use_voicemail" id="use_voicemail"<?php if ( $option['use_voicemail'] == "on" ) { print " checked=\"checked\""; } ?> /> <label for="use_voicemail"><?php printf(__('Use %1$s in dropdown button. Leave unchecked if you do not have %2$s or %3$s.', 'skype-online-status'),"<strong>".__('Leave me voicemail', 'skype-online-status')."</strong>", '<a href="http://www.tkqlhce.com/click-3049686-10520919" target="_top">'.__('SkypeIn','skype-online-status').'</a><img src="http://www.ftjcfx.com/image-3049686-10520919" width="1" height="1" border="0"/>', '<a href="http://www.tkqlhce.com/click-3049686-10423078" target="_top">'.__('Skype Voicemail','skype-online-status').'</a>
<img src="http://www.ftjcfx.com/image-3049686-10423078" width="1" height="1" border="0"/>') ?></label>
			<br /><input type="checkbox" name="use_function" id="use_function"<?php if ( $option['use_function'] == "on" ) { print " checked=\"checked\""; } ?> /> <label for="use_function"><?php printf(__('Use %1$s (define below) for %2$s tags.', 'skype-online-status'),"<strong>".__('Action text', 'skype-online-status')."</strong>","{add/call/chat/userinfo/voicemail/sendfile}") ?></label>
			<br /><label for="use_status"><strong><?php _e('Status texts', 'skype-online-status') ?></strong> <?php printf(__('for the %s tag', 'skype-online-status'),"{status}"); _e(': ', 'skype-online-status') ?></label><select name="use_status" id="use_status">
						<option value=""<?php if ( $option['use_status'] == "" ) print " selected=\"selected\"" ?>><?php _e('Disabled', 'skype-online-status') ?></option>
						<option value="custom"<?php if ( $option['use_status'] == "custom" ) print " selected=\"selected\"" ?>><?php _e('Custom...', 'skype-online-status') ?></option>
						<?php foreach ($skype_avail_languages as $key => $value) {
						echo '<option value="'.$key.'"';
						if ( $option['use_status'] == $key ) echo ' selected="selected"';
						echo '>Skype default text in '.$value.'</option>
						'; } 
						unset($value) ?> 
					</select> <a href="#" onclick="javascript:SwitchInfoBlock('statustext_info');return(false);">?</a> 
			<blockquote id="statustext_info" style="display:none"><em><?php printf(__('If you select %1$s, the tags %2$s, %3$s and %4$s in the button template will be ignored.', 'skype-online-status'),"<strong>".__('Disabled', 'skype-online-status')."</strong>","{status}","{statustxt}","{sep2}"); 
						echo "<br />"; 
						printf(__('When security settings on your server are too tight (<strong>safe_mode</strong> enabled, <strong>open_basedir</strong> restictions or <strong>allow_url_fopen</strong> being disabled), your status does not show or the Skype button doesn\'t load and you find an error like "Warning: file_get_contents() [function.file-get-contents]: URL file-access is disabled in the server configuration..." in the server log files, use either %1$s or %2$s here.','skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Disabled', 'skype-online-status')."</strong>") ?></em></blockquote>
					<?php if (!SOSREMOTE) {
						echo "<br /><span style=\"color:red\">" . __('Note:', 'skype-online-status') . " " . __('The security settings on your server are too tight for the online status to be read from the Skype server.', 'skype-online-status') . " " . sprintf(__('It is advised to set %1$s to %2$s here untill this is fixed.', 'skype-online-status'),"<strong>".__('Status texts', 'skype-online-status')."</strong>","<strong>".__('Disabled', 'skype-online-status')."</strong>") . sprintf(__('You can also select %1$s but only the %2$s status text will be available.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Error', 'skype-online-status')."</strong>") . "</span>"; } ?></p>
				<p><br /><input type="checkbox" name="use_getskype" id="use_getskype"<?php if ( $option['use_getskype'] == "on") { print " checked=\"checked\""; } ?> /> <label for="use_getskype"><?php printf(__('Use %s link.', 'skype-online-status'),"<strong>".__('Download Skype now!','skype-online-status')."</strong>") ?></label>
					<br /><input type="checkbox" name="getskype_newline" id="getskype_newline"<?php if ( $option['getskype_newline'] == "on") { print " checked=\"checked\""; } ?> /> <label for="getskype_newline"><?php _e('Place link on a new line.', 'skype-online-status') ?></label><br />
					<label for="getskype_text"><?php _e('Link text', 'skype-online-status'); _e(': ', 'skype-online-status') ?></label><input type="text" name="getskype_text" style="width: 250px;" id="getskype_text" value="<?php echo stripslashes(htmlspecialchars($option['getskype_text'])) ?>" /><br />
					<label for="getskype_link"><?php _e('Link URL', 'skype-online-status'); _e(': ', 'skype-online-status') ?></label>
						<select name="getskype_link" id="getskype_link">
							<option value=""<?php if ( $option['getskype_link'] == "" ) print " selected=\"selected\"" ?>><?php _e('Default affiliate link', 'skype-online-status') ?></option>
							<option value="skype_mainpage"<?php if ( $option['getskype_link'] == "skype_mainpage" ) print " selected=\"selected\"" ?>><?php _e('Skype main page', 'skype-online-status') ?></option>
							<option value="skype_downloadpage"<?php if ( $option['getskype_link'] == "skype_downloadpage" ) print " selected=\"selected\"" ?>><?php _e('Skype download page', 'skype-online-status') ?></option>
							<option value="custom_link"<?php if ( $option['getskype_link'] == "custom_link" ) print " selected=\"selected\"" ?>><?php _e('Custom...', 'skype-online-status') ?></option>
						</select> <a href="#" onclick="javascript:SwitchInfoBlock('linkurl_info');return(false);">?</a> 
						<blockquote id="linkurl_info" style="display:none"><em><?php _e('Leave to Default if you are generous and think downloads should create some small possible revenue for the developer of this plugin -- that\'s me, thanks! :) -- but if you think open source developers are greedy bastards and should go away, select one of the other options -- just kidding, feel free... Really! You can always donate on the Notes & Live Support section ;).', 'skype-online-status'); printf(__('If you want to create your own link (say you have a Commission Junction, TradeDoubler or ValueCommerce account, read more on http://www.skype.com/partners/affiliate/) to get possible revenue from downloads yourself, select %1$s and paste the link code in the textarea under %2$s below.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Custom Download Link', 'skype-online-status')."</strong>") ?></em></blockquote></p>
		</fieldset>

		<fieldset class="options">
			<br /><h4><?php _e('Custom Status texts', 'skype-online-status') ?></h4>
			<p><?php printf(__('Text that will replace the %s tag depending on actual online status.', 'skype-online-status'),"{status}"); 

	if ($option['use_status'] != "custom") { 
	echo " " . sprintf(__('Please, change %1$s to %2$s under %3$s (above) for these options to become available first.', 'skype-online-status'),"<strong>".__('Status texts', 'skype-online-status')."</strong>","<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Display', 'skype-online-status')."</strong>");
	$status_readonly = " readonly=\"readonly\""; 
	$status_style = "color:grey;"; 
	} 
	?>
				</p>
				<table style="margin-left:5px;margin-top:10px">
					<tr>
						<th><?php _e('Status', 'skype-online-status') ?></th>
						<th><?php _e('Value', 'skype-online-status') ?></th>
						<th><?php _e('Text', 'skype-online-status') ?></th>
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
				unset($value) ?> 
					<tr>
						<td><label for="status_error_text"><?php _e('Error', 'skype-online-status') ?></label></td>
						<td><?php _e('(none)', 'skype-online-status') ?></td>
						<td><input type="text" name="status_error_text" id="status_error_text" value="<?php echo stripslashes(htmlspecialchars($option['status_error_text'])) ?>" /></td>
					</tr>
				</table>
		</fieldset>
		<fieldset class="options">
			<br /><h4><?php _e('Tag texts', 'skype-online-status') ?></h4>
			<p><?php _e('Define texts to replace their respective template tags relating to the Skype button action.', 'skype-online-status') ?></p> 
			<table style="margin-left:5px;margin-top:10px">
				<tr>
					<th><?php _e('Action', 'skype-online-status') ?></th>
					<th><?php _e('Tag', 'skype-online-status') ?></th>
					<th><?php _e('Text', 'skype-online-status') ?></th>
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
			unset($value) ?> 
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
				<table style="margin-left:5px;margin-top:10px">
					<tr>
						<th><?php _e('Other', 'skype-online-status') ?></th>
						<th><?php _e('Tag', 'skype-online-status') ?></th>
						<th><?php _e('Text', 'skype-online-status') ?></th>
					</tr>
					<tr>
						<td><label for="seperator1_text"><?php _e('First seperator', 'skype-online-status') ?></label></td>
						<td>{sep1}</td>
						<td><input type="text" name="seperator1_text" id="seperator1_text" value="<?php echo stripslashes(htmlspecialchars($option['seperator1_text'])) ?>"<?php echo $other_readonly ?> style="<?php echo $other_style ?>" /></td>
					</tr>
					<tr>
						<td><label for="my_status_text"><?php _e('My status', 'skype-online-status') ?></label></td>
						<td>{statustxt}</td>
						<td><input type="text" name="my_status_text" id="my_status_text" value="<?php echo stripslashes(htmlspecialchars($option['my_status_text'])) ?>"<?php echo $other_readonly ?> style="<?php echo $other_style ?>" /></td>
					</tr>
					<tr>
						<td><label for="seperator2_text"><?php _e('Second seperator', 'skype-online-status') ?></label></td>
						<td>{sep2}</td>
						<td><input type="text" name="seperator2_text" id="seperator2_text" value="<?php echo stripslashes(htmlspecialchars($option['seperator2_text'])) ?>"<?php echo $other_readonly ?> style="<?php echo $other_style ?>" /></td>
					</tr>
				</table>
		</fieldset>

		<fieldset class="options">
			<br /><h4><?php _e('Custom Template', 'skype-online-status') ?></h4>
			<p><?php printf(__('The currently selected template has been loaded into the database. You can edit it here if you like, but be sure to select %1$s under %2$s or your changes will be lost.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Basic Options', 'skype-online-status')." / ".__('Theme', 'skype-online-status')."</strong>") ?> <br /><br /><label for="button_template"><?php _e('Customize currently loaded template', 'skype-online-status'); _e(': ', 'skype-online-status') ?></label> <a href="#" onclick="javascript:SwitchInfoBlock('customtemplate_info');return(false);">?</a><br />
			<textarea name="button_template" id="button_template" style="width:98%;height:240px;" onchange="javascript:document.getElementById('button_theme').options[0].selected=true;document.getElementById(visible_preview).style.display='none';document.getElementById('custom_edit').style.display='block';visible_preview='custom_edit';"><?php echo stripslashes(htmlspecialchars($option['button_template'])) ?></textarea></p>
			<blockquote id="customtemplate_info" style="display:none"><em><?php _e('Available tags:','skype-online-status') ?> {skypeid} {username} {function} {functiontxt} {action} {add} {call} {chat} {userinfo} {voicemail} {sendfile} {status} {statustxt} {tag1} {tag2} <br /><?php _e('Available markers:','skype-online-status') ?> &lt;!-- voicemail_start --&gt; &lt;!-- voicemail_end --&gt; <br /><?php printf(__('See %s for more instructions.','skype-online-status'),"<strong>".__('Quick Guide', 'skype-online-status')."</strong>") ?><br /><span style="color:red"><?php printf(__('Oh, and did I mention this? Changes to the template will only be loaded when the option %1$s under %2$s is selected!', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Basic Options', 'skype-online-status')." / ".__('Theme', 'skype-online-status')."</strong>") ?></span></em></blockquote>
		</fieldset>

		<fieldset class="options">
			<br /><h4><?php _e('Custom Download Link', 'skype-online-status') ?></h4>
			<p><?php printf(__('If you are a <a href="http://www.skype.com/partners/affiliate/">Skype Affiliate</a> select %1$s under %2$s (above) and paste your link/banner code (HTML/Javascript) here.', 'skype-online-status'),"<strong>".__('Custom...', 'skype-online-status')."</strong>","<strong>".__('Advanced Options', 'skype-online-status')." / ".__('Display &amp; Function', 'skype-online-status')."</strong>") ?><br /><br /><label for="getskype_custom_link"><strong><?php _e('Link/Banner Code', 'skype-online-status'); _e(': ', 'skype-online-status') ?></strong></label><br /><textarea name="getskype_custom_link" id="getskype_custom_link" style="width:98%;height:100px;"><?php echo stripslashes(htmlspecialchars($option['getskype_custom_link'])) ?></textarea></p>
		</fieldset>

		<p class="submit">
			<input type="submit" name="skype_status_update" value="<?php _e('Save Changes') ?> &raquo;" />
		</p>

		<p style="clear:both;text-align:right;padding-top:10px"><a href="#wphead"><?php _e('Top') ?></a></p>

	    </div> <!-- inside -->
	  </div> <!-- postbox -->

	</div> <!-- settings -->

<?php include(SOSPLUGINDIR . '/skype-quickguide-notes.php') ?>

      </div>
    </div> <!-- post-body-content has-sidebar-content -->
  </div> <!--post-body has-sidebar -->

</div> <!-- poststuff metabox-holder has-right-sidebar -->
<?php wp_nonce_field('closedpostboxes','closedpostboxesnonce',false) ?>
<?php wp_nonce_field('meta-box-order','meta-box-order-nonce',false) ?>
</form>
</div> <!-- wrap -->

<script type="text/javascript">
var visible_preview = "<?php echo $option['button_theme'] ?>";

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

<script type="text/javascript">
	document.getElementById('loading').style.display='none';
	document.getElementById('notes').style.display='none'; 
	document.getElementById('guide').style.display='none'; 
</script>

<?php
}
?>
