<?php
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

function skype_widget_options ($widget_args = 1) {
	global $skype_widget_default_values, $wp_registered_widgets;
	static $updated = false; 

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('skype_widget_options');
	if (!is_array ($options)) 
		$options = array();

	$args = skype_build_args($options[$number]);

	// disable get skype now! link in preview
	$skype_widget_default_values['use_getskype'] = "";
	$options[$number]['use_getskype'] = "off";
	$args .= "&use_getskype=";

	if (!$updated && !empty($_POST['sidebar'])) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'skype_status_widget' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "skype-status-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed. 
 					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['skype_widget'] as $widget_number => $widget_opt ) {
			if ( !isset($widget_opt['title']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;

			if ($widget_opt['button_theme']!="") // then get template file content to load into db
				$widget_opt['button_template'] = stripslashes(skype_get_template_file($widget_opt['button_theme'])); 
			else 
				$widget_opt['button_template'] = ""; 

			if ( !current_user_can('unfiltered_html') ) {
				$widget_opt['before'] = wp_filter_post_kses( $widget_opt['before'] );
				$widget_opt['after'] = wp_filter_post_kses( $widget_opt['after'] );
			}

			$options[$widget_number] = array ( 
				'widget_id' => $widget_number,
				'title' => trim(strip_tags(stripslashes($widget_opt['title']))),
				'skype_id' => trim(strip_tags(stripslashes($widget_opt['skype_id']))),
				'user_name' => trim(strip_tags(stripslashes($widget_opt['user_name']))),
				'button_theme' => stripslashes($widget_opt['button_theme']),
				'button_template' => $widget_opt['button_template'],
				'use_voicemail' => $widget_opt['use_voicemail'],
				'before' => stripslashes($widget_opt['before']),
				'after' => stripslashes($widget_opt['after'])
				);
		}
		unset($widget_opt);

		update_option('skype_widget_options', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		extract( $skype_widget_default_values[$number], EXTR_SKIP );
		$walk = skype_walk_templates("", $skype_widget_default_values, "", "", FALSE); // get list of templates
		$number = '%i%';
	} else {
		extract( $options[$number], EXTR_SKIP );
		foreach ($options[$number] as $key => $value) { if(!$value) unset($options[$number][$key]); }
		$walk = skype_walk_templates("", $options[$number], "", "", FALSE); // get list of templates
	}
?>

<div style="width:48%;float:right">
	<?php _e('Theme', 'skype-online-status'); ?><?php _e(': ', 'skype-online-status'); ?>
	<div id="-<?php echo $number; ?>" style="margin:0;padding:0;height:380px;overflow:auto;border:1px solid #ddd"> 
		<div style="margin:5px 0;padding-top:5px;<?php if ($button_theme == '') echo 'background-color:#efefef' ?>"<label><div style="margin-left:20px"><?php echo skype_status($args,FALSE); ?></div><input type="radio" name="skype_widget[<?php echo $number; ?>][button_theme]" value=""<?php if ($button_theme == "") echo " checked=\"checked\""; ?>><?php _e('Default', 'skype-online-status'); ?></label></div> 

		<?php foreach ($walk['previews'] as $key => $value) { echo "<div style=\"margin:5px 0;padding-top:5px;"; if ($value[0] == $button_theme) { echo "background-color:#eee"; } echo "\"><label><div style=\"margin-left:20px\">$value[1]</div><input type=\"radio\" name=\"skype_widget[$number][button_theme]\" value=\"$value[0]\""; if ($value[0] == $button_theme) { echo " checked=\"checked\""; } echo ">$key</label></div> 
	"; } unset($value); ?>
	</div>
</div>

<div style="width:48%;float:left;">
<p style="text-align:left">
<label for="skype_widget_title-<?php echo $number; ?>"><?php _e('Title'); ?>:</label><br />
<input class="widefat" type="text" id="skype_widget_title-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][title]" value="<?php echo stripslashes(htmlspecialchars($title)); ?>" />
</p>

<p style="text-align:left">
<label for="skype_widget_skype_id-<?php echo $number; ?>"><?php _e('Skype ID', 'skype-online-status'); ?>*<?php _e(': ', 'skype-online-status'); ?></label>
<input class="widefat" type="text" id="skype_widget_skype_id-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][skype_id]" value="<?php echo stripslashes(htmlspecialchars($skype_id)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_user_name-<?php echo $number; ?>"><?php _e('Full Name', 'skype-online-status'); ?>*<?php _e(': ', 'skype-online-status'); ?></label>
<input class="widefat" type="text" id="skype_widget_user_name-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][user_name]" value="<?php echo stripslashes(htmlspecialchars($user_name)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_before-<?php echo $number; ?>"><?php _e('Text before button', 'skype-online-status'); ?>**<?php _e(': ', 'skype-online-status'); ?></label>
<input class="widefat" type="text" id="skype_widget_before-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][before]" value="<?php echo stripslashes(htmlspecialchars($before)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_after-<?php echo $number; ?>"><?php _e('Text after button', 'skype-online-status'); ?>**<?php _e(': ', 'skype-online-status'); ?></label>
<input class="widefat" type="text" id="skype_widget_after-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][after]" value="<?php echo stripslashes(htmlspecialchars($after)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_use_voicemail-<?php echo $number; ?>"><?php _e('Use Voicemail?', 'skype-online-status'); ?></label>*** <select class="select" name="skype_widget[<?php echo $number; ?>][use_voicemail]" id="skype_widget_use_voicemail-<?php echo $number; ?>">
<option value=""<?php if ($use_voicemail == "") print " selected=\"selected\""; ?>><?php _e('Default', 'skype-online-status'); ?></option>
<option value="on"<?php if ($use_voicemail == "on") print " selected=\"selected\""; ?>><?php _e('Yes'); ?></option>
<option value="off"<?php if ($use_voicemail == "off") print " selected=\"selected\""; ?>><?php _e('No'); ?></option></select>
</label> 
</p>
<p style="clear:both;font-size:78%;font-weight:lighter;">* <?php _e('Leave blank to use the default options as you defined on the <a href="options-general.php?page=skype-status.php">Skype Online Status Settings</a> page.', 'skype-online-status'); ?><br />
** <?php _e('You can use some basic HTML here like &lt;br /&gt; for new line.', 'skype-online-status'); ?><br />
*** <?php printf(__('Set to %s if you do not have a SkypeIn account or SkypeVoicemail.', 'skype-online-status'), __('No')); ?></p>
</div>

<div style="clear:both">
<input type="hidden" id="skype_widget_submit-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][submit]" value="1" /></div>

<?php 
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

function skype_add_widget () {
	// used for WP < 2.5
	if (function_exists ('register_sidebar_widget')) {
		register_sidebar_widget ('Skype Status','skype_status_widget');
		register_widget_control ('Skype Status','skype_widget_options');
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
	// build args
	if ($options['skype_id'])
		$args = "skype_id=".$options['skype_id']."&";
	if ($options['user_name'])
		$args .= "user_name=".$options['user_name']."&";
	if ($options['use_voicemail'])
		$args .= "use_voicemail=".$options['use_voicemail']."&";
	if ($options['button_template'])
		$args .= stripslashes($options['button_template']);
	return $args;
}
?>
