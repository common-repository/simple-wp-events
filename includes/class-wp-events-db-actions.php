<?php

/**
 * File that contains functions related to database operations
 *
 * @link       //wpminds.com
 * @since      1.1.1
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/includes
 */

/**
 * File that contains functions related to database operations
 *
 * @since      1.1.1
 * @package    Wp_Events
 * @subpackage Wp_Events/includes
 * @author     WP Minds <support@wpminds.com>
 */
class Wp_Events_Db_Actions {

	/**
	 * Adding Subscriber Table
	 *
	 * @since 1.1.1
	*/
	public static function add_subscriber_table() {

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'events_subscribers';

		$sql = "CREATE TABLE IF NOT EXISTS " .$table_name. "(
                    id BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                    subscriber_firstname VARCHAR(60) NOT NULL,
                    subscriber_lastname VARCHAR(60) NOT NULL,
                    subscriber_email VARCHAR(80) NOT NULL,
                    time_generated DATETIME NOT NULL,
					wpe_status INT(1) NOT NULL DEFAULT 1,
					subscriber_phone VARCHAR(80) NOT NULL,
					subscriber_texting_permission INT(1) NOT NULL DEFAULT 0
                )" .$charset_collate. ";";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta($sql);

	}

	/**
	 * Adding Registration Table
	 *
	 * @since 1.1.1
	*/
	public static function add_registration_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'events_registration';
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name. " (
                    ID BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                    post_id BIGINT(20) UNSIGNED,
                    first_name VARCHAR(80) NOT NULL,
                    last_name VARCHAR(80) NOT NULL,
                    addres_one VARCHAR(240) NOT NULL,
                    addres_two VARCHAR(240),
                    city VARCHAR(80) NOT NULL,
                    state VARCHAR(80) NOT NULL,
                    zip VARCHAR(80) NOT NULL,
                    phone VARCHAR(80) NOT NULL,
                    email VARCHAR(320) NOT NULL,
                    fax VARCHAR(80),
                    business_name VARCHAR(80),
                    hear_about_us VARCHAR(80) NOT NULL,
                    time_generated DATETIME NOT NULL,
					wpe_seats VARCHAR(80) NOT NULL,
					guests VARCHAR(255),
					wpe_status INT(1) NOT NULL DEFAULT 1,
					texting_permission INT(1) NOT NULL DEFAULT 0
                )" .$charset_collate. ";";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}

	/**
	 * Checks if a table exists in the database
	 *
	 * @param string $table
	 *
	 * @since 1.1.1
	 * @return bool
	 */
	public static function wpe_table_exists( $table ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name ) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves data for single registration from database
	 * 
	 * @since 1.2.0
	 * @return array
	 */
	public static function wpe_get_registration_data( $entry_id = null, $status = null, $format = 'OBJECT' ) {
		global $wpdb;
		$table_name	   = 'events_registration';
		if ( isset( $entry_id ) && $entry_id !== '' && isset( $status ) && $status !== '' ) {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name WHERE ID = %s AND wpe_status in ( %s )", [ $entry_id, $status ] );
		} else if ( isset( $entry_id ) && $entry_id !== '' ) {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name WHERE ID = %s", $entry_id );
		} else if ( isset( $status ) && $status !== '' ) {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name WHERE wpe_status in ( %s )", $status );
		} else {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name" );
		}
		$results = $wpdb->get_results( $sql, $format );
		return $results;
	}

	/**
	 * Retrieves data for single subscription from database
	 * 
	 * @since 1.2.0
	 * @return array
	 */
	public static function wpe_get_subscription_data( $entry_id = null, $status = null ) {
		global $wpdb;
		$table_name	   = 'events_subscribers';
		if ( isset( $entry_id ) && $entry_id !== '' && isset( $status ) && $status !== '' ) {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name WHERE ID = %s AND wpe_status in ( %s )", [ $entry_id, $status ] );
		} else if ( isset( $entry_id ) && $entry_id !== '' ) {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name WHERE ID = %s", $entry_id );
		} else if ( isset( $status ) && $status !== '' ) {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name WHERE wpe_status in ( %s )", $status );
		} else {
			$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}$table_name" );
		}
		$results = $wpdb->get_results( $sql, OBJECT );
		return $results;
	}

	/**
	 * Retrieves ID of event corresponding to the entry
	 * 
	 * @since 1.2.0
	 * @return array
	 */
	public static function wpe_get_event_id( $entry_id ) {
		global $wpdb;
		$table_name      = 'events_registration';
		$sql             = "SELECT post_id FROM {$wpdb->prefix}$table_name WHERE ID = %d";
		$event_id        = $wpdb->get_var( $wpdb->prepare( $sql, $entry_id ) );
		return $event_id;
	}

	/**
	 * Updates record in database when an entry is edited
	 * 
	 * @since 1.2.0
	 * @return array
	 */
	public static function wpe_update_entry() {

		$current_user		   = wp_get_current_user();
		$user				   = $current_user->display_name;
		$_REQUEST['edited-by'] = $user;
		/**
		 * wpe_request_log global function created in includes/wp-events-global-functions.php
		 */
		wpe_request_log( $_REQUEST );

		$form_data = wpe_sanitize( $_POST['formData'] );

		/**
		 * wpe_decode_array global function created in includes/wp-events-global-functions.php
		*/
		$form_data = wpe_decode_array( $form_data );
		$referrer  = $form_data['_wp_http_referer'];
		$text_perm = wpe_sanitize( $_POST['permissions'] );

		global $wpdb;

		// If Nonce Is Not Verified Then Return
		if ( ! wp_verify_nonce( $form_data['wpe_entry_form_nonce'], 'wp_event_entry_form' ) ) {
			$response = '0000';
			wpe_send_ajax_response( $response );
		}

		if ( strpos( $referrer, 'registrations' ) !== false ) {
			$table_name	  = 'events_registration';
			$id			  = 'ID';
			$updated_data = array(
				'first_name'	 	 => isset( $form_data['wpe_first_name'] ) ? sanitize_text_field( $form_data['wpe_first_name'] ) : '',
				'last_name'		 	 => isset( $form_data['wpe_last_name'] ) ? sanitize_text_field( $form_data['wpe_last_name'] ) : '',
				'addres_one'	 	 => isset( $form_data['wpe_address'] ) ? sanitize_text_field( $form_data['wpe_address'] ) : '',
				'addres_two'	 	 => isset( $form_data['wpe_address_2'] ) ? sanitize_text_field( $form_data['wpe_address_2'] ) : '',
				'city'			 	 => isset( $form_data['wpe_city'] ) ? sanitize_text_field( $form_data['wpe_city'] ) : '',
				'state'			 	 => isset( $form_data['wpe_state'] ) ? sanitize_text_field( $form_data['wpe_state'] ) : '',
				'zip'			 	 => isset( $form_data['wpe_zip'] ) ? sanitize_text_field( $form_data['wpe_zip'] ) : '',
				'phone'			 	 => isset( $form_data['wpe_phone'] ) ? sanitize_text_field( $form_data['wpe_phone'] ) : '',
				'email'			 	 => isset( $form_data['wpe_email'] ) ? sanitize_text_field( $form_data['wpe_email'] ) : '',
				'fax'			 	 => isset( $form_data['wpe_fax'] ) ? sanitize_text_field( $form_data['wpe_fax'] ) : '',
				'business_name'	 	 => isset( $form_data['wpe_business_name'] ) ? sanitize_text_field( $form_data['wpe_business_name'] ) : '',
				'hear_about_us'	 	 => isset( $form_data['hear_about_us'] ) ? sanitize_text_field( $form_data['hear_about_us'] ) : '',
				'wpe_seats'		 	 => isset( $form_data['wpe_seats'] ) ? sanitize_text_field( $form_data['wpe_seats'] ) : '',
				'guests'		 	 => isset( $form_data['guests'] ) ? sanitize_text_field( $form_data['guests'] ) : '',
				'texting_permission' => $text_perm === 'true' ? 1 : 0,
			);
			// Data Format
			$format = [
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			];
		} else {
			$table_name	  = 'events_subscribers';
			$id			  = 'id';
			$updated_data = array(
				'subscriber_firstname' 			   => isset( $form_data['wpe_first_name'] ) ? sanitize_text_field( $form_data['wpe_first_name'] ) : '',
				'subscriber_lastname'  			   => isset( $form_data['wpe_last_name'] ) ? sanitize_text_field( $form_data['wpe_last_name'] ) : '',
				'subscriber_email'	   			   => isset( $form_data['wpe_email'] ) ? sanitize_text_field( $form_data['wpe_email'] ) : '',
				'subscriber_phone'	   			   => isset( $form_data['wpe_phone'] ) ? sanitize_text_field( $form_data['wpe_phone'] ) : '',
				'subscriber_texting_permission'	   => $text_perm === 'true' ? 1 : 0,
			);
			// Data Format
			$format = [
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			];
		}

		$entry_id = isset( $form_data['entry'] ) ? $form_data['entry'] : '';
		$result = $wpdb->update(
			"{$wpdb->prefix}$table_name",
			$updated_data,
			[ $id => $entry_id ],
			$format,
			'%d'
		);
		
		$response = 'Record Updated Successfully!';

		wpe_send_ajax_response( $response );
	}
}