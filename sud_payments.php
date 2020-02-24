<?php 
/**
 * Plugin Name: SUD Payments
 * Plugin URI: N/A
 * Description: Custom payment gateway and other optimization packages
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
if (!class_exists('SUD_Payments_WPWC')){
    add_action('plugins_loaded', 'SUD_Payments_WPWC_init', 11);
   function SUD_Payments_WPWC_init(){
    class SUD_Payments_WPWC extends WC_Payment_Gateway{
        public $title;
        function __construct(){
            $this->title = plugin_basename(__FILE__);
            add_action('wp_enqueue_scripts', array(
                $this, 'enqueue'
            ));
        }
    } // Class End
   }// fucntion end
}
?>