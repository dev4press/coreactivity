# Troubleshooting coreActivity

<!-- category: troubleshooting -->
<!-- tag: troubleshooting -->
<!-- seo-title: %%post_title%% for coreActivity - %%sitetitle%% -->
<!-- seo-description: Troubleshoot missing activity entries, notifications, and geolocation data in coreActivity. -->
<!-- seo-keywords: troubleshooting,logs -->

Use these checks to diagnose coreActivity logging, notifications, geolocation, and live updates. On multisite, open the coreActivity panels in Network Admin when checking network-wide settings and event controls.

## An expected event is missing

1. In the coreActivity **Dashboard**, check the logging status. Enable logging if it is disabled.
2. In **Events**, find the component and event and check that the event is active. REST API reads and several metadata events are inactive when first registered. Integration events also depend on their corresponding plugin being active.
3. In **Logs**, clear the search, filters, view, and period; on multisite, check the appropriate site or the network log. Reproduce the activity and look for the new entry.
4. In **Settings**, review the relevant exception list and **Skip Duplicates**. The latter can suppress repeated entries for supported events. Check [retention and cleanup](log-data-and-retention.md) if older entries are missing.
5. If a full-page cache serves a request without loading WordPress, coreActivity does not run for that request. See [Cache Plugins](cache-plugins.md).

For REST read events, also check **Skip read logging for server requests** in Settings. See [REST API Logging](rest-api-logging.md).

## A notification does not arrive

In **Settings → Notifications**, enable the relevant notification type and check its recipients and delay or digest schedule. In **Events**, enable that notification type for the events you want, or use **Tools → Notifications** for bulk control. Confirm an eligible event appears in **Logs**. Scheduled notifications depend on WordPress cron; email delivery depends on the site's mail configuration. See [Activities Notifications](activities-notifications.md).

## Location data is missing or unexpected

In **Settings → Geo Location**, check the selected provider. Under **Optional Data**, enable **Country Code** and/or **Expanded Meta Information** if you want those values stored with new entries. For a local database, check its token or license and download status on the Dashboard; scheduled database updates depend on WordPress cron. The online method depends on an external service. Provider data can be unavailable or approximate, and the logged IP depends on the request and proxy configuration. See [Activities GEO Location](geo-location.md) and [Detecting Visitor IP](detecting-visitor-ip.md).

## Live updates do not appear

In **Settings → Logs Panel**, check **Live Updates**. It requests new matching entries every 10 seconds while the Logs panel is open. Clear filters and check the current view, then trigger an enabled event. A site-level multisite log shows only that site's entries. Reload the Logs panel after changing settings.

## coreActivity reports a database or setup problem

Open the Dashboard and check the installation or update status and database information. If setup has not completed, follow the plugin's prompts; **Tools → Recheck and Update** reruns the plugin's checks. Before using Reset / Remove or any cleanup operation, review the selected action and make an appropriate backup. See [Plugin Maintenance and Removal](plugin-maintenance-and-removal.md).
