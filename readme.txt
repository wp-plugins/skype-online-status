=== Skype Online Status ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Skype%20Online%20Status&item_number=2%2e8&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us
Tags: skype, skype button, online, status, online status, widget, widgets, post, posts, quicktag, tinymce, tinymce3, im, skype-button
Requires at least: 2.7
Tested up to: 3.0.1
Stable tag: 2.8.2

Unlimited, highly customizable and accessible (!) Skype buttons through widgets, post quicktags and/or template tags.

== Description ==

The plugin Skype Online Status allows you to add one or multiple, highly customizable and accessible (!) Skype buttons to your blog. You can pick any of the available Skype button themes that will show your online status and place them in your *pages and posts content* (with a neat little Skype quicktag button in the WYSIWYG editor), in your sidebar via *widgets* (unlimited number) or anywhere in your theme template files.

The Skype Status admin page and Skype Widget options are adequately annotated but there is also a **Quick Guide** section with lots of information on all the possible ways to get you blog Skyped. If you still cannot get it working the way you want to, find a Skype link to the developer on the Notes & Live Support section.

Button themes are mostly based on the themes you can find on [Skype's button wizard page](http://www.skype.com/share/buttons/wizard.html "Skype buttons") but more feature rich, like showing your online status in the alt text of the image, more accessible (!) and much more customizable, like setting your own Status texts in your blogs language. Even a button to invoke normal phone calls or multichats or conference calls is possible!

= Which version? =

Older versions have been developed for WordPress 2.0+, 2.1+ and 2.3+ but with versions 2.6.x the main development focus was on WordPress 2.6. From version 2.6.9, WordPress 2.7+ is supported but support for below WordPress 2.2 has been dropped. 

This plugin also has been tested and working fine on WPMU 2.7+.

= Translations =

Since version 2.6.4.0, Skype Online Status is ready for internationalization. Anyone who wants to contribute can do so by making a transation. Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") (also included in the latest package) for more info.

- **Belorussian** * Author: [M. Comfi](http://www.comfi.com/)
- **Danish** * Author: [Georg S. Adamsen](http://wordpress.blogos.dk/2009/03/18/skype-online-status/)
- **Dutch** (unfinished) * Author: [R.A. van Hagen](http://4visions.nl/)
- **French** * Author: [Emmanuelle Traduction](http://www.translatonline.com/)
- **German** * Author: [Fabian Schulz](http://www.fabianschulz.net/blog/2009/08/05/skype-online-status/)
- **Italian** * Author: [Gianni Diurno](http://gidibao.net/index.php/portfolio/ "Gianni Diurno - Portfolio")
- **Japanese** * Author: [Ryuei Sasaki](http://riuiski.com)
- **Russian** * Author: [Leonid Jack](http://studio-xl.com/)
- **Spanish** * Authors: [Rodolfo Castro & Rocio Moreno](http://www.ars-legalconsulting.es/blog/)
- **Ukrainian** * Author: [Michael Svystun](http://emarketingblog.com.ua/)

New translations and their Authors will be mentioned here.

= Donations =

Top donators making my continued work possible:

- **G. Pelzer** Life is a journey. [Life Coaching](http://vivocoaching.com/)
- **Yamisi Daniel, USA** What could you do with more time? [inTouch Virtual Assistance](http://www.intouchva.com/)
- **Michelle Vandepas, USA** Live Your life's Purpose. [Divine Purpose unleashed](http://divinepurposeunleashed.com/)

= Example =

Go to the [Skype Online Status plugin page](http://4visions.nl/en/index.php?section=55 "Skype Online Status plugin - 4Visions") to see a Skype button in action (right side) or go and test a button on [Skype's button page](http://www.skype.com/share/buttons/ "Skype buttons"). The button templates used there -- and more -- are available in this plugin.

== Installation ==

Quick installation: [Install now](http://coveredwebservices.com/wp-plugin-install/?plugin=skype-online-status) !

 &hellip; OR &hellip;

Search for "skype online status" and install with that slick **Plugins > Add New** back-end page.

 &hellip; OR &hellip;

Follow these steps:

Note: When upgrading from version 2.5.x or previous: please deactivate plugin and remove the old directories and files before uploading! 

 1. Download archive and unpack.
 2. Upload (and overwrite) the /skype-online-status/ folder and its content to the /plugins/ folder. 
 3. Activate plugin on the Plug-ins page
 4. Configure (or update) your SkypeID and settings on the Options > Skype Status page
 5. Activate the widget or put `<?php if (function_exists(get_skype_status)) { get_skype_status(''); } ?>` in your sidebar.php or any other template file or use the Skype quicktag button to insert the **[**skype-status**]** shortcode in your posts to display the Skype button inside your blog posts and pages. 

Read more on usage in the Quick Guide section of the Options > Skype Status page, available after installation.

== Frequently Asked Questions ==

= Why is my Skype button showing Offline even when I am online? =

First, you have to change an option in your Skype client settings to make your online status publicly visible:

1. Open your Skype client on your computer and go to 'Options > Privacy' or 'Advanced' (depending on your client version).

2. Find the option 'Allow my status to be shown on the web' and place a checkmark beside it.

3. Close the Options window and change your Skype status to Offline and back Online again to update the Skype server.

If your online status is still incorrect, type in your webbrowser http://mystatus.skype.com/yourskypeid (replace that last part with your Skype ID) and check what is shown there. The plugin takes your status directly from the Skype server and should correspond with your status shown on the above URL (using your Skype ID obviously). If your status is not correct on that URL, it basically is out of your/my hands. You can wait for the Skype server to get 'up to date' or browse the Skype forums for fixes suggested by others...

= I have found a bug. Where do I report it? =

For bug reports, go to [WordPress Support &raquo; Skype Online Status](http://wordpress.org/tags/skype-online-status "WordPress Support &raquo; Skype Online Status") and describe what is happening there. Please provide as much related info as you can but at least give me your plugin and Wordpress version number.

= I have a feature request. Where do I ask? =

For all 'normal' feature requests and suggestions go to [WordPress Support &raquo; Skype Online Status](http://wordpress.org/tags/skype-online-status "WordPress Support &raquo; Skype Online Status") but if you want custom development, please contact me directly from the *Notes & Live Support* section on the Skype Online Status options page or browse to my website [4Visions](http://4visions.nl/ "4Visions"). I will gladly consider any proposal.

= I have trouble setting up a Skype button on my blog. Where do I get help? =

Have you read the *Quick Guide* section of your WordPress Options > Skype Status page, available after installation already? For all remaining questions, post them on [WordPress Support &raquo; Skype Online Status](http://wordpress.org/tags/skype-online-status "WordPress Support &raquo; Skype Online Status") or go for Live Support via Skype Chat to the Support section on the Skype Online Status Options page after installation. I will be happy to respond :)

= I have installed the plugin, fiddled with the settings, added a widget/quicktag, there is no error message but I see no button! =

This usually happens when the plugin cannot find a Skype ID to work with. You can set a different Skype ID for each widget but be sure to set a Skype ID on your Skype options page so the plugin as at least one to fall back on. If you still see no Skype button, reset all options using the Reset button or the Remove button to remove-uninstall-reinstall and start completely fresh.

= I have installed the plugin and it is working fine, but now there is a ad link below my Skype button. What's with that? =

By default, the plugin displays a link below each Skype button that allows visitors without Skype to download it. This link goes via the plugin creators Skype Affiliate account so with each new Skype install there will be some small revenue for maintenance and support of this plugin. If you object to that, you can either disable the link, change is to a direct link or replace it with your own affiliate link on the Skype Online Status options page.

= I would like to contribute. Is that possible? =

Ofcource! What did you have in mind? Monetary funding is always appreciated but if you can help me out in development, that's cool too :) Right now, translation (.mo) files are MORE then welcome! Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") (also included in the latest package) for more info.

Any small donation via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Skype%20Online%20Status&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8 "PayPal Donate") to keep me developing is _**VERY**_ welcome. Thanks!

Read more on usage in the **Quick Guide** section of your WordPress Options > Skype Status page, available after installation.

== Screenshots ==

1. Set your basic fall-back and advanced options on the Skype options page.
2. Activate a Skype widget and either set alternative options or leave blank and fall-back options will be used.
3. Example of Skype button widget in sidebar.

== Other Notes ==

This plugin has grown a lot since the start but it should be mentioned that it was built upon the neat little plugin Skype Button v2.01 by Anti Veeranna. Many thanks!

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

= Support =

Post your questions on [WordPress Support &raquo; Skype Online Status](http://wordpress.org/tags/skype-online-status "WordPress Support &raquo; Skype Online Status") or go for Live Support via Skype Chat to the Support section on the Skype Online Status Options page after installation.

= What's New =

Since version 1.1 there have been some major changes to the layout of the 'Options > Skype Status' page. Documentation has been much extended. 

And some new features:

- internationalization
- widgetized!
- extra button templates
- a 'Reset options' button to revert to original settings and a 'Remove' button to completely clear the plugin settings from your database
- a quicktag and rte button to display a Skype Status button in posts and page content!
- editable template
- language switch for Skype default status texts
- Download Skype Now! link (replaceable with your own referral link since version 2.6)

Since version 2.6.3 there are unlimited customizable Skype Status Widgets available :)
Since version 2.6.4 there is internationalization... Anyone who wants to contribute, please consider doing a translation :)

= Wish List =

For version 3 and beyond :)

- More translations! Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") (also included in the latest package) for more info.
- Skypecasts widgets !
- Skype-like button theme wizard...
- Upload your own button/template files
- integration with WP user system (skype field on the Profile page, ++)
- Get XML online status (and local time, possible?)

= Bugs =

Please report anything you find on [WordPress Support &raquo; Skype Online Status](http://wordpress.org/tags/skype-online-status "WordPress Support &raquo; Skype Online Status")

When reporting bugs, please describe as carefully as possible and provide information like the version number of the plugin, WordPress and - if relevant - server security settings, PHP and MySQL version. Provide as much related info as you can, so we can reproduce the error... And fix it :) 

= Known issues =

- The javascript provided by Skype that detects whether Skype is installed (client side), does not work properly on Linux. It defaults to displaying a download popup even if Skype is installed on the visitors computer after the Skype button is clicked. Hopefully, Skype will fix this some day :/

== Changelog ==

= 2.8.2 =
* Live Support for contributors
* hiding of 5x5 white square created by Skype detection SWF Object
* bugfix: RTE button missing

= 2.8 =
* new skypeCheck script for Windows7 / IE8 compatibility
* new shortcode in posts and pages can handle options like skype_id to override default settings
* load skypeCheck script only when needed

= 2.7.9.1 =
* bugfix: back-end style and script load

= 2.7.9 =
* skypeCheck javascript moved to footer to improve experienced/visual page load times
* bugfix: render no button/widget if skypeid is missing
* bugfix: widget blank page after setting Full Name value
* bugfix: slow load times on Widgets page
* changes to options page
* code cleanup
* new Ukrainian translation

= 2.7 =
* German, Spanish, Ukrainian, Russian and Belarusian translations
* `wp_remote_fopen` replacing own cURL/`remote_fopen` routine
* admin page revision for WP 2.8
* code cleanup and multiple online status check streamlining
* switch to global WP constants (like `WP_CONTENT_DIR`)
* bugfix: multiple widget theme preview

= 2.6.9 = 
* Danish + Italian translations 
* bugfixes in install routine and detect blog language on reset
* bugfix: allow the use of cURL library (if available) while `allow_url_fopen` is off
* Removal of good old Buttonsnap Library to avoid showstopper error in WP 2.7 
* adaptation of settings page to fit the new WP 2.7 backend

= 2.6.4 =
* Internationalization: Read [translation instructions](http://svn.wp-plugins.org/skype-online-status/trunk/languages/language-support.txt "Skype Online Status translation instructions") for more info.

= 2.6.3 =
* Implement the use of cURL if available
* Multiple widgets!! 
* Thank-you box for donators at backend
* Bugfixes: widget options page function, widgets for WP < 2.5, `get_skype_status` and valid characters in SkypeID

= 2.6.2 =
* bugfix in Custom template save to db (stripslashes)
* heaps more themes 
* new {function} tag to My Status templates 
* improved widget with preview

= 2.6.1 =
* automatic blog language detection for status text
* some small bugfixes and code improvements
* complete removal button
* simple widget
* removed built-in update checker (redundant since WP2.5 auto-update) 
* add your own download link
* improved `reg_exp` for quicktag replacement (defeating wpautop's wrapping p)
* minor changes in available settings (newline for download link optional)
* fixed &-sign in fields causing failed w3c validation

= 2.5 =
* made quicktag work for 2.1+ new TinyMCE button plugin routine

= 2.4 =
* onkeydown action on admin textarea
* backwards compatibility with PHP versions previous to 4.3 ( fallback to `file()` instead of `file_get_contents()` ) 
* check for allow_url_fopen before remote file reading (used in status check and upgrade check) with dynamic options change

= 2.3 =
* Download Skype now! link (with option to change text or disable), more template files and an upgrade function

= 2.2 =
* moved buttonsnap.php
* changes to Quick Guide, template files and Live Support 
* bugfixes: 
 1. quicktag button not showing; 
 2. multiple skype buttons in 1 post not showing
* minor changes to admin page
* global string for speed improvement

= 2.1 =
* Skype default status texts in different languages

= 2.0 =
* editable template and live support link
* minor bugfix (altered defaulting to fallback template procedure)

= 1.9 =
* RTE guicktag button for <!--skype status--> hook

= 1.8 =
* improved performance by loading template in database

= 1.7 =
* added post hook <!--skype status--> and appended instructions to quickguide


= 1.6 =
* templating guide 
* redesign Options > Skype Status page

= 1.5 =
* plain text fallback template in core code

= 1.4 =
* reset button 
* default settings

= 1.3 =
* new template tags {username} {sep1} {sep2}

= 1.2 =
* bugfixes:
 1. inconsistent options page form-labels; 
 2. `skype_status_check` not defaulting to `status_error_txt` when mystatus.skype.com is off-line 

= 1.1 =
* added new text template file

= 1.0 =
* instructions (quick guide)
* skype user name
* customizability for `get_skype_status('options')`
* redesign admin interface
* lot's of new settings and template tags
* added function `skype_parse_theme()` and `skype_status_check()`

= 0.1 =
* function and syntax conversion from plugin Skype Button (by Anti Veeranna, discontinued)
* starting date: 2006-03-03

== Upgrade Notice ==

= 2.8.2 =
BUGFIX release. RTE button script fix, Live Support for contributors and hiding of 5x5 white Skype detection SWF Object square.

