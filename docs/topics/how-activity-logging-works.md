# How Activity Logging Works

<!-- category: getting-started -->
<!-- tag: logging -->
<!-- seo-title: %%post_title%% with coreActivity - %%sitetitle%% -->
<!-- seo-description: Learn how coreActivity records events, request details, and optional metadata. -->
<!-- seo-keywords: logging -->

Components listen for supported WordPress and integration activity. When an enabled event occurs, its component prepares the event data and passes it to coreActivity for storage. See [Components and Events](components-and-events.md) to choose which events to track.

## Data for Each Entry

The fields and metadata depend on the event and settings. Activity can originate from a web request, REST API, AJAX, cron, or CLI. Some values may be empty or use defaults when they are not available.

### Required Data

The main log record can contain:

* **Blog and event IDs**: the site associated with the entry and the registered event. Network-level events can use blog ID `0`.
* **User ID and time**: the associated user where available, or `0`, and the logged time in GMT. CLI and cron contexts are treated as anonymous.
* **IP and request details**: the selected IP, request context, HTTP method, protocol, and request path and query string where available. Proxy configuration affects the IP; see [Detecting Visitor IP](detecting-visitor-ip.md).
* **Object type, ID, and name**: identify the relevant post, user, option, or other object when supplied by the event.
* **Country code**: stored when country logging is enabled and a location is available.

Not every field applies to every event. A logged request can contain information in its query string; see [Log Data and Retention](log-data-and-retention.md).

### Optional Data

Depending on settings and availability, additional values are stored as metadata linked to the main log entry.

* User Agent: each web based request comes with the User Agent value used to identify the software making the request. This is used to identify source device. **User Agent value can be faked by the software making the request.**
* Referrer: if the request comes from some other page, that can be included as a referrer value for the request.
* Device Detection: plugin can detect the request source software/device from the User Agent, and this can be stored to speed up the log review later.
* GEO Location: this can be detailed information about GEO located information from the request IP.

### Event Specific Meta Data

Events may add their own metadata. Its contents vary by component, event, and configuration.

## Storing data in Database

The main fields are stored in the logs table; additional values are stored in the log metadata table. See [Database Schema](database-schema.md) for the table structure and [Log Data and Retention](log-data-and-retention.md) for cleanup options.
