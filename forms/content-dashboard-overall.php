<?php

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Statistics;
use Dev4Press\v43\Core\Quick\File;

$statistics = Statistics::instance()->overall();
$db_stats   = ! is_multisite() || is_network_admin() ? DB::instance()->get_statistics() : array();

?>

<div class="d4p-group d4p-dashboard-card d4p-card-double">
    <h3><?php esc_html_e( "Overall Statistics", "coreactivity" ); ?></h3>
    <div class="d4p-group-inner">
        <div class="coreactivity-overall-wrapper">
            <div class="coreactivity-overall-for-components">
                <i class="d4p-icon d4p-ui-folder d4p-icon-7x d4p-icon-fw"></i>
                <div>
                    <h4><?php _e( "Components", "coreactivity" ); ?></h4>
                    <div class="__available">
                        <strong><?php echo esc_html( $statistics[ 'components' ][ 'available' ] ); ?></strong> <?php esc_html_e( "Available", "coreactivity" ); ?>
                    </div>
                    <div>
                        <strong><?php echo esc_html( $statistics[ 'components' ][ 'total' ] ); ?></strong> <?php esc_html_e( "Total Registered", "coreactivity" ); ?>
                    </div>
                </div>
            </div>
            <div class="coreactivity-overall-for-events">
                <i class="d4p-icon d4p-ui-radar d4p-icon-7x d4p-icon-fw"></i>
                <div>
                    <h4><?php _e( "Events", "coreactivity" ); ?></h4>
                    <div class="__available">
                        <strong><?php echo esc_html( $statistics[ 'events' ][ 'available' ] ); ?></strong> <?php esc_html_e( "Available", "coreactivity" ); ?>
                    </div>
                    <div>
                        <strong><?php echo esc_html( $statistics[ 'events' ][ 'total' ] ); ?></strong> <?php esc_html_e( "Total Registered", "coreactivity" ); ?>
                    </div>
                </div>
            </div>
        </div>
		<?php if ( ! empty( $db_stats ) ) { ?>
            <hr class="coreactivity-overall-sep"/>
            <div class="coreactivity-database-wrapper">
                <i class="d4p-icon d4p-ui-database d4p-icon-5x d4p-icon-fw"></i>
                <div>
                    <div class="__element">
                        <strong><?php echo esc_html( $db_stats[ 'tables' ][ 'logs' ][ 'rows' ] ); ?></strong> <?php esc_html_e( "Log Entries", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( File::size_format( $db_stats[ 'tables' ][ 'logs' ][ 'total' ], 2, ' ', false ) ); ?></strong> <?php esc_html_e( "Log Size", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( $db_stats[ 'tables' ][ 'logmeta' ][ 'rows' ] ); ?></strong> <?php esc_html_e( "Metadata Rows", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( File::size_format( $db_stats[ 'tables' ][ 'logmeta' ][ 'total' ], 2, ' ', false ) ); ?></strong> <?php esc_html_e( "Metadata Size", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( $db_stats[ 'tables' ][ 'events' ][ 'rows' ] ); ?></strong> <?php esc_html_e( "Events Records", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( File::size_format( $db_stats[ 'tables' ][ 'events' ][ 'total' ], 2, ' ', false ) ); ?></strong> <?php esc_html_e( "Events Size", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( $db_stats[ 'oldest' ] ); ?></strong> <?php esc_html_e( "Oldest Entry Day", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( $db_stats[ 'range' ] . ' ' . __( "days", "coreactivity" ) ); ?></strong> <?php esc_html_e( "Log Range", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong>3</strong> <?php esc_html_e( "Database Tables", "coreactivity" ); ?>
                    </div>
                    <div class="__element">
                        <strong><?php echo esc_html( File::size_format( $db_stats[ 'size' ], 2, ' ', false ) ); ?></strong> <?php esc_html_e( "Total Tables Size", "coreactivity" ); ?>
                    </div>
                </div>
            </div>
		<?php } ?>
    </div>
</div>
