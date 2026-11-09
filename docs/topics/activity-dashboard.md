# Activity Dashboard

<!-- category: plugin-panel -->
<!-- tag: dashboard -->
<!-- prefix: panel -->
<!-- seo-title: coreActivity Activity Dashboard Panel - %%sitetitle%% -->
<!-- seo-description: Get various statistical information, and important status messages in one place with coreActivity plugin for WordPress. -->
<!-- seo-keywords: dashboard -->

Dashboard is the starting point for the activity overview, and it has several blocks showing useful information, and the current plugin version shows Statistics, Geo Location information and basic Events data.

## Panel Structure

Top of the Dashboard panel shows the IP for the server, and the current user viewing the website IP, with country flags, if the Geo location is enabled. And, these two elements are present on most of the plugin panels.

![Dashboard Panel](panel-dashboard.png)

Main dashboard area includes 4 widgets.

### Components and Events

You can see number of currently enabled Components and Events, and logging status, with option to deactivate logging (and enable it again).

It is very useful to have the main logging control available, so you disable logging for testing purposes.

### Database Statistics

Plugin uses 3 database tables, and depending on the activity of the website, these tables can grow in size quickly, and here you can keep that in check, and always know how many records they have, how big they are, and if needed, you can run the cleanup tools to remove old events.

### Last 30 days Statistics

The most useful overview is the number of logged events by component in the past 30 days. Click on the Component icon will open the Logs panel and filter the logged events by component.

### Geo Location

This box shows the Geo Location method and other relevant information, if you use Database based Geo location.
