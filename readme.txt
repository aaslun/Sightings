=== Sightings ===
Author: Andreas Lundgren (webdez)
Contributors: webdez, Norran
Tags: geotagging, geolocation, geolocate, geotag, place, location, gps, placemark, sightings, map, crowd sourcing, crowdsourcing, maps, google maps
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: trunk
License: GPLv2 or later
Donate link: http://webdez.se/donate/

Sightings lets you or your readers geo-tag any post or page in your WordPress site to display your blog posts on a Google map.

== Description ==

Why navigate your blog by search or menus? Display your blog on Google maps instead!
The purpose of Sightings is to allow you to link any of your post or page with a specific geographic location.
Sightings is an easy to use plug-in that utilizes the Google Maps v3 JavaScript API to let you geo-tag any post or page in your WordPress site.
Since version 1.3 you can even let your visitors submit their own geotagged places by dropping their own markers on the [sightings-map]!
Display all your recorded sightings on a larger map by using the shortcode: [sightings-map] on any post or page.

Example of situations where you'd find Sightings useful, if:

* you're blogging about UFO sightings and want to display the location where the UFO was seen
* you're reporting news and want to pin-point the location of main event in the story
* you're blogging about the latest trip you took to a city or a nice place where you've been on vacation
* you want to give directions to someone about something connected to what you are writing

You can surely come up with other useful ways to use Sightings!

Requirements:

* PHP 5.3 or later

Features:

* Easy to get started, easy to use
* Geo-tag any post or page
* Display the geo-tag on a Google map that can be viewed on the post
* Display all recorded geo-tags collectively on a larger map by typing the shortcode: [sightings-map] into any post or page
* Multiple language support (English, Swedish)

== Installation ==

Manual install: Unzip the file into your WordPress plugin-directory. Activate the plugin in wp-admin.

Install through WordPress admin: Go to Plugins > Add New. Search for "Sightings". Locate the plugin in the search results. Click "Install now". Click "Activate".

== Frequently Asked Questions ==

= How do I use this plugin? =

Once you have installed and activated the plugin, create a new post. Scroll down a bit until you see the map meta-box.
Click and drag the marker to a desired position on the map.
Click the "Use" button.
Save/Publish your post.
View the post, the map should now be displayed at the bottom of your post, above the comments.

= How do I let my visitors submit their own location markers? =

On any post or page, type in the text: [sightings-map]
Publish the post or page.
On the published page a map will now appear that allows your visitors to contribute geotagged locations along with some descriptive text and user information.
Click on the "[+] Contribute with a location" link at the bottom of the map. A marker will appear. Drag it to desired location. Click it and enter location information. Click "Submit".
Every submission is imported into your blog as a drafted post, awaiting your approval. Just change the post status to "published" if you want it to appear publicly on your blog along with your other sightings.
It is highty recommended that you configure the Sightings settings in admin first. Here you can also choose to recieve e-mails when someone submits a new sighting through the form.

= How do I display all markers on a larger map? =

First, make sure that you have created some posts, positioned and saved the geolocations on the maps.
Otherwise the map will be empty.
Create a new post/page.
In the text meta-box, type in the text: [sightings-map]
Save/publish the post/page.
View the post/page.

= Can I filter out markers by category on the larger map? =

Yes, just enter the [sightings-map] attribute cat_slug or cat_id for the category you want to display on the map.
This will only fetch and display sightings markers for the provided category on the [sightings-map].
For example: [sightings-map cat_id="3"] ,will display only sightings that have the category with id 3.

= I really like this plugin! How can i contribute? =

I'm glad to hear that! Please consider rating it 5 stars and spread the word (Twitter, Facebook, Google+ etc.)
You can also send me feedback or suggestions for improvement, write me on Twitter (@aaslun).
Or, if you want to make me really happy and motivate further development, give a small donation here: [http://webdez.se/donate/](http://webdez.se/donate/)
Thanks!

== Screenshots ==

1. The map meta-box in wp-admin.

2. A post presentation with the sightings map displayed below the post content.

3. The Sighings settings page in wp-admin.

4. The Sightings contribution form displayed on a [sightings-map] in the Twenty Ten theme.

5. An empty [sightings-map] with the contribute-link at the bottom.

6. A contribution marker on the [sightings-map]. The marker appears after clicking on the contribute-link.

== Changelog ==

= 1.3.2 =
* Removed needless Gravity Forms feature (feature just wasn't useful enough).
* Fixed CSS-bugs.
* Support for multiple markers on one post.
* Button to "Delete all sightings", will delete all sightings post-meta (map positions) from the database.
* IMPORTANT! You must delete all your previous sightings after upgrading to v 1.3.2. Multiple marker feature is not backwards compatible with sightings created before v 1.3.2.
* No marker added initially on new posts. Makes sense, first marker should be added by user.

= 1.3.2.1 =
* Important bug fix for user submitted post images. User was forced to submit an image, if no image was submitted a fatal error was thrown. (Thanks to Karen for bug reporting)

= 1.3.1 =
* Better back-end handling of image uploads for contributor sightings.
* CSS fixes

= 1.3 =
* Sightings post maps are now displayed at the top of the post instead of at the bottom.
* Removed the old contribution form and shortcode. You can now leave a contribution by dropping a marker directly on the [sightings-map].
* Support for connecting with Gravity Forms to display number of replies on a form used on a Sight, if you have it installed.
* Bugfixes: Contributor categories was fetched wrong. Selected contributor author was not set on load.
* Improved plugin file structure offers better overview.
* Replaced lambda functions in filters.php for better PHP compatibility.
* Better Swedish translations.
* Support for allow_contributors, allow_contributor_image, width, height, zoom, scrollwheel, draggable, cat_slug and cat_id attributes on the [sightings-map] shortcode.

= 1.2 =
* Crowd sourcing feature: let your visitors submit their own sightings through a contributor form
* Fixed some minor bugs
* Improved validation to reduce risk of user error
* Rewritten Sightings core to support a more object oriented approach to make code easier to maintain and develop

= 1.1 =
* Multiple language support
* Added translation for the Swedish text domain
* Prepared a text domain template (sightings-xx_XX) to encourage translations to other languages
* Fixed some typos
* Replaced short open tags to increase PHP compatibility
* Updated the FAQ

= 1.0 =
* Initial release. Basic functionality.

== Upgrade Notice ==

= 1.3.2.1 =
* Important bug fix for user submitted post images on sights.

= 1.3.2 =
Support for multiple markers on one post. IMPORTANT! You must delete all your previous sightings after upgrading to v 1.3.2. Multiple marker feature is not backwards compatible with sightings created before v 1.3.2.

= 1.3.1 =
Minor CSS tweaks and better back-end handling of media uploads.

= 1.3 =
Contributor shortcode removed and merged into Sightings map shortcode. Important bug-fixes for contributor categories, and much more. See plugin readme.txt file for full changelog.

= 1.2 =
New crowd sourcing features, bug fixes and better form validation.

= 1.1 =
Now support for multiple languages. Added Swedish text domain. Replaced all short open tags for increased PHP compatibility.

= 1.0 =
Initial release.
