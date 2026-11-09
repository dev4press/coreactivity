# Activity Logs

<!-- category: plugin-panel -->
<!-- tag: logs -->
<!-- prefix: panel -->
<!-- seo-title: coreActivity Activity Logs Panel - %%sitetitle%% -->
<!-- seo-description: This is the main coreActivity panel where all the logged activity is displayed with live updates, filters and more. -->
<!-- seo-keywords: logs -->

This is the main plugin panel where you can actually see all the logged events in great detail. Due to the amount of data that can be displayed, there are various methods to organize data and columns to show.

For a complete workflow from selecting events to investigating and maintaining entries, see [Managing Logs and Events](logs-and-events-management.md).

![Detail from Logs Panel](panel-logs.png)

## Find entries

Use the search box and available filters to narrow the list by values such as event, component, user, IP, site, object, request context, or period. The available filters depend on the current view and site configuration. Clear a filter to broaden the results.

Row actions can open logs for a specific user, event, IP, or object. A view focuses the table on the selected value; a filter narrows the results while keeping the corresponding filter available. In a multisite network, the network panel can show entries across sites, while a site's panel is restricted to that site.

## Inspect an entry

Select **View** on an entry to open its data dialog. The available tabs show the recorded fields, metadata, location, and detected device information. The details depend on the event and on which optional data was recorded. Use the row's metadata toggle to expand additional information directly in the table.

![Log entry data dialog](popup-data.png)

![Log entry metadata dialog](popup-meta.png)

Some rows offer a WHOIS lookup for an IP. The response comes from an external WHOIS service and may be unavailable or incomplete.

## Configure and remove entries

Use **Settings → Logs Panel** to choose optional columns and display options. When enabled, live updates request new entries every 10 seconds and retain the current filters. The panel must remain open for live updates.

Select entries and use the bulk delete action to remove them. To remove older entries by age and event, use the [cleanup tool](activity-tools.md#data-cleanup). Review the selection before deletion; removed entries cannot be restored by the plugin. For retention and data considerations, see [Log Data and Retention](log-data-and-retention.md).
