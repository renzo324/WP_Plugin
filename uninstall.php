<?php
/**
 * uninstall trigger
 * @package           Dl_Table
 */

 if(! defined ('WP_UNINSTALL_PLUGIN')){
     die;
 }

//  clear database of data stored from the plugin
// $downloadtable = get_posts( array('post_type' => 'downloadable', 'numberposts' => -1) );

// foreach ($downloadtable as $downloadable){
//     wp_delete_post($downloadable->ID, true);
// }

// sql based manipulation
// global $wpdb;
// $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'downloadable'");
// $wpdb->query("DELETE FROM wp_posts_meta WHERE post_id NOT IN (SELECT id FROM wp_posts)");