<div class="accordion-panel show-for-large">
    <div class="large-12 text-center accordion-container">
        <div class="pana-accordion" id="accordion">
          <div class="pana-accordion-wrap">
            <?php $query1 = new WP_Query( 'order=asc&category_name=product&post_status=publish&posts_per_page=5&paged='. get_query_var('paged')); ?>
            <?php if ( $query1->have_posts() ) : while ( $query1->have_posts() ) : $query1->the_post(); ?>
              <div class="pana-accordion-item accBG" style="background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>)">
                <h2 class="pana-hdline"><?php the_title(); ?></h2>
              </div>
            <?php endwhile;  wp_reset_postdata(); else : ?>
            <?php _e( 'Sorry, no posts matched your criteria.' ); ?>
            <?php endif; ?>
          </div>
        </div>
    </div>
  </div>