<?php

// Add button for WordPress 2.5+ using built in hooks, thanks to Subscribe2
function sos_mce3_plugin($arr) {
	$arr['sosquicktag'] = SOSPLUGINURL . '/js/mce3_editor_plugin.js';
	return $arr;
}

function sos_mce3_button($buttons) {
	array_push($buttons, "|", "sosquicktag");
	return $buttons;
}
	
function skype_button_init() {
	global $skype_status_config;
	if ($skype_status_config['use_buttonsnap']!="on") return;

	if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;

	add_filter('mce_external_plugins', 'sos_mce3_plugin');
	add_filter('mce_buttons', 'sos_mce3_button', 99);
}

