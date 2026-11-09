# Activity Settings

<!-- category: plugin-panel -->
<!-- tag: settings -->
<!-- prefix: panel -->
<!-- seo-title: coreActivity Settings Panel - %%sitetitle%% -->
<!-- seo-description: All the settings for coreActivity plugin in one place, split into several sub panels. -->
<!-- seo-keywords: settings -->

As with all other plugins, panel `Settings` contains multiple pages with plugin settings. All settings have descriptions, and additional information, depending on the option.

![All the Settings](panel-settings.png)

## Logging Data

These settings control duplicate suppression, visitor IP handling, and whether to skip REST API read requests from the website's own server. See [Detecting Visitor IP](detecting-visitor-ip.md) before enabling forwarded headers.

## Optional Data

Logging process gathers all sorts of data, but not everything needs to be logged, and some information can take a lot of space in the database.

### IP Location Data

Plugin can store country code in the main logs table, and additional GEO location information as metadata. It is recommended to use GEO Location database, instead of the Online GEO Location for this to work properly.

### Meta Data

Standard [optional metadata](how-activity-logging-works.md#optional-data) includes User Agent and Referrer. User Agent is useful for various things, even with it not being very reliable. Based on user agent, plugin can determine the software and device from where the request has originated, and this can be useful when reviewing the logs.

Finally, some events can store a lot of additional information that is not very useful, and for now, plugin allows you to include plugin and theme description and changes in transient data values.

## Exceptions

Some events can generate many entries. The exception lists let you skip selected objects or patterns for supported event types. For supported object types, the Logs panel also offers a `Do Not Log` action in the Object column.

## Geo Location

This panel has options to configure the Geo Location method, and for some methods you need to provide additional information and configuration

![Main GEO Location Settings](settings-geo-main.png)

More information is available in user guide [Activities GEO Location](geo-location.md) and related articles for individual GEO Location methods.

## Logs Panel

Settings here are used to configure how the Logs panel grid is displayed, because there are a lot of data to show, and table can be limiting based on the screen size.

### Skip Duplicates

Some events can generate a lot of repeated log entries that are completely the same (same error for Errors or DebugPress error events), and with this option you can limit the same exact entry to be logged once a day only. Not every event can be skipped with this option active, it affects only some components and events.

### Content Display

Here you can enable or disable IP GEO Location county flag, and you can modify the IPv4 display to hide parts of the IP on display by masking some of the address bytes.

![Log content settings](settings-log-content.png)

Other data you can show (or hide) are simplified names for components and events (by default, it shows registration codes for these, simplified names are full names). You can show the user avatar where applicable, and you can link Blog ID in the multisite network.

### Table Layout

When it comes to the logs table, there are several options to choose some of the additional columns.

### Log Panel Updates

Plugin logs table supports live updates to retrieve latest events while the logs panel is opened. If this is enabled, live update will run every 10 seconds, and the logs panel shows the live update counter on top of the log.

## Tracking

Settings related to activity tracking, currently only for users last activity, last login and logout, and here you can change the value for online window period.

## Notifications

Plugin supports instant notifications, daily and weekly digests, and all can be configured with options for sending, time of day and more.

![Notifications Settings](settings-notifications.png)

For Instant notifications, plugin supports delayed sending of notifications to minimize number of emails sent that can happen with huge number of logged events.

## Maintenance

Automatic cleanup removes entries older than the configured number of months. See [Log Data and Retention](log-data-and-retention.md) for the default and the manual cleanup tool.

## Advanced

Various other features and settings are controlled from this settings panel, and these are not something that will change often.

Right now, advanced settings include the main logging switch, integration into admin bar with Activities menu and Setup Wizard activation.
