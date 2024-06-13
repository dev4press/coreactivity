# coreActivity

## Changelog

### Version: 2.3.3 / June 13, 2024

* **edit** few various minor updates and tweaks
* **edit** Device Detector Library 6.3.2
* **edit** Dev4Press Library 4.9.1

### Version: 2.3.2 / May 16, 2024

* **fix** notifications tracking has broken detection process
* **fix** wordpress database delta tracking not working
* **fix** events panel button for bulk deleting has broken URL

### Version: 2.3.1 / May 5, 2024

* **edit** expanded context help for several panels
* **edit** changes to organization of some plugin settings
* **edit** few changes to the plugin `readme.txt` file

### Version: 2.3 / May 2, 2024

* **edit** improvements to the Logs panel display for some events
* **edit** IP2Location Library 9.7.3
* **edit** Device Detector Library 6.3.1
* **edit** Dev4Press Library 4.8
* **fix** few minor issues with the component loading
* **fix** several issues with PHP 8.1 and newer

### Version: 2.2 / March 20, 2024

* **new** events panel links to the filtered cleanup tools panel
* **edit** expanded list of default WordPress Options keys
* **edit** expanded list of default WordPress Sitemeta keys
* **edit** various plugin core updates and tweaks
* **edit** Device Detector Library 6.3.0
* **edit** Dev4Press Library 4.7.3
* **fix** logs panel order by IP was not working

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
