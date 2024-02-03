# coreActivity

## Changelog

### Version: 2.0 / February 12 2024

* **new** dedicated Users login, logout and online tracking
* **new** metadata events: for posts, woocommerce and bbpress
* **new** metadata events: for terms, comments and users
* **new** expand Users panels with status and activity columns
* **new** expand Users panel with link to activity logs for each user
* **new** settings for meta exceptions with some default values
* **edit** reorganized scheduled jobs code into own class `Jobs`
* **edit** Dev4Press Library 4.7
* **fix** few issues related to the PHP 8.3

### Version: 1.8.2 / January 26 2024

* **edit** updated process for getting visitor IP
* **edit** Dev4Press Library 4.6.1

### Version: 1.8.1 / January 24 2024

* **edit** various small updated to some components
* **fix** log dialog data not all properly escaped on display 
* **fix** potential unauthenticated stored XSS vulnerability

### Version: 1.8 / January 11 2024

* **new** component: `Privacy` with 10 events
* **new** component `WordPress` expanded with 3 new events
* **new** action for `Logs` to display WhoIs for IP
* **new** library `WhoIs` for getting IP WhoIs information
* **edit** various improvements for the popup view data display
* **edit** Device Detector Library 6.2.1
* **fix** new events in the `Events` panel can trigger fatal error
* **fix** errors with the display of some object information
* **fix** few issues with displaying Bot detection information
