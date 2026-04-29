jQuery(document).foundation();
jQuery(document).ready(function() {
    jQuery('.accordion p:empty, .orbit p:empty').remove();
	jQuery('.archive-grid .columns').last().addClass( 'end' );
	jQuery('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]').wrap("<div class='flex-video'/>");
	jQuery('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]').wrap("<div class='flex-video'/>");
	
	
jQuery(".accordion .accordion-item:not(:first-child) .accordion-title").click(function(){
     document.getElementById("a69").setAttribute("style", "display:none");
  });
  
});
console.log('hello');

  jQuery(window).load(function() {
 // executes when complete page is fully loaded, including all frames, objects and images
 jQuery(document).ready( function() {
     document.getElementById("a69").setAttribute("style", "display:block");
  });
 
});
 