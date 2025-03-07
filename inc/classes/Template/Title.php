<?php
/**
 * Adds elements to the site guide title.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\Template;

/**
 * Title class.
 */
class Title {
	/**
	 * Add the actions.
	 */
	public function __construct() {
		add_action( 'get_template_part_template-parts/singular/categories', [ $this, 'callback' ] );
		add_filter( 'get_post_metadata', [ $this, 'author_meta' ], 10, 3 );
	}

	/**
	 * Output a link to the archive.
	 *
	 * @return void
	 */
	public function callback() {
		if ( ! is_singular( 'site-guide' ) ) {
			return;
		}

		$logo = file_get_contents( trailingslashit( RKV_SITE_GUIDE_PATH ) . 'assets/images/reaktiv-mark.svg' );

		$logo = str_replace(
			[
				'<svg',
				'viewBox="0 0 432 432"',
				'<style',
			], 
			[
				'<svg height="100" width="100" role="image"',
				'viewBox="160 108 216 216"',
				'<title>' . esc_html__( 'Reaktiv logo', 'rkv-site-guide' ) . '</title><style',
			],
			$logo
		);
		?>
<div class="rkv-site-guide-header">
	<a href="<?php echo esc_url( get_post_type_archive_link( 'site-guide' ) ); ?>" class="rkv-site-guide-header__link">
		<?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p>
			<?php esc_html_e( 'Site Guide', 'rkv-site-guide' ); ?>
		</p>
	</a>
</div>
		<?php
	}

	/**
	 * Filter the display post author meta value.
	 *
	 * @param mixed  $val      The value to return, either a single metadata value or an array
	 *                         of values depending on the value of `$single`. Default null.
	 * @param int    $post_id  ID of the object metadata is for.
	 * @param string $meta_key Metadata key.
	 * @return mixed
	 */
	public function author_meta( $val, $post_id, $meta_key ) {
		if ( 'cyber_nbcuni_hide_author' === $meta_key && 'site-guide' === get_post_type( $post_id ) ) {
			$val = [ 1 ];
		}

		return $val;
	}
}
