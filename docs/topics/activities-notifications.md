# Activities Notifications

<!-- category: feature -->
<!-- tag: notifications -->
<!-- seo-title: %%post_title%% with coreActivity - %%sitetitle%% -->
<!-- seo-description: coreActivity plugin for WordPress can send email notifications: instant, daily digest and weekly digest. -->
<!-- seo-keywords: notifications -->

coreActivity has 3 types of notifications: instant, daily and weekly digest. All types of notifications will include only events that have one of these notifications enabled. To enable or disable events notifications, you can use [Events Panel](activity-events.md) or [Notifications](activity-tools.md#notifications) tool.

## Instant Notifications

When an eligible event is logged and instant notifications are enabled, the plugin can send an email immediately or schedule one after the configured interval. The default interval is five minutes.

### Delayed Instant Notifications

The interval limits how soon another instant email can be sent. Events occurring during the interval can be collected for a scheduled email instead of producing a separate message for each event. Scheduled delivery depends on WordPress cron and can occur later than the configured time.

## Daily and Weekly Digest

Digest notifications can be sent every day, or once a week (or both). Digest email includes overview of all logged events by component, with links to the Log component view.

![Example of Notifications Digest email](notifications-email.png)

In multisite environment, digest emails are sent only from the main network blog.

## Settings and Configuration

Plugin [Settings](activity-settings.md#notifications) for Notifications allow you to enable or disable each type, add emails to send notifications to (if not, website admin email will be used) and configure delay for instant notifications, and day and hour for sending digests.
