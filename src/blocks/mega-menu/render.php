<?php
/**
 * Server-side render template for the Easy Mega Menu block.
 *
 * @package Easy_Mega_Menu
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $attributes['menuId'] ) ) {
	return;
}

echo EMM_Frontend::instance()->render( $attributes['menuId'] );
