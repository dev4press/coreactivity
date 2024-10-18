=== coreActivity: Activity Logging plugin for WordPress ===
Contributors: GDragoN
Donate link: https://buymeacoffee.com/millan
Tags: dev4press, activity log, activity, audit log, event log
Stable tag: 2.6
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Requires CP: 2.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Monitor and log all kinds of activity happening on the WordPress website, with fine control over events to log, detailed log and events panels...

== Description ==
CoreActivity is a free plugin for monitoring and logging various activities of the WordPress powered website. The plugin is highly modular, with events registered and controlled by multiple Components.

= Quick Introduction Video =
https://www.youtube.com/watch?v=JCukiRMWjeA

Currently, plugin has 28 components with a total of 174 events, with direct integration with 12 popular plugins.

= WordPress Core Components =
* Attachments (3 events)
* Comments (5 events)
* Errors (6 events)
* Notifications (4 events)
* Options (7 events)
* Plugins (9 events)
* Posts (6 events)
* Privacy (10 events)
* Terms (6 events)
* Themes (6 events)
* Users (21 events)
* WordPress (8 events)
* REST API (8 events)

= WordPress Network Only Components =
* Multisite Network (15 events)
* Sitemeta (7 events)

= CoreActivity Internal Component =
* Internal (4 events)

= Third-party Plugins Components =
* bbPress (6 events)
* BuddyPress (4 events)
* Contact Form 7 (3 events)
* DebugPress (9 event)
* Duplicate Post (1 event)
* Forminator (1 event)
* GD Forum Manager (4 event)
* Gravity Forms (6 events)
* Jetpack (2 events)
* SweepPress (3 event)
* User Switching (4 events)
* WooCommerce (6 events)

= Geo Location of IPs =
The plugin can locate where the IP making the request is coming from. There are currently three methods available, with more coming in the future:

* Online via GeoJS.io website
* IP2Location Local Database
* MaxMind GeoLite2 Local Database

To use IP2Location, you need to have an account on IP2Location, to get the download token, and getting the Lite versions of the database is free. To use MaxMind GeoLite2, you need to have MaxMind account, and the license for downloading the files, it is free for the GeoLite2 database files. Plugin supports use of all Lite databases for both providers, and using the provided token it can keep the database updated on a weekly basis.

= More Features =
* Instant Notifications
* Daily Digest Notifications
* Weekly Digest Notifications
* IP WhoIs Information
* Request Device Detection
* Users login, logout, online tracking
* Log Cleanup Tools
* Auto Log Cleanup
* Log Live Updates
* Define Exceptions

= Home, Documentation and GitHub =
* Learn more about the plugin: [CoreActivity Website](https://www.dev4press.com/plugins/coreactivity/)
* Plugin Knowledge Base: [CoreActivity Support](https://www.dev4press.com/kb/coreactivity/)
* Contribute to plugin development: [CoreActivity on GitHub](https://github.com/dev4press/coreactivity)

== Installation ==
= General Requirements =
* PHP: 7.4 or newer

= PHP Notice =
* Plugin doesn't work with PHP 7.3 or older versions.

= WordPress Requirements =
* WordPress: 5.9 or newer

= WordPress Notice =
* Plugin may work with WordPress 5.8 or older versions, but there is no guarantee of that, and plugin is no longer tested with these WordPress versions.

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
Yes. But since cache plugins are bypassing WordPress to serve cached response, some events will be affected when the cached response is returned. To learn more, check out this article: [CoreActivity and Cache Plugins](https://www.dev4press.com/kb/article/coreactivity-cache-plugins/).

= How precise is GEO Location? =
If the database for GEO location is regularly updated, locating the IP to the country is most likely close to 100% precise. When it comes to more detailed location within the country, that is not always precise, and it depends on the country.

== Changelog ==
= 2.5 (2024.10.15) = 
* New: use composer to install third party libraries
* New: REST API option to skip logging read requests from server
* Edit: updates to the loading of third party libraries
* Edit: many updates to the plugin loading process
* Edit: Device Detector Library 6.4.1
* Edit: Dev4Press Library 5.1

= 2.4.2 (2024.08.19) =
* Edit: few minor updates and tweaks

= 2.4.1 (2024.08.19) =
* Edit: updated Dev4Press links
* Edit: Dev4Press Library 5.0.1

= 2.4 (2024.07.17) =
* New: component: `REST` with 8 events
* Edit: Dev4Press Library 5.0
* Fix: input not checked for the cleanup panel

= 2.3.6 (2024.07.05) =
* New: replaced `get_user_by()` with internal function
* Edit: few minor tweaks to internal actions and filters
* Edit: minor updates to the popup dialogs look and feel

= 2.3.5 (2024.06.30) =
* Edit: minor changes to the `Logs` class

= 2.3.4 (2024.06.26) =
* Edit: expanded list of detected email notifications
* Edit: few various minor updates and tweaks
* Edit: protect all PHP files from direct file access
* Edit: Dev4Press Library 4.9.2
* Fix: minor issue with the GEO DB download schedule
* Fix: flags doesn't show on the page header server and visitor IP

= 2.3.3 (2024.06.13) =
* Edit: few various minor updates and tweaks
* Edit: Device Detector Library 6.3.2
* Edit: Dev4Press Library 4.9.1

= 2.3.2 (2024.05.16) =
* Fix: notifications tracking has broken detection process
* Fix: wordpress database delta tracking not working
* Fix: events panel button for bulk deleting has broken URL

= 2.3.1 (2024.05.05) =
* Edit: expanded context help for several panels
* Edit: changes to organization of some plugin settings
* Edit: few changes to the plugin `readme.txt` file

= 2.3 (2024.05.02) =
* Edit: improvements to the Logs panel display for some events
* Edit: IP2Location Library 9.7.3
* Edit: Device Detector Library 6.3.1
* Edit: Dev4Press Library 4.8
* Fix: few minor issues with the component loading
* Fix: several issues with PHP 8.1 and newer

= 2.2 (2024.03.20) =
* New: events panel links to the filtered cleanup tools panel
* Edit: expanded list of default WordPress Options keys
* Edit: expanded list of default WordPress Sitemeta keys
* Edit: various plugin core updates and tweaks
* Edit: Device Detector Library 6.3.0
* Edit: Dev4Press Library 4.7.3
* Fix: logs panel order by IP was not working

= 2.1 (2024.03.12) =
* New: settings for the main data logging, currently for IP only
* New: option for controlling the process of getting forwarded IPs
* New: wizard options for forwarded IP and duplicated entries logging
* Edit: display shorter log counts numbers in the admin bar menu
* Edit: Dev4Press Library 4.7.1
* Fix: minor issue with the logging entries object type

= 2.0.1 (2024.02.12) =
* Fix: fatal error on the plugin dashboard

= 2.0 (2024.02.12) =
* New: dedicated `Users` login, logout and online tracking
* New: metadata events: for posts, woocommerce and bbpress
* New: metadata events: for terms, comments and users
* New: plugins component: installed and updated events
* New: plugins component: error install and update events
* New: themes component: installed and updated events
* New: themes component: error install and update events
* New: expand Users panels with status and activity columns
* New: expand Users panels with a link to activity logs for each user
* New: option to allow for skipping duplicated event entries
* New: some components and events have the `skip_duplicates` flag
* New: logs panel shows the live updates countdown to new update
* New: option to control the visibility of the IPv4 address in the log
* New: settings for metadata exceptions with some default values
* New: show number of new logged entries in the admin bar menu 
* Edit: logs live updates time is now set to 10 seconds
* Edit: blog dashboard in multisite network updated and improved
* Edit: various updates and improvements to the logs and live updates
* Edit: improvements to the page header current IPs integration
* Edit: improvements to the admin bar menu display
* Edit: reorganized scheduled jobs code into own class `Jobs`
* Edit: Dev4Press Library 4.7
* Fix: logs panel bulk removal of selected entries not working
* Fix: issue with the wrong URLs for actions in the live log items
* Fix: few issues related to the PHP 8.3

= 1.8.2 (2024.01.26) =
* Edit: updated process for getting visitor IP
* Edit: Dev4Press Library 4.6.1

= 1.8.1 (2024.01.24) =
* Edit: various small updated to some components
* Fix: log dialog data not all properly escaped on display
* Fix: potential unauthenticated stored XSS vulnerability

= 1.8 (2024.01.11) =
* New: component: `Privacy` with 10 events
* New: component `WordPress` expanded with 3 new events
* New: action for `Logs` to display WhoIs for IP
* New: library `WhoIs` for getting IP WhoIs information
* Edit: various improvements for the popup view data display
* Edit: Device Detector Library 6.2.1
* Fix: new events in the `Events` panel can trigger fatal error
* Fix: errors with the display of some object information
* Fix: few issues with displaying Bot detection information

= 1.7 (2024.01.07) =
* New:  show blog information in the `Logs` popup view
* New:  show view for Blog in the `Logs` for multisite network mode
* New:  option to show linked Blog ID in the `Logs`
* New:  option to show link to individual blog log for Blog ID
* Edit: improvements in displaying objects info and links
* Edit: improvements to the dashboard widgets display
* Edit: several minor styling changes in the `Logs` display
* Edit: changes in display of the `Logs` detection column
* Fix: object column missing under some conditions
* Fix: minor issue with `Activity` linked check method
* Fix: component statistics scale using total instead of max value
* Fix: several missing string translation contexts

= 1.6 (2024.01.03) =
* New: save device detection information and filter on saving log
* New: optional device detection column for the Log
* New: device detection tab in the Log popup view for each entry
* New: matomo `Device Detector` library to parse user agent information
* New: sitemeta option updated event has equal values check
* Edit: expanded information for some of the plugin settings
* Edit: expanded setup wizard with few more questions
* Edit: changed default activity status for some events
* Edit: switch blogs to get object information in network mode
* Edit: improved styling for the `View` popup
* Edit: Dev4Press Library 4.6
* Fix: options on an exception list still were getting logged
* Fix: order of the init and tracking actions for components
* Fix: option updated event not always detecting equal values

= 1.5.4 (2023.12.19) =
* Edit: function `json_encode` replaced with `wp_json_encode`
* Edit: various plugin core updates and tweaks
* Edit: Dev4Press Library 4.5.2
* Fix: potential vulnerability issue with IP not being properly validated
* Fix: few issues with the Network component events logging

= 1.5.3 (2023.12.12) =
* Edit: changed the size of the View popup dialog
* Fix: missing escaping of the large meta-data block on display

= 1.5.2 (2023.12.12) =
* Fix: screen options not visible on the multisite network panels
* Fix: live logs update not working due to the script name change
* Fix: styling related to the IP buttons in the admin side header

= 1.5.1 (2023.12.11) =
* Fix: fatal error due to the enqueue code regression

= 1.5 (2023.12.11) =
* New: method in `Statistics` class to get component statistics
* New: expanded setup `Wizard` with a geolocation panel
* Edit: various small updates and tweaks to admin interface
* Edit: GeoIP2 Library 1.11.1
* Edit: IP2Location Library 9.7.2
* Edit: Dev4Press Library 4.5
* Fix: multiple jobs scheduling when running in multisite
* Fix: few notices with display of meta-data in the logs

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
= 2.5 =
Library Updated. Many improvements and changes.

= 2.4 =
New component and events. Library Updated.

= 2.3 =
External libraries updated. Few minor changes and fixes.

= 2.2 =
Events link to clean up. Device Detector library update Library Updated. Few minor changes.

== Screenshots ==
* Plugin Dashboard
* Events Control Panel
* Log Panel
* Log filtered by Component
* Log Panel Settings
* List of Events
