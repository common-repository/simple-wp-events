<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       //wpminds.com
 * @since      1.0.0
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/admin
 * @author     WP Minds <support@wpminds.com>
 */
class Wp_Events_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
     * Admin Settings Object
     *
     * @since 1.0.0
     * @access private
     * @var object $admin_settings      The Admin Settings Object
	*/
	private $admin_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_admin_settings();
        $this->load_dependencies();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.449
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name.'-admin', plugin_dir_url( __FILE__ ) . 'css/wp-events-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __DIR__ ) . 'assets/css/wp-events.css', array(), $this->version, 'all' );
        //check if current post type is wp_event then add this css file
        if( 'wp_events' == wpe_get_current_post_type() ) {
		    wp_enqueue_style( $this->plugin_name.'-jquery-ui', plugin_dir_url( __FILE__ ) .'css/jquery-ui.min.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name.'-select2', plugin_dir_url( __FILE__ ) .'css/select2.min.css', array(), $this->version, 'all' );
        }
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.449
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker');
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
        wp_enqueue_script( 'jquery-inputmask', plugin_dir_url( __DIR__ ) . 'assets/js/jquery.inputmask.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'date-validation', plugin_dir_url( __FILE__ ) . 'js/wp-events-date-validation.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-events-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'date-validation' ), $this->version, false );

        wp_enqueue_script( 'jquery-serialize', plugin_dir_url( __DIR__ ) . 'assets/js/jquery.serializejson.js', array( 'jquery' ), $this->version, false );
        
        if( 'wp_events' == wpe_get_current_post_type() ) {
            wp_enqueue_script( 'jquery-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
        }

        //localizing ajax url
		wp_localize_script(
			$this->plugin_name,
			'wpe_ajaxobject',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
                'pluginsUrl'     => plugins_url(),
                'seminarMessage' => wpe_get_seminar_message(),
                'webinarMessage' => wpe_get_webinar_message(),
                'wpeAjaxNonce'   => wp_create_nonce('ajax-nonce'),
                'wpePluginBase'  => WPE_PLUGIN_BASE,
			)
		);
	}

	/**
     * Load Admin Settings Class
     *
     * @since 1.0.0
	*/
	public function load_admin_settings() {
        require_once plugin_dir_path(__FILE__).'includes/class-wp-events-admin-settings.php';
        $this->admin_settings=new Wp_Events_Admin_Settings();

    }

	/**
	 * Load Dependency Files.
     *
     * @since 1.4.3
	 */
	public function load_dependencies() {
        require_once plugin_dir_path(__FILE__).'includes/wp-events-admin-helpers.php';
        require_once plugin_dir_path(__FILE__).'includes/class-wp-events-locations.php';
		require_once plugin_dir_path(__FILE__).'includes/class-wp-events-list-registrations.php';
        require_once plugin_dir_path(__FILE__).'includes/class-wp-events-list-subscribers.php';
        require_once plugin_dir_path(__FILE__).'includes/wp-events-list-entries.php';
        require_once plugin_dir_path(__FILE__).'includes/wp-events-view-edit-entry.php';
        require_once plugin_dir_path(__FILE__).'includes/class-wp-events-admin-requests.php';
        require_once plugin_dir_path(__FILE__).'includes/wp-events-export-events.php';
    }


    /**
     * registering event named post type
     *
     * @since 1.0.0
     */
    public function register_event_post_type() {

        $display_settings = get_option('wpe_settings');
	    $post_name = $display_settings['events_post_name'];
	    $single_name = '';
	    if( substr($post_name, -1) === 's' ) {
		    $single_name = substr($post_name, 0, -1);
        } else {
		    $single_name = $post_name;
        }
	    $post_slug = $display_settings['events_slug'];
        $post_name = apply_filters('wp_events_post_menu_label',$post_name);
        $labels = array(
            'name'               => $post_name,
            'singular_name'      => $single_name,
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New '.$single_name,
            'edit_item'          => 'Edit '.$single_name,
            'new_item'           => 'New '.$single_name,
            'view_item'          => 'View '.$single_name,
            'search_items'       => 'Search '.$single_name,
            'not_found'          => 'No '.$single_name.' found',
            'not_found_in_trash' => 'No '.$single_name.' found in Trash',
            'menu_name'          => $post_name,
        );

        if( $post_slug === '' ) {
	        $post_slug = 'events';
        }
        $archive = true;
        $option = get_option( 'wpe_display_settings' );
        if ( isset( $option['disable_archive'] ) ) {
        	$archive = false;
        }
		
        $args = array(
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail','excerpt','comments' ),
            'public'              => true,
            'show_in_rest'        => true,
            'exclude_from_search' => false,
            'has_archive'         => $archive,
            'can_export'          => true,
            'rewrite'             => array( 'slug' => $post_slug, 'with_front' => FALSE ),
            'menu_icon'           => 'dashicons-calendar-alt', // https://developer.wordpress.org/resource/dashicons/
        );

        register_post_type( 'wp_events', $args );
    }

	/**
	 * registering custom metaboxes for storing additional details.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_metaboxes_for_details() {
		/**
		 * Event fields for Event related information
		 */

		$general_option = get_option( 'wpe_settings' );
		$post_name      = $general_option['events_post_name'];
		add_meta_box( 'wpe_event_settings', $post_name . ' Settings',
            array($this,'wpe_event_settings_meta_box_callback'),
			'wp_events',
			'advanced',
            'high'
		);

        add_meta_box( 'wpevent_fields', $post_name . ' Details',
            array($this,'wpevent_fields_meta_box_callback'),
			'wp_events',
			'advanced',
            'high'
		);

		add_meta_box( 'wpevent_email_notification', 'Contact Person',
            array($this,'wpevent_notification_meta_box_callback'),
			'wp_events',
			'side'
		);

        add_meta_box( 'wpevent_reminder_notification', 'Event Reminder',
            array($this,'wpevent_reminder_meta_box_callback'),
			'wp_events',
			'side'
		);

        if ( $post_name[strlen( $post_name ) - 1 ] === 's' ) {
            $single_post = substr( $post_name, 0, -1 );
        }

        add_meta_box( 'wpevent_duplicate_event', 'Duplicate ' . $single_post,
            array($this,'wpevent_duplication_meta_box_callback'),
			'wp_events',
			'side',
            'high'
		);
	}

	/**
     * Meta box callback for wpe_event_settings
     *
     * @param $post
     *
     * @since 1.4.0
     */
    public function wpe_event_settings_meta_box_callback( $post ) {
        wp_nonce_field( 'wpe_event_settings_nonce', 'wpe_event_settings_nonce' );

        ?>
        <div class="wpevents-main">
            <div class="wpe-event-settings close-reg wp-event-subsection">
                <div class="wpe-settings-title wp-event-section-title"><p><?php _e( 'Close Registrations', 'simple-wp-events' ); ?></p></div>
                <div class="close-reg event-control">
                    <?php $checkbox_meta = get_post_meta( $post->ID, 'wpevent-close-reg', true ); ?>
                    <label for="wpevent-close-reg" class="wpe-checkbox">
                    <input name="wpevent-close-reg" id="wpevent-close-reg" value="yes" type="checkbox" <?php echo $checkbox_meta === 'yes' ? 'checked' : '' ?> />
                    <span class="slider round"></span>
                    </label>
                    <small>Error Message</small>
                </div>
            </div>
            <div class="wpe-event-settings wpe-hide-archive wp-event-subsection">
                <div class="wpe-settings-title wp-event-section-title"><p>Hide From Archive</p></div>
                <div class="wpe-hide-archive event-control">
                    <?php $checkbox2_meta = get_post_meta( $post->ID, 'wpevent-hide-archive', true ); ?>
                    <label for="wpevent-hide-archive" class="wpe-checkbox">
                    <input name="wpevent-hide-archive" id="wpevent-hide-archive" value="yes" type="checkbox" <?php echo $checkbox2_meta === 'yes' ? 'checked' : '' ?> />
                    <span class="slider round"></span>
                    </label>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
            </div>
            <div class="wpe-event-settings wpe-limit-seats wp-event-subsection">
                <div class="wpe-settings-title wp-event-section-title"><p><?php _e( 'Seats per Registration', 'simple-wp-events' ); ?></p></div>
                <div class="event-control wpe-left">
                    <input id="wpevent-limit-seats" class="wp-event-field" type="number" min="1" max="10" name="wpevent-limit-seats" value="<?php echo get_post_meta( $post->ID, 'wpevent-limit-seats', true ); ?>"/>
                </div>
            </div>

        </div>
        <?php

    }

    /**
     * Meta box callback for wpevents_fields
     *
     * @param $post
     *
     * @since 1.0.2
     */
    public function wpevent_fields_meta_box_callback( $post ) {
        wp_nonce_field( 'wpevents_fields_nonce', 'wpevents_fields_nonce' );

        ?>
        <div class="wpevents-main">
            <div class="wp-event-subsection wpe-event-type">
                <div class="wp-event-section-title"><p><?php _e( 'Event Type', 'simple-wp-events' ); ?></p></div>
                <div class="event-type event-control wpe-left">
                <?php
                $type = get_post_meta( $post->ID, 'wpevent-type', true );
                ?>
                    <label for="event-type"><?php _e( 'Select Event Type Below', 'simple-wp-events' ); ?></label>
                    <select class="wp-event-field wpe-form-control" id="event-type" name="wpevent-type">
                        <option <?php  if( $type === 'seminar' ) echo 'selected'; ?> value="seminar"><?php _e( 'Seminar', 'simple-wp-events' ); ?></option>
                        <option <?php  if( $type === 'webinar' ) echo 'selected'; ?> value="webinar"><?php _e( 'Webinar (Online Event)', 'simple-wp-events' ); ?></option>
                    </select>
                </div>
            </div>

            <div class="wp-event-section-title"><p><?php _e( 'Time & Date', 'simple-wp-events' ); ?></p></div>
            <div class="wpevent-time-date wp-event-subsection">
	            <?php
                $event_date_time = wpevent_date_time( $post->ID );
	            ?>
            <div class="start-date event-control wpe-left">
                <label for="wpevent-start-date"><?php _e( 'Start Date*', 'simple-wp-events' ); ?></label>
                <input id="wpevent-start-date" class="wpevent-start-date wp-event-datepicker wp-event-field" type="text" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" title="YYYY-MM-DD" name="wpevent-start-date" value="<?php echo isset( $event_date_time['start_date'] ) ? esc_attr( $event_date_time['start_date'] ) : '' ;?>" autocomplete="off" required/>
                <small><?php _e( 'This field is required', 'simple-wp-events' ); ?></small>
            </div>

            <div class="end-date event-control wpe-right">
                <label for="wpevent-end-date"><?php _e( 'End Date*', 'simple-wp-events' ); ?></label>
                <input id="wpevent-end-date" class="wpevent-end-date wp-event-datepicker wp-event-field" type="text" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" title="YYYY-MM-DD" name="wpevent-end-date" value="<?php echo isset( $event_date_time['end_date'] ) ? esc_attr( $event_date_time['end_date'] ) : '' ;?>" autocomplete="off" required/>
                <small><?php _e( 'This field is required', 'simple-wp-events' ); ?></small>
            </div>

            <div class="start-time event-control wpe-left">
                <label for="wpevent-start-time"><?php _e( 'Start Time', 'simple-wp-events' ); ?></label>
                <input id="wpevent-start-time" class="wpevent-start-time wp-event-field timepicker" type="time" name="wpevent-start-time" value="<?php echo isset( $event_date_time['start_time'] ) ? esc_attr( $event_date_time['start_time'] ) : '00:00';?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>

            <div class="end-time event-control wpe-right">
                <label for="wpevent-end-time"><?php _e( 'End Time', 'simple-wp-events' ); ?></label>
                <input id="wpevent-end-time" class="wpevent-end-time wp-event-field timepicker" type="time" name="wpevent-end-time" value="<?php echo isset( $event_date_time['end_time'] ) ? esc_attr( $event_date_time['end_time'] ) : '23:59' ;?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>

            <div class="all-day event-control wpe-left">
                <div class="wpe-settings-title wp-event-section-title"><p><?php _e( 'All Day', 'simple-wp-events' ); ?></p></div>
                <?php $checkbox4_meta = get_post_meta( $post->ID, 'wpevent-all-day', true ); ?>
                <label for="wpevent-all-day" class="wpe-checkbox">
                <input name="wpevent-all-day" id="wpevent-all-day" value="yes" type="checkbox" <?php echo $checkbox4_meta === 'yes' ? 'checked' : '' ?> />
                <span class="slider round"></span>
                </label>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="end-time event-control wpe-right">
                <div class="wpe-settings-title wp-event-section-title"><p><?php _e( 'No End Time', 'simple-wp-events' ); ?></p></div>
                <?php $checkbox3_meta = get_post_meta( $post->ID, 'wpevent-no-endtime', true ); ?>
                <label for="wpevent-no-endtime" class="wpe-checkbox">
                <input name="wpevent-no-endtime" id="wpevent-no-endtime" value="yes" type="checkbox" <?php echo $checkbox3_meta === 'yes' ? 'checked' : '' ?> />
                <span class="slider round"></span>
                </label>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>

        </div>
        <div <?php if( $type === 'webinar' ) echo 'style="display:none;"'; ?>  class="wp-events-location wp-event-subsection">
            <div class="location event-control wpe-left">
                <label for="wpevent-location"><?php _e( 'Location', 'simple-wp-events' ); ?></label>
                <?php wpevents_location_drop_down(get_post_meta( $post->ID, 'wpevent-location', true ), 'wpevent-location' );?>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="wpe-add-location event-control wpe-right">
                <button id="wpe-location-btn" class="wpe-location-btn components-button is-primary"><?php _e( 'Add New Location', 'simple-wp-events' ); ?></button>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="wpe-location-fields" <?php echo 'style="display:none;"'; ?>>
                <div class="venue event-control wpe-left">
                    <label for="wpevent-venue"><?php _e( 'Venue', 'simple-wp-events' ); ?></label>
                    <input  id="wpevent-venue" class="wp-event-field wpe-location-field" type="text" name="wpevent-venue" value="<?php echo get_post_meta( $post->ID, 'wpevent-venue', true );?>"/>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
                <div class="address event-control wpe-right">
                    <label for="wpevent-address"><?php _e( 'Address', 'simple-wp-events' ); ?></label>
                    <input id="wpevent-address" class="wp-event-field wpe-location-field" type="text" name="wpevent-address" value="<?php echo get_post_meta( $post->ID, 'wpevent-address', true );?>"/>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
                <div class="countery event-control wpe-left">
                    <label for="country"><?php _e( 'Country', 'simple-wp-events' ); ?></label>
                    <?php wpevents_country_drop_down(get_post_meta( $post->ID, 'wpevent-country', true ), 'wpevent-country' );?>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
                <div class="city event-control wpe-right">
                    <label for="wpevent-city"><?php _e( 'City', 'simple-wp-events' ); ?></label>
                    <input id="wpevent-city" class="wp-event-field wpe-location-field" type="text" name="wpevent-city" value="<?php echo get_post_meta( $post->ID, 'wpevent-city', true );?>"/>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
                <div class="state event-control wpe-left">
                    <label for="wpevent-state"><?php _e( 'State', 'simple-wp-events' ); ?></label>
                    <input id="wpevent-state" class="wp-event-field wpe-location-field" type="text" name="wpevent-state" value="<?php echo get_post_meta( $post->ID, 'wpevent-state', true );?>"/>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
                <div class="zip event-control wpe-right">
                    <label for="wpevent-zip"><?php _e( 'Zip', 'simple-wp-events' ); ?></label>
                    <input id="wpevent-zip" class="wp-event-field wpe-location-field" type="text" name="wpevent-zip" value="<?php echo get_post_meta( $post->ID, 'wpevent-zip', true );?>"/>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
                <div class="wpe-save-location event-control wpe-left">
                    <button id="wpe-save-location" class="wpe-location-btn components-button is-primary"><?php _e( 'Save Location', 'simple-wp-events' ); ?></button>
                    <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
                </div>
            </div>
        </div>
        <div class="wp-events-additional wp-event-subsection">
            <div class="wp-event-section-title"><p><?php _e( 'Additonal Information', 'simple-wp-events' ); ?></p></div>
            <div class="phone event-control wpe-left" id="event-control">
                <label for="wpevent-phone"><?php _e( 'Phone', 'simple-wp-events' ); ?></label>
                <input id="wpevent-phone" title="(123) 111-1234" class="wp-event-field" type="tel" name="wpevent-phone" value="<?php echo get_post_meta( $post->ID, 'wpevent-phone', true );?>"/>
                <small><?php _e( 'Please enter number in correct format (xxx) xxx-xxxx.', 'simple-wp-events' ); ?></small>
            </div>
            <div class="external-url event-control wpe-right">
                <label for="wpevent-external-url"><?php _e( 'External URL', 'simple-wp-events' ); ?></label>
                <input id="wpevent-external-url" class="wp-event-field" type="url" name="wpevent-external-url" value="<?php echo get_post_meta( $post->ID, 'wpevent-external-url', true );?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="seats event-control wpe-left">
                <label for="wpevent-seats"><?php _e( 'Total Seats', 'simple-wp-events' ); ?></label>
                <input id="wpevent-seats" class="wp-event-field" type="number" min="1" step="1" name="wpevent-seats" value="<?php echo esc_attr( wpe_get_total_seats( $post->ID ) ); ?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="seats event-control wpe-right">
                <label for="wpevent-remaining-seats"><?php _e( 'Remaining Seats', 'simple-wp-events' ); ?></label>
                <input id="wpevent-remaining-seats" class="wp-event-field" type="number" name="wpevent-remaining-seats" value="<?php echo absint( wpe_get_remaining_seats( $post->ID ) ); ?>" readonly/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div <?php if( $type === 'webinar' ) echo 'style="display:none;"'; ?>  class="wpe-map-div map event-control wpe-left">
                <label for="wpevent-map-url"><?php _e( 'Map URL', 'simple-wp-events' ); ?></label>
                <input id="wpevent-map-url" class="wp-event-field" type="url" name="wpevent-map-url" value="<?php echo get_post_meta( $post->ID, 'wpevent-map-url', true );?>"/>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="wpe-thankyou-div event-control <?php echo $type == 'webinar' ? 'wpe-left' : 'wpe-right'; ?>">
                <label for="wpevent-ty-url"><?php _e( 'Thankyou Page', 'simple-wp-events' ); ?></label>
                <select class="wp-event-field" id="wpevent-ty-url" name="wpevent-ty-url">
                    <?php
                    $options = wpe_get_all_pages();
                    $options = array_merge( ['Select Page'], $options );
                    foreach ( $options as $page ) {
                        if( $page == get_post_meta( $post->ID, 'wpevent-ty-url', TRUE ) ) {
                            ?> <option selected value="<?php echo $page; ?>"><?php echo get_the_title( $page ); ?></option> <?php
                        } else if( $page == "Select Page" ) {
                            ?> <option value=""><?php echo 'Select Page'; ?></option> <?php
                        } else {
                            ?> <option value="<?php echo $page; ?>"><?php echo get_the_title( $page ); ?></option> <?php
                        }
                    }
                    ?>
                </select>
                <small><?php _e( 'Error Message', 'simple-wp-events' ); ?></small>
            </div>
            <div class="confirmation_message event-control">
                <label class="confirmation-message-label"><?php _e( 'Confirmation Mail Message', 'simple-wp-events' ); ?></label>
                    <?php
                        $confirmation_me_meta = get_post_meta( $post->ID, 'wpevent-confirmation-message', TRUE );
                        if ( ! $confirmation_me_meta ) {
                            $confirmation_me_meta = $this->get_confirmation_message( $post->ID, $type );
                        };
                        echo wpe_editor( $confirmation_me_meta ,'wpevent-confirmation-message', 'wpevent-confirmation_message' );
					?> 
            </div>
        </div>

        </div>
        <?php

    }

	/**
	 * Email Notification drop down callback
	 *
	 * @param $post
	 *
	 * @since 1.0.438
	 */
	public function wpevent_notification_meta_box_callback( $post ) {
		$notified_user     = get_post_meta( $post->ID, 'wpevent-email-notification', TRUE );
		$wpe_firm_settings = get_option( 'wpe_firm_settings' );
		$all_users         = get_users();

		$is_selected = FALSE;
		if ( $notified_user === '' || $notified_user === FALSE ) {
			$notified_user = isset( $wpe_firm_settings['admin_mail'] ) ? $wpe_firm_settings['admin_mail'] : 'Default';
		}

		echo '<p>Selected user will receive email notification when someone registers to the seminar</p>';
		echo '<select name="wpe_event_notification" class="wpe-contact-person">';
		foreach ( $all_users as $user ) {
			if ( $notified_user === $user->user_email ) {
				$is_selected = TRUE;
				echo '<option selected="selected" value="' . esc_attr( $user->user_email ) . '">' . esc_html( $user->display_name ) . '</option>';
			} else {
				echo '<option value="' . esc_attr( $user->user_email ) . '">' . esc_html( $user->display_name ) . '</option>';
			}
		}
		if ( $is_selected === FALSE && $notified_user === 'Default' ) {
			echo '<option selected="selected" value="">Select a Contact Person</option>';
		} elseif ( $is_selected === FALSE && isset( $wpe_firm_settings['admin_mail'] ) ) {
			echo '<option selected="selected" value="' . esc_attr( $wpe_firm_settings['admin_mail'] ) . '">' . esc_html( $wpe_firm_settings['admin_mail'] ) . '</option>';
		} elseif ( isset( $wpe_firm_settings['admin_mail'] ) ) {
			echo '<option value="' . esc_attr( $wpe_firm_settings['admin_mail'] ) . '">' . esc_html( $wpe_firm_settings['admin_mail'] ) . '</option>';
		}
		echo '</select>';
	}

    /**
	 * Event Reminder button callback
	 *
	 * @param $post
	 *
	 * @since 1.7.6
	 */
	public function wpevent_reminder_meta_box_callback( $post ) {
        echo '<button title="Event Reminder" class="wpe-btn wpe-post-'. $post->ID .'" id="wpe-event-reminder">Send Reminder</button>';
    }

    /**
	 * Duplicate Event link callback
	 *
	 * @param $post
	 *
	 * @since 1.4.0
	 */
	public function wpevent_duplication_meta_box_callback( $post ) {

        if( ! current_user_can( 'edit_posts' ) ) {
            return;
        }
    
        $url = wp_nonce_url(
            add_query_arg(
                array(
                    'action' => 'wpe_duplicate_post_as_draft',
                    'post'   => $post->ID,
                ),
                'admin.php'
            ),
            basename(__FILE__),
            'duplicate_nonce'
        );
    
        echo '<a href="' . esc_url( $url ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';

    }

	/**
	 * Returns confirmation message for single events
	 *
	 * returns value from settings if not saved manually
	 *
	 * @param  $post_id
	 *
	 * @return string
	 */
	private function get_confirmation_message( $post_id, $type ) {
		$option = get_option('wpe_mail_settings');
        if( $type === 'webinar' ) {
            return wpautop( $option['webinar_success_message'] );
        }
		return wpautop( $option['mail_success_message'] );
	}

	/**
	 * saving metabox data in post meta
	 * hooked to save_post
	 *
	 * @param $post_id
	 *
	 * @since 1.0.2
	 */
    public function wpevents_save_meta_box($post_id) {

	    // Return if we're doing an auto save
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		    return;
        }

        //nonce validation
        if ( ! wp_verify_nonce( filter_input(INPUT_POST, 'wpevents_fields_nonce'), 'wpevents_fields_nonce' ) ) {
            return;
        }

        //if post type is wp_events
        if( filter_input(INPUT_POST, 'post_type') !== "wp_events" ) {
            return;
        }

        //time&date
	    $wpevent_start_date_time = filter_input( INPUT_POST, 'wpevent-start-date' ) . ' ' . filter_input( INPUT_POST, 'wpevent-start-time' );
	    $wpevent_end_date_time   = filter_input( INPUT_POST, 'wpevent-end-date' ) . ' ' . filter_input( INPUT_POST, 'wpevent-end-time' );
	    $wp_event_type           = filter_input( INPUT_POST, 'wpevent-type' );

        if( isset( $wp_event_type ) && $wp_event_type === 'seminar' ) {
	        //location
	        $wp_event_address  = filter_input( INPUT_POST, 'wpevent-address', FILTER_SANITIZE_STRING );
	        $wp_event_city     = filter_input( INPUT_POST, 'wpevent-city', FILTER_SANITIZE_STRING );
	        $wp_event_zip      = filter_input( INPUT_POST, 'wpevent-zip', FILTER_SANITIZE_NUMBER_INT );
	        $wp_event_state    = filter_input( INPUT_POST, 'wpevent-state', FILTER_SANITIZE_STRING );
	        $wp_event_venue    = filter_input( INPUT_POST, 'wpevent-venue', FILTER_SANITIZE_STRING );
	        $wp_event_country  = filter_input( INPUT_POST, 'wpevent-country', FILTER_SANITIZE_STRING );
	        $wp_event_location = filter_input( INPUT_POST, 'wpevent-location', FILTER_SANITIZE_STRING );

	        update_post_meta( $post_id, "wpevent-address", $wp_event_address );
	        update_post_meta( $post_id, "wpevent-city", $wp_event_city );
	        update_post_meta( $post_id, "wpevent-zip", $wp_event_zip );
	        update_post_meta( $post_id, "wpevent-state", $wp_event_state );
	        update_post_meta( $post_id, "wpevent-venue", $wp_event_venue );
	        update_post_meta( $post_id, "wpevent-country", $wp_event_country );
	        update_post_meta( $post_id, "wpevent-location", $wp_event_location );

        }

        //additional info
        $wp_event_phone         = filter_input(INPUT_POST,'wpevent-phone');
        $wp_event_website       = filter_input(INPUT_POST,'wpevent-external-url');
        $wp_event_seats         = filter_input(INPUT_POST,'wpevent-seats');
        $wp_event_map_url       = filter_input(INPUT_POST,'wpevent-map-url');
        $wp_event_confirmation  = filter_input(INPUT_POST,'wpevent-confirmation_message');
        $wp_event_typage        = filter_input( INPUT_POST, 'wpevent-ty-url', FILTER_SANITIZE_STRING );

        //event notification
	    $wp_event_notification  = filter_input(INPUT_POST,'wpe_event_notification');

        //event settings
        $wp_event_close_reg     = filter_input(INPUT_POST,'wpevent-close-reg');
	    $wpe_hide_in_archive    = filter_input(INPUT_POST,'wpevent-hide-archive');
	    $wpe_limit_seats        = filter_input(INPUT_POST,'wpevent-limit-seats');
        $wpe_no_endtime         = filter_input(INPUT_POST,'wpevent-no-endtime');
        $wpe_all_day            = filter_input(INPUT_POST,'wpevent-all-day');


	    //event type
	    update_post_meta( $post_id, "wpevent-type", $wp_event_type );

	    // Time & Date
	    update_post_meta( $post_id, "wpevent-start-date-time", strtotime( $wpevent_start_date_time ) );
	    update_post_meta( $post_id, "wpevent-end-date-time", strtotime( $wpevent_end_date_time ) );

        //additional information
        update_post_meta( $post_id, "wpevent-phone", $wp_event_phone );
        update_post_meta( $post_id, "wpevent-external-url", $wp_event_website );
        update_post_meta( $post_id, "wpevent-seats", $wp_event_seats );
        update_post_meta( $post_id, "wpevent-map-url", $wp_event_map_url );
        update_post_meta( $post_id, "wpevent-confirmation-message", $wp_event_confirmation );
        update_post_meta( $post_id, "wpevent-ty-url", $wp_event_typage );
        update_post_meta( $post_id, "wpevent-email-notification", $wp_event_notification );
        update_post_meta( $post_id, "wpevent-close-reg", $wp_event_close_reg );
        update_post_meta( $post_id, "wpevent-hide-archive", $wpe_hide_in_archive );
        update_post_meta( $post_id, "wpevent-no-endtime", $wpe_no_endtime );
        update_post_meta( $post_id, "wpevent-all-day", $wpe_all_day );
        update_post_meta( $post_id, "wpevent-limit-seats", $wpe_limit_seats );

        if( ! empty( $wp_event_website ) ) {
            add_action( 'wpseo_saved_postdata', function() use ( $post_id ) { 
                update_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', '1' );
            }, 999 );
        } else {
            add_action( 'wpseo_saved_postdata', function() use ( $post_id ) { 
                update_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', '2' );
            }, 999 );
        }

    }

    /**
     * Registering Custom Taxonomy for post_type wp_events
     *
     * @since 1.0.0
    */
    public function wpevents_category() {

        $labels = array(
            'name'          => _x( 'Categories', 'taxonomy general name', 'simple-wp-events' ),
            'singular_name' => _x( 'Category', 'taxonomy singular name', 'simple-wp-events' ),
            'search_items'  => __( 'Search Categories', 'simple-wp-events' ),
            'all_items'     => __( 'All Categories', 'simple-wp-events' ),
            'edit_item'     => __( 'Edit Category', 'simple-wp-events' ),
            'update_item'   => __( 'Update Category', 'simple-wp-events' ),
            'add_new_item'  => __( 'Add New Category', 'simple-wp-events' ),
            'new_item_name' => __( 'New Category', 'simple-wp-events' ),
            'menu_name'     => __( 'Categories', 'simple-wp-events' ),
        );

        $args = array(
            'labels'        => $labels,
            'show_ui'       => true,
            'show_in_rest'  => true,
            'hierarchical'  => true,
            'rewrite'       => [ 'slug'  => 'wpevents-category', 'hierarchical' => true, 'with_front' => FALSE ]
        );

        register_taxonomy( 'wpevents-category', ['wp_events'], $args );
    }


	/**
	 * Change post status "Publish" to "Future Events"
	 *
	 * @param $views
	 *
	 * @return mixed
	 */

	public function change_publish_status_text( $views ) {
	    unset($views['all']);
	    unset($views['publish']);

        $wpe_current_view = '';

        if ( ! isset( $_GET['post_status'] ) ) {
            $wpe_current_view = isset( $_GET['event_status'] ) ? wpe_sanitize( $_GET['event_status'] ) : 'all'; 
        }

		$updated['all']     = '<a '. wpe_is_current( $wpe_current_view, 'all' ) .'href="edit.php?post_type=wp_events">All <span class="count">(' . wpe_get_posts_count() .')</span></a>';
		$updated['future']  = '<a '. wpe_is_current( $wpe_current_view, 'future' ) .'href="edit.php?post_type=wp_events&#038;event_status=future">Future <span class="count">(' . wpe_get_posts_count('future') .')</span></a>';
		$updated['past']    = '<a '. wpe_is_current( $wpe_current_view, 'past' ) .'href="edit.php?post_type=wp_events&#038;event_status=past">Past <span class="count">(' . wpe_get_posts_count('past') .')</span></a>';
		$updated['ongoing'] = '<a '. wpe_is_current( $wpe_current_view, 'ongoing' ) .'href="edit.php?post_type=wp_events&#038;event_status=ongoing">Ongoing <span class="count">(' . wpe_get_posts_count('ongoing') .')</span></a>';
		return array_merge( $updated, $views);
	}

	/**
	 * sets meta query based on event status
	 *
	 * Sets meta query based on post_status
	 *
	 * @param $query
	 *
	 * @return mixed
	 * @since 1.0.3
	 */
	function wpevents_post_status_param( $query ) {
		if ( is_admin() && $query->is_main_query() && get_current_screen()->id == 'edit-wp_events' ) {

			if ( filter_input( INPUT_GET, 'event_status' ) === NULL ) {
				return $query;
			}

			if ( filter_input( INPUT_GET, 'event_status' ) === 'past' ) {
				$query->set( 'meta_query', [
					[
						'key'     => 'wpevent-end-date-time',
						'compare' => '<',
						'value'   => strtotime( current_time( 'mysql' ) ),
						'type'    => 'numeric',
					],
				] );
				$query->set( 'post_status', 'publish' );
			} else if ( filter_input( INPUT_GET, 'event_status' ) === 'future' ) {
				$query->set( 'meta_query', [
					[
						'key'     => 'wpevent-start-date-time',
						'compare' => '>',
						'value'   => strtotime( current_time( 'mysql' ) ),
						'type'    => 'numeric',
					],
				] );
				$query->set( 'post_status', 'publish' );
			} else if ( filter_input( INPUT_GET, 'event_status' ) === 'ongoing' ) {
				$query->set( 'meta_query', [
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
				] );
				$query->set( 'post_status', 'publish' );
			}
		}
	}

	/**
	 * Settings page for wp_events
     *
     * @since 1.0.51
    */
    public function wpevents_submenu_page() {

	    global $wpe_entries_page;

	    $wpe_entries_page = add_submenu_page(
		    'edit.php?post_type=wp_events',
		    'WP Events Entries', /*page title*/
		    'Entries', /*menu title*/
		    'manage_options', /*roles and capabilities*/
		    'wp_forms_entries',
		    array($this,'wpevents_form_entries_page') /*callback function*/
	    );

	    add_action( 'load-'.$wpe_entries_page, array( $this, 'wpe_load_screen_options_for_entries' ) );

	    add_submenu_page(
            'edit.php?post_type=wp_events',
            'WP Events Settings', /*page title*/
            'Settings', /*menu title*/
            'manage_options', /*roles and capabilities*/
            'wp_events_settings',
            array($this,'wpevents_settings_page') /*callback function*/
        );

        add_submenu_page(
            null,
            'WP Events View/Edit Entry', /*page title*/
            'View/Edit Entry', /*menu title*/
            'manage_options', /*roles and capabilities*/
            'wpe_view_entry',
            array($this,'wpevents_view_entry_page') /*callback function*/
        );

    }

	/**
	 * Load screen options callback for entries page
	 *
	 * @since 1.0.51
	 */
	public function wpe_load_screen_options_for_entries() {

		global $wpe_entries_page;

		$screen = get_current_screen();

		// get out of here if we are not on our entries page
		if ( ! is_object( $screen ) || $screen->id != $wpe_entries_page ) {
			return;
		}

		$arguments = [
			'label'   => __( 'Entries Per Page', 'simple-wp-events' ),
			'default' => 20,
			'option'  => 'wpe_entries_per_page',
		];
		add_screen_option( 'per_page', $arguments );
	}

    /**
     * Adds filter by type dropdown to events admin dashboard
     *
     * @since 1.4.0
     */
    public function restrict_events_by_type() {
        global $typenow;
        $type = isset( $_GET['wp_events_type'] ) ? wpe_sanitize( $_GET['wp_events_type'] ) : 'all';
        if ( $typenow == 'wp_events' ) {
            ?>
            <select id="wp_events_type" name="wp_events_type">
                <option <?php  if( $type === 'all' ) echo 'selected'; ?> value="all"><?php _e( 'All Types', 'simple-wp-events' ); ?></option>
                <option <?php  if( $type === 'seminar' ) echo 'selected'; ?> value="seminar"><?php _e( 'Seminar', 'simple-wp-events' ); ?></option>
                <option <?php  if( $type === 'webinar' ) echo 'selected'; ?> value="webinar"><?php _e( 'Webinar (Online Event)', 'simple-wp-events' ); ?></option>
            </select>
            <?php
        }
    }

    /**
     * Filters events list by type dropdown
     *
     * @param $query
     * 
     * @since 1.4.0
     */
    public function wpe_filter_by_type( $query ) {
        global $pagenow;
        // Get the post type
        $post_type = isset( $_GET['post_type'] ) ? wpe_sanitize( $_GET['post_type'] ) : '';
        if ( is_admin() && $pagenow =='edit.php' && $post_type == 'wp_events' && isset( $_GET['wp_events_type'] ) && $_GET['wp_events_type'] !== 'all' ) {
          $query->query_vars['meta_key'] = 'wpevent-type';
          $query->query_vars['meta_value'] = wpe_sanitize( $_GET['wp_events_type'] );
          $query->query_vars['meta_compare'] = '=';
        }
    }

	/**
	 * Saving Screen Options
	 *
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 * @since 1.0.51
	 */
	function wpe_set_screen_option( $status, $option, $value ) {
		if ( 'wpe_entries_per_page' == $option ) {
			return $value;
		}
	}

	/**
     * wp_events post type columns
     *
     * @since 1.2.0
     */
    public function wpevents_post_type_columns() {
        $label      = get_option( 'wpe_settings' );
        $post_name  = esc_attr( $label['events_post_name'] );
        return array(
            'cb'              => '<input type="checkbox" />',
            'title'           => $post_name . __( ' Title', 'simple-wp-events' ),
            'author'          => __( 'Author', 'simple-wp-events' ),
            'category'        => $post_name . __(' Category', 'simple-wp-events' ),
            'event_date'      => $post_name . __( ' Date', 'simple-wp-events' ),
			'wpe_seats'       => __( 'Remaining | Total<br>Seats', 'simple-wp-events' ),
            'wpe_status'      => $post_name . __( ' Status', 'simple-wp-events' ),
            'date'            => __( 'Date', 'simple-wp-events' ),
            'start_date'      => $post_name . __( ' start date', 'simple-wp-events' ),
            'start_time'      => $post_name . __( ' start time', 'simple-wp-events' ),
            'end_date'        => $post_name . __( ' end date', 'simple-wp-events' ),
            'end_time'        => $post_name . __( ' end time', 'simple-wp-events' ),
        );
    }

    /**
     * Fill wp_events custom columns
     *
     * @param $column
     * @param $post_id
     *
     * @since 1.2.0
     */
    public function wpevents_fill_post_type_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'author':
                    echo get_the_author( $post_id ) ;
                break;
            case 'category':
                    $this->post_type_categpry_column( $post_id ) ;
                break;
            case 'event_date':
	            echo wpe_get_event_dates( $post_id );
                break;
            case 'start_time':
                $start_time = get_post_meta( $post_id, 'wpevent-start-date-time', true );
                if( $start_time !== '' ) {
                    echo date( 'H:i', $start_time );
                }
                break;
            case 'start_date':
                $start_date = get_post_meta( $post_id, 'wpevent-start-date-time', true );
                if( $start_date !== '' ) {
                    echo date( 'Y-m-d', $start_date );
                }
                break;
            case 'end_date':
	            $end_date = get_post_meta( $post_id, 'wpevent-end-date-time', true );
	            if( $end_date !== '' ) {
		            echo date( 'Y-m-d', $end_date );
	            }
                break;
            case 'end_time':
                $end_time = get_post_meta( $post_id, 'wpevent-end-date-time', true );
                if( $end_time !== '' ) {
                    echo date( 'H:i', $end_time );
                }
                break;
			case 'wpe_seats':
				echo esc_html( wpe_get_remaining_seats( $post_id ) . ' | ' . wpe_get_total_seats( $post_id ) );
                break;
            case 'wpe_status':
                echo esc_html( wpevent_event_status ( $post_id ) );
                break;
        }
    }

	/**
	 * Making Custom Columns Sortable
	 *
	 * @param $columns
	 *
	 * @return mixed
     *
     * @since 1.0.43
	 */
	public function wpevent_custom_sortable_columns( $columns ) {
		$columns['event_date'] = 'event_date';
		return $columns;
	}

    /**
     * Displays Custom taxonomies in wp_events category column
     *
     * @param $post_id
     * @since 1.0.0
     */
    private function post_type_categpry_column( $post_id ) {
        $post_type = 'wp_events';
        $terms = wp_get_object_terms( $post_id, 'wpevents-category');
        if( !empty( $terms ) ) {
            $output = [];
            foreach ( $terms as $term ) {
                $output[]='<a href="' . admin_url( 'edit.php?' . 'taxonomy' . '='.  $term->taxonomy . '&tag_ID='. $term->term_id .'&post_type=' . $post_type ) . '">' . $term->name . '</a>';
            }
            if( isset( $output ) ) {
                echo implode(', ', wpe_escape_html( $output ) );
            }
        } else {
            _e( 'Uncategorized', 'simple-wp-events' );
        }
    }

    /**
     * Display date and time fields in quick-edit section
     *
     * @param $column_name
     * @param $post_type
     * 
     * @since 1.2.0
     */
    public function wpe_quick_edit_fields( $column_name, $post_type ) {
    
        if ( ! $post_type == 'wp_events') {
            return;
        }
    
        switch( $column_name ) :
            case 'start_date': {

                wp_nonce_field( 'wpe_q_edit_nonce', 'wpe_nonce' );
    
                echo '<fieldset class="inline-edit-col-right date-time-metaboxes">
                    <div class="inline-edit-col">
                        <div class="inline-edit-group wp-clearfix">';
                echo '<label>
                        <span class="title">Start Date</span>
                        <span class="input-text-wrap">
                        <input id="quickedit-start-date" class="wpevent-start-date" type="date" name="wpevent-quickedit-start-date" value="">
                        </span>
                    </label>';
    
                break;
            }
            case 'start_time': {
    
                echo '<label>
                        <span class="title">Start Time</span>
                        <span class="input-text-wrap">
                        <input id="quickedit-start-time" class="wpevent-start-time timepicker" type="time" name="wpevent-quickedit-start-time" value="">
                        </span>
                    </label>
                    </div>';
    
                break;
            }
            case 'end_date': {
    
                echo '<div class="inline-edit-group wp-clearfix">
                    <label>
                        <span class="title">End Date</span>
                        <span class="input-text-wrap">
                        <input id="quickedit-end-date" class="wpevent-end-date" type="date" name="wpevent-quickedit-end-date" value="">
                        </span>
                    </label>';    
                break;
            }
            case 'end_time': {
    
                echo '<label>
                        <span class="title">End Time</span>
                        <span class="input-text-wrap">
                        <input id="quickedit-end-time" class="wpevent-end-time timepicker" type="time" name="wpevent-quickedit-end-time" value="">
                        </span>
                    </label>';
    
                // for the LAST column only - closing the fieldset element
                echo '</div></div></fieldset>';
    
                break;
            }
    
        endswitch;
    
    }
    
    /**
     * Saves data for date and time quick edit boxes
     *
     * @param $post_id
     * @since 1.2.0
     */
    public function wpe_quick_edit_save( $post_id ) {
 
        //check user capabilities
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        //if post type is wp_events
        if( filter_input( INPUT_POST, 'post_type' ) !== "wp_events" ) {
            return;
        }
     
        // check nonce
        if ( ! wp_verify_nonce( $_POST['wpe_nonce'], 'wpe_q_edit_nonce' ) ) {
            return;
        }
     
        // update the start date and time
        if ( isset( $_POST['wpevent-quickedit-start-date'] ) && isset( $_POST['wpevent-quickedit-start-time'] ) ) {
            $new_s_dt = wpe_sanitize( $_POST['wpevent-quickedit-start-date'] ) . ' ' . wpe_sanitize( $_POST['wpevent-quickedit-start-time'] );
            update_post_meta( $post_id, "wpevent-start-date-time", strtotime( $new_s_dt ) );
        }
     
        // update the end date and time
        if ( isset( $_POST['wpevent-quickedit-end-date'] ) && isset( $_POST['wpevent-quickedit-end-time'] ) ) {
            $new_e_dt = wpe_sanitize( $_POST['wpevent-quickedit-end-date'] ) . ' ' . wpe_sanitize( $_POST['wpevent-quickedit-end-time'] );
            update_post_meta( $post_id, "wpevent-end-date-time", strtotime( $new_e_dt ) );
        }
     
    }

    /**
	 * Register and define the JavaScript for populating the quick edit meta boxes.
     *
     * @since 1.2.0
     */
    public function wpe_quick_edit_js() {
        $current_screen = get_current_screen();

        if ( $current_screen->id != 'edit-wp_events' || $current_screen->post_type !== 'wp_events' ) {
            return;
        }
        ?>
        <!-- add JS script -->
        <script type="text/javascript">
            jQuery( function( $ ) {

                // we create a copy of the WP inline edit post function
                var wpe_inline_editor = inlineEditPost.edit;

                // Note: Hooking inlineEditPost.edit must be done in a JS script, loaded after wp-admin/js/inline-edit-post.js
                // then we overwrite the inlineEditPost.edit function with our own code
                inlineEditPost.edit = function( id ) {

                    // call the original WP edit function 
                    wpe_inline_editor.apply( this, arguments );

                    // get the post ID
                    var $post_id = 0;
                    if ( typeof( id ) == 'object' ) {
                        $post_id = parseInt( this.getId( id ) );
                    }

                    // if we have our post
                    if ( $post_id != 0 ) {

                        // define the edit row
                        var edit_row = $( '#edit-' + $post_id );
                        var post_row = $( '#post-' + $post_id );

                        // get the data
                        var startDate = $( '.column-start_date', post_row ).text();
                        var startTime = $( '.column-start_time', post_row ).text();
                        var endDate   = $( '.column-end_date', post_row ).text();
                        var endTime   = $( '.column-end_time', post_row ).text();

                        // populate the data
                        $( ':input[name="wpevent-quickedit-start-date"]', edit_row ).val( startDate );
                        $( ':input[name="wpevent-quickedit-start-time"]', edit_row ).val( startTime );
                        $( ':input[name="wpevent-quickedit-end-date"]', edit_row ).val( endDate );
                        $( ':input[name="wpevent-quickedit-end-time"]', edit_row ).val( endTime );
                    }
                }
            });
        </script>
        <?php
    }
    
    /*
     * Adds view registrations link in row actions on posts screen
     * 
     * @param array $actions
     * @param object $post
     * 
     * @since 1.2.0
     * @return array
     */
    public function view_registrations_link( $actions, $post ) {

        if ( $post->post_type === 'wp_events' ) {
            $number = isset( $_GET['paged'] ) ? wpe_sanitize( $_GET['paged'] ) : '1';
            $status = isset( $_GET['event_status'] ) ? wpe_sanitize( $_GET['event_status'] ) : '';
            if ( $status !== '' ) {
                $status = '&event_status='. $status;
            }
            $post_status = isset( $_GET['post_status'] ) ? wpe_sanitize( $_GET['post_status'] ) : '';
            if ( $post_status !== '' ) {
                $post_status = '&post_status='. $post_status;
            }
            $actions['view_registrations'] = '<a href="edit.php?post_type=wp_events&page=wp_forms_entries&tab=registrations&display=all&wpe_titles='. $post->ID .'&posts_page='. $number . $status . $post_status .'" class="view_registrations">' . __('View Registrations', 'simple-wp-events') . '</a>';
        }
        
        return $actions; 
    }

    /**
     * Appends duplicate link to post row actions.
     *
     * @param array $actions
     * @param object $post
     * 
     * @since 1.4.0
     * @return array
     */
    public function wpe_duplicate_post_link( $actions, $post ) {

        if( ! current_user_can( 'edit_posts' ) ) {
            return $actions;
        }

        if ( $post->post_type !== 'wp_events' ) {
            return $actions;
        }
    
        $url = wp_nonce_url(
            add_query_arg(
                array(
                    'action' => 'wpe_duplicate_post_as_draft',
                    'post'   => $post->ID,
                ),
                'admin.php'
            ),
            basename(__FILE__),
            'duplicate_nonce'
        );
    
        $actions[ 'duplicate' ] = '<a href="' . $url . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    
        return $actions;
    }
    
    /**
     * Duplicates post with taxonimies and post meta and
     * saves it as a draft.
     *
     * @since 1.4.0
     */
    public function wpe_duplicate_post_as_draft() {
    
        // check if post ID has been provided and action
        if ( empty( $_GET[ 'post' ] ) ) {
            wp_die( 'No post to duplicate has been provided!' );
        }
    
        // Nonce verification
        if ( ! isset( $_GET[ 'duplicate_nonce' ] ) || ! wp_verify_nonce( $_GET[ 'duplicate_nonce' ], basename( __FILE__ ) ) ) {
            return;
        }
    
        // Get the original post id
        $post_id = absint( $_GET[ 'post' ] );
    
        // And all the original post data then
        $post = get_post( $post_id );

        $current_user    = wp_get_current_user();
        $new_post_author = $current_user->ID;
    
        // if post data exists, create the post duplicate
        if ( $post ) {
    
            // new post data array
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_author'    => $new_post_author,
                'post_content'   => $post->post_content,
                'post_excerpt'   => $post->post_excerpt,
                'post_name'      => $post->post_name,
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'post_status'    => 'draft',
                'post_title'     => $post->post_title,
                'post_type'      => $post->post_type,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order
            );
    
            // insert the post by wp_insert_post() function
            $new_post_id = wp_insert_post( $args );
    
            /*
             * get all current post terms ad set them to the new post draft
             */
            $taxonomies = get_object_taxonomies( get_post_type( $post ) ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
            if( $taxonomies ) {
                foreach ( $taxonomies as $taxonomy ) {
                    $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
                    wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
                }
            }
    
            // duplicate all post meta
            $post_meta = get_post_meta( $post_id );
            if( $post_meta ) {
    
                foreach ( $post_meta as $meta_key => $meta_values ) {
    
                    if( '_wp_old_slug' == $meta_key ) { // do nothing for this meta key
                        continue;
                    }
    
                    foreach ( $meta_values as $meta_value ) {
                        add_post_meta( $new_post_id, $meta_key, $meta_value );
                    }
                }
            }

            wp_safe_redirect(
                add_query_arg(
                    array(
                        'post_type' => ( 'post' !== get_post_type( $post ) ? get_post_type( $post ) : false ),
                        'saved'     => 'wpe_post_duplication' // just a custom slug here
                    ),
                    admin_url( 'edit.php' )
                )
            );
            exit;
    
        } else {
            wp_die( 'Post creation failed, could not find original post.' );
        }
    
    }

    /**
     * Changes past events status to draft.
     *
     * @since 1.6.0
     */
    public function wpe_past_events_draft() {
		$option = get_option( 'wpe_events_settings' );
        if ( isset( $option['draft_past_events'] ) ) {
            $past_events = wpe_get_past_events();
            if( $past_events ) {
                foreach ( $past_events as $eventID ) {
                    $arg = array(
                        'ID'            => $eventID,
                        'post_status'   => 'draft',
                    );
                    wp_update_post( $arg );
                }
            }
        }
    }

    /**
     * Displays admin success notice when post is duplicated.
     *
     * @since 1.4.0
     */
    public function wpe_duplication_admin_notice() {

        // Get the current screen
        $screen = get_current_screen();
    
        if ( 'edit' !== $screen->base ) {
            return;
        }
    
        //Checks if settings updated
        if ( isset( $_GET[ 'saved' ] ) && 'wpe_post_duplication' == $_GET[ 'saved' ] ) {
            $general_option = get_option( 'wpe_settings' );
		    $post_name      = $general_option['events_post_name'];
            if ( $post_name[strlen( $post_name ) - 1 ] === 's' ) {
                $single_post = substr( $post_name, 0, -1 );
            }

            echo '<div class="notice notice-success is-dismissible"><p>'. esc_html( $single_post ) .' duplicated.</p></div>';
             
        }
    }

    /**
     * Displays admin success notice when post is duplicated.
     *
     * @since 2.1.0
     */
    public function wpe_premium_admin_notice() {
        echo '<div class="wpe-premium-notice notice notice-success is-dismissible"><p><strong><i>Exciting News: Our Website is Live!</i></strong><br>Ready to elevate your website with our premium plugin? Head over to <a target="_blank" href="https://simplewpevents.com/">https://simplewpevents.com/</a> now and take the first step towards unlocking its full potential.</p></div>';
    }

    /**
     * Adds custom links in installed plugins row
     * 
     * @since 2.1.0
     */
    public function wpe_add_settings_link( $links ) {
        $settings_link = '<a href="edit.php?post_type=wp_events&page=wp_events_settings">' . __( 'Settings', 'simple-wp-events' ) . '</a>';
        $premium_link  = '<a class="wpe-premium-link" target="_blank" href="https://simplewpevents.com/">' . __( 'Go Premium', 'simple-wp-events' ) . '</a>';
        array_push( $links, $settings_link );
        array_push( $links, $premium_link );
        return $links;
    }

    /**
     * Admin Form Entries Page      Callback for wp_forms_entries page added in wpevents_submenu_page
     *
     * @since 1.0.0
    */
    public function wpevents_form_entries_page() {
	    require_once plugin_dir_path( __FILE__ ). 'templates/wp-events-form-entries-display.php';
    }

    /**
     * Admin settings page Display      Callback for
     *
     * @since 1.0.0
    */
    public function wpevents_settings_page() {
        require_once plugin_dir_path( __FILE__ ). 'templates/wp-events-admin-settings-display.php';
    }

    /**
     * Callback for View Registration page display
     *
     * @since 1.2.0
    */
    public function wpevents_view_entry_page() {
        require_once plugin_dir_path( __FILE__ ). 'templates/wp-events-view-entry-display.php';
    }

    /**
     * Admin Settings Function
     *
     * @action wp_events_settings_tab
     * @since 1.0.0
    */
    public function wpevents_admin_settings_tabs() {
        $this->admin_settings->wpe_admin_settings_tab();
    }

    /**
     * Admin Settings Tabs Content
     *
     * @since 1.0.0
    */
    public function wpevents_admin_settings_content() {
        $this->admin_settings->wpe_admin_settings_content();
    }

    /**
     * Registering Setting for Admin Settings Page
     *
     * @since 1.0.0
    */
    public function wpevents_register_settings() {
        $this->admin_settings->wpe_admin_register_settings();
    }

    function wpe_plugin_display_activation_notice() {
        echo '<div class="wpe-notice notice-info notice is-dismissible" style="background-color:#fff; display:flex; align-items:center; gap:20px;">';
        echo '<img width="100" src="'. plugins_url() . '/' . WPE_PLUGIN_BASE . '/assets/feedback.png' .'">';
        echo '<div class="wpe-notice-content">';
        echo '<p>' . esc_html__('Hello! It looks like you\'ve been using Simple WP Events Plugin on your website — thank you so much!', 'simple-wp-events') . '</p>';
        echo '<p>' . esc_html__('If you could take a moment to leave us a 5-star rating on WordPress, we\'d greatly appreciate it. Your support not only motivates us but also helps other users make informed choices when selecting Simple WP Events Plugin. Thank you!', 'simple-wp-events') . '</p>';
        echo '<p><a href="https://wordpress.org/plugins/simple-wp-events/#reviews" class="button button-primary" target="_blank">Review Us</a></p>';
        echo '</div>';
        echo '</div>';
    }

}