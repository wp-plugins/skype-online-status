<?php
function skype_status_widget ($args) {
	$opt = get_option('skype_widget_options');
	if (!is_array($opt))
		return;
	extract($args);
	extract($opt);

	if (!$title)
		$title = "Skype Online Status";

	echo $before_widget;
	echo $before_title . $title . $after_title;
	echo "<div class=\"skype-status\">";
	echo stripslashes($before);
	echo skype_status($skype_id,$user_name,"",$use_voicemail,$button_template);
	echo stripslashes($after);
	echo "</div>";
	echo $after_widget;
}

function skype_widget_options () {
	global $skype_widget_default;
	$opt = get_option ('skype_widget_options');
	if (!is_array ($opt)) 
		$opt = $skype_widget_default_values;

	// get list of templates
	$buttondir = dirname(__FILE__)."/templates/";
	$select = "<option value=\"default\"";
	if ($opt['button_theme'] == "") {
		$display = " block";
		$select .= " selected=\"selected\"";
	} else { $display = " none"; }
	$select .= ">Default</option>";

	$previews = "<div id=\"default\" style=\"display:$display;\">".skype_parse_theme($opt)."</div>";

	$walk = skype_walk_templates(dirname(__FILE__)."/templates/", $opt, $select, $previews);
	$select = $walk['select'];
	$previews = $walk['previews'];

	if ($_POST['skype_widget_submit']) {
		if ($_POST['skype_widget_button_theme']!="") { // get template file content to load into db
			$opt['button_template'] = stripslashes( skype_get_template_file($_POST['skype_widget_button_theme']) );
		} else { $opt['button_template'] = ""; }

		$opt['title'] = $_POST['skype_widget_title'];
		$opt['skype_id'] = $_POST['skype_widget_skype_id'];
		$opt['user_name'] = $_POST['skype_widget_user_name'];
		$opt['button_theme'] = $_POST['skype_widget_button_theme'];
		$opt['use_voicemail'] = $_POST['skype_widget_use_voicemail'];
		$opt['before'] = $_POST['skype_widget_before'];
		$opt['after'] = $_POST['skype_widget_after'];

		update_option('skype_widget_options', $opt);
	} ?>

<script type="text/javascript">
var visible_preview = "<?php echo $option['button_theme']; ?>";

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
</script>

<p style="text-align:left">
<label for="skype_widget_title">Widget Title:</label><br />
<input style="width:100%" type="text" id="skype_widget_title" name="skype_widget_title" value="<?php echo stripslashes(htmlspecialchars($opt['title'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_skype_id">Skype ID*:</label><br />
<input style="width:100%" type="text" id="skype_widget_skype_id" name="skype_widget_skype_id" value="<?php echo stripslashes(htmlspecialchars($opt['skype_id'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_user_name">Username*:</label><br />
<input style="width:100%" type="text" id="skype_widget_user_name" name="skype_widget_user_name" value="<?php echo stripslashes(htmlspecialchars($opt['user_name'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_before">Text before (use &lt;br /&gt; for new line):</label><br />
<input style="width:100%" type="text" id="skype_widget_before" name="skype_widget_before" value="<?php echo stripslashes(htmlspecialchars($opt['before'])); ?>" />
</p>
<style type="text/css"><!-- .no_underline a { border-bottom:none;width:auto;height:100px;overflow:hidden } --></style>	
<div class="no_underline"><?php echo $previews; ?></div>
<p style="text-align:left">
<label for="skype_widget_button_theme">Theme:</label> <select name="skype_widget_button_theme" id="skype_widget_button_theme" style="width:100%" onchange="ChangeStyle(this);" onfocus="PreviewStyle(this);"><?php echo $select; ?></select>
</p>
<p style="text-align:left">
<label for="skype_widget_use_voicemail">Use Voicemail**:</label> <select name="skype_widget_use_voicemail" id="skype_widget_use_voicemail">
<option value=""<?php if ($opt['use_voicemail'] == "") { print " selected=\"selected\""; } ?>>Default</option>
<option value="on"<?php if ($opt['use_voicemail'] == "on") { print " selected=\"selected\""; } ?>>Always on</option>
<option value="off"<?php if ($opt['use_voicemail'] == "off") { print " selected=\"selected\""; } ?>>Always off</option></select>
</label> 
</p>
<p style="text-align:left">
<label for="skype_widget_after">Text after (use &lt;br /&gt; for new line):</label><br />
<input style="width:100%" type="text" id="skype_widget_after" name="skype_widget_after" value="<?php echo stripslashes(htmlspecialchars($opt['after'])); ?>" />
</p>
<p style="font-size:78%;font-weight:lighter;">* Leave blank to use default options as defined on the Skype Status Options page.<br />
** Leave to <em>Always off</em> if you do not have a SkypeIn account or SkypeVoicemail.</p>
<input type="hidden" id="skype_widget_submit" name="skype_widget_submit" value="1" />

<?php 
}

function skype_add_widget () {
	if (function_exists ('register_sidebar_widget')) {
		register_sidebar_widget ('Skype Status','skype_status_widget');
		register_widget_control ('Skype Status','skype_widget_options');
	}
}

?>