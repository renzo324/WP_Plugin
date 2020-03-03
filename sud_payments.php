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
add_filter( 'woocommerce_payment_gateways', 'ick_add_gateway_class' );
function ick_add_gateway_class( $gateways ) {
	$gateways[] = 'SUD_Payments_WPWC'; // your class name is here
	return $gateways;
}
 
if (!class_exists('SUD_Payments_WPWC')){
    add_action('plugins_loaded', 'SUD_Payments_WPWC_init', 11);
   function SUD_Payments_WPWC_init(){
    class SUD_Payments_WPWC extends WC_Payment_Gateway{
      
 		public function __construct() {
            $this->id = 'other_payment';
            $this->method_title = __('ICK Gateway','woocommerce-other-payment-gateway');
            $this->title = __('ICK Gateway','woocommerce-other-payment-gateway');
            $this->has_fields = true;
            $this->init_form_fields();
            $this->init_settings();
            $this->enabled = $this->get_option('enabled');
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->hide_text_box = $this->get_option('hide_text_box');
            $this->text_box_required = $this->get_option('text_box_required');
            $this->order_status = $this->get_option('order_status');
    
    
            add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
         }
            /**
              * Plugin options
              */
              public function init_form_fields(){
 
                $this->form_fields = array(
                    'enabled' => array(
                        'title'       => 'Enable/Disable',
                        'label'       => 'Enable Gateway',
                        'type'        => 'checkbox',
                        'description' => '',
                        'default'     => 'no'
                    ),
                    'title' => array(
                        'title'       => 'Title',
                        'type'        => 'text',
                        'description' => 'This controls the title which the user sees during checkout.',
                        'default'     => 'Credit Card',
                        'desc_tip'    => true,
                    ),
                    'description' => array(
                        'title'       => 'Description',
                        'type'        => 'textarea',
                        'description' => 'This controls the description which the user sees during checkout.',
                        'default'     => 'Pay with your credit card via our super-cool payment gateway.',
                    ),
                    'testmode' => array(
                        'title'       => 'Test mode',
                        'label'       => 'Enable Test Mode',
                        'type'        => 'checkbox',
                        'description' => 'Place the payment gateway in test mode using test API keys.',
                        'default'     => 'yes',
                        'desc_tip'    => true,
                    ),
                    'test_publishable_key' => array(
                        'title'       => 'Test Publishable Key',
                        'type'        => 'text'
                    ),
                    'test_private_key' => array(
                        'title'       => 'Test Private Key',
                        'type'        => 'password',
                    ),
                    'publishable_key' => array(
                        'title'       => 'Live Publishable Key',
                        'type'        => 'text'
                    ),
                    'private_key' => array(
                        'title'       => 'Live Private Key',
                        'type'        => 'password'
                    )
                );
            }
            function process_admin_options(){
 
                if ( empty( $_POST['your_field'] ) ) {
                    WC_Admin_Settings::add_error( 'Error: Please fill required fields' );
                    return false;
                }
             
            }
            
            public function payment_fields() {
            
            }
     
            /*
             * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
             */
             public function payment_scripts() {
                
                    // we need JavaScript to process a token only on cart/checkout pages, right?
                    if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
                        return;
                    }
                
                    // if our payment gateway is disabled, we do not have to enqueue JS too
                    if ( 'no' === $this->enabled ) {
                        return;
                    }
                
                    // no reason to enqueue JavaScript if API keys are not set
                    if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
                        return;
                    }
                
                    // do not work with card detailes without SSL unless your website is in a test mode
                    if ( ! $this->testmode && ! is_ssl() ) {
                        return;
                    }
                
                    // let's suppose it is our payment processor JavaScript that allows to obtain a token
                    wp_enqueue_script( 'collectjs', 'https://ick.transactiongateway.com/token/Collect.js' );
                
                    // and this is our custom JS in your plugin directory that works with token.js
                    wp_register_script( 'woocommerce_ick', plugins_url( 'ick.js', __FILE__ ), array( 'jquery', 'ick_js' ) );
                
                    // in most payment processors you have to use PUBLIC KEY to obtain a token
                    wp_localize_script( 'woocommerce_ick', 'ick_params', array(
                        'publishableKey' => '	6797Rr-G4XbF8-PtKYwR-6g6A4t'
                    ) );
                
                    wp_enqueue_script( 'woocommerce_ick' );
                            
     
             }
     
            /*
              * Fields validation
             */
            public function validate_fields() {
                if( empty( $_POST[ 'billing_first_name' ]) ) {
                    wc_add_notice(  'First name is required!', 'error' );
                    return false;
                }
                return true;
            
     
            }
     
            /*
             * We're processing the payments here
             */
            public function process_payment( $order_id ) {
     
                
	global $woocommerce;
 
	// we need it to get any order detailes
	$order = wc_get_order( $order_id );
 
 
	/*
 	 * Array with parameters for API interaction
	 */
	// $args = array(
 
		
 
	// );
 
	/*
	 * Your API interaction could be built with wp_remote_post()
 	 */
	 $response = wp_remote_post( '{payment processor endpoint}', $args );
 
 
	 if( !is_wp_error( $response ) ) {
 
		 $body = json_decode( $response['body'], true );
 
		 // it could be different depending on your payment processor
		 if ( $body['response']['responseCode'] == 'APPROVED' ) {
 
			// we received the payment
			$order->payment_complete();
			$order->reduce_order_stock();
 
			// some notes to customer (replace true with false to make it private)
			$order->add_order_note( 'Hey, your order is paid! Thank you!', true );
 
			// Empty cart
			$woocommerce->cart->empty_cart();
 
			// Redirect to the thank you page
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
			);
 
		 } else {
			wc_add_notice(  'Please try again.', 'error' );
			return;
		}
 
    
        function woocommerce_nmi_fallback_notice()
        {
            echo  '<div class="error"><p>' . sprintf( __( 'WooCommerce Custom Payment Gateways depends on the last version of %s to work!', 'nmi_three_step' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>' ;
        }
        
        /* Load functions. */
        function nmi_three_step_load()
        {
            
            if ( !class_exists( 'WC_Payment_Gateway' ) ) {
                add_action( 'admin_notices', 'woocommerce_nmi _fallback_notice' );
                return;
            }
            
            function wc_Custom_add_nmi_gateway( $methods )
            {
                $methods[] = 'NMI_Custom_Payment_Gateway';
                return $methods;
            }
            
            add_filter( 'woocommerce_payment_gateways', 'wc_Custom_add_nmi_gateway' );
            // Include the WooCommerce Custom Payment Gateways classes.
            require_once plugin_dir_path( __FILE__ ) . 'nmi_three_step_gateway_functions.php';
        }
        
        add_action( 'plugins_loaded', 'nmi_three_step_load', 0 );} else {
		wc_add_notice(  'Connection error.', 'error' );
		return;
	}
     
             }
     
            /*
             * Webhook
             */
            public function webhook() {
     
            
     
             }
    } // Class End
   }// fucntion end
}
?>