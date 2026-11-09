# Activity Events

<!-- category: plugin-panel -->
<!-- tag: events -->
<!-- prefix: panel -->
<!-- seo-title: coreActivity Activity Events Panel - %%sitetitle%% -->
<!-- seo-description: Events panel for coreActivity plugin, is main management panel for components and events with some additional settings. -->
<!-- seo-keywords: events -->

The Events panel is used to manage all the registered events, show information about them and handle additional settings, including activity of the event and notifications.

Each event or component that has at least one log entry related to them, have two icons: `View` (an Eye icon) and `Filter` (a Funnel icon). These do the same thing, but the View will put show results in the View mode where the viewed component or event are removed from the filter and the results set for cleaner look.

> To learn more about Events, check out this [Article](components-and-events.md) and check out list of all [Events](events.md).

For a complete workflow from selecting events to investigating and maintaining entries, see [Managing Logs and Events](logs-and-events-management.md).

![Events Panel](panel-events.png)

## Event Actions

All event actions are handled by the Toggle buttons. And, the Bulk Actions are also available to do all the same things as toggle buttons, but on a selected events.

### Event Status

The Status column has the toggle to enable or disable the event. Disabled event are not tracked or logged.

### Notifications

For each notification type, you have 3 toggle buttons to enable or disable them. Enabled events for a notification means that enable events will be included in the notifications (instant or digest).

## More about Events

Most events are active when first registered, and will monitor and log data. But, there are some events that are inactive when first registered, because they can generate large amount of log entries, and you should only enable them if you really want that data.

### Events that are inactive on registration

* **Post Meta Added/Updated/Deleted**: all three of these are inactive on start, because when editing content, there will be large number of these events fired, and they will not always offer any useful information.
* **Comment Meta Added/Updated/Deleted**: all three of these are inactive on start for same reason as Posts meta events. The difference is that Comments don't use many meta keys by default, and usually that is done by third party plugins dealing with comments.
* **Term Meta Added/Updated/Deleted**: all three of these are inactive on start for same reason as Posts meta events. The difference is that Terms don't use any meta keys by default, and usually that is done by third party plugins dealing with terms.
* **User Meta Added/Updated/Deleted**: all three of these are inactive on start for same reason as Posts meta events. Users meta read and write operations are done very often, and can generate a lot of information that is not very useful.
* **Transient Set/Deleted**: both WordPress and plugins use transient records for caching and can modify them very often. Logging this is not very useful, unless you are tracking something specific. 
* **Site Transient Set/Deleted**: both WordPress and plugins use transient records for caching and can modify them very often. Logging this is not very useful, unless you are tracking something specific.
* **REST API Read**: accessing REST API to read data is not always useful, especially if you do a lot of editing. If you want to track something specific, you can have this event enabled.
* **DebugPress PHP Error**: if you run website in debug mode, this can generate large number of entries. If you need to track some specific error, enable this event, otherwise, leave it disabled.
* **DebugPress AJAX Call**: WordPress makes a lot of AJAX calls all the time, logging each one is not useful. If you track something specific, you should use this event. 
* **DebugPress HTTP Call**: WordPress makes many HTTP calls, and logging each one is not useful. If you track something specific, you should use this event.
* **coreActivity Daily/Weekly Digest**: plugin can log each time it sends a digest, but that is not very useful, so it is installed as disabled by default.
