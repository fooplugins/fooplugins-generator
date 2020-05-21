<?php
namespace FooPlugins\Generator\Admin;

/**
 * Admin Init Class
 * Runs all classes that need to run in the admin
 */

if ( !class_exists( 'FooPlugins\Generator\Admin\Init' ) ) {

	class Init {

		/**
		 * Init constructor.
		 */
		function __construct() {
			new namespace\Updates();
			new namespace\Settings();
			new namespace\BoilerplateDownloadHandler();
		}
	}
}
