<?php
/**
 * This files contain plugin's global functions
 *
 * @since 1.0.2
 */

if ( ! function_exists( 'wpevent_date_time' ) ) {
	/**
	 * Returns event date and time
	 *
	 * Gets UNIX timestamp from post meta and returns
	 * he event start/end date-time in an associative array
	 *
	 * @param $post_ID
	 *
	 * @return array
	 * @since 1.0.2
	 */
	function wpevent_date_time( $post_ID ) {
		$start_date_time = get_post_meta( $post_ID, 'wpevent-start-date-time', TRUE );
		$end_date_time   = get_post_meta( $post_ID, 'wpevent-end-date-time', TRUE );
		$start_arr       = [];
		$end_arr         = [];
		if ( $start_date_time !== '' ) {
			$start_arr = explode( ' ', date( 'Y-m-d H:i', $start_date_time ) );
			$start_arr = array_combine( [ 'start_date', 'start_time', ], $start_arr );
		}
		if ( $end_date_time !== '' ) {
			$end_arr = explode( ' ', date( 'Y-m-d H:i', $end_date_time ) );
			$end_arr = array_combine( [ 'end_date', 'end_time', ], $end_arr );
		}
		if( !empty( $start_arr) && !empty( $end_arr ) ) {
			return array_merge( $start_arr, $end_arr );
		} else if( !empty( $start_arr ) ) {
			return $start_arr;
		} else if ( !empty( $end_arr ) ) {
			return $end_arr;
		}
		return [];
	}
}

if( !function_exists( 'wpevent_event_status' ) ) {
	/**
	 * Returns Event status based on event date
	 *
	 * @param $post_ID
	 *
	 * @return string
	 */
	function wpevent_event_status( $post_ID ) {
		$post_status = get_post_status ( $post_ID );
		if( $post_status !== 'publish' ) {
			return $post_status;
		}
		$start_date_time   = get_post_meta( $post_ID, 'wpevent-start-date-time', TRUE );
		$end_date_time     = get_post_meta( $post_ID, 'wpevent-end-date-time', TRUE );
		$current_date_time = strtotime( current_time( 'mysql' ) );
		if ( $end_date_time < $current_date_time ) {
			return 'Past';
		} if( $start_date_time > $current_date_time ) {
			return 'Future';
		}
		return 'Ongoing';
	}
}

if( !function_exists( 'wpe_send_ajax_response' ) ) {
	/**
	 * Returns JSON response
	 *
	 * @param $message
	 * @param $code
	 *
	 * @return string
	 */
	function wpe_send_ajax_response( $message, $code=200 ) {
		wp_send_json( $message, $code );
		die();
	}
}

if( !function_exists( 'wpe_decode_array' ) ) {
	/**
	 * Returns decoded array
	 *
	 * @param $encoded_arr
	 *
	 * @return array
	 *
	 * @since 1.0.447
	 */
	function wpe_decode_array( $encoded_arr ) {
		return urldecode_deep( $encoded_arr );
	}
}

if( !function_exists( 'wpe_request_log' ) ) {
	/**
	 * Logs every request sent to forms in logs.txt
	 *
	 * @param $request
	 *
	 */
	function wpe_request_log( $request ) {
		$file = plugin_dir_path( __DIR__ ).'/logs.txt';
		$eol = "\r\n";
		$time = date( "Y-m-d h:i:s" );
		$contents = $time . ' ' . serialize( $request ) . $eol;
		file_put_contents( $file, $contents, FILE_APPEND );
	}
}

if ( ! function_exists( 'wpevent_start_date_time' ) ) {
	/**
	 * Returns event start date and time
	 *
	 * Gets UNIX timestamp from post meta and returns
	 * the event start/end date-time in an associative array
	 *
	 * @param $post_ID
	 *
	 * @return array
	 * @since 1.0.448
	 */
	function wpevent_start_date_time( $post_ID ) {
		$start_date_time = get_post_meta( $post_ID, 'wpevent-start-date-time', TRUE );
		$start_arr       = [];
		if ( $start_date_time !== '' ) {
			$start_arr = explode( ' ', date( 'Y-m-d H:i', $start_date_time ) );
			$start_arr = array_combine( [ 'start_date', 'start_time', ], $start_arr );
		}
		if( !empty( $start_arr ) ) {
			return $start_arr;
		}
		return [];
	}
}

if ( ! function_exists( 'get_booked_seats' ) ) {
	/**
	 * Returns number of seats booked
	 *
	 * @global object $wpdb instantiation of the wpdb class.
	 *
	 * @param int $post_id
	 *
	 * @return int
	 * @since 1.0.449
	 */
	function get_booked_seats( $post_id ) {
		global $wpdb;
		$table_name = 'events_registration';
		if ( ! Wp_Events_Db_Actions::wpe_table_exists( $table_name ) ) {
			Wp_Events_Db_Actions::add_registration_table();
		}
		$sql = "SELECT SUM(wpe_seats) FROM {$wpdb->prefix}$table_name WHERE post_id = %d AND 
				wpe_status in ( %d, %d )";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, $post_id, WPE_ACTIVE, WPE_APPROVED ) );
		$result = (int) $result;
		return $result;
	}
}

if ( ! function_exists( 'wpe_is_current' ) ) {
	/**
	 * Compares first two arguments and returns class attributes
	 * with value 'current'
	 *
	 * @param  string  $current
	 * @param  string  $compare
	 * @param  bool    $echo
	 *
	 * @return string
	 *
	 * @since 1.1.0
	 */
	function wpe_is_current( string $current, string $compare, bool $echo = FALSE ) : string {
		if ( $current !== $compare ) {  // return if it's not equal
			return '';
		}

		if ( ! $echo ) {    // return string if echo is set to false
			return 'class="current"';
		}

		echo 'class="current"';
	}
}

if ( ! function_exists( 'wpe_get_remaining_seats' ) ) {
	/**
	 * Returns number of seats remaining for single event
	 *
	 * @param int $post_id
	 *
	 * @return int
	 * @since 1.1.1
	 */
	function wpe_get_remaining_seats( $post_id ) {
		$total_seats  = get_post_meta( $post_id, 'wpevent-seats', true );
		$booked_seats = get_booked_seats( $post_id ); //function defined in wp-events-global-functions.php
		if ( $total_seats === '' ) {
			$remaining_seats = 'N/A';
		} else {
			$remaining_seats = (int) $total_seats - $booked_seats;
		}

		return $remaining_seats;
	}
}

if ( ! function_exists( 'wpe_get_event_dates' ) ) {
	/**
	 * Returns start and end dates of the events
	 *
	 * @param int $post_id
	 *
	 * @return string
	 * @since 1.1.1
	 */
	function wpe_get_event_dates( $post_id ) {
		$event_date_time = wpevent_date_time( $post_id );
		$start_date      = isset( $event_date_time['start_date'] ) ? strtotime( $event_date_time['start_date'] ) : 0;
		$end_date        = isset( $event_date_time['end_date'] ) ? strtotime( $event_date_time['end_date'] ) : 0;
		$start           = date( 'd M Y', $start_date );
		$end             = date( 'd M Y', $end_date );
		if ( $start === $end ) {
			return $start;
		} else {
			return $start . ' -<br>' . $end;
		}
	}
}

if ( ! function_exists( 'wpe_get_total_seats' ) ) {
	/**
	 * Returns number of total seats allocated for an event
	 *
	 * @param int $post_id
	 *
	 * @return int
	 * @since 1.1.1
	 */
	function wpe_get_total_seats( $post_id ) {
		$total_seats  = get_post_meta( $post_id, 'wpevent-seats', true );
		if ( $total_seats === '' ) {
			return 'N/A';
		}else {
			return $total_seats;
		}
	}
}

if ( ! function_exists( 'wpe_get_posts_count' ) ) {
	/**
	 * Returns number of total posts corresponding to $status
	 *
	 * @param string $status
	 *
	 * @return int
	 * @since 1.2.0
	 */
	function wpe_get_posts_count( $status = null ) {
		$args = array(
			'post_type'      => 'wp_events',
			'posts_per_page' => '-1',
			'post_status'	 => array( 'publish', 'draft', 'future', 'private' ),
		);
		if ( $status === 'future' ) {
			$args['meta_query'] = [
				[
					'key'     => 'wpevent-start-date-time',
					'compare' => '>',
					'value'   => strtotime( current_time( 'mysql' ) ),
					'type'    => 'numeric',
				],
			];
			$args['post_status'] = ['publish'];
		}
		if ( $status === 'past' ) {
			$args['meta_query'] = [
				[
					'key'     => 'wpevent-end-date-time',
					'compare' => '<',
					'value'   => strtotime( current_time( 'mysql' ) ),
					'type'    => 'numeric',
				],
			];
			$args['post_status'] = ['publish'];
		}
		if ( $status === 'ongoing' ) {
			$args['meta_query'] = [
				'relation' => 'AND',
				[
					'key'     => 'wpevent-start-date-time',
					'compare' => '<=',
					'value'   => strtotime( current_time( 'mysql' ) ),
					'type'    => 'numeric',
				],
				[
					'key'     => 'wpevent-end-date-time',
					'compare' => '>=',
					'value'   => strtotime( current_time( 'mysql' ) ),
					'type'    => 'numeric',
				],
			];
			$args['post_status'] = ['publish'];
		}
		$posts = get_posts( $args );
		$count = count( $posts );
		return $count;
	}
}

if ( ! function_exists( 'wpe_form_field' ) ) {
	/**
	 * Outputs a field for registration form 
	 *
	 * @param string $key
	 * @param mixed $args
	 * @param string $value
	 *
	 * @since 1.2.0
	 * @return string
	 */
	function wpe_form_field( $key, $field ) {
		$data = array(
			'label'		    => isset( $field['label'] ) ? $field['label'] : '',
			'type'		    => isset( $field['type'] ) ? $field['type'] : 'text',
			'class'	  	    => isset( $field['class'] ) ? $field['class'] : array(),
			'field-size' 	=> isset( $field['field-size'] ) ? $field['field-size'] : 'wpe-large',
			'guest-class'	=> isset( $field['guest-class'] ) ? $field['guest-class'] : array(),
			'id'	   	    => $key.'-id',
			'value'		    => isset( $field['value'] ) ? $field['value'] : '',
			'required'	    => isset( $field['required'] ) ? $field['required'] : false,
			'title'	   	    => isset( $field['title'] ) ? $field['title'] : '',
			'options'		=> isset( $field['options'] ) ? $field['options'] : array(),
			'disabled'	    => isset( $field['disabled'] ) ? $field['disabled'] : false,
			'min'		    => isset( $field['min'] ) ? $field['min'] : '',
			'max'		    => isset( $field['max'] ) ? $field['max'] : '',
			'public'		=> isset( $field['public'] ) ? $field['public'] : false,
			'placeholder'	=> isset( $field['placeholder'] ) ? $field['placeholder'] : '',
		);
				
		if ( $data['required'] ) {
			$data['class'][] = 'wpe-validate-required';
			$required        = 'required';
			$data['label']	 = $data['label'] . '*';
		} else {
			$required = '';
		}

		if ( $data['disabled'] ) {
			$disabled = 'disabled';
		} else {
			$disabled = '';
		}

		$title = '';

		if ( $data['title'] !== '' ) {
			$title = 'title="'. $data['title'] .'"';
		}

		$max = '';
		$min = '';

		if ( $data['min'] !== '' && $data['max'] !== '' ) {
			$min = 'min="'. $data['min'] .'"';
			$max = 'max="'. $data['max'] .'"';
		}

		$minmax = $min . ' ' . $max;

		$field_html = '<div class="wpe-form-control wpe-field-container ' . $data['field-size'] . ' ' . esc_attr( implode( ' ', $data['guest-class'] ) ) .'">';
		$label_id   = $data['id'];

		switch ( $data['type'] ) {		
			case 'textarea':
				$field_html .= '<label>' . $data['label'] . '</label>';
				$field_html .= '<textarea name="' . esc_attr( $key ) . '" '. $title .' class="input-text wpe-field ' . esc_attr( implode( ' ', $data['class'] ) ) . '" id="' . esc_attr( $data['id'] ) . '">' . esc_textarea( $data['value'] ) . '</textarea>';
				break;
			case 'checkbox':
				$field_html .= '<label class="checkbox">
						<input type="' . esc_attr( $data['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $data['class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $data['id'] ) . '" data-unchecked-value="0" value="1" ' . checked( $data['value'], 1, false ) . ' ' . $disabled .' /> ' . $data['label'] . $required . '</label>';

				break;
			case 'text':
			case 'password':
			case 'datetime':
			case 'datetime-local':
			case 'date':
			case 'month':
			case 'time':
			case 'week':
			case 'number':
			case 'email':
			case 'url':
			case 'tel':
				$field_html .= '<label>' . $data['label'] . '</label>';
				$field_html .= '<input type="' . esc_attr( $data['type'] ) . '" '. $title .' class="input-text wpe-field ' . esc_attr( implode( ' ', $data['class'] ) ) . '" ' . $minmax . ' name="' . esc_attr( $key ) . '" id="' . esc_attr( $data['id'] ) . '"  value="' . esc_attr( $data['value'] ) . '" ' . $required . '/>';
				break;
			case 'hidden':
				$field_html .= '<input type="' . esc_attr( $data['type'] ) . '" class="input-hidden wpe-field ' . esc_attr( implode( ' ', $data['class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $data['id'] ) . '" value="' . esc_attr( $data['value'] ) . '" />';
				break;
			case 'select':
				$options	= '';

				$field_html .= '<label>' . $data['label'] . '</label>';
				foreach ( $data['options'] as $option_text ) {
					$option_text = trim( $option_text, " " );
					$options .= '<option value="' . esc_attr( $option_text ) . '" ' . selected( $data['value'], $option_text, false ) . '>' . esc_html( $option_text ) . '</option>';
				}

				$field_html .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $data['id'] ) . '" class="select wpe-field ' . esc_attr( implode( ' ', $data['class'] ) ) . '" >
						' . $options . '
					</select>';
				break;
			case 'radio':
				$label_id .= '_' . current( array_keys( $data['options'] ) );

				if ( ! empty( $data['options'] ) ) {
					foreach ( $data['options'] as $option_text ) {
						$field_html .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $data['class'] ) ) . '" value="' . esc_attr( $option_text ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $data['id'] ) . '"' . checked( $data['value'], $option_text, false ) . ' />';
						$field_html .= '<label class="radio">' . esc_html( $option_text ) . '</label>';
					}
				}
				break;
		}

		$field_html .= '<small>Error Message</small></div>';

		if ( ! empty( $field_html ) ) {
			$label_html = '';

			if ( $data['label'] && 'checkbox' !== $data['type'] ) {
				$label_html .= '<label>' . wp_kses_post( $data['label'] ) . $required . '</label>';
			}
		}

		echo wp_kses( $field_html, wpe_get_allowed_html() );
	}
}

if ( ! function_exists( 'wpe_get_hearaboutus_options' ) ) {
	/**
	 * Returns options for hear about us dropdown
	 *
	 * @return array
	 * @since 1.2.0
	 */
	function wpe_get_hearaboutus_options() {
		$form_options	 = get_option( 'wpe_forms_settings' );
		$default_options = 'An Email I Received, Blog / Facebook, Internet / Search Engine, Landing Pages, Radio and TV, Link from another website, Mailing / Postcard, Newsletter, Newspaper, Other, Referral';
	    $options	     = isset( $form_options['hearaboutus_options'] ) ? $form_options['hearaboutus_options'] : '';
		$options 		 = trim( $options );
		if( $options === '' ) {
			$options = $default_options;
		}
		$options		 = explode( ',', $options );
		return $options;
	}
}

if ( ! function_exists( 'wpe_get_dropdown' ) ) {
	/**
	 * Returns select dropdown
	 *
	 * @param string $name
	 * @param string $label
	 * @param array $options
	 * 
	 * @return string
	 * @since 1.2.0
	 */
	function wpe_get_dropdown( $name, $label, $options ) {
		?>
		<div class="wpe-form-control wpe-field-container wpe-full-width">
			<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>">
			<?php
				for( $i = 0; $i < sizeof( $options ); $i++ ) {
					?>
					<option value="<?php echo esc_attr( $options[$i] ); ?>"><?php echo esc_html( $options[$i] ); ?></option>
					<?php
				}
			?>
			</select>
        </div>
		<?php
	}
}

if ( ! function_exists( 'get_confirmation_message' ) ) {
	/**
	 * Returns confirmation message for single events
	 *
	 * returns value from settings if not saved manually
	 *
	 * @param  $post_id
	 *
	 * @param  $mail_options
	 *
	 * @return string
	 */
	function get_confirmation_message( $post_id, $mail_options, $type ) {
		$meta = get_post_meta( $post_id, 'wpevent-confirmation-message', true );
		if( $meta ) {
			return $meta;
		}
		if( $type === 'webinar' ){
			return wpautop( $mail_options['webinar_success_message'] );
		}

		return wpautop( $mail_options['mail_success_message'] );
	}
}

if( ! function_exists( 'wpe_get_captcha_type' ) ) {
	/**
	 * Returns Type of captcha selected in settings
	 *
	 * @return string
	 *
	 * @since 1.3.0
	 */
	function wpe_get_captcha_type() {
        $option = get_option('wpe_reCAPTCHA_settings');
        return $option['reCAPTCHA_type'];
    }
}

if( ! function_exists( 'wpe_get_site_key' ) ) {
	/**
	 * Returns captcha site key from settings
	 *
	 * @return string
	 *
	 * @since 1.3.3
	 */
	function wpe_get_site_key() {
        $option = get_option('wpe_reCAPTCHA_settings');
        return $option['reCAPTCHA_site_key'];
    }
}

if( ! function_exists( 'wpe_get_secret_key' ) ) {
	/**
	 * Returns captcha secret key from settings
	 *
	 * @return string
	 *
	 * @since 1.3.3
	 */
	function wpe_get_secret_key() {
        $option = get_option('wpe_reCAPTCHA_settings');
        return $option['reCAPTCHA_secret_key'];
    }
}

if( ! function_exists( 'wpe_get_past_events' ) ) {
	/**
	 * Returns Past Events
	 *
	 * @since 1.5.2
	 */
	function wpe_get_past_events() {
		$past_events = array();
		$args = [
			'post_type'      	 => 'wp_events',
			'posts_per_page' 	 => -1,
			'post_status'		 => 'publish',
			'meta_key'       	 => 'wpevent-start-date-time',
			'order'          	 => 'ASC',
			'orderby'        	 => 'meta_value_num',
			'meta_query'     	 => [
				'price_clause' 	 => [
					'key'     	 => 'wpevent-end-date-time',
					'value'   	 => strtotime( current_time( 'mysql' ) ),
					'type'    	 => 'numeric',
					'compare' 	 => '<',
				],
			],
		];

		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$past_events[] = $post->ID;
		}

		return $past_events;
    }
}

if( ! function_exists( 'wpe_dark_mode' ) ) {
	/**
	 * Returns class for dark mode
	 *
	 * @since 1.5.2
	 */
	function wpe_dark_mode() {
		$display_options = get_option( 'wpe_display_settings' );
        $darkmode        = isset( $display_options['dark_mode'] ) ? 'wpe-dark-mode-text' : '';
		return $darkmode;
    }
}

if( ! function_exists( 'wpe_dark_bg' ) ) {
	/**
	 * Returns class for dark mode
	 *
	 * @since 1.5.2
	 */
	function wpe_dark_bg() {
		$display_options = get_option( 'wpe_display_settings' );
        $darkmode        = isset( $display_options['dark_mode'] ) ? 'wpe-dark-mode' : '';
		return $darkmode;
    }
}

if( ! function_exists( 'wpe_get_admin_timezone' ) ) {
	/**
	 * Returns name of admin's local timezone
	 *
	 * @since 1.7.4
	 * @return string
	 */
	function wpe_get_admin_timezone() {
		$option = get_option('wpe_settings');
		return $option['admin_timezone'];
	}
}

if( ! function_exists( 'wpe_get_allowed_html' ) ) {
	/**
	 * Returns allowed html array for filtering
	 *
	 * @return array
	 * @since 1.5.3
	 */
	function wpe_get_allowed_html() {
		$allowed_html = array(
			'div' => array(
				'class' => array(),
			),
			'label' => array(
				'class' => array(),
			),
			'textarea' => array(
				'name' => array(),
				'class' => array(),
				'id' => array(),
			),
			'input' => array(
				'type' => array(),
				'class' => array(),
				'value' => array(),
				'name' => array(),
				'id' => array(),
				'checked' => array(),
				'disabled' => array(),
				'required' => array(),
				'style' => array(),
			),
			'small' => array(),
			'option' => array(
				'value' => array(),
				'selected' => array(),
			),
			'select' => array(
				'name' => array(),
				'class' => array(),
				'id' => array(),
			),
			'br' => array(),
			'span' => array(
				'class' => array(),
			),
			'p' => array(
				'style' => array(),
			),
			'a' => array(
				'href' => array(),
				'class' => array(),
			),
			'button' => array(
				'class' => array(),
				'id' => array(),
			),
			'ul' => array(
				'class' => array(),
			),
			'li' => array(
				'class' => array(),
			),
			'strong' => array(
				'class' => array(),
			),
		);

		return $allowed_html;
    }
}

if( ! function_exists( 'wpe_sanitize' ) ) {
	/**
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 *
	 * @param string|array $data Data to sanitize.
	 * @return string|array
	 */
	function wpe_sanitize( $data ) {
		if ( is_array( $data ) ) {
			return array_map( 'wpe_sanitize', $data );
		} else {
			return is_scalar( $data ) ? sanitize_text_field( $data ) : $data;
		}
	}
}

if( ! function_exists( 'wpe_escape_html' ) ) {
	/**
	 * Clean html using wp_kses. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 *
	 * @param string|array $data Data to sanitize.
	 * @return string|array
	 */
	function wpe_escape_html( $data ) {
		if ( is_array( $data ) ) {
			return array_map( 'wpe_escape_html', $data );
		} else {
			return is_scalar( $data ) ? wp_kses_post( $data ) : $data;
		}
	}
}

if( ! function_exists( 'wpe_get_IP' ) ) {
	/**
	 * gets the current user IP address
	 *
	 * @since  1.7.4
	 */
	function wpe_get_IP() {
		//whether ip is from the share internet
	
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = filter_var( $_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP);
		} //whether ip is from the proxy
		elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = filter_var( $_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP);
		} //whether ip is from the remote address
		else {
			$ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
		}
		return $ip;
	}
}

if( ! function_exists( 'wpe_get_user_timezone' ) ) {
	/**
	 * Returns name of user's local timezone
	 *
	 * @since 1.7.4
	 * @return string
	 */
	function wpe_get_user_timezone() {
		$ip	   = wpe_get_IP();
		$ipdat = @json_decode( file_get_contents( "http://ip-api.com/json/" . $ip ) );
		if ( strpos( $_SERVER['HTTP_HOST'], "local" ) !== false || $_SERVER['HTTP_HOST'] == '127.0.0.1' ) {
			$ipdat = @json_decode( file_get_contents( "http://ip-api.com/json/" ) );
		}
		if( $ipdat ) {
			return $ipdat->timezone;
		}
		return 'America/New_York';
	}
}

if( ! function_exists( 'wpe_get_event_time' ) ) {
	/**
	 * Returns event time
	 *
	 * @since 1.7.5
	 * @return string
	 */
	function wpe_get_event_time( $post_id ) {
		$event_date_time = wpevent_date_time( $post_id );
		$start_time      = isset( $event_date_time['start_time'] ) ? strtotime( $event_date_time['start_time'] ) : 0;
		$end_time        = isset( $event_date_time['end_time'] ) ? strtotime( $event_date_time['end_time'] ) : 0;
		$wpe_all_day 	 = get_post_meta( $post_id, 'wpevent-all-day', TRUE );
		$wpe_no_endtime  = get_post_meta( $post_id, 'wpevent-no-endtime', TRUE );
		if( $wpe_all_day ) {
			return 'All Day';
		} else if( $wpe_no_endtime ) {
			return date( 'h:i A', esc_html( $start_time ) );
		} else {
			return date( 'h:i A', esc_html( $start_time ) ) . ' - ' . date( 'h:i A', esc_html( $end_time ) );
		}
	}
}

if( ! function_exists( 'wpe_get_current_post_type' ) ) {
    /**
     * Gets the current post type for admin area
     * 
     * @since 1.8.0
     * @return string|null
     */
    function wpe_get_current_post_type() {
	
        global $post, $typenow, $current_screen;
        
        if ( $post && $post->post_type ) return $post->post_type;
        
        else if( $typenow ) return $typenow;
        
        else if ( $current_screen && $current_screen->post_type ) return $current_screen->post_type;
        
        else if( isset($_REQUEST['post_type'] ) ) return sanitize_key( $_REQUEST['post_type'] );
        
        return null;
	}
}

if( ! function_exists( 'wpe_auto_email' ) ) {
	/**
	 * Calls mail function for reminder emails
	 * 
	 * @param int $post_id
	 *
	 * @since 1.8.0
	 * @return string
	 */
	function wpe_auto_email( $post_id ) {
		global $wpdb;
		$form_data 		 = array(
			'post' => $post_id,
		);
		Wpe_Shortcodes::set_form_data( $form_data );
		$mail_options    = get_option( 'wpe_mail_settings' );
		$firm_info       = get_option( 'wpe_firm_settings' );
		$event_date_time = wpevent_date_time( $post_id );
		$start_date      = isset( $event_date_time['start_date'] ) ? strtotime( $event_date_time['start_date'] ) : 0;
		$end_date        = isset( $event_date_time['end_date'] ) ? strtotime( $event_date_time['end_date'] ) : 0;
		$start           = date( 'F j', $start_date );
		$table_name      = $wpdb->prefix . 'events_registration';
		$results         = $wpdb->get_results( $wpdb->prepare('SELECT * FROM '. $table_name .' WHERE post_id = %d', $post_id ) );
		$subject         = html_entity_decode( do_shortcode( $mail_options['reminder_subject'] ) );
		$from_name       = $firm_info['mail_from_name'];
		$from_email      = $mail_options['mail_from'];
		$headers[]       = 'Content-Type: text/html;';
		$headers[]       = "from :$from_name <$from_email>";
		$cc				 = $mail_options['reminder_cc'];
		if( $cc != '' ) {
			$cc = str_replace( " ", "", $cc );
			$cc_array = explode( ",", $cc );
			foreach ( $cc_array as $cc_email ) {
				$form_data 		 = array(
					'post' 			 => $post_id,
					'wpe_first_name' => 'Admin',
					'wpe_last_name'  => '',
				);
				Wpe_Shortcodes::set_form_data( $form_data );
				$message = wpautop( do_shortcode( $mail_options['reminder_mail_message'] ) );
				wp_mail( $cc_email, $subject, $message, $headers );
			}
		}
		if( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				if( $result->wpe_status == 1 || $result->wpe_status == 3 ) {
					$email   = esc_attr( $result->email );
					$form_data 		 = array(
						'post' 			 => $post_id,
						'wpe_first_name' => $result->first_name,
						'wpe_last_name'  => $result->last_name,
					);
					Wpe_Shortcodes::set_form_data( $form_data );
					$message = wpautop( do_shortcode( $mail_options['reminder_mail_message'] ) );
					wp_mail( $email, $subject, $message, $headers );
				}
			}
			return 1;
		}
		return 2;
	}
}

if( ! function_exists( 'wpe_get_active_posts' ) ) {
	/**
	 * Returns number of posts to be displayed on
	 * archive page
	 *
	 * @return int
	 *
	 * @since 1.8.0
	 */
	function wpe_get_active_posts() {
        $posts = get_posts( wpe_get_default_query_args() );
		$count = 0;
		foreach( $posts as $post ) {
			$hide_in_archive = get_post_meta( $post->ID, 'wpevent-hide-archive', true );
			if ( $hide_in_archive === 'yes' ) {
				continue;
			}
			$count++;
		}
		return $count;
    }
}

if ( ! function_exists( 'wpe_get_event_address' ) ) {
	/**
	 * Returns event venue output
	 *
	 * returns nothing if event type is webinar
	 *
	 * @param $post_id
	 *
	 * @since 1.0.448
	 */
	function wpe_get_event_address( $post_id ) {

		$wpe_type 		 = get_post_meta( $post_id, 'wpevent-type', TRUE );
		$wpe_location 	 = (int) get_post_meta( $post_id, 'wpevent-location', TRUE );
		$location_id 	 = $wpe_location != 0 ? $wpe_location : $post_id;
		$venue_html 	 = '';

		if ( $wpe_type !== 'webinar' && $location_id !== 0 ) {
			$venue_meta 	 = $wpe_location != 0 ? 'wpevent-loc-venue' : 'wpevent-venue';
			$address_meta 	 = $wpe_location != 0 ? 'wpevent-loc-address' : 'wpevent-address';
			$city_meta  	 = $wpe_location != 0 ? 'wpevent-loc-city' : 'wpevent-city';
			$state_meta 	 = $wpe_location != 0 ? 'wpevent-loc-state' : 'wpevent-state';
			$zip_meta   	 = $wpe_location != 0 ? 'wpevent-loc-zip' : 'wpevent-zip';
			$country_meta 	 = $wpe_location != 0 ? 'wpevent-loc-country' : 'wpevent-country';
			$wpe_venue       = get_post_meta( $location_id, $venue_meta, TRUE ) ?? '';
			$wpe_addr        = get_post_meta( $location_id, $address_meta, TRUE ) ?? '';
			$wpe_city        = get_post_meta( $location_id, $city_meta, TRUE ) ?? '';
			$wpe_state       = get_post_meta( $location_id, $state_meta, TRUE ) ?? '';
			$wpe_zip         = get_post_meta( $location_id, $zip_meta, TRUE ) ?? '';
			$wpe_country     = get_post_meta( $location_id, $country_meta, TRUE ) ?? '';
			$venue_data 	 = array(
				'venue'   => $wpe_venue,
				'address' => $wpe_addr,
				'city' 	  => $wpe_city,
				'state'	  => $wpe_state,
				'zip'	  => $wpe_zip,
				'country' => $wpe_country,
			);
			$venue_data = apply_filters( 'wpe_event_address', $venue_data );
			$venue_data = array_filter( $venue_data );
			$venue_html = implode( ", ", $venue_data );
			if ( $venue_html !== '' ) {
				$venue_html = '<span class="wpe-location">' . wp_kses( $venue_html, wpe_get_allowed_html() ) . '</span>';
			}
		}
		return $venue_html;
	}
}