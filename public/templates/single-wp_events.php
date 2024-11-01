<?php
/**
 * wp_events Single page
 *
 * @since 1.0.446
 */

/**
 * Redirects to external source if available
 * only admin can see single page
*/
wpe_redirect_to_external_url( get_the_ID() );
get_header();
?>

    <div class="wpe-event">
        <div class="wpe-full-wrap <?php echo wpe_dark_bg(); ?> <?php echo wpe_dark_mode(); ?>">
            <div class="wpevents-container">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) : the_post();
						$post_id         = get_the_ID();                                                        // the event ID
						$event_date_time = wpevent_date_time( $post_id );
						$start_date      = isset( $event_date_time['start_date'] ) ? strtotime( $event_date_time['start_date'] ) : 0;
						$start_time      = isset( $event_date_time['start_time'] ) ? strtotime( $event_date_time['start_time'] ) : 0;
						$end_date        = isset( $event_date_time['end_date'] ) ? strtotime( $event_date_time['end_date'] ) : 0;
						$end_time        = isset( $event_date_time['end_time'] ) ? strtotime( $event_date_time['end_time'] ) : 0;
						$end_date_time   = get_post_meta( $post_id, 'wpevent-end-date-time', TRUE );
						$wpe_location 	 = (int) get_post_meta( $post_id, 'wpevent-location', TRUE );
						$location_id 	 = $wpe_location != 0 ? $wpe_location : $post_id;
						$venue_meta 	 = $wpe_location != 0 ? 'wpevent-loc-venue' : 'wpevent-venue';
						$address_meta 	 = $wpe_location != 0 ? 'wpevent-loc-address' : 'wpevent-address';
						$city_meta  	 = $wpe_location != 0 ? 'wpevent-loc-city' : 'wpevent-city';
						$state_meta 	 = $wpe_location != 0 ? 'wpevent-loc-state' : 'wpevent-state';
						$country_meta 	 = $wpe_location != 0 ? 'wpevent-loc-country' : 'wpevent-country';
						$wpe_venue       = get_post_meta( $location_id, $venue_meta, TRUE ) ?? '';
						$wpe_addr        = get_post_meta( $location_id, $address_meta, TRUE ) ?? '';
						$wpe_city        = get_post_meta( $location_id, $city_meta, TRUE ) ?? '';
						$wpe_state       = get_post_meta( $location_id, $state_meta, TRUE ) ?? '';
						$wpe_country     = get_post_meta( $location_id, $country_meta, TRUE ) ?? '';
						$seats           = get_post_meta( $post_id, 'wpevent-seats', TRUE );
						$wpe_all_day 	 = get_post_meta( $post_id, 'wpevent-all-day', TRUE );
						$wpe_no_endtime  = get_post_meta( $post_id, 'wpevent-no-endtime', TRUE );
						if( $wpe_no_endtime ) {
							$end_time = strtotime('23:59');
						}
						$booked_seats 	 = get_booked_seats( $post_id ); //Function defined in wp-events-global-functions.php
						$gmap_url        = get_post_meta( $post_id, 'wpevent-map-url', TRUE );                   // google map URL
						$post_type       = 'wp_events';
						$terms           = wp_get_object_terms( $post_id, 'wpevents-category' );
						$wpe_type        = get_post_meta( $post_id, 'wpevent-type', TRUE );
						$wpe_type 		 = str_replace( '-', ' ', $wpe_type );
						$wpe_phone       = get_post_meta( $post_id, 'wpevent-phone', true );
						$tzString 		 = empty( wpe_get_admin_timezone() ) ? 'America/New_York' : wpe_get_admin_timezone();
						$tz 			 = new \DateTimeZone($tzString);
						$admin_offset 	 = ( $tz->getOffset(new \DateTime()))/3600;
						$tzString 		 = empty( wpe_get_user_timezone() ) ? 'America/New_York' : wpe_get_user_timezone();
						$tz 			 = new \DateTimeZone($tzString);
						$user_offset 	 = ( $tz->getOffset(new \DateTime()))/3600;
						$total_offset 	 = $user_offset - $admin_offset;
						$set_timezone 	 = new DateTimeZone( wpe_get_user_timezone() );
						$start_tz_date   = new DateTime( date('Y-m-d H:i:s', strtotime('+'. $total_offset .' hours', get_post_meta( $post_id , 'wpevent-start-date-time', TRUE ) ) ), $set_timezone );
						$end_tz_date   	 = new DateTime( date('Y-m-d H:i:s', strtotime('+'. $total_offset .' hours', get_post_meta( $post_id , 'wpevent-end-date-time', TRUE ) ) ), $set_timezone );
						$image 			 = get_the_post_thumbnail();
						$full_content 	 = ! empty( $image ) ? '' : 'wpe-full-content';
						?>
                        <?php
                        echo apply_filters( 'wpe_single_title', '<h1 class="wpe-single-title">'. get_the_title() .'</h1>' );
                        ?>
						<div class="wpe-single-top">
							<?php if( ! empty( $image ) ) { ?>
							<div class="wpe-event-thumbnail">
								<?php
								echo $image; // Event Featured Image
								?>
							</div>
							<?php } ?>
							<div class="wpe-single-content <?php echo $full_content; ?>">
								<span class="wpe-category"><?php
									echo apply_filters( 'wpe_single_type', '<span class="wpe-terms"><strong>Type:&nbsp;</strong>' . esc_html( $wpe_type ) . '</span>' );
									if( !empty( $terms ) ) {
										$cat_html  = '';
										foreach ( $terms as $term ) {
											$cat_html .= '<a href="' . get_term_link( $term->term_id ) . '">' . $term->name . '</a>,&nbsp;';
										}
										if( $cat_html !== '' ) {
											echo apply_filters( 'wpe_single_category', '<span class="wpe-type"><strong>Category:&nbsp;</strong>' . rtrim( wp_kses_post( $cat_html ), ',&nbsp;' ) . '</span>' );
										}
									}
									?>
								</span>
								<span class="wpe-complete-duration">
									<strong>Date: </strong><?php
									if ( $start_date === $end_date ) {
										echo date( 'F j', $start_date );
									} else {
										echo date( 'F j', $start_date ) . ' - ' . date( 'F j', $end_date );
									} ?>
								</span>
								<span class="wpe-duration-date">
									<strong>Time: </strong>
									<?php
									echo wpe_get_event_time( $post_id );
									?>
								</span>
								<?php if( $wpe_phone !== '' ) {?>
								<span class="wpe-duration-date">
									<strong>Phone: </strong><?php
									echo "<a href='tel:". esc_attr( $wpe_phone ) ."'>" . esc_html( $wpe_phone ) . "</a>"; ?>
								</span>
								<?php  
								} 
								if( $wpe_type != 'webinar' ) {
									?>
									<span class="wpe-address">
										<?php echo '<strong>Venue:</strong> ' . wpe_get_event_address( $post_id ); ?>
									</span>
									<?php 
								}
								echo wpe_display_external_url_to_admin( $post_id );?>
							</div>
						</div>
                        <div class="wpe-add-to-calendar">
							<?php
							if ( $gmap_url !== '' ) {
								echo '<a class="wpe-button gmap-button" target="_blank" href="' . esc_url( $gmap_url ) . '">Google Map</a>';
							}
							//Replacing Spaces with + symbol to add in Query String
							$e_title         = preg_replace( '/\s+/', '+', get_the_title() );
							$s_date 		 = new DateTime(date( 'Ymd', $start_date ) . 'T' . date( 'His', $start_time ));
							$e_date 		 = new DateTime(date( 'Ymd', $end_date ) . 'T' . date( 'His', $end_time ));
							if( $wpe_all_day ) {
								$c_date = $s_date->format('Ymd') . '/' . $e_date->format('Ymd');
							} else {
								$c_date = $s_date->format('Ymd') . 'T' . $s_date->format('His') . '/' . $e_date->format('Ymd') . 'T' . $e_date->format('His');
							}
							$e_description   = preg_replace( '/\s+/', '+', wp_trim_words( get_the_excerpt(), 10, ' ' ) );
							$e_address       = preg_replace( '/\s+/', '+', $wpe_venue . '+' . $wpe_addr . '+' . $wpe_city . '+' . $wpe_country );
							$add_to_calendar = 'https://www.google.com/calendar/event?action=TEMPLATE&amp;text=' . $e_title . '&amp;dates=' . $c_date . '&amp;details=' . $e_description . '&amp;location=' . $e_address . '&amp;trp=false&amp;' . 'sprop=website:' . get_site_url() . '&amp;ctz=' . wpe_get_admin_timezone();
							strpos( (string)$total_offset, '-' ) !== false ? $s_date->add(new DateInterval("PT" . absint( $total_offset ) . "H")) : $s_date->sub(new DateInterval("PT" . absint( $total_offset ) . "H"));
							strpos( (string)$total_offset, '-' ) !== false ? $e_date->add(new DateInterval("PT" . absint( $total_offset ) . "H")) : $e_date->sub(new DateInterval("PT" . absint( $total_offset ) . "H"));
							if( $wpe_all_day ) {
								$add_to_outlook  = 'https://outlook.live.com/calendar/0/deeplink/compose?allday=true&body='. $e_description .'&enddt=' . $end_tz_date->format('Y-m-d') . '&location=' . $e_address . '&path=%2Fcalendar%2Faction%2Fcompose&rru=addevent&startdt=' . $start_tz_date->format('Y-m-d') . '&subject=' . get_the_title();
							} else {
								$add_to_outlook  = 'https://outlook.live.com/calendar/0/deeplink/compose?body='. $e_description .'&enddt=' . $end_tz_date->format('Y-m-d') . 'T' . $end_tz_date->format('H:i:s') . '&location=' . $e_address . '&path=%2Fcalendar%2Faction%2Fcompose&rru=addevent&startdt=' . $start_tz_date->format('Y-m-d') . 'T' . $start_tz_date->format('H:i:s') . '&subject=' . get_the_title();
							}
							?>
                            <ul class="wpe-calendar-ul">
                                <li class="wpe-calendar-list"><a href="javascript:void(0)">+ Calendar</a>
                                    <ul class="wpe-calendar-sublist">
                                        <li><a target="_blank" href="<?php
											echo esc_url( $add_to_calendar ); ?>">Google
                                                Calendar</a></li>
                                        <li><a target="_blank" href="<?php
											echo esc_url( $add_to_outlook ); ?>">Outlook
                                                Calendar</a></li>
                                        <li id="download-ics"><a href="javascript:void(0)">Download ICS File</a></li>
                                        <?php $venue = get_post_meta( $wpe_location, 'wpevent-loc-venue', TRUE );
                                        if ( $venue == "" ) {
                                        	$venue = 'online';
                                        }
                                        $address = get_post_meta( $wpe_location, 'wpevent-loc-address', TRUE );
                                        if ( $address == "" ) {
                                        	$address = 'webinar';
                                        }
                                        ?>
                                        <div class="ics-text" id="get-ics-text">BEGIN:VCALENDAR<?php echo "\n" ?>VERSION:2.0<?php echo "\n" ?>PRODID:-//WPMINDS//NONSGML v1.0//EN<?php echo "\n" ?>CALSCALE:GREGORIAN<?php echo "\n" ?>BEGIN:VEVENT<?php echo "\n" ?>VENUE:<?php echo esc_html( $wpe_venue ); ?><?php echo "\n" ?>DESCRIPTION:<?php echo strip_tags( get_the_excerpt()); ?><?php echo "\n" ?>ADDRESS:<?php echo esc_html( $wpe_addr ); ?><?php echo "\n" ?>DTSTART:<?php echo $start_tz_date->format( 'Ymd\THis' ); ?><?php echo "\n" ?>DTEND:<?php echo $end_tz_date->format( 'Ymd\THis' ); ?><?php echo "\n" ?>URL;VALUE=URI:<?php echo get_the_permalink( $post_id ); ?><?php echo "\n" ?>SUMMARY:<?php echo strip_tags( get_the_title()); ?><?php echo "\n" ?>LOCATION:<?php echo wp_kses( $venue_html, wpe_get_allowed_html() ); ?><?php echo "\n" ?>PHONE:<?php echo get_post_meta( $post_id, 'wpevent-phone', true ); ?><?php echo "\n" ?>DTSTAMP:<?php echo date('Ymd\THis'); ?><?php echo "\n" ?>UID:<?php echo uniqid(); ?><?php echo "\n" ?>END:VEVENT<?php echo "\n" ?>END:VCALENDAR</div>
                                        <div class="filename"><?php echo strip_tags( get_the_title()) .'.ics' ?></div>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="wpe-description">
                            <?php
                            echo get_the_content();                   // Event Content
                            ?>
                        </div>
						<?php
						$close_event = get_post_meta( $post_id, 'wpevent-close-reg', true );
						if ( $end_date_time < strtotime( current_time( 'mysql' ) ) ) { //current datetime is greater than event end datetime
							?> <div class="wpe-past-event-msg"> <?php
							$option = get_option( 'wpe_display_settings' );
							echo esc_html( $option['past_event_text'] );
							?> </div> <?php
						} else if ( $booked_seats < $seats && $close_event !== 'yes' ) {  // booked seats is less than available seats and event is not closed  
							/**
							 * Prints Registration Form
							 *
							 * @since  1.0.0
							 * @action wpe_registration_form
							 */
							if( empty( $post->post_password ) || !post_password_required() ){
								// do some stuff
								do_action ( 'wp_events_registration_form' );                          // Displays Events Registration Form
								$text = __( 'ThankYou For Registering.', 'simple-wp-events' );
								wpe_get_thankyou_popup( $text );
							}
						} 
						else {
							$option 		 = get_option( 'wpe_forms_settings' );
							$wailtist_form   = $option['waitlist_form'];
							if ( $wailtist_form ) {																	// Displays Events Waitlisting Form
								do_action( 'wp_events_waitlist_form' );
								$text = __( 'ThankYou For Your Interest.', 'simple-wp-events' );
								wpe_get_thankyou_popup( $text );
							} else {
								?> <div class="wpe-close-reg-msg"> <?php
								$option = get_option( 'wpe_display_settings' );							// Displays Text when Waitlisting Form is hide
								$text   = wpe_sanitize( $option['closed_reg'] );
								echo esc_html( $text );
								?> </div> <?php
							}							 
						}
					endwhile;
				endif;
				?>
            </div>
        </div>
    </div>
<?php

get_footer();
?>