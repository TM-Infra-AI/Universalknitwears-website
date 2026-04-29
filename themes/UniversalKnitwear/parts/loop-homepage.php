<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>  itemscope itemtype="http://schema.org/WebPage">					
    <section class="entry-content" itemprop="articleBody">
    <!-- empty section -->
      <?php the_content(); ?>
	    <?php wp_link_pages(); ?>
	</section> <!-- end article section -->				
</article> <!-- end article -->