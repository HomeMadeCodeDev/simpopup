<?php

// Only uninstall if Wordpress itself asked for uninstallation
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
} 

// Remove the options of the extension
delete_option( 'simpopup-cookie-value' );
delete_option( 'simpopup_adminonly' );
delete_option( 'simpopup_enabled' );
delete_option( 'simpopup_html' );
delete_option( 'simpopup_css' );
delete_option( 'simpopup_js' );