<?php 

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here
}
?>