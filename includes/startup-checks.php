<?php
/**
 * Does some preliminary checks before the plugin is loaded
 */


if ( !function_exists('foogen_min_php_admin_notice' ) ) {
	/**
	 * Show an admin notice to administrators when the minimum PHP version could not be reached
	 */
	function foogen_min_php_admin_notice() {
        //only show the admin message to users who can install plugins
        if ( !current_user_can('install_plugins' ) ) { return; }

		extract( get_plugin_data(FOOGEN_FILE, true, false ) );
		echo '<div class=\'notice notice-error\'>
			<p><strong>' . $Name . '</strong> could not be initialized because you need to be running at least PHP version ' . FOOGEN_MIN_PHP . ', and you are running version ' . phpversion() . '.
		</div>';
	}
}

if ( !function_exists('foogen_min_wp_admin_notice' ) ) {
	/**
	 * Show an admin notice to administrators when the minimum WP version could not be reached
	 */
	function foogen_min_wp_admin_notice() {
        //only show the admin message to users who can install plugins
        if ( !current_user_can('install_plugins' ) ) { return; }

		extract(get_plugin_data(FOOGEN_FILE, true, false));
		global $wp_version;
		echo '<div class=\'notice notice-error\'>
			<p><strong>' . $Name . '</strong> could not be initialized because you need WordPress to be at least version ' . FOOGEN_MIN_WP . ', and you are running version ' . $wp_version . '.
			<a href="' . admin_url('update-core.php') . '">Update WordPress now.</a>
		</div>';
	}
}

//check minimum PHP version
if ( version_compare( phpversion(), FOOGEN_MIN_PHP, "<" ) ) {
    add_action( 'admin_notices', 'foogen_min_php_admin_notice' );
	return false;
}

//check minimum WordPress version
global $wp_version;
if ( version_compare( $wp_version, FOOGEN_MIN_WP, '<' ) ) {
    add_action( 'admin_notices', 'foogen_min_wp_admin_notice' );
	return false;
}

//if we got here, then we passed all startup checks and the plugin can be loaded
return true;
