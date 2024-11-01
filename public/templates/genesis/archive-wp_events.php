<?php


add_filter( 'genesis_archive_crumb', 'wpe_custom_archive_crumb' );
if ( ! function_exists( 'wpe_custom_archive_crumb' ) ) {
	/**
     * set breadcrumb to post label
     *
	 * @param $crumb
	 *
	 * @return string
     * @since 1.0448
	 */
	function wpe_custom_archive_crumb( $crumb ) {
		//Set Breadcrumb by Post Label saved in settings
		$settings = get_option( 'wpe_settings' );
		if( isset( $settings['events_post_name'] ) ) {
		    return (string)$settings['events_post_name'];
        }

		return $crumb;
	}
}

add_filter( 'wpseo_breadcrumb_output', 'wpe_yoast_crumb' );
if ( ! function_exists( 'wpe_yoast_crumb' ) ) {
	/**
     * Replace breadcrumb string events with post label
     *
	 * This function's only purpose is to add breadcrumbs compatibility
	 * with Yoast SEO
     *
	 * @param $output
	 *
	 * @return string
     * @since 1.0.448
	 */
	function wpe_yoast_crumb( $output ) {
		$settings = get_option( 'wpe_settings' );
		if ( stripos( $output, 'events' ) ) {
			return str_ireplace( 'events', (string) $settings['events_post_name'], $output );
		}
		return $output;
	}
}

/**
 * Removing Default Genesis Loop
 *
 * @since 1.0.448
 */
remove_action( 'genesis_loop', 'genesis_do_loop' );

add_action( 'genesis_loop', 'wpe_custom_loop' );

if ( ! function_exists( 'wpe_custom_loop ' ) ) {
	/**
	 * Custom Loop overrides default genesis loop on archive page
	 *
	 * @since 1.0.449
	 */
	function wpe_custom_loop() {
		?>

        <div class="wpe-event">
			<div class="wpevents-container <?php echo wpe_dark_bg(); ?> <?php echo wpe_dark_mode(); ?>">
				<?php
				echo wpe_get_archive_page_title();

				$args  = wpe_get_default_query_args();
				$count = wpe_display_archive_posts( $args );

				if ( $count == 0 ) {
					/**
					 * Print the subscriber form if no event is added
                     * or all events are over due
					 *
					 * @since  1.0.448
					 * @action wpe_display_subscribe_form
					 */
					do_action( 'wp_events_subscribe_form' );          // Displays Subscribe Form
					$text = __( 'ThankYou For Subscribing.', 'simple-wp-events' );
					wpe_get_thankyou_popup( $text );
				}
				wp_reset_postdata();
				?>
            </div>
        </div>
		<?php
	}
}
genesis();