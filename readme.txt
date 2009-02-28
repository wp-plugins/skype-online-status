=== Skype Online Status ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Skype%20Online%20Status&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: skype, skype button, online, status, online status, widget, widgets, post, posts, quicktag, tinymce, tinymce3, im, skype-button
Requires at least: 2.3
Tested up to: 2.7.1
Stable tag: 2.6.9.0

Unlimited, highly customizable and accessible (!) Skype buttons through widgets, post quicktags and/or template tags.

== Description ==

The plugin Skype Online Status allows you to add one or multiple, highly customizable and accessible (!) Skype buttons to your blog. You can pick any of the available Skype button themes that will show your online status and place them in your *pages and posts content* (with a neat little Skype quicktag button in the WYSIWYG editor), in your sidebar via *widgets* (unlimited number) or anywhere in your theme template files.

The Skype Status admin page and Skype Widget options are adequately annotated but there is also a **Quick Guide** section with lots of information on all the possible ways to get you blog Skyped. If you still cannot get it working the way you want to, find a Skype link to the developer on the Notes & Live Support section.

Button themes are mostly based on the themes you can find on [Skype's button wizard page](http://www.skype.com/share/buttons/wizard.html "Skype buttons") but more feature rich, like showing your online status in the alt text of the image, more accessible (!) and much more customizable, like setting your own Status texts in your blogs language. Even a button to invoke normal phone calls or multichats or conference calls is possible!

= Which version? =

Older versions have been developed for WordPress 2.0+, 2.1+ and 2.3+ but with versions 2.6.x the main development focus was on WordPress 2.6. From version 2.6.9, WordPress 2.7+ is supported but support for below WordPress 2.1 has been dropped. 

This plugin also has been tested on WPMU.

= Translations =

Since version 2.6.4.0, Skype Online Status is ready for internationalization. Anyone who wants to contribute can do so by making a transation. Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") (also included in the latest package) for more info.

- **Italian** \* Author: [Gianni Diurno](http://wordpress.org/extend/plugins/profile/gidibao) ([portfolio](http://gidibao.net/index.php/portfolio/ "Gianni Diurno - Portfolio"))
- **Dutch** \* Author: [R.A. van Hagen](http://wordpress.org/extend/plugins/profile/RavanH) (unfinished)

Translations and their Authors will be mentioned here.

== Installation ==

Note: When upgrading from version 2.5.x or previous: please deactivate plugin and remove the old directories and files before uploading! Or just use the auto-update feature on your Pugins page ;)

Steps:

1. Download archive and unpack.

2. Upload and save (or overwrite) files and folders to the plugins folder directory. You should have the following file structure below /wp-content/plugins/

    - skype-online-status/js/langs/\*.js
    - skype-online-status/js/editor_plugin.js
    - skype-online-status/js/mce3\_editor\_plugin.js
    - skype-online-status/js/sosquicktag.css
    - skype-online-status/js/skypeCheck.js.php
    - skype-online-status/oops/\*.html
    - skype-online-status/oops/\*.css
    - skype-online-status/templates/\*.html
    - skype-online-status/buttonsnap.php (only for WP 2.0 and previous)
    - skype-online-status/editor.php
    - skype-online-status/skype-admin.php
    - skype-online-status/skype-functions.php
    - skype-online-status/skype-status.php
    - skype-online-status/skype-widget.php
    - skype-online-status/skype\_button.gif
    - skype-online-status/skype\_marker.gif
    - skype-online-status/spacer.gif

3. Activate plugin on the Plug-ins page

4. Configure (or update) your SkypeID and settings on the Options > Skype Status page

5. Activate the widget or put <?php if (function\_exists(get\_skype\_status)) { get\_skype\_status(''); } ?> in your sidebar.php (or any other template file) and use the <!--skype status--> quicktag in your posts to display the Skype button on your blog pages. 

Read more on usage in the Quick Guide section of the Options > Skype Status page, available after installation.

== Frequently Asked Questions ==

**Q: Why is my Skype button showing Offline even when I am online?**

**A:** First, you have to change an option in your Skype client settings to make your online status publicly visible:

1. Open your Skype client and go to 'Options > Privacy' or 'Advanced' (depending on your client version).

2. Find the option 'Allow my status to be shown on the web' and place a checkmark beside it.

3. Close the Options window and change your Skype status to Offline and back Online again to update the Skype server.

If your online status is still incorrect, type in your webbrowser http://mystatus.skype.com/yourskypename and check what is shown there. The plugin takes your status directly from the Skype server and should correspond with your status shown on the above URL (using your skype name obviously). If your status is not correct on that URL, it basically is out of your hands. You can wait for the Skype server to get 'up to date' or browse the Skype forums for fixes suggested by others...

**Q: I have found a bug. Where do I report it?**

**A:** For bug reports, go to [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group") and describe what is happening there. Please provide as much related info as you can but at least give me your plugin and Wordpress version number.

**Q: I have a feature request. Where do I ask?**

**A:** For all 'normal' feature requests go to [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group") but if you want custom development, please contact me directly. I will gladly consider any proposal. You can find my Skype ID on the *Notes & Live Support* section of your WordPress Options > Skype Status page, available after installation. Or browse to my website [4Visions](http://4visions.nl/ "4Visions").

**Q: I have trouble setting up a Skype button on my blog. Where do I get help?**

**A:** Have you read the *Quick Guide* section of your WordPress Options > Skype Status page, available after installation already? For all remaining questions, go to [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group") and post them there. I will be happy to respond in due time :)

**Q: I would like to contribute. Is that possible?**

**A:** Ofcource! What did you have in mind? Monetary funding is always appreciated but if you can help me out in development, that's cool too :) Right now, translation (.mo) files are MORE then welcome! Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") (also included in the latest package) for more info.

Any small donation via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Skype%20Online%20Status&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8 "PayPal Donate") to keep me developing is _**VERY**_ welcome. Thanks!

Read more on usage in the **Quick Guide** section of your WordPress Options > Skype Status page, available after installation.

== Screenshots ==

Sorry, no screenshots. 

But you can go to the [Skype Online Status plugin page](http://4visions.nl/en/index.php?section=55 "Skype Online Status plugin - 4Visions") to see a Skype button in action (right side) or go and test a button on [Skype's button page](http://www.skype.com/share/buttons/ "Skype buttons"). The button templates used there are also available in this plugin.

== Support ==

Post your questions, bugs and feature requests on [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group")

When reporting bugs, please describe as carefully as possible and provide information like the version number of the plugin, WordPress and - if relevant - server security settings, PHP and MySQL version.

This readme comes packed with version 2.6.4.0 of the 'Skype Online Status' plugin for WordPress 2+.

    Version: 2.6.4.0
    Status: Beta 
    Latest stable: 2.6.4.0
    Licence: GNU General Public License

== What's New ==

Since version 1.1 there have been some major changes to the layout of the 'Options > Skype Status' page. Documentation has been much extended. 

And some new features:

- internationalization
- widgetized!
- lots of new templates
- some new tags for the template files,
- a 'Reset options' button to revert to original settings and a 'Remove' button to completely clear the plugin settings from your database
- a quicktag to display a Skype Status button in posts and page content!
- editable template
- language switch for Skype default status texts
- download skype now link (replaceable with your own referral link since version 2.6)

Since version 2.6.3.0 there are unlimited customizable Skype Status Widgets available :)
This version 2.6.4.0 is geared towards internationalization... Anyone who wants to contribute, please consider doing a translation.

== Notes ==

This plugin has grown a lot since the start but it should be mentioned that it was built upon the neat little plugin Skype Button v2.01 by Anti Veeranna. Many thanks!

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

= Wish List =

For version 3 and beyond :)

- Translations! Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") (also included in the latest package) for more info.
- Skypecasts widgets !
- Skype-like button theme wizard...
- Upload your own button/template file
- integration with WP user system (skype field on the Profile page, ++)
- Get XML online status (and local time?)

= Bugs =

Please report anything you find on [Skype Online Status Google Group](http://groups.google.com/group/wp-skype-online-status "Skype Online Status Google Group"). Provide as much related info as you can, so we can reproduce the error... And fix it :) 

= Known issues =

- The javascript provided by Skype that detects whether Skype is installed (client side), does not work properly on Linux. It defaults to displaying a download popup even if Skype is installed on the visitors computer after the Skype button is clicked. Hopefully, Skype will fix this some day :/

== Revision History ==

- [2008-09-09] version 2.6.4.1: Italian translation + small transl. bugfixes
- [2008-09-03] version 2.6.4.0: Internationalization. Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") for more info.
- [2008-08-23] version 2.6.3.3: Bugfix in get\_skype\_status and valid characters in SkypeID
- [2008-08-11] version 2.6.4.0beta: Internationalization (send in your translations please!)
- [2008-08-10] version 2.6.3.2: Bugfix in widgets for WP < 2.5
- [2008-08-08] version 2.6.3.1: Implement the use of cURL if available, bugfix in widget options page function
- [2008-08-06] version 2.6.3.0: Multiple widgets!! + Thank-you box for donators at backend
- [2008-06-23] version 2.6.2.1: bugfix in Custom template save to db (stripslashes)
- [2008-06-21] version 2.6.2.0: heaps more themes + added new {function} tag to My Status templates + improved widget with preview
- [2008-06-16] version 2.6.1.2: automatic blog language detection for status text, some small bugfixes + complete removal button
- [2008-06-04] version 2.6.1.0: added simple widget, removed built-in update checker (redundant since WP2.5 auto-update), some bugfixes and code improvements, add your own download link
- [2007-04-09] version 2.6.0.9: improved reg\_exp for quicktag replacement (defeating wpautop's wrapping p), minor changes in available settings (newline for download link optional), fixed &-sign in fields causing failed w3c validation
- [2007-02-18] version 2.5: made quicktag work for 2.1+ new TinyMCE button plugin routine
- [2006-11-21] version 2.4.1: added onkeydown action on admin textarea
- [2006-11-03] version 2.4: added backwards compatibility with PHP versions previous to 4.3 ( fallback to file() instead of file\_get\_contents() ) and a check for allow\_url\_fopen before remote file reading (used in status check and upgrade check) with dynamic options change
- [2006-09-25] version 2.3: added Download Skype now! link (with option to change text or disable), more template files and an upgrade function
- [2006-09-20] version 2.2.2: moved buttonsnap.php, changes to Quick Guide, template files and Live Support and bugfixes: 
 1. quicktag button not showing; 
 2. multiple skype buttons in 1 post not showing
- [2006-09-04] version 2.2.1: minor changes to admin page
- [2006-07-28] version 2.2.0: used global string for speed improvement
- [2006-07-05] version 2.1.0: added Skype default status texts in different languages
- [2006-07-04] version 2.0.1: minor bugfix (altered defaulting to fallback template procedure)
- [2006-06-30] version 2.0: added editable template and live support link
- [2006-06-29] version 1.9: added RTE guicktag button for <!--skype status--> hook
- [2006-06-27] version 1.8: improved performance by loading template in database
- [2006-06-23] version 1.7: added post hook <!--skype status--> and appended instructions to quickguide
- [2006-06-23] version 1.6: wrote templating guide and redesigned the Options > Skype Status page
- [2006-06-22] version 1.5: added plain text fallback template to core code
- [2006-06-22] version 1.4: added reset button and default settings
- [2006-06-21] version 1.3: added new template tags {username} {sep1} {sep2}
- [2006-06-20] version 1.2: minor bugfixes
 1. inconsistent options page form-labels; 
 2. skype\_status\_check not defaulting to status\_error\_txt when mystatus.skype.com is off-line 
- [2006-05-02] version 1.1: added new text template file
- [2006-04-26] version 1.0: wrote instructions (quick guide)
- [2006-04-20] version 0.9: added skype user name
- [2006-04-12] version 0.8: added customizability for get\_skype\_status('options')
- [2006-04-10] version 0.7: redesign admin interface
- [2006-03-05] version 0.3 - 0.6: added lot's of new settings and template tags
- [2006-03-03] version 0.2: added function skype\_parse\_theme() and skype\_status\_check()
- [2006-03-03] version 0.1: function and syntax conversion from plugin Skype Button (Anti Veeranna)

