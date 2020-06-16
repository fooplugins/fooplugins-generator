<?php
namespace FooPlugins\Generator\Admin;


/**
 * FooPlugins Generator Boilerplate State Manager Class
 */

if ( ! class_exists( 'FooPlugins\Generator\Admin\BoilerplateStateManager' ) ) {

	class BoilerplateStateManager {

		public $state = array();
		public $errors = array();

		function __construct() {
			add_filter( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\input', array( $this, 'get_field_value_from_input' ), 10, 5 );
			add_filter( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\field', array( $this, 'get_field_value_from_field' ), 10, 5 );
			add_filter( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\state', array( $this, 'get_field_value_from_state' ), 10, 5 );
		}

		/**
		 * Returns true if there are any errors
		 * @return bool
		 */
		public function has_errors() {
			return count( $this->errors ) > 0;
		}

		/**
		 * Builds up boilerplate state from the data
		 *
		 * @param $boilerplate_object
		 * @param $boilerplate_data
		 *
		 * @return mixed|void
		 */
		public function build_state( $boilerplate_object, $boilerplate_data ) {
			$this->state = array();

			//build up values for all fields
			foreach( $boilerplate_object['fields'] as $field_key => $field ) {
				$source = foogen_safe_get_from_array( 'source', $field, 'input' );
				$value = apply_filters( 'Fooplugins\Generator\Admin\BoilerplateStateManager\GetFieldValue\\' . $source, null, $field_key, $field, $boilerplate_data, $this->state );
				if ( $value !== null ) {
					$this->state[ $field_key ] = $value;

					if ( empty( $value ) && isset( $field['required'] ) && $field['required'] ) {
						$this->errors[] = $field;
					}
				}
			}

			//add the current date to the state
			$this->state['date'] = date( 'Y-m-d' );

			//allow the state to change from other places
			$this->state = apply_filters( 'Fooplugins\Generator\Admin\BoilerplateStateManager\FinaliseState', $this->state, $boilerplate_object, $boilerplate_data );

			return $this->state;
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
