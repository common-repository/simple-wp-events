jQuery(document).ready(function ($) {
	'use strict';
	
	$("#wpe_phone").inputmask({"mask": "(999) 999-9999"});

	/**
	 * display error if form is submitted without checking recaptcha
	 * 
	 * @since 1.2.0
	 * @returns bool
	 */
	 function wpeValidRecaptcha() {
		var response = grecaptcha.getResponse();
		if (response.length === 0) {
			$('.recaptcha-error').text('The captcha is required.').css('color', 'red');
			$('.recaptcha-error').css('display', 'block');
			$('.recaptcha-error').css('visibility', 'visible');
			return false;
		} else {
			$('.recaptcha-error').css('display', 'none');
			return true;
		}
	}

	/**
	 * ajax request for reCAPTCHA validation
	 * 
	 * @param {object} form 
	 * @param {string} data 
	 * @param {string} action2
	 * 
	 * @since 1.2.0 
	 */
	function wpeVerifyCaptcha( form ) {
		var serializedValues = form.serialize();
		jQuery.ajax({ 
			type: 'POST',
			url: wpe_ajaxobject.ajaxurl,
			data: { serializedValues,
					action: 'wpe_verify_captcha',
					captchaResponse: grecaptcha.getResponse() },
			success: function( response ) {
				if( response === 'success' ) {
					submitForm( form );
				} else {
					wpe_popup('Please verify captcha and submit again.');
				}
			},
			error: function( error ) {
				wpe_popup('Could not verify reCAPTCHA.');
			}
		});
	}

	/**
	 * email validate function
	 * */
	function validateEmail( $email ) {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		return emailReg.test( $email );
	}

	/**
	 * email validate function
	 * */
	function validatePhone( $phone ) {
		var phoneReg = /^(\([0-9]{3}\) |[0-9]{3}-)[0-9]{3}-[0-9]{4}$/;
		return phoneReg.test( $phone );
	}

	/**
	 * zip validate function
	 * */
	function validateZip( $zip ) {
		var zipReg = /^\d{5}$/;
		if( $zip.length != 5 || ! zipReg.test( $zip ) ) {
			return false;
		}
		return true;
	}

	if ( $('body').hasClass('single-wp_events') || $('form').hasClass('wpe-subscribe-form') ) {
		/**
		 *Form Validation
		* */
		//variables
		var siteKey      = wpe_ajaxobject.captchaSiteKey;
		var secretKey    = wpe_ajaxobject.captchaSecretKey;
		var form 		 = '';
		var updatedClass = '';
		var allForms 	 = $("form");
		allForms.each( function() {
			var form 	  = $( this );
			var formClass = form.attr( 'class' );
			if( formClass === 'wpe-register-form' || formClass === 'wpe-subscribe-form' || formClass === 'wpe-waitlist-form' ) {
				updatedClass  = formClass;	
			}	
		});
		switch( updatedClass ) {
			case 'wpe-register-form':
				var form = $( '#wpe-register-form' );
				break;
			case'wpe-subscribe-form':
				var form = $( '#wpe-subscribe-form' );
				break;
			case'wpe-waitlist-form':
				var form = $( '#wpe-waitlist-form' );
				break;
		}
		var formInput = '';
		switch( updatedClass ) {
			case 'wpe-register-form':
				formInput = $( 'form#wpe-register-form :input' );
				break;
			case'wpe-subscribe-form':
				formInput = $( 'form#wpe-subscribe-form :input' );
				break;
			case'wpe-waitlist-form':
				formInput = $( 'form#wpe-waitlist-form :input' );
				break;
		}
			
		form.submit( function (e) {
			e.preventDefault();
			
			/**
			 * check if any of the field is empty
			 * */
			//variables
			var valueFalse = '';
			formInput.each( function() {
				var input = $( this ); // 
				if ( input.prop( 'required' ) ) {
					$( ".wpe-above-error-field" ).removeClass( 'error' );
					input.parent().removeClass( 'error' );
					input.siblings().css('display', 'none');
					if ( ! input.val() || ( input.is( ':checkbox' ) && ! input.is( ":checked" ) ) ) {
						input.parent().addClass( 'error' ); 
						input.siblings().css('display', 'inline-block');
						valueFalse = 'dont Reload';
					}
				}
				input.parent().removeClass( 'correct-email' );
				input.parent().removeClass( 'correct-phone' );
				input.parent().removeClass( 'correct-zip' );
				if ( input.attr( "id" ) == 'wpe_email' && ! ( input.parent().hasClass( 'correct-email' ) ) ) {
					if ( ! validateEmail( input.val() ) ) {
						input.parent().addClass( 'correct-email' );
						$('.wpe-form-control .wpe-email-error-class').css('display', 'block');
						valueFalse = 'dont Reload';
					}
				}
				if ( input.val() != '' && input.attr( "id" ) == 'wpe_phone' && ! ( input.parent().hasClass( 'error' ) ) ) {
					if ( ! validatePhone( input.val() ) ) {
						input.parent().addClass( 'correct-phone' );
						$('.wpe-form-control .wpe-phone-error-class').css('display', 'block');
						valueFalse = 'dont Reload';
					}
				}
				if ( input.val() != '' && input.attr( "id" ) == 'wpe_zip' && ! ( input.parent().hasClass( 'error' ) ) ) {
					if ( ! validateZip( input.val() ) ) {
						input.parent().addClass( 'correct-zip' );
						input.siblings().css('display', 'block');
						valueFalse = 'dont Reload';
					}
				}
			});

			/**
			 * if any required field is empty stop the execution and show error 
			 * */ 
			if ( valueFalse == 'dont Reload' ) {
				$( ".wpe-above-error-field" ).addClass( 'error' );
				document.getElementById("wpe-error-div").scrollIntoView({ behavior: "smooth" });
				return false;
			}
			if( $( 'span.g-recaptcha' ).text() === 'Captcha not found.' ) {
				submitForm( form );
			} else { 
				if( wpe_ajaxobject.captchaType === 'checkbox' && siteKey !== '' && secretKey !== '' ) {
					if ( wpeValidRecaptcha() ) {
						wpeVerifyCaptcha( form );
					}
				} else if ( wpe_ajaxobject.captchaType === 'invisible' && siteKey !== '' && secretKey !== '' ) {
					grecaptcha.execute();
					setTimeout( function() {
						wpeVerifyCaptcha( form );
					}, 1000);
				}
			}
		});
	}
	function submitForm( submittedForm ) {
		// Serialize the data in the form
		const serializedData = submittedForm.serializeJSON();
		var action = "";
		switch( updatedClass ) {
			case 'wpe-register-form':
				var action = 'wpe_registration_form';
				break;
			case 'wpe-subscribe-form':
				var action = 'wpe_subscribe_form';
				break;
			case 'wpe-waitlist-form':
				var action = 'wpe_waitlist_form';
				break;
		}
		wpe_save_form_data( serializedData, action );
	}
	
	/**
	 * ajax request for handling saving of form data
	 * 
	 * @param {string} serializedData 
	 * @param {string} action 
	 * 
	 * @since 1.2.0
	 */
	function wpe_save_form_data(serializedData, action) {
		jQuery.ajax({
			url: wpe_ajaxobject.ajaxurl,
			type: 'post',
			data: {
				action: action,
				form_data: serializedData
			},

			// pre-request callback function.
			beforeSend: function () {
				$( '#wpe-button' ).attr( 'disabled', true);
				$( '.wpe-button-loader' ).fadeIn();
			},

			// function to be called if the request succeeds.
			success: function (response) {
				$( '#wpe-button' ).attr( 'disabled', false);
				window.location.href = decodeURIComponent(response.url);
				$( '.wpe-button-loader' ).fadeOut();
			}
		})
	}

	//on event single page
	if ( $('body').hasClass('single-wp_events') || $('body').hasClass('post-type-archive-wp_events') ) {
		if( $('.wpe-full-wrap').hasClass('wpe-dark-mode') ) {
			$('body').css( 'background', '#000' );
		}
		if (window.location.href.includes('thankyou')) {
			$('.thankyou-popup').css('display', 'block');
			setTimeout(function () {
				$('.thankyou-popup').fadeOut();
				//clean parameters from url
				var clean_uri = window.location.href.substring(0, window.location.href.indexOf("?"));
				window.history.replaceState({}, document.title, clean_uri);
			}, 3000)
		}
	}

	
	/**
	 * Additional Guests for webinars
	 * */
	const box = '<div class="wpe-col-2 wpe-field guest-box">' + $('.guest-box').html() + '</div>';
	const guest_info = $('.guest-info');
	guest_info.empty();
	$('#event-seats').on('change', function (e) {
		let optionSelected = $("option:selected", this);
		let valueSelected = this.value;
		if (valueSelected > 1) {
			$('.wpe-guests-heading').fadeIn();
			let guest_length = $('.guest-info .guest-box').length;
			if (guest_length >= valueSelected) {
				for (let i = valueSelected; i < guest_length + 1; i++) {
					$(".guest-box").last().remove()
				}
			}
			for (let i = guest_length; i < valueSelected - 1; i++) {
				guest_info.append(box.replaceAll('Guest', 'Guest ' + (i + 1)));
			}
			$('.wpe-guest-field').prop('required', true);
		} else {
			$('.wpe-guests-heading').fadeOut();
			guest_info.empty();
			$('.wpe-guest-field').prop('required', false);
		}
	});

	// on Download ics File click
	$(document).on('click', '#download-ics', function (e) {
		e.preventDefault();
		downloadics();
	});

	/**
	 * On details button click show/hide event description on archive page
	 *
	 * @since 1.0.449
	 * */
	$(document).on('click', '.wpe-detail-button', function () {
		jQuery(this).toggleClass('wpe-active');
		if (jQuery(this).next().hasClass('wpe-display-none')) {
			jQuery(this).next().removeClass('wpe-display-none');
			jQuery(this).parent().addClass('wpe-full-wd');
			jQuery(this).next().fadeIn();
		} else {
			jQuery(this).next().addClass('wpe-display-none');
			jQuery(this).parent().removeClass('wpe-full-wd');
			jQuery(this).next().fadeOut();
		}
	});

	/**
	 * LoadMore Pagination for archives.
	 *
	 * @since 1.5.1
	 * */
	 jQuery( function( $ ) { // use jQuery code inside this to avoid "$ is not defined" error
		$('.wpe_loadmore_btn').click( function() {
	 
			var button = $(this),
				data = {
				'action': 'loadmore',
				'query': wpe_ajaxobject.posts, // that's how we get params from wp_localize_script() function
				'page' : wpe_ajaxobject.current_page
			};
	 
			$.ajax({ // you can also use $.post here
				url : wpe_ajaxobject.ajaxurl, // AJAX handler
				data : data,
				type : 'POST',
				beforeSend : function ( xhr ) {
					button.html('<span class="wpe-button">Loading...</span>'); // change the button text, you can also add a preloader image
				},
				success : function( data ){
					if( data ) { 
						button.html( '<span class="wpe-button">Load More Events</span>' ).prev().before(data); // insert new posts
						wpe_ajaxobject.current_page++;
	 
						if ( wpe_ajaxobject.current_page == wpe_ajaxobject.max_page ) 
							button.remove(); // if last page, remove the button
	 
						// you can also fire the "post-load" event here if you use a plugin that requires it
						// $( document.body ).trigger( 'post-load' );
					} else {
						button.remove(); // if no data, remove the button as well
					}
				}
			});
		});
	} );

	if( $('.wpevents-container').hasClass('wpe-dark-mode') ) {
		$('body').css( 'background', 'black' );
	}
});

function validURL( str ) {
	var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
		'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
		'((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
		'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
		'(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
		'(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
	return !!pattern.test(str);
}

/**
 * Gets text data for ics File and creates it
 * */
function makeTextFile( text ) {
	var textFile = null;
	var data = new Blob([text], { type: 'text/plain' });

	// If we are replacing a previously generated file we need to
	// manually revoke the object URL to avoid memory leaks.
	if (textFile !== null) {
		window.URL.revokeObjectURL(textFile);
	}

	textFile = window.URL.createObjectURL(data);

	return textFile;
}

/**
 * Creates download link for ics File
 * */
function downloadics() {
	textbox = jQuery("div.ics-text").text();
	var link = document.createElement('a');
	link.href = makeTextFile(textbox);
	link.download = jQuery("div.filename").text();
	link.click();
}

//display popup on current page
function wpe_popup( message, image = 0 ) {
	jQuery('body').prepend('<div class="wpe-popup"><div class="popup-inner"><span class="close-btn"></span><p>' + message + '</p></div></div>');
	if ( image != 0 ) {
		jQuery('.popup-inner').prepend( '<img src="' + image + '">' );
	}
	setTimeout(function () {
		jQuery('.wpe-popup').fadeOut();
	}, 3000);
}

function CaptchaExpired() {
	const form = $('form').hasClass('wpe-register-form') ? $('#wpe-register-form') : $('#wpe-subscribe-form');
	grecaptcha.reset();
	if( wpe_ajaxobject.captchaType === 'invisible' ) {
		alert('Captcha Verification Expired.\nPlease fill the form again.');
		form.trigger("reset");
	}
}