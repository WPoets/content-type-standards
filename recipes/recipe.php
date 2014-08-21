<?php
/*
Plugin Name: Recipes Content Type
Plugin URI:
Description: Add a recipe content type to your WordPress site.
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
class Recipes {

	/*--------------------------------------------*
	 * Variables and Constants
	 *--------------------------------------------*/
	const name = 'Recipe';
	const slug = 'recipe';
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
		$this->init_content_type();
	}

	/* Runs when the plugin is activated */
	function install_content_type() {}

	/* Runs when the plugin is initialized */
	function init_content_type() {

		add_action( 'init', array( $this, 'add_content_type' ) );
		add_action( 'init', array( $this, 'add_taxonomies' ) );

		if ( is_admin() ) {

			add_filter( 'post_updated_messages', array( $this, 'update_messages' ) );
			add_action( 'contextual_help', array( $this, 'update_contextual_help' ), 10, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'meta_box_admin_style' ) );
			add_action( 'admin_footer', array( $this, 'meta_box_js_footer' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_custom_meta_box' ) );

		} else {

		}
	}

	/* Add the custom content type */
	public function add_content_type() {
		$labels = array(
			'name'               => _x( 'Recipes', 'post type general name' ),
			'singular_name'      => _x( 'Recipe', 'post type singular name' ),
			'add_new'            => __( 'Add New' ),
			'add_new_item'       => __( 'Add New Recipe' ),
			'edit_item'          => __( 'Edit Recipe' ),
			'new_item'           => __( 'New Recipe' ),
			'all_items'          => __( 'All Recipe' ),
			'view_item'          => __( 'View Recipe' ),
			'search_items'       => __( 'Search Recipes' ),
			'not_found'          => __( 'No recipe found' ),
			'not_found_in_trash' => __( 'No recipe found in the Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => 'Recipes'
		);
		$args = array(
			'labels'        	  => $labels,
			'description'   	  => 'Holds our recipes and recipe specific data',
			'public'        	  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position' 	  => 5,
			'menu_icon' 		  => 'dashicons-feedback',
			'can_export'          => true,
			'has_archive'   	  => true,
			'supports'      	  => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'capability_type'     => 'post'
		);
		register_post_type( 'recipe', $args );
	}

	/* Add taxonomies for the new content type */
	public function add_taxonomies() {
		$labels = array(
			'name'              => _x( 'Recipe Categories', 'taxonomy general name' ),
			'singular_name'     => _x( 'Recipe Category', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Recipe Categories' ),
			'all_items'         => __( 'All Categories' ),
			'parent_item'       => __( 'Parent Category' ),
			'parent_item_colon' => __( 'Parent Category:' ),
			'edit_item'         => __( 'Edit Category' ),
			'update_item'       => __( 'Update Category' ),
			'add_new_item'      => __( 'Add New Category' ),
			'new_item_name'     => __( 'New Category' ),
			'menu_name'         => __( 'Categories' )
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true
		);
		register_taxonomy( 'newsletter_category', 'recipe', $args );
	}

	/* Update the messaging in the admin */
	public function update_messages( $messages ) {
		global $post, $post_ID;
		$messages['newsletter'] = array(
			0 => '',
			1 => sprintf( __('Recipe updated. <a href="%s">View recipe</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __(self::name.' updated.'),
			5 => isset($_GET['revision']) ? sprintf( __('Recipe restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Recipe published. <a href="%s">View recipe</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Recipe saved.'),
			8 => sprintf( __('Recipe submitted. <a target="_blank" href="%s">Preview recipe</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Recipe scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview recipe</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Recipe draft updated. <a target="_blank" href="%s">Preview recipe</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) )
		);
		return $messages;
	}

	/* Add contextual help tab in the admin */
	public function update_contextual_help( $contextual_help, $screen_id, $screen ) {
		//Single Newsletter Edit Screen
		if ( 'newsletter' == $screen->id ) {

			$contextual_help = '<h2>Recipes</h2>
			<p>Recipes show the details of email marketing campaigns on the website. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p>
			<p>You can view/edit the details of each product by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

		//Recipes Edit Screen
		} elseif ( 'edit-newsletter' == $screen->id ) {

			$contextual_help = '<h2>Editing products</h2>
			<p>This page allows you to view/modify product details. Please make sure to fill out the available boxes with the appropriate details (product image, price, brand) and <strong>not</strong> add these details to the product description.</p>';

		}
		return $contextual_help;
	}

	/* Register and enqueue style sheet */
	public function meta_box_admin_style() {
	    wp_enqueue_style(self::slug.'-admin', plugins_url('css/admin.css', __FILE__));
	}

	/* Add Custom Meta Box */
	public function add_custom_meta_box() {
	    add_meta_box(
	        self::slug.'_box',
	        __( self::name, 'myplugin_textdomain' ),
	        array( $this, 'custom_meta_box_content' ),
	        self::slug,
	        'normal',
	        'high'
	    );
	}

	/* Prints meta box content */
	public function custom_meta_box_content( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'custom_meta_box_content_nonce' );

		/*Custom Fields
		 *
		 * "Recipe Type" Tags
		 * "Yield" text field
		 * "Prep Time" text field ISO 8601 duration format
		 * "Cook Time" text field ISO 8601 duration format
		 * "Total Time" text field ISO 8601 duration format
		 * "Ingredients" repeater with "Amount" and "Ingredient" text field
		 * "Instructions" wysiwyg
		 *--Nutrition--
		 * "Serving Size" text field
		 * "Calories" text field
		 * "Fat" text field
		 * "Saturated Fat" text field
		 * "Unsaturated Fat" text field
		 * "Carbohydrates" text field
		 * "Sugar" text field
		 * "Fiber" text field
		 * "Protein" text field
		 * "Cholesterol" text field
		 *
		 */

		$html = null;

		$html .= '<div id="recipe-meta-box">';

			//"Yield" text field
			$yield = get_post_meta( $post->ID, '_yield', true );
			$html .= '<div class="recipe-field yield-field">';
			$html .= '<label for="yield">'.__( 'Yield' ).'</label>';
			$html .= '<input type="text" name="yield" id="yield" placeholder="" value="'.( !empty($yield) ? $yield : "" ).'" />';
			$html .= '</div>';

			//"Prep Time" text field
			$prep_time = get_post_meta( $post->ID, '_prep_time', true );
			$html .= '<div class="recipe-field prep_time-field">';
			$html .= '<label for="prep_time">'.__( 'Prep Time' ).'</label>';
			$html .= '<input type="number" name="prep_time" id="prep_time" placeholder="" value="'.( !empty($prep_time) ? $prep_time : "" ).'" />';
			$html .= '<p class="description">'.__( 'Enter the time in minutes.' ).'</p>';
			$html .= '</div>';

			//"Cook Time" text field
			$cook_time = get_post_meta( $post->ID, '_cook_time', true );
			$html .= '<div class="recipe-field cook_time-field">';
			$html .= '<label for="cook_time">'.__( 'Cook Time' ).'</label>';
			$html .= '<input type="number" name="cook_time" id="cook_time" placeholder="" value="'.( !empty($cook_time) ? $cook_time : "" ).'" />';
			$html .= '<p class="description">'.__( 'Enter the time in minutes.' ).'</p>';
			$html .= '</div>';

			//"Total Time" text field
			$total_time = get_post_meta( $post->ID, '_total_time', true );
			$html .= '<div class="recipe-field total_time-field">';
			$html .= '<label for="total_time">'.__( 'Total Time' ).'</label>';
			$html .= '<input type="number" name="total_time" id="total_time" placeholder="" value="'.( !empty($total_time) ? $total_time : "" ).'" />';
			$html .= '<p class="description">'.__( 'Enter the time in minutes.' ).'</p>';
			$html .= '</div>';

			//get the saved meta as an arry
			$ingredients = array();
			$ingredients = get_post_meta($post->ID,'_ingredients',true);
			$ingredients_count = 0;
			$html .= '<div class="recipe-field ingredients-field clearfix">';
			$html .= '<label>'.__( 'Ingredient' ).'</label>';
			$html .= '<p class="description">'.__( 'Add a list of ingredient needed for the recipe. You can drag and drop to re-order the list.' ).'</p>';
			if ( count( $ingredients ) > 0 ) {
				foreach( (array)$ingredients as $ingredient ) {
					if ( isset( $ingredient['amount'] ) || isset( $ingredient['item'] ) ) {
						$html .= '<div class="ingredient-field">';
						$html .= '<div title="'.__( 'Sort Ingredient' ).'" class="sort-icon dashicons dashicons-sort"></div>';
						$html .= '<label for="amount">Amount</label>';
						$html .= '<input type="text" name="ingredient['.$ingredients_count.'][amount]" id="amount" value="'.( !empty($ingredient['amount']) ? $ingredient['amount'] : "" ).'" />';
						$html .= '<label for="item">Item</label>';
						$html .= '<input type="text" name="ingredient['.$ingredients_count.'][item]" id="item" value="'.( !empty($ingredient['item']) ? $ingredient['item'] : "" ).'" />';
						$html .= '<div title="'.__( 'Remove Ingredient' ).'" class="remove-ingredient dismiss-icon dashicons dashicons-dismiss"></div>';
						$html .= '</div>';
						$ingredients_count++;
					}
				}
			}
			$html .= '</div>';
			$html .= '<span class="add-ingredient button button-secound button-medium">'.__('Add Ingredient').'</span>';
			?>

			<script>
				jQuery( function($){

					$( ".ingredients-field" ).sortable({});

					//Add fields for ingredients
			        var count = <?php echo $ingredients_count; ?>;
			        $(".add-ingredient").click(function() {
						console.log('clicked');
			            count = count + 1;
			            $('.ingredients-field').append('<div class="ingredient-field"><div class="sort-icon dashicons dashicons-sort"></div><label for="amount">Amount</label><input type="text" name="ingredient['+count+'][amount]" id="amount" placeholder="Enter Amount and Unit of Measurement" /><label for="item">Item</label><input type="text" name="ingredient['+count+'][item]" id="item" placeholder="" /><div title="<?php _e( 'Remove Ingredient' ); ?>" class="remove-ingredient dismiss-icon dashicons dashicons-dismiss"></div></div>');
			            //$(this).before('test');
			            return false;
			        });

					//Remove fields from ingredients
			        $(".remove-ingredient").live('click', function() {
			            $(this).parent().remove();
			        });
				});
			</script>

			<?php
			//print fields so far
			echo $html;

			//Instructions wysiwyg
			$instructions = get_post_meta($post->ID,'_instructions',true);
			$settings = array(
				'media_buttons' => false,
				'textarea_name' => 'instructions',
			);
			echo '<div class="recipe-field instructions-field clearfix">';
			echo '<label for="recipe-instructions">'.__( 'Instructions' ).'</label>';
			wp_editor( $instructions, 'recipe-instructions', $settings );
			echo '</div>';

			//reset html and collect nutrition fields to print
			$html = null;

			$html .= '<h2>Nutrition</h2>';
			$html .= '<p class="description">'.__( 'Add the nutritional information for the set serving size.' ).'</p>';

			//"Serving Size" field
			$serving_size = get_post_meta( $post->ID, '_serving_size', true );
			$html .= '<div class="recipe-field serving_size-field">';
			$html .= '<label for="serving_size">'.__( 'Serving Size' ).'</label>';
			$html .= '<input type="text" name="serving_size" id="serving_size" placeholder="" value="'.( !empty($serving_size) ? $serving_size : "" ).'" />';
			$html .= '</div>';

			//"Calories" field
			$calories = get_post_meta( $post->ID, '_calories', true );
			$html .= '<div class="recipe-field calories-field">';
			$html .= '<label for="calories">'.__( 'Calories' ).'</label>';
			$html .= '<input type="text" name="calories" id="calories" placeholder="Calories per Serving" value="'.( !empty($calories) ? $calories : "" ).'" />';
			$html .= '</div>';

			//"Fat" field
			$fat = get_post_meta( $post->ID, '_fat', true );
			$html .= '<div class="recipe-field fat-field">';
			$html .= '<label for="fat">'.__( 'Fat' ).'</label>';
			$html .= '<input type="text" name="fat" id="fat" placeholder="Fat per Serving" value="'.( !empty($fat) ? $fat : "" ).'" />';
			$html .= '</div>';

			//"Saturated Fat" field
			$saturated_fat = get_post_meta( $post->ID, '_saturated_fat', true );
			$html .= '<div class="recipe-field saturated_fat-field">';
			$html .= '<label for="saturated_fat">'.__( 'Saturated Fat' ).'</label>';
			$html .= '<input type="text" name="saturated_fat" id="saturated_fat" placeholder="Saturated Fat per Serving" value="'.( !empty($saturated_fat) ? $saturated_fat : "" ).'" />';
			$html .= '</div>';

			//"Unsaturated Fat" field
			$unsaturated_fat = get_post_meta( $post->ID, '_unsaturated_fat', true );
			$html .= '<div class="recipe-field unsaturated_fat-field">';
			$html .= '<label for="unsaturated_fat">'.__( 'Unsaturated Fat' ).'</label>';
			$html .= '<input type="text" name="unsaturated_fat" id="unsaturated_fat" placeholder="Unsaturated Fat per Serving" value="'.( !empty($unsaturated_fat) ? $unsaturated_fat : "" ).'" />';
			$html .= '</div>';

			//"Carbohydrates" field
			$carbohydrates = get_post_meta( $post->ID, '_carbohydrates', true );
			$html .= '<div class="recipe-field carbohydrates-field">';
			$html .= '<label for="carbohydrates">'.__( 'Carbohydrates' ).'</label>';
			$html .= '<input type="text" name="carbohydrates" id="carbohydrates" placeholder="Carbohydrates per Serving" value="'.( !empty($carbohydrates) ? $carbohydrates : "" ).'" />';
			$html .= '</div>';

			//"Sugar" field
			$sugar = get_post_meta( $post->ID, '_sugar', true );
			$html .= '<div class="recipe-field sugar-field">';
			$html .= '<label for="sugar">'.__( 'Sugar' ).'</label>';
			$html .= '<input type="text" name="sugar" id="sugar" placeholder="Sugar per Serving" value="'.( !empty($sugar) ? $sugar : "" ).'" />';
			$html .= '</div>';

			//"Fiber" field
			$fiber = get_post_meta( $post->ID, '_fiber', true );
			$html .= '<div class="recipe-field fiber-field">';
			$html .= '<label for="fiber">'.__( 'Fiber' ).'</label>';
			$html .= '<input type="text" name="fiber" id="fiber" placeholder="Fiber per Serving" value="'.( !empty($fiber) ? $fiber : "" ).'" />';
			$html .= '</div>';

			//"Protein" field
			$protein = get_post_meta( $post->ID, '_protein', true );
			$html .= '<div class="recipe-field protein-field">';
			$html .= '<label for="protein">'.__( 'Protein' ).'</label>';
			$html .= '<input type="text" name="protein" id="protein" placeholder="Protein per Serving" value="'.( !empty($protein) ? $protein : "" ).'" />';
			$html .= '</div>';

			//"Cholesterol" field
			$cholesterol = get_post_meta( $post->ID, '_cholesterol', true );
			$html .= '<div class="recipe-field cholesterol-field">';
			$html .= '<label for="cholesterol">'.__( 'Cholesterol' ).'</label>';
			$html .= '<input type="text" name="cholesterol" id="cholesterol" placeholder="Cholesterol per Serving" value="'.( !empty($cholesterol) ? $cholesterol : "" ).'" />';
			$html .= '</div>';


		$html .= '</div>';
		echo $html;
	}

	/* Save the custom meta box fields to the postmeta table */
	public function meta_box_js_footer() {}

	/* Save the custom meta box fields to the postmeta table */
	public function save_custom_meta_box( $post_id ) {
		// Checks save status
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'custom_meta_box_content_nonce' ] ) && wp_verify_nonce( $_POST[ 'custom_meta_box_content_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return;
		}

		/* Save Yield */
		if( isset( $_POST[ 'yield' ] ) ) {
			update_post_meta( $post_id, '_yield', sanitize_text_field( $_POST[ 'yield' ] ) );
		}
		/* Save Prep Time */
		if( isset( $_POST[ 'prep_time' ] ) ) {
			update_post_meta( $post_id, '_prep_time', sanitize_text_field( $_POST[ 'prep_time' ] ) );
		}
		/* Save cook_time */
		if( isset( $_POST[ 'cook_time' ] ) ) {
			update_post_meta( $post_id, '_cook_time', sanitize_text_field( $_POST[ 'cook_time' ] ) );
		}
		/* Save total_time */
		if( isset( $_POST[ 'total_time' ] ) ) {
			update_post_meta( $post_id, '_total_time', sanitize_text_field( $_POST[ 'total_time' ] ) );
		}

		/*Save Ingredients */
		if( isset( $_POST[ 'ingredient' ] ) ) {
			$ingredients = $_POST[ 'ingredient' ];
			//sanitize items in array
			foreach( (array)$ingredients as $key => $ingredient ){
				$ingredients[$key]['amount'] = sanitize_text_field( $ingredient['amount'] );
				$ingredients[$key]['item'] = sanitize_text_field( $ingredient['item'] );
			}
			update_post_meta( $post_id, '_ingredients', $ingredients );
		}

		/* Save Instructions */
		if( isset( $_POST[ 'instructions' ] ) ) {
			update_post_meta( $post_id, '_instructions', esc_textarea( $_POST[ 'instructions' ] ) );
		}
		/* Save serving_size */
		if( isset( $_POST[ 'serving_size' ] ) ) {
			update_post_meta( $post_id, '_serving_size', sanitize_text_field( $_POST[ 'serving_size' ] ) );
		}
		/* Save calories */
		if( isset( $_POST[ 'calories' ] ) ) {
			update_post_meta( $post_id, '_calories', sanitize_text_field( $_POST[ 'calories' ] ) );
		}
		/* Save fat */
		if( isset( $_POST[ 'fat' ] ) ) {
			update_post_meta( $post_id, '_fat', sanitize_text_field( $_POST[ 'fat' ] ) );
		}
		/* Save saturated_fat */
		if( isset( $_POST[ 'saturated_fat' ] ) ) {
			update_post_meta( $post_id, '_saturated_fat', sanitize_text_field( $_POST[ 'saturated_fat' ] ) );
		}
		/* Save unsaturated_fat */
		if( isset( $_POST[ 'unsaturated_fat' ] ) ) {
			update_post_meta( $post_id, '_unsaturated_fat', sanitize_text_field( $_POST[ 'unsaturated_fat' ] ) );
		}
		/* Save carbohydrates */
		if( isset( $_POST[ 'carbohydrates' ] ) ) {
			update_post_meta( $post_id, '_carbohydrates', sanitize_text_field( $_POST[ 'carbohydrates' ] ) );
		}
		/* Save sugar */
		if( isset( $_POST[ 'sugar' ] ) ) {
			update_post_meta( $post_id, '_sugar', sanitize_text_field( $_POST[ 'sugar' ] ) );
		}
		/* Save fiber */
		if( isset( $_POST[ 'fiber' ] ) ) {
			update_post_meta( $post_id, '_fiber', sanitize_text_field( $_POST[ 'fiber' ] ) );
		}
		/* Save protein */
		if( isset( $_POST[ 'protein' ] ) ) {
			update_post_meta( $post_id, '_protein', sanitize_text_field( $_POST[ 'protein' ] ) );
		}
		/* Save cholesterol */
		if( isset( $_POST[ 'cholesterol' ] ) ) {
			update_post_meta( $post_id, '_cholesterol', sanitize_text_field( $_POST[ 'cholesterol' ] ) );
		}

	}

}
add_action('plugins_loaded', array( 'Recipes', 'get_instance' ) );
