=== coreActivity ===
Contributors: GDragoN
Donate link: https://buymeacoffee.com/millan
Tags: dev4press, activity log, activity, events, audit log, event log
Stable tag: 1.0
Requires at least: 5.5
Tested up to: 6.3
Requires PHP: 7.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Monitor and log all kinds of activity happening on the WordPress website, with fine control over events to log, detailed log and events panels, and more.

== Description ==

CoreActivity is a free plugin for monitoring and logging various activities of the WordPress powered website. The plugin is highly modular, with events registered and controlled by multiple Components.

Currently, plugin has 23 components with a total of 117 events, with direct integration with 9 popular plugins.

= WordPress Core Components =
* Attachments (3 events)
* Comments (2 events)
* Errors (6 events)
* Notifications (4 events)
* Options (7 events)
* Plugins (5 events)
* Posts (3 events)
* Terms (3 events)
* Themes (2 events)
* Users (18 events)
* WordPress (5 events)

= WordPress Network Only Components =
* Multisite Network (15 events)
* Sitemeta (7 events)

= CoreActivity Internal Component =
* Internal (4 events)

= Third-party Plugins Components =
* bbPress (3 events)
* BuddyPress (4 events)
* Contact Form 7 (3 events)
* DebugPress (7 event)
* Duplicate Post (1 event)
* Gravity Forms (6 events)
* Jetpack (2 events)
* SweepPress (3 event)
* User Switching (4 events)

= More Features =
* Instant Notifications
* Daily Digest Notifications
* Weekly Digest Notifications
* Log Cleanup Tools
* Auto Log Cleanup
* GEO location of IPs
* Log Live Updates
* Define Exceptions

= Home and GitHub =
* Learn more about the plugin: [CoreActivity Website](https://plugins.dev4press.com/coreactivity/)
* Contribute to plugin development: [CoreActivity on GitHub](https://github.com/dev4press/coreactivity)

== Installation ==
= General Requirements =
* PHP: 7.3 or newer

= PHP Notice =
* Plugin doesn't work with PHP 7.2 or older versions.

= WordPress Requirements =
* WordPress: 5.5 or newer

= WordPress Notice =
* Plugin doesn't work with WordPress 5.4 or older versions.

= Basic Installation =
* Plugin folder in the WordPress plugins should be `coreactivity`.
* Upload `coreactivity` folder to the `/wp-content/plugins/` directory.
* Activate the plugin through the 'Plugins' menu in WordPress.
* Plugin adds a new top level menu called 'CoreActivity' inside Tools.
* Check all the plugin settings before using the plugin.

== Frequently Asked Questions ==
= Where can I configure the plugin? =
Open the newly added top level `CoreActivity` panel, and there you will find the 'Settings' page.

= How is the events detection working? =
Plugin uses a system of filters and actions in WordPress (and supported plugins) to hook and process information and log events based on that. If the changes to the website are done by direct database manipulation via queries or custom functions that don't use established procedures, plugin can't detect such events.

= How are the log entries stored? =
Plugin creates three new database tables where all events are registered, and all the events logged.

= How much the logging process slows down the website? =
If you have all events enabled, the plugin will add 150+ hooks into various elements of WordPress and plugins. That is not too much when compared to 3000+ hooks WordPress runs on average, so it will not have a big impact on WordPress performance. And, it is a good idea to disable events you don't need to use.

== Changelog ==
= 1.0 (2023.09.06) =
* First official release

== Upgrade Notice ==
= 1.0 =
First official release.

== Screenshots ==
