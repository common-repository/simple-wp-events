<?php
/**
 * Wp events Subscribe form
*/
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Displays Subscriber Form when No event exists
 *
 * @since 1.0.0
 */
if( !function_exists( 'wpe_display_subscribe_form' ) ) {
	function wpe_display_subscribe_form() {
		/**
		 * Fires before Subscriber Form
		 *
		 * @since  1.0.0
		 * @action wpe_before_subscribe_form
		 */
		do_action( 'wp_event_before_subscribe_form' );

		$form_options               = get_option( 'wpe_forms_settings' );
        $captcha_options            = get_option( 'wpe_reCAPTCHA_settings' );
		$labels                     = isset( $form_options['subscriber_form_labels'] );
		$form_title                 = isset( $form_options['subscriber_form_title'] ) ? sanitize_text_field( $form_options['subscriber_form_title'] ) : '';
		$form_description           = isset( $form_options['subscriber_form_description'] ) ? sanitize_text_field( $form_options['subscriber_form_description'] ) : '';
		$form_button                = isset( $form_options['subscriber_form_button'] ) ? sanitize_text_field( $form_options['subscriber_form_button'] ) : __( 'Subscribe', 'simple-wp-events' );
        $form_textin_permission     = isset( $form_options['subscriber_form_texting_permission'] ) ? sanitize_text_field( $form_options['subscriber_form_texting_permission'] ) : __( 'I agree to receive texts at the number provided from [wpe_firm_name]. Frequency may vary and include information on appointments, events, and other marketing messages. Message/data rates may apply. To opt-out, text STOP at any time.', 'simple-wp-events' );
        $hide_phone_number          = isset( $form_options['subscriber_enable_phone_number'] );
        $req_phone                  = isset( $form_options['req_subform_phone'] );
        $req_email                  = isset( $form_options['req_subform_email'] );
        $hide_texting_permission    = isset( $form_options['subscriber_enable_texting_permission'] );
        ?>
        <div class="wpe-form-holder">
            <div class="wpe-subscribe-form-container">
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" novalidate class="wpe-subscribe-form" id="wpe-subscribe-form" autocomplete="off">
                    <div class="wpe-col-full wpe-field">
                        <div class="wpe-above-error-field">
                            <span class="wpe-error-exclamation">! </span><?php _e( 'There was some problem with your submission. Please review the fields below', 'simple-wp-events' ); ?>
                        </div>
                    </div>
                    <?php wp_nonce_field('wp_events_subscribe_form','wpe_subscribe_form');
                    if( $form_title != '' ) {
                        ?>
                        <h2 class="wpe-h2"><?php echo apply_filters( 'wpe_subscribe_form_heading',  esc_html( $form_title ) ); ?></h2>
	                    <?php
                    }
                    if( $form_description != '' ) {
                        ?>
                        <p class="wpe-form-description"><?php echo apply_filters( 'wpe_subscribe_form_description',  wp_kses( $form_description, wpe_get_allowed_html() ) ); ?></p>
	                    <?php
                    }?>
                    <div class="wpe-name-box wpe-col-2">
                        <div class="wpe-form-control">
                            <?php if( $labels ) { echo'<label for="wpe_username">' . __(  'First Name *', 'simple-wp-events' ) . '</label>';}?>
                            <input type="text" name="wpe_first_name" id="wpe_firstname" <?php if( !$labels ) {?>placeholder="<?php _e( 'First Name*', 'simple-wp-events' ); ?>"<?php }?> required>
                            <small><?php _e( 'This field is required', 'simple-wp-events' ); ?></small>
                        </div>
                        <div class="wpe-form-control">
                            <?php if( $labels ) { echo'<label for="wpe_username">' . __(  'Last Name*', 'simple-wp-events' ) . '</label>';}?>
                            <input type="text" name="wpe_last_name" id="wpe_lastname" <?php if( !$labels ) {?>placeholder="<?php _e( 'Last Name*', 'simple-wp-events' ); ?>"<?php }?> required>
                            <small><?php _e( 'This field is required', 'simple-wp-events' ); ?></small>
                        </div>
                    </div>
                    <div class="wpe-col-2">
                        <?php $star = $req_email ? ' *' : ''; ?>
                        <div class="wpe-form-control">
                            <?php if( $labels ) { echo'<label for="wpe_email">' . __(  'Email', 'simple-wp-events' ) . $star .'</label>';}?>
                            <input type="email" name="wpe_email" id="wpe_email" <?php if( !$labels ) {?>placeholder="<?php _e( 'Email', 'simple-wp-events' ); echo $star; ?>"<?php } echo $req_email ? 'required' : ''; ?>>
                            <small><?php _e( 'This field is required', 'simple-wp-events' ); ?></small>
                            <span class="wpe-email-error-class"><?php _e( 'Please enter a valid email address.', 'simple-wp-events' ); ?></span>
                        </div>
                        <?php 
                        if( $hide_phone_number ) { 
                        ?>
                        <?php $star = $req_phone ? ' *' : ''; ?>
                        <div class="wpe-form-control">
                            <?php if( $labels ) { echo'<label for="wpe_phone">Cell Phone Number'. $star .'</label>';}?>
                            <input type="text" title="(123) 111-1234" name="wpe_phone" id="wpe_phone" <?php if( !$labels ) {?>placeholder="Cell Phone Number<?php echo $star; ?>"<?php } echo $req_phone ? 'required' : ''; ?>>
                            <small><?php _e( 'This field is required', 'simple-wp-events' ); ?></small>
                            <span class="wpe-phone-error-class"><?php _e( 'Please enter phone in correct format - (123) 111-1234', 'simple-wp-events' ); ?></span>
                        </div>
                        <?php } ?>
                    </div>
                   <?php
                    if( $hide_texting_permission ) { 
                    ?>
                    <div class="wpe-form-control wpe-field-container wpe-full-width wpe-texting-permission">
                        <input  type="checkbox" name="wpe_texting_permission" id="wpe_texting_permission" value="1">
                        <label for="wpe_texting_permission"> <?php echo esc_html( do_shortcode( $form_textin_permission ) ); ?></label>
                        <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                    </div>
                    <?php
                    } 
                    ?>
                    <input type="hidden" name="action" value="subscribe_form">
                    <?php
                    $site_key = isset( $captcha_options['reCAPTCHA_site_key'] ) ? $captcha_options['reCAPTCHA_site_key'] : '';
                    if( $site_key !== '' ) {
                        ?>
                        <div class="wpe-form-control wpe-field-container wpe-full-width">
                        <div class="g-recaptcha" data-expired-callback="CaptchaExpired" data-sitekey="<?php echo esc_attr( $site_key ) ?>" <?php if ( $captcha_options['reCAPTCHA_type'] === 'invisible' ) { echo 'data-size="invisible"'; } ?> ></div>
                        <small class="recaptcha-error"><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                        </div>
                        <?php
                    } else {
                        ?>
                        <span class="g-recaptcha"><?php _e( 'Captcha not found.', 'simple-wp-events' ); ?></span>
                        <?php
                    }
                    ?>
                    <button id="wpe-button" class="button wpe-button"><?php echo apply_filters( 'wpe_subscribe_form_button_text',  esc_html( $form_button ) ); ?></button>
                    <div class="wpe-button-loader"></div>
                </form>
            </div>
        </div>
        <?php
		/**
		 * Fires after Subscriber Form
		 *
		 * @since 1.0.0
		 * @action wpe_after_subscribe_form
		 */
        do_action('wp_event_after_subscribe_form');
    }
}

add_action( 'wp_events_subscribe_form', 'wpe_display_subscribe_form' );


/**
 * Before subscriber form Area       Displays HtML or text before subscriber form
 *
 * @since 1.0.2
*/
if( !function_exists( 'wpe_before_subscribe_form' ) ) {
	function wpe_before_subscribe_form() {
		$before_form_message = get_option( 'wpe_forms_settings' );
		if ( isset( $before_form_message['before_subscriber_form_message'] ) && $before_form_message['before_subscriber_form_message'] !== '' ) {
			$html = '<div class="before-subscribe-form '. wpe_dark_mode() .'"><p>' . do_shortcode( $before_form_message['before_subscriber_form_message'] ) . '</p></div>';
			echo wp_kses( $html, wpe_get_allowed_html() );
		}
	}
}

add_action( 'wp_event_before_subscribe_form', 'wpe_before_subscribe_form' );

/**
 * After subscriber Form     Displays HTML or text after subscriber form
 *
 * @since 1.0.2
*/

if( !function_exists( 'wpe_after_subscribe_form' ) ) {
	function wpe_after_subscribe_form() {
		$after_form_message = get_option( 'wpe_forms_settings' );
		if ( isset($after_form_message['after_subscriber_form_message']) && $after_form_message['after_subscriber_form_message'] !== '' ) {
			$html = '<div class="after-subscribe-form '. wpe_dark_mode() .'"><p>' . do_shortcode( $after_form_message['after_subscriber_form_message'] ) . '</p></div>';
			echo wp_kses( $html, wpe_get_allowed_html() );
		}
	}
}

add_action( 'wp_event_after_subscribe_form', 'wpe_after_subscribe_form' );
