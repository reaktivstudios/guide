<?php 
/**
 * Adds images
 * 
 * @package rkv-guide
 */

namespace RKV\Site_Guide\API\Blocks;

/**
 * Image block.
 */
class Image extends Base {
	/**
	 * Set the content.
	 *
	 * @return void
	 */
	protected function set_content() {
		$img_id = get_option( $this->block['id'] );
 
		if ( $img_id && $this->set_content_from_media_library( $img_id ) ) {
			return;
		}

		$data = $this->block['data'] ?? [];
		$type = $data->type ?? 'invalid';
		$file = $data->$type ?? null;

		if ( empty( $file ) ) {
			return;
		}

		$url = $file->url ?? '';
		if ( empty( $url ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		add_filter( 'image_sideload_extensions', [ $this, 'allow_mp4' ] );
		
		$image = media_sideload_image( $url, 0, null, 'id' );

		remove_filter( 'image_sideload_extensions', [ $this, 'allow_mp4' ] );
 
		if ( $image && ! is_wp_error( $image ) ) {
			$this->set_content_from_media_library( $image );
			update_post_meta( $image, 'rkv_site_guide', true );
			update_option( $this->block['id'], $image );
		}
	}

	/**
	 * Gets the content from the media library.
	 *
	 * @param  int $img_id The image ID.
	 * @return bool
	 */
	private function set_content_from_media_library( $img_id ) {
		$attachment = get_post( $img_id );

		if ( empty( $attachment ) ) {
			return false;
		}

		$this->block['content'] = wp_get_attachment_image( $img_id, 'full' );

		if ( empty( $this->block['content'] ) ) {
			$url                    = wp_get_attachment_url( $img_id, 'full' );
			$this->block['content'] = wp_video_shortcode( [ 'src' => $url ], '' ) ?? '';
		}

		return ! empty( $this->block['content'] );
	}

	/**
	 * Allows MP4 files to be sideloaded.
	 *
	 * @param  array $allowed The allowed types.
	 * @return array
	 */
	public function allow_mp4( $allowed ) {
		if ( ! in_array( 'mp4', $allowed, true ) ) {
			$allowed[] = 'mp4';
		}

		return $allowed;
	}
}
