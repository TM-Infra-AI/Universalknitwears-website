<?php
/*
Template Name: Full Width (No Sidebar)
*/
?>

<?php get_header(); ?>		
	<div id="content">
		<div id="inner-content" class="row">
		<?php custom_breadcrumbs(); ?>
		<div class="large-10 medium-10 small-centered " role="main">	
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<?php get_template_part( 'parts/loop', 'page' ); ?>
			<?php endwhile; endif; ?>							
		</div> <!-- end #main -->
		</div> <!-- end #inner-content -->
	</div> <!-- end #content -->
<?php get_footer(); ?>