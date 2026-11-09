# Users Tracking

<!-- category: feature -->
<!-- tag: users,tracking -->
<!-- level: advanced -->
<!-- seo-title: %%post_title%% with coreActivity - %%sitetitle%% -->
<!-- seo-description: coreActivity can store metadata records for each user to track latest activity, login, logout and determine the online status. -->
<!-- seo-keywords: tracking -->

coreActivity includes basic users tracking, and adds 3 new user metadata records: last user activity timestamp, last user login timestamp and last user logout timestamp. Based on the last user activity, plugin can determine when if the user is currenty online.

And, these records are displayed as new columns for the Users panel, including support for multisite network users panel, and normal users panel for individual blogs. New columns can be used to order users by the latest activity or latest login.

![Expanded Users Panel](users-activity.png)

This tracking was added in plugin version 2.0, and so far is not used outside the Users panel. But, this feature will be useful for other plugins that need centralized tracking of users online status or login activity. [coreSecurity Pro](https://plugins.dev4press.com/coresecurity/) plugin has features that depend on this, and will have more to help with the users management and prevention of spam or other malicious activity.
