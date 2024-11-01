<?php

/**
 * Fired during plugin deactivation
 *
 * @link       //wpminds.com
 * @since      1.0.0
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_Events
 * @subpackage Wp_Events/includes
 * @author     WP Minds <support@wpminds.com>
 */
class Wp_Events_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option('rewrite_rules');
		wp_reset_postdata();
	}

}
