<?php

/**
 * The locations related functionality of the plugin.
 *
 * @link       //wpminds.com
 * @since      1.3.0
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/admin
 */

/**
 * The locations related functionality of the plugin.
 *
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/admin
 * @author     WP Minds <support@wpminds.com>
 */

 // If this file is called directly, abort.
if ( !defined('WPINC') ) {
	die;
}

/**
 * Class that implements all the functionality related
 * to event locations.
 * 
 * @since 1.3.0
 */
class Wp_Events_Locations {

    /**
	 * Name of the custom post type
	 * 
	 * @since 1.3.0
	 * @var string $post_name
	 */
	protected $post_name = 'Locations';

    /**
	 * Slug of the custom post type
	 * 
	 * @since 1.3.0
	 * @var string $slug_name
	 */
	protected $post_slug = 'locations';


    /**
     * registering Locations post type
     *
     * @since 1.3.0
     */
    public function register_locations_post_type() {
	    $single_name = 'Location';
        $labels = array(
            'name'               => $this->post_name,
            'singular_name'      => $single_name,
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New '.$single_name,
            'edit_item'          => 'Edit '.$single_name,
            'new_item'           => 'New '.$single_name,
            'search_items'       => 'Search '.$single_name,
            'not_found'          => 'No '.$single_name.' found',
            'not_found_in_trash' => 'No '.$single_name.' found in Trash',
            'menu_name'          => $this->post_name,
        );

        $args = array(
            'labels'              => $labels,
            'supports'            => array( 'title', 'thumbnail' ),
            'show_in_rest'        => true,
            'show_in_menu'        => 'edit.php?post_type=wp_events',
            'exclude_from_search' => true,
            'can_export'          => true,
            'publicly_queryable'  => false,
            'public'              => false,
            'show_ui'             => true,
            'rewrite'             => array( 'slug' => $this->post_slug, 'with_front' => FALSE ),
            'menu_icon'           => 'dashicons-location-alt', // https://developer.wordpress.org/resource/dashicons/
        );

        register_post_type( $this->post_slug, $args );
    }

    /**
	 * registering custom metaboxes for storing location details.
	 *
	 * @since 1.3.0
	 */
	public function register_custom_metaboxes_for_locations() {

		/**
		 * Fields for locations related information
		 */
		add_meta_box( 'wpevent_locations', 'Location Details',
            array($this,'wpevent_locations_meta_box_callback'),
			$this->post_slug,
			'advanced',
            'high'
		);
	}

    /**
     * Meta box callback for wpevents_locations
     *
     * @param $post
     *
     * @since 1.3.0
     */
    public function wpevent_locations_meta_box_callback( $post ) {
        wp_nonce_field( 'wpevents_locations_nonce', 'wpevents_locations_nonce' );

        ?>
        <div class="wpevents-main">
        <div class="wp-events-location wp-event-subsection">
            <div class="venue event-control wpe-left">
                <label for="wpevent-loc-venue"><?php _e( 'Venue', 'simple-wp-events' ); ?></label>
                <input  id="wpevent-loc-venue" class="wp-event-field wpevent-location" type="text" name="wpevent-loc-venue" value="<?php echo get_post_meta( $post->ID, 'wpevent-loc-venue', true );?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="address event-control wpe-right">
                <label for="wpevent-loc-address"><?php _e( 'Address', 'simple-wp-events' ); ?></label>
                <input id="wpevent-loc-address" class="wp-event-field wpevent-location" type="text" name="wpevent-loc-address" value="<?php echo get_post_meta( $post->ID, 'wpevent-loc-address', true );?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="city event-control wpe-left">
                <label for="wpevent-loc-city"><?php _e( 'City', 'simple-wp-events' ); ?></label>
                <input id="wpevent-loc-city" class="wp-event-field wpevent-location" type="text" name="wpevent-loc-city" value="<?php echo get_post_meta( $post->ID, 'wpevent-loc-city', true );?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="state event-control wpe-right">
                <label for="wpevent-loc-state"><?php _e( 'State', 'simple-wp-events' ); ?></label>
                <input id="wpevent-loc-state" class="wp-event-field wpevent-location" type="text" name="wpevent-loc-state" value="<?php echo get_post_meta( $post->ID, 'wpevent-loc-state', true );?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="zip event-control wpe-left">
                <label for="wpevent-loc-zip"><?php _e( 'Zip', 'simple-wp-events' ); ?></label>
                <input id="wpevent-loc-zip" class="wp-event-field" type="text" name="wpevent-loc-zip" value="<?php echo get_post_meta( $post->ID, 'wpevent-loc-zip', true );?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="country event-control wpe-right">
                <label for="country"><?php _e( 'Country', 'simple-wp-events' ); ?></label>
                <?php wpevents_country_drop_down( get_post_meta( $post->ID, 'wpevent-loc-country', true ), 'wpevent-loc-country' );?>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
        </div>
        </div>
        <?php
    }

    /**
	 * saving metabox data in post meta
	 * hooked to save_post
	 *
	 * @param $post_id
	 *
	 * @since 1.3.0
	 */
    public function wpevents_save_locations_meta( $post_id ) {

	    // Return if we're doing an auto save
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		    return;
        }

        //nonce validation
        if ( ! wp_verify_nonce( filter_input(INPUT_POST, 'wpevents_locations_nonce'), 'wpevents_locations_nonce' ) ) {
            return;
        }

        //if post type is wp_events
        if( filter_input(INPUT_POST, 'post_type') !== $this->post_slug ) {
            return;
        }

        //location
        $location_address = filter_input( INPUT_POST, 'wpevent-loc-address', FILTER_SANITIZE_STRING );
        $location_city    = filter_input( INPUT_POST, 'wpevent-loc-city', FILTER_SANITIZE_STRING );
        $location_zip     = filter_input( INPUT_POST, 'wpevent-loc-zip', FILTER_SANITIZE_STRING );
        $location_state   = filter_input( INPUT_POST, 'wpevent-loc-state', FILTER_SANITIZE_STRING );
        $location_venue   = filter_input( INPUT_POST, 'wpevent-loc-venue', FILTER_SANITIZE_STRING );
        $location_country = filter_input( INPUT_POST, 'wpevent-loc-country', FILTER_SANITIZE_STRING );

        update_post_meta( $post_id, "wpevent-loc-address", $location_address );
        update_post_meta( $post_id, "wpevent-loc-city", $location_city );
        update_post_meta( $post_id, "wpevent-loc-zip", $location_zip );
        update_post_meta( $post_id, "wpevent-loc-state", $location_state );
        update_post_meta( $post_id, "wpevent-loc-venue", $location_venue );
        update_post_meta( $post_id, "wpevent-loc-country", $location_country );

    }

}