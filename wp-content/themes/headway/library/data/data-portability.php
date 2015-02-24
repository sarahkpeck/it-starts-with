<?php
class HeadwayDataPortability {


	public static function export_skin($skin_name = 'Unnamed', $include_live_css = false, $included_templates = false) {

		do_action('headway_before_export_skin');

		/* Set up variables */
			$element_data = HeadwayElementsData::get_all_elements();

			$skin = array(
				'name' => $skin_name,
				'element-data' => $element_data
			);

			if ( $include_live_css )
				$skin['live-css'] = HeadwayOption::get('live-css');

		/* If templates are to be included then query for them and convert all mirrored blocks into original blocks by pulling their mirror target's settings */
			if ( is_array($included_templates) ) {

				$skin['templates'] = array();

				$white_listed_block_instances = array();
				$white_listed_wrapper_instances = array();

				foreach ( $included_templates as $template_id ) {

					$template_name = HeadwayLayout::get_name('template-' . $template_id);
					$template_blocks = HeadwayBlocksData::get_blocks_by_layout('template-' . $template_id);
					$template_wrappers = HeadwayWrappers::get_layout_wrappers('template-' . $template_id);

					/* Loop through each block in the template and check if it's mirrored.  If it is, replace it with the block that it's mirroring */
						foreach ( $template_blocks as $template_block_index => $template_block ) {

							if ( !$mirrored_block = HeadwayBlocksData::is_block_mirrored($template_block) ) {

								/* Allow this block's instances to be in the skin */
								$white_listed_block_instances[] = $template_block['id'];

								/* Delete mirror option in case it's present */
								if ( isset($template_blocks[$template_block_index]['settings']['mirror-block']) )
									unset($template_blocks[$template_block_index]['settings']['mirror-block']);

								continue;

							}

							$template_blocks[$template_block_index]['id'] = $mirrored_block['id'];
							$template_blocks[$template_block_index]['settings'] = $mirrored_block['settings'];

							/* Allow this block's instances to be in the skin */
							$white_listed_block_instances[] = $mirrored_block['id'];
							
						}

					/* Whitelist wrapper instances */
						foreach ( $template_wrappers as $template_wrapper_id => $template_wrapper_settings )
							$white_listed_wrapper_instances[] = $template_wrapper_id;


					$skin['templates'][$template_name] = $template_blocks;
					$skin['templates'][$template_name]['wrappers'] = $template_wrappers;

				}

			}

		/* Need to remove instances that aren't in the included templates and layout specific customizations from $element_data here */
			foreach ( $skin['element-data'] as $element_id => $element ) {

				/* Add in the element group that way it can be saved @since 3.4.8 */
				$element_registration = HeadwayElementAPI::get_element($element_id);
				$skin['element-data'][$element_id]['group'] = $element_registration['group'];

				/* Remove instances that aren't in this skin's templates */
				if ( isset($element['special-element-instance']) ) {

					/* White list doesn't exist, just remove all instances */
					if ( !isset($white_listed_block_instances) ) {
						unset($skin['element-data'][$element_id]['special-element-instance']);
					
					/* White List Exists, loop through each instance and check its ID */
					} else {

						foreach ( $skin['element-data'][$element_id]['special-element-instance'] as $instance_id => $properties ) {

							$instance_id_fragments = explode('-', $instance_id);

							$instance_potential_block_id_search = preg_match('/\bblock\b\-[0-9]+/', $instance_id, $instance_potential_block_id_search_results);
							$instance_potential_block_id = str_replace('block-', '', end($instance_potential_block_id_search_results));
							$instance_potential_wrapper_id = $instance_id_fragments[0] . '-' . $instance_id_fragments[1];

							/* Wrapper instance conditional.  If a positive match, CONTINUE that way the unset doesn't happen */
								if ( strpos($instance_id, 'wrapper-') === 0 && in_array($instance_potential_wrapper_id, $white_listed_wrapper_instances) )
									continue;

							/* Block instance conditional.  If a positive match, CONTINUE that way the unset doesn't happen */
								else if ( strpos($instance_id, 'block-') !== false && is_numeric($instance_potential_block_id) && in_array($instance_potential_block_id, $white_listed_block_instances) )
									continue;

							/* Delete the instance if it doesn't match the block OR wrapper whitelist */
								unset($skin['element-data'][$element_id]['special-element-instance'][$instance_id]);

						}


					}

				}
				
				/* Remove layout-specific customizations from the skin */
				if ( isset($element['special-element-layout']) )
					unset($skin['element-data'][$element_id]['special-element-layout']);

			}

		/* Spit the file out */
		return self::to_json('Headway Skin - ' . $skin_name, 'skin', $skin);
		
	}


	public static function export_block_settings($block_id) {

		/* Set up variables */
			$block = HeadwayBlocksData::get_block($block_id);

		/* Check if block exists */
			if ( !$block )
				die('Error: Could not export block settings.');

		/* Spit the file out */
			return self::to_json('Block Settings - ' . HeadwayBlocksData::get_block_name($block), 'block-settings', array(
				'id' => $block_id,
				'type' => $block['type'],
				'settings' => $block['settings'],
				'styling' => HeadwayBlocksData::get_block_styling($block)
			));

	}


	public static function export_layout($layout_id) {

		/* Set up variables */
			if ( !$layout_name = HeadwayLayout::get_name($layout_id) )
				die('Error: Invalid layout.');

			$layout = array(
				'name' => $layout_name,
				'blocks' => HeadwayBlocksData::get_blocks_by_layout($layout_id)
			);

		/* Convert all mirrored blocks into original blocks by pulling their mirror target's settings */
			/* Loop through each block in the template and check if it's mirrored.  If it is, replace it with the block that it's mirroring */
			foreach ( $layout['blocks'] as $layout_block_index => $layout_block ) {

				if ( !$mirrored_block = HeadwayBlocksData::is_block_mirrored($layout_block) )
					continue;

				$layout['blocks'][$layout_block_index] = $mirrored_block;
				
			}

		/* Spit the file out */
		return self::to_json('Headway Layout - ' . $layout_name, 'layout', $layout);

	}


	/**
	 * Convert array to JSON file and force download.
	 * 
	 * Images will be converted to base64 via HeadwayDataPortability::encode_images()
	 **/
	public static function to_json($filename, $data_type = null, $array) {

		if ( !$array['data-type'] = $data_type )
			die('Missing data type for HeadwayDataPortability::to_json()');

		$array['image-definitions'] = self::encode_images($array);

		header('Content-Disposition: attachment; filename="' . $filename . '.json"');
		header('Content-Type: application/json');
		header('Pragma: no-cache');

		echo json_encode($array);

	}


		/**
		 * Convert all images to base64.
		 * 
		 * This method is recursive.
		 **/
		public static function encode_images(&$array, $images = null) {

			if ( !$images )
				$images = array();

			foreach ( $array as $key => $value ) {

				if ( is_array($value) ) {

					$images = array_merge($images, self::encode_images($array[$key], $images));
					continue;

				} else if ( !is_serialized($value) && is_string($value) ) {

					$image_matches = array();

					/* PREG_SET_ORDER makes the $image_matches array make more sense */
					preg_match_all('/([a-z\-_0-9\/\:\.]*\.(jpg|jpeg|png|gif))/i', $value, $image_matches, PREG_SET_ORDER); 

					/* Go through each image in the string and download it then base64 encode it and replace the URL with variable */
					foreach ( $image_matches as $image_match ) {

						if ( !count($image_match) )
							continue;

						$image_request = wp_remote_get($image_match[0]);

						if ( $image_request && $image_contents = wp_remote_retrieve_body($image_request) ) {

							$image = array(
								'base64_contents' => base64_encode($image_contents),
								'mime_type' => $image_request['headers']['content-type']
							);

							/* Add base64 encoded image to image definitions. */
								/* Make sure that the image isn't already in the definitions.  If it is, $possible_duplicate will be the key/ID to the image */
								if ( !$possible_duplicate = array_search($image, $images) )
									$images['%%IMAGE_REPLACEMENT_' . (count($images) + 1) . '%%'] = $image;

							/* Replace the URL with variable that way it can be replaced with uploaded image on import.  If $possible_duplicate isn't null/false, then use it! */
								$variable = $possible_duplicate ? $possible_duplicate : '%%IMAGE_REPLACEMENT_' . (count($images)) . '%%';
								$array[$key] = str_replace($image_match[0], $variable, $array[$key]);

						}

					}
	
				}

			}

			return $images;

		}


	/**
	 * Convert base64 encoded image into a file and move it to proper WP uploads directory.
	 **/
	public static function decode_image_to_uploads($base64_string) {

		/* Make sure user has permissions to edit in the Visual Editor */
			if ( !HeadwayCapabilities::can_user_visually_edit() )
				return;

		/* Create a temporary file and decode the base64 encoded image into it */
			$temporary_file = wp_tempnam();
			file_put_contents($temporary_file, base64_decode($base64_string));

		/* Use wp_check_filetype_and_ext() to figure out the real mimetype of the image.  Provide a bogus extension and then we'll use the 'proper_filename' later. */
			$filename = 'headway-imported-image.jpg';
			$file_information = wp_check_filetype_and_ext($temporary_file, $filename);

		/* Construct $file array which is similar to a PHP $_FILES array.  This array must be a variable since wp_handle_sideload() requires a variable reference argument. */
			if ( headway_get('proper_filename', $file_information) )
				$filename = $file_information['proper_filename'];

			$file = array(
				'name' => $filename,
				'tmp_name' => $temporary_file
			);

		/* Let WordPress move the image and spit out the file path, URL, etc.  Set test_form to false that way it doesn't verify $_POST['action'] */
			$upload = wp_handle_sideload($file, array('test_form' => false));

			/* If there's an error, be sure to unlink/delete the temporary file in case wp_handle_sideload() doesn't. */
			if ( isset($upload['error']) )
				@unlink($temporary_file);

			return $upload;

	}


}