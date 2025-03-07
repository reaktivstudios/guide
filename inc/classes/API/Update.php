<?php 
/**
 * Updates or adds a doc.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API;

use RKV\Site_Guide\API\Blocks\Title;
use stdClass;

/**
 * Undocumented class
 */
class Update {
	/**
	 * Notion ID.
	 *
	 * @var string
	 */
	private string $notion_id;

	/**
	 * The status.
	 *
	 * @var string
	 */
	private string $status = '';

	/**
	 * The content.
	 *
	 * @var string
	 */
	private string $content = '';

	/**
	 * The parsed blocks.
	 *
	 * @var array
	 */
	private array $blocks = [];

	/**
	 * The properties.
	 *
	 * @var stdClass
	 */
	private stdClass $properties;

	/**
	 * The last edited time.
	 *
	 * @var string
	 */
	private string $last_edited;

	/**
	 * The title.
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * The type.
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * The post ID.
	 *
	 * @var [type]
	 */
	private $post_id;

	/**
	 * Set initial properties and initialize the status.
	 *
	 * @param  string   $id          The notion ID.
	 * @param  string   $last_edited The last edited time.
	 * @param  stdClass $properties  The document properties.
	 */
	public function __construct( $id, $last_edited, $properties ) {
		$this->notion_id   = $id;
		$this->last_edited = $last_edited;
		$this->properties  = $properties;
		
		$this->check_status();
		$this->process();
	}

	/**
	 * Get the post ID.
	 *
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get the status.
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Checks to see if this is a new or updated doc.
	 *
	 * @return void
	 */
	private function check_status() {
		$data = get_option( $this->notion_id );

		if ( empty( $data ) ) {
			$this->status = 'add';
			return;
		}

		$last_edited   = $data['last_edited'] ?? '';
		$this->post_id = $data['post_id'] ?? 0;

		if ( $this->last_edited === $last_edited && empty( $_GET['force'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( empty( $post_id ) ) {
			$this->status = 'add';
			return;
		}

		$this->status = 'update';
	}

	/**
	 * Initiate processing of the doc.
	 *
	 * @return void
	 */
	private function process() {
		switch ( $this->status ) {
			case 'add':
				$this->build_content();
				$this->add();
				break;
			case 'update':
				$this->build_content();
				$this->update();
				break;
		}

		if ( in_array( $this->status, [ 'add', 'update' ], true ) ) {
			$this->add_type();
			$this->store();
		}
	}

	/**
	 * Add new doc.
	 *
	 * @return void
	 */
	private function add() {
		$docs = get_posts(
			[
				'post_type' => 'site-guide',
				'title'     => $this->title,
				'fields'    => 'ids',
			] 
		);

		if ( $docs ) {
			$this->post_id = $docs[0];
			$this->update();
			return;
		}

		$this->post_id = wp_insert_post(
			[
				'post_type'    => 'site-guide',
				'post_status'  => 'publish',
				'post_title'   => $this->title,
				'post_content' => $this->content,
			],
			true
		);
	}

	/**
	 * Update existing doc.
	 *
	 * @return void
	 */
	private function update() {
		$args = [
			'ID'           => $this->post_id,
			'post_title'   => $this->title,
			'post_content' => $this->content,
		];

		wp_update_post( $args );
	}

	/**
	 * Add the type.
	 *
	 * @return void
	 */
	private function add_type() {
		if ( empty( $this->post_id ) ) {
			return;
		}

		$tax  = 'guide-type';
		$term = get_term_by( 'name', $this->type, $tax );

		if ( ! $term ) {
			$term = wp_insert_term( $this->type, $tax );
		}

		if ( is_wp_error( $term ) ) {
			return;
		}

		wp_set_post_terms( $this->post_id, [ $term->term_id ], $tax );
	}

	/**
	 * Store the data.
	 *
	 * @return void
	 */
	private function store() {
		if ( empty( $this->post_id ) ) {
			return;
		}

		if ( ! empty( $this->blocks ) ) {
			update_post_meta( $this->post_id, 'blocks', wp_json_encode( $this->blocks ) );
		}

		if ( ! empty( $this->properties->Role ) && ! empty( $this->properties->Role->select ) && ! empty( $this->properties->Role->select->name ) ) {
			update_post_meta( $this->post_id, 'rkv_role_req', $this->properties->Role->select->name );
		}

		update_post_meta( $this->post_id, 'notion_id', $this->notion_id );

		update_option(
			$this->notion_id,
			[
				'last_edited' => $this->last_edited,
				'post_id'     => $this->post_id,
			] 
		);
	}

	/**
	 * Build the content.
	 *
	 * @return void
	 */
	private function build_content() {
		$name = $this->properties->Name ?? new stdClass();
		$type = $this->properties->Type ?? new stdClass();

		if ( ! empty( $name ) ) {
			$text_block  = new Title( $name );
			$block       = $text_block->get_block();
			$this->title = $block['content'];
		}

		if ( ! empty( $type ) ) {
			$select     = $type->select ?? new stdClass();
			$this->type = $select->name ?? '';
		}

		$block_parser  = new Get_Blocks( $this->notion_id );
		$this->content = $block_parser->get_the_content();
		$this->blocks  = $block_parser->get_the_blocks();
	}
}
