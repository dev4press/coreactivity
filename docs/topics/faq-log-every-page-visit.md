# Does coreActivity log every page visit?

<!-- type: faq -->
<!-- category: getting-started -->
<!-- tag: logging,events -->
<!-- seo-title: %%post_title%% - %%sitetitle%% -->
<!-- seo-description: Learn which activity coreActivity records and how page caching affects logging. -->
<!-- seo-keywords: logging,events,cache -->

coreActivity logs supported, enabled events when their components detect activity during WordPress processing. It is not a record of every page view. A full-page cache can serve a request without loading WordPress, so coreActivity cannot record activity for that request.

See [How Activity Logging Works](how-activity-logging-works.md), [Activity Events](activity-events.md), and [Cache Plugins](cache-plugins.md).
