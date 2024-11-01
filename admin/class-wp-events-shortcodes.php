<?php

class Wpe_Shortcodes {

	/**
     * create all form data
	 * @var $form_data
	 */
	private static $form_data;

	/**
     * Shortcode class constructor
     *
     * all shortcodes callback added here
     *
	*/
    public function __construct(){
        add_shortcode('wpe_event_name', [ $this, 'wpe_event_name' ] );
        add_shortcode('wpe_user_first_name', [ $this, 'wpe_user_first_name' ] );
        add_shortcode('wpe_user_email', [ $this, 'wpe_user_email' ] );
        add_shortcode('wpe_user_last_name', [ $this, 'wpe_user_last_name' ] );
		add_shortcode('wpe_user_phone', [ $this, 'wpe_user_phone' ] );
        add_shortcode('wpe_event_details', [ $this, 'wpe_event_details' ] );
        add_shortcode('wpe_registration_details', [ $this, 'wpe_registration_details' ] );
        add_shortcode('wpe_event_date_time', [ $this, 'wpe_event_date_time' ] );
        add_shortcode('wpe_event_link', [ $this, 'wpe_event_link' ] );
        add_shortcode('wpe_event_seats', [ $this, 'wpe_event_seats' ] );
        add_shortcode('wpe_site_url', [ $this, 'wpe_site_url' ] );
        add_shortcode('wpe_firm_name', [ $this, 'wpe_firm_name' ] );
        add_shortcode('wpe_notification_email', [ $this, 'wpe_notification_email' ] );
        add_shortcode('wpe_firm_phone', [ $this, 'wpe_firm_phone' ] );
        add_shortcode('wpe_firm_email', [ $this, 'wpe_firm_email' ] );
        add_shortcode('wpe_owner_name', [ $this, 'wpe_owner_name' ] );
    }


	/**
	 * gets the form data
	 *
	 * @return mixed
	 */
	public static function get_form_data() {
		return self::$form_data;
	}

	/**
	 * sets the attribute form data
	 *
	 * this is the most important method data needs to
	 * be set before using the shortcodes
	 *
	 * @param  mixed  $form_data
	 */
	public static function set_form_data( $form_data ) : void {
		self::$form_data = $form_data;
	}

	public function wpe_event_name(){
	    return get_the_title( self::$form_data['post'] ?? self::$form_data['post_id'] );
    }

    public function wpe_user_first_name(){
	    return sanitize_text_field( self::$form_data['wpe_first_name'] ?? self::$form_data['first_name'] );
    }

    public function wpe_user_email(){
        return self::$form_data['wpe_email'] ?? self::$form_data['email'];
    }

    public function wpe_user_last_name(){
        return sanitize_text_field( self::$form_data['wpe_last_name'] ?? self::$form_data['last_name'] );
    }
	
	public function wpe_user_phone(){
        return sanitize_text_field( self::$form_data['wpe_phone'] ?? self::$form_data['phone'] );
    }

	public function wpe_event_date_time() {
		$post_id         = self::$form_data['post'] ?? self::$form_data['post_id'];
		$event_date_time = wpevent_date_time( $post_id );
	    $start_date      = isset( $event_date_time['start_date'] ) ? strtotime( $event_date_time['start_date'] ) : 0;
	    $end_date        = isset( $event_date_time['end_date'] ) ? strtotime( $event_date_time['end_date'] ) : 0;
	    
		ob_start();
        ?>
        <p style="margin: 5px 0">
            <strong>Date: </strong>
            <?php
                if ( $start_date === $end_date ) {
                    echo date( 'F j, Y', $start_date );
                } else {
                    echo date( 'F j, Y', $start_date ) . ' - ' . date( 'F j, Y', $end_date );
                } ?>
        </p>
        <p style="margin: 5px 0">
            <strong>Time: </strong>
            <?php echo wpe_get_event_time( $post_id ); ?>
        </p>
	    <?php
		return ob_get_clean();
	}

	public function wpe_event_link(){
	    return get_permalink( self::$form_data['post'] ?? self::$form_data['post_id'] );
    }

	public function wpe_event_seats(){
        return sanitize_text_field( self::$form_data['wpe_seats'] );
    }

	public function wpe_site_url(){
        return '<a href="'. get_site_url() .'">'. get_site_url() .'</a>';
    }

	public function wpe_firm_name(){
		$options = get_option( 'wpe_firm_settings' );
		$firm 	 = $options['mail_from_name'];
        return $firm;
    }

	public function wpe_notification_email(){
        $options = get_option( 'wpe_firm_settings' );
		$email 	 = $options['admin_mail'];
        return '<a href="mailto:'. $email .'">'. $email .'</a>';
    }

	public function wpe_firm_phone(){
        $options = get_option( 'wpe_firm_settings' );
		$phone 	 = $options['firm_phone'];
        return $phone;
    }

	public function wpe_firm_email(){
        $options = get_option( 'wpe_firm_settings' );
		$email 	 = $options['admin_mail'];
        return $email;
    }

	public function wpe_owner_name(){
        $options = get_option( 'wpe_firm_settings' );
		$name 	 = $options['owner_name'];
        return $name;
    }


    public function wpe_event_details() {
	    $post_id         = self::$form_data['post'] ?? self::$form_data['post_id'];
	    $event_name      = get_the_title( $post_id );
	    $event_date_time = wpevent_date_time( $post_id );
	    $start_date      = isset( $event_date_time['start_date'] ) ? strtotime( $event_date_time['start_date'] ) : 0;
	    $start_time      = isset( $event_date_time['start_time'] ) ? strtotime( $event_date_time['start_time'] ) : 0;
	    $end_date        = isset( $event_date_time['end_date'] ) ? strtotime( $event_date_time['end_date'] ) : 0;
	    $end_time        = isset( $event_date_time['end_time'] ) ? strtotime( $event_date_time['end_time'] ) : 0;
        ob_start();
        ?>
        <p style="color: blue; margin: 5px 0; text-transform: uppercase;">
            <strong><?php echo esc_html( $event_name ); ?></strong>
        </p>
        <p style="margin: 5px 0">
            <strong>DAY: </strong>
            <?php
                if ( $start_date === $end_date ) {
                    echo date( 'F j, Y', $start_date ) . ' ' . wpe_get_event_time( $post_id );
                } else {
                    echo date( 'F j, Y', $start_date ) . ' - ' . date( 'F j, Y', $end_date ) . ' ' . wpe_get_event_time( $post_id );
                } ?>
        </p>
	    <?php
		$venue = wpe_get_event_address( $post_id );
		if ( $venue != '' ) echo '<strong>LOCATION:</strong><br> ' . $venue;
	    return ob_get_clean();
    }

    public function wpe_registration_details(){
		$form_options    = get_option( 'wpe_forms_settings' );
		$addrees1        = isset( $form_options['form_address1'] );
        $city            = isset( $form_options['form_city'] );
        $state           = isset( $form_options['form_state'] );
        $zip             = isset( $form_options['form_zip'] );
        $fax             = isset( $form_options['form_fax'] );
        $hearAbout       = isset( $form_options['form_hear_about'] );
		$mail_options    = get_option( 'wpe_mail_settings' );

	    $post_id        = self::$form_data['post'] ?? self::$form_data['post_id'];
	    $wpe_first_name = sanitize_text_field( self::$form_data['wpe_first_name'] ?? self::$form_data['first_name'] );
	    $wpe_last_name  = sanitize_text_field( self::$form_data['wpe_last_name'] ?? self::$form_data['last_name'] );
	    $wpe_email      = sanitize_text_field( self::$form_data['wpe_email'] ?? self::$form_data['email'] );
	    $wpe_phone      = sanitize_text_field( self::$form_data['wpe_phone'] ?? self::$form_data['phone'] );
	    $wpe_seats      = sanitize_text_field( self::$form_data['wpe_seats'] );
		$wpe_address	= sanitize_text_field( self::$form_data['wpe_address'] ?? self::$form_data['addres_one'] );
		$wpe_city		= sanitize_text_field( self::$form_data['wpe_city'] ?? self::$form_data['city'] );
		$wpe_state		= sanitize_text_field( self::$form_data['wpe_state'] ?? self::$form_data['state'] );
		$wpe_zip		= sanitize_text_field( self::$form_data['wpe_zip'] ?? self::$form_data['zip'] );
		$wpe_source		= sanitize_text_field( self::$form_data['hear_about_us'] );
		$texting 		= self::$form_data['wpe_texting_permission'] ?? self::$form_data['wpe_texting'];
		$wpe_texting	= $texting == '1' ? 'Yes' : 'No';

        $registration_details = "<p style='margin: 5px 0'><strong>First name</strong>: $wpe_first_name</p>";
        $registration_details .= "<p style='margin: 5px 0'><strong>Last name</strong>: $wpe_last_name</p>";
		if( ! $addrees1 && $wpe_address != '' ):
		$registration_details .= "<p style='margin: 5px 0'><strong>Address</strong>: $wpe_address</p>";
		endif;
		if( ! $city && $wpe_city != '' ):
        $registration_details .= "<p style='margin: 5px 0'><strong>City</strong>: $wpe_city</p>";
		endif;
		if( ! $state && $wpe_state != '' ):
        $registration_details .= "<p style='margin: 5px 0'><strong>State</strong>: $wpe_state</p>";
        endif;
		if( ! $zip && $wpe_zip != '' ):
		$registration_details .= "<p style='margin: 5px 0'><strong>Zip Code</strong>: $wpe_zip</p>";
        endif;
		$registration_details .= "<p style='margin: 5px 0'><strong>Email</strong>: $wpe_email</p>";
		if( $wpe_phone != '' ):
        $registration_details .= "<p style='margin: 5px 0'><strong>Phone</strong>: $wpe_phone</p>";
		endif;
		if( ! $hearAbout && $wpe_source != '' ):
        $registration_details .= "<p style='margin: 5px 0'><strong>How did you hear about us</strong>: $wpe_source</p>";
		endif;
		if( $wpe_seats != '' ):
        $registration_details .= "<p style='margin: 5px 0'><strong>Seats</strong>: $wpe_seats</p>";
		endif;
		$registration_details .= "<p style='margin: 5px 0'><strong>Texting Permission</strong>: $wpe_texting</p>";

		if( isset( self::$form_data['wpe_guest_first_name'] ) && isset( self::$form_data['wpe_guest_last_name'] ) ) {
			$guest_info = $this->get_guest_information( self::$form_data['wpe_guest_first_name'], self::$form_data['wpe_guest_last_name'] );
		} else {
			$guest_info = self::$form_data['guests'];
		}
		$guest_info = trim( $guest_info );
		if( $guest_info !== FALSE && $guest_info !== '' ) {
			$registration_details .= "<p style='margin: 5px 0'><strong>Guests</strong>: $guest_info</p>";
		}

        return $registration_details;
    }

	private function get_guest_information( $guest_first_names, $guest_last_names ) {
		$guest_names = isset( $guest_first_names ) ? $guest_first_names : false ;
		$guest_last_names = isset( $guest_last_names ) ? $guest_last_names : false ;

		if( $guest_names === false && $guest_last_names === false ) {     // returns false if  empty
			return false;
		}
		for( $i=0; $i < count( $guest_last_names ); $i++ ) {
			$guest_names[$i] = sanitize_text_field( $guest_names[$i] ). ' ' .sanitize_text_field( $guest_last_names[$i] );
		}
		return implode( ', ',$guest_names );
	}

}