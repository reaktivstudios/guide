<?php
/**
 * Sync local docs with the DB.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\Local_Docs;

use function RKV\Guide\get_image_src;
use function RKV\Guide\get_docs_path;

/**
 * Sync class.
 */
class Sync {
	/**
	 * List of doc files.
	 *
	 * @var array
	 */
	private array $docs = [];

	/**
	 * List of synced docs.
	 *
	 * @var array
	 */
	private array $synced = [];

	/**
	 * Indicates if the docs were updated.
	 *
	 * @var boolean
	 */
	private bool $updated = false;

	/**
	 * The synced docs key.
	 *
	 * @var string
	 */
	private string $synced_key = 'rkv_site_guide_synced_docs';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * Initialize the sync.
	 */
	public function init() {
		$this->set_docs();
		$this->maybe_sync();
	}

	/**
	 * Set the docs.
	 */
	private function set_docs() {
		$docs_path = get_docs_path();

		// Get a glob of all doc files.
		$php_docs = glob( $docs_path . '**/*.php' );
		$md_docs  = glob( $docs_path . '**/*.md' );
		$docs     = array_merge( $php_docs, $md_docs );

		foreach ( $docs as $doc ) {
			$dirname  = dirname( $doc );
			$basename = basename( $dirname );

			$this->docs[ $basename ] = [
				'path' => $doc,
				'time' => filemtime( $doc ),
			];
		}
	}

	/**
	 * Checks to see if the docs need to be synced.
	 *
	 * @return void
	 */
	private function maybe_sync() {
		$this->synced = get_option( $this->synced_key, [] );

		foreach ( $this->docs as $slug => $doc ) {
			if ( ! isset( $this->synced[ $slug ] ) || $this->synced[ $slug ] !== $doc['time'] ) {
				$this->sync_doc( $slug, $doc );
			}
		}

		if ( $this->updated ) {
			update_option( $this->synced_key, $this->synced );
		}
	}

	/**
	 * Sync the doc.
	 *
	 * @param  string $slug The doc slug.
	 * @param  string $doc  The doc data.
	 * @return void
	 */
	private function sync_doc( $slug, $doc ) {
		$details = [
			'title'   => '',
			'content' => '',
		];
		
		if ( 'php' === pathinfo( $doc['path'], PATHINFO_EXTENSION ) ) {
			$details = $this->get_php_doc( $doc['path'] );
		} elseif ( 'md' === pathinfo( $doc['path'], PATHINFO_EXTENSION ) ) {
			$details = $this->get_md_doc( $doc['path'] );
		}

		$content = $details['content'];
		$title   = $details['title'];
		$post    = get_page_by_path( $slug, OBJECT, 'site-guide' );
		$args    = [
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_type'    => 'site-guide',
			'post_content' => $content,
		];

		if ( ! $content ) {
			return;
		}

		if ( ! $title ) {
			$args['post_title'] = $slug;
		}

		if ( ! $post || ! $post->ID ) {
			$post_id = wp_insert_post( $args );
		} else {
			$post_id    = $post->ID;
			$args['ID'] = $post_id;
			wp_update_post( $args );
		}

		// Marks this as a local synced post to prevent edits.
		update_post_meta( $post_id, '_sync_m_time', $doc['time'] );

		$this->synced[ $slug ] = $doc['time'];
		$this->updated         = true;
	}

	/**
	 * Gets the content and title from a php doc.
	 *
	 * @param  string $doc The path to the doc.
	 * @return array
	 */
	private function get_php_doc( $doc ) {
		$data = get_file_data( $doc, [ 'title' => 'Title' ] );
		ob_start();
		require $doc;
		return [
			'title'   => $data['title'],
			'content' => ob_get_clean(),
		];
	}

	/**
	 * Undocumented function
	 *
	 * @param  string $doc The path to the doc.
	 * @return array
	 */
	private function get_md_doc( $doc ) {
		$parsedown = new \Parsedown();
		$content   = file_get_contents( $doc ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$title     = '';

		preg_match( '/^# (.*)/m', $content, $matches );

		if ( isset( $matches[1] ) ) {
			$title   = $matches[1];
			$content = str_replace( '# ' . $title, '', $content );
		}

		// Find any images and replace the relative path with URL.
		$dir     = dirname( $doc );
		$content = preg_replace_callback( 
			'/!\[(.*)\]\((.*)\)/',
			function ( $matches ) use ( $dir ) {
				$url = get_image_src( $matches[2], $dir );
				return '![' . $matches[1] . '](' . $url . ')';
			},
			$content
		);
		
		return [
			'title'   => $title,
			'content' => $parsedown->text( $content ),
		];
	}
}
