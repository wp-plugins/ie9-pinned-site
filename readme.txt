=== Plugin Name ===
Contributors: fatihboy
Tags: IE9, Internet Explorer, Pinned Site, Jump List, Taskbar
Requires at least: 2.0.2
Tested up to: 3.0.1
Stable tag: 1.1.2

Adds support for Internet explorer 9 pinned site features to wordpress blog.

== Description ==

Pinned Site is a feature provided to web developers by Internet Explorer 9 which allows developers to integrate
their web sites to Windows 7 desktop. A pinned site, that is dragged from Internet Explorer 9 to Windows 7 taskbar,
can interact with the user using tasbar jump list, thumbnail toolbar and overlay icons.

This plugIn allows a wordpress site to have all pinned site features, that is;

*   Pinned site name, tooltip and startup url entries
*   Jump list entries for new post entry, comment moderation and file upload
*   Jump list entries for every page defined in WordPress site
*   Custom jump list entries of post, categories or tags

= Translated Languages Available =

Here's a list of currently translated languages. If you'd like to contribute, please have a look at the POT file in the 
languages folder and send me your translations.

* Turkish


Please visit [http://www.enterprisecoding.com/blog/projects/ie9-pinned-site] for further information.

== Installation ==

1. Download and unzip the plugin, then upload the entire `ie9-pinned-site` directory to the `/wp-content/plugins` directory;
 or simply upload the zip file if you use WordPress 2.7 or newer
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to *Settings > IE9 Pinned Site*
1. Enter the values in the text boxes.
1. Save your changes

== Screenshots ==

1. IE9 Pinned Site setting page
2. An example of configured jump list
3. Add an existing page block
4. Meta data block


== Changelog ==

= 1.0.0 =
* Initial version

= 1.0.1 =
* Fix: Administrative jump list entries won't shown.. Now logged in user can see administarative enries instead of page listing
* No pinned site javascript output when client is not IE9

= 1.1.0 =
* Addin completely rewritten
* Improved customuzation options and settings page

= 1.1.1 =
* Fix: Missing images added

= 1.1.2 =
* Fix: Call to undefined method ie9PinnedSiteCustomUrl::render4JumpList() when custom url added to jump list