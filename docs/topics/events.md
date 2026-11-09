# List of Events

<!-- category: getting-started -->
<!-- tag: events -->
<!-- seo-title: %%post_title%% in coreActivity plugin - %%sitetitle%% -->
<!-- seo-description: Full list of events registered by every included component in coreActivity plugin with plugin version when the event is added. -->
<!-- seo-keywords: events,components -->

All supported coreActivity events are split into components. Components are split into categories. Here is the list of all components and events it has.

## Internal

### Internal (`coreactivity/internal`)

* Daily Digest `digest-daily`
* Weekly Digest `digest-weekly`
* Log Cleanup `log-cleanup`
* Auto Log Cleanup `log-cleanup-auto`

## WordPress

### Attachments (`coreactivity/attachment`)

* Attachment Deleted `deleted`
* Attachment Edited `edited`
* Attachment Uploaded `uploaded`

### Comments (`coreactivity/comment`)

* Comment Deleted `deleted`
* Comment Meta Added `meta-added` (v2.0)
* Comment Meta Deleted `meta-deleted` (v2.0)
* Comment Meta Updated `meta-updated` (v2.0)
* Comment Status Change `status-change`

### Errors (`coreactivity/error`) {id="errors-coreactivity-error"}

* 404 Not Found `404`
* 404 Not Found File `404-file`
* 404 Not Found Media `404-media`
* 404 Not Found PHP `404-php`
* 404 Not Found Script `404-script`
* 404 Not Found Style `404-style`

### Multisite Network (`coreactivity/network`)

* Blog Created `blog-created`
* Blog Removed `blog-removed`
* Blog Signup `blog-signup`
* Blog Status Archive `blog-status-archive`
* Blog Status Deleted `blog-status-delete`
* Blog Status Mature `blog-status-mature`
* Blog Status Not Archive `blog-status-not-archive`
* Blog Status Not Deleted `blog-status-not-delete`
* Blog Status Not Mature `blog-status-not-mature`
* Blog Status Not Spam `blog-status-not-spam`
* Blog Status Private `blog-status-private`
* Blog Status Public `blog-status-public`
* Blog Status Spam `blog-status-spam`
* Blog Updated `blog-updated`
* Failed Blog Signup `failed-blog-signup`

### Notifications (`coreactivity/notification`)

* Email Failed `email-failed`
* Unknown Email Failed `email-failed-unknown`
* Email Sent `email-sent`
* Unknown Email Sent `email-sent-unknown`

### Options (`coreactivity/option`)

* Core Option Deleted `core-option-deleted`
* Core Option Changed `core-option-edited`
* Option Added `option-added`
* Option Deleted `option-deleted`
* Option Changed `option-edited`
* Transient Deleted `transient-deleted`
* Transient Set `transient-set`

### Plugins (`coreactivity/plugin`)

* Plugin Activated `activated`
* Plugin Deactivated `deactivated`
* Plugin Deleted `deleted`
* Plugin Install Error `install-error` (v2.0)
* Plugin Installed `installed` (v2.0)
* Plugin Network Activated `network-activated`
* Plugin Network Deactivated `network-deactivated`
* Plugin Updated Error `update-error` (v2.0)
* Plugin Updated `updated` (v2.0)

### Posts (`coreactivity/post`)

* Post Deleted `deleted`
* Post Meta Added `meta-added` (v2.0)
* Post Meta Deleted `meta-deleted` (v2.0)
* Post Meta Updated `meta-updated` (v2.0)
* Post Status Change `status-change`
* Post Term Relationship Changes `term-relationship-change`

### Privacy (`coreactivity/privacy`)

* Personal Data Erased `erased-personal-data` (v1.8)
* Completed Export of Personal Data `export-personal-data-completed` (v1.8)
* Confirmed Export of Personal Data `export-personal-data-confirmed` (v1.8)
* Deleted Request for Export of Personal Data `export-personal-data-request-deleted` (v1.8)
* Requested Export of Personal Data `export-personal-data-requested` (v1.8)
* Created Personal Data File `personal-data-file-created` (v1.8)
* Completed Removal of Personal Data `remove-personal-data-completed` (v1.8)
* Confirmed Removal of Personal Data `remove-personal-data-confirmed` (v1.8)
* Deleted Request for Removal of Personal Data `remove-personal-data-request-deleted` (v1.8)
* Requested Removal of Personal Data `remove-personal-data-requested` (v1.8)

### REST API (`coreactivity/rest-api`)

* REST Item Created `rest-create-item` (v2.4)
* REST Item Delete `rest-delete-item` (v2.4)
* REST Item Edited `rest-edit-item` (v2.4)
* REST Item Read `rest-read-item` (v2.4)
* Invalid REST Request `rest-request-invalid` (v2.4)
* Unauthorized REST Request `rest-request-unauthorized` (v2.4)
* Unknown REST Route `rest-route-unknown` (v2.4)
* REST Server Error `rest-server-error` (v2.4)

### Sitemeta (`coreactivity/sitemeta`)

* Core Sitemeta Deleted `core-sitemeta-deleted`
* Core Sitemeta Changed `core-sitemeta-edited`
* Sitemeta Added `sitemeta-added`
* Sitemeta Deleted `sitemeta-deleted`
* Sitemeta Changed `sitemeta-edited`
* Site Transient Deleted `transient-deleted`
* Site Transient Set `transient-set`

### Terms (`coreactivity/term`)

* Term Created `created`
* Term Deleted `deleted`
* Term Edited `edited`
* Term Meta Added `meta-added` (v2.0)
* Term Meta Deleted `meta-deleted` (v2.0)
* Term Meta Updated `meta-updated` (v2.0)

### Themes (`coreactivity/theme`)

* Theme Deleted `deleted`
* Theme Install Error `install-error` (v2.0)
* Theme Installed `installed` (v2.0)
* Theme Switched `switched`
* Theme Updated Error `update-error` (v2.0)
* Theme Updated `updated` (v2.0)

### Users (`coreactivity/user`)

* Activate User `activate-user`
* User Deleted `deleted`
* Edited Display Name `edited-display-name`
* Edited Email `edited-email`
* Edited Meta `edited-meta-data`
* Edited Password `edited-password`
* Edited URL `edited-url`
* Failed Login `failed-login`
* Failed Login Cookie `failed-login-cookie`
* Failed User Signup `failed-user-signup`
* Login `login`
* Logout `logout`
* User Meta Added `meta-added` (v2.0)
* User Meta Deleted `meta-deleted` (v2.0)
* User Meta Updated `meta-updated` (v2.0)
* Password Reset `password-reset`
* Request Password Reset `password-reset-request`
* Request Password Reset Invalid `password-reset-request-invalid`
* Registered `registered`
* User Role Changed `role-changed`
* User Signup `user-signup`

### WordPress (`coreactivity/wordpress`)

* Available WordPress Update `available-update-core` (v1.8)
* Available Plugin Updates `available-update-plugin` (v1.8)
* Available Theme Updates `available-update-theme` (v1.8)
* Content Export `content-export`
* CRON Event Scheduled `cron-schedule`
* Database Delta Queries `database-delta`
* WordPress Core Update `update-core`
* WordPress Core Auto Update `update-core-auto`

## Plugin

### bbPress (`coreactivity/bbpress`)

* Post Deleted `deleted`
* Post Meta Added `meta-added` (v2.0)
* Post Meta Deleted `meta-deleted` (v2.0)
* Post Meta Updated `meta-updated` (v2.0)
* Post Status Change `status-change`
* Post Term Relationship Changes `term-relationship-change`

### BuddyPress (`coreactivity/buddypress`)

* Component Activated `component-activated`
* Component Deactivated `component-deactivated`
* Group Created `group-created`
* Group Updated `group-updated`

### Contact Form 7 (`coreactivity/contact-form-7`)

* Form Deleted `form-deleted`
* Submission Failed `submission-failed`
* Submission Sent `submission-sent`

### DebugPress (`coreactivity/debugpress`)

* Admin AJAX Call `admin-ajax-call` (v1.3)
* Deprecated Argument `deprecated-argument`
* Deprecated Constructor `deprecated-constructor`
* Deprecated File `deprecated-file`
* Deprecated Function `deprecated-function`
* Deprecated Hook Run `deprecated-hook-run`
* Doing It Wrong `doing-it-wrong`
* HTTP API Call `http-api-call` (v1.3)
* PHP Error `php-error`

### Duplicate Post (`coreactivity/duplicate-post`)

* Post Duplicated `duplicated`

### Forminator (`coreactivity/forminator`)

* Form Deleted `form-deleted`

### GD Forum Manager (`coreactivity/gd-forum-manager`)

* Forum Bulk Edit `forum-bulk` (v1.4)
* Forum Edit `forum-edit` (v1.4)
* Topic Bulk Edit `topic-bulk` (v1.4)
* Topic Edit `topic-edit` (v1.4)

### Gravity Forms (`coreactivity/gravityforms`)

* Form Activated `activated`
* Form Created `created`
* Form Deactivated `deactivated`
* Form Deleted `deleted`
* Form Restored `restored`
* Form Trashed `trashed`

### Jetpack (`coreactivity/jetpack`)

* Activated Module `module-activated`
* Deactivated Module `module-deactivated`

### SweepPress (`coreactivity/sweeppress`)

* Sweeping Completed `completed`
* Deleted CRON job `cron-deleted`
* Run CRON job `cron-run`
* Options Deleted `options-deleted`
* Sitemeta Deleted `sitemeta-deleted`
* Postmeta Deleted `postmeta-deleted`
* Termmeta Deleted `termmeta-deleted`
* Commentmeta Deleted `commentmeta-deleted`
* Usermeta Deleted `usermeta-deleted`
* Blogmeta Deleted `blogmeta-deleted`
* Logmeta Deleted `logmeta-deleted`

### User Switching (`coreactivity/user-switching`)

* Switched Back `switch-back`
* Switched Back from User `switch-back-user`
* Switched Off `switch-off-user`
* Switched to User `switch-to-user`

### WooCommerce (`coreactivity/woocommerce`)

* Post Deleted `deleted` (v1.1)
* Post Meta Added `meta-added` (v2.0)
* Post Meta Deleted `meta-deleted` (v2.0)
* Post Meta Updated `meta-updated` (v2.0)
* Post Status Change `status-change` (v1.1)
* Post Term Relationship Changes `term-relationship-change` (v1.1)
