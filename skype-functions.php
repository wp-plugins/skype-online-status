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
		return '<a href="skype:{skypeid}?call" title="{call}{sep1}{username}{sep2}{status}">{username}{sep2}{status}</a>';
}

// template tag hook
function get_skype_status($args = '') {
	echo skype_status($args, TRUE, FALSE);
}

// main function
function skype_status($r = '', $use_js = TRUE, $status = FALSE) {
	global $skype_status_config, $add_skype_status_script;
	$r = wp_parse_args( $r, $skype_status_config );

	if (!$r['skype_id'])
		return "<!-- " . __('Skype button disabled:', 'skype-online-status') . " " . __('Missing Skype ID.', 'skype-online-status') . "-->";

	$add_skype_status_script = true;

	// if alternate theme is set or no template in db, get it from template file and override
	if ($r['button_theme'] != $skype_status_config['button_theme'] || ($r['button_theme'] && !$r['button_template']) ) 
		$r['button_template'] = skype_get_template_file($r['button_theme']);

	return '<!-- Skype button generated by Skype Online Status plugin version '.SOSVERSION.' ( RavanH - http://4visions.nl/ ) -->
' . skype_parse_theme( $r , $use_js , $status ) . '
<!-- end Skype button -->'; 

}

// routine to render all template files based on one config
function skype_walk_templates( $buttondir, $option_preview, $select, $previews, $use_js = TRUE, $select_only = FALSE ) {
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
					
					// and collect their previews if...
					if (!$select_only)
						$previews[$matches[1]] = array( $template_name , 
							skype_parse_theme($option_preview,$use_js,$status) ) ; 
				}
			}
			closedir($dh);
		}
	}
	if ( !$select_only && ksort($select) && ksort($previews))
		return array ( "select" => $select , "previews" => $previews );
	elseif ( ksort($select) )
		return array ( "select" => $select );
	else
		return FALSE;
}

// skypeCheck script in footer
// http://scribu.net/wordpress/optimal-script-loading.html (the Jedi Knight way)
function skype_status_script() {
	global $skype_status_config, $add_skype_status_script;

	if ( ! $add_skype_status_script )
		return;

	if ($skype_status_config['getskype_link'] == "skype_mainpage" || $skype_status_config['getskype_link'] == "skype_downloadpage") {
		wp_register_script('skypecheck', 'http://download.skype.com/share/skypebuttons/js/skypeCheck.js', '', '2.0', true);
	} else {
		wp_register_script('skypecheck', SOSPLUGINURL.'/js/skypeCheck.js', '', SOSVERSION, true);
	}
	wp_print_scripts('skypecheck');
}

// wrapper function which calls the Skype Status button
function skype_status_callback($content) {
	if(strpos($content,'-skype status-'))
		$content = preg_replace('/(<p.*>)?(<!-|\[)?-skype status-(->|\])?(<\/p>)?/', skype_status(), $content);
	return $content;
}
function skype_status_shortcode_callback($atts) {
	global $skype_status_config;
	$r = shortcode_atts( array(
			'skype_id' => $skype_status_config['skype_id'],
			'user_name' => $skype_status_config['user_name'],
			'button_theme' => $skype_status_config['button_theme'],
			'use_voicemail' => $skype_status_config['use_voicemail'],
			'button_function' => $skype_status_config['button_function'],
			'use_getskype' => $skype_status_config['use_getskype'],
		), $atts );
	return skype_status($r);
}

// admin hooks
function skype_status_add_menu() {
	/* Register our plugin page */
	$page = add_submenu_page('options-general.php',__('Skype Online Status', 'skype-online-status'),__('Skype Status', 'skype-online-status'),'manage_options',SOSPLUGINFILE,'skype_status_options');
	/* Using registered $page handle to hook script load */
        add_action('admin_print_scripts-' . $page, 'skype_status_scripts_admin');
}

function skype_status_scripts_admin($hook) {
	global $skype_status_config;

	//if ( $hook == 'settings_page_'.SOSPLUGINFILE ) {
	if ($skype_status_config['getskype_link'] == "skype_mainpage" || $skype_status_config['getskype_link'] == "skype_downloadpage") {
		wp_register_script('skypecheck', 'http://download.skype.com/share/skypebuttons/js/skypeCheck.js', '', '2.0', true);
	} else {
		wp_register_script('skypecheck', SOSPLUGINURL.'/js/skypeCheck.js', '', SOSVERSION, true);
	}
	
	//wp_register_script('jquery.postmessage', SOSPLUGINURL.'/js/jquery.ba-postmessage.min.js', 'jquery', '0.5', true);

	wp_enqueue_script('skypecheck');
	//wp_enqueue_script('jquery.postmessage');
	wp_enqueue_script('postbox');
	wp_enqueue_script('dashboard');
	wp_enqueue_style('dashboard');
	//}
}

function skype_status_add_action_link( $links, $file ) {
	static $this_plugin;

	if ( empty($this_plugin) ) $this_plugin = SOSPLUGINFILE;

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . admin_url('options-general.php?page='.SOSPLUGINFILE) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
	}
 
	return $links;
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


// SHORTCODE TINYMCE FUNCTIONS

// Add button for WordPress 2.5+ using built in hooks, thanks to Subscribe2
function sos_mce3_plugin($arr) {
	$arr['sosquicktag'] = SOSPLUGINURL . '/js/mce3_editor_plugin.js';
	return $arr;
}

function sos_mce3_button($buttons) {
	array_push($buttons, "|", "sosquicktag");
	return $buttons;
}



/**
 * Skype widget class
 *
 * @since 2.8.4
 */
class Skype_Status_Widget extends WP_Widget {

	function Skype_Status_Widget() {
		$this->WP_Widget(
			'skype-status', 
			__('Skype Status', 'skype-online-status'),
			array(
				'classname' => 'skype_widget', 
				'description' => __('Add a Skype Online Status button', 'skype-online-status')
			), 
			array(
				'width' => 370, 
				'id_base' => 'skype-status'
			)
		);
		
		// attempt upgrade from pre 2.8.4 widgets
		if ( $options = get_option('skype_widget_options') ) {
			$options['_multiwidget'] = 1;
			update_option('widget_skype-status', $options);
			delete_option('skype_widget_options');
		}
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		$before = apply_filters('widget_text', $instance['before'], $instance);
		$after = apply_filters('widget_text', $instance['after'], $instance);

		$skype_args = skype_build_args($instance);

		echo $before_widget;
		if (!empty( $title ))
			echo $before_title . $title . $after_title;
		echo '<div class="skype-status-button">' . $before;
		echo skype_status($skype_args);
		echo $after . '</div>' . $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['skype_id'] =  trim(strip_tags(stripslashes($new_instance['skype_id']))); 
		$instance['user_name'] =  trim(strip_tags(stripslashes($new_instance['user_name']))); 

		if ( current_user_can('unfiltered_html') ) {
			$instance['before'] =  $new_instance['before'];
			$instance['after'] =  $new_instance['after'];
		} else {
			$instance['before'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['before']) ) ); // wp_filter_post_kses() expects slashed
			$instance['after'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['after']) ) ); // wp_filter_post_kses() expects slashed
		}

		if ( $new_instance['button_theme'] != '' ) // then get template file content to load into db
			$instance['button_template'] = stripslashes(skype_get_template_file($instance['button_theme'])); 
		else 
			$instance['button_template'] = '';
		
		$instance['button_theme'] =  stripslashes($new_instance['button_theme']); 

		$instance['use_voicemail'] =  $new_instance['use_voicemail']; 

		return $instance;
	}

	function form( $instance ) {
		$defaults = array ( 
			'title' => __('Skype Status', 'skype-online-status'),	// Widget title
			'skype_id' => '',			// Skype ID to replace {skypeid} in template files
			'user_name' => '',			// User name to replace {username} in template files
			'button_theme' => '',			// Theme to be used, value must match a filename (without extention) from the /plugins/skype_status/templates/ directory or leave blank
			'button_template' => '',		// Template of the theme loaded
			'use_voicemail' => '',			// Wether to use the voicemail invitation ("on") or not ("off") or leave to default ("")
			'before' => '',				// text that should go before the button
			'after' => '',				// text that should go after the button
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags($instance['title']);
		$user_name = strip_tags($instance['user_name']);
		$before = format_to_edit($instance['before']);
		$after = format_to_edit($instance['after']);

		$walk = skype_walk_templates("", $instance, "", "", FALSE, TRUE); // get list of templates
?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('skype_id'); ?>"><?php _e('Skype ID', 'skype-online-status'); ?>*<?php _e(': ', 'skype-online-status'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('skype_id'); ?>" name="<?php echo $this->get_field_name('skype_id'); ?>" type="text" value="<?php echo $instance['skype_id']; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('user_name'); ?>"><?php _e('Full Name', 'skype-online-status'); ?>*<?php _e(': ', 'skype-online-status'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('user_name'); ?>" name="<?php echo $this->get_field_name('user_name'); ?>" type="text" value="<?php echo esc_attr($user_name); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('before'); ?>"><?php _e('Text before button', 'skype-online-status'); ?>**<?php _e(': ', 'skype-online-status'); ?></label>
		<textarea class="widefat" rows="2" cols="20" id="<?php echo $this->get_field_id('before'); ?>" name="<?php echo $this->get_field_name('before'); ?>"><?php echo $before; ?></textarea></p>

		<p><label for="<?php echo $this->get_field_id('button_theme'); ?>"><?php _e('Theme', 'skype-online-status'); ?><?php _e(': ', 'skype-online-status'); ?></label>
		<select class="select" id="<?php echo $this->get_field_id('button_theme'); ?>" name="<?php echo $this->get_field_name('button_theme'); ?>"><option value=""<?php if ($instance['button_theme'] == '') echo " selected=\"selected\"" ?>><?php _e('Default', 'skype-online-status') ?>&nbsp;</option><?php foreach ($walk['select'] as $key => $value) { echo "<option value=\"$value\""; if ($value == $instance['button_theme']) { echo " selected=\"selected\""; } echo ">$key&nbsp;</option>"; } unset($value) ?></select></p>

		<p><label for="<?php echo $this->get_field_id('after'); ?>"><?php _e('Text after button', 'skype-online-status'); ?>**<?php _e(': ', 'skype-online-status'); ?></label>
		<textarea class="widefat" rows="2" cols="20" id="<?php echo $this->get_field_id('after'); ?>" name="<?php echo $this->get_field_name('after'); ?>"><?php echo $after; ?></textarea></p>

		<p><label for="<?php echo $this->get_field_id('use_voicemail'); ?>"><?php _e('Use Voicemail?', 'skype-online-status'); ?></label>*** 
		<select class="select" id="<?php echo $this->get_field_id('use_voicemail'); ?>" name="<?php echo $this->get_field_name('use_voicemail'); ?>"><option value=""<?php if ($instance['use_voicemail'] == '') echo " selected=\"selected\"" ?>><?php _e('Default', 'skype-online-status'); ?></option><option value="on"<?php if ($instance['use_voicemail'] == 'on') echo " selected=\"selected\"" ?>><?php _e('Yes'); ?></option><option value="off"<?php if ($instance['use_voicemail'] == 'off') echo " selected=\"selected\"" ?>><?php _e('No'); ?></option></select></p>



<p style="clear:both;font-size:78%;font-weight:lighter;">* <?php _e('Leave blank to use the default options as you defined on the <a href="options-general.php?page=skype-status.php">Skype Online Status Settings</a> page.', 'skype-online-status'); ?><br />
** <?php _e('You can use some basic HTML here like &lt;br /&gt; for new line.', 'skype-online-status'); ?><br />
*** <?php printf(__('Set to %1$s if you do not have %2$s or %3$s.', 'skype-online-status'), __('No'), '<a href="http://www.tkqlhce.com/click-3049686-10520919" target="_top">'.__('SkypeIn','skype-online-status').'</a><img src="http://www.ftjcfx.com/image-3049686-10520919" width="1" height="1" border="0"/>', '<a href="http://www.tkqlhce.com/click-3049686-10423078" target="_top">'.__('Skype Voicemail','skype-online-status').'</a><img src="http://www.ftjcfx.com/image-3049686-10423078" width="1" height="1" border="0"/>'); ?></p>


<?php
	}
}

