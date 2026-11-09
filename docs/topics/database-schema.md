# Database Schema

<!-- category: getting-started -->
<!-- tag: database-schema -->
<!-- level: advanced,developer -->
<!-- seo-title: %%post_title%% for coreActivity - %%sitetitle%% -->
<!-- seo-description: coreActivity plugin installs various new database tables needed to store data for some features. -->
<!-- seo-keywords: database -->

The plugin uses three network-scoped tables. The `wp_` prefix below is an example; the actual base prefix depends on the WordPress installation. On multisite, the tables are shared across the network and log entries identify their site with `blog_id` where applicable.

## Events

This table holds information and settings for every event registered in the plugin.

```SQL
CREATE TABLE `wp_coreactivity_events` (
	`event_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`category` VARCHAR(32) NOT NULL DEFAULT 'wordpress',
	`component` VARCHAR(128) NOT NULL DEFAULT '',
	`event` VARCHAR(128) NOT NULL DEFAULT '',
	`status` VARCHAR(32) NOT NULL DEFAULT 'active',
	`rules` TEXT NULL DEFAULT NULL,
	PRIMARY KEY (`event_id`),
	UNIQUE INDEX `category_component_event` (`category`, `component`, `event`),
	INDEX `component` (`component`),
	INDEX `event` (`event`),
	INDEX `status` (`status`),
	INDEX `category` (`category`)
) ENGINE=InnoDB;
```

## Logs

This is the main table for logged data.

```SQL
CREATE TABLE `wp_coreactivity_logs` (
	`log_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`blog_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
	`event_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
	`user_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
	`logged` DATETIME NULL DEFAULT NULL,
	`ip` VARCHAR(64) NULL DEFAULT NULL,
	`context` VARCHAR(16) NOT NULL DEFAULT '' COMMENT 'REST, CRON, AJAX, CLI',
	`method` VARCHAR(16) NOT NULL DEFAULT '' COMMENT 'POST, GET, PUT, DELETE ...',
	`protocol` VARCHAR(16) NOT NULL DEFAULT '' COMMENT 'HTTP/1.0, HTTP/1.1 ...',
	`request` TEXT NULL DEFAULT NULL,
	`object_type` VARCHAR(64) NULL DEFAULT NULL,
	`object_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
	`object_name` VARCHAR(255) NULL DEFAULT NULL,
	`country_code` CHAR(2) NULL DEFAULT NULL,
	PRIMARY KEY (`log_id`),
	INDEX `blog_id` (`blog_id`),
	INDEX `event_id` (`event_id`),
	INDEX `user_id` (`user_id`),
	INDEX `logged` (`logged`),
	INDEX `ip` (`ip`),
	INDEX `context` (`context`),
	INDEX `method` (`method`),
	INDEX `object_type` (`object_type`),
	INDEX `object_id` (`object_id`),
	INDEX `object_name` (`object_name`),
	INDEX `country_code` (`country_code`)
) ENGINE=InnoDB;
```

## Logmeta

This is a meta table for all the logged data.

```SQL
CREATE TABLE `wp_coreactivity_logmeta` (
	`meta_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`log_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
	`meta_key` VARCHAR(128) NOT NULL DEFAULT '',
	`meta_value` LONGTEXT NULL DEFAULT NULL,
	PRIMARY KEY (`meta_id`),
	INDEX `log_id` (`log_id`),
	INDEX `meta_key` (`meta_key`)
) ENGINE=InnoDB;
```