<?php
function skype_walk_templates($buttondir,$option_preview,$select,$previews,$use_js = TRUE) {
	if (is_dir($buttondir)) {
		if ($dh = opendir($buttondir)) {
			while (($file = readdir($dh)) !== false) {
				$fname = $buttondir . $file;
				if (is_file($fname) && ".html" == substr($fname,-5)) {

					$theme_name = substr(basename($fname),0,-5);

					// attempt to get the human readable name from the first line of the file
					$option_preview['button_template'] = file_get_contents($fname);
					preg_match("|<!-- (.*) - |ms",$option_preview['button_template'],$matches);
					if (!$matches[1] || $matches[1]=="")
						$matches[1] = $theme_name;

					// collect the options
					$select[$matches[1]] = $theme_name;
					
					// and collect their previews 
					$previews[$matches[1]] = array( $theme_name , skype_status($option_preview['skype_id'],$option_preview['user_name'],"",$option_preview['use_voicemail'],$option_preview['button_template'],$use_js) ) ;
				}
			}
			closedir($dh);
		}
	}
	if ( ksort($select) && ksort($previews))
		return array ( "select" => $select , "previews" => $previews );
	else
		return FALSE;
}

function skype_default_values() { 
	global $skype_default_values,$skype_avail_languages,$skype_avail_statusmsg,$skype_avail_functions;

	//build status texts
	foreach ($skype_avail_statusmsg as $key => $value) {
		$fullkey = "status_".$key."_text";
		$skype_default_values[$fullkey] = $value;
	}
	unset($value);

	//build function texts
	foreach ($skype_avail_functions as $key => $value) {
		$fullkey = $key."_text";
		$skype_default_values[$fullkey] = $value;
	}
	unset($value);

	// set language to blogs WPLANG (or leave unchanged)
	if (SOSALLOWURLFOPEN) {
		if (WPLANG=='') {
			$skype_default_values['use_status'] = "en";
		} else {
			$conv = strtolower(str_replace("_","-",WPLANG));
			$first_two = substr(WPLANG,0,2);
			foreach ($skype_avail_languages as $key) {
				if ( $conv == $key ) { // get full language/country match
					$skype_default_values['use_status'] = $key;
					break;
				} elseif ( $first_two == substr($key,0,2) ) { // or try to get language only match
					$skype_default_values['use_status'] = substr($key,0,2);
					break;
				}
			}
		}
	}

	if ($skype_default_values['button_theme']!="custom_edit") // get template file content to load into db
		$skype_default_values['button_template'] = skype_get_template_file($skype_default_values['button_theme']);

	return $skype_default_values;
}

// online status checker function
// needs allow_url_fopen to be enabled on your server (if not, see default settings)
function skype_status_check($skypeid, $format=".txt") {
	if (SOSALLOWURLFOPEN && $skypeid) { 
		if (SOSUSECURL) {
			$tmp = curl_get_file_contents('http://mystatus.skype.com/'.$skypeid.$format);
			define('SOSCURLFLAG', TRUE);
		} else { $tmp = file_get_contents('http://mystatus.skype.com/'.$skypeid.$format); }
		if ($tmp && $tmp!="") $contents = str_replace("\n", "", $tmp);
	}

        if ($contents) return $contents;
            else return FALSE;
}

// helper functions to make sure that only valid data gets into database
function skype_status_valid_id($id) {
	return preg_match("/^(\w|\.)*$/",$id);
}

function skype_status_valid_theme($theme) {
	return !preg_match("/\W/",$theme);
}

function skype_parse_theme($config,$use_js = TRUE) {
	global $skype_avail_functions;

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

	//define value to replace {functiontxt} based on {function}
	$functiontxt = $config[$config['button_function'].'_text'];

	// build array with tags and replacement values
	$tags_replace = array(
		"{skypeid}" => $config['skype_id'],
		"{function}" => $config['button_function'],
		"{functiontxt}" => $functiontxt,
		"{status}" => $status,
		"{statustxt}" => $config['my_status_text'],
		"{username}" => $config['user_name'],
		"{sep1}" => $config['seperator1_text'],
		"{sep2}" => $config['seperator2_text'],
		);
	//and append with function texts 
	foreach ($skype_avail_functions as $key => $value) {
		if ($config['use_function']!="on")
			$config[$key.'_text'] = "";
		$tags_replace["{".$key."}"] = $config[$key.'_text'];
	}

	// delete javascript from template if disabled
	if ($use_js == FALSE) {
		$config['button_template'] = preg_replace("|<script type=\"text\/javascript\" (.*)script>|","",$config['button_template']);
	}

	// delete voicemail lines if not needed else append arrays with tags and replacement values
	if ($config['use_voicemail']!="on") {
		$config['button_template'] = preg_replace("|<!-- voicemail_start (.*) voicemail_end -->|","",$config['button_template']);
	} else {
		$tags_replace["<!-- voicemail_start -->"] = "";
		$tags_replace["<!-- voicemail_end -->"] = "";
	}

	// after that, delete from first line <!-- (.*) -->
	$theme_output = preg_replace("|<!-- (.*) http://www.skype.com/go/skypebuttons -->|","",$config['button_template']);

	// replace all tags with values
	$theme_output = str_replace(array_keys($tags_replace),array_values($tags_replace),$theme_output);

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
	if ($r['use_js'] == "0" || $r['use_js'] == "no" )
		$r['use_js'] = FALSE;
	echo skype_status($r['skype_id'], $r['user_name'], $r['button_theme'], $r['use_voicemail'], "", $r['use_js']);
}

// main function
function skype_status($skype_id = FALSE, $user_name = FALSE, $button_theme = FALSE, $use_voicemail = FALSE, $button_template = FALSE, $use_js = TRUE) {
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
	if ($button_theme && $button_theme != "default") 
		$r['button_template'] = skype_get_template_file($button_theme);
	elseif ($r['button_template'] == "")
		$r['button_template'] = skype_get_template_file($r['button_theme']);

	// make sure there is a template from database or file else revert to basic plain-text fallback template
	if ($r['button_template'] == "") 
		$r['button_template'] = '<a href="skype:{skypeid}?call" onclick="return skypeCheck();" title="{call}{sep1}{username}{sep2}{status}">{username}{sep2}{status}</a>';		

	return '<!-- Skype button generated by Skype Online Status plugin version '.SOSVERSION.' ( RavanH - http://4visions.nl/ ) -->
' . skype_parse_theme( $r , $use_js ) . '
<!-- end Skype button -->'; 
}

// script in header
function skype_status_script() {
	global $skype_status_config;
	echo '
	<!-- Skype script used for Skype Online Status plugin version '.SOSVERSION.' by RavanH - http://4visions.nl/ -->
	';

	if ($skype_status_config['getskype_link'] == "skype_mainpage" || $skype_status_config['getskype_link'] == "skype_downloadpage")
		echo '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>';
	else
		echo '<script type="text/javascript" src="'.SOSPLUGINURL.'js/skypeCheck.js.php"></script>';

	echo '
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

////////// aditional PHP functions

//get file content using curl
if (!function_exists('curl_get_file_contents')) {
function curl_get_file_contents($URL) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }
}

//PHP 4.2.x Compatibility function
if (!function_exists('file_get_contents')) {
	function file_get_contents($filename, $incpath = false, $resource_context = null) {
	  if (false === $fh = fopen($filename, 'rb', $incpath)) {
	      trigger_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
	      return false;
	  }

	  clearstatcache();
	  if ($fsize = @filesize($filename)) {
	      $data = fread($fh, $fsize);
	  } else {
	      $data = '';
	      while (!feof($fh)) {
		  $data .= fread($fh, 8192);
	      }
	  }

	  fclose($fh);
	  return $data;
	}
}
?>
