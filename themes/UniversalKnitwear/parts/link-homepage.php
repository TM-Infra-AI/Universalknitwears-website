<div class="linkSection row">
    	<div class="small-12 medium-6 large-6 columns" >
		<h2>To Remember</h2>
			<div style="background:#ffffff;overflow:hidden;" >
				<?php 
				$currentMonth = time();
				?>
				<table class="toRemember">
			        <thead>
			          <tr>
			            <th style=" width: 25%">Event Start</th>
			            <th style=" width: 25%" >Event End</th>
			            <!--<th>timestamp</th>-->
						<th>Title</th>
			          </tr>
			        </thead>
			    
				
				<?php 
				// query
					
				$the_query = new WP_Query(array(
					'post_type'			=> 'calender',
					'posts_per_page'	=> -1,
					'meta_key'			=> 'event_start',
					'orderby'			=> 'meta_value',
					'order'				=> 'DESC'
				));
				?>
				<?php if( $the_query->have_posts() ): ?>
					<?php while( $the_query->have_posts() ) : $the_query->the_post(); $class = get_field('upcoming_events') ? 'class="featured"' : '';	?>
					<?php if(get_field('event_start') && get_field('event_end')):?>
					
					
						<?php 					
						$startDate= get_field('event_start');
						$startEventDate = strtotime($startDate);
						if($startEventDate >= $currentMonth):
						?>				
						<tr <?php echo $class; ?>class ="hide">
						<div class="small-12 medium-12 large-12 float-left">
						<td>
							<?php if(get_field('event_start')){  echo  get_field('event_start') ;}?><input type="hidden" value="<?php echo  get_field('event_start') ;?>">
						</td>
						<td>
							<?php if(get_field('event_end')){  echo get_field('event_end') ;}?>
						</td>
						<td>
							<a class="" href="<?php the_permalink() ?>" rel="bookmark" title="Link to <?php the_title_attribute(); ?>"><?php the_excerpt(); ?></a></td>
						</div>
						</tr>
						<?php endif; ?>	
					
					<?php endif; ?>	
					<?php endwhile;?>
				<?php endif; ?>					
			   	
				<?php wp_reset_query();	 // Restore global post data stomped by the_post(). ?>
			
			</table>
			</div>
    </div>
<?php $query = new WP_Query( 'order=asc&pagename=about-us&post_status=publish&posts_per_page=1paged='. get_query_var('paged')); ?>
   	<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>  
 	<div class="small-12 medium-6 large-6 columns" >
	<h2><?php the_excerpt(); ?></h2>
	<div class="sliderBG">
	<a class="trickyLinkToSLide" href="<?php the_permalink() ?>" rel="bookmark" title="Link to <?php the_title_attribute(); ?>">
		<div class="linkBG" style="height: 194px; background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>)">
		<?php //the_title(); ?>
		</div>
	</a>	
  	</div>
    </div>			 
   	<?php endwhile;?>
	<?php wp_reset_postdata(); else : ?>
	<?php _e( 'Sorry, no posts matched your criteria.' ); ?>
<?php endif; ?>	
</div>