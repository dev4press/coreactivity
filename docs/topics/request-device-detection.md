# Request Device Detection

<!-- category: feature -->
<!-- tag: device,detection -->
<!-- seo-title: %%post_title%% with coreActivity - %%sitetitle%% -->
<!-- seo-description: Each request logged by coreActivity can be processed to detect the device and the software making the request. -->
<!-- seo-keywords: device,detection -->

Each request coming to the website has User Agent used to identify the device and software making the request. Device information can be displayed in the main Log as a column, or inside the meta row or the event popup.

![Detected Bot in the entry popup dialog](popup-device-detection.png)

> Device Detection is done using the [Matomo Device Detector](https://github.com/matomo-org/device-detector) library.

## Is detection reliable

Device Detection is very reliable process, and the used library is regularly maintained including lists of thousands of devices, bots, browsers and more. But, there are problems with the actual user agent that go beyond the detection.

* User Agent value can be faked by the software making the request. This means that bad bots can set user agent of the legitimate browser or bot as a mask.
* Even with the possibility that user agent is fake, it is still a useful data because most bots do use own user agent and this value can help with the firewall.

But, even when bots fake the user agent, the bots software is usually old, and user agent may be set to very old and outdated browser or version, making it easier to recognize as a bad bot.

## Requirements

For device detection to work, you need to have user agent logged with every request. Check out the [Settings](activity-settings.md#optional-data) panel to make sure user agent logging is enabled.
