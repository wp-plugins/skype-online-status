<?php
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
	if (SOSREMOTE) {
		if (WPLANG=='') {
			$skype_default_values['use_status'] = "en";
		} else {
			$conv = strtolower(str_replace("_","-",WPLANG));
			$first_two = substr(WPLANG,0,2);
			foreach ($skype_avail_languages as $key => $value) {
				if ( $conv == $key ) { // get full language/country match
					$skype_default_values['use_status'] = $key;
					break;
				} elseif ( $first_two == $key ) { // or try to get language only match
					$skype_default_values['use_status'] = $key;
					break;
				}
			}
		}
	} else { $skype_default_values['use_status'] = ""; }

	if ($skype_default_values['button_theme']!="custom_edit") // get template file content to load into db
		$skype_default_values['button_template'] = skype_get_template_file($skype_default_values['button_theme']);

	return $skype_default_values;
}

// online status checker function
function skype_status_check($skypeid=false, $format=".txt") {
	if (!$skypeid) return 'error';
 
	$tmp = wp_remote_fopen('http://mystatus.skype.com/'.$skypeid.$format);
	if (!$tmp) return 'error';
	else $contents = str_replace("\n", "", $tmp);

        if ($contents!="") return $contents;
        else return 'error';
}

// helper functions to make sure that only valid data gets into database
function skype_status_valid_id($id) {
	return preg_match("/^(\w|\.)*$/",$id);
}

function skype_status_valid_theme($theme) {
	return !preg_match("/\W/",$theme);
}

function skype_parse_theme($config, $use_js = TRUE, $status = FALSE) {
	global $skype_avail_functions;

	// get online status to replace {status} tag
	if ($config['use_status']=="") {
		$status = "";
		$config['my_status_text'] = "";
		$config['seperator2_text'] = "";
	} elseif (!$status) {
		if ($config['use_status']=="custom") {
			$num = skype_status_check($config['skype_id'], ".num");
			$status = $config['status_'.$num.'_text'];
		} else {
			$status = skype_status_check($config['skype_id'], ".txt.".$config['use_status']);
		}
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

	// make sure there is a template, else revert to basic plain-text fallback template
	if (!$config['button_template']) 
		$config['button_template'] = '<a href="skype:{skypeid}?call" title="{call}{sep1}{username}{sep2}{status}">{username}{sep2}{status}</a>';		

	// replace all tags with values
	$theme_output = str_replace(array_keys($tags_replace),array_values($tags_replace),$theme_output);

	if ($config['use_getskype'] == "on") { 
		if ($config['getskype_newline'] == "on") 
			$theme_output .= "<br />";

		if ($config['getskype_link'] == "skype_mainpage")
			$theme_output .= " <a href=\"http://www.skype.com\" title= \"".$config['getskype_text']."\">".$config['getskype_text']."</a>";
		elseif ($config['getskype_link'] == "skype_downloadpage")
			$theme_output .= " <a href=\"http://www.skype.com/go/download\" title= \"".$config['getskype_text']."\">".$config['getskype_text']."</a>";
		elseif ($config['getskype_link'] == "custom_link" && $config['getskype_custom_link'] != "" )
			$theme_output .= $config['getskype_custom_link'];
		else
			$theme_output .= " <a href=\"http://4visions.nl/skype-online-status/go/download\" title=\"".$config['getskype_text']."\" onmouseover=\"window.status='http://www.skype.com/go/download';return true;\" onmouseout=\"window.status=' ';return true;\">".$config['getskype_text']."</a>";
		}
	return str_replace(array("\r\n", "\n\r", "\n", "\r", "%0D%0A", "%0A%0D", "%0D", "%0A"), "", $theme_output);
}

function skype_get_template_file($filename) { // check template file existence and return content
	$buttondir = SOSPLUGINDIR."/templates/";
	if ($filename != "" && file_exists($buttondir.$filename.".html")) 
		return file_get_contents($buttondir.$filename.".html");
	else 
		return "";
}

// template tag hook
function get_skype_status($args = '') {
	echo skype_status($args, TRUE, FALSE);
}

// main function
function skype_status($r = '', $use_js = TRUE, $status = FALSE) {
	global $skype_status_config;
	$r = wp_parse_args( $r, $skype_status_config );

	if (!$r['skype_id'])
		return "<!-- " . __('Skype button disabled:', 'skype-online-status') . " " . __('Missing Skype ID.', 'skype-online-status') . "-->";

	// if alternate theme is set or no template in db, get it from template file and override
	if ($r['button_theme'] != $skype_status_config['button_theme'] || ($r['button_theme'] && !$r['button_template']) ) 
		$r['button_template'] = skype_get_template_file($r['button_theme']);

	return '<!-- Skype button generated by Skype Online Status plugin version '.SOSVERSION.' ( RavanH - http://4visions.nl/ ) -->
' . skype_parse_theme( $r , $use_js , $status ) . '
<!-- end Skype button -->'; 
}

// routine to render all template files based on one config
function skype_walk_templates($buttondir, $option_preview, $select, $previews, $use_js = TRUE) {
	global $skype_status_config;
	$option_preview = wp_parse_args( $option_preview, $skype_status_config );

	// default dir
	if (!$buttondir) $buttondir = SOSPLUGINDIR.'/templates/';

	if (is_dir($buttondir)) {
		// do online status check once
		if ($option_preview['use_status']=="") {
			$status = "";
		} else {
			if ($option_preview['use_status']=="custom") {
				$num = skype_status_check($option_preview['skype_id'], ".num");
				$status = $option_preview['status_'.$num.'_text'];
			} else {
				$status = skype_status_check($option_preview['skype_id'], ".txt.".$option_preview['use_status']);
			}
		}

		// go through the template files in the given directory
		if ($dh = opendir($buttondir)) {
			while (($file = readdir($dh)) !== false) {
				$fname = $buttondir . $file;
				if (is_file($fname) && ".html" == substr($fname,-5)) {

					$template_name = substr(basename($fname),0,-5);

					// attempt to get the human readable name from the first line of the file
					$option_preview['button_template'] = file_get_contents($fname);
					preg_match("|<!-- (.*) - |ms",$option_preview['button_template'],$matches);
					if (!$matches[1])
						$matches[1] = $template_name;

					// collect the options
					$select[$matches[1]] = $template_name;
					
					// and collect their previews 
					$previews[$matches[1]] = array( $template_name , 
						skype_parse_theme($option_preview,$use_js,$status) ) ; 
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

// skypeCheck script in footer
function skype_status_script() {
	global $skype_status_config;
	if ($skype_status_config['getskype_link'] == "skype_mainpage" || $skype_status_config['getskype_link'] == "skype_downloadpage")
		echo '
<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>';
	else
		echo '
<script type="text/javascript" src="'.SOSPLUGINURL.'/js/skypeCheck.js"></script>';
}

// wrapper function which calls the Skype Status button
function skype_status_callback($content) {
	if(strpos($content,'-skype status-')) {
		$content = preg_replace('/(<p.*>)?(<!-|\[)?-skype status-(->|\])?(<\/p>)?/', skype_status(), $content);
	}
	return $content;
}

// admin hooks
function skype_status_add_menu() {
	global $wp_version;
	if (function_exists('add_options_page')) {
		add_options_page(__('Skype Online Status', 'skype-online-status'),__('Skype Status', 'skype-online-status'),'manage_options',SOSPLUGINFILE,'skype_status_options');
	}
}

function skype_status_scripts_admin($hook) {
	if ( $hook == 'settings_page_'.SOSPLUGINFILE ) {
		wp_enqueue_script('postbox');
		wp_enqueue_script('dashboard');
		wp_enqueue_style('dashboard');
	}
}

/* initialization REMOVE
function skype_status_install() {
	global $skype_status_config;

	$skype_status_config = skype_default_values();
	$skype_status_config['installed'] = TRUE;
	add_option('skype_status',$skype_status_config);
} */

function skype_status_add_action_link( $links, $file ) {
	static $this_plugin;

	if ( empty($this_plugin) ) $this_plugin = SOSPLUGINFILE;

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . admin_url('options-general.php?page='.SOSPLUGINFILE) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
	}
 
	return $links;
}

// WIDGET FUNCTIONS

function skype_status_widget ($args, $widget_args = 1) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('skype_widget_options');
	if (!isset($options[$number]))
		return;

	$title = apply_filters('widget_title', $options[$number]['title']);
	$before = apply_filters('widget_text', $options[$number]['before']);
	$after = apply_filters('widget_text', $options[$number]['after']);

	$args = skype_build_args($options[$number]);

	echo $before_widget;
	if (!empty( $title ))
		echo $before_title . $title . $after_title;
	echo "<div class=\"skype-status-button\">";
	echo $before;
	echo skype_status($args);
	echo $after;
	echo "</div>";
	echo $after_widget;
}

function skype_widget_register() {
	if ( !$options = get_option('skype_widget_options') )
		$options = array();

	if ( isset( $options['title'] ) )
		$options = skype_widget_upgrade();

	$widget_ops = array( 'classname' => 'skype_widget', 'description' => "Skype Online Status button" );
	$control_ops = array('width' => 600, 'id_base' => 'skype-status');

	$name = "Skype";

	$registered = false;
	foreach ( array_keys($options) as $o ) {
		if ( !isset($options[$o]['widget_id']) )
			continue;
		$id = "skype-status-$o";
		$registered = true;
		wp_register_sidebar_widget( $id, $name, 'skype_status_widget', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'skype_widget_options', $control_ops, array( 'number' => $o ) );
	}

	if ( !$registered ) {
		wp_register_sidebar_widget( 'skype-status-1', $name, 'skype_status_widget', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'skype-status-1', $name, 'skype_widget_options', $control_ops, array( 'number' => -1 ) );
	}
}

function skype_widget_upgrade() {
	$options = get_option('skype_widget_options');
	if ( !isset( $options['title'] ) ) 
		return $options;

	$newoptions = array( 1 => $options );

	update_option( 'skype_widget_options', $newoptions );

	$sidebars_widgets = get_option( 'sidebars_widgets' );
	if ( is_array( $sidebars_widgets ) ) {
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( is_array( $widgets ) ) {
				foreach ( $widgets as $widget )
					$new_widgets[$sidebar][] = ( $widget == 'Skype Status' ) ? 'skype-status-1' : $widget;
			} else {
				$new_widgets[$sidebar] = $widgets;
			}
		}
		if ( $new_widgets != $sidebars_widgets )
			update_option( 'sidebars_widgets', $new_widgets );
	}

	return $newoptions;
}

function skype_build_args($options) {
	// build args (except button_theme !) 
	if ($options['skype_id'])
		$args = "skype_id=".$options['skype_id']."&";
	if ($options['user_name'])
		$args .= "user_name=".$options['user_name']."&";
	if ($options['use_voicemail'])
		$args .= "use_voicemail=".$options['use_voicemail']."&";
	if ($options['button_template'])
		$args .= "button_template=".stripslashes($options['button_template']);
	return $args;
}

?>
