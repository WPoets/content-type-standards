<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> itemscope itemtype="http://schema.org/Recipe">

		<header class="recipe-header">

			<h1 class="recipe-title" iitemprop="name"><?php the_title(); ?></h1>

			<meta itemprop="datePublished" content="<?php echo get_the_time('Y-m-j'); ?>"/>

		</header><?php // end article header ?>

		<section class="recipe-content clearfix">

			<div class="recipe-description" itemprop="description">

				<?php the_content(); ?>

			</div>

			<div class="recipe-image">
			<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'large', array( 'itemprop' => "image" ) );
				}
			?>
			</div>
            
            <div class="recipe-meta clearfix">
			<?php //Collect and print the recipe's yield amount
				$yield = get_post_meta( get_the_ID(), '_yield', true );
				if( !empty($yield) ){
					echo '<div class="yield" itemprop="recipeYield"><span class="yield-title meta-title">Yield</span><span class="yield-amount meta-value">'.esc_html( $yield ).'</span></div>';
				}

                //Collect and print the recipe's prep time
				$prep_time = esc_html( get_post_meta( get_the_ID(), '_prep_time', true ) );
				if( !empty($prep_time) ){
                    $prep_time = str_ireplace('minutes', '', $prep_time);
                    echo '<div class="prep-time"><span class="prep-title meta-title">Prep Time (mins)</span><time class="meta-value" itemprop="prepTime" content="PT'.$prep_time.'M">'.$prep_time.'</time></div>';
				}

                //Collect and print the recipe's cook time
				$cook_time = esc_html( get_post_meta( get_the_ID(), '_cook_time', true ) );
				if( !empty($cook_time) ){
                    $cook_time = str_ireplace('minutes', '', $cook_time);
					echo '<div class="cook-time"><span class="cook-title meta-title">Cook Time (mins)</span><time class="meta-value" itemprop="cookTime" content="PT'.$cook_time.'M">'.$cook_time.'</time></time></div>';
				}

                //Collect and print the recipe's total time
				$total_time = esc_html( get_post_meta( get_the_ID(), '_total_time', true ) );
				if( !empty($total_time) ){
                    $total_time = str_ireplace('minutes', '', $total_time);
					echo '<div class="total-time"><span class="total-title meta-title">Total Time (mins)</span><time class="meta-value" itemprop="totalTime" content="PT'.$total_time.'M">'.$total_time.'</time></time></div>';
				}
			?>
            </div>

			<div class="recipe-ingredients">

			<?php //Collect and print the ingredients
				$ingredients = get_post_meta( get_the_ID(), '_ingredients', true );
				if( !empty( $ingredients ) ){
                    echo '<h3>Ingredients</h3>';
					echo '<ul class="ingredients">';
					foreach( $ingredients as $ingredient ){
						echo '<li class="ingredient" itemprop="ingredients"><span class="amount">'.$ingredient["amount"].'</span> <span class="item">'.$ingredient["item"].'</span></li>';
					}
					echo '</ul>';
				}
			?>

			</div>

			<div class="recipe-instructions" itemprop="recipeInstructions">

                <h3>Instructions</h3>
				<?php echo apply_filters( 'the_content', ( get_post_meta( get_the_ID(), '_instructions', true ) ) ); ?>

			</div>

			<?php wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'scaffolding' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) ); ?>

			<div class="nutrition-info clearfix" itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation">

                <h3>Nutrition Facts</h3>
			<?php //Collect and print the nutritional information for the recipe
				$serving_size = get_post_meta( get_the_ID(), '_serving_size', true );
				if(!empty($serving_size)){
					echo '<div class="nutrition-item serving-size clearfix" itemprop="servingSize"><strong class="nutrition-title">Serving Size</strong> <span class="nutrition-content">'.esc_html($serving_size).'</div>';
				}

				$calories = get_post_meta( get_the_ID(), '_calories', true );
				if(!empty($calories)){
					echo '<div class="nutrition-item calories clearfix" itemprop="cholesterolContent"><strong class="nutrition-title">Calories</strong> <span class="nutrition-content">'.esc_html($calories).'</div>';
				}

				$fat = get_post_meta( get_the_ID(), '_fat', true );
				if(!empty($fat)){
					echo '<div class="nutrition-item fat clearfix" itemprop="fatContent"><strong class="nutrition-title">Total Fat</strong> <span class="nutrition-content">'.esc_html($fat).'</div>';
				}

				$saturated_fat = get_post_meta( get_the_ID(), '_saturated_fat', true );
				if(!empty($saturated_fat)){
					echo '<div class="nutrition-item saturated-fat clearfix" itemprop="saturated_fat"><span class="nutrition-title">Saturated Fat</span> <span class="nutrition-content">'.esc_html($saturated_fat).'</div>';
				}

				$unsaturated_fat = get_post_meta( get_the_ID(), '_unsaturated_fat', true );
				if(!empty($unsaturated_fat)){
					echo '<div class="nutrition-item unsaturated-fat clearfix" itemprop="unsaturatedFatContent"><span class="nutrition-title">Unsaturated Fat</span> <span class="nutrition-content">'.esc_html($unsaturated_fat).'</div>';
				}

				$trans_fat = get_post_meta( get_the_ID(), '_trans_fat', true );
				if(!empty($unsaturated_fat)){
					echo '<div class="nutrition-item trans-fat clearfix" itemprop="transFatContent"><span class="nutrition-title">Trans Fat</span> <span class="nutrition-content">'.esc_html($trans_fat).'</div>';
				}

				$cholesterol = get_post_meta( get_the_ID(), '_cholesterol', true );
				if(!empty($cholesterol)){
					echo '<div class="nutrition-item cholesterol clearfix" itemprop="cholesterolContent"><strong class="nutrition-title">Cholesterol</strong> <span class="nutrition-content">'.esc_html($cholesterol).'</div>';
				}

				$sodium = get_post_meta( get_the_ID(), '_sodium', true );
				if(!empty($sodium)){
					echo '<div class="nutrition-item sodium clearfix" itemprop="sodiumContent"><strong class="nutrition-title">Sodium</strong> <span class="nutrition-content">'.esc_html($sodium).'</div>';
				}

				$carbohydrates = get_post_meta( get_the_ID(), '_carbohydrates', true );
				if(!empty($carbohydrates)){
					echo '<div class="nutrition-item carbohydrates clearfix" itemprop="carbohydrateContent"><strong class="nutrition-title">Carbohydrates</strong> <span class="nutrition-content">'.esc_html($carbohydrates).'</div>';
				}

				$fiber = get_post_meta( get_the_ID(), '_fiber', true );
				if(!empty($fiber)){
					echo '<div class="nutrition-item fiber clearfix" itemprop="fiberContent"><span class="nutrition-title">Fiber</span> <span class="nutrition-content">'.esc_html($fiber).'</div>';
				}

				$sugar = get_post_meta( get_the_ID(), '_sugar', true );
				if(!empty($sugar)){
					echo '<div class="nutrition-item sugar clearfix" itemprop="sugarContent"><span class="nutrition-title">Sugar</span> <span class="nutrition-content">'.esc_html($sugar).'</div>';
				}

				$protein = get_post_meta( get_the_ID(), '_protein', true );
				if(!empty($protein)){
					echo '<div class="nutrition-item protein clearfix" itemprop="proteinContent"><strong class="nutrition-title">Protein</strong> <span class="nutrition-content">'.esc_html($protein).'</div>';
				}

			?>
			</div>

		</section><?php // end article section ?>

		<footer class="recipe-footer clearfix" role="contentinfo">

			<?php if (get_the_term_list($post->ID, 'recipe_category')) : ?>
			<p class="categories"><span class="categories-title">Categories:</span> <?php echo get_the_term_list($post->ID, 'recipe_category', '', ', ') ?></p>
			<?php endif; ?>

			<?php if (get_the_term_list($post->ID, 'recipe_tag')) : ?>
			<p class="tags"><span class="tags-title">Tags:</span> <?php echo get_the_term_list($post->ID, 'recipe_tag', '', ', ') ?></p>
			<?php endif; ?>

		</footer><?php // end article footer ?>

	</article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>