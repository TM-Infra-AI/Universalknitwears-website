<footer class="footer" role="contentinfo">
    <div id="inner-footer" class="row">
        <div class="large-12 medium-12 columns">
            <nav role="navigation">
                <?php joints_footer_links(); ?>
            </nav>
        </div>
        <div class="small-12 columns certificateS">
            <?php $query = new WP_Query( 'order=asc&category_name=certificates&post_status=publish'); ?>
            <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
            <?php the_content() ; ?>
            <?php endwhile;  wp_reset_postdata(); else : ?>
            <?php _e( 'Sorry, no posts matched your criteria.' ); ?>
            <?php endif; ?>
        </div>
       
    </div>
    <!-- end #inner-footer -->
     <div class="large-12 medium-12 columns copyrightInfo">
            <p class="source-org copyright">&copy; UNIVERSAL KNITWEARS . Mail: <a href="mailto:suraj@universalknitwears.com">suraj@universalknitwears.com</a> . Phone: 0091-9811222680</p>
        </div>
</footer>
<!-- end .footer -->
</div>
<!-- end .main-content -->
</div>
<!-- end .off-canvas-wrapper-inner -->
</div>
<!-- end .off-canvas-wrapper -->
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.min.js"></script>
<script>
jQuery('.certificates').slick({
    dots: true,
    autoplay: true,
    infinite: true,
    arrows: false,
    speed: 300,
    slidesToShow: 6,
    slidesToScroll: 1,
    responsive: [{
            breakpoint: 1024,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 1,
                infinite: true,
                dots: true
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
    ]
});
// jQuery('.certificates').slick({
// dots: false,
//         autoplay: true,
//         infinite: true,
//         arrows: false,
//         speed: 700,
//         slidesToShow: 5,
//         slidesToScroll: 1
// 	});
jQuery('.homeSlider').slick({
    dots: true,
    autoplay: true,
    infinite: true,
    arrows: false,
    speed: 700,
    slidesToShow: 1,
    slidesToScroll: 1
});
</script>
<?php if ( is_front_page() ) { ?>
<script>
jQuery(function() {
    accordion.init({
        id: 'accordion',
        expandWidth: 460,
        itemWidth: 120,
        extpand: 4,
        delay: 3000,
        animateTime: 400,
        borderWidth: 1,
        autoPlay: false,
        deviator: 30,
        bounce: "-0px"
    });
});
jQuery('tr.featured').sort(function(a, b) {

    return new Date(jQuery(a).find('input').val()).getTime() > new Date(jQuery(b).find('input').val()).getTime()

}).appendTo('tbody');
</script>
<?php } ?>
<?php wp_footer(); ?>
</body>

</html>
<!-- end page -->