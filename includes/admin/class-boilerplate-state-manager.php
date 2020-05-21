<?php
namespace FooPlugins\Generator\Admin;


/**
 * FooPlugins Generator Boilerplate State Manager Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateStateManager' ) ) {

	class BoilerplateStateManager {

		function __construct() {
			add_filter( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\input', array( $this, 'get_field_value_from_input' ), 10, 5 );
			add_filter( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\field', array( $this, 'get_field_value_from_field' ), 10, 5 );
			add_filter( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\state', array( $this, 'get_field_value_from_state' ), 10, 5 );
		}

		public function build_state( $boilerplate_object, $boilerplate_data ) {
			$state = array();

			//build up values for all fields
			foreach( $boilerplate_object['fields'] as $field_key => $field ) {
				$source = foogen_safe_get_from_array( 'source', $field, 'input' );
				$value = apply_filters( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\\' . $source, null, $field_key, $field, $boilerplate_data, $state );
				if ( $value !== null ) {
					$state[ $field_key ] = $value;
				}
			}

			//add the current date to the state
			$state['date'] = date( 'Y-m-d' );

			//allow the state to change from other places
			$state = apply_filters( 'Fooplugins\Generator\Admin\BoilerplateStateManager\FinaliseState', $state, $boilerplate_object, $boilerplate_data );

			return $state;
		}

		/**
		 * Get the value from the boilerplate form data
		 *
		 * @param $value
		 * @param $field_key
		 * @param $field
		 * @param $boilerplate_data
		 * @param $boilerplate_state
		 *
		 * @return mixed
		 */
		function get_field_value_from_input( $value, $field_key, $field, $boilerplate_data, $boilerplate_state ) {
			return foogen_safe_get_from_array( $field_key, $boilerplate_data, $value );
		}

		/**
		 * Get the value from calling a function and passing in state
		 *
		 * @param $value
		 * @param $field_key
		 * @param $field
		 * @param $boilerplate_data
		 * @param $boilerplate_state
		 *
		 * @return mixed|null
		 */
		function get_field_value_from_state( $value, $field_key, $field, $boilerplate_data, $boilerplate_state ) {
			$function = foogen_safe_get_from_array( 'function', $field, false );
			if ( $function !== false ) {
				$value = @call_user_func( $function, $boilerplate_state );
				if ( isset( $value ) ) {
					$boilerplate_state[ $field_key ] = $value;
				}
			}

			return $value;
		}

		/**
		 * Get the value from another field and optionally call a function passing in that field's value
		 *
		 * @param $value
		 * @param $field_key
		 * @param $field
		 * @param $boilerplate_data
		 * @param $boilerplate_state
		 *
		 * @return mixed|null
		 */
		function get_field_value_from_field( $value, $field_key, $field, $boilerplate_data, $boilerplate_state ) {
			$field_name = foogen_safe_get_from_array( 'field', $field, false );
			if ( $field_name !== false ) {
				$value = foogen_safe_get_from_array( $field_name, $boilerplate_data, $value );
				$function = foogen_safe_get_from_array( 'function', $field, false );
				if ( $function !== false ) {
					$value = @call_user_func( $function, $value );
				}
			}

			return $value;
		}
	}
}