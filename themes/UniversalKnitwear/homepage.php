<?php
/*
Template Name: Home Page
*/
 get_header(); ?>					
	<div id="content">
		<div id="inner-content" class="row">
		    <main id="main" class="large-10 medium-10 small-centered">
				<?php get_template_part( 'parts/slider', 'homepage' ); ?>

				<h2>HOME FURNISHING</h2>
				<?php get_template_part( 'parts/acc', 'homepage' ); ?>
				<?php get_template_part( 'parts/accordion', 'homepage' ); ?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php get_template_part( 'parts/loop', 'homepage' ); ?>
				<?php endwhile; endif; ?>							
				<?php get_template_part( 'parts/link', 'homepage' ); ?>
			</main> <!-- end #main -->
		</div> <!-- end #inner-content -->
	</div> <!-- end #content -->
<?php get_footer(); ?>