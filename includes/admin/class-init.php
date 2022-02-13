<?php
namespace FooPlugins\Generator\Admin;

use FooPlugins\Generator\Pro\Admin\BoilerplateIncludesFreemius;

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
			new namespace\BoilerplateIncludes();
			new namespace\BoilerplateActionHandler();
			new namespace\BoilerplateActionHandlerDownload();
			new namespace\BoilerplateActionHandlerInstall();
			new namespace\BoilerplateActionHandlerShow();
			new namespace\BoilerplateIncludesFreemius();
		}
	}
}
