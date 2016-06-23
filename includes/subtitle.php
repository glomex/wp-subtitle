<?php

/**
 * @package     WP Subtitle
 * @subpackage  Subtitle Class
 */

class WP_Subtitle {

	/**
	 * Post ID
	 *
	 * @var  int
	 */
	private $post_id = 0;

	/**
	 * Constructor
	 *
	 * @param  int|WP_Post  $post  Post object or ID.
	 */
	public function __construct( $post ) {

		// Post ID
		if ( is_a( $post, 'WP_Post' ) ) {
			$this->post_id = absint( $post->ID );
		} else {
			$this->post_id = absint( $post );
		}

	}

	/**
	 * The Subtitle
	 *
	 * @param  array  $args  Display parameters.
	 */
	public function the_subtitle( $args = '' ) {

		echo $this->get_subtitle( $args );

	}

	/**
	 * Get the Subtitle
	 *
	 * @uses  apply_filters( 'wps_subtitle' )
	 *
	 * @param   array   $args  Display parameters.
	 * @return  string         The filtered subtitle meta value.
	 */
	public function get_subtitle( $args = '' ) {

		if ( $this->post_id && $this->is_supported_post_type() ) {

			$args = wp_parse_args( $args, array(
				'before' => '',
				'after'  => ''
			) );

			$subtitle = apply_filters( 'wps_subtitle', $this->get_raw_subtitle(), get_post( $this->post_id ) );

			if ( ! empty( $subtitle ) ) {
				$subtitle = $args['before'] . $subtitle . $args['after'];
			}

			return $subtitle;

		}

		return '';

	}

	/**
	 * Get Raw Subtitle
	 *
	 * @return  string  The subtitle meta value.
	 */
	public function get_raw_subtitle() {

		return get_post_meta( $this->post_id, $this->get_post_meta_key(), true );

	}

	/**
	 * Update Subtitle
	 *
	 * @param   string    $subtitle  Subtitle.
	 * @return  int|bool             Meta ID if new entry. True if updated, false if not updated or the same as current value.
	 */
	public function update_subtitle( $subtitle ) {

		return update_post_meta( $this->post_id, $this->get_post_meta_key(), wp_kses_post( $subtitle ) );

	}

	/**
	 * Get Post Meta Key
	 *
	 * @uses  apply_filters( 'wps_subtitle_key' )
	 *
	 * @return  string  The subtitle meta key.
	 */
	private function get_post_meta_key() {

		return apply_filters( 'wps_subtitle_key', 'wps_subtitle', $this->post_id );

	}

	/**
	 * Is Supported Post Type?
	 *
	 * @return  boolean
	 */
	private function is_supported_post_type() {

		$post_types = $this->get_supported_post_types();

		return in_array( get_post_type( $this->post_id ), $post_types );

	}

	/**
	 * Get Supported Post Types
	 *
	 * @return  array  Array of supported post types.
	 */
	private function get_supported_post_types() {

		$post_types = (array) get_post_types( array(
			'_builtin' => false
		) );

		$post_types = array_merge( $post_types, array( 'post', 'page' ) );

		$supported = array();

		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'wps_subtitle' ) ) {
				$supported[] = $post_type;
			}
		}

		return $supported;

	}

}