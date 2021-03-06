<?php

/**
 *
 * @link              https://github.com/renzo324/
 * @since             1.0.1
 * @package           DM-Tools
 *
 * @wordpress-plugin
 * Plugin Name:       DM-Tools
 * Plugin URI:        https://github.com/renzo324/WP_Plugin
 * Description:       Dungeon Master's tools via WordPress
 * Version:           1.2.1
 * Author:            Lorenzo Ibay
 * Author URI:        https://github.com/renzo324/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       DM-Tools
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
define('DM_TOOLS_VERSION', '1.2.1');
if (!class_exists('DM_Tools_WP')) {
    class DM_Tools_WP
    {
        public $title;
        function __construct()
        {
            $this->title = plugin_basename(__FILE__);
            add_action('wp_enqueue_scripts', array(
                $this,
                'enqueue'
            ));
            add_shortcode('charnotes', array(
                $this,
                'character_sheet_access'
			));

        }
        function register()
        {
            //on activation enqueue scripts
            add_action('admin_enqueue_scripts', array(
                $this,
                'enqueue'
            ));
            
        }
       
        function enqueue()
        {
            // get scripts
            wp_register_style('pluginstyle', plugins_url('/assets/DM-Tools.css', __FILE__));
            wp_enqueue_style('pluginstyle', plugins_url('/assets/DM-Tools.css', __FILE__));
            wp_register_script('pluginscript', plugins_url('/assets/DM-Tools.js', __FILE__));
            wp_enqueue_script('pluginscript', plugins_url('/assets/DM-Tools.js', __FILE__));
            wp_enqueue_style('bootstrap4', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
            wp_enqueue_script('boot2', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array(
                'jquery'
            ), '', true);
            wp_enqueue_script('boot3', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array(
                'jquery'
            ), '', true);
        }
       
        public function player_access_field()
        {
			$IsAdmin = is_super_admin(get_current_user_id());    
			if($IsAdmin){
				// WP_Query arguments
				$args = array(
					'post_type' => array(
						'dm_notes'
					),
					'post_status' => array(
						'publish'
					),
					'nopaging' => true,
					'order' => 'ASC',
					'orderby' => 'menu_order'
				);
				
				//New Query
				$loop = new WP_Query($args);
				if ($loop->have_posts()) {
					echo '<h1>DM Notes Table</h1>';
					echo '<div class="divTable dm-notes-table">
						<div class="divTableBody">
						<form action="#" method="post"> 
							<div class="divTableRow dm-notes-row dm-notes-head">
								<div class="divTableCell dm-notes-col-1">&nbsp;File</div>
								<div class="divTableCell dm-notes-col-2">&nbsp;Access</div>
							</div>';
					while ($loop->have_posts()) {
						$loop->the_post();
						if(in_array(get_the_ID(), get_user_meta(get_current_user_id(), 'grant_access')[0])){
							echo '<div class="divTableRow dm-notes-row table-success">
							<div class="divTableCell dm-notes-col-1">&nbsp;' . get_the_title() . '</div>
							<div class="divTableCell dm-notes-col-2 ">&nbsp;    <input type="checkbox" name="grant_access[]" checked="checked" value="' . get_the_ID() . '"></div>
							</div>';
						}else{
							echo '<div class="divTableRow dm-notes-row">
							<div class="divTableCell dm-notes-col-1">&nbsp;' . get_the_title() . '</div>
							<div class="divTableCell dm-notes-col-2">&nbsp;    <input type="checkbox" name="grant_access[]" value="' . get_the_ID() . '"></div>
							</div>';
						}
					} // end while
					
					
				} // end if
				else {
					echo 'No posts available';
				}
				
				
			};
            
		}
		
		function save_player_access_field( $user_id ) {

			if ( !current_user_can( 'edit_user', $user_id ) )
				return false;
				if (isset($_POST['submit'])) {
					if (!empty($_POST['grant_access'])) {
						$granted= [];
						
						if (count($_POST['grant_access']) > 1) {
							foreach ($_POST['grant_access'] as $selected) {
								array_push($granted, $selected);
							}
						} else {
							foreach ($_POST['grant_access'] as $selected) {
								array_push($granted, $selected);
							}
						}
						
					} else {
						
					}
				}
			/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
			update_user_meta( $user_id, 'grant_access', $granted );
		}
        //things to run on activation hook
        function activate()
        {
            flush_rewrite_rules();
        }
        
        //User Facing functionality
        public function character_sheet_access($atts)
        {
            
            if(empty($atts)){
				$args = array(
					'post_type' => array(
						'pcs'
					),
					'post_status' => array(
						'publish'
					),
					'nopaging' => true,
					'order' => 'ASC',
					'orderby' => 'menu_order',
					
				);
				
				//New Query
				$loop = new WP_Query($args);
				if ($loop->have_posts()) {
					
					
					while ($loop->have_posts()) {
						$loop->the_post();
						

					} // end while

				} // end if
				else {
					return 'No posts available';
				}
			}
			else{}
            
        }
    }
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here
}

add_action('plugins_loaded', 'wc_offline_gateway_init', 11);
    $DM_Tools_WP = new DM_Tools_WP();
    $DM_Tools_WP->register();
    //activation
    register_activation_hook(__FILE__, array(
        $DM_Tools_WP,
        'activate'
    ));
}
