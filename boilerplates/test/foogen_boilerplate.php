<?php

return array(
	'name'        => 'test',
	'title'       => __( 'Test Generator', 'foogen' ),
	'exclude_files' => array( '.DS_Store', 'foogen_boilerplate.php', 'foogen_include.php', 'excluded.txt' ),
	'exclude_directories' => array( '.git', '.svn', '.', '..', 'excluded' ),
	'description' => __( 'Generates a simple txt file to showcase the use of all the capabilities of the generator', 'foogen' ),
	'actions'     => 'show',
	'fields'      => array(
		'name'         => array(
			'label'    => __( 'Name', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'Bob The Builder', 'foogen' ),
			'required' => true
		),
		'namespace'         => array(
			'label'    => __( 'Test Slashes', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'CoolPlugins\\CoolThing', 'foogen' ),
			'desc'     => __( 'This variable is used to test values that contain slashes, for example namespaces within PHP files.', 'foogen' ),
			'required' => true
		),
		'title'         => array(
			'label'    => __( 'Optional Title', 'foogen' ),
			'type'     => 'text',
		),
		'includes' => array(
			'label' => __( 'Includes', 'foogen' ),
			'type' => 'checkbox'
		),
		'filename' => array(
			'source' => 'field',
			'field' => 'name',
			'function' => '__foogen_filename',
		),
	),
	'rules' => array(
		array(
			'type' => 'exclude_files',
			'field' => 'title',
			'value' => '',
			'files' => array(
				'optional-include.txt'
			)
		),
		array(
			'type' => 'exclude_directories',
			'field' => 'title',
			'value' => '',
			'directories' => array(
				'includes\optional'
			)
		),
		array(
			'type' => 'exclude_directories',
			'field' => 'includes',
			'value' => '',
			'directories' => array(
				'includes'
			)
		)
	)
);
