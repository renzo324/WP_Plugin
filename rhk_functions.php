<?php 
/**
 * Plugin Name: Rehaka Functions
 * Plugin URI: N/A
 * Description: Short code generator and optimization packages for ReHaKa
 * Author: Lorenzo Ibay
 * Author URI: http://github.com/renzo324/
 * Version: 1.0
 *
 * 
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *      
 */
// Prevent direct calling
if (!defined('WPINC')) {
    die;
}
/**
 * Check if WooCommerce is active
 **/
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}