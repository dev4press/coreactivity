=== coreActivity: Activity Logging plugin for WordPress ===
Contributors: GDragoN
Donate link: https://buymeacoffee.com/millan
Tags: dev4press, activity log, activity, events, audit log, event log
Stable tag: 1.4
Requires at least: 5.5
Tested up to: 6.4
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Monitor and log all kinds of activity happening on the WordPress website, with fine control over events to log, detailed log and events panels, and more.

== Description ==

CoreActivity is a free plugin for monitoring and logging various activities of the WordPress powered website. The plugin is highly modular, with events registered and controlled by multiple Components.

Currently, plugin has 26 components with a total of 127 events, with direct integration with 12 popular plugins.

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
* DebugPress (9 event)
* Duplicate Post (1 event)
* Forminator (1 event) / v1.4
* GD Forum Manager (4 event) / v1.4
* Gravity Forms (6 events)
* Jetpack (2 events)
* SweepPress (3 event)
* User Switching (4 events)
* WooCommerce (3 events) / v1.1

= Geo Location of IPs =
The plugin can locate where the IP making the request is coming from. There are currently three methods available, with more coming in the future:

* Online via GeoJS.io website
* IP2Location Local Database
* MaxMind GeoLite2 Local Database

To use IP2Location, you need to have account on IP2Location, to get the download token, and getting the Lite versions of the database is free. To use MaxMind GeoLite2, you need to have MaxMind account, and the license for downloading the files, it is free for the GeoLite2 database files. Plugin supports use of all Lite databases for both providers, and using the provided token it can keep the database updated on a weekly basis.

= More Features =
* Instant Notifications
* Daily Digest Notifications
* Weekly Digest Notifications
* Log Cleanup Tools
* Auto Log Cleanup
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

= Will coreActivity work if the cache plugin is used? =
Yes. But, since cache plugins are bypassing WordPress to serve cached response, some events will be affected when the cached response is returned. To learn more, check out this article: [CoreActivity and Cache Plugins](https://support.dev4press.com/kb/article/coreactivity-cache-plugins/).

= How precise GEO Location is? =
If the database for GEO location is regularly updated, locating the IP to the country is most likely close to 100% precise. When it comes to more detailed location within the country, that is not always precise, and it depends on the country.

== Changelog ==
= 1.4 (2023.11.15) =
* New: component: `GD Forum Manager` plugin, with 4 events
* New: component: `Forminator` plugin, with 1 event
* New: logs panel view support for the object by ID or name
* New: store statistics for each event on the daily base
* New: filter events by the plugin it originated from
* Edit: optimized logs panel views processing and matching
* Edit: log item dialog view updated rendering for expandability
* Edit: improved `Event` view display for the Logs panel 
* Edit: Dev4Press Library 4.4 Beta
* Fix: several small issues with the `Live` Logs updates
* Fix: object filtering for logs panel was unfinished
* Fix: notifications property not found for new events

= 1.3 (2023.11.06) =
* New: geolocation with the use of `MaxMind GeoLite2` database
* New: `MaxMind GeoLite2` support for weekly downloading of Lite database
* New: option to hide the `Object` column from the Logs
* New: plugin dashboard widget for the GEO Location information
* New: component `DebugPress` expanded with two new events
* New: logs panel option to filter by country based on geolocation
* New: logs panel popup dialog with overview of all event data split in tabs
* Edit: changes in the order for some columns on the log panel
* Edit: expanded `SweepPress` sweeping job logged data
* Edit: various improvements to the Logs panel styling
* Edit: improved method for running the GEO Location database update
* Edit: Dev4Press Library 4.4 Beta
* Fix: initial GEO Location database update is not triggered properly

= 1.2 (2023.10.30) =
* New: database: logs table has new `country_code` column
* New: logging: options for logging country code and other location information
* New: geolocation settings: choose between online and `IP2Location` database
* New: geolocation with the use of `IP2Location` database
* New: `IP2Location` support for weekly downloading of Lite database
* New: registered weekly maintenance background job
* Edit: Dev4Press Library 4.4 Beta
* Fix: logs override filtering not working properly always
* Fix: all CRON handlers registered as filters and not actions
* Fix: weekly digest scheduled to run each day

= 1.1 (2023.10.16) =
* New: component: WooCommerce plugin, with 3 events
* New: notifications component: support for WooCommerce `WC_Email` logging
* New: logs panel action to stop logging some of the object type by value
* New: logs panel metadata column as alternative to the metadata row
* New: logs panel with added views for context and method
* New: tool for bulk control of events notifications status
* New: more settings related to object types exclusions
* Edit: sitemeta component: default object type is now `sitemeta`
* Edit: many improvements to the `Logs` class for expandability
* Edit: few improvements to the base `Component` class
* Edit: few improvements to the Logs table and rendering
* Edit: Dev4Press Library 4.4
* Fix: logs filtering in some cases not working properly
* Fix: some events not always obeying exclusion conditions
* Fix: few issues with the content terms relationship change event

= 1.0.5 (2023.10.05) =
* Edit: Dev4Press Library 4.3.5
* Fix: admin pages header IP display may be broken if IP is unknown

= 1.0.4 (2023.10.03) =
* Edit: more changes related to PHPCS and WPCS validation
* Edit: Dev4Press Library 4.3.4

= 1.0.3 (2023.09.26) =
* Edit: more changes related to PHPCS and WPCS validation
* Edit: Dev4Press Library 4.3.3

= 1.0.2 (2023.09.25) =
* Edit: Dev4Press Library 4.3.2

= 1.0.1 (2023.09.20) =
* Edit: more changes related to PHPCS and WPCS validation
* Edit: Dev4Press Library 4.3.1

= 1.0 (2023.09.06) =
* First official release

== Upgrade Notice ==
= 1.4 =
New components. Many tweaks, improvements and fixes.

= 1.3 =
MaxMind GeoLite2 support. Many improvements and fixes.

= 1.2 =
IP2Location support. Many improvements and fixes.

= 1.1 =
New component. Various Logs panel improvements. Tweaks and bug fixes.

== Screenshots ==
* Plugin Dashboard
* Events Control Panel
* Log Panel
* Log filtered by Component
* Log Panel Settings
* List of Events
