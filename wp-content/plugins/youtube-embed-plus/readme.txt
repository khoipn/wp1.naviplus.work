=== Embed Plus for YouTube Gallery, Livestream and Lazy Loading with Facades ===
Contributors: embedplus
Plugin Name: Embed Plus for YouTube Gallery, Livestream and Lazy Loading with Facades
Tags: youtube, youtube gallery, youtube live stream, lazy load, youtube channel
Requires at least: 4.5
Tested up to: 6.9
Stable tag: 14.2.5
License: GPLv3 or later

A multi-featured plugin to embed YouTube in WordPress. Embed a video, YouTube channel gallery, playlist, or YouTube livestream. Defer JavaScript too!

== Description ==

**A multi-featured plugin to embed YouTube in WordPress**. Use this plugin to embed and customize a YouTube gallery (channel or playlist),  YouTube livestream (and premiere), YouTube short, or standard YouTube video. This plugin for YouTube can be customized in a wide variety of ways. The plugin also focuses on performance with the ability to lazy load with a facade and defer JavaScript while serving minified CSS and JavaScript. Here are a few recently added features to all versions (free and Pro) of this plugin:

* YouTube gallery embeds and standard videos now support Facade Mode which improves plugin performance by loading a lighter version of the player, until it is clicked. The plugin then loads the real YouTube embed after clicking (note: for live streams, the real player is always loaded).  We have tested this feature in multiple cases with a mix of other WordPress plugins, and we found it to successfully improve your Lighthouse performance score by addressing the following recommendation: "Some third-party resources can be lazy loaded with a facade - 1 facade alternative available." You can demo this facade feature on our [Embed Plus for YouTube Plugin](https://youtube-wordpress-plugin.embedplus.com/) WordPress test site.
* Defer critical and non critical JavaScript - improve the initial load time of your pages by allowing this plugin's critical and non critical Javascript code to begin execution only after a page is loaded. That reasoning is the basis for the Defer CSS Javascript (styles and code) recommendation you'll see in various web profiling tools.  We plan to add a plugin feature that will defer critical and non critical CSS in the future.
* Compatible with the WordPress Gutenberg block editor (it also stays backwards-compatible with the classic editor WordPress plugin). Both the Gutenberg block selector and the Gutenberg classic block will show the wizard button. For the Gutenberg block selector, click on the (+) sign for the block editor list. The EmbedPlus for YouTube Wizard block is located under the "Embeds" category (make sure you choose "YouTube **Wizard**"). See more on [how to embed a YouTube video, gallery, or livestream with the WordPress Gutenberg block editor here >>](https://www.embedplus.com/embed-youtube-video-gallery-livestream-wordpress-gutenberg-block-editor.aspx) The plugin is also compatible with several popular page builders like [Elementor](https://www.youtube.com/watch?v=ldNfIGRTxDU), [Beaver Builder](https://www.youtube.com/watch?v=bPgz0jyt7TE), [SiteOrigin](https://www.youtube.com/watch?v=7QNYw_g-7WM), and [Visual Composer](https://www.youtube.com/watch?v=FWBQc9XhAqM).  For these and pretty much any other page builder with a short code widget, you can also embed your video, gallery, live stream, or premiere by creating the short code in the plugin's wizard and then embedding the code in the page builder's short code widget or text widget.
* Privacy and Consent - Improved privacy and GDPR compliance options like YouTube no cookie, YouTube API restrictions, and a customizable GDPR consent message. Note: if you have a full-blown consent plugin installed on your WordPress site, you may not need these particular features of this YouTube plugin.
* YouTube gallery capability: channel and playlist – The ability to make playlist and channel embeds have a gallery layout. By default, the plugin can generate a grid-based [responsive playlist or channel gallery >>](https://www.embedplus.com/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx). Your visitors can browse through pages of video thumbnails and choose from videos that are pulled from an entire YouTube channel or playlist.
* YouTube gallery auto continuous play - embed a playlist or channel gallery and allow it to play one video after the next without requiring viewers to click a thumbnail.
* YouTube Premieres - The plugin provides you the same embedding methods for YouTube live streams as it does for premiere embeds.
* Simultaneous playback control - you can enable/disable the ability for visitors to have separate videos running at the same time on the same page. This also works with YouTube livestream and gallery embeds that are embedded by our YouTube plugin.
* Improved accessibility by using title attributes for screen reader support. Hopefully this plugin can help your site pass functional accessibility evaluations (FAE).
* Improved AJAX WordPress theme support.
* Site origin information with each embed code as an extra security measure. In YouTube's/Google's own words, checking this option "protects against malicious third-party JavaScript being injected into your page and hijacking control of your YouTube player." We especially recommend checking it as it adds higher security than the built-in YouTube embedding method that comes with the current version of WordPress (i.e. oembed).
* Easy "Insert" button for both the Visual and Text mode of the editor (for YouTube gallery and live stream embedding as well).
* Embed an entire YouTube channel as a (non-gallery, single embedded player) playlist.
* Start video settings for playlist embeds. You can now choose to start a playlist with a specific video or have the plugin automatically start with the most recently added video. **Note: If you're embedding a YouTube gallery, the order will be defined by the order of the channel or playlist on YouTube.com**.
* [Volume level initialization](https://www.embedplus.com/mute-volume-youtube-wordpress.aspx) - helpful when autoplay is checked. Applies to all videos embedded with this YouTube plugin.
* iOS playback preferences.
* Automatic localization/internationalization so you can set the player's interface language from English to another.
* Instant HTTPS support. It can even convert past non-HTTPS videos to HTTPS.  Did you know that Google uses HTTPS/SSL support as a ranking signal for SERP?
* "At a Glance" direct access to your YouTube posts/pages.
* Shortcode support for embedding multiple videos on one line.
* General playlist embedding support.
* Migration support for other YouTube plugins for WordPress - allows you convert other YouTube plugin embeds to ours

**Click the Download button to start exploring this plugin for YouTube**, or take a look at some more introductory details below.

This plugin helps you easily manage the growing complexity of YouTube embedding. It provides a spectrum of basic and advanced features of the YouTube embedded player and will have you posting videos in seconds after installing it.

The settings page has plenty of default options that you can automatically apply to your embedded YouTube video, gallery, and/or live stream:

* Turn on/off all annotations by default
* Automatically center all your videos
* Automatically start playing your videos
* Autohide controls until hovering
* Loop your videos
* Show/hide related videos at the end
* Show/hide the video title and other info (YouTube/Google has deprecated this feature)
* Use the light theme
* Show/hide player controls
* Turn on/off closed captions by default
* Make your video, gallery, or live stream responsive so that it dynamically fits in all screen sizes (smart phone, PC and tablet)

Customizations can be also made to each YouTube embed by adding more to the link as shown below. Adding these will override the above global defaults that you set. Want a visual customizer wizard instead? [Go Pro here](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx?ref=readme) (more info in the next section):

* width - Sets the width of your player. If omitted, the default width will be the width of your theme's content. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350"`
* height - Sets the height of your player. If omitted, this will be calculated for you automatically. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350"`
* autoplay - Set this to 1 to autoplay the video (or 0 to play the video once). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&autoplay=1"`
* cc_load_policy - Set this to 1 to turn on closed captioning (or 0 to leave them off). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&cc_load_policy=1"`
* iv_load_policy - Set this to 3 to turn off annotations (or 1 to show them). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&iv_load_policy=3"`
* loop - Set this to 1 to loop the video (or 0 to not loop). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&loop=1"`
* rel - Set this to 0 to only show related videos from the same channel as the current video (or 1 to show videos from any channel). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&rel=0"`
* showinfo - Set this to 0 to hide the video title and other info (or 1 to show it). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&showinfo=0"` (YouTube/Google has deprecated this feature)
* fs - Set this to 0 to hide the fullscreen button (or 1 to show it). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&fs=0"`

You can also start and end each individual video at particular times. Like the above, each option should begin with '&'

* start - Sets the time (in seconds) to start the video. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&start=20"`
* end - Sets the time (in seconds) to stop the video. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&end=100"`

> **About the Pro version of the [Embed PLus for YouTube Plugin](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx?ref=readme)**
>
> If you like this free version, you may even upgrade to a separate Pro plugin with features like:
>
> * Full visual embedding wizard 
> * [Alternate playlist and channel gallery styling >>](https://www.embedplus.com/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx) (list layouts and slider layouts, popup/lightbox player, thumbnail hiding for text only paging, and more)
> * Caching to avoid making frequent requests to YouTube.com and speed up your page loads
> * Automatic video thumbnail images: each post or page that contains at least one video will have the thumbnail of its first video serve as its featured image
> * [Lazy load YouTube embeds] - (https://www.embedplus.com/defer-parsing-of-javascript-improve-gtmetrix-speed-of-page-with-youtube-embed.aspx) with eye-catching [effects and animations](https://www.embedplus.com/add-special-effects-to-youtube-embeds-in-wordpress.aspx)
> * [YouTube live chat] - (https://www.embedplus.com/embed-youtube-live-chat-box-with-live-stream-embed-wordpress.aspx) Add more interaction to your site by including the YouTube live chat box as part of each live stream embed. Note that live chat can also be an option for earning money from your audience by using the Super Chat and Super Stickers feature.
> * Automatic tagging for video SEO
> * Automatic Open Graph tagging for Facebook
> * Deleted video alerts (i.e., did Google remove or take down videos I previously embedded?) 
> * Mobile compatibility checking (i.e., see if your embeds have restrictions that can block your site's mobile visitors from viewing)
> * Alerts when visitors from different countries are blocked from viewing your embeds
> * Priority support
>
> You also get access to our deleted video alerts to help avoid showing embedded videos that are later removed from YouTube.com. You even get an embedder-centric [analytics dashboard](https://www.embedplus.com/dashboard/easy-youtube-analytics-preview.aspx?platform=sim) that adds view tracking to each of your embeds so that you can answers questions like:
>
> * How much are your visitors actually watching the videos you post?
> * How does the view activity on your site compare to other sites like it?
> * What and when are your best and worst performing YouTube embeds?
> * How much do the producers of the YouTube videos you embed rely on **your site**, versus other sites and YouTube.com, for views?
> * Are you embedding videos that are blocked in other countries?
> * Have your visitors tried to view a page and/or gallery on your site with deleted/unavailable videos?

See more details after installing. Enjoy!

[This WordPress plugin is maintained by Embed Plus for YouTube](https://www.embedplus.com/).

== Installation ==

1. Use the WordPress plugin installer to install the plugin.  Alternatively, you can just extract the folder in our download package and upload it to your plugin directory.
1. Access the Plugins admin menu to activate the YouTube embed plugin.
1. Make your default settings after clicking the new YouTube menu item that shows up in your admin panel. This is where you can make global settings on the appearance of each video and gallery, as well as activate: defer JavaScript  (defer CSS coming soon), GDPR mode, user restrictions, and more.
1. To embed videos in your post, use the wizard to embed the shortcode. Example: `[embedyt]https://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]` If you don't know exactly which video you want to embed, use the free built-in search feature to find and insert one.
1. You can also [embed a playlist and channel gallery with this plugin](https://www.embedplus.com/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx).  Please install the plugin and visit the settings page for instructions.
1. To get video SEO, an analytics dashboard and many other premium features, [sign up for one of the options here](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx?ref=readme)

**Manual codes (adding these will override the default settings in the admin):**

* width - Sets the width of your player. If omitted, the default width will be the width of your theme's content. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350"`
* height - Sets the height of your player. If omitted, this will be calculated for you automatically. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350"`
* autoplay - Set this to 1 to autoplay the video (or 0 to play the video once). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&autoplay=1"`
* cc_load_policy - Set this to 1 to turn on closed captioning (or 0 to leave them off). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&cc_load_policy=1"`
* iv_load_policy - Set this to 3 to turn off annotations (or 1 to show them). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&iv_load_policy=3"`
* loop - Set this to 1 to loop the video (or 0 to not loop). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&loop=1"`
* rel - Set this to 0 to only show related videos from the same channel as the current video (or 1 to show videos from any channel). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&rel=0"`
* showinfo - Set this to 0 to hide the video title and other info (or 1 to show it). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&showinfo=0"` (YouTube/Google has deprecated this feature)
* fs - Set this to 0 to hide the fullscreen button (or 1 to show it). Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&fs=0"`

You can also start and end each individual video at particular times. Like the above, each option should begin with '&'

* start - Sets the time (in seconds) to start the video. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&start=20"`
* end - Sets the time (in seconds) to stop the video. Example: `"https://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350&end=100"`

**We recommend using the wizard, but if you're manually pasting a link, always follow these rules:**

* Make sure the url is really on its own line by itself. Or, if you need multiple videos on the same line, make sure each URL is wrapped properly with the shortcode. Example: `[embedyt]https://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]`
* Make sure the url is not an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the url and click the "unlink" button in your editor.
* Make sure you did **not** format or align the url in any way. If your url still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes).
* Finally, there's a slight chance your custom theme is the issue, if you have one. To know for sure, we suggest temporarily switching to one of the default WordPress themes (e.g., "Twenty Thirteen") just to see if your video does appear. If it suddenly works, then your custom theme is the issue. You can switch back when done testing.
 
== Frequently Asked Questions ==

= How do I add this plugin for YouTube to WordPress? =

Watch the following YouTube video which shows the process for our specific plugin: 

https://www.youtube.com/watch?v=9RveWWsrmuw

= How do I embed a YouTube video with a facade? =

See a demo on this [YouTube WordPress Test Page](https://youtube-wordpress-plugin.embedplus.com/youtube-embed-with-facade/)

= How do I create a YouTube gallery in WordPress? =

Here's a video that shows the steps with our YouTube plugin, along with the full set of gallery features:

https://www.youtube.com/watch?v=iMX5TMobCw4

= Why use a YouTube plugin? =

You can certainly embed YouTube videos in WordPress with oembed using the default embedder. However, a good YouTube WordPress plugin helps you easily embed and customize a video, playlist, channel, gallery, premiere, live stream, and facade.  The premium version of this plugin has additional features like embed playback analytics.

= How to get a YouTube API key? =

To embed a gallery, livestream, schema tags, and more, you'll need to get a YouTube API key from Google. The Data API is free and comes with a decent quota, which can be extended.  See here for [steps on creating a YouTube API key](https://www.embedplus.com/how-to-create-a-youtube-api-key.aspx).

You can also learn plenty of other tips on the [EmbedPlus How To Page](https://www.embedplus.com/youtube-plugin-documentation.aspx).

= How do I embed a YouTube livestream on WordPress? =

Assuming you already have the EmbedPlus plugin active, you can embed a YouTube stream (premiere or livestream) in a very similar manner.

* [Steps for a premiere](https://www.youtube.com/watch?v=PqroYZ1qVtY}
* [Steps for a standard live stream](https://www.youtube.com/watch?v=PQ5mY2WaqcI&t=218s)

== Screenshots ==

1. Embed Plus for YouTube Plugin Screenshot 1: Paste a YouTube link on its own line and it will become a YouTube embed on your website. Or, use the shortcode method.
2. Embed Plus for YouTube Plugin Screenshot 2: How to get to YouTube's admin settings
3. Embed Plus for YouTube Plugin Screenshot 3: Visual YouTube Wizard and Search Tool
4. Embed Plus for YouTube Plugin Screenshot 4: Wizard Insert Search Result Screenshot
5. Embed Plus for YouTube Plugin Screenshot 5: Wizard Search Results Screenshot
6. Embed Plus for YouTube Plugin Screenshot 6: Gallery layout for a playlist or channel
7. Embed Plus for YouTube Plugin Screenshot 7: YouTube playlist galleries are also easily supported.  Here's the Billboard Top 25 Songs for example.
8. Embed Plus for YouTube Plugin Screenshot 8: Localization/internationalization so you can set the player's interface language
9. Embed Plus for YouTube Plugin Screenshot 9: Optional YouTube channel subscription button above gallery thumbnails

== Changelog ==

= Embed Plus for YouTube Plugin 14.2.5 =
* This version improves AJAX security hardening.

= Embed Plus for YouTube Plugin 14.2.4 =
* This version fixes a lightbox gallery issue for pro users, and allows you to disable keyboard controls for both free and pro users.

= Embed Plus for YouTube Plugin 14.2.3.3 =
* This version fixes a referrer policy issue that occurs in galleries for some users.

= Embed Plus for YouTube Plugin 14.2.3.2 =
* This version fixes a referrer policy issue for some users in certain browsers.

= Embed Plus for YouTube Plugin 14.2.3 =
* This version removes the modest branding option which has been deprecated by Google. Also, pro users can experiment with the beta feature for filtering out YouTube Shorts from galleries.

= Embed Plus for YouTube Plugin 14.2.2 =
* This version cleans up outdated code and is tested for WordPress 6.8 compatibility.

= Embed Plus for YouTube Plugin 14.2.1.3 =
* This version updates the name of the plugin to fully adhere to WordPress.org's plugin name guidelines.

= Embed Plus for YouTube Plugin 14.2.1.2 =
* This update addresses issues with the [channel-based automatic live stream detection/loading feature](https://www.embedplus.com/how-to-embed-a-youtube-livestream-in-wordpress.aspx) for the [Pro version](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx), and provides better compatibility with PHP 8.3+ for both Free and Pro versions.

= Embed Plus for YouTube Plugin 14.2.1 =
* This update fixes issues with the [channel-based automatic live stream detection/loading feature](https://www.embedplus.com/how-to-embed-a-youtube-livestream-in-wordpress.aspx) for the [Pro version](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx), and better responsive sizing HTML validation for both Free and Pro versions.

= Embed Plus for YouTube Plugin 14.2 =
* This update gives guidance to YouTube's recent API limitations on the [channel-based automatic live stream detection/loading feature](https://www.embedplus.com/how-to-embed-a-youtube-livestream-in-wordpress.aspx), with a solution available to [Pro users](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx)

= Embed Plus for YouTube Plugin 14.1.6.3 =
* Fixes a compatibility issue with Contact Forms 7

= Embed Plus for YouTube Plugin 14.1.6.2 =
* Updated API key instructions

= Embed Plus for YouTube Plugin 14.1.6.1 =
* Fixes a YouTube API referer issue that occurs on some hosts

= Embed Plus for YouTube Plugin 14.1.6 =
* Tested for compatibility with WordPress 6.2+

= Embed Plus for YouTube Plugin 14.1.5 =
* Ability to turn on/off maxresdefault image quality
* Prevent iframes from affecting main frame browser history
* Better calls to htmlspecialchars function
* Wizard window size fix
* Fix livestream fallback issue

= Embed Plus for YouTube Plugin 14.1.4.1 =
* Better compatibility with older browsers

= Embed Plus for YouTube Plugin 14.1.4 =
* Fixes issues from 14.1.3
* Higher quality facade images
* Better compatibility with Jetpack and Short Pixel lazy loaded images
* Fixes an issue with the Classic/TinyMCE editor

= Embed Plus for YouTube Plugin 14.1.3 =
* Better compatibility with Jetpack images
* Avoids unused javascript when not using responsive sizing
* Fixes obscure livestream fallback issue

= Embed Plus for YouTube Plugin 14.1.2 =
* Higher quality facade images
* Avoid unused javascript with facade mode

= Embed Plus for YouTube Plugin 14.1.1 =
* Better compatability with WordPress 6.0
* Easier channel live stream wizard
* Cleaner uninstalls
* Add intro helper video

= Embed Plus for YouTube Plugin 14.1 =
* Adds the live stream fallback feature to channel-based live stream embeds

= Embed Plus for YouTube Plugin 14.0.1.4 =
* YouTube Wizard compatibility for WordPress 5.9+

= Embed Plus for YouTube Plugin 14.0.1.3 =
* Patch for 14.0.1.2, JS fix for compatibility with jQuery 3.0+

= Embed Plus for YouTube Plugin 14.0.1.2 =
* Fix facade playlist embed issue
* Improve compatibility with AJAX themes

= Embed Plus for YouTube Plugin 14.0.1.1 =
* Patch for 14.0.1, higher resolution for facade images

= Embed Plus for YouTube Plugin 14.0.1 =
* Higher resolution for facade images
* Fix CSS issue with GDPR embeds
* YouTube shorts support

= Embed Plus for YouTube Plugin 14.0 =
* New facade mode for lighter and faster page loads (see Performance tab)
* CSS fixes

= Embed Plus for YouTube Plugin 13.4.3 =
* Automatically turn on default dimensions for new installations, to skip oEmbed width/height lookups. Improves performance (recommended for all users, given most YouTube videos are 16:9 ratio)
* Better compatibility with cookie compliance plugins that overtake the player with a placeholder

= Embed Plus for YouTube Plugin 13.4.2 =
* Compatible with latest jQuery updates
* Compatible with WordPress 5.7+

= Embed Plus for YouTube Plugin 13.4.1.2 =
* Allows you to specify the default language (when available) that the player will use to display closed captions

= Embed Plus for YouTube Plugin 13.4.1.1 =
* Prevent deprecated notice for wp_make_content_images_responsive filter

= Embed Plus for YouTube Plugin 13.4.1 =
* Better compatibility with WordPress 5.5

= Embed Plus for YouTube Plugin 13.4 =
* Better compatibility with themes that use the Gutenberg block editor and responsive sizing
* Fix notice on new installs
* Add pagebuilder tips to Wizard tab of the documentation

= Embed Plus for YouTube Plugin 13.3.1 =
* Fix simultaneous playback control issue
* Add support for premiere video embedding

= Embed Plus for YouTube Plugin 13.2.3 =
* Defer JavaScript for performance
* Compatibility between loop and hide relative videos features
* Simultaneous playback control

= Embed Plus for YouTube Plugin 13.2.1 =
* Provides more compatibility with other lazyload plugins and responsive themes

= Embed Plus for YouTube Plugin 13.2.0.2 =
* Fixes the "Hide related videos at the end of playback" feature (fixes issue with galleries)

= Embed Plus for YouTube Plugin 13.2.0.1 =
* Compatibility with legacy versions of PHP
* Fix Responsive sizing issue with lazyloading plugins

= Embed Plus for YouTube Plugin 13.2 =
* Add ability to embed a live stream from a given channel
* Reduction in live stream YouTube API quota usage
* Compatibility with other plugins having tabbed sections (e.g. Yuzo)
* Show YouTube API errors to admins only

= Embed Plus for YouTube Plugin 13.1.2.5 =
* Improve responsive sizing aspect ratio in widgets
* Fix issue: some users receive 403 error when saving

= Embed Plus for YouTube Plugin 13.1.2.4 =
* Pagination and autonext allow the current video to play
* Pagination on mobile fix
* vi deprecation announcement

= Embed Plus for YouTube Plugin 13.1.2.3 =
* gallery thumbnail mobile tap/scroll fix

= Embed Plus for YouTube Plugin 13.1.2.2 =
* Wizard open/close fix
* iOS gallery thumbnail click fix
* class-oembed deprecation fix

= Embed Plus for YouTube Plugin 13.1.2.1 =
* Compatibility with legacy versions of PHP

= Embed Plus for YouTube Plugin 13.1.2 =
* More descriptive plugin name
* Gallery column size and YouTube API message fixes

= Embed Plus for YouTube Plugin 13.1.1 =
* Fixes gallery autonext issue
* Fixes deprecated controls parameter
* Fixes "not live" content issue
* Remove deprecated showinfo parameter

= Embed Plus for YouTube Plugin 13.1 =
* Brings back the ability to hide related/suggested videos at end of playback
* Allows monetized sites to select multiple IAB categories
* Option to run shortcode in admin (compatibility tab)
* Lowers quota usage for live videos
* Fixes "missing dependencies" block editor CSS reference
* Fixes default dimensions issue

= Embed Plus for YouTube Plugin 13.0.1 =
* Clearer instructions
* Feature deprecation (rel, showinfo)

= Embed Plus for YouTube Plugin 13.0 =
* Compatible with the new Gutenberg block editor, and stays backwards-compatible with the classic editor
* Marked deprecated features
* Improved wizard instructions

= Embed Plus for YouTube Plugin 12.2 =
* Improved ads.txt verification management
* Fixed gallery box-sizing bug
* Remove some deprecated YouTube parameters

= Embed Plus for YouTube Plugin 12.1 =
* Improved autoplay compatibility
* Improved sign-up process for the new monetization feature

= Embed Plus for YouTube Plugin 12.0.1 =
* Force HTTPS for the YouTube API if the API is enabled
* Makes the wizard's lightbox expand/contract responsively for different size screens

= Embed Plus for YouTube Plugin 12.0 =
* Improves the admin interface, and includes a new optional feature for users that want to monetize their sites through contextual video from vi.ai.

= Embed Plus for YouTube Plugin 11.9.2 =
* Makes the GDPR consent message display more compatible with other plugins (fixes content filter)

= Embed Plus for YouTube Plugin 11.9.1 =
Improved GDPR compliance, with new Privacy section containing:
* GDPR consent mode
* YouTube no cookie
* YouTube API loading restrictions

= Embed Plus for YouTube Plugin 11.8.7 =
* Helps with GDPR compliance by allowing you to choose when YouTube.com's API is loaded

= Embed Plus for YouTube Plugin 11.8.6.1 =
* Rollback jQuery Updater plugin compatibility

= Embed Plus for YouTube Plugin 11.8.6 =
* Updated YouTube API key video tutorial
* Improved channel wizard process
* Better compatibility with jQuery Updater plugin

= Embed Plus for YouTube Plugin 11.8.5 =
* Adds the YouTube wizard button to the new built-in WordPress text widget
* Fixes a Mac/iOS gallery scrolling issue

= Embed Plus for YouTube Plugin 11.8.4 =
* Fixes gallery AJAX issue
* Adds ability to hide fullscreen button
* Adds ability to hide private videos from galleries
* Changes localized script dependency to jquery

= Embed Plus for YouTube Plugin 11.8.3 =
* Improved compatibility with PHP 7
* Improved compatibility with Divi theme
* Improved AJAX compatibility
* Improved accessibility
* Fix gallery scrolling
* Fix "not live" content output
* Modernize and improve wizard interface (Pro)
* Ability to hide thumbnail images from galleries (Pro)

= Embed Plus for YouTube Plugin 11.8.2 =
* Fix auto-next gallery issue
* Make settings form more secure

= Embed Plus for YouTube Plugin 11.8.1 =
* Fix playlist gallery issue

= Embed Plus for YouTube Plugin 11.8 =
* Improved AJAX theme compatibility
* Ability to choose which roles can use the editor wizard
* Volume + autoplay fix

= Embed Plus for YouTube Plugin 11.7.1 =
* Remove gallery thumbnail translucency
* Various bug fixes for galleries and the wizard

= Embed Plus for YouTube Plugin 11.7 =
* Separate Free and Pro codebases
* Remove deprecated features
* Clean up code

= Embed Plus for YouTube Plugin 11.6 =
* Fixed issue where Free version had some remnants of Pro analytics codebase supporting features here: https://www.embedplus.com/dashboard/easy-youtube-analytics-preview.aspx?platform=sim
* New feature: live streaming from a channel
* Optimized player and gallery loading that may improve Lighthouse reports
* Improved compatibility with pagebuilders
* Improved compatibility with later versions of jQuery
* Removed frameborder for W3C validation
* Gallery pagination enhancement
* PHP 7 compatibility testing

= Embed Plus for YouTube Plugin 11.5 =
* thumbnail stacking for mobile galleries
* better support for ajax themes
* alt text to images

= Embed Plus for YouTube Plugin 11.4 =
* (Free) Improved subscribe button CSS and a new migration option.
* (Pro) Improved accessibility for popup lightbox galleries.

= Embed Plus for YouTube Plugin 11.3.1 =
* (Free) improved debug mode messages
* (Pro) autonext for popup lightbox galleries and hide clear cache button option

= Embed Plus for YouTube Plugin 11.3 =
* Improved responsive sizing for AJAX-based themes
* Popup lightbox display option for Pro galleries

= Embed Plus for YouTube Plugin 11.2 =
* Adds improved wizard and gallery options for Free and Pro users.
* Improved handling of PHP notices.
* Clear cache shortcut added for Pro users.

= Embed Plus for YouTube Plugin 11.1 =
* Automatic continuous play for playlist and channel gallery embeds.
* Ability to add a YouTube channel subscription link to all galleries.
* Ability to hide Previous/Next buttons and page numbers.
* Featured images can now be pulled from playlists (using the thumbnail of the first video).

= Embed Plus for YouTube Plugin 11.0.1 =
Improves HTTPS support for structured data tags, and HTTPS support in Firefox.

= Embed Plus for YouTube Plugin 11.0 =
Improves playlist and gallery embedding functionality.

= Embed Plus for YouTube Plugin 10.9 =
This version offers a host of updates: compatibility fixes, higher quality featured images, selective responsive sizing, wizard enhancements, and circular shaped thumbnails.

= Embed Plus for YouTube Plugin 10.8 =
This update improves compatibility and adds features to Free and Pro galleries.

= Embed Plus for YouTube Plugin 10.7 =
Adds legacy option.

= Embed Plus for YouTube Plugin 10.6 =
Enhanced debugging support for galleries.

= Embed Plus for YouTube Plugin 10.5 =
Enhanced gallery settings for scrolling, video titles, and thumbnail styling.

= Embed Plus for YouTube Plugin 10.4 =
Addresses limitations some users were getting when pasting a YouTube channel and/or playlist gallery.

= Embed Plus for YouTube Plugin 10.3 =
Adds the ability to make playlist and channel embeds have a gallery layout. By the default, the plugin can generate a grid-based [responsive playlist and channel gallery >>](https://www.embedplus.com/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx)

= Embed Plus for YouTube Plugin 10.2 =
Adds start video settings for playlist embeds. You can now choose to start a playlist with a specific video or have the plugin automatically start with the most recently added video.

= Embed Plus for YouTube Plugin 10.1 =
Adds the ability to automatically migrate from another plugin's shortcode. Caching feature now allows lifetime settings. Improved compatibility with other plugins using the YouTube API.

= Embed Plus for YouTube Plugin 10.0 =
This update includes improved tips (Free and Pro) and adds caching to the Pro version for faster page loading.

= Embed Plus for YouTube Plugin 9.8 =
Upgraded code to use YouTube API v3.

= Embed Plus for YouTube Plugin 9.7 =
Adds improved accessibility for screen readers and video thumbnail as featured image support.  

= Embed Plus for YouTube Plugin 9.5 =
Site origin information with each embed code as an extra security measure. In YouTube's/Google's own words, checking this option "protects against malicious third-party JavaScript being injected into your page and hijacking control of your YouTube player." We especially recommend checking it as it adds higher security than the built-in YouTube embedding method that comes with the current version of WordPress (i.e. oembed).

= Embed Plus for YouTube Plugin 9.4 =
Adds Autofit Widget option for Free and PRO users. Also adds slide from left animation to [PRO effects >>](https://www.embedplus.com/add-special-effects-to-youtube-embeds-in-wordpress.aspx)

= Embed Plus for YouTube Plugin 9.3 =
Improved volume functionality and interface updates (Free and PRO).  [Special effects added to PRO version >>](https://www.embedplus.com/add-special-effects-to-youtube-embeds-in-wordpress.aspx)

= Embed Plus for YouTube Plugin 9.2 =
HTTPS/SSL detection is now fully automatic. The manual checkbox is no longer needed.

= Embed Plus for YouTube Plugin 9.1 =
With this version, the plugin can now automatically detect your site's default language and set the interface of the embedded YouTube player to match.

= Embed Plus for YouTube Plugin 9.0 =
Adds automatic localization/internationalization so you can set the player's interface language from English to another.

= Embed Plus for YouTube Plugin 8.9 =
Allows volume level initialization.

= Embed Plus for YouTube Plugin 8.8 =
* Better embedplus plugin conflict notifications.
* Greater emphasis on HTTPS support due to Google's recent announcement about [HTTPS/SSL as an SEO/SERP signal](https://embedplus.com/convert-old-youtube-embeds-to-https-ssl.aspx).
* Supports latest version of WordPress (3.9.2).

= Embed Plus for YouTube Plugin 8.7 =
(PRO) Extends the plugin's existing tagging capabilities by also adding Open Graph markup to enhance Facebook sharing/discovery of your pages.

= Embed Plus for YouTube Plugin 8.6 =
Expanded HTTPS/SSL support.

= Embed Plus for YouTube Plugin 8.5 =
This update features a new iOS related option for both Free and PRO users. PRO users additionally have the new mobile compatibility checker.

= Embed Plus for YouTube Plugin 8.4 =
This update features improved responsive theme support for both Free and PRO versions.  It also adds refined schema tag support to the Pro SEO feature.

= Embed Plus for YouTube Plugin 8.3 =
Now compatible with WordPress 3.9.

= Embed Plus for YouTube Plugin 8.2 =
An at a glance regex improvement (all users). Dashboard now warns you of embeds that are blocked from your visitors in other countries (PRO).

= Embed Plus for YouTube Plugin 8.1 =
Fixes rare YouTube ID issue.

= Embed Plus for YouTube Plugin 8.0 =
Fixes rare ajax issue.

= Embed Plus for YouTube Plugin 7.9 =
YouTube searching and inserting now works in Text mode of the editor too.

= Embed Plus for YouTube Plugin 7.8 =
"At a Glance" direct access to YouTube posts/pages.

= Embed Plus for YouTube Plugin 7.7 =
Fixes black bar issue when no height/width is specified.

= Embed Plus for YouTube Plugin 7.6 =
Added notice about Google's HD problem.

= Embed Plus for YouTube Plugin 7.5 =
Ability to embed an entire channel as a playlist embed.

= Embed Plus for YouTube Plugin 7.3 =
Change the color of the progress bar from red to white.

= Embed Plus for YouTube Plugin 7.2 =
Added music video extras to inspire your posts (PRO).

= Embed Plus for YouTube Plugin 7.1 =
Added autohide controls feature

= Embed Plus for YouTube Plugin 7.0 =
Shortcode support for embedding multiple videos on one line

= Embed Plus for YouTube Plugin 6.4 =
Fixes for some users of WordPress 3.8

= Embed Plus for YouTube Plugin 6.3 =
Removed possible e_notices.

= Embed Plus for YouTube Plugin 6.2 =
Given the increasing focus on privacy, the no-cookies options was added as a free option to all users.  Priority support enhancements also made.

= Embed Plus for YouTube Plugin 6.1 =
Easier access to general settings and dashboard.

= Embed Plus for YouTube Plugin 6.0 =
This version opens up the ability to view Internet video discussions to all wizard users.
**Also, due to numerous users being unable to find the wizard button, we moved it up next to the "Add Media" button.**
Finally, we added Video SEO tags as a PRO option. 

= Embed Plus for YouTube Plugin 5.1 =
Added ability to set default dimensions.
Enhanced compatibility with SSL sites.

= Embed Plus for YouTube Plugin 5.0 =
Built-in YouTube video search, viewing, and insertion right from your editor tab (for all users).
The ability to review the latest web discussions about a video you want to embed before embedding it (PRO users).
Other minor optimizations.

= Embed Plus for YouTube Plugin 4.8 =
Works when pasting embed link in sidebar text widgets

= Embed Plus for YouTube Plugin 4.7 =
Improved separation of PRO and Free features in the UI

= Embed Plus for YouTube Plugin 4.6 =
Added optional responsive video layout to fit all screen sizes. (smart phone, PC and tablet)

= Embed Plus for YouTube Plugin 4.5 =
Added support for playlists.
added support for wmode.

= Embed Plus for YouTube Plugin 4.1 =
Fixed spacing issue. Also added ability to fall back to old spacing format.

= Embed Plus for YouTube Plugin 4.0 =
New features for all users: lazy loading for the flash player by default and the ability to hide player controls for a cleaner look.

= Embed Plus for YouTube Plugin 3.7 =
Enhanced deleted video checker for PRO users

= Embed Plus for YouTube Plugin 3.5 =
Added ability to try to force HTML5 player to speed up page loading

= Embed Plus for YouTube Plugin 3.3 =
HTTPS: Added secure YouTube embedding

= Embed Plus for YouTube Plugin 3.2 =
Ensures video-specific height overrides defaults properly

= Embed Plus for YouTube Plugin 3.1 =
Fixed obscure height problem

= Embed Plus for YouTube Plugin 3.0 =
Added Visual YouTube Wizard for PRO users
Added autologin to analytics for PRO users
Added priority support form for PRO users

= Embed Plus for YouTube Plugin 2.6 =
Compatible with WP 3.6

= Embed Plus for YouTube Plugin 2.4 =
Added auto HD support
Support for shorthand (i.e. `"https://www.youtu.be"`)
Fixed editor issue

= Embed Plus for YouTube Plugin 2.3 =
Start/end time shortcut bug fix

= Embed Plus for YouTube Plugin 2.2 =
Minor changes

= Embed Plus for YouTube Plugin 2.1 =
By request from several users, we've added easier access to the video analytics dashboard

= Embed Plus for YouTube Plugin 2.0 =

This upgrade specifically integrates a user-friendly YouTube Analytics Dashboard to this plugin so you can learn a lot more about the videos you post.  Download it if you would like it to use your site's YouTube-related activity to help answer questions like:
 
* How much are your visitors actually watching the videos you post?
* How does the view activity on your site compare to other sites like it?
* What and when are your best and worst performers?
* How much do the producers of the YouTube videos you embed rely on **your site** for views?

We think these are all interesting questions; however, note that there's no need to upgrade if you don't.

= Embed Plus for YouTube Plugin 1.1 =
Fixed minor bug.

= Embed Plus for YouTube Plugin 1.0 =
First release uploaded to the plugin repository.

== Other Notes ==

This plugin for YouTube can include premium features like lazy load (with animation), alternate playlist and channel gallery styles, automatic video thumbnail support, automatic schema tagging for video SEO, mobile compatibility checking, and deleted video alerts when you [upgrade to PRO](https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx?ref=readme).
