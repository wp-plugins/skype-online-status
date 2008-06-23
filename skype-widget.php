<?php
function skype_status_widget ($args) {
	global $skype_widget_config;
	if (!is_array($skype_widget_config))
		return;
	extract($args);
	extract($skype_widget_config);

	echo $before_widget;
	if ($title)
		echo $before_title . $title . $after_title;
	echo "<div class=\"skype-status\">";
	echo stripslashes($before);
	echo skype_status($skype_id,$user_name,"",$use_voicemail,$button_template);
	echo stripslashes($after);
	echo "</div>";
	echo $after_widget;
}

function skype_widget_options () {
	global $skype_widget_default, $skype_widget_config, $skype_status_config;
	$opt = $skype_widget_config;
	if (!is_array ($opt)) 
		$opt = $skype_widget_default_values;

	// get list of templates
	$walk = skype_walk_templates(dirname(__FILE__)."/templates/", $opt, "", "",FALSE);

	if ($_POST['skype_widget_submit']) {
		if ($_POST['skype_widget_button_theme']=="default")
			$_POST['skype_widget_button_theme']="";
		if ($_POST['skype_widget_button_theme']!="") // get template file content to load into db
			$opt['button_template'] = stripslashes( skype_get_template_file($_POST['skype_widget_button_theme']) );
		else $opt['button_template'] = ""; 

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
var visible_preview = "<?php echo $opt['button_theme']; ?>";

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
<style type="text/css"><!-- .no_underline a { border-bottom:none;width:auto;height:100px;overflow:hidden } --></style>
<div class="no_underline"><?php echo skype_status($skype_widget_config['skype_id'],$skype_widget_config['user_name'],"",$skype_widget_config['use_voicemail'],$skype_widget_config['button_template'],FALSE); ?></div>
<label for="skype_widget_title">Widget Title:</label><br />
<input style="width:100%" type="text" id="skype_widget_title" name="skype_widget_title" value="<?php echo stripslashes(htmlspecialchars($opt['title'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_before">Text before (use &lt;br /&gt; for new line):</label><br />
<input style="width:100%" type="text" id="skype_widget_before" name="skype_widget_before" value="<?php echo stripslashes(htmlspecialchars($opt['before'])); ?>" />
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
<label for="skype_widget_after">Text after (use &lt;br /&gt; for new line):</label><br />
<input style="width:100%" type="text" id="skype_widget_after" name="skype_widget_after" value="<?php echo stripslashes(htmlspecialchars($opt['after'])); ?>" />
</p>
<p style="text-align:left">
<label for="skype_widget_use_voicemail">Use Voicemail**:</label> <select name="skype_widget_use_voicemail" id="skype_widget_use_voicemail">
<option value=""<?php if ($opt['use_voicemail'] == "") { print " selected=\"selected\""; } ?>>Default</option>
<option value="on"<?php if ($opt['use_voicemail'] == "on") { print " selected=\"selected\""; } ?>>Always on</option>
<option value="off"<?php if ($opt['use_voicemail'] == "off") { print " selected=\"selected\""; } ?>>Always off</option></select>
</label> 
</p>
<p style="text-align:left">
<label for="skype_widget_button_theme">Theme:</label> <select name="skype_widget_button_theme" id="skype_widget_button_theme" style="width:100%" onchange="ChangeStyle(this);" onblur="PreviewStyle(this);">
<option value="default"<?php if ($opt['button_theme'] == "") echo " selected=\"selected\""; ?> onmouseover="PreviewStyle(this);" onmouseout="UnPreviewStyle(this);">Default</option>
<?php foreach ($walk['select'] as $key => $value) { echo "<option value=\"$value\""; if ($value == $opt['button_theme']) { echo " selected=\"selected\""; } echo " onmouseover=\"PreviewStyle(this);\" onmouseout=\"UnPreviewStyle(this);\">$key</option>
"; } unset($value); ?></select>
</p>
<p><strong>Preview:</strong></p>
<div class="no_underline" style="margin-bottom:10px;"><div id="default" style="display:<?php if ($opt['button_theme'] == '') echo 'block'; else echo 'none' ?>"><div style="height:32px">Default</div><?php echo skype_parse_theme($skype_status_config,FALSE); ?></div>
<?php foreach ($walk['previews'] as $key => $value) { echo "<div id=\"$value[0]\" style=\"display:"; if ($value[0] == $opt['button_theme']) echo "block"; else echo "none"; echo "\"><div style=\"height:38px;border-bottom:1px dotted grey;margin:0 0 5px 0\">$key</div>$value[1]</div>
"; } unset($value); ?></div>
<p style="font-size:78%;font-weight:lighter;">* Leave blank to use default options as defined on the <a href="options-general.php?page=skype-status.php">Skype Online Status Settings</a> page.<br />
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
