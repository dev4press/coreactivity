# Activities GEO Location

<!-- type: user-guide -->
<!-- category: getting-started -->
<!-- tag: location -->
<!-- seo-title: %%post_title%% in coreActivity plugin - %%sitetitle%% -->
<!-- seo-description: To identify the source of every IP logged in the activities log, coreActivity can use different GEO Locations methods for best results. -->
<!-- seo-keywords: events,components -->

Based on the IP from the event, plugin can GEO locate the request source. The location can include country, state (or region) and city, and can also include latitude and longitude too.

For request analysis, the plugin can store a country code and, optionally, additional information returned by the selected provider.

## Available Services

Right now, plugin supports 3 different methods for GEO location.

* **Online** [GeoJS.io](online-geojs-io.md)
* **Database** [IP2Location](database-ip2location.md)
* **Database** [MaxMind](database-maxmind.md)

To change active Geo Location service, use the **Geo Location** settings panel. Database services require license or token to enable database download.

The providers offer IPv4 and IPv6 support; the available detail depends on the selected database and the address. The online method sends IP lookup requests to the external service and depends on its availability. Local databases require a provider token or license for downloads and updates, but lookups use the downloaded file. Consider your site's privacy requirements and network configuration when choosing a method.

![Location displayed in entry popup](popup-location.png)

## Location Limitations

It is very important to know that GEO Location is not perfect, and it is not 100% correct! It is not a good idea to base decisions on the GEO located country alone, because there are many factors that can influence the locations for the IP. **GEO location can't identify individual address or household!**

### Data Accuracy

Country and city results are estimates and can be missing or incorrect. Proxies, VPNs, mobile networks, and outdated records can affect the result. Coordinates are approximate and do not identify a street address or household.

## GEO Location Database

In general, GEO location is based on the data for the IP (both IPv4 and IPv6) assignments done by IANA (Internet Assigned Numbers Authority) and local ISP (Internet Service Provider) or other authorities. All that data is compiled into the database that is updated over time.

Provider data changes over time; an IP assignment and its estimated location can change. Keep local databases updated and avoid treating a location as definitive.

## Recommendations

* In [Settings](activity-settings.md#optional-data), enable country code and/or expanded location data as needed. Results depend on the selected provider and the information available for the address.
* Choose a local database if you prefer local lookups and can maintain its download credentials. Choose the online service if its external requests and availability are suitable for your site.
