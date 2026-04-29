<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
	<header class="article-header">	
		<h1 class="entry-title single-title" itemprop="headline"><?php the_excerpt(); ?></h1>
		<?php //get_template_part( 'parts/content', 'byline' ); ?>
    </header> <!-- end article header -->
					
    <section class="entry-content" itemprop="articleBody">
    <table>
    	<tr>
    	<td colspan="2">
    		<?php the_post_thumbnail('large'); ?>
    	</td>
    	</tr>
    	<tr>
		    <td>
		    Start Date:
		    </td>
		    <td>
		    <?php if(get_field('event_start')){  echo  get_field('event_start') ;}?>
		    </td>
	    </tr>
        <tr>
		    <td>
		    End Date:
		    </td>
		    <td>
			<?php if(get_field('event_end')){  echo get_field('event_end') ;}?>
		    </td>
	    </tr>
        <tr>
		    <td>
		    Recurring Event:
		    </td>
		    <td>
		    <?php if(get_field('recurring_event')){  echo get_field('recurring_event') ;}?>
		    </td>
	    </tr>
        <tr>
		    <td>
		    Importance:
		    </td>
		    <td>
		    <?php if(get_field('importance')){  echo get_field('importance') ;}?>
		    </td>
	    </tr>
        <tr>
		    <td>
		    Description:
		    </td>
		    <td>
			<?php the_content(); ?>		    
		    </td>
	    </tr>
    </table>
		

	</section> <!-- end article section -->
						
	<footer class="article-footer">
		<p class="tags"><?php the_tags('<span class="tags-title">' . __( 'Tags:', 'jointswp' ) . '</span> ', ', ', ''); ?></p>	</footer> <!-- end article footer -->
									
	<?php comments_template(); ?>	
													
</article> <!-- end article -->