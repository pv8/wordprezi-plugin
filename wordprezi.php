<?php
/**
 * Plugin Name: WordPrezi
 * Plugin URI: http://wordprezi.appspot.com/plugin
 * Description: Easy way to embed Prezi presentations in Wordpress blogposts
 * Version: 0.8.1
 * Author: Pablo
 * Author URI: http://pv8.io
 * License: GPLv3
 */

/*  Copyright 2013  WordPrezi  (email : wordprezi@pv8.io)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//define('WP_DEBUG', true);

define( 'URL_PATTERN', "/^(https?):\/\/.*prezi\.com\/(.+)/" );

function validate_params( $atts ) {

	extract( shortcode_atts( array(
		'url' => null,
		'width' => 500,
		'height' => 400,
		'zoom_freely' => 'N',
		'use_html5' => 'N'
	), $atts ) );

	$err_msg = '';

	// check URL
	if ( !$url || !preg_match( URL_PATTERN, $url ) ) {
		$err_msg .= "- invalid Prezi URL (found '$url')<br>";
	}

	// check width and height
	if ( ! is_numeric( $width ) || ! is_numeric( $height ) ) {
		$err_msg .= "- width/height must be numeric (found width='$width', height='$height')<br>";
	}

	if ( $err_msg ) {
		return "<em><strong>[WordPrezi plugin error:</strong><br>" . PHP_EOL .
			$err_msg . "<strong>]</strong></em>";
	} else {
		return null;
	}
}

function wordprezi_shortcode( $atts ) {

	$params_errors = validate_params( $atts );

	if ( $params_errors ) {
		return $params_errors;
	}

	extract( shortcode_atts( array(
		'url' => null,
		'width' => 550,
		'height' => 400,
		'zoom_freely' => 'N',
		'use_html5' => 'N'
	), $atts ) );

	preg_match( URL_PATTERN, $url, $url_parts );

	$path_parts = explode('/', rtrim($url_parts[2], '/'));
	
	$lock_to_path = ( strtoupper( $zoom_freely ) === 'Y'? 0: 1 );
	$html5 = ( strtoupper( $use_html5 ) === 'Y'? 1: 0 );

	if (count($path_parts) > 1 && $path_parts[0] == 'p') {
		$prezi_id = $path_parts[1];
		return "<!-- begin WordPrezi (Prezi Next)-->" . PHP_EOL .
		"<iframe src='https://prezi.com/p/{$prezi_id}/embed?bgcolor=ffffff&amp;" .
		"lock_to_path={$lock_to_path}&amp;autoplay=no&amp;autohide_ctrls=0" .
		"&amp;features=undefined&amp;disabled_features=undefined" .
		"&amp;html5={$html5}' " .
		"width='{$width}' height='{$height}' frameBorder='0'" .
		"webkitAllowFullScreen='1' mozAllowFullscreen='1' allowfullscreen='1'>" .
		"</iframe>" . PHP_EOL .
		"<!-- end WordPrezi -->" . PHP_EOL;
	} elseif (count($path_parts) > 1 && $path_parts[0] == 'view') {
		$prezi_id = $path_parts[1];
		return "<!-- begin WordPrezi -->" . PHP_EOL .
		"<iframe src='https://prezi.com/view/{$prezi_id}/embed" .
		"width='{$width}' height='{$height}' " .
		"webkitallowfullscreen='1' mozallowfullscreen='1' allowfullscreen='1'>" .
		"</iframe>" . PHP_EOL .
		"<!-- end WordPrezi -->" . PHP_EOL;
	} else {
		$prezi_id = $path_parts[0];
		return "<!-- begin WordPrezi (Prezi Classic) -->" . PHP_EOL .
		"<iframe src='https://prezi.com/embed/{$prezi_id}/?bgcolor=ffffff&amp;" .
		"lock_to_path={$lock_to_path}&amp;autoplay=no&amp;autohide_ctrls=0" .
		"&amp;features=undefined&amp;disabled_features=undefined" .
		"&amp;html5={$html5}' " .
		"width='{$width}' height='{$height}' frameBorder='0'" .
		"webkitAllowFullScreen mozAllowFullscreen allowfullscreen>" .
		"</iframe>" . PHP_EOL .
		"<!-- end WordPrezi -->" . PHP_EOL;
	}
}

add_shortcode( 'prezi', 'wordprezi_shortcode' );

?>
