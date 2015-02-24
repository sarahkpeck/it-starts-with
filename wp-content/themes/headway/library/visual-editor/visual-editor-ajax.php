<?php
class HeadwayVisualEditorAJAX {
	
	
	private static function json_encode($data) {

		header('content-type:application/json');

		if ( headway_get('callback') )
			echo headway_get('callback') . '(';

		echo json_encode($data);

		if ( headway_get('callback') )
			echo ')';

	}


	/* Saving methods */
	public static function secure_method_save_options() {
		
		//Set up options
		parse_str(headway_post('options'), $options);
		
		if ( HeadwayVisualEditor::save($options) )
			echo 'success';
		
	}
	

	/* Layout Selector */
	public static function method_get_layout_selector_pages() {

		$pages = HeadwayVisualEditorDisplay::list_pages();

		echo trim(str_replace(array("\n", "\t"), '', $pages));

	}


	public static function method_get_layout_selector_templates() {

		$templates = HeadwayVisualEditorDisplay::list_templates();

		echo trim(str_replace(array("\n", "\t"), '', $templates));

	}

	
	/* Block methods */
	public static function method_get_available_block_id() {

		$block_id_blacklist = headway_post('block_id_blacklist', array());

		echo HeadwayBlocksData::get_available_block_id($block_id_blacklist);

	}


	public static function method_get_available_block_id_batch() {
		
		$block_id_blacklist = headway_post('block_id_blacklist', array());
		$number_of_ids = headway_post('number_of_ids', 10);
		
		if ( !is_numeric($number_of_ids) )
			$number_of_ids = 10;

		$block_ids = array();

		for ( $i = 1; $i <= $number_of_ids; $i++ ) {

			$available_block_id = HeadwayBlocksData::get_available_block_id($block_id_blacklist);

			$block_ids[] = $available_block_id;
			$block_id_blacklist[] = $available_block_id;

		}

		self::json_encode($block_ids);

	}
	
	
	public static function method_get_layout_blocks_in_json() {
		
		$layout = headway_post('layout', false);
		$layout_status = HeadwayLayout::get_status($layout);
		
		if ( $layout_status['customized'] != true )
			return false;

		self::json_encode(array(
			'blocks' => HeadwayBlocksData::get_blocks_by_layout($layout, true),
			'wrappers' => HeadwayWrappers::get_layout_wrappers($layout, true)
		));
		
	}
	
	
	public static function method_load_block_content() {

		/* Check for grid safe mode */
			if ( HeadwayOption::get('grid-safe-mode', false, false) ) {

				echo '<div class="alert alert-red block-safe-mode"><p>Grid Safe mode enabled.  Block content not outputted.</p></div>';

				return;

			}

		/* Go */
		$layout = headway_post('layout');
		$block_origin = headway_post('block_origin');
		$block_default = headway_post('block_default', false);
		
		$unsaved_block_settings = headway_post('unsaved_block_settings', false);
		
		/* If the block origin is a string or ID, then get the object from DB. */
		if ( is_numeric($block_origin) || is_string($block_origin) )
			$block = HeadwayBlocksData::get_block($block_origin);
			
		/* Otherwise use the object */
		else
			$block = $block_origin;
									
		/* If the block doesn't exist, then use the default as the origin.  If the default doesn't exist... We're screwed. */
		if ( !$block && $block_default )
			$block = $block_default;
						
		/* If the block settings is an array, merge that into the origin.  But first, make sure the settings exists for the origin. */
		if ( !isset($block['settings']) )
			$block['settings'] = array();
		
		if ( is_array($unsaved_block_settings) && count($unsaved_block_settings) && isset($unsaved_block_settings['settings']) ) {

			$block = headway_array_merge_recursive_simple($block, $unsaved_block_settings);

		}
			
		/* If the block is set to mirror, then get that block. */
		if ( $mirrored_block = HeadwayBlocksData::is_block_mirrored($block) ) {

			$original_block = $block;

			$block = $mirrored_block;
			$block['original'] = $original_block;

		}
					
		/* Add a flag into the block so we can check if this is coming from the visual editor. */
		$block['ve-live-content-query'] = true;
		
		/* Show the content */		
		do_action('headway_block_content_' . $block['type'], $block);

		/* Output dynamic JS and CSS */
			if ( headway_post('mode') != 'grid' ) {

				$block_types = HeadwayBlocks::get_block_types();

				/* Dynamic CSS */
					if ( method_exists($block_types[$block['type']]['class'], 'dynamic_css') ) {

						echo '<style type="text/css">';
							echo call_user_func(array($block_types[$block['type']]['class'], 'dynamic_css'), $block['id'], $block);
						echo '</style><!-- AJAX Block Content Dynamic CSS -->';

					}

				/* Run enqueue action and print right away */
					if ( method_exists($block_types[$block['type']]['class'], 'enqueue_action') ) {

						/* Remove all other enqueued scripts to reduce conflicts */
							global $wp_scripts;
							$wp_scripts = null;
							remove_all_actions('wp_print_scripts');

						/* Remove all other enqueued styles to reduce conflicts */
							global $wp_styles;
							$wp_styles = null;
							remove_all_actions('wp_print_styles');

						echo call_user_func(array($block_types[$block['type']]['class'], 'enqueue_action'), $block['id'], $block);
						wp_print_scripts();
						wp_print_footer_scripts(); /* This isn't really needed, but it's here for juju power */

					}

				/* Output dynamic JS */
					if ( method_exists($block_types[$block['type']]['class'], 'dynamic_js') ) {

						echo '<script type="text/javascript">';
							echo call_user_func(array($block_types[$block['type']]['class'], 'dynamic_js'), $block['id'], $block);
						echo '</script><!-- AJAX Block Content Dynamic JS -->';

					}

			}
		/* End outputting dynamic JS and CSS */

	}
	
	
	public static function method_load_block_options() {
		
		$layout = headway_post('layout');
		$block_id = headway_post('block_id');
		$unsaved_options = headway_post('unsaved_block_options', array());
	
		$block = HeadwayBlocksData::get_block($block_id);
		
		//If block is new, set the bare basics up
		if ( !$block ) {
			
			$block = array(
				'type' => headway_post('block_type'),
				'new' => true,
				'id' => $block_id,
				'layout' => $layout
			);
		
		}
				
		/* Merge unsaved options in */
		if ( is_array($unsaved_options) )
			$block['settings'] = is_array(headway_get('settings', $block)) ? array_merge($block['settings'], $unsaved_options) : $unsaved_options;
							
		do_action('headway_block_options_' . $block['type'], $block, $layout);
		
	}


	/* Wrapper Methods */
	public static function method_load_wrapper_options() {
		
		$layout_id = headway_post('layout');
		$wrapper_id = headway_post('wrapper_id');
		$unsaved_options = headway_post('unsaved_wrapper_options', array());
	
		$wrapper = HeadwayWrappers::get_wrapper($wrapper_id, $layout_id);

		/* Merge unsaved options in */
			if ( is_array($unsaved_options) )
				$wrapper = array_merge($wrapper, $unsaved_options);
							
		do_action('headway_wrapper_options', $wrapper, $layout_id);
		
	}

	
	/* Box methods */
	public static function method_load_box_ajax_content() {
		
		$layout = headway_post('layout');
		$box_id = headway_post('box_id');
				
		do_action('headway_visual_editor_ajax_box_content_' . $box_id);
		
	}
	
	
	/* Layout methods */
	public static function method_get_layout_name() {
				
		$layout = headway_post('layout');
		
		echo HeadwayLayout::get_name($layout);
		
	}
	
	
	public static function secure_method_revert_layout() {
		
		$layout = headway_post('layout_to_revert');
		
		//Delete the blocks
		HeadwayBlocksData::delete_by_layout($layout);

		//Delete the wrappers
		HeadwayOption::delete($layout, 'wrappers');
		
		//Remove the customized flag
		HeadwayLayoutOption::set($layout, 'customized', false);

		do_action('headway_visual_editor_reset_layout');
		
		echo 'success';
		
	}


	/* Design editor methods */
	public static function method_get_element_inputs() {
		
		$element = headway_post('element');
		$special_element_type = headway_post('specialElementType', false);
		$special_element_meta = headway_post('specialElementMeta', false);
		$group = $element['group'];
		
		$unsaved_values = headway_post('unsavedValues', false);
		
		/* Make sure that the library is loaded */
		Headway::load('visual-editor/panels/design/property-inputs');

		/* Get values */
			if ( !$special_element_type && !$special_element_meta ) {

				$property_values = HeadwayElementsData::get_element_properties($element['id'], $group);
				$property_values_excluding_defaults = HeadwayElementsData::get_element_properties($element['id'], $group, true);

			} else {

				$property_values_args = array(
					'element' => $element['id'], 
					'se_type' => $special_element_type, 
					'se_meta' => $special_element_meta, 
					'element_group' => $group
				);

				$property_values = HeadwayElementsData::get_special_element_properties($property_values_args);
				$property_values_excluding_defaults = HeadwayElementsData::get_special_element_properties(array_merge($property_values_args, array('exclude_default_data' => true)));

			}

		/* Merge in the unsaved values */
			$property_values = is_array($unsaved_values) ? array_merge($property_values, $unsaved_values) : $property_values;
			$property_values_excluding_defaults = is_array($unsaved_values) ? array_merge($property_values_excluding_defaults, $unsaved_values) : $property_values_excluding_defaults;
	
		/* Display the appropriate inputs and values depending on the element */
		HeadwayPropertyInputs::display($element, $special_element_type, $special_element_meta, $property_values, $property_values_excluding_defaults);
	
	}
	

	public static function method_get_design_editor_elements() {

		$current_layout = headway_post('layout');
		$all_elements = HeadwayElementAPI::get_all_elements();
		$groups = HeadwayElementAPI::get_groups();

		$customized_element_data = HeadwayElementsData::get_all_elements(true);

		$elements = array();

		/* Assemble the arrays */
		foreach ( $all_elements as $group_id => $main_elements ) {
			
			$elements[$group_id] = array();

			/* Loop through main elements */
			foreach ( $main_elements as $main_element_id => $main_element_settings ) {

				/* Handle main element */
				$inherit_location = HeadwayElementAPI::get_element(HeadwayElementAPI::get_inherit_location($main_element_id));

				$elements[$group_id][$main_element_id] = array(
					'selector' => $main_element_settings['selector'],
					'id' => $main_element_settings['id'],
					'name' => $main_element_settings['name'],
					'description' => headway_get('description', $main_element_settings),					
					'properties' => $main_element_settings['properties'],
					'group' => $group_id,
					'groupName' => headway_get($group_id, $groups),
					'states' => headway_get('states', $main_element_settings, array()),
					'instances' => headway_get('instances', $main_element_settings, array()),
					'inherit-location' => headway_get('id', $inherit_location),
					'inherit-location-name' => headway_get('name', $inherit_location),
					'disallow-nudging' => headway_get('disallow-nudging', $main_element_settings, false),
					'indent-in-selector' => headway_get('indent-in-selector', $main_element_settings, false),
					'customized' => isset($customized_element_data[$main_element_settings['id']]) ? true : false,
					'children' => array()
				);

				/* Loop through main element instances and add customized flag if necessary */
					foreach ( $elements[$group_id][$main_element_id]['instances'] as $main_element_instance_id => $main_element_instance_settings ) {

						if ( isset($customized_element_data[$main_element_settings['id']]['special-element-instance'][$main_element_instance_id]) )
							$elements[$group_id][$main_element_id]['instances'][$main_element_instance_id]['customized'] = true;

					} 

				/* Loop through element children */
				foreach ( headway_get('children', $main_element_settings, array()) as $child_element_id => $child_element_settings ) {

					/* Handle child element */
					$inherit_location = HeadwayElementAPI::get_element(HeadwayElementAPI::get_inherit_location($child_element_id));

					$elements[$group_id][$main_element_id]['children'][$child_element_id] = array(
						'selector' => $child_element_settings['selector'],
						'id' => $child_element_settings['id'],
						'name' => $child_element_settings['name'],
						'description' => headway_get('description', $child_element_settings),
						'properties' => $child_element_settings['properties'],
						'group' => $group_id,
						'groupName' => headway_get($group_id, $groups),
						'parent' => $main_element_id,
						'parentName' => headway_get('name', $main_element_settings),
						'states' => headway_get('states', $child_element_settings, array()),
						'instances' => headway_get('instances', $child_element_settings, array()),
						'indent-in-selector' => headway_get('indent-in-selector', $child_element_settings, false),
						'inherit-location' => headway_get('id', $inherit_location),
						'inherit-location-name' => headway_get('name', $inherit_location),
						'disallow-nudging' => headway_get('disallow-nudging', $child_element_settings, false),
						'customized' => isset($customized_element_data[$child_element_settings['id']]) ? true : false,
					);

					/* Loop through sub element instances and add customized flag if necessary */
						foreach ( $elements[$group_id][$main_element_id]['children'][$child_element_id]['instances'] as $sub_element_instance_id => $sub_element_instance_settings ) {

							if ( isset($customized_element_data[$child_element_settings['id']]['special-element-instance'][$sub_element_instance_id]) )
								$elements[$group_id][$main_element_id]['children'][$child_element_id]['instances'][$sub_element_instance_id]['customized'] = true;

						} 


				}

			}
	
		}

		/* Spit it all out */
		self::json_encode($elements);

	}
	

	/* Template methods */
	public static function secure_method_add_template() {

		//Send the template ID back to JavaScript so it can be added to the list
		self::json_encode(HeadwayLayout::add_template(headway_post('template_name')));
		
	}
	
	
	public static function secure_method_delete_template() {
		
		//Retreive templates
		$templates = HeadwayOption::get('list', 'templates', array());
		
		//Unset the deleted ID
		$id = headway_post('template_to_delete');
		
		//Delete template if it exists and send array back to DB
		if ( isset($templates[$id]) ) {
			
			unset($templates[$id]);
			
			//Delete the blocks from the template
			HeadwayBlocksData::delete_by_layout('template-' . $id);

			//Delete all options from the template
			HeadwayLayoutOption::delete_all_from_layout('template-' . $id);
			
			//Delete template from templates list
			HeadwayOption::set('list', $templates, 'templates');

			do_action('headway_visual_editor_delete_template');
			
			echo 'success';
			
		} else {
			
			echo 'failure';
			
		}
		
	}
	
	
	public static function secure_method_assign_template() {
		
		$layout = headway_post('layout');
		$template = str_replace('template-', '', headway_post('template'));
		
		//Add the template flag
		HeadwayLayoutOption::set($layout, 'template', $template);

		do_action('headway_visual_editor_assign_template');
		
		echo HeadwayLayout::get_name('template-' . $template);
		
	}
	
	
	public static function secure_method_remove_template_from_layout() {
		
		$layout = headway_post('layout');
		
		//Remove the template flag
		if ( !HeadwayLayoutOption::set($layout, 'template', false) ) {
			echo 'failure';
			
			return;
		}
		
		if ( HeadwayLayoutOption::get($layout, 'customized', false) === true ) {
			echo 'customized';
			
			return;
		}

		do_action('headway_visual_editor_unassign_template');
			
		echo 'success';
		
	}
	
	
	/* Micellaneous methods */
	public static function method_clear_cache() {
		
		if ( HeadwayCompiler::flush_cache(true) && HeadwayBlocks::clear_block_actions_cache() )
			echo 'success';
		else
			echo 'failure';
		
	}

	
	public static function method_ran_tour() {
		
		$mode = headway_post('mode');

		HeadwayOption::set('ran-tour-' . $mode, true);
		
	}
	
	
	public static function method_change_grid_height() {
		
		$grid_height = headway_post('grid_height');		
		
		//Make sure the grid height is numeric and at least 800px
		if ( !is_numeric($grid_height) || $grid_height < 800 )
			return false;
						
		HeadwayOption::set('grid-height', $grid_height);
		
	}
	

	public static function method_fonts_list() {

		return do_action('headway_fonts_ajax_list_fonts_' . headway_post('provider'));

	}


	/* Data Portability */
		/* General Data Portability */
			public static function method_import_images() {

				Headway::load('data/data-portability');

				/* Set up variables */
					$import_file = headway_post('importFile');
					$image_definitions = headway_get('image-definitions', $import_file, array());

					$imported_images = array();

				/* Loop through base64'd images and move them to uploads directory */
					foreach ( $image_definitions as $image_id => $image )
						$imported_images[$image_id] = HeadwayDataPortability::decode_image_to_uploads($image['base64_contents']);

				/* Replace image variables in the import file */
					foreach ( $imported_images as $imported_image_id => $imported_image ) {

						/* Handle sideloading errors */
						if ( headway_get('error', $imported_image) ) {

							/* Replace entire array with error to stop import of settings */
							$import_file = array(
								'error' => headway_get('error', $imported_image)
							);

						} else if ( headway_get('url', $imported_image) ) {

							$import_file = self::import_images_recursive_replace($imported_image_id, $imported_image['url'], $import_file);

						}

					}

				/* Remove giant image definitions from import file */
					unset($import_file['image-definitions']);

				/* Send import file with images replaced back to Visual Editor */
					self::json_encode($import_file);

			}


				public static function import_images_recursive_replace($variable, $replace, $array) {

					if ( !is_array($array) )
						return str_replace($variable, $replace, $array);

					$processed_array = array();

					foreach ( $array as $key => $value )
						$processed_array[$key] = self::import_images_recursive_replace($variable, $replace, $value);

					return $processed_array;

				}


		/* Skin Portability */
			public static function method_export_skin() {

				Headway::load('data/data-portability');

				$skin_name = headway_get('skin-name', false, 'Unnamed');
				$include_live_css = headway_fix_data_type(headway_get('include-live-css', false, true));
				$included_templates = headway_get('included-templates', false);

				return HeadwayDataPortability::export_skin($skin_name, $include_live_css, $included_templates);

			}


		/* Layout Portability */
			public static function method_export_layout() {

				Headway::load('data/data-portability');

				$layout = headway_get('layout', false);				

				return HeadwayDataPortability::export_layout($layout);

			}


		/* Block Settings Portability */
			public static function method_export_block_settings() {

				Headway::load('data/data-portability');

				return HeadwayDataPortability::export_block_settings(headway_get('block-id'));

			}
	 
	
}