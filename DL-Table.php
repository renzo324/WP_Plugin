<?php

/**
 *
 * @link              https://github.com/renzo324/
 * @since             1.0.0
 * @package           DM-Tools
 *
 * @wordpress-plugin
 * Plugin Name:       DM-Tools
 * Plugin URI:        https://github.com/renzo324/WP_Plugin
 * Description:       Dungeon Master's tools via WordPress
 * Version:           1.0.0
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
define('DM_TOOLS_VERSION', '1.0.1');
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
            add_shortcode('charsheet', array(
                $this,
                'character_sheet_access'
			));
			// add_filter('grant_access', array(
			// 	$this,
			// 	'grant_access_method'
			// ));
            add_action('personal_options', array(
                $this,
                'extra_user_profile_field'
            ));
            add_action('personal_options', array(
                $this,
                'extra_user_profile_field'
			));
			add_action( 'personal_options_update', array(
				$this,
				'save_extra_user_profile_field'
				
			));
			add_action( 'edit_user_profile_update',array(
				$this,
				'save_extra_user_profile_field'
				
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
            wp_register_style('pluginstyle', plugins_url('/assets/DL-Table.css', __FILE__));
            wp_enqueue_style('pluginstyle', plugins_url('/assets/DL-Table.css', __FILE__));
            wp_register_script('pluginscript', plugins_url('/assets/DL-Table.js', __FILE__));
            wp_enqueue_script('pluginscript', plugins_url('/assets/DL-Table.js', __FILE__));
            wp_enqueue_style('bootstrap4', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
            wp_enqueue_script('boot1', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(
                'jquery'
            ), '', true);
            wp_enqueue_script('boot2', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array(
                'jquery'
            ), '', true);
            wp_enqueue_script('boot3', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array(
                'jquery'
            ), '', true);
        }
       
        public function extra_user_profile_field()
        {
			$IsAdmin = is_author(get_current_user_id());    
			if($IsAdmin){
				// WP_Query arguments
				$args = array(
					'post_type' => array(
						'downloads'
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
					echo '<h1>User Downloads</h1>';
					echo '<div class="divTable downloads-table">
						<div class="divTableBody">
						<form action="#" method="post"> 
							<div class="divTableRow downloads-row downloads-head">
								<div class="divTableCell downloads-col-1">&nbsp;File</div>
								<div class="divTableCell downloads-col-2">&nbsp;Access</div>
							</div>';
					while ($loop->have_posts()) {
						$loop->the_post();
						$url= get_field("download")['url'];
						if(in_array(get_the_ID(), get_user_meta(get_current_user_id(), 'grant_access')[0])){
							echo '<div class="divTableRow downloads-row">
							<div class="divTableCell downloads-col-1">&nbsp;' . get_the_title() . '</div>
							<div class="divTableCell downloads-col-2 table-success">&nbsp;    <input type="checkbox" name="grant_access[]" checked="checked" value="' . get_the_ID() . '"></div>
							</div>';
						}else{
							echo '<div class="divTableRow downloads-row">
							<div class="divTableCell downloads-col-1">&nbsp;' . get_the_title() . '</div>
							<div class="divTableCell downloads-col-2">&nbsp;    <input type="checkbox" name="grant_access[]" value="' . get_the_ID() . '"></div>
							</div>';
						}
					} // end while
					
					
				} // end if
				else {
					echo 'No posts available';
				}
				
				
			};
            
		}
		
		function save_extra_user_profile_field( $user_id ) {

			if ( !current_user_can( 'edit_user', $user_id ) )
				return false;
				if (isset($_POST['submit'])) {
					if (!empty($_POST['grant_access'])) {
						$granted= [];
						// Loop to store and display values of individual checked checkbox.
						// echo '[charsheet grant="';
						if (count($_POST['grant_access']) > 1) {
							foreach ($_POST['grant_access'] as $selected) {
								array_push($granted, $selected);
							}
						} else {
							foreach ($_POST['grant_access'] as $selected) {
								array_push($granted, $selected);
							}
						}
						// echo '"]';
					} else {
						// echo "<b>Please Select Atleast One Option.</b>";
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
						'downloads'
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
					$table ='<div class="divTable downloads-table">';
					$table .='<div class="divTableBody">';
					$table .='	<div class="divTableRow downloads-row downloads-head">';
					$table .='		<div class="divTableCell downloads-col-1">&nbsp;Download</div>';
					$table .='		<div class="divTableCell downloads-col-2">&nbsp;View</div>';
					$table .='		<div class="divTableCell downloads-col-3">&nbsp;Save</div>';
					$table .='	</div>';
					// $downloadables= '';
					// echo count($grant_arr);
					// echo print_r($atts);
					while ($loop->have_posts()) {
						$loop->the_post();
						$view= get_field("download")['url'];
						$save= get_field("download")['link'];
						if(in_array(get_the_ID(), get_user_meta(get_current_user_id(), 'grant_access')[0])){
						$table.= '<div class="divTableRow downloads-row">
						<div class="divTableCell downloads-col-1">&nbsp;' . get_the_title() . '</div>
						<div class="divTableCell downloads-col-2">&nbsp;<a href="#" data-toggle="modal" data-target="#' . get_the_ID() . '">View</a></div>
						<div class="divTableCell downloads-col-2">&nbsp;<a href="' . $view . '" target="_blank">Save</a></div>
						</div>
						<!-- Modal -->
							<div class="modal fade" id="' . get_the_ID() . '" tabindex="-1" role="dialog" aria-labelledby="' . get_the_title() . '" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<iframe src="' . $view . '" style="height:800px;width:300px;"></iframe>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
								</div>
							</div>
						</div>';}
					} // end while

					$table.='</div></div>';
					// echo $tableStart.$downloadables.$tableEnd;
					return $table;
				} // end if
				else {
					return 'No posts available';
				}
			}
			else{
				// Attributes
            
				extract(shortcode_atts(array(
					'grant' => array()
				), $atts));
				
				$grant_arr = explode(',', $grant);
				// WP_Query arguments
				$args = array(
					'post_type' => array(
						'downloads'
					),
					'post_status' => array(
						'publish'
					),
					'nopaging' => true,
					'order' => 'ASC',
					'orderby' => 'menu_order',
					'post__in' => $grant_arr
				);
				
				//New Query
				$loop = new WP_Query($args);
				if ($loop->have_posts()) {
					$table ='<div class="divTable downloads-table">';
					$table .='<div class="divTableBody">';
					$table .='	<div class="divTableRow downloads-row downloads-head">';
					$table .='		<div class="divTableCell downloads-col-1">&nbsp;Download</div>';
					$table .='		<div class="divTableCell downloads-col-2">&nbsp;View</div>';
					$table .='		<div class="divTableCell downloads-col-3">&nbsp;Save</div>';
					$table .='	</div>';
					// $downloadables= '';
					// echo count($grant_arr);
					// echo print_r($atts);
					while ($loop->have_posts()) {
						$loop->the_post();
						$view= get_field("download")['url'];
						$save= get_field("download")['link'];

						$table.= '<div class="divTableRow downloads-row">
						<div class="divTableCell downloads-col-1">&nbsp;' . get_the_title() . '</div>
						<div class="divTableCell downloads-col-2">&nbsp;<a href="#" data-toggle="modal" data-target="#' . get_the_ID() . '">View</a></div>
						<div class="divTableCell downloads-col-2">&nbsp;<a href="' . $view . '" target="_blank">Save</a></div>
						</div>
						<!-- Modal -->
							<div class="modal fade" id="' . get_the_ID() . '" tabindex="-1" role="dialog" aria-labelledby="' . get_the_title() . '" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<iframe src="' . $view . '" style="height:800px;width:300px;"></iframe>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
								</div>
							</div>
						</div>';
					} // end while

					$table.='</div></div>';
					// echo $tableStart.$downloadables.$tableEnd;
					return $table;
				} // end if
				else {
					return 'No posts available';
				}
			}
            
        }
    }
    $DM_Tools_WP = new DM_Tools_WP();
    $DM_Tools_WP->register();
    //activation
    register_activation_hook(__FILE__, array(
        $DM_Tools_WP,
        'activate'
    ));
}