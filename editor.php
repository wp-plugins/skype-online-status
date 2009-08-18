<?php

// Add button for WordPress 2.5+ using built in hooks, thanks to Subscribe2
function sos_mce3_plugin($arr) {
	$path = SOSPLUGINURL . '/js/mce3_editor_plugin.js';
	$arr['sosquicktag'] = $path;
	return $arr;
}

function sos_mce3_button($arr) {
	$arr[] = 'sosquicktag';
	return $arr;
}
// Add button in WordPress v2.1+, thanks to An-archos
function sos_mce_plugin($plugins) {
	$plugins[] = '-sosquicktag';
	return $plugins;
}
function sos_mce_button($button) {
	$button[] = 'sosquicktag';
	return $button;
}
function sos_tinymce_before_init() {
	echo "tinyMCE.loadPlugin('sosquicktag', '" . SOSPLUGINURL . "/js/');\n"; 
}

// Hide buttons the user doesn't want to see in WP v2.1+
function sos_buttonhider() {
	global $skype_status_config;
	if ($skype_status_config['use_buttonsnap']!="on") {
		echo "<style type='text/css'>\n";
		echo "	#mce_editor_0_skypeonlinestatus { display: none; }\n";
		echo "</style>\n";
	}
}
	
function skype_button_init() {
	global $wp_db_version;

	if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
	
	// check for WP version (first 2.5+ then 2.1 then older) and activated RTE
	if ( $wp_db_version >= 6846 && 'true' == get_user_option('rich_editing') ) {
			add_filter('mce_external_plugins', 'sos_mce3_plugin');
			add_filter('mce_buttons', 'sos_mce3_button');
	} elseif ( 3664 <= $wp_db_version && 'true' == get_user_option('rich_editing') ) {
		add_filter('mce_plugins', 'sos_mce_plugin');
		add_filter('mce_buttons', 'sos_mce_button');
		add_action('tinymce_before_init', 'sos_tinymce_before_init');
		add_action('admin_head', 'sos_buttonhider');
	}
}

// Style a marker in the Rich Text Editor for the quicktag
function skype_button_css() {
	$skype_marker_url = SOSPLUGINURL . '/skype_marker.gif';
	echo "
		.skype_marker {
			display: block;
			height: 15px;
			width: 200px;
			margin-top: 5px;
			background-image: url({$skype_marker_url});
			background-repeat: no-repeat;
			background-position: center;
		}
	";
}
?>
