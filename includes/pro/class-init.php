<?php
namespace FooPlugins\Generator\Pro;

/**
 * FooPlugins Generator Init Class
 * Runs at the startup of the plugin
 * Assumes after all checks have been made, and all is good to go!
 */

if ( !class_exists( 'FooPlugins\Generator\Pro\Init' ) ) {

	class Init {

		/**
		 * Initialize the plugin by setting localization, filters, and administration functions.
		 */
		public function __construct() {
			if ( is_admin() ) {
				new namespace\Admin\Init();
				new namespace\ProBoilerplates();
			}
		}
	}
}
