<?php
/**
 * Runs all the Freemius initialization code
 */

if ( ! function_exists( 'foogen_fs' ) ) {
	// Create a helper function for easy SDK access.
	function foogen_fs() {
		global $foogen_fs;

		if ( ! isset( $foogen_fs ) ) {
			// Include Freemius SDK.
			require_once FOOGEN_PATH . '/freemius/start.php';

			$foogen_fs = fs_dynamic_init( array(
				'id'                  => '6152',
				'slug'                => 'fooplugins-generator',
				'type'                => 'plugin',
				'public_key'          => 'pk_bfc88433ebe2aa2c7b66bfa24110f',
				'is_premium'          => false,
				'has_addons'          => false,
				'has_paid_plans'      => false,
				'menu'                => array(
					'slug'           => 'foogen-settings',
					'support'        => false,
					'parent'         => array(
						'slug' => 'options-general.php',
					),
				),
			) );
		}

		return $foogen_fs;
	}

	// Init Freemius.
	foogen_fs();
	// Signal that SDK was initiated.
	do_action( 'foogen_fs_loaded' );
}