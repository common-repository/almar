<?php
/* Text */
add_filter( 'ws_sanitize_text', 'sanitize_text_field' );

/* Password */

add_filter( 'ws_sanitize_password', 'sanitize_text_field' );

/* Textarea */

function ws_sanitize_textarea($input) {
	global $allowedposttags;
	$output = wp_kses( $input, $allowedposttags);
	return $output;
}

add_filter( 'ws_sanitize_textarea', 'ws_sanitize_textarea' );

/* Select */

add_filter( 'ws_sanitize_select', 'ws_sanitize_enum', 10, 2);

/* Radio */

add_filter( 'ws_sanitize_radio', 'ws_sanitize_enum', 10, 2);

/* Images */

add_filter( 'ws_sanitize_images', 'ws_sanitize_enum', 10, 2);

/* Checkbox */

function ws_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = '1';
	} else {
		$output = false;
	}
	return $output;
}
add_filter( 'ws_sanitize_checkbox', 'ws_sanitize_checkbox' );

/* Multicheck */

function ws_sanitize_multicheck( $input, $option ) {
	$output = '';
	if ( is_array( $input ) ) {
		foreach( $option['options'] as $key => $value ) {
			$output[$key] = false;
		}
		foreach( $input as $key => $value ) {
			if ( array_key_exists( $key, $option['options'] ) && $value ) {
				$output[$key] = "1";
			}
		}
	}
	return $output;
}
add_filter( 'ws_sanitize_multicheck', 'ws_sanitize_multicheck', 10, 2 );

/* Color Picker */

add_filter( 'ws_sanitize_color', 'ws_sanitize_hex' );

/* Uploader */

function ws_sanitize_upload( $input ) {
	$output = '';
	$filetype = wp_check_filetype($input);
	if ( $filetype["ext"] ) {
		$output = $input;
	}
	return $output;
}
add_filter( 'ws_sanitize_upload', 'ws_sanitize_upload' );

/* Editor */

function ws_sanitize_editor($input) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$output = $input;
	}
	else {
		global $allowedtags;
		$output = wpautop(wp_kses( $input, $allowedtags));
	}
	return $output;
}
add_filter( 'ws_sanitize_editor', 'ws_sanitize_editor' );

/* Allowed Tags */

function ws_sanitize_allowedtags($input) {
	global $allowedtags;
	$output = wpautop(wp_kses( $input, $allowedtags));
	return $output;
}

/* Allowed Post Tags */

function ws_sanitize_allowedposttags($input) {
	global $allowedposttags;
	$output = wpautop(wp_kses( $input, $allowedposttags));
	return $output;
}

add_filter( 'ws_sanitize_info', 'ws_sanitize_allowedposttags' );


/* Check that the key value sent is valid */

function ws_sanitize_enum( $input, $option ) {
	$output = '';
	if ( array_key_exists( $input, $option['options'] ) ) {
		$output = $input;
	}
	return $output;
}

/* Background */

function ws_sanitize_background( $input ) {
	$output = wp_parse_args( $input, array(
		'color' => '',
		'image'  => '',
		'repeat'  => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	) );

	$output['color'] = apply_filters( 'ws_sanitize_hex', $input['color'] );
	$output['image'] = apply_filters( 'ws_sanitize_upload', $input['image'] );
	$output['repeat'] = apply_filters( 'ws_background_repeat', $input['repeat'] );
	$output['position'] = apply_filters( 'ws_background_position', $input['position'] );
	$output['attachment'] = apply_filters( 'ws_background_attachment', $input['attachment'] );

	return $output;
}
add_filter( 'ws_sanitize_background', 'ws_sanitize_background' );

function ws_sanitize_background_repeat( $value ) {
	$recognized = ws_recognized_background_repeat();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'ws_default_background_repeat', current( $recognized ) );
}
add_filter( 'ws_background_repeat', 'ws_sanitize_background_repeat' );

function ws_sanitize_background_position( $value ) {
	$recognized = ws_recognized_background_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'ws_default_background_position', current( $recognized ) );
}
add_filter( 'ws_background_position', 'ws_sanitize_background_position' );

function ws_sanitize_background_attachment( $value ) {
	$recognized = ws_recognized_background_attachment();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'ws_default_background_attachment', current( $recognized ) );
}
add_filter( 'ws_background_attachment', 'ws_sanitize_background_attachment' );


/* Typography */

function ws_sanitize_typography( $input, $option ) {

	$output = wp_parse_args( $input, array(
		'size'  => '',
		'face'  => '',
		'style' => '',
		'color' => ''
	) );

	if ( isset( $option['options']['faces'] ) && isset( $input['face'] ) ) {
		if ( !( array_key_exists( $input['face'], $option['options']['faces'] ) ) ) {
			$output['face'] = '';
		}
	}
	else {
		$output['face']  = apply_filters( 'ws_font_face', $output['face'] );
	}

	$output['size']  = apply_filters( 'ws_font_size', $output['size'] );
	$output['style'] = apply_filters( 'ws_font_style', $output['style'] );
	$output['color'] = apply_filters( 'ws_sanitize_color', $output['color'] );
	return $output;
}
add_filter( 'ws_sanitize_typography', 'ws_sanitize_typography', 10, 2 );

function ws_sanitize_font_size( $value ) {
	$recognized = ws_recognized_font_sizes();
	$value_check = preg_replace('/px/','', $value);
	if ( in_array( (int) $value_check, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'ws_default_font_size', $recognized );
}
add_filter( 'ws_font_size', 'ws_sanitize_font_size' );


function ws_sanitize_font_style( $value ) {
	$recognized = ws_recognized_font_styles();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'ws_default_font_style', current( $recognized ) );
}
add_filter( 'ws_font_style', 'ws_sanitize_font_style' );


function ws_sanitize_font_face( $value ) {
	$recognized = ws_recognized_font_faces();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'ws_default_font_face', current( $recognized ) );
}
add_filter( 'ws_font_face', 'ws_sanitize_font_face' );

/**
 * Get recognized background repeat settings
 *
 * @return   array
 *
 */
function ws_recognized_background_repeat() {
	$default = array(
		'no-repeat' => __('No Repeat', 'wpthemeseo'),
		'repeat-x'  => __('Repeat Horizontally', 'wpthemeseo'),
		'repeat-y'  => __('Repeat Vertically', 'wpthemeseo'),
		'repeat'    => __('Repeat All', 'wpthemeseo'),
		);
	return apply_filters( 'ws_recognized_background_repeat', $default );
}

/**
 * Get recognized background positions
 *
 * @return   array
 *
 */
function ws_recognized_background_position() {
	$default = array(
		'top left'      => __('Top Left', 'wpthemeseo'),
		'top center'    => __('Top Center', 'wpthemeseo'),
		'top right'     => __('Top Right', 'wpthemeseo'),
		'center left'   => __('Middle Left', 'wpthemeseo'),
		'center center' => __('Middle Center', 'wpthemeseo'),
		'center right'  => __('Middle Right', 'wpthemeseo'),
		'bottom left'   => __('Bottom Left', 'wpthemeseo'),
		'bottom center' => __('Bottom Center', 'wpthemeseo'),
		'bottom right'  => __('Bottom Right', 'wpthemeseo')
		);
	return apply_filters( 'ws_recognized_background_position', $default );
}

/**
 * Get recognized background attachment
 *
 * @return   array
 *
 */
function ws_recognized_background_attachment() {
	$default = array(
		'scroll' => __('Scroll Normally', 'wpthemeseo'),
		'fixed'  => __('Fixed in Place', 'wpthemeseo')
		);
	return apply_filters( 'ws_recognized_background_attachment', $default );
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */

function ws_sanitize_hex( $hex, $default = '' ) {
	if ( ws_validate_hex( $hex ) ) {
		return $hex;
	}
	return $default;
}

/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */

function ws_recognized_font_sizes() {
	$sizes = range( 9, 71 );
	$sizes = apply_filters( 'ws_recognized_font_sizes', $sizes );
	$sizes = array_map( 'absint', $sizes );
	return $sizes;
}

/**
 * Get recognized font faces.
 *
 * Returns an array of all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function ws_recognized_font_faces() {
	$default = array(
		'arial'     => 'Arial',
		'verdana'   => 'Verdana, Geneva',
		'trebuchet' => 'Trebuchet',
		'georgia'   => 'Georgia',
		'times'     => 'Times New Roman',
		'tahoma'    => 'Tahoma, Geneva',
		'palatino'  => 'Palatino',
		'helvetica' => 'Helvetica*'
		);
	return apply_filters( 'ws_recognized_font_faces', $default );
}

/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function ws_recognized_font_styles() {
	$default = array(
		'normal'      => __( 'Normal', 'wpthemeseo' ),
		'italic'      => __( 'Italic', 'wpthemeseo' ),
		'bold'        => __( 'Bold', 'wpthemeseo' ),
		'bold italic' => __( 'Bold Italic', 'wpthemeseo' )
		);
	return apply_filters( 'ws_recognized_font_styles', $default );
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */

function ws_validate_hex( $hex ) {
	$hex = trim( $hex );
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return false;
	}
	else {
		return true;
	}
}