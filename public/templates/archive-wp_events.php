<?php
/**
 * wp_events archive page
 *
 * @since 1.0.2
 */
get_header();

?>

<div class="wpe-event">
    <div class="wpe-full-wrap <?php echo wpe_dark_bg(); ?> <?php echo wpe_dark_mode(); ?>">
        <div class="wpevents-container">
            <?php
            echo wpe_get_archive_page_title();

            $args  = wpe_get_default_query_args();
            $count = wpe_display_archive_posts( $args );

            if ( $count == 0 ) {                
                /**
                 * Print the subscriber form if no event is added
                 * or all events are over due
                 *
                 * @since  1.0.449
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
</div>
<?php
get_footer();
?>