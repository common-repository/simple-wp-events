<?php
/**
 * WP Events Registration Form
 *
 * This file mainly consists of HTML
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Displays Registration Form on single page
 *
 * @since 1.0.0
 */
if( !function_exists( 'wpe_registration_form' ) ) {
    function wpe_registration_form() {

        /**
         * Fires Before Registration Form
         *
         * @since 1.0.0
         * @action wpe_before_registration_form
        */
        do_action('wp_event_before_registration_form');

	    $form_options               = get_option( 'wpe_forms_settings' );
	    $captcha_options            = get_option( 'wpe_reCAPTCHA_settings' );
	    $labels                     = isset( $form_options['form_labels'] );
	    $form_button                = isset( $form_options['registration_form_button'] ) ? $form_options['registration_form_button'] : __( 'Submit', 'simple-wp-events' );
        $addrees1                   = isset( $form_options['form_address1'] );
        $addrees2                   = isset( $form_options['form_address2'] );
        $city                       = isset( $form_options['form_city'] );
        $state                      = isset( $form_options['form_state'] );
        $zip                        = isset( $form_options['form_zip'] );
        $fax                        = isset( $form_options['form_fax'] );
        $businessName               = isset( $form_options['form_businessName'] );
        $hearAbout                  = isset( $form_options['form_hear_about'] );
        $req_addrees1               = isset( $form_options['req_form_address1'] );
        $req_addrees2               = isset( $form_options['req_form_address2'] );
        $req_city                   = isset( $form_options['req_form_city'] );
        $req_state                  = isset( $form_options['req_form_state'] );
        $req_zip                    = isset( $form_options['req_form_zip'] );
        $req_fax                    = isset( $form_options['req_form_fax'] );
        $req_phone                  = isset( $form_options['req_form_phone'] );
        $req_email                  = isset( $form_options['req_form_email'] );
        $req_businessName           = isset( $form_options['req_form_businessName'] );
        $form_textin_permission     = isset( $form_options['reg_form_texting_permission'] ) ? $form_options['reg_form_texting_permission'] : 'I agree to receive texts at the number provided from [wpe_firm_name]. Frequency may vary and include information on appointments, events, and other marketing messages. Message/data rates may apply. To opt-out, text STOP at any time.';
        $hide_texting_permission    = isset( $form_options['reg_enable_texting_permission'] );
        ?>
        <div class="wpe-main-form-holder">
            <div class="wpe-register-form-container">
                <form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" class="wpe-register-form" id="wpe-register-form" novalidate autocomplete="off">
                    <div class="wpe-col-full wpe-field">
                        <div class="wpe-above-error-field" id="wpe-error-div"><span class="wpe-error-exclamation">! </span><?php _e( 'There was some problem with your submission. Please review the fields below', 'simple-wp-events' ); ?></div>
                    </div>
                    <div class="wpe-col-2 wpe-field">
                        <div class="wpe-form-control wpe-field-container wpe-left-half">
                        <?php if( $labels) { echo'<label for="wpe_first_name">' . __( 'First Name *', 'simple-wp-events' ) . '</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_first_name" id="wpe_first_name" <?php if( !$labels ) {?>placeholder="<?php _e( 'First Name *', 'simple-wp-events' ); ?>" <?php } ?> required>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                   </div>
                   <div class="wpe-form-control wpe-field-container wpe-right-half">
                        <?php if( $labels) { echo'<label for="wpe_last_name">' . __( 'Last Name *', 'simple-wp-events' ) . '</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_last_name" id="wpe_last_name" <?php if( !$labels ) {?>placeholder="<?php _e( 'Last Name *', 'simple-wp-events' ); ?>" <?php } ?>required>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                   </div>
                </div>
                <div class="wpe-col-2 wpe-field">
                <?php if ( ! $addrees1 ) { 
                    $star = $req_addrees1 ? ' *' : '';
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-left-half">
                        <?php if( $labels) { echo'<label for="wpe_address">' . __( 'Address', 'simple-wp-events' ) . $star .'</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_address" id="wpe_address" <?php if( !$labels ) {?>placeholder="<?php _e( 'Address', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_addrees1 ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php }   if ( ! $addrees2 ) {
                    $star = $req_addrees2 ? ' *' : '';
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-right-half">
                        <?php if( $labels) { echo'<label for="wpe_address_2">' . __( 'Address 2', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_address_2" id="wpe_address_2" <?php if( !$labels ) {?>placeholder="<?php _e( 'Address 2', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_addrees2 ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php  } ?>
                </div>
                <div class="wpe-col-3 wpe-field">
                <?php if ( ! $city ) { 
                    $star = $req_city ? ' *' : '';
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-left-third">
                        <?php if( $labels) { echo'<label for="wpe_city">' . __( 'City ', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_city" id="wpe_city" <?php if( !$labels ) {?>placeholder="<?php _e( 'City', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_city ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php } if ( ! $state ) {
                    $star = $req_state ? ' *' : '';
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-middle-third">
                        <?php if( $labels) { echo'<label for="wpe_state">' . __( 'State ', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_state" id="wpe_state" <?php if( !$labels ) {?>placeholder="<?php _e( 'State', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_state ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php } if ( ! $zip ) { 
                    $star = $req_zip ? ' *' : '';
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-right-third">
                        <?php if( $labels) { echo'<label for="wpe_zip">' . __( 'Zip ', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_zip" id="wpe_zip" <?php if( !$labels ) {?>placeholder="<?php _e( 'Zip', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_zip ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                        <span class="wpe-zip-error-class"><?php _e( 'Please enter correct zip code.', 'simple-wp-events' ); ?></span>
                    </div>
                    <?php }?>
                </div>
                <div class="wpe-col-3 wpe-field">
                    <?php $star = $req_phone ? ' *' : ''; ?>
                    <div class="wpe-form-control wpe-field-container wpe-left-third">
                        <?php if ( $labels) { echo'<label for="wpe_phone">' . __( 'Phone/Cell Phone', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="text" title="(123) 111-1234" name="wpe_phone" id="wpe_phone" <?php if( !$labels ) {?>placeholder="<?php _e( 'Phone/Cell Phone', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_phone ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                        <span class="wpe-phone-error-class"><?php _e( 'Please enter phone in correct format - (123) 111-1234', 'simple-wp-events' ); ?></span>
                    </div>
                    <?php $star = $req_email ? ' *' : ''; ?>
                    <div class="wpe-form-control wpe-field-container wpe-middle-third">
                        <?php if( $labels) { echo'<label for="wpe_email">' . __( 'Email', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="email" name="wpe_email" id="wpe_email" <?php if( !$labels ) {?>placeholder="<?php _e( 'Email', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_email ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                        <span class="wpe-email-error-class"><?php _e( 'Please enter a valid email address.', 'simple-wp-events' ); ?></span>
                    </div>
                    <?php if ( ! $fax ) { 
                    $star = $req_fax ? ' *' : '';
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-right-third">
                        <?php if( $labels) { echo'<label for="wpe_fax">' . __( 'Fax', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="number" name="wpe_fax" id="wpe_fax" <?php if( !$labels ) {?>placeholder="<?php _e( 'Fax', 'simple-wp-events' ); echo $star; ?>"<?php } echo $req_fax ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php } ?>
                </div>
                <?php if ( ! $businessName ) { 
                $star = $req_businessName ? ' *' : '';
                ?>
                <div class="wpe-col-full wpe-field">
                    <div class="wpe-form-control wpe-field-container wpe-full-width">
                        <?php if( $labels) { echo'<label for="wpe_business_name">' . __( 'Business Name', 'simple-wp-events' ) . $star . '</label>';}?>
                        <input class="wpe-field" type="text" name="wpe_business_name" id="wpe_business_name" <?php if( !$labels ) {?>placeholder="<?php _e( 'Business Name', 'simple-wp-events' ); echo $star; ?>" <?php } echo $req_businessName ? 'required' : ''; ?>>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                </div>
                <?php } ?>
                <div class="wpe-col-full wpe-field">
                    <?php
                     if ( ! $hearAbout ) {
                    //display dropdown for hear about us
                    wpe_get_dropdown( 'hear_about_us', 'How did you hear about us?', wpe_get_hearaboutus_options() );
                }
                    $option = get_option('wpe_settings');
                    if( isset( $option['privacy_policy'] ) && $option['privacy_policy'] !== '' ) {?>
                    <div class="wpe-form-control wpe-field-container wpe-full-width">
                        <label for="wpe_privacy_policy"><?php _e( 'Privacy Policy', 'simple-wp-events' ); ?></label>
                        <textarea class="wpe-field" name="wpe_settings[privacy_policy]" id="wpe_privacy_policy" readonly><?php
                            echo trim( esc_textarea( $option['privacy_policy'] ) );  ?></textarea>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php } ?>
                </div>
                <?php
                $consent_box    = isset( $form_options['consent_checkbox'] ) ? $form_options['consent_checkbox'] : __( 'I have read & consent to the above.*', 'simple-wp-events' );
                $disclaimer_box = isset( $form_options['disclaimer_checkbox'] ) ? $form_options['disclaimer_checkbox'] : __( 'I have read & understand your website Disclaimer.*', 'simple-wp-events' );
                ?>
                <div class="wpe-col-full wpe-field">
                    <div class="wpe-form-control wpe-field-container wpe-full-width">
                        <input  type="checkbox" name="wpe_consent_box" id="wpe_consent_box" value="I have read &amp; consent to the above." required>
                        <label for="wpe_consent_box"><?php echo wp_kses( $consent_box, wpe_get_allowed_html() ) ?></label>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <div class="wpe-form-control wpe-field-container wpe-full-width">
                        <input  type="checkbox" name="wpe_disclaimer_box" id="wpe_disclaimer_box" value="I have read &amp; understand your website Disclaimer." required>
                        <label for="wpe_disclaimer_box"> <?php echo wp_kses( $disclaimer_box, wpe_get_allowed_html() ) ?></label>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php
                    if( $hide_texting_permission ) { 
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-full-width">
                        <input  type="checkbox" name="wpe_texting_permission" id="wpe_texting_permission" value="1">
                        <label for="wpe_texting_permission"><?php echo wp_kses( do_shortcode( $form_textin_permission ), wpe_get_allowed_html() ); ?></label>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php
                    } 
                    ?>
                </div>
                <div class="wpe-col-full wpe-field">
                    <div class="wpe-form-control wpe-field-container wpe-full-width">
	                    <?php
		                    ?>
                            <label for="event-seats"><?php _e( 'Seats', 'simple-wp-events' ); ?></label>
		                    <?php
                        wpe_get_seats_dropdown();
	                    ?>
                        <small><?php _e( 'This field is required.', 'simple-wp-events' ); ?></small>
                    </div>
                </div>
                <div style="display: none" class="wpe-full-width wpe-guests-heading"><?php _e( 'Name(s) of Guest(s) Other Than Yourself', 'simple-wp-events' ) ?></div>
                <div class="guest-info wpe-form-control wpe-field-container wpe-full-width">
                    <div style="display: none" class="wpe-col-2 wpe-field guest-box">
                        <div class="wpe-form-control wpe-field-container wpe-left-half">
                            <?php if( $labels) { echo'<label>' . __(  'Guest First Name*', 'simple-wp-events' ) . '</label>';}?>
                            <input class="wpe-field wpe-guest-field" type="text" name="wpe_guest_first_name[]" <?php if( !$labels ) {?>placeholder="<?php _e( 'Guest First Name*', 'simple-wp-events' ); ?>" <?php } ?>>
                            <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                        </div>
                        <div class="wpe-form-control wpe-field-container wpe-right-half">
                            <?php if( $labels) { echo'<label>' . __(  'Guest Last name*', 'simple-wp-events' ) . '</label>';}?>
                            <input class="wpe-field wpe-guest-field" type="text" name="wpe_guest_last_name[]" <?php if( !$labels ) {?>placeholder="<?php _e( 'Guest Last name*', 'simple-wp-events' ); ?>" <?php } ?>>
                            <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="action" value="registration_form">
                <input type="hidden" name="post" value="<?php echo get_the_ID(); ?>">
                <?php wp_nonce_field('wp_event_registration_form','wpe_register_form_nonce');
                $site_key = isset( $captcha_options['reCAPTCHA_site_key'] ) ? $captcha_options['reCAPTCHA_site_key'] : '';
                if( $site_key !== '' ) {
                    ?>
                <div class="form-flex">
                    <div class="wpe-form-control wpe-field-container wpe-full-width">
                        <div class="g-recaptcha" data-expired-callback="CaptchaExpired" data-sitekey="<?php echo esc_attr( $site_key ); ?>" <?php if ( $captcha_options['reCAPTCHA_type'] === 'invisible' ) { echo 'data-size="invisible"'; } ?> ></div>
                        <small class="recaptcha-error"><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                    </div>
                </div>
                        <?php
                    } else {
                        ?>
                        <span class="g-recaptcha"><?php _e( 'Captcha not found.', 'simple-wp-events' ); ?></span>
                        <?php
                    }
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-submit-button">
                        <button id="wpe-button" class="button wpe-button"><?php echo apply_filters( 'wpe_registration_form_button_text', esc_html( $form_button ) ); ?> </button>
                    </div>
                    <div class="wpe-button-loader"></div>
                </form>
            </div>
        </div>

        <?php
        /**
         * Fires after Registration Form
         *
         * @since 1.0.0
         * @action wpe_after_registration_form
         */
        do_action('wp_event_after_registration_form');
    }
}

add_action('wp_events_registration_form', 'wpe_registration_form');


/**
 * Before Registration Form     Displays HTML or text before registration form
 *
 * @since 1.0.2
*/
if( !function_exists( 'wpe_before_registration_form' ) ) {
	function wpe_before_registration_form() {
		$before_form_message = get_option( 'wpe_forms_settings' );
		if ( isset( $before_form_message['before_registration_form_message'] ) && $before_form_message['before_registration_form_message'] !== '' ) {
			$html = '<div class="before-registration-form"><p>' . $before_form_message['before_registration_form_message'] . '</p></div>';
			echo wp_kses( $html, wpe_get_allowed_html() );
		}
	}
}

add_action( 'wp_event_before_registration_form', 'wpe_before_registration_form' );


/**
 * After Registration Form     Displays HTML or text after registration form
 *
 * @since 1.0.2
 */
if( !function_exists( 'wpe_after_registration_form' ) ) {
	function wpe_after_registration_form() {
		$after_form_message = get_option( 'wpe_forms_settings' );
		if ( isset( $after_form_message['after_registration_form_message'] ) && $after_form_message['after_registration_form_message'] !== '' ) {
			$html = '<div class="after-registration-form"><p>' . $after_form_message['after_registration_form_message'] . '</p></div>';
			echo wp_kses( $html, wpe_get_allowed_html() );
		}
	}
}

add_action( 'wp_event_after_registration_form', 'wpe_after_registration_form' );
