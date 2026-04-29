<?php get_header(); ?>
	
	<div id="content">
	
		<div id="inner-content" class="row">
			<?php custom_breadcrumbs(); ?>
		    <main id="main" class="large-10 medium-10 small-centered" role="main">
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			    	<?php get_template_part( 'parts/loop', 'page' ); ?>
			    
			    <?php endwhile; endif; ?>							
			    					
			</main> <!-- end #main -->

		    <?php //get_sidebar(); ?>
		    
		</div> <!-- end #inner-content -->

	</div> <!-- end #content -->

<?php get_footer(); ?>