<?php
/* Custom Settings API Functions */
function adapt_do_settings_sections($page) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
		return;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		echo '
			<div class="postbox">
			<!--<div title="Click to toggle" class="handlediv"><br /></div>-->
			';
		if ( $section['title'] )
			echo "<h3 style=\"cursor: inherit;\">{$section['title']}</h3>\n";
		echo '<div class="inside">';
		call_user_func($section['callback'], $section);
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
			continue;
		
		do_settings_fields($page, $section['id']);
		echo '</div>';
		echo '</div>';
	}
}

function adapt_do_settings_fields($page, $section) {
	global $wp_settings_fields;

	if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
		return;

	foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
		if ( !empty($field['args']['label_for']) )
			echo '<label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label>';
		else
			echo $field['title'];
		call_user_func($field['callback'], $field['args']);
	}
}
?>