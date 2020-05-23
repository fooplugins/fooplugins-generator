<?php
namespace FooPlugins\Generator\Pro;

use FooPlugins\Generator\Admin\BoilerplateLoader;

/**
 * FooPlugins Generator Pro Boilerplates Class
 */

if ( !class_exists( 'FooPlugins\Generator\Pro\ProBoilerplates' ) ) {

	class ProBoilerplates {

		/**
		 * Class constructor
		 */
		public function __construct() {
			if ( is_admin() ) {
				add_filter( 'FooPlugins\Generator\Admin\GetAllBoilerplates', array( $this, 'add_pro_boilerplates' ) );
			}
		}

		/**
		 * Adds boilerplates from the PRO plugin
		 *
		 * @param $boilerplates
		 *
		 * @return array
		 */
		public function add_pro_boilerplates( $boilerplates ) {
			$loader = new BoilerplateLoader();

			$pro_boilerplate_directory = untrailingslashit( FOOGEN_PATH ) . DIRECTORY_SEPARATOR . 'includes/pro/boilerplates';

			$pro_boilerplates = $loader->load_boilerplates_from_directory( $pro_boilerplate_directory );

			return array_merge( $boilerplates, $pro_boilerplates );
		}
	}
}
