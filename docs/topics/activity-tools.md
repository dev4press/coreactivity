# Activity Tools

<!-- category: plugin-panel -->
<!-- tag: settings -->
<!-- prefix: panel -->
<!-- seo-title: coreActivity Tools Panel - %%sitetitle%% -->
<!-- seo-description: Various tools for coreActivity can be used to control notifications settings, cleanup logs and more. -->
<!-- seo-keywords: cleanup -->

The tools panel is available in every Dev4Press plugin, and it includes several standard panels: Recheck and Update, Export Settings, Import Settings and Reset/Remove. 

> Export Settings can only export the plugin settings, the contents of the logs and events tables is not part of the export.

But, coreActivity has few more tools that are specific for this plugin only.

## Notifications

With this tool, you can easily enable or disable all events notifications status. If you want to enable all events for daily or weekly digest, or for instant notification, you can do it from this panel in bulk.

![Notifications Bulk Control](tools-notifications.png)

## Data Cleanup

While coreActivity can run automatic and scheduled data cleanups to remove old records, you can do data cleanup anytime you want, and select the period to delete, and one or more events.

See [Log Data and Retention](log-data-and-retention.md) for the retention setting and the difference between automatic and manual cleanup.

![Cleanup Tool](tools-cleanup.png)

The goal is to delete old data, but you can select how old data to affect, and select one or more, or all events to delete. 

* This tool can take a bit of time to complete the cleanup.
* Depending on the amount of records removed, database tables can end up with the overhead. To optimize the database tables after delete operations, you can use [SweepPress Pro](https://sweep.press/) plugin.
