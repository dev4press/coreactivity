# coreActivity

## Changelog

### Version: 2.2 / March 21, 2024

* **edit** various plugin core updates and tweaks
* **edit** Dev4Press Library 4.7.2

### Version: 2.1 / March 12, 2024

* **new** settings for the main data logging, currently for IP only
* **new** option for controlling the process of getting forwarded IPs
* **new** wizard options for forwarded IP and duplicated entries logging
* **edit** display shorter log counts numbers in the admin bar menu
* **edit** Dev4Press Library 4.7.1
* **fix** minor issue with the logging entries object type

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
