<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       //wpminds.com
 * @since      1.0.0
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
    settings_errors();
    global $wpe_active_tab;
    $wpe_active_tab = isset( $_GET['tab'] ) ? wpe_sanitize( $_GET['tab'] ) : 'general'; ?>
    <div class="wpe-header">
        <div class="wpe-header-wrap">
            <img width="40" height="40" src="<?php echo plugins_url() . '/' . WPE_PLUGIN_BASE . '/assets/img/logo.png'; ?>">
            <h1><?php _e( 'Simple WP Events Settings', 'simple-wp-events' ); ?></h1>
            <p class="wpe-documentation">Need Help? Visit the plugin <a target="_blank" href="https://simplewpevents.com/docs/">documentation</a>.</p>
        </div>
    </div>
    <form method="post" action="options.php" id="wpe-settings-form">
    <h2 class="nav-tab-wrapper">
        <?php
        do_action( 'wp_events_settings_tab' );
        ?>
    </h2>
    <div class="wpe-settings-content wrap">
        <?php
        do_action( 'wp_events_settings_content' );
        ?>
    </div>
        <?php
        $other_attributes = array( 'id' => 'wpe-save-settings' ); 
        submit_button( __( 'Save Settings', 'simple-wp-events' ), 'primary', 'wpe-save-settings', true, $other_attributes ); ?>
    </form>
