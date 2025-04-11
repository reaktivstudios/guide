<?php 
/**
 * Creates standard text elements like paragraphs, headings, etc.
 * 
 * @package rkv-guide
 */

namespace RKV\Site_Guide\API\Blocks;

/**
 * Title block.
 */
class Title extends Text {
	/**
	 * Set the content.
	 *
	 * @return void
	 */
	protected function set_content() {
		$rich_text = $this->block['data'] ?? [];

		foreach ( $rich_text as $text_node ) {
			$this->block['content'] .= $this->process_text_node( $text_node );
		}
	}
}
