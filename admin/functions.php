<?php

/**
 * ADMINISTRATION
 */

/**
 * Register widget location within the template
 *
 * @return  void
 * @since   1.0
 */
function shprinkone_theme_options_init() {
	register_setting(
	'shprinkone_options', // Options group, see settings_fields() call in twentyeleven_theme_options_render_page()
	'shprinkone_theme_options', // Database option, see twentyeleven_get_theme_options()
	'shprinkone_theme_options_validate' // The sanitization callback, see twentyeleven_theme_options_validate()
	);
	// Register our settings field group
	add_settings_section(
	'general', // Unique identifier for the settings section
	'', // Section title (we don't want one)
	'__return_false', // Section callback (we don't want anything)
	'theme_options' // Menu slug, used to uniquely identify the page; see twentyeleven_theme_options_add_page()
	);

	add_settings_field('layout', __('Default Layout', 'shprinkone'), 'shprinkone_settings_field_layout', 'theme_options', 'general');
	add_settings_field('template', __('Default Template', 'shprinkone'), 'shprinkone_settings_field_template', 'theme_options', 'general');
}

add_action('admin_init', 'shprinkone_theme_options_init');

/**
 * Add theme admin page
 *
 * @return  void
 * @since   1.0
*/
function shprinkone_add_theme_page() {
	$theme_page = add_theme_page(
			__('Theme Options', 'shprinkone'), // Name of page
			__('Theme Options', 'shprinkone'), // Label in menu
			'edit_theme_options', // Capability required
			'theme_options', // Menu slug, used to uniquely identify the page
			'shprinkone_theme_options_render' // Function that renders the options page
	);

	if (!$theme_page)
		return;
}

add_action('admin_menu', 'shprinkone_add_theme_page');

/**
 * Render options
 *
 * @return  void
 * @since   1.0
*/
function shprinkone_theme_options_render() {
	$theme_name = wp_get_theme();
	echo '<div class="wrap">';
	screen_icon();
	echo '<h2>' . sprintf(__('%s Theme Options', 'shprinkone'), $theme_name) . '</h2>';
	settings_errors();
	echo '<form method="post" action="options.php">';
	settings_fields('shprinkone_options');
	do_settings_sections('theme_options');
	submit_button();
	echo'</form></div>';
}
/**
 * Set field layout
 *
 * @return  void
 * @since   1.0
 */
function shprinkone_settings_field_layout() {
	$options = shprinkone_get_theme_options();

	echo '<table class="widefat">';
	echo '<thead><tr>';
	echo '<th style="width:10px;"></th>';
	echo '<th>' . __('Thumbnail', 'shprinkone') . '</th>';
	echo '<th>' . __('Position', 'shprinkone') . '</th>';
	echo '</tr></thead>';
	echo '<tbody>';

	$i = 0;
	foreach (shprinkone_layouts() as $key => $layout) {
		$checked = ($options['theme_layout'] == $key) ? 'checked="checked"' : '';
		echo ($i %2 == 0)?  '<tr>' : '<tr class="alternate">';
		echo '<td><input type="radio" name="shprinkone_theme_options[theme_layout]" value="'.$layout['value'].'" ' . $checked . '></td>';
		echo '<td><img src="' . esc_url($layout['thumbnail']) . '" width="80" height="80" alt="" /></td>';
		echo '<td>' . $layout['label'] .'</td>';
		echo '</tr>';
		$i++;
	}
	echo '<tbody>';
	echo '</table>';
}
/**
 * Set field template
 *
 * @return  void
 * @since   1.0
 */
function shprinkone_settings_field_template() {
	$options = shprinkone_get_theme_options();
	// Template selection
	echo '<table class="widefat">';
	echo '<thead><tr>';
	echo '<th style="width:10px;"></th>';
	echo '<th>' . __('Colors', 'shprinkone') . '<br/>[@navbarBackground, @bodyBackground, @linkColor, @textColor]</th>';
	echo '<th>' . __('Name', 'shprinkone') . '</th>';
	echo '<th>' . __('Description', 'shprinkone') . '</th>';
	echo '<th>' . __('Author', 'shprinkone') . '</th>';
	echo '</tr></thead>';
	echo '<tbody>';
	$i = 0;
	foreach (shprinkone_get_theme_templates() as $key => $template) {
		$checked = ($options['theme_template'] == $key) ? 'checked="checked"' : '';
		echo ($i %2 == 0)?  '<tr>' : '<tr class="alternate">';
		echo '<td><input type="radio" name="shprinkone_theme_options[theme_template]" value="'.$template['value'].'" ' . $checked . '></td>';
		echo '<td>' . shprinkone_format_template_colors($template['colors']) .'</td>';
		echo '<td>' . $template['name'] .'</td>';
		echo '<td>' . $template['description'] .'</td>';
		echo '<td>' . $template['author'] .'</td>';
		echo '</tr>';
		$i++;
	}
	echo '<tbody>';
	echo '</table>';
}

function shprinkone_format_template_colors($colors){
	$html = '';
	if (!is_array($colors)){
		return $html;
	}
	$html .= '<table><tbody><tr>';
	foreach ($colors as $color){
		$html .= '<td style="height: 5px; width: 5px; background-color:'. $color .'; border:none;"></td>';
	}
	$html .= '</tbody></tr></table>';
	return $html;
}

/**
 * Get theme options
 *
 * @return  void
 * @since   1.0
 */
function shprinkone_get_theme_options() {
	return get_option('shprinkone_theme_options', shprinkone_get_default_theme_options());
}

/**
 * Get default theme options
 *
 * @return  void
 * @since   1.0
 */
function shprinkone_get_default_theme_options() {
	$default_theme_options = array(
			'theme_layout' => 'content-sidebar',
			'theme_template' => 'cyborg'
	);

	return apply_filters('shprinkone_default_theme_options', $default_theme_options);
}

/**
 * Set Layouts
 *
 * @return  mixed
 * @since   1.0
 */
function shprinkone_layouts() {
	$layout_options = array();
	$layout_options['content-sidebar'] = array(
			'value' => 'content-sidebar',
			'label' => __('Content on left', 'shprinkone'),
			'thumbnail' => get_template_directory_uri() . '/img/content-sidebar.png',
	);
	$layout_options['sidebar-content'] = array(
			'value' => 'sidebar-content',
			'label' => __('Content on right', 'shprinkone'),
			'thumbnail' => get_template_directory_uri() . '/img/sidebar-content.png',
	);
	$layout_options['content'] = array(
			'value' => 'content',
			'label' => __('One-column, no sidebar', 'shprinkone'),
			'thumbnail' => get_template_directory_uri() . '/img/content.png',
	);
	return apply_filters('shprinkone_layouts', $layout_options);
}

/**
 * Templates raw data
 *
 * @return  mixed
 * @since   1.0
 */
function shprinkone_get_theme_templates() {
	$templateList = array();
	$templateList['bootstrap'] = array(
			'name'=> 'Bootstrap classic',
			'value'=> 'bootstrap',
			'author'=> 'http://twitter.github.com/bootstrap/',
			'path' =>'/css/bootstrap.min.css',
			'colors' => array(),
			'description' => __('Sleek, intuitive, and powerful front-end framework for faster and easier web development.', 'shprinkone')
	);
	$templateList['cerulean'] = array(
			'name'=> 'Cerulean',
			'value' => 'cerulean',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/cerulean.bootswatch.min.css',
			'colors' => array('#2FA4E7','#FFFFFF','#2FA4E7','#555555'),
			'description' => __('A calm, blue sky.', 'shprinkone')
	);
	$templateList['cosmo'] = array(
			'name'=> 'Cosmo',
			'value'=> 'cosmo',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/cosmo.bootswatch.min.css',
			'colors' => array('#080808','#FFFFFF', '#007FFF', '#555555'),
			'description' => __('An ode to Metro.', 'shprinkone')
	);
	$templateList['cyborg'] = array(
			'name'=> 'Cyborg',
			'value'=> 'cyborg',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/cyborg.bootswatch.min.css',
			'colors' => array('#020202','#060606','#33B5E5','#999999'),
			'description' => __('Jet black and electric blue.', 'shprinkone')
	);
	$templateList['amelia'] = array(
			'name'=> 'Amelia',
			'value'=> 'amelia',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/amelia.bootswatch.min.css',
			'colors' => array('#AD1D28','#003F4D','#DEBB27','#FFFFFF'),
			'description' => __('Sweet and cheery.', 'shprinkone')
	);
	$templateList['readable'] = array(
			'name'=> 'Readable',
			'value'=> 'readable',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/readable.bootswatch.min.css',
			'colors' => array('#F6F6F6', '#F6F6F6', '#E78B24' , '#333333'),
			'description' => __('Optimized for legibility.', 'shprinkone')
	);
	$templateList['slate'] = array(
			'name'=> 'Slate',
			'value'=> 'slate',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/slate.bootswatch.min.css',
			'colors' => array('#272B30', '#272B30','#FFFFFF', '#C8C8C8'),
			'description' => __('Shades of gunmetal gray.', 'shprinkone')
	);
	$templateList['united'] = array(
			'name'=> 'United',
			'value'=> 'united',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/united.bootswatch.min.css',
			'colors' => array('#DD4814', '#FFFFFF', '#DD4814', '#333333'),
			'description' => __('Ubuntu orange and unique font.', 'shprinkone')
	);
	$templateList['spacelab'] = array(
			'name'=> 'Spacelab',
			'value'=> 'spacelab',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/spacelab.bootswatch.min.css',
			'colors' => array('#EEEEEE', '#FFFFFF', '#09d', '#666666'),
			'description' => __('Silvery and sleek.', 'shprinkone')
	);
	$templateList['spruce'] = array(
			'name'=> 'Spruce',
			'value'=> 'spruce',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/spruce.bootswatch.min.css',
			'colors' => array('#013435', '#FFFFFF', '#015B4E', '#555555'),
			'description' => __('Camping in the woods.', 'shprinkone')
	);
	$templateList['simplex'] = array(
			'name'=> 'Simplex',
			'value'=> 'simplex',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/simplex.bootswatch.min.css',
			'colors' => array('#fefefe', '#F7F7F7', '#D9230F', '#555555'),
			'description' => __('Mini and minimalist.', 'shprinkone')
	);
	$templateList['journal'] = array(
			'name'=> 'Journal',
			'value'=> 'journal',
			'author'=> 'http://bootswatch.com/',
			'path' =>'/css/journal.bootswatch.min.css',
			'colors' => array('#FFFFFF', '#FFFFFF', '#777777', '#777777'),
			'description' => __('Crisp like a new sheet of paper.', 'shprinkone')
	);
	$templateList['custom'] = array(
			'name'=> 'Custom',
			'value'=> 'custom',
			'author'=> 'none',
			'path' =>'/css/custom.css',
			'colors' => array(),
			'description' => __('An empty CSS file that you can modify as you like.', 'shprinkone')
	);
	return apply_filters('shprinkone_get_theme_templates', $templateList);
}

/**
 * Validate theme options
 *
 * @return  mixed
 * @since   1.0
 */
function shprinkone_theme_options_validate($input) {
	$output = $defaults = shprinkone_get_default_theme_options();

	// Theme layout must be in our array of theme layout options
	if (isset($input['theme_layout']) && array_key_exists($input['theme_layout'], shprinkone_layouts()))
		$output['theme_layout'] = $input['theme_layout'];

	if (isset($input['theme_template']) && array_key_exists($input['theme_template'], shprinkone_get_theme_templates()))
		$output['theme_template'] = $input['theme_template'];

	return apply_filters('shprinkone_theme_options_validate', $output, $input, $defaults);
}

