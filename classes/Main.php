<?php

namespace BEAPI\Algolia_Content_Exclude;

use function add_action;
use function add_filter;
use function get_post_types;
use function WPSentry\ScopedVendor\Clue\StreamFilter\fun;

/**
 * The purpose of the main class is to init all the plugin base code like :
 *  - Taxonomies
 *  - Post types
 *  - Shortcodes
 *  - Posts to posts relations etc.
 *  - Loading the text domain
 *
 * Class Main
 * @package BEAPI\Algolia_Content_Exclude
 */
class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init(): void {
		add_action( 'init', [ $this, 'init_translations' ] );
		add_action( 'init', [ $this, 'register_meta' ], 50 );

		add_filter( 'algolia_should_index_post', [ $this, 'should_index' ], 10, 2 );
		add_filter( 'algolia_should_index_searchable_post', [$this, 'should_index'], 10, 2 );
	}

	/**
	 * Load the plugin translation
	 */
	public function init_translations(): void {
		// Load translations
		load_plugin_textdomain( 'algolia-content-exclude', false, ALGOLIA_CONTENT_EXCLUDE_PLUGIN_DIRNAME . '/languages' );
	}

	/**
	 * Change the should index based on the metabox value
	 *
	 * @param          $should_index
	 * @param \WP_Post $post
	 *
	 * @return bool
	 * @author Nicolas JUEN
	 */
	public function should_index( $should_index, \WP_Post $post ): bool {
		$meta = (bool) \get_post_meta( $post->ID, '_algolia_content_exclude', true );

		return ! ( true === $meta );
	}

	/**
	 * Register the hidden metadata for the sidebar.
	 *
	 * @author Nicolas JUEN
	 */
	public function register_meta(): void {
		$post_types = get_post_types( [ 'show_ui' => true ] );

		foreach ( $post_types as $post_type ) {
			register_post_meta( 'post', '_algolia_content_exclude', [
				'object_subtype'    => $post_type,
				'show_in_rest'      => true,
				'single'            => true,
				'description'       => 'Allow the content to be excluded into Algolia',
				'default'           => false,
				'type'              => 'boolean',
				'sanitize_callback' => function ( $value ) {
					return (bool) $value;
				},
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			] );
		}
	}
}
