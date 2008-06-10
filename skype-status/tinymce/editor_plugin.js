tinyMCE.importPluginLanguagePack('sosquicktag', 'en,nl');

var TinyMCE_SOSQuicktag = {
	getInfo : function() {
		return {
			longname : "Skype Online Status Quicktag",
			author : 'Ravan',
			authorurl : 'http://4visions.nl/',
			infourl : 'http://www.4visions.nl/en/index.php?section=55',
			version : '2.1.0'
		};
	},
	getControlHTML : function(cn) {
		switch (cn) {
			case 'sosquicktag':
					button = tinyMCE.getButtonHTML('skypeonlinestatus', 'lang_sosquicktag_title', '{$pluginurl}/../skype_button.gif', 'skypeonlinestatus');
					return button;
		}
		return '';
	},
	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case 'skypeonlinestatus':
				buttonsnap_settext('[-skype status-]');
				return true;
		}
		return false;
	},
};

var buttonsnap_markers = new Array( "skypeonlinestatus" );

var buttonsnap_classes = new Array( "skype_marker" );

tinyMCE.addPlugin('sosquicktag', TinyMCE_SOSQuicktag);