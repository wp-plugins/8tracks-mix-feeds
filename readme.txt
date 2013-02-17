=== 8tracks Mix Feeds ===
Contributors: 
Donate link: 
Tags: 8tracks music feed mixes playlist 
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Displays feeds of liked, created or dashboard mixes from a user's 8tracks profile.

== Description ==

This plugin displays feeds of mixes from an 8tracks profile. Feeds will show a mix cover images and mix titles on hover. Currently available feeds are:

* Liked mixes
* Created mixes
* Dashboard mixes

You can create a feed in two ways: (1) with a widget or (2) with a shortcode.

The shortcode is as follows: 

[mixes heading="" user="" type="" items="" item_size="" items_per_row=""  mix_label_size="" ]

= heading =

Pretty self explanatory, this will appear right above your mixes in h2 tags.

= user =

The 8tracks username you want to pull mixes from. Mine is chateloinus. Follow me!

= type =

This can either be “created”, “liked”, or “mix_feed.” I was outputting mixes that I’ve liked with the shortcode I used earlier. With, “created,” you can output mixes that you’ve created yourself, and with “mix_feed” you can output mixes that appear on your 8tracks dashboard.

= items =

The number of mixes you’ll be pulling. The maximum is 50 and the default is 5.

= item_size =

The width and height of each mix cover image in pixels. The maximum is 250px and the default is 50px.

= items_per_row =

You can control how many mixes you have in each row and achieve a grid like feed like I did before or…

Try vertical orientation:

[mixes user="reechiesambooca" type="mix_feed" items="3" item_size="250" items_per_row="1" ]

Or horizontal orientation (set items and items_per_row to the same value):

[mixes user="chateloinus" type="mix_feed" items="6" items_per_row="6" ]

The default is 5 and the maximum is 50.

= mix_label_size =

This is the font size of the headings that appear when you hover over a mix. Depending on the size of each mix, you’re going to want to change this so that they fit. Don’t forget to specify your units of measurement, whether it’s in px, em, or %. The default is 30px.

== Installation ==

1. Upload `8tracks-mix-feeds` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Appearance** -> **Widgets** and drag and drop 8tracks Mix Feeds into the appropriate section and set its options. You can also use a shortcode on any post or page (see description).

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.1 (Planned)**

* Allow authentication to show listening history feed.
* Show a profile badge with widget and shortcode.

**1.0 (Current) **

* Added shortcode and widget to show liked, created, and dashboard mixes in feed.

== Upgrade notice ==