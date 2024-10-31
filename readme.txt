=== PinkVisual API Connector ===
Contributors: saguarodigital
Donate link: http://saguarodigital.com/
Tags: pinkvisual, PV, api, affiliate program
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.0

Allows integration between the PinkVisual API and WordPress.

== Description ==

The PinkVisual API Plugin allows you to integrate PinkVisual's content with your site via
the PinkVisual API [http://api.pinkvisual.com/]. Using your PinkVisual API ID you can earn
revenue from surfers sent to PinkVisual.

The plugin includes a simple shortcode [pvapi] to add content to your site.

Please see the (Usage)[http://wordpress.org/extend/plugins/pvapi/other_notes/] page for information about using the shortcode.

== Installation ==

1. Install the `pvapi` directory into the .../plugins directory.
1. *(Optional)* Set your API Keys to use your own account.
1. *(Optional)* Copy the .../pvapi/templates files into your own theme and modify them to change
the presentation of the content.

== Frequently Asked Questions ==

= Can I use my own revid and API keys =

Yes! Simply visit the "PVAPI Options" screen and set your own values.

== Changelog ==

= 1.0 =
* Initial Version

== Usage ==

The simplest possible usage is to include a [pvapi] tag in your post. This will include a number of recent episodes
determined by the default list length value defined on the options page.

To any usage of the pvapi tag, you may include a `num=` attribute to list only that many episodes. This individually
overrides the value set on the options page. For example [pvapi num=3] to display only 3 episodes.

You can use a "source name" (see [Source Documentation at PinkVisual API](http://pink-visual-api.readthedocs.org/en/latest/data-types/source.html))
to specify that episodes should only be drawn from a single source. For example, [pvapi source="bes"]. You may
also include a num argument with a source, such as [pvapi source="bes" num="3"].

You may use an "episode name" (see [Episode Documentation at PinkVisual API](http://pink-visual-api.readthedocs.org/en/latest/data-types/episode.html))
to specify a sinlge specific episode for inclusion.

In order to simplify shortcode usage, there is a "PinkVisual Media" button, which appears just to the right of the "Upload Image"
button above the post editor. Simply click to kick off an interactive insertion workflow.

At the moment, actor and niche arguments are not supported, but they will be released in the next version.
