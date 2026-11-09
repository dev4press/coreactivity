# Detecting Visitor IP

<!-- category: getting-started -->
<!-- tag: ip -->
<!-- seo-title: %%post_title%% in coreActivity plugin - %%sitetitle%% -->
<!-- seo-description: Detecting visitor IP is not straightforward process, and there are various things to take into consideration. -->
<!-- seo-keywords: ip -->

coreActivity records an IP when one is available for an event. The value depends on your web server, proxies, and request headers; it is not proof of a visitor's identity.

## Direct and forwarded addresses

`REMOTE_ADDR` normally identifies the immediate peer connecting to the server. Behind a proxy or load balancer, that may be the proxy instead of the visitor. Server configuration can also alter the value supplied to PHP.

The forwarded IP option in [Settings](activity-settings.md#logging-data) and the [Setup Wizard](using-setup-wizard.md) allows the plugin to consider `X-Forwarded-For` and `X-Forwarded` in addition to `REMOTE_ADDR`. The helper considers the available addresses in order, preferring public addresses over private and loopback addresses. A public `REMOTE_ADDR` can therefore be selected before a forwarded value. When the chosen address differs from the remote address, the latter can also be stored as metadata and indicated by a shield in the log.

Forwarded headers can be supplied by a visitor unless your infrastructure removes or replaces untrusted headers. A valid-looking IP is not necessarily the real visitor. Before enabling this option, check which proxies connect to your server and how they handle these headers. Test with known requests and compare the log with your server or proxy records.

## Cloudflare

When the immediate peer matches the plugin's Cloudflare address list and a Cloudflare connecting-IP header is present, the helper handles that header separately, including when the general forwarded IP option is disabled. Server-side address rewriting or another proxy in front of WordPress can affect this detection; verify the result for your setup.
