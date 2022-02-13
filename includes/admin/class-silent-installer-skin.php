<?php

namespace FooPlugins\Generator\Admin;

use WP_Upgrader_Skin;

if ( ! class_exists( 'WP_Upgrader_Skin' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skins.php';
}

if ( !class_exists( 'FooPlugins\Generator\Admin\SilentInstallerSkin' ) ) {

	class SilentInstallerSkin extends WP_Upgrader_Skin {
		public $feedback = false;

		public function header() {
		}

		public function footer() {
		}

		public function before() {
		}

		public function after() {
		}

		public function feedback( $feedback, ...$args ) {
			$this->feedback = $feedback;
		}
	}
}
