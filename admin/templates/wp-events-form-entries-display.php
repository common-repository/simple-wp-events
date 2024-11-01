<?php
/**
 * Provide a Form Entries view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       //wpminds.com
 * @since      1.0.0
 *
 * @package    Wp_Events
 * @subpackage Wp_Events/admin/templates
 */
?>
<div class="wpe-header">
	<div class="wpe-header-wrap">
		<img width="40" height="40" src="<?php echo plugins_url() . '/' . WPE_PLUGIN_BASE . '/assets/img/logo.png'; ?>">
		<h1><?php _e( 'Simple WP Events Entries', 'simple-wp-events' ); ?></h1>
		<p class="wpe-documentation">Need Help? Visit the plugin <a target="_blank" href="https://simplewpevents.com/docs/">documentation</a>.</p>
	</div>
</div> 
<?php wpe_go_back_link(); ?>
<div class="wrapper">
	<?php
	global $wpe_entries_tab;
	$wpe_entries_tab = isset( $_GET['tab'] ) ? wpe_sanitize( $_GET['tab'] ) : 'registrations'; 
	?>
    <form id="wpe-list-form" method="get">
        <input type="hidden" name="post_type" class="post_type_page" value="wp_events">
        <input type="hidden" name="page" value="<?php
		echo esc_attr( $_REQUEST['page'] ) ?>">
		<input type="hidden" name="tab" value="<?php
		echo esc_attr( $wpe_entries_tab ); ?>">
        <input type="hidden" name="display" value="<?php
	    echo isset( $_GET['display'] ) ? esc_attr( $_GET['display'] ) : 'all'; ?>">
        <h2 class="nav-tab-wrapper">
			<?php
			//To hook function wpe_admin_entries_tab.
			do_action( 'wp_events_entries_tab' );
			?>
        </h2>
		<?php
		//To hook function wpe_display_entries_table.
		do_action( 'wp_events_entries_table' ); ?>
    </form>
</div>
