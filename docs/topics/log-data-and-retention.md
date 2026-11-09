# Log Data and Retention

<!-- category: getting-started -->
<!-- tag: logging,retention -->
<!-- seo-title: %%post_title%% in coreActivity - %%sitetitle%% -->
<!-- seo-description: Review the data coreActivity can record and configure log retention and cleanup. -->
<!-- seo-keywords: logging,retention,cleanup -->

coreActivity records enabled events in the WordPress database. Review the information your site records, who can access it, and how long you need to keep it. The available fields and metadata depend on the event and your settings.

## What can be recorded

An entry can include a user ID, IP address, time, event and object information, and request details. A request path and query string can contain information supplied by a visitor or another application. Optional data can include a user agent, referrer, device information, or geolocation. Components may add event-specific metadata; third-party integrations can add their own events and data.

Inspect representative entries in the [Logs panel](activity-logs.md) to understand what your configuration records. [How Activity Logging Works](how-activity-logging-works.md) describes the main fields. Masking an IP in the log display controls its presentation; do not assume it removes the stored address.

## Who can see logs

The Logs panel is an administrative interface. On multisite, the network panel can show network-wide activity and an individual site's panel is limited to that site. Restrict administrative access appropriately and consider what log data is included in database backups and notification emails.

## Automatic cleanup

In **Settings → Maintenance**, enable or disable removal of old entries and set the number of months to keep. A new installation defaults to automatic cleanup enabled with a 24-month period. A daily scheduled job removes older entries; the actual run depends on WordPress cron. Existing settings may differ from these defaults.

## Manual cleanup and removal

Use **Tools → Data Cleanup** to select an age and one or more events to remove. You can also select individual rows for bulk deletion in the Logs panel. Check the selection first: these operations remove log data and the plugin cannot undo them.

The Reset / Remove tool offers separate options for settings, geolocation files, and database data or tables. Review the selected options and make an appropriate backup before using it. Export Settings exports configuration, not the log tables. See [Activity Tools](activity-tools.md) and [Maintenance and Removal](plugin-maintenance-and-removal.md).
