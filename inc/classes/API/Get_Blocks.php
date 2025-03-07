<?php 
/**
 * Get Blocks for a parent ID.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API;

use RKV\Site_Guide\API\Blocks\Parse;

/**
 * Gets the blocks for a page or block with children.
 */
class Get_Blocks {
	/**
	 * Number of attempts to try.
	 *
	 * @var integer
	 */
	private int $attempts = 5;

	/**
	 * The notion ID.
	 *
	 * @var string
	 */
	private string $notion_id;

	/**
	 * The blocks as an array.
	 *
	 * @var array
	 */
	private array $blocks = [];

	/**
	 * Active list ID for nesting list items.
	 *
	 * @var mixed
	 */
	private $list_id = false;

	/**
	 * The content.
	 *
	 * @var string
	 */
	private string $content = '';

	/**
	 * Set the notion ID and initiate the request.
	 *
	 * @param  string $id The notion ID.
	 */
	public function __construct( $id ) {
		$this->notion_id = $id;
		
		$this->request();
	}

	/**
	 * Get the content.
	 *
	 * @return string
	 */
	public function get_the_content() {
		$this->build_content();
		return $this->content;
	}

	/**
	 * Gets the blocks.
	 *
	 * @return array
	 */
	public function get_the_blocks() {
		return $this->blocks;
	}

	/**
	 * Request the blocks.
	 *
	 * @return void
	 */
	private function request() {
		$args     = [
			'timeout' => 10, // phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
			'headers' => [
				'Authorization'  => 'Bearer ' . RKV_SITE_GUIDE_API_KEY,
				'Notion-Version' => '2022-06-28',
				'Content-Type'   => 'application/json',
			],
		];
		$response = wp_remote_get( 'https://api.notion.com/v1/blocks/' . $this->notion_id . '/children?page_size=100', $args ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get

		if ( is_wp_error( $response ) && $this->attempts ) {
			$this->request();
			--$this->attempts;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( is_string( $body ) ) {
			$body = json_decode( $body );
		}

		if ( isset( $body->results ) ) {
			$this->parse_blocks( $body->results );
		}
	}

	/**
	 * Parses the blocks into a nested array.
	 *
	 * @param  array $results The results.
	 * @return void
	 */
	private function parse_blocks( $results ) {
		foreach ( $results as $result ) {
			if ( $result->archived || $result->in_trash ) {
				continue;
			}

			$type  = $result->type ?? '';
			$block = $this->parse_block( $result );

			if ( in_array( $type, [ 'bulleted_list_item', 'numbered_list_item' ], true ) ) {
				$list_type = str_replace( '_item', '', $type );

				if ( false === $this->list_id || $this->blocks[ $this->list_id ]['type'] !== $list_type ) {
					$block_wrap_obj               = new \stdClass();
					$block_wrap_obj->type         = $list_type;
					$block_wrap_obj->has_children = false;
					$block_wrap_obj->id           = '';

					$this->blocks[] = $this->parse_block( $block_wrap_obj );
					$this->list_id  = count( $this->blocks ) - 1;
				}

				$this->blocks[ $this->list_id ]['children'][] = $block;
			} else {
				$this->list_id  = false;
				$this->blocks[] = $block;
			}
		}
	}

	/**
	 * Build the content.
	 *
	 * @return void
	 */
	private function build_content() {
		$content_parser = new Content_Parser( $this->blocks );
		$this->content  = $content_parser->get_content();
	}

	/**
	 * Parses the block to get the content.
	 *
	 * @param  \stdClass $block The block.
	 * @return array
	 */
	private function parse_block( $block ) {
		$parser = new Parse( $block );
		return $parser->get_block();
	}
}
