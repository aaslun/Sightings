=== Sightings ===
Author: Andreas Lundgren (webdez)
Contributors: webdez
Tags: geotagging, geolocation, geolocate, geotag, place, location, gps, placemark, sightings, map, crowd sourcing, maps, google maps
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: trunk
License: GPLv2 or later
Donate link: http://webdez.se/donate/

Sightings is an easy to use plug-in that lets you geo-tag any post or page in WordPress and display them on Google maps in your blog.

== Description ==

The purpose of Sightings is to connect any post or page with a map of a specific location.
Sightings is an easy to use plug-in that utilizes the Google Maps v3 JavaScript API to let you geo-tag any post or page in your WordPress installation.
Since version 1.2 you can even let your visitors submit their own geotagged places through a contribution form! Just use the shortcode: [sightings-form] on any post or page.
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
* Display the geo-tag on a Google map inside the post
* Display all recorded geo-tags collectively on a larger map by typing the shortcode: [sightings-map] into any post or page
* Multiple language support

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

= How do I let my visitors submit their own Sightings? =

On any post or page, type in the text: [sightings-form]
Publish the post or page.
On the published page a form will now appear that allows your visitors to submit geotagged locations along with some descriptive text and user information.
Every submission is imported into your blog as a drafted post, awaiting your approval. Just change the post status to "published" if you want it to appear publicly on your blog along with your other sightings.
It is highty recommended that you configure the Sightings settings in admin first. Here you can also choose to recieve e-mails when someone submits a new sighting through the form.

= How do i display all markers on a larger map? =

First, make sure that you have created some posts, positioned and saved the geolocations on the maps.
Otherwise the map will be empty.
Create a new post/page.
In the text meta-box, type in the text: [sightings-map]
Save/publish the post/page.
View the post/page.

= I really like this plugin! How can i contribute? =

I'm glad to hear that! Please consider rating it 5 stars and spread the word (Twitter, Facebook, Google+ etc.)
You can also send me feedback or suggestions for improvement, write me on Twitter (@aaslun).
Or, if you want to make me really happy and motivate further development, give a small donation here: [http://webdez.se/donate/](http://webdez.se/donate/)
Thanks!

== Screenshots ==

1. The map meta-box in wp-admin.

2. A post presentation with the sightings map displayed below the post content.

3. The Sighings settings page in wp-admin.

4. The Sightings contribution form displayed on a page in the Twenty Ten theme.

== Changelog ==

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

= 1.2 =
New crowd sourcing features, bug fixes and better form validation.

= 1.1 =
Now support for multiple languages. Added Swedish text domain. Replaced all short open tags for increased PHP compatibility.

= 1.0 =
Initial release.
