<?php
function skype_status_widget ($args, $widget_args = 1) {
	global $skype_widget_config;
	extract($args, EXTR_SKIP);
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	extract( $widget_args, EXTR_SKIP );

	if (!isset($skype_widget_config[$number]))
		return;

	echo $before_widget;
	if ($skype_widget_config[$number]['title'])
		echo $before_title . $skype_widget_config[$number]['title'] . $after_title;
	echo "<div class=\"skype-status-button\">";
	echo stripslashes($skype_widget_config[$number]['before']);
	echo skype_status($skype_widget_config[$number]['skype_id'],$skype_widget_config[$number]['user_name'],"",$skype_widget_config[$number]['use_voicemail'],stripslashes($skype_widget_config[$number]['button_template']));
	echo stripslashes($skype_widget_config[$number]['after']);
	echo "</div>";
	echo $after_widget;
}

function skype_widget_options ($widget_args = 1) {
	global $skype_widget_default_values, $skype_widget_config, $skype_status_config, $wp_registered_widgets;
	static $updated = false; 

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = $skype_widget_config;

	if (!is_array ($options)) 
		$options = array();

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

		update_option('skype_widget_options', $options);
		$updated = true;

	}

	if ( -1 == $number ) {
		$walk = skype_walk_templates(dirname(__FILE__)."/templates/", $skype_widget_default_values, "", "",FALSE); // get list of templates
		$title = $skype_widget_default_values['title'];
		$skype_id = $skype_widget_default_values['skype_id'];
		$user_name = $skype_widget_default_values['user_name'];
		$button_theme = $skype_widget_default_values['button_theme'];
		$button_template = $skype_widget_default_values['button_template'];
		$use_voicemail = $skype_widget_default_values['use_voicemail'];
		$before = $skype_widget_default_values['before'];
		$after = $skype_widget_default_values['after'];
		$number = '%i%';
	} else {
		$walk = skype_walk_templates(dirname(__FILE__)."/templates/", $options[$number], "", "",FALSE); // get list of templates
		$title = $options[$number]['title'];
		$skype_id = $options[$number]['skype_id'];
		$user_name = $options[$number]['user_name'];
		$button_theme = $options[$number]['button_theme'];
		$button_template = $options[$number]['button_template'];
		$use_voicemail = $options[$number]['use_voicemail'];
		$before = $options[$number]['before'];
		$after = $options[$number]['after'];
	}
?>

<script type="text/javascript">
var visible_preview = "<?php echo $button_theme; ?>";

function ChangeStyle(el) {
	eval("document.getElementById('" + visible_preview + "-<?php echo $number; ?>').style.display='none'");
	eval("document.getElementById('" + el.value + "-<?php echo $number; ?>').style.display='block'");
	visible_preview = el.value;
}

function PreviewStyle(elmnt) {
	eval("document.getElementById('" + visible_preview + "-<?php echo $number; ?>').style.display='none'");
	eval("document.getElementById('" + elmnt.value + "-<?php echo $number; ?>').style.display='block'");
}

function UnPreviewStyle(elmnt) {
	eval("document.getElementById('" + elmnt.value + "-<?php echo $number; ?>').style.display='none'");
	eval("document.getElementById('" + visible_preview + "-<?php echo $number; ?>').style.display='block'");
}
</script>

<p style="text-align:left">
<label for="skype_widget_title-<?php echo $number; ?>"><?php _e('Title'); ?>:</label><br />
<input class="widefat" type="text" id="skype_widget_title-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][title]" value="<?php echo stripslashes(htmlspecialchars($title)); ?>" />
</p>
<div class="no_underline" style="width:34%;float:right;margin:10px 0;">
<style type="text/css"><!-- .no_underline a { border-bottom:none;width:auto;height:100px;overflow:hidden } --></style>
<?php echo stripslashes($before); ?>
<div id="-<?php echo $number; ?>" style="display:<?php if ($button_theme == '') echo 'block'; else echo 'none' ?>;margin:0;padding:0"><?php echo skype_status($skype_id,$user_name,"",$use_voicemail,"",FALSE); ?></div>
<?php foreach (array_values($walk['previews']) as $value) { echo "<div id=\"$value[0]-$number\" style=\"display:"; if ($value[0] == $button_theme) echo "block"; else echo "none"; echo ";margin:0;padding:0\">$value[1]</div>
"; } unset($value); ?>
<?php echo stripslashes($after); ?>
</div>
<div style="width:64%;float:left;">
<p style="text-align:left">
<label for="skype_widget_skype_id-<?php echo $number; ?>">Skype ID:</label>* 
<input class="widefat" type="text" id="skype_widget_skype_id-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][skype_id]" value="<?php echo stripslashes(htmlspecialchars($skype_id)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_user_name-<?php echo $number; ?>">Username:</label>* 
<input class="widefat" type="text" id="skype_widget_user_name-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][user_name]" value="<?php echo stripslashes(htmlspecialchars($user_name)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_before-<?php echo $number; ?>">Text before button:</label>** 
<input class="widefat" type="text" id="skype_widget_before-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][before]" value="<?php echo stripslashes(htmlspecialchars($before)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_after-<?php echo $number; ?>">Text after button:</label>** 
<input class="widefat" type="text" id="skype_widget_after-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][after]" value="<?php echo stripslashes(htmlspecialchars($after)); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_use_voicemail-<?php echo $number; ?>">Use Voicemail?</label>*** <select class="select" name="skype_widget[<?php echo $number; ?>][use_voicemail]" id="skype_widget_use_voicemail-<?php echo $number; ?>">
<option value=""<?php if ($use_voicemail == "") print " selected=\"selected\""; ?>>Default</option>
<option value="on"<?php if ($use_voicemail == "on") print " selected=\"selected\""; ?>><?php _e('Yes'); ?></option>
<option value="off"<?php if ($use_voicemail == "off") print " selected=\"selected\""; ?>><?php _e('No'); ?></option></select>
</label> 
</p>
<p style="text-align:left">
<label for="skype_widget_button_theme-<?php echo $number; ?>">Theme:</label> <select name="skype_widget[<?php echo $number; ?>][button_theme]" id="skype_widget_button_theme-<?php echo $number; ?>" onchange="ChangeStyle(this);" onblur="PreviewStyle(this);">
<option value=""<?php if ($button_theme == "") echo " selected=\"selected\""; ?> onmouseover="PreviewStyle(this);" onmouseout="UnPreviewStyle(this);">Default</option>
<?php foreach ($walk['select'] as $key => $value) { echo "<option value=\"$value\""; if ($value == $button_theme) { echo " selected=\"selected\""; } echo " onmouseover=\"PreviewStyle(this);\" onmouseout=\"UnPreviewStyle(this);\">$key</option>
"; } unset($value); ?></select>
</p>
</div>
<p style="clear:both;font-size:78%;font-weight:lighter;">* Leave blank to use the default options as you defined on the <a href="options-general.php?page=skype-status.php">Skype Online Status Settings</a> page.<br />
** You can use some basic HTML here like &lt;br /&gt; for new line.<br />
*** Leave to <em>Always off</em> if you do not have a SkypeIn account or SkypeVoicemail.</p>
<input type="hidden" id="skype_widget_submit-<?php echo $number; ?>" name="skype_widget[<?php echo $number; ?>][submit]" value="1" />

<?php 
}

function skype_widget_register() {
	global $skype_widget_config;
	if ( !$skype_widget_config )
		$skype_widget_config = array();

	if ( isset( $skype_widget_config['title'] ) )
		$skype_widget_config = skype_widget_upgrade();

	$widget_ops = array( 'classname' => 'skype_widget', 'description' => "Skype Online Status button" );
	$control_ops = array('width' => 600, 'id_base' => 'skype-status');

	$name = "Skype Status";

	$id = false;
	foreach ( array_keys($skype_widget_config) as $o ) {
		if ( !isset($skype_widget_config[$o]['widget_id']) )
			continue;
		$id = "skype-status-$o";
		wp_register_sidebar_widget( $id, $name, 'skype_status_widget', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'skype_widget_options', $control_ops, array( 'number' => $o ) );
	}

	if ( !$id ) {
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
	global $skype_widget_config;
	if ( !isset( $skype_widget_config['title'] ) ) {
		return $skype_widget_config;
}

	$newoptions = array( 1 => $skype_widget_config );

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
?>
