<?php
namespace FooPlugins\Generator;

/**
 * FooPlugins Generator Init Class
 * Runs at the startup of the plugin
 * Assumes after all checks have been made, and all is good to go!
 */

if ( !class_exists( 'FooPlugins\Generator\Init' ) ) {

	class Init {

		/**
		 * Initialize the plugin by setting localization, filters, and administration functions.
		 */
		public function __construct() {
			//load the plugin text domain
			add_action( 'plugins_loaded', function() {
				load_plugin_textdomain(
					FOOGEN_SLUG,
					false,
					plugin_basename( FOOGEN_FILE ) . '/languages/'
				);
			});

			if ( is_admin() ) {
				new namespace\Admin\Init();
			}

			if ( foogen_fs()->is__premium_only() ) {
				if ( foogen_fs()->can_use_premium_code() ) {
					new namespace\Pro\Init();
				}
			}
		}
	}
}
