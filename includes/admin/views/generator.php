<?php

$boilerplates = foogen_get_all_boilerplates();
$boilerplate_data = array();
$selected_boilerplate = false;
$boilerplate_error = '';

$nonce = foogen_safe_get_from_array( 'foogen_generate', $_REQUEST, '' );

if ( ! empty( $nonce ) && wp_verify_nonce( $nonce, 'foogen_generate' ) ) {

    $selected_boilerplate = foogen_safe_get_from_array( 'selected_boilerplate', $_POST, false );
    if ( $selected_boilerplate !== false ) {
	    $boilerplate_data = foogen_safe_get_from_array( $selected_boilerplate, $_POST, array() );
    } else {
        //nothing selected!
	    $boilerplate_error = __( 'Select a boilerplate!', 'foogen' );
    }
}
?>
<script type="text/javascript">
    jQuery( function() {
        jQuery( '.boilerplate_select' ).on( 'change', function() {
            //hide previously selected
            jQuery( '.active_boilerplate' ).removeClass( 'active_boilerplate' );

            jQuery( '.boilerplate_field-' + this.value ).addClass( 'active_boilerplate' );

            //hide all the buttons
			jQuery('.foogen-generator-actions button').hide();

            //show the appropriate action buttons
			var actions = jQuery( '#' + this.value + '-actions' ).val().split(' ');
			jQuery.each( actions, function( index, action ) {
				jQuery('.foogen-generator-actions button[value="' + action + '"]').show();
			});

			jQuery( '.foogen-generator-actions' ).show();
		});

		//hide all the buttons
		jQuery('.foogen-generator-actions button').hide();

		var actions = jQuery( '.active_boilerplate input' ).val();
		if ( actions ) {
			jQuery( '.foogen-generator-actions' ).show();

			jQuery.each(actions.split(' '), function (index, action) {
				jQuery('.foogen-generator-actions button[value="' + action + '"]').show();
			});
		}

		jQuery('.foogen-tabs a').click( function(e) {
			jQuery('.foogen-tabs a').removeClass('nav-tab-active');

			jQuery(this).addClass('nav-tab-active');

			var activeTab = jQuery(this).data('tab');

			jQuery( '.foogen-tab-content').hide();

			jQuery( '.foogen-tab-content[data-tab="' + activeTab + '"]').show();
		});

    });
</script>
<style>
	.foogen-generator form {
		width:50%;
		margin-top:10px;
		border: solid 3px #aaa;
		border-radius: 5px;
		padding: 0;
		background: #ddd;
	}

	.foogen-generator form h2 {
		margin: 0;
		background: #aaa;
		color: #fff;
		padding: 10px;
	}

    .foogen-generator form div {
        padding:10px;
    }

	.foogen-generator form div.boilerplate_field {
        display: none;
	}

    .foogen-generator form div.boilerplate_field.active_boilerplate {
        display: block;
    }

	.foogen-generator form label{
		margin: 3px 1px;
		display: block;
		font-weight: bold;
	}

	.foogen-generator form .button-row input {
		margin-right: 5px;
	}

	.foogen-generator form textarea {
		height: 50px;
	}

	.foogen-generator form input[type="text"],
	.foogen-generator form textarea,
	.foogen-generator form select {
		width: 100% !important;
	}

	.foogen-generator form span {
		margin-left: 5px;
		font-style: italic;
		display: block;
		color: #888;
	}

	.foogen-generator form input+span {
		margin-top: 3px;
	}

	.foogen-generator form .boilerplate-error {
		color: #800;
	}

	.foogen-generator form .boilerplate-message {
		color: #0b0;
	}

	.foogen-generator pre {
		max-height: 500px;
		overflow: scroll;
		border: solid 1px #aaa;
		border-radius: 2px;
		background: #fff;
		overflow-x: hidden;
		padding: 5px;
	}

	.foogen-generator .foogen-tabs a {
		box-shadow: none !important;
		outline: none !important;
	}

</style>

<div class="foogen-generator">
	<h1><?php _e( 'FooPlugins Code Generator', 'foogen' ); ?></h1>
	<p><?php _e( 'Select a boilerplate, fill in all the details, and generate some code!', 'foogen' ); ?></p>
	<form method="post">
		<div>
			<label><?php _e( 'Boilerplate', 'foogen' ); ?></label>
			<select class="boilerplate_select" name="selected_boilerplate">
                <option>--<?php _e( 'select', 'foogen' ); ?>--</option>
                <?php foreach( $boilerplates as $boilerplate ) {
                    $selected = $selected_boilerplate === $boilerplate['name'] ? 'selected="selected"' : '';
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo esc_attr( $boilerplate['name'] ); ?>"><?php echo esc_html( $boilerplate['title'] ); ?></option>
                <?php } ?>
			</select>
		</div>
        <?php foreach( $boilerplates as $boilerplate ) {
            $boilerplate_name = $boilerplate['name'];
            if ( isset( $boilerplate['description'] ) ) {
            ?>
            <div class="boilerplate_field boilerplate_field-<?php echo $boilerplate_name; ?> <?php echo ($selected_boilerplate === $boilerplate_name) ? 'active_boilerplate' : ''; ?>">
                <i class="dashicons dashicons-info"></i> <?php echo esc_html( $boilerplate['description'] ); ?>
				<input type="hidden" id="<?php echo esc_attr( $boilerplate_name ); ?>-actions" value="<?php echo esc_html( $boilerplate['actions'] ); ?>" />
            </div>
            <?php
            }

            foreach( $boilerplate['fields'] as $field_key => $field ) {
	            $source = foogen_safe_get_from_array( 'source', $field, 'input' );
	            if ( $source !== 'input' ) continue;
	            $default_field_value = foogen_safe_get_from_array( 'default', $field, '' );
	            $field_value = foogen_safe_get_from_array( $field_key, $boilerplate_data, $default_field_value );
	            $field_id = $boilerplate_name . '_' . $field_key;
	            $field_label = $field['label'];
	            if ( isset( $field['required'] ) && $field['required'] ) {
					$field_label .= ' *';
				}
                ?>
        <div class="boilerplate_field boilerplate_field-<?php echo $boilerplate_name; ?> <?php echo ($selected_boilerplate === $boilerplate_name) ? 'active_boilerplate' : ''; ?>">
            <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field_label ); ?></label>
            <?php if ( $field['type'] === 'text' ) { ?>
                <input id="<?php echo esc_attr( $field_id ); ?>" type="text" name="<?php echo $boilerplate_name; ?>[<?php echo $field_key; ?>]" value="<?php echo esc_attr( $field_value ); ?>"/>
            <?php } else if ( $field['type'] === 'textarea' ) { ?>
                <textarea id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo $boilerplate_name; ?>[<?php echo $field_key; ?>]"><?php echo esc_html( $field_value ) ?></textarea>
            <?php } ?>
			<?php if ( isset( $field['desc'] ) ) { ?>
				<span><?php echo esc_html( $field['desc'] ); ?></span>
			<?php } ?>
        </div>
        <?php } } ?>
		<div class="foogen-generator-actions" style="display:none">
			<button name="action" class="button button-primary" value="download"><?php _e( 'Generate &amp; download .zip', 'foogen' ); ?></button>
			<button name="action" class="button button-primary" value="install"><?php _e( 'Generate &amp; Install Plugin', 'foogen' ); ?></button>
			<button name="action" class="button button-primary" value="show"><?php _e( 'Generate', 'foogen' ); ?></button>

			<?php foreach ( foogen_get_global_messages() as $message ) {
				if ( $message['error'] ) {
					echo "<p class=\"boilerplate-error\">{$message['message']}</p>";
				} else {
					echo "<p class=\"boilerplate-message\">{$message['message']}</p>";
				}
			} ?>
			<?php wp_nonce_field( 'foogen_generate', 'foogen_generate' ); ?>
		</div>
	</form>
	<?php do_action( 'FooPlugins\Generator\Admin\AfterForm' ); ?>
</div>
