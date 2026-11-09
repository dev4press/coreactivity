# Components and Events

<!-- category: getting-started -->
<!-- tag: components,events -->
<!-- seo-title: coreActivity %%post_title%% - %%sitetitle%% -->
<!-- seo-description: coreActivity has 20+ components and 150+ events registered by those components, and directly supports many popular plugins. -->
<!-- seo-keywords: events,components -->

coreActivity plugin registers 20+ components, and each component registers one or more events. And, other plugins can register own components and add events. To track all that, and make sure that all events are logged correctly, all events are stored into database table.

## Events Registration

Each event is registered, along with the component that owns it.

Each component code has two parts: the plugin adding the component and the component name. Components added by coreActivity use `coreactivity/{component-name}`. Each event code is paired with its component, so different components can use the same event code.

Some components and events can have requirements to work, so that events related to a specific plugin will be used only if that plugin is active.

* Some events can be disabled initially, on registration.
* Events on registration have notifications disabled. 
* Each event can be enabled or disabled via the Events panel.

## Events Panel

To get more information about the Events panel, check out this [article](activity-events.md).

## List of Events

To see all the events registered by the coreActivity plugin, check out this [article](events.md).

## Third Party Plugins

Plugin currently supports 12 plugins directly, having dedicated components integrated in coreActivity.

### Support integrated in coreActivity

coreActivity includes integrations for the following plugins. Availability depends on the corresponding plugin being active; consult the [event list](events.md) for the events registered by each integration.

* bbPress (6 events)
* BuddyPress (4 events)
* Contact Form 7 (3 events)
* [DebugPress](https://wordpress.org/plugins/debugpress/) (9 event)
* Duplicate Post (1 event)
* Forminator (1 event)
* [GD Forum Manager](https://wordpress.org/plugins/gd-forum-manager-for-bbpress/) (4 event)
* Gravity Forms (6 events)
* Jetpack (2 events)
* [SweepPress](https://wordpress.org/plugins/sweeppress/) Pro and Lite (11 events)
* User Switching (4 events)
* WooCommerce (6 events)

### Plugin that register own components and events

Right now several Dev4Press plugins include components for coreActivity, and this will be expanded in the future.

* [GD bbPress Toolbox Pro](https://plugins.dev4press.com/gd-bbpress-toolbox/) (2 events)
* [coreSecurity Pro](https://plugins.dev4press.com/coresecurity/) (30+ events)

#### coreSecurity Pro

coreSecurity Pro depends on coreActivity, and it practically can't work without coreActivity, with most features depending on having logs.
