<?php

/**
 * Skype widget class
 *
 * @since 2.8.4
 */
class Skype_Status_Widget extends WP_Widget {

	function Skype_Status_Widget() {
		$this->WP_Widget(
			'skype-status', 
			__('Skype Button', 'skype-online-status'),
			array(
				'classname' => 'skype_widget', 
				'description' => __('Add a Skype button', 'skype-online-status')
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

		$skype_args = ($instance['skype_id']) ? 'skype_id='.$instance['skype_id'].'&' : '';
		$skype_args .= ($instance['user_name']) ? 'user_name='.$instance['user_name'].'&' : '';
		$skype_args .= ($instance['use_voicemail']) ? 'use_voicemail='.$instance['use_voicemail'].'&' : '';
		$skype_args .= ($instance['button_template']) ? 'button_template='.stripslashes($instance['button_template']):'';

		echo $before_widget;
		if (!empty( $title ))
			echo $before_title . $title . $after_title;
		echo '<div class="skype-status-button">' . $before;
		echo Skype_Online_Status::skype_status($skype_args);
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
			$instance['button_template'] = stripslashes(Skype_Online_Status::get_template_file($new_instance['button_theme'])); 
		else 
			$instance['button_template'] = '';
		
		$instance['button_theme'] =  stripslashes($new_instance['button_theme']); 

		$instance['use_voicemail'] =  $new_instance['use_voicemail']; 

		return $instance;
	}

	function form( $instance ) {
		$defaults = array ( 
			'title' => __('Skype Button', 'skype-online-status'),	// Widget title
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
		<select class="select" id="<?php echo $this->get_field_id('button_theme'); ?>" name="<?php echo $this->get_field_name('button_theme'); ?>">
			<?php
				foreach ( Skype_Online_Status::$avail_colors as $key => $value ) { 
						echo "<option value=\"$value\""; if ($value == $instance['button_theme']) { echo " selected=\"selected\""; } echo ">{$key}&nbsp;</option>";
				}
				unset($value);
			?>
		</select></p>

		<p><label for="<?php echo $this->get_field_id('after'); ?>"><?php _e('Text after button', 'skype-online-status'); ?>**<?php _e(': ', 'skype-online-status'); ?></label>
		<textarea class="widefat" rows="2" cols="20" id="<?php echo $this->get_field_id('after'); ?>" name="<?php echo $this->get_field_name('after'); ?>"><?php echo $after; ?></textarea></p>

		<p style="clear:both;font-size:78%;font-weight:lighter;">* <?php _e('Leave blank to use the default options as you defined on the <a href="options-general.php?page=skype-status.php">Skype Online Status Settings</a> page.', 'skype-online-status'); //printf(__('Leave blank to use the default options as you defined on the %1$s page.', 'skype-online-status'), '<a href="'.admin_url('options-general.php?page='.SOSPLUGINBASENAME).'">'.__('Settings').'</a>'); ?><br />
		** <?php _e('You can use some basic HTML here like &lt;br /&gt; for new line.', 'skype-online-status'); ?><br />
		*** <?php printf(__('Set to %1$s if you do not have %2$s or %3$s.', 'skype-online-status'), __('No'), '<a href="https://support.skype.com/en/category/ONLINE_NUMBER_SKYPEIN/" target="_blank">'.__('SkypeIn','skype-online-status').'</a><img src="//www.ftjcfx.com/image-3049686-10520919" width="1" height="1" border="0"/>', '<a href="https://support.skype.com/en/category/VOICEMAIL/" target="_blank">'.__('Skype Voicemail','skype-online-status').'</a><img src="//www.ftjcfx.com/image-3049686-10423078" width="1" height="1" border="0"/>'); ?></p>
<?php
	}
}

/**
 * Skype class
 *
 * @since 2.8.5
 */

class Skype_Online_Status {

	/**
	* Plugin variables
	*/
	
	private static $script_done = false;

	private static $id = 0;

	private static $default_settings;
	
	protected static $default_properties = array(
				'name' => 'call',			// call, chat or dropdown
				'participants' => '',		// can be more than one for group chats (comma or ; seperated?)
				'imageSize' => 32, 			// between 10 and 32 (only 10, 12, 14, 16, 24 & 32 ?)
				'imageColor' => 'skype',	// skype (blue) or white
				'listParticipants' => true, // participant visibility in group chats
				'video' => true, 			// allow video call
				'topic' => '', 				// set topic for group chats
				'listTopic' => true, 		// topic visibility in group chats
				);

	public static $avail_functions;

	public static $avail_colors;

	public static $pagehook;

	public static $config;

	protected static $whats_new = "<p>
	* Legacy STATUS buttons are no longer supported by Skype/Microsoft. Forced move to new Skype Button.
	</p>";

	/**
	* Plugin functions
	*/

	public static function init() {
		// Internationalization
		add_action('plugins_loaded', array(__CLASS__, 'textdomain_init'));

		// Initialisation
		add_action('init', array(__CLASS__, 'skype_status_init'));
		add_action('widgets_init', array(__CLASS__, 'register_widget'));
	}
	
	public static function textdomain_init() {
		load_plugin_textdomain('skype-online-status', false, dirname(plugin_basename( __FILE__ )).'/languages');
	}

	public static function register_widget() {
		register_widget('Skype_Status_Widget');
	}
	
	public static function skype_status_init() {
		self::$config = get_option( 'skype_status', self::get_default_values() );

		// do stuff for admin ONLY when on the backend
		if ( is_admin() ) {

			// check for plugin upgrade
			if (self::$config['skype_status_version'] !== SOSVERSION) {
				// merge new default into old settings
				self::$config = array_merge (self::get_default_values(), self::$config);
				// update: populate db with missing values and set upgraded flag to true
				self::$config['skype_status_version'] = SOSVERSION;
				self::$config['upgraded'] = true;
				update_option('skype_status',self::$config);
			}

			// Shortcode button
			if (self::$config['use_buttonsnap']=="on" && current_user_can('edit_posts') && current_user_can('edit_pages')) {
				add_filter('mce_external_plugins', array(__CLASS__, 'mce3_plugin'));
				add_filter('mce_buttons', array(__CLASS__, 'mce3_button'), 99);
			}

			// create WP hooks
			add_filter('plugin_action_links_' . SOSPLUGINBASENAME, array(__CLASS__, 'add_action_link'));
			add_action('admin_menu', array(__CLASS__, 'add_menu'));
			add_filter('screen_layout_columns', array(__CLASS__, 'admin_layout_columns'), 10, 2);

		}
	
		add_shortcode('skype-status', array(__CLASS__, 'shortcode_callback'));

	}

	private static function get_default_values() { 
	
		if ( null === self::$default_settings ) {
			self::$default_settings = array(
				'skype_id' => 'echo123',
				'user_name' => __('Skype Test Call', 'skype-online-status'),
				'button_theme' => 'skype',		// "skype" (=blue) or "white"
				'button_size' => 32, 			// between 10 and 32 only ?
				'button_function' => 'call',	// Function to replace {function} in template files
//				'listParticipants' => true, //
//				'video' => true, //
//				'topic' => '', //
//				'listTopic' => true, //
				'use_buttonsnap' => 'on', 		// Wether to display a shortcode button in RTE for posts
												// ("on") or not ("")
				'use_getskype' => 'on', 		// Wether to show the Download Skype now! link
				'getskype_newline' => 'on',		// Put the Download Skype now! link on a new line ("on") or not ("")
				'getskype_text' => __('&raquo; Get Skype, call free!', 'skype-online-status'),
												// Text to use for the Download Skype now! link
				'getskype_link' => '',			// What link to use for download: the default ("") will generate some
												// revenue for me (thanks! :-) ), "skype_mainpage" for skype.com main page,
												// "skype_downloadpage" for skype.com download page
				'getskype_custom_link' => '',	// put your own customized link here
				'skype_status_version' => SOSVERSION,
				'upgraded' => false,
				'installed' => true,
			);

			self::$avail_functions = array (
				'call' => __('Call me!', 'skype-online-status'),
				'chat' => __('Chat with me', 'skype-online-status'),
				//'add' => __('Add me to Skype', 'skype-online-status'),
				//'userinfo' => __('View my profile', 'skype-online-status'),
				//'voicemail' => __('Leave me voicemail', 'skype-online-status'),
				//'sendfile' => __('Send me a file', 'skype-online-status'),
				'dropdown' => __('Dropdown', 'skype-online-status')
			);

			self::$avail_colors = array (
				'skype' => __('Skype blue', 'skype-online-status'),
				'white' => __('White', 'skype-online-status')
			);
	
		}

		return self::$default_settings;
	}

	// CORE
	
	public static function skype_status( $r = array() ) {
				
		$r = wp_parse_args( $r, self::$default_settings );
		
		if ( empty($r['skype_id']) )
			return '<!-- ' . __('Skype button disabled:', 'skype-online-status') . ' ' . __('Missing Skype ID.', 'skype-online-status') . '-->';

		// we've got an ID, let's do this...
		$return = '
<!-- Skype button generated by Skype Online Status plugin version '.SOSVERSION.' ( RavanH - http://status301.net/ ) -->';

		// add script and set flag
		if ( !self::$script_done ) {
			$return .= '
<script type="text/javascript" src="http://www.skypeassets.com/i/scom/js/skype-uri.js"></script>';
			self::$script_done = true;
		}

		$return .= '
<div id="SkypeButton_'.++self::$id.'">
<script type="text/javascript">
    Skype.ui({
      "name": "'.$r['button_function'].'",
      "element": "SkypeButton_'.self::$id.'",
      "participants": ["'.$r['skype_id'].'"],
      "imageColor": "'.$r['button_theme'].'",
      "imageSize": '.$r['button_size'].'
    });
</script>
</div>
';

		if ( isset($r['use_getskype']) && $r['use_getskype'] == "on" ) { 
				
			if ( !isset( $r['getskype_link'] ) )
				$r['getskype_link'] = '';
			if ( !isset( $r['getskype_text'] ) )
				$r['getskype_text'] = '';

			switch( $r['getskype_link'] ) {
				case 'skype_downloadpage' :
					$return .= " <a rel=\"nofollow\" href=\"http://www.skype.com/go/download\" title=\"{$r['getskype_text']}\">{$r['getskype_text']}</a>";
				case 'custom_link' :
					if ( !empty($r['getskype_custom_link']) )
						$return .= stripslashes( $r['getskype_custom_link'] );
				default :
					$return .= " <a rel=\"nofollow\" target=\"_blank\" href=\"http://status301.net/skype-online-status/go/download\" title=\"{$r['getskype_text']}\" onmouseover=\"window.status='http://www.skype.com/go/download';return true;\" onmouseout=\"window.status=' ';return true;\">{$r['getskype_text']}</a>";
			}
		}

		return $return;
	}

	// CONTENT SHORTCODE

	public static function shortcode_callback($atts, $content = null) {

		$r = shortcode_atts( array(
				'skype_id' => self::$config['skype_id'],
				//'user_name' => self::$config['user_name'],
				'button_theme' => self::$config['button_theme'],
				//'use_voicemail' => self::$config['use_voicemail'],
				'button_function' => self::$config['button_function'],
				'use_getskype' => self::$config['use_getskype'],
			), $atts );
		
		return self::skype_status($r);
	}

	// ADMIN HOOKS
	
	public static function add_menu() {

		/* Register our plugin page */
		self::$pagehook = add_submenu_page('options-general.php',__('Skype Button', 'skype-online-status'),__('Skype Button', 'skype-online-status'),'manage_options',SOSPLUGINFILE,array(__CLASS__, 'skype_options'));

		/* Using registered $page handle to hook script load */
		add_action('load-' . self::$pagehook, array(__CLASS__, 'scripts_admin'));
	}

	public static function skype_options() {
		
		add_filter( 'get_user_option_closedpostboxes_'.self::$pagehook, array(__CLASS__, 'closed_meta_boxes') );
		
		add_thickbox();
		
		//may be needed to ensure that a special box is always available
		add_meta_box('basicdiv', __('Basic Options', 'skype-online-status'), array(__CLASS__.'_Admin', 'meta_box_basic'), self::$pagehook, 'normal', 'high');
		add_meta_box('submitdiv', __('Sections','skype-online-status'), array(__CLASS__.'_Admin', 'meta_box_submit'), self::$pagehook, 'side', 'high');
		add_meta_box('supportdiv', __('Support','skype-online-status'), array(__CLASS__.'_Admin', 'meta_box_support'), self::$pagehook, 'side', 'core');
		add_meta_box('morediv', __('Get more from Skype','skype-online-status'), array(__CLASS__.'_Admin', 'meta_box_more'), self::$pagehook, 'normal', 'low');
		add_meta_box('resourcesdiv', __('Resources','skype-online-status'), array(__CLASS__.'_Admin', 'meta_box_resources'), self::$pagehook, 'side', 'low');

		//load admin page
		include(SOSPLUGINDIR . '/skype-admin.php');
	}

	public static function closed_meta_boxes( $closed ) {
		// set default closed metaboxes
		if ( false === $closed )
			$closed = array( 'advanceddiv', 'supportdiv', 'donationsdiv', 'resourcesdiv' );

		// remove closed setting of some metaboxes
		$closed = array_diff ( $closed , array ( 'morediv' ) );

		return $closed;
	}

	// Adds an action link to the Plugins page
	public static function add_action_link( $links ) {
		$settings_link = '<a href="' . admin_url('options-general.php?page='.SOSPLUGINFILE) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public static function scripts_admin($hook) {
	
		// needed javascripts to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script('common');
		wp_enqueue_script('wp-list');
		wp_enqueue_script('postbox');
	
		wp_enqueue_script('skypecheck');

		//add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore
		add_meta_box('advanceddiv', __('Advanced Options', 'skype-online-status'), array(__CLASS__.'_Admin', 'meta_box_advanced'), self::$pagehook, 'normal', 'core');
		add_meta_box('discussiondiv', __('Discussion'), array(__CLASS__.'_Admin', 'meta_box_discussion'), self::$pagehook, 'normal', 'low');
		add_meta_box('donationsdiv', __('Credits','skype-online-status'), array(__CLASS__.'_Admin', 'meta_box_donations'), self::$pagehook, 'side', 'default');

	}

	// Add button for WordPress 2.5+ using built in hooks, thanks to Subscribe2
	public static function mce3_plugin($arr) {
		$arr['sosquicktag'] = plugins_url( '/js/mce3_editor_plugin.js', SOSPLUGINBASENAME );
		return $arr;
	}

	public static function mce3_button($buttons) {
		array_push($buttons, "|", "sosquicktag");
		return $buttons;
	}

	// for WordPress 2.8 we have to tell, that we support 2 columns !
	public static function admin_layout_columns($columns, $screen) {
		if ($screen == self::$pagehook) {
			$columns[self::$pagehook] = 2;
		}
		return $columns;
	}

}
