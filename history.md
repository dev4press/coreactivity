# coreActivity

## Changelog

### Version: 2.0.1 / February 12, 2024

* **fix** fatal error on the plugin dashboard

### Version: 2.0 / February 12, 2024

* **new** dedicated `Users` login, logout and online tracking
* **new** metadata events: for posts, woocommerce and bbpress
* **new** metadata events: for terms, comments and users
* **new** plugins component: installed and updated events
* **new** plugins component: error install and update events
* **new** themes component: installed and updated events
* **new** themes component: error install and update events
* **new** expand Users panels with status and activity columns
* **new** expand Users panels with a link to activity logs for each user
* **new** option to allow for skipping duplicated event entries
* **new** some components and events have the `skip_duplicates` flag
* **new** logs panel shows the live updates countdown to new update
* **new** option to control the visibility of the IPv4 address in the log
* **new** settings for metadata exceptions with some default values
* **new** show number of new logged entries in the admin bar menu
* **edit** logs live updates time is now set to 10 seconds
* **edit** blog dashboard in multisite network updated and improved
* **edit** various updates and improvements to the logs and live updates
* **edit** improvements to the page header current IPs integration
* **edit** improvements to the admin bar menu display
* **edit** reorganized scheduled jobs code into own class `Jobs`
* **edit** Dev4Press Library 4.7
* **fix** logs panel bulk removal of selected entries not working
* **fix** issue with the wrong URLs for actions in the live log items
* **fix** few issues related to the PHP 8.3

### Version: 1.8.2 / January 26, 2024

* **edit** updated process for getting visitor IP
* **edit** Dev4Press Library 4.6.1

### Version: 1.8.1 / January 24, 2024

* **edit** various small updated to some components
* **fix** log dialog data not all properly escaped on display
* **fix** potential unauthenticated stored XSS vulnerability

### Version: 1.8 / January 11, 2024

* **new** component: `Privacy` with 10 events
* **new** component `WordPress` expanded with 3 new events
* **new** action for `Logs` to display WhoIs for IP
* **new** library `WhoIs` for getting IP WhoIs information
* **edit** various improvements for the popup view data display
* **edit** Device Detector Library 6.2.1
* **fix** new events in the `Events` panel can trigger fatal error
* **fix** errors with the display of some object information
* **fix** few issues with displaying Bot detection information

### Version: 1.7 / January 7, 2024

* **new** show blog information in the `Logs` popup view
* **new** show view for Blog in the `Logs` for multisite network mode
* **new** option to show linked Blog ID in the `Logs`
* **new** option to show link to individual blog log for Blog ID
* **edit** improvements in displaying objects info and links
* **edit** improvements to the dashboard widgets display
* **edit** several minor styling changes in the `Logs` display
* **edit** changes in display of the `Logs` detection column
* **fix** object column missing under some conditions
* **fix** minor issue with `Activity` linked check method
* **fix** component statistics scale using total instead of max value
* **fix** several missing string translation contexts

### Version: 1.6 / January 3, 2024

* **new** save device detection information and filter on saving log
* **new** optional device detection column for the Log
* **new** device detection tab in the Log popup view for each entry
* **new** matomo `Device Detector` library to parse user agent information
* **new** sitemeta option updated event has equal values check
* **edit** expanded information for some of the plugin settings
* **edit** expanded setup wizard with few more questions
* **edit** changed default activity status for some events
* **edit** switch blogs to get object information in network mode
* **edit** improved styling for the `View` popup
* **edit** Dev4Press Library 4.6
* **fix** options on an exception list still were getting logged
* **fix** order of the init and tracking actions for components
* **fix** option updated event not always detecting equal values

### Version: 1.5.4 / December 19, 2023

* **edit** function `json_encode` replaced with `wp_json_encode`
* **edit** various plugin core updates and tweaks
* **edit** Dev4Press Library 4.5.2
* **fix** potential vulnerability issue with IP not being properly validated
* **fix** few issues with the Network component events logging

### Version: 1.5.3 / December 12, 2023

* **edit** changed the size of the View popup dialog
* **fix** missing escaping of the large meta-data block on display

### Version: 1.5.2 / December 12, 2023

* **fix** screen options not visible on the multisite network panels
* **fix** live logs update not working due to the script name change
* **fix** styling related to the IP buttons in the admin side header

### Version: 1.5.1 / December 11, 2023

* **fix** fatal error due to the enqueue code regression

### Version: 1.5 / December 11, 2023

* **new** method in `Statistics` class to get component statistics
* **new** expanded setup `Wizard` with a geolocation panel
* **edit** various small updates and tweaks to admin interface
* **edit** GeoIP2 Library 1.11.1
* **edit** IP2Location Library 9.7.2
* **edit** Dev4Press Library 4.5
* **fix** multiple jobs scheduling when running in multisite
* **fix** few notices with display of meta-data in the logs

### Version: 1.4 / November 15, 2023

* **new** component: `GD Forum Manager` plugin, with 4 events
* **new** component: `Forminator` plugin, with 1 event
* **new** logs panel view support for the object by ID or name
* **new** store statistics for each event on the daily base
* **new** filter events by the plugin it originated from
* **edit** optimized logs panel views processing and matching
* **edit** log item dialog view updated rendering for expandability
* **edit** improved `Event` view display for the Logs panel
* **edit** Dev4Press Library 4.4 Beta
* **fix** several small issues with the `Live` Logs updates
* **fix** object filtering for logs panel was unfinished
* **fix** notifications property not found for new events

### Version: 1.3 / November 6, 2023

* **new** geolocation with the use of `MaxMind GeoLite2` database
* **new** `MaxMind GeoLite2` support for weekly downloading of Lite database
* **new** option to hide the `Object` column from the Logs
* **new** plugin dashboard widget for the GEO Location information
* **new** component `DebugPress` expanded with two new events
* **new** logs panel option to filter by country based on geolocation
* **new** logs panel popup dialog with overview of all event data split in tabs
* **edit** changes in the order for some columns on the log panel
* **edit** expanded `SweepPress` sweeping job logged data
* **edit** various improvements to the Logs panel styling
* **edit** improved method for running the GEO Location database update
* **edit** Dev4Press Library 4.4 Beta
* **fix** initial GEO Location database update is not triggered properly

### Version: 1.2 / October 30, 2023

* **new** database: logs table has new `country_code` column
* **new** logging: options for logging country code and other location information
* **new** geolocation settings: choose between online and `IP2Location` database
* **new** geolocation with the use of `IP2Location` database
* **new** `IP2Location` support for weekly downloading of Lite database
* **new** registered weekly maintenance background job
* **edit** Dev4Press Library 4.4 Beta
* **fix** logs override filtering not working properly always
* **fix** all CRON handlers registered as filters and not actions
* **fix** weekly digest scheduled to run each day

### Version: 1.1 / October 16, 2023

* **new** component: WooCommerce plugin, with 3 events
* **new** notifications component: support for WooCommerce `WC_Email` logging
* **new** logs panel action to stop logging some of the object type by value
* **new** logs panel metadata column as alternative to the metadata row
* **new** logs panel with added views for context and method
* **new** tool for bulk control of events notifications status
* **new** more settings related to object types exclusions
* **edit** sitemeta component: default object type is now `sitemeta`
* **edit** many improvements to the `Logs` class for expandability
* **edit** few improvements to the base `Component` class
* **edit** few improvements to the Logs table and rendering
* **edit** Dev4Press Library 4.4 Beta
* **fix** logs filtering in some cases not working properly
* **fix** some events not always obeying exclusion conditions
* **fix** few issues with the content terms relationship change event

### Version: 1.0.5 / October 5, 2023

* **edit** Dev4Press Library 4.3.5
* **fix** admin pages header IP display may be broken if IP is unknown

### Version: 1.0.4 / October 2, 2023

* **edit** more changes related to PHPCS and WPCS validation
* **edit** Dev4Press Library 4.3.4

### Version: 1.0.3 / September 26, 2023

* **edit** more changes related to PHPCS and WPCS validation
* **edit** Dev4Press Library 4.3.3

### Version: 1.0.2 / September 25, 2023

* **edit** Dev4Press Library 4.3.2

### Version: 1.0.1 / September 20, 2023

* **edit** more changes related to PHPCS and WPCS validation
* **edit** Dev4Press Library 4.3.1

### Version: 1.0 / September 6, 2023

* **new** first official release
