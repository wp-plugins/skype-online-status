VERSION

This readme comes packed with version 2.2.0 beta of the 'Skype Online Status' plugin for WordPress 2+.


ABOUT

The plugin Skype Online Status allows you to add multiple, highly customizable and accessible Skype buttons to your blog. It was built upon the neat little plugin Skype Button v2.01 by Anti Veeranna. Many thanks!

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

It has been developed for WordPress 2+. Report bugs to Ravan skype-status@4visions.nl


WHAT's NEW

Since version 1.1 there have been some major changes to the layout of the 'Options > Skype Status' page. Documentation has been extended. And some new features:
- some new tags for the template files,
- a 'Reset options' button to revert to original settings,
- the <!--skype status--> tag to display a Skype Status button in posts and page content!
- editable template
- language switch for Skype default status texts


INSTALLATION

- when upgrading from version 1.7 or lower: deactivate plugin and delete old directory /skype_status/ and all files in it,
- unzip new version and upload directory /skype-status/ and all the files in it to your WordPress /plugins/ folder,
- go to your Plug-ins section in your WordPress admin area and activate the plugin 'Skype Online Status',
- go to the Options section and open page Skype Status to manage settings and read instructions on usage and new features...


NOTES
		
	Wish List version 3 and beyond :)
		- Make multiple Skype ID's with own settings possible
		- Internationalization
		- Get XML online status (and local time?)
	
	Revision History
		[2006-09-04] version 2.2.1: minor changes to admin page
		[2006-07-28] version 2.2.0: used global string for speed improvement
		[2006-07-05] version 2.1.0: added Skype default status texts in different languages
		[2006-07-04] version 2.0.1: minor bugfix (altered defaulting to fallback template procedure)
		[2006-06-30] version 2.0: added editable template and live support link
		[2006-06-29] version 1.9: added RTE guicktag button for <!--skype status--> hook
		[2006-06-27] version 1.8: improved performance by loading template in database
		[2006-06-23] version 1.7: added post hook <!--skype status--> and appended instructions to quickguide
		[2006-06-23] version 1.6: wrote templating guide and redesigned the admin interface
		[2006-06-22] version 1.5: added plain text fallback template to core code
		[2006-06-22] version 1.4: added reset button and default settings
		[2006-06-21] version 1.3: added new template tags {username} {sep1} {sep2}
		[2006-06-20] version 1.2: minor bugfixes
			1. inconsistent options page form-labels 
			2. skype_status_check not defaulting to status_error_txt when mystatus.skype.com is off-line 
		[2006-05-02] version 1.1: added new text template file
		[2006-04-26] version 1.0: wrote instructions (quick guide)
		[2006-04-20] version 0.9: added skype user name
		[2006-04-12] version 0.8: added customizability for get_skype_status('options')
		[2006-04-10] version 0.7: redesign admin interface
		[2006-03-05] version 0.3 - 0.6: added lot's of new settings and template tags
		[2006-03-03] version 0.2: added function skype_parse_theme() and skype_status_check()
		[2006-03-03] version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)

