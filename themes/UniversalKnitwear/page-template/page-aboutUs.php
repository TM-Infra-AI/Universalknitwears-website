
<div id="content">
	<div id="inner-content" class="row">
	    <div class="large-10 medium-10 small-centered " role="main">
	        <?php get_template_part( 'parts/slider', 'homepage' ); ?>
	        <div class="small-12 medium-12 large-12">
				<ul class="accordion" data-accordion data-allow-all-closed="true">
		        <?php $query = new WP_Query( 'order=asc&category_name=about-us&post_status=publish&posts_per_page=12paged='. get_query_var('paged')); ?>
        		<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
  	     	    <li class="accordion-item">
  	     	       <a href="#a<?php the_ID(); ?>" role="tab" class="accordion-title" id="<?php the_ID(); ?>-heading" aria-controls="<?php the_ID(); ?>"><?php the_title(); ?></a> 
					<div id="a<?php the_ID(); ?>" class="accordion-content" role="tabpanel" data-tab-content aria-labelledby="<?php the_ID(); ?>-heading">
					<?php the_content() ?>
					</div>          
         		</li>
          		<?php endwhile;?>
   	    		<?php wp_reset_postdata(); else : ?>
        		<?php _e( 'Sorry, no posts matched your criteria.' ); ?>
        		<?php endif; ?>						
			   </ul>
			</div>
			<?php get_template_part( 'parts/link', 'homepage' ); ?>
		</div> <!-- end #main -->    
	</div> <!-- end #inner-content -->	
</div> <!-- end #content -->