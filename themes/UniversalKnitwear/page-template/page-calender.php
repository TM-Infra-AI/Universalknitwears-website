<div id="content">
	<div id="inner-content" class="row">
			<?php custom_breadcrumbs(); ?>
	    <div class="large-10 medium-10 small-centered calender" role="main">	
        <div class="row">
            <?php $query = new WP_Query( 'order=asc&post_type=calender&post_status=publish&posts_per_page=12paged='. get_query_var('paged')); ?>
            <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
        
            <div class="small-6 medium-3 large-3 columns months">
                <h4><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
             <a href="<?php the_permalink() ?>"><div class="months-img" style="background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>)"></div></a>
            </div>
        
        <?php endwhile;?>
   	    <?php wp_reset_postdata(); else : ?>
        <?php _e( 'Sorry, no posts matched your criteria.' ); ?>
        <?php endif; ?>						
		</div> <!-- end #main -->
        </div>
	</div> <!-- end #inner-content -->
</div> <!-- end #content -->