# coreActivity & Cache Plugins

<!-- category: getting-started -->
<!-- tag: cache -->
<!-- level: advanced -->
<!-- seo-title: %%post_title%% - %%sitetitle%% -->
<!-- seo-description: Cache plugins modify the way WordPress website processes request, and it does affect various aspects of the website normal operations. -->
<!-- seo-keywords: cache -->

## How Cache plugins work

Most cache plugins (including popular WP Super Cache, W3 Total Cache, WP Rocket...) are caching the full page response as HTML, and store that static HTML to load it later, bypassing WordPress loading and request processing to speed up the response time by serving content that is rendered and cached.

That means, that for some requests coming to the website, if there is a cached HTML matching the request, that HTML will be served, and WordPress is not loaded. That means, that plugins running inside the WordPress will not be loaded or used for such requests.

When a specific cached request or whole cache is cleared, the cache plugin will again generate cache the next time a request is made, and to generate cache, the full WordPress process and all the plugins will be executed and events logging will work as normal. 

## coreActivity and Cache

coreActivity is a WordPress plugin, and it works inside the WordPress normal processing, and it depends on WordPress loading and running to work. That means, that if the cached page is served, WordPress doesn't load, and with that coreActivity plugin doesn't load.

If a user visits a page before the page is cached (or after the cache is cleared), the WordPress loads, and any events happening, will be logged by the coreActivity.

Even with the use of cache, everything of importance will be logged by coreActivity, and only repeated occurrences of specific events will not be logged, if exactly the same request is made and found in cache.

## Events affected

Only few events are actually (somewhat) affected by the cache, and only events belonging to the [`coreactivity/error`](events.md#errors-coreactivity-error) component. This component monitors the 404 pages on the front end, and it logs various cases of the missing page or invalid URL (to the missing file, for instance). These events are also useful to catch attempts to probe website for the specific URL patterns known for various exploits.

**These events will still be logged**, even with cache in use, but only first instance of the unique 404 request will be logged (until the cache is cleared, and the same URL can again trigger the event for logging). Finally, some cache plugins don't even cache 404 requests, so this might not be an issue at all.

So, this component will still log all the unique 404 requests, but will not log every instance of that same event, and will log only event happening with un-cached response.

## Third party events

Since other plugins can register components and events in coreActivity, it will depend on the specific cases. But, everything possible will be logged, if the WordPress is running, and it is triggering the event logging code.
