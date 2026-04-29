<div class="orbit" role="region" aria-label="Favorite Space Pictures" data-orbit data-options="animInFromLeft:fade-in; animInFromRight:fade-in; animOutToLeft:fade-out; animOutToRight:fade-out;" >
  <div class="sliderBG">
  <ul class="homeSlider">
    	<?php 
		$query = new WP_Query( 'order=asc&category_name=slider&post_status=publish&posts_per_page=-1');
		
		$query_aboutus = new WP_Query( 'order=asc&category_name=about-us-slider&post_status=publish&posts_per_page=-1');
		
		if(is_front_page()):
		
			if ( $query->have_posts() ) : 
				while ( $query->have_posts() ) : $query->the_post();?>
				<li style="height: 404px; background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>)"></li>
				<?php
				endwhile;
				wp_reset_postdata(); 
			else :
				_e( 'Sorry, no posts matched your criteria.' );
			endif; 
		else:
			if ( $query_aboutus->have_posts() ) : 
				while ( $query_aboutus->have_posts() ) : $query_aboutus->the_post();?>
				<li style="height: 404px; background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>)"></li>
				<?php
				endwhile;
				wp_reset_postdata(); 
			else :
				_e( 'Sorry, no posts matched your criteria.' );
			endif;
		endif;
		
		?>	
	</ul>
	</div> 
<div class="sh_bottom"></div>

</div> 