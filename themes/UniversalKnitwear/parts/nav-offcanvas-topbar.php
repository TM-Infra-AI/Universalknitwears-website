<!-- By default, this menu will use off-canvas for small
	 and a topbar for medium-up -->
<div class="top-bar" id="top-bar-menu">	
<div class="top-bar-left float-left">
		 <a class="logo" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a> 
	</div>
<div class="row custom_top show-for-medium">
				<?php wp_nav_menu( array( 'theme_location' => 'top-menu' ) ); ?>

	<div class="top-bar-right show-for-medium">
		<?php joints_top_nav(); ?>	
	</div>
</div>
<div class="top-bar-right float-right show-for-small-only">
		<ul class="menu">
			<li><button class="menu-icon" type="button" data-toggle="off-canvas"></button></li>
			<li><a data-toggle="off-canvas"><?php _e( '', 'jointswp' ); ?></a></li>
			<li class="mobile_cart"><?php wp_nav_menu( array( 'theme_location' => 'top-menu' ) ); ?></li>
		</ul>
	</div>
</div>