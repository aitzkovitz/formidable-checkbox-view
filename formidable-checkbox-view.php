<?php
/*
Plugin Name: Formidable Checkbox View
Description: Plugin extending formidable forms.
Author: Aaron Itzkovitz
Version: 1.0.0
Author URI: http://aaronitzkovitz.com/
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Register the JavaScript for the 'Accountability' page.
 *
 * @since    1.0.0
 */
function fcv_enqueue_scripts(){
	
	global $post;
	if ( 16768 != (int) $post->ID ) { return; }
	wp_enqueue_script( 
		'fcw-checkbox-ajax',
		plugin_dir_url( __FILE__ ) . 'js/fcw.js',
		array( 'jquery' ),
		1.0,
		true
	);
  	wp_localize_script( 'fcw-checkbox-ajax' , 'scriptObject', array(
		'ajaxurl'               => admin_url( 'admin-ajax.php' ),
		'security'              => wp_create_nonce( 'fcv-update-entry' )
	));
}
add_action( 'wp_enqueue_scripts', 'fcv_enqueue_scripts' );

/**
 * Register the AJAX hook to update the database with new 'Invoice' data.
 *
 * @since    1.0.0
 */
function update_entry_data(){
	// check ajax
	$pass = check_ajax_referer( 'fcv-update-entry', 'security', false);
	if (!$pass) { wp_die( 'security failed' ); }

	$id_entry_array = $_POST[ 'data' ][ 'entry_id_array' ];
	$id_entry_array = array_map( function( $value ) { return (int)$value; }, $id_entry_array );
	$sql_array = implode( "','", $id_entry_array );
	global $wpdb;

	$query = $wpdb->query( 
		$wpdb->prepare( 
			"UPDATE wp_frm_item_metas 
			SET meta_value = %s
			WHERE item_id IN ('$sql_array')
			AND field_id = 217",
			'Invoiced'
		)
	);

	if ( !$query ) {
		wp_send_json_error( array(
			'status' 	=> 'Query failed'
		));
	} else {
		wp_send_json_success( array(
			'status' 	=> 'Query succeeded',
			'to_delete'	=> $id_entry_array
		));
	}
}

// priv version
add_action( 'wp_ajax_update_entry', 'update_entry_data' );