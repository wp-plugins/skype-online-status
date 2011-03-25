/*
 * $Id: mce3_editor_plugin.js 201 2008-02-12 15:56:56Z RavanH $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('sosquicktag');

	tinymce.create('tinymce.plugins.SOSQuicktagPlugin', {
		init : function(ed, url) {

			// Register buttons
			ed.addButton('sosquicktag', {
				title : 'sosquicktag.title', 
				image : url + '/../skype_button.gif', 
				onclick : function() {
					var m = '';
					var skypeId = prompt("1/6 Your Skype ID\r\n\r\nEnter an alternative Skype ID or leave empty to use the default.", "");
            				if (skypeId == null)
            					return;
            				if (skypeId != 'undefined')
            					var m = ' skype_id="'+skypeId+'"';
            				
					var userName = prompt("2/6 Your Name\r\n\r\nEnter an alternative name or leave empty to use the default.", "");
            				if (userName == null)
            					return;
            				if (userName != 'undefined')
            					var m = m+' user_name="'+userName+'"';
            				
					var buttonTheme = prompt("3/6 Skype Button Theme\r\n\r\nTo use an alternative template, enter any template file name (without the .html extention) from /wp-content/plugins/skype-online-status/templates/ or leave empty to use the default.", "");
            				if (buttonTheme == null)
            					return;
            				if (buttonTheme != 'undefined')
            					var m = m+' button_theme="'+buttonTheme+'"';
            
					var buttonFunction = prompt("4/6 Skype Button Function\r\n\r\nEnter an alternative function like 'call', 'add', 'chat', 'userinfo', 'voicemail' or 'sendfile' (without the quotes) or leave empty to use the default.", "");
            				if (buttonFunction == null)
            					return;
            				if (buttonFunction != 'undefined')
            					var m = m+' button_function="'+buttonFunction+'"';
            
					var useVoicemail = prompt("5/6 Use 'Leave a voicemail' Link\r\n\r\nYou can force enable or force disable it. NOTE: The link is only available in some multi-function themes. Enter 'on' or 'off' (without the quotes) or leave empty to use the default.", "");
            				if (useVoicemail == null)
            					return;
            				if (useVoicemail != 'undefined')
            					var m = m+' use_voicemail="'+useVoicemail+'"';
            
					var useGetSkype = prompt("6/6 Use 'Get Skype' Link\r\n\r\nForce enable or disable display of the Get Skype link below or after the button. Enter 'on' or 'off' (without the quotes) or leave blank to use default.", "");
            				if (useGetSkype == null)
            					return;
            				if (useGetSkype != 'undefined')
            					var m = m+' use_getskype="'+useGetSkype+'"';
            
                			ed.execCommand('mceInsertContent', false, '[skype-status'+m+']');
				}
			});

		},
		
		createControl : function(n, cm) {
            		return null;
        	},

		getInfo : function() {
			return {
				longname : "Skype Online Status Quicktag",
				author : 'RavanH',
				authorurl : 'http://4visions.nl/',
				infourl : 'http://www.4visions.nl/en/wordpress-plugins/skype-online-status/',
				version : '2.8'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('sosquicktag', tinymce.plugins.SOSQuicktagPlugin);
})();
