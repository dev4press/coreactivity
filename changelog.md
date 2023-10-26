# coreActivity

## Changelog

### Version: 1.2 / October 30 2023

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

### Version: 1.1 / October 16 2023

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

### Version: 1.0.5 / October 5 2023

* **edit** Dev4Press Library 4.3.5
* **fix** admin pages header IP display may be broken if IP is unknown

### Version: 1.0.4 / October 2 2023

* **edit** more changes related to PHPCS and WPCS validation
* **edit** Dev4Press Library 4.3.4

### Version: 1.0.3 / September 26 2023

* **edit** more changes related to PHPCS and WPCS validation
* **edit** Dev4Press Library 4.3.3

### Version: 1.0.2 / September 25 2023

* **edit** Dev4Press Library 4.3.2

### Version: 1.0.1 / September 20 2023

* **edit** more changes related to PHPCS and WPCS validation
* **edit** Dev4Press Library 4.3.1

### Version: 1.0 / September 6 2023

* **new** first official release