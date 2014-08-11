<?php
/*
Plugin Name: Newsletter Content Type
Plugin URI:
Description: Add a newsletter content type to your WordPress site.
Version: 0.1
Author: Tim Howe
Author Email: tim@hallme.com
License:

  Copyright 2011 Tim Howe (tim@hallme.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
class Newsletters {

	/*--------------------------------------------*
	 * Variables and Constants
	 *--------------------------------------------*/
	const name = 'Newsletter';
	const slug = 'newsletter';
	protected static $instance = null;

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/* Constructor */
	function __construct() {
		$this->init_newsletter_content_type();
	}

	/* Runs when the plugin is activated */
	function install_newsletter_content_type() {}

	/* Runs when the plugin is initialized */
	function init_newsletter_content_type() {

		add_action( 'init', array( $this, 'add_content_type' ) );
		add_action( 'init', array( $this, 'add_taxonomies' ) );

		if ( is_admin() ) {

			add_filter( 'post_updated_messages', array( $this, 'update_messages' ) );
			add_action( 'contextual_help', array( $this, 'update_contextual_help' ), 10, 3 );
			add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_box' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_image_enqueue' ) );
			add_action( 'save_post', array( $this, 'save_custom_meta_box' ) );

		} else {

		}
	}

	/* Add the custom content type */
	public function add_content_type() {
		$labels = array(
			'name'               => _x( self::name.'s', 'post type general name' ),
			'singular_name'      => _x( self::name, 'post type singular name' ),
			'add_new'            => __( 'Add New' ),
			'add_new_item'       => __( 'Add New '.self::name ),
			'edit_item'          => __( 'Edit '.self::name ),
			'new_item'           => __( 'New '.self::name ),
			'all_items'          => __( 'All '.self::name ),
			'view_item'          => __( 'View '.self::name ),
			'search_items'       => __( 'Search '.self::name.'s' ),
			'not_found'          => __( 'No '.self::slug.' found' ),
			'not_found_in_trash' => __( 'No '.self::slug.' found in the Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => self::name
		);
		$args = array(
			'labels'        	  => $labels,
			'description'   	  => 'Holds our '.self::slug.'s and '.self::slug.' specific data',
			'public'        	  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position' 	  => 5,
			'menu_icon' 		  => 'dashicons-media-document',
			'can_export'          => true,
			'has_archive'   	  => true,
			'supports'      	  => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'capability_type'     => 'post'
		);
		register_post_type( self::slug, $args );
	}

	/* Add taxonomies for the new content type */
	public function add_taxonomies() {
		$labels = array(
			'name'              => _x( 'Newsletter Categories', 'taxonomy general name' ),
			'singular_name'     => _x( 'Newsletter Category', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Newsletter Categories' ),
			'all_items'         => __( 'All Newsletter Categories' ),
			'parent_item'       => __( 'Parent Newsletter Category' ),
			'parent_item_colon' => __( 'Parent Newsletter Category:' ),
			'edit_item'         => __( 'Edit Newsletter Category' ),
			'update_item'       => __( 'Update Newsletter Category' ),
			'add_new_item'      => __( 'Add New Newsletter Category' ),
			'new_item_name'     => __( 'New Newsletter Category' ),
			'menu_name'         => __( 'Newsletter Categories' )
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true
		);
		register_taxonomy( 'newsletter_category', self::name, $args );
	}

	/* Update the messaging in the admin */
	public function update_messages( $messages ) {
		global $post, $post_ID;
		$messages['newsletter'] = array(
			0 => '',
			1 => sprintf( __('Newsletter updated. <a href="%s">View newsletter</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Newsletter updated.'),
			5 => isset($_GET['revision']) ? sprintf( __('Product restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Product published. <a href="%s">View product</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Product saved.'),
			8 => sprintf( __('Newsletter submitted. <a target="_blank" href="%s">Preview newsletter</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Newsletter scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview newsletter</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Newsletter draft updated. <a target="_blank" href="%s">Preview newsletter</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) )
		);
		return $messages;
	}

	/* Add contextual help tab in the admin */
	public function update_contextual_help( $contextual_help, $screen_id, $screen ) {
		//Single Newsletter Edit Screen
		if ( 'newsletter' == $screen->id ) {

			$contextual_help = '<h2>Newsletters</h2>
			<p>Newsletters show the details of email marketing campaigns on the website. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p>
			<p>You can view/edit the details of each product by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

		//Newsletters Edit Screen
		} elseif ( 'edit-newsletter' == $screen->id ) {

			$contextual_help = '<h2>Editing products</h2>
			<p>This page allows you to view/modify product details. Please make sure to fill out the available boxes with the appropriate details (product image, price, brand) and <strong>not</strong> add these details to the product description.</p>';

		}
		return $contextual_help;
	}

	/* Add a custom meta box for inputs in the new content type*/
	public function add_custom_meta_box( $post_type ) {
	    add_meta_box(
	        'newsletter_file_box',
	        __( 'Newsletter File', self::slug ),
	        array( $this, 'meta_box_content' ),
	        self::slug,
	        'side',
	        'default'
	    );
	}

	/* Add the actual fields to the custom meta box */
	public function meta_box_content( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( plugin_basename( __FILE__ ), 'meta_box_content_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, '_newsletter-file', true );

		$html = '<p>';
		$html .= '<input type="text" name="newsletter-file" id="newsletter-file" size="19" placeholder="Select a File" value="'.( !empty($value) ? $value : "" ).'" />';
		$html .= '<input type="button" id="newsletter-file-button" class="button" value="'.__( "Upload", self::slug ).'" />';
		$html .= '<p class="description">Select or Upload a Newsletter File.</p>';
		$html .= '</p>';
	    echo $html;
	}

	/* Loads the image management javascript */
	function add_image_enqueue() {
        wp_enqueue_media();

        // Registers and enqueues the required javascript.
        wp_register_script( 'meta-box-file', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ) );
        wp_localize_script( 'meta-box-file', 'meta_image',
            array(
                'title' => __( 'Choose or Upload a File', self::slug ),
                'button' => __( 'Use this file', self::slug ),
            )
        );
        wp_enqueue_script( 'meta-box-file' );
	}

	/* Save the custom meta box fields to the postmeta table */
	public function save_custom_meta_box( $post_id ) {
		// Checks save status
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'meta_box_content_nonce' ] ) && wp_verify_nonce( $_POST[ 'meta_box_content_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
		}

		// Checks for input and sanitizes/saves if needed
		if( isset( $_POST[ 'newsletter-file' ] ) ) {
			update_post_meta( $post_id, '_newsletter-file', sanitize_text_field( $_POST[ 'newsletter-file' ] ) );
		}
	}

}
add_action('plugins_loaded', array( 'Newsletters', 'get_instance' ) );
