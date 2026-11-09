# Database MaxMind GeoLite2

<!-- category: features -->
<!-- tag: location,geolite2 -->
<!-- seo-title: %%post_title%% in coreActivity plugin - %%sitetitle%% -->
<!-- seo-description: MaxMind GEOLite2 is popular GEO Location service, and it is supported by coreActivity to identify the source of the logged IPs. -->
<!-- seo-keywords: geolite2 -->

MaxMind is provider for the GeoIP2 and GeoLite2 databases. GeoLite2 is free to use, and similar to IP2Location service, you need to have a free license code to enable database download and update.

> This product includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com">https://www.maxmind.com</a>.

To get the download license, register on the [MaxMind website](https://www.maxmind.com/en/account/login), and once you are logged in there, generate new license. Plugin will attempt to download database file once a week during regular weekly maintenance.

![MaxMind GeoLite2 Settings](settings-geo-maxmind.png)

## Available Database Versions

GeoLite2 offers Country and City databases. Both support IPv4 and IPv6 addresses.

The Country database provides country-level information. The City database can also provide city, approximate coordinates, postal code, and time zone where available. Database sizes change as the provider updates the files.
