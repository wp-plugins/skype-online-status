=== Skype Online Status ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Skype%20Online%20Status&item_number=2%2e6%2e1%2e0&no_shipping=0&no_note=1&tax=0&currency_code=EUR&lc=NL&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: skype, skype button, online, status, online status, widget, widgets, post, posts, quicktag, tinymce
Requires at least: 2.0
Tested up to: 2.5.1
Stable tag: 2.6.1.2

Checks your Skype Online Status and allows you to add multiple, highly customizable and accessible Skype buttons to your blog through widget, post quicktag and/or template tag.

== Description ==

The plugin Skype Online Status allows you to add one or multiple, highly customizable and accessible (!) Skype buttons to your blog. You can pick any of the available Skype button themes that will show your online status and place them in your pages/posts content, in your sidebar via a widget or anywhere in your theme template files. The themes are mostly based on the themes you can find on http://www.skype.com/share/buttons/wizard.html but much more feature rich, like showing your online status in the alt text of the image and much more customizable, like setting your own texts in your own language for each available status. Even using a button to invoke normal phone calls or multichats or conference calls is possible!

This plugin has grown a lot since the start but it should be mentioned that it was built upon the neat little plugin Skype Button v2.01 by Anti Veeranna. Many thanks!

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

Older versions have been developed for WordPress 2.0+ and 2.1+ but from version 2.6.1.0 upward, also for WordPress 2.5+.

Read more on usage in the Quick Guide section of the Options > Skype Status page, available after installation.

== Installation ==

Note: When updating from version 2.5 or previous: please deactivate plugin and remove the old directories and files before uploading! 

Steps:

1. Download archive and unpack.

2. Upload and save (or overwrite) files and folders to the plugins folder directory. You should have the following file structure below /wp-content/plugins/

     skype-online-status/templates/*.html
     skype-online-status/tinymce/langs/*.js
     skype-online-status/tinymce/editor_plugin.js
     skype-online-status/tinymce3/langs/*.js
     skype-online-status/tinymce3/editor_plugin.js
     skype-online-status/tinymce3/sosquicktag.css
     skype-online-status/buttonsnap.php (only needed for WP version 1.5 and 2.0)
     skype-online-status/editor.php
     skype-online-status/skype-options.php
     skype-online-status/skype-status.php
     skype-online-status/skype_button.gif
     skype-online-status/skype_marker.gif
     skype-online-status/spacer.gif
     

3. Activate plugin on the Plug-ins page

4. Configure (or update) your SkypeID and settings on the Options > Skype Status page

5. Activate the widget or put <?php if (function_exists(get_skype_status)) { get_skype_status(''); } ?> in your sidebar.php (or any other template file) and use the <!--skype status--> quicktag in your posts to display the Skyp button on your blog pages. 

Read more on usage in the Quick Guide section of the Options > Skype Status page, available after installation.

== Frequently Asked Questions ==

For all your questions, bugs and feature requests go to [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group")

== Screenshots ==

Sorry, no screenshots. 

But you can go to the [Skype Online Status plugin page](http://4visions.nl/en/index.php?section=55 "Skype Online Status plugin - 4Visions") to see it in action (right side) or go and test a button on [Skype's button page](http://www.skype.com/share/buttons/ "Skype buttons"). The button templates used there are also available in this plugin.

== Support ==

Post your questions, bugs and feature requests on [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group")

When reporting bugs, please describe as carefully as possible and provide information like the version number of the plugin, WordPress and - if relevant - server security settings, PHP and MySQL version.

This readme comes packed with version 2.6.1.1 of the 'Skype Online Status' plugin for WordPress 2+.

    Version: 2.6.1.2
    Status: Stable 
    Latest stable: 2.6.1.2
    Licence: GNU General Public License

== What's New ==

Since version 1.1 there have been some major changes to the layout of the 'Options > Skype Status' page. Documentation has been extended. 

And some new features:

     - some new tags for the template files,
     - a 'Reset options' button to revert 
       to original settings,
     - the <!--skype status--> tag to display 
       a Skype Status button in posts and page content!
     - editable template
     - language switch for Skype default status texts
     - lots more templates
     - download skype now link
     - automatic plugin upgrade check showing on the options page!

Since version 2.6.1.0 there is a customizable Skype Status Widget available :)

== Notes ==		

Wish List version 3 and beyond :)

     - Multiple widgets
     - Skype-like wizard...
     - Add Skypecasts widget
     - Upload your own button
     - integration with WP user level system
     - Internationalization
     - Get XML online status (and local time?)

Know bugs:
     - (Since 2.6.1.1 no known bugs yet, please report anything you find on [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group") )

Know issues:
     - The javascript provided by Skype that detects whether Skype is (client side) installed, does not work properly on Linux. It defaults to displaying a download popup even if Skype is installed on the visitors computer after the Skype button is clicked. Hopefully, Skype will fix this some day :/

== Revision History ==

[2008-06-16] version 2.6.1.2: automatic blog language detection for status text, some small bugfixes + complete removal button

[2008-06-04] version 2.6.1.0: added simple widget, removed built-in update checker (redundant since WP2.5 auto-update), some bugfixes and code improvements, add your own download link

[2007-04-09] version 2.6.0.9: improved reg_exp for quicktag replacement (defeating wpautop's wrapping p), minor changes in available settings (newline for download link optional), fixed &-sign in fields causing failed w3c validation

[2007-02-18] version 2.5: made quicktag work for 2.1+ new TinyMCE button plugin routine

[2006-11-21] version 2.4.1: added onkeydown action on admin textarea

[2006-11-03] version 2.4: added backwards compatibility with PHP versions previous to 4.3 ( fallback to file() instead of file_get_contents() ) and a check for allow_url_fopen before remote file reading (used in status check and upgrade check) with dynamic options change

[2006-09-25] version 2.3: added Download Skype now! link (with option to change text or disable), more template files and an upgrade function

[2006-09-20] version 2.2.2: moved buttonsnap.php, changes to Quick Guide, template files and Live Support and bugfixes: 
1. quicktag button not showing; 
2. multiple skype buttons in 1 post not showing

[2006-09-04] version 2.2.1: minor changes to admin page

[2006-07-28] version 2.2.0: used global string for speed improvement

[2006-07-05] version 2.1.0: added Skype default status texts in different languages

[2006-07-04] version 2.0.1: minor bugfix (altered defaulting to fallback template procedure)

[2006-06-30] version 2.0: added editable template and live support link

[2006-06-29] version 1.9: added RTE guicktag button for <!--skype status--> hook

[2006-06-27] version 1.8: improved performance by loading template in database

[2006-06-23] version 1.7: added post hook <!--skype status--> and appended instructions to quickguide

[2006-06-23] version 1.6: wrote templating guide and redesigned the Options > Skype Status page

[2006-06-22] version 1.5: added plain text fallback template to core code

[2006-06-22] version 1.4: added reset button and default settings

[2006-06-21] version 1.3: added new template tags {username} {sep1} {sep2}

[2006-06-20] version 1.2: minor bugfixes
1. inconsistent options page form-labels; 
2. skype_status_check not defaulting to status_error_txt when mystatus.skype.com is off-line 

[2006-05-02] version 1.1: added new text template file

[2006-04-26] version 1.0: wrote instructions (quick guide)

[2006-04-20] version 0.9: added skype user name

[2006-04-12] version 0.8: added customizability for get_skype_status('options')

[2006-04-10] version 0.7: redesign admin interface

[2006-03-05] version 0.3 - 0.6: added lot's of new settings and template tags

[2006-03-03] version 0.2: added function skype_parse_theme() and skype_status_check()

[2006-03-03] version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)

