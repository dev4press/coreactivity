# REST API Logging

<!-- category: tutorials -->
<!-- tag: rest-api -->
<!-- level: advanced,developer -->
<!-- seo-title: %%post_title%% with coreActivity plugin - %%sitetitle%% -->
<!-- seo-description: Logging REST-API requests is a very interesting feature that can help discover issues with the REST API. -->
<!-- seo-keywords: rest-api -->

coreActivity since version 2.4 includes a component for logging activities related to the WordPress REST API using 8 events. By default, all except one event are active on installation, and one event for tracking REST-API read is set to inactive, and if you want to use it, you need to enable it from the [Events](activity-events.md) panel.

## Valid REST API requests

For responses below 400, coreActivity uses the response status and request method to select an event:

* **REST Item Read**: GET, HEAD, OPTIONS, and other methods not classified as edits or deletes. This event is inactive on registration because reads can generate many entries.
* **REST Item Delete**: a DELETE response.
* **REST Item Created**: a POST, PUT, or PATCH response with status 201.
* **REST Item Edited**: another POST, PUT, or PATCH response below 400.

These labels describe how the plugin classifies the response; they do not establish that an endpoint changed a particular item or that a request was authorized in a particular way. The **Skip read logging for server requests** setting can exclude reads identified as originating from the website's own server.

## Invalid REST API requests

If the REST API request is not valid, various things can happen, and coreActivity can log all sorts of errors and problems with the REST requests.

* **Unknown REST Route**: a response with status 404. A 404 alone does not establish why the resource or route was unavailable.
* **Unauthorized REST Request**: If the attempt is made to access REST route using method that requires authorization, without valid authorization, the attempt will be logged. This can point to malicious attempts, where bots try to push changes to your website without valid authorization.
* **Invalid REST Request**: This event covers REST requests that website can't resolve for various reasons, and return one of the 40x error codes (with the exception of 401 and 403).
* **REST Server Error**: If the request triggers internal server error on the website, this event will be logged. This can point to the server issue, missing or broken plugin that handles the REST route.

## Importance of REST API logging

There are many reasons to have REST API logging active. In recent years, REST API is very important part of the WordPress for various purposes, and it is used more and more. Because of that, REST API is a target of the activity that is not always desirable.

* You can find user agents of bots that are accessing your website content via REST API. These types of requests can't be logged by the normal website analytics systems, because they use JavaScript for tracking and can't track REST requests.
* You can discover malicious attempts that don't follow normal attack vectors, and are probing REST API routes to find some routs that can be exploited in some way due to the bad plugin implementing route.
* You can discover problems with the website content by analyzing invalid requests and server side errors happening during REST API calls.
* You can see all the malicious attempts to run REST API requests that are not authorized, or with invalid authorization pointing to a form of brute force attacks.

And, with the help of [coreSecurity Pro](https://plugins.dev4press.com/coresecurity/), these REST API logs can be helpful in increasing website security.
