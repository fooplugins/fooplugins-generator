<?php
namespace FooPlugins\Generator\Pro\Admin;

/**
 * Pro Admin Init Class
 * Runs all classes that need to run in the admin for PRO
 */

if ( !class_exists( 'FooPlugins\Generator\Pro\Admin\Init' ) ) {

	class Init {

		/**
		 * Init constructor.
		 */
		function __construct() {
			new namespace\BoilerplateZipGeneratorIncludes();
		}
	}
}
