<?php


/* =============================================================================
 *
 * Function for specific theme, remember to keep all the functions
 * specified for this theme inside this file.
 *
 * ============================================================================*/

// Define theme specific constant
if (!defined('NOO_THEME_NAME'))
{
  define('NOO_THEME_NAME', 'noo-jobmonster');
}

if (!defined('NOO_THEME_VERSION'))
{
  define('NOO_THEME_VERSION', '0.0.1');
}
function noo_relative_time($a=''){
	return human_time_diff($a, current_time( 'timestamp' ));
}
function noo_excerpt_read_more( $more ) {
	return '';
}
add_filter( 'excerpt_more', 'noo_excerpt_read_more' );

function noo_content_read_more( $more ) {
	return '';
}

add_filter( 'the_content_more_link', 'noo_content_read_more' );


//// Include specific widgets
// require_once( $widget_path . '/<widgets_name>.php');
// Change file size upload for user non-adminstrator
// function noo_limit_upload_size_limit_for_non_admin( $limit ) {
//   if ( ! current_user_can( 'manage_options' ) ) {
//     $limit = 1000000; // 1mb in bytes
//   }
//   return $limit;
// }
 
// add_filter( 'upload_size_limit', 'noo_limit_upload_size_limit_for_non_admin' );
 
 
// function noo_apply_wp_handle_upload_prefilter( $file ) {
//   if ( ! current_user_can( 'manage_options' ) ) {
//     $limit = 1000000; // 1mb in bytes
//     if ( $file['size'] > $limit ) {
//       $file['error'] = __( 'Maximum filesize is 1mb', 'noo' );
//     }
//   }
//   return $file;
// }
 
// add_filter( 'wp_handle_upload_prefilter', 'noo_apply_wp_handle_upload_prefilter' );
// Change user title
// function fep_custom_name(){
//     $a = fep_get_participants( fep_get_the_id() );
    
//     $b = get_current_user_id();

//     $get = $b;
//     foreach ($a as $v) {
//         if($v != $b) $get = $v;
//     }
//     echo fep_user_name( $get );
// }
// add_action( 'fep_message_table_column_content_author', 'fep_custom_name');