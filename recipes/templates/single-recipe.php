<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> itemscope itemtype="http://schema.org/Recipe">

		<header class="recipe-header">

			<h1 class="recipe-title" iitemprop="name"><?php the_title(); ?></h1>

			<meta itemprop="datePublished" content="<?php echo get_the_time('Y-m-j'); ?>"/>

			<?php //Collect and print the recipe's yield amount
				$yield = get_post_meta( get_the_ID(), '_yield', true );
				if( !empty($yield) ){
					echo '<span class="yield" itemprop="recipeYield">'.esc_html( $yield ).'</span>';
				}

				$prep_time = esc_html( get_post_meta( get_the_ID(), '_prep_time', true ) );
				if( !empty($prep_time) ){
					echo '<span class="prep-time">Prep Time: <time itemprop="prepTime" content="PT'.$prep_time.'M">'.$prep_time.' Minutes</time></span>';
				}

				$cook_time = esc_html( get_post_meta( get_the_ID(), '_cook_time', true ) );
				if( !empty($cook_time) ){
					echo '<span class="cook-time">Cook Time: <time itemprop="cookTime" content="PT'.$cook_time.'M">'.$cook_time.' Minutes</time></time></span>';
				}

				$total_time = esc_html( get_post_meta( get_the_ID(), '_total_time', true ) );
				if( !empty($total_time) ){
					echo '<span class="total-time">Total Time: <time itemprop="totalTime" content="PT'.$total_time.'M">'.$total_time.' Minutes</time></time></span>';
				}
			?>

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

			<div class="recipe-ingredients">

			<?php //Collect and print the ingredients
				$ingredients = get_post_meta( get_the_ID(), '_ingredients', true );
				if( !empty( $ingredients ) ){
					echo '<ul class="ingredients">';
					foreach( $ingredients as $ingredient ){
						echo '<li class="ingredient" itemprop="ingredients"><span class="amount">'.$ingredient["amount"].'</span> <span class="item">'.$ingredient["item"].'</span></li>';
					}
					echo '</ul>';
				}
			?>

			</div>

			<div class="recipe-instructions" itemprop="recipeInstructions">

				<?php echo apply_filters( 'the_content', ( get_post_meta( get_the_ID(), '_instructions', true ) ) ); ?>

			</div>

			<?php wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'scaffolding' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) ); ?>

			<div class="nutrition-info" itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation">

			<?php //Collect and print the nutritional information for the recipe
				$serving_size = get_post_meta( get_the_ID(), '_serving_size', true );
				if(!empty($serving_size)){
					echo '<span class="nutrition-item serving-size" itemprop="servingSize">'.esc_html($serving_size).'</span>';
				}

				$calories = get_post_meta( get_the_ID(), '_calories', true );
				if(!empty($calories)){
					echo '<span class="nutrition-item calories" itemprop="cholesterolContent">'.esc_html($calories).'</span>';
				}

				$fat = get_post_meta( get_the_ID(), '_fat', true );
				if(!empty($fat)){
					echo '<span class="nutrition-item fat" itemprop="fatContent">'.esc_html($fat).'</span>';
				}

				$saturated_fat = get_post_meta( get_the_ID(), '_saturated_fat', true );
				if(!empty($saturated_fat)){
					echo '<span class="nutrition-item saturated-fat" itemprop="saturated_fat">'.esc_html($saturated_fat).'</span>';
				}

				$unsaturated_fat = get_post_meta( get_the_ID(), '_unsaturated_fat', true );
				if(!empty($unsaturated_fat)){
					echo '<span class="nutrition-item unsaturated-fat" itemprop="unsaturatedFatContent">'.esc_html($unsaturated_fat).'</span>';
				}

				$trans_fat = get_post_meta( get_the_ID(), '_trans_fat', true );
				if(!empty($unsaturated_fat)){
					echo '<span class="nutrition-item trans-fat" itemprop="transFatContent">'.esc_html($trans_fat).'</span>';
				}

				$cholesterol = get_post_meta( get_the_ID(), '_cholesterol', true );
				if(!empty($cholesterol)){
					echo '<span class="nutrition-item cholesterol" itemprop="cholesterolContent">'.esc_html($cholesterol).'</span>';
				}

				$sodium = get_post_meta( get_the_ID(), '_sodium', true );
				if(!empty($sodium)){
					echo '<span class="nutrition-item sodium" itemprop="sodiumContent">'.esc_html($sodium).'</span>';
				}

				$carbohydrates = get_post_meta( get_the_ID(), '_carbohydrates', true );
				if(!empty($carbohydrates)){
					echo '<span class="nutrition-item carbohydrates" itemprop="carbohydrateContent">'.esc_html($carbohydrates).'</span>';
				}

				$fiber = get_post_meta( get_the_ID(), '_fiber', true );
				if(!empty($fiber)){
					echo '<span class="nutrition-item fiber" itemprop="fiberContent">'.esc_html($fiber).'</span>';
				}

				$sugar = get_post_meta( get_the_ID(), '_sugar', true );
				if(!empty($sugar)){
					echo '<span class="nutrition-item sugar" itemprop="sugarContent">'.esc_html($sugar).'</span>';
				}

				$protein = get_post_meta( get_the_ID(), '_protein', true );
				if(!empty($protein)){
					echo '<span class="nutrition-item protein" itemprop="proteinContent">'.esc_html($protein).'</span>';
				}

			?>
			</div>

		</section><?php // end article section ?>

		<footer class="recipe-footer" role="contentinfo">

			<?php if(get_the_category_list()): ?>
			<p class="categories"><span class="categories-title">Categories:</span> <?php echo get_the_category_list(', ') ?></p>
			<?php endif; ?>

			<?php the_tags('<p class="tags"><span class="tags-title">Tags:</span> ', ', ', '</p>'); ?>

		</footer><?php // end article footer ?>

	</article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>