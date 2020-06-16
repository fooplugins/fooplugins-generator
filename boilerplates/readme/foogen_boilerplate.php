<?php

return array(
	'name'        => 'readme',
	'title'       => __( 'readme.txt Generator', 'foogen' ),
	'description' => __( 'Generates a simple readme.txt', 'foogen' ),
	'actions'     => 'show',
	'fields'      => array(
		'name'         => array(
			'label'    => __( 'Plugin Name', 'foogen' ),
			'type'     => 'text',
			'default'  => __( 'Cool Plugin', 'foogen' ),
			'required' => true
		),
		'desc'         => array(
			'label'    => __( 'Plugin Description', 'foogen' ),
			'type'     => 'textarea',
			'default'  => __( 'A cool description about what your cool plugin can do', 'foogen' ),
			'desc'     => __( 'A short description of the plugin. This should be no more than 150 characters. No markup here.', 'foogen' ),
			'required' => true
		),
		'contributors' => array(
			'label'    => __( 'Contributors', 'foogen' ),
			'type'     => 'text',
			'default'  => wp_get_current_user()->user_login,
			'desc'     => __( 'A case sensitive, comma separated list of all WordPress.org usernames who have contributed to the code.', 'foogen' ),
			'required' => true
		),
		'tags'         => array(
			'label'    => __( 'Tags', 'foogen' ),
			'type'     => 'text',
			'desc'     => __( '1 to 12 comma separated terms that describe the plugin. Only the first five will show on the generated page, and anything over 12 will be detrimental to SEO. Plugins must refrain from using competitors plugin names as tags.', 'foogen' ),
			'default'  => __( 'best plugin, ecommerce, media, social, generator' ),
			'required' => true
		)
	)
);
