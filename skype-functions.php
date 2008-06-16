<?php
function skype_default_values() { 
	global $skype_default_values, $skype_avail_languages;
	// set language to blogs WPLANG (or leave unchanged)
	if (WPLANG=='') {
		$skype_default_values['use_status'] = "en";
	} else {
		$conv = strtolower(str_replace("_","-",WPLANG));
		$first_two = substr(WPLANG,0,2);
		foreach ($skype_avail_languages as $value) {
			if ( $conv == $value ) { // get full language/country match
				$skype_default_values['use_status'] = $value;
				break;
			} elseif ( $first_two == substr($value,0,2) ) { // or try to get language only match
				$skype_default_values['use_status'] = substr($value,0,2);
				break;
			}
		}
		unset($value);
	}
	if ($skype_default_values['button_theme']!="custom_edit") // get template file content to load into db
		$skype_default_values['button_template'] = skype_get_template_file($skype_default_values['button_theme']);
	return $skype_default_values;
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
	if (!is_array($r))
		return '';

	// check and override predefined config with args
	if ($skype_id) $r['skype_id'] = $skype_id;
	if ($user_name) $r['user_name'] = $user_name;
	if ($use_voicemail) $r['use_voicemail'] = $use_voicemail;
	if ($button_template) $r['button_template'] = $button_template;

	// if alternate theme is set, get it from template file and override
	if ($button_theme) 
		$r['button_template'] = skype_get_template_file($button_theme);
	elseif ($r['button_template'] == "")
		$r['button_template'] = skype_get_template_file($r['button_theme']);

	// make sure there is a template from database or file else revert to basic plain-text fallback template
	if ($r['button_template'] == "") 
		$r['button_template'] = '<a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{call}{sep1}{username}{sep2}{status}">{username}{sep2}{status}</a>';		

	return '<!-- Skype button generated by Skype Online Status plugin version '.SOSVERSION.' ( RavanH - http://4visions.nl/ ) -->
' . skype_parse_theme($r) . '
<!-- end Skype button -->'; 
}

// script in header
function skype_status_script() {
	print '
	<!-- Skype script used for Skype Online Status plugin version '.SOSVERSION.' by RavanH - http://4visions.nl/ -->
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

?>