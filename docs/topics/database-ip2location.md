# Database IP2Location Lite

<!-- category: features -->
<!-- tag: location,ip2location -->
<!-- seo-title: %%post_title%% in coreActivity plugin - %%sitetitle%% -->
<!-- seo-description: IP2Location is popular GEO Location service, and it is supported by coreActivity to identify the source of the logged IPs. -->
<!-- seo-keywords: ip2location -->

IP2Location is a popular GEO Location database service, and coreActivity supports use of the Free / Lite Geo location database.

> This site or product includes IP2Location LITE data available from <a href="https://lite.ip2location.com">https://lite.ip2location.com</a>.

IP2Location has free and premium services and databases. For this plugin purposes, Lite database is quite sufficient, and coreActivity at this time supports use of Lite databases only. 

coreActivity plugin automatically downloads and updates the database file, and for that, it needs valid download token from the [IP2Location website](https://lite.ip2location.com/). To get the download token, register on the IP2Location Lite website (it is free), and once you are logged in, get the free download token and use in the Geo Location settings. Plugin will attempt to download database file once a week during regular weekly maintenance, or when the plugin is updated.

![IP2Location Settings](settings-geo-ip2location.png)

## Available Database Versions

IP2Location provides several different free databases. They have different amount of data, and you can choose which one works for you best. All databases provide support both IPv4 and IPv6 addresses.

Smallest database version contains only Country locations, and it is about 7MB in size. The largest database is around 150MB, and it contains Country, City, Region, Location, ZIP Code and Timezone.
