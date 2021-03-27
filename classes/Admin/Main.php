<?php

namespace BEAPI\Algolia_Content_Exclude\Admin;

use BEAPI\Algolia_Content_Exclude\Singleton;
use WP_Post;
use function delete_post_meta;
use function file_exists;
use function file_get_contents;
use function get_post_meta;
use function update_post_meta;
use function wp_register_script;
use const ALGOLIA_CONTENT_EXCLUDE_DIR;
use const ALGOLIA_CONTENT_EXCLUDE_URL;

/**
 * Basic class for Admin
 *
 * Class Main
 * @package BEAPI\Algolia_Content_Exclude\Admin
 */
class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	public function init(): void {
		add_action( 'admin_init', [ $this, 'register_script' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_script' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ], 10, 2 );
		add_action( 'transition_post_status', [ $this, 'process_metabox' ], 10, 3 );
	}

	/**
	 * Register the scripts for admin gutenberg
	 *
	 * @author Nicolas JUEN
	 */
	public function register_script(): void {
		$file = ALGOLIA_CONTENT_EXCLUDE_DIR . '/assets/build/index.asset.php';
		if ( ! file_exists( $file ) ) {
			return;
		}

		$script_asset = require $file;
		wp_register_script(
			'algolia-content-exclude',
			ALGOLIA_CONTENT_EXCLUDE_URL . '/assets/build/index.js',
			$script_asset['dependencies'],
			$script_asset['version']
		);
	}

	/**
	 * Enqueue Gutenberg scripts
	 *
	 * @author Nicolas JUEN
	 */
	public function enqueue_script(): void {
		wp_enqueue_script( 'algolia-content-exclude' );
	}

	/**
	 * Register the metabox
	 *
	 * @param string $post_type
	 * @param WP_Post $post
	 *
	 * @author Nicolas JUEN
	 */
	public function add_meta_box( $post_type, $post ): void {
		if ( ! ( $post instanceof WP_Post ) ) {
			return;
		}

		add_meta_box(
			'algolia-content-exclude',
			'Algolia Content Exclude',
			[ $this, 'metabox' ],
			$post_type,
			'normal',
			'high',
			[
				'__back_compat_meta_box' => true,
			]
		);
	}

	/**
	 * Displays the metabox content
	 *
	 * @param WP_Post $content
	 *
	 * @author Nicolas JUEN
	 */
	public function metabox( $content ): void {
		wp_nonce_field( 'algolia-content-exclude-' . $content->ID, 'algolia-content-exclude-nonce' ); ?>
		<p>
			<input type="checkbox" id="algolia-content-exclude-checkbox" name="algolia-content-exclude" value="1" <?php checked( get_post_meta( $content->ID, '_algolia_content_exclude', true ) ); ?> />
			<label for="algolia-content-exclude-checkbox">
				<?php esc_html_e( 'Exclude from Algolia', 'algolia-content-exclude' ); ?>
			</label>
		</p>
		<?php
	}

	public function process_metabox( string $new_status, string $old_status, WP_Post $content ): void {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $content ) ) {
			return;
		}

		if ( ! isset( $_POST['algolia-content-exclude-nonce'] ) || ! wp_verify_nonce( $_POST['algolia-content-exclude-nonce'], 'algolia-content-exclude-' . $content->ID ) ) {
			return;
		}

		if ( isset( $_POST['algolia-content-exclude'] ) ) {
			update_post_meta( $content->ID, '_algolia_content_exclude', (bool) $_POST['algolia-content-exclude'] );
			return;
		}

		delete_post_meta( $content->ID, '_algolia_content_exclude' );
	}
}
