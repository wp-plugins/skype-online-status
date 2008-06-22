(function() {
	tinymce.create('tinymce.plugins.SOSQuicktagPlugin', {
		init : function(ed, url) {
			var pb = '<img src="' + url + '/../spacer.gif" class="mceSOSQuicktag mceItemNoResize" />', cls = 'mceSOSQuicktag', sep = ed.getParam('separator', '<!--skype status-->'), pbRE;

			pbRE = new RegExp(sep.replace(/[\?\.\*\[\]\(\)\{\}\+\^\$\:]/g, function(a) {return '\\' + a;}), 'g');

			// Register commands
			ed.addCommand('mceSOSQuicktag', function() {
				ed.execCommand('mceInsertContent', 0, pb);
			});

			// Register buttons
			ed.addButton('sosquicktag', {title : 'Insert Skype Online Status Quicktag', image : url + '/../skype_button.gif', cmd : cls});

			ed.onInit.add(function() {
				ed.dom.loadCSS(url + "/sosquicktag.css");

				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG' && ed.dom.hasClass(o.node, cls))
							o.name = 'sosquicktag';
					});
				}
			});

			ed.onClick.add(function(ed, e) {
				e = e.target;

				if (e.nodeName === 'IMG' && ed.dom.hasClass(e, cls))
					ed.selection.select(e);
			});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('sosquicktag', n.nodeName === 'IMG' && ed.dom.hasClass(n, cls));
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(pbRE, pb);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="mceSOSQuicktag') !== -1)
							im = sep;

						return im;
					});
			});
		},

		getInfo : function() {
			return {
				longname : "Skype Online Status Quicktag",
				author : 'RavanH',
				authorurl : 'http://4visions.nl/',
				infourl : 'http://www.4visions.nl/en/index.php?section=55',
				version : '3.1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('sosquicktag', tinymce.plugins.SOSQuicktagPlugin);
})();