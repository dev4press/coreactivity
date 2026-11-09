# Plugin Support for Multisite Network

<!-- category: getting-started -->
<!-- tag: multisite -->
<!-- seo-title: coreActivity %%post_title%% - %%sitetitle%% -->
<!-- seo-description: coreActivity fully supports use in the WordPress multisite network environment, with centralized management and working across the whole network. -->
<!-- seo-keywords: multisite -->

coreActivity has full support for the multisite network installations. If you run WordPress multisite, coreActivity can be activated only as Network wide plugin, and it will run across all the websites in the network.

Main coreActivity panel is available under Network Admin in WordPress, and there will be centralized logs, events panel, settings and tools. You can see all logged events across the whole network, and each entry will show the ID of the blog where the event happened.

But, in multisite environment, coreActivity will add panels into the individual blogs administration, where the logs will be filtered by the current blog ID. All plugin settings are still global, but you can view logs for every blog individually.

Site-level log views and deletion are limited to that site. Network administrators can manage network-wide settings and review the centralized log. Network-level events may use blog ID `0`. See [Log Data and Retention](log-data-and-retention.md) for cleanup and storage information.
