<?php

use function Dev4Press\v51\Functions\panel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include COREACTIVITY_PATH . 'forms/content-about-minor.php';

?>

<div class="d4p-about-whatsnew">
    <div class="d4p-whatsnew-section d4p-whatsnew-heading">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit whole align-center">
                <h2>WordPress Activity Logging</h2>
                <p class="lead-description">
                    coreActivity now has over 150 events to monitor your website
                </p>
                <p>
                    Since the version 1.0 is released, the coreActivity plugin has seen extensive updates, and the Version 2.0 again expands the list of events, integrates into Users panel and more.
                </p>

				<?php if ( isset( $_GET['install'] ) && sanitize_key( $_GET['install'] ) === 'on' ) { // phpcs:ignore WordPress.Security.NonceVerification ?>
                    <a class="button-primary" href="<?php echo esc_url( panel()->a()->panel_url( 'wizard' ) ); ?>"><?php esc_html_e( 'Run Setup Wizard', 'coreactivity' ); ?></a>
				<?php } ?>

                <div class="coreactivity-about-counters">
                    <div><i class="d4p-icon d4p-ui-folder d4p-icon-fw"></i> <strong>28</strong> Components</div>
                    <div><i class="d4p-icon d4p-ui-radar d4p-icon-fw"></i> <strong>182</strong> Events</div>
                    <div><i class="d4p-icon d4p-ui-plug d4p-icon-fw"></i> <strong>12</strong> Plugins</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d4p-whatsnew-section core-grid">
        <div class="core-row">
            <div class="core-col-sm-12 core-col-md-6">
                <h3>Fully Modular</h3>
                <p>
                    The Plugin is based around components, and each component has one or more events it can track and log. Via plugin settings, you can disable events you don't want to use.
                </p>
                <p>
                    Plugin can be expanded with more components and events to track more things. Plugin already includes support for many popular plugins, and that number will grow over time.
                </p>
                <p>
                    The Plugin has full support for the Multisite Network, and it has to be activated Network wide if your website is running in the network mode.
                </p>
            </div>
            <div class="core-col-sm-12 core-col-md-6">
                <h3>Database Logging</h3>
                <p>
                    All the events logged are stored in the database. The Plugin adds three database tables, one for all the registered events, and two for log with the additional logmeta table.
                </p>
                <p>
                    The Plugin can automatically remove old entries from the log (controllable via settings), and it also has a tool where you can remove by age and selected events.
                </p>
                <p>
                    The Plugin can automatically remove old entries from the log (controllable via settings), and it also has a tool where you can remove by age and selected events.
                </p>
            </div>
        </div>

        <div class="core-row">
            <div class="core-col-sm-12 core-col-md-6">
                <h3>Logs Panel</h3>
                <p>
                    Easy to use a very powerful Logs panel will allow you to filter the log entries, search, and reorder. You can filter by several log parameters, including the date ranges.
                </p>
                <p>
                    Each log entry can have additional metadata logged, and that can have a large number of items, so the log panel hides metadata by default, and you can open and display it under each log row.
                </p>
            </div>
            <div class="core-col-sm-12 core-col-md-6">
                <h3>Advanced Features</h3>
                <p>
                    Plugin can run geolocation for each logged IP and display the flag of country (if possible). And, each IP in the log will be marked with the comparison with current server and current visitor IPs.
                </p>
                <p>
                    Logs panel has live update feature, and every 10 seconds will make the AJAX call to get the latest events based on the current Log filters, allowing you to monitor events logged in real time.
                </p>
            </div>
        </div>
    </div>
</div>
