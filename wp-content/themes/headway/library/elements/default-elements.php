<?php
add_action('headway_register_elements', 'headway_register_default_elements');
function headway_register_default_elements() {
	
	HeadwayElementAPI::register_element(array(
		'id' => 'default-text',
		'name' => 'Paragraph Text',
		'properties' => array('fonts'),
		'default-element' => true,
		'selector' => 'body'
	));

	HeadwayElementAPI::register_element(array(
		'id' => 'default-hyperlink',
		'name' => 'Hyperlink',
		'properties' => array('fonts'  => array('color', 'font-styling', 'capitalization'), 'text-shadow', 'borders', 'box-shadow', 'rounded-corners'),
		'default-element' => true,
		'selector' => 'a'
	));
	
	HeadwayElementAPI::register_element(array(
		'id' => 'default-block',
		'name' => 'Block',
		'properties' => array('background', 'borders', 'fonts', 'padding', 'rounded-corners', 'box-shadow', 'overflow'),
		'default-element' => true,
		'selector' => '.block'
	));

	HeadwayElementAPI::register_element(array(
		'id' => 'block-title',
		'name' => 'Block Title',
		'selector' => '.block-title',
		'default-element' => true
	));

	HeadwayElementAPI::register_element(array(
		'id' => 'block-subtitle',
		'name' => 'Block Subtitle',
		'selector' => '.block-subtitle',
		'default-element' => true
	));
	
}