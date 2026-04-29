jQuery(document).ready(function($){
    $('.attachment_delete').click(function() {
        $('.input_text').each( function() { $(this).val('') });
        return false;
    });
    
    var toggleShippingQuotelist = function() {

        if ($('#adq_inherit_shipping_conf').is(':checked')) {
                $('.adq_inherit_shipping').slideUp( 200 );
        }
        else {
                $('.adq_inherit_shipping').slideDown( 200 );                
        }
    };
    
    $( '#adq_inherit_shipping_conf' ).on( 'change', toggleShippingQuotelist );
    
    if (typeof($( '#adq_inherit_shipping_conf' )) != 'undefined' && $( '#adq_inherit_shipping_conf' ) != null) {
            toggleShippingQuotelist();
    }
    
    
    var toggleTermsVisibility = function() {
        
        $('.adq_terms_visibility').each(function() {
                $(this).remove();
        });   
        
        var taxonomy = $('#adq_taxonomy_filter').val();
        if ( taxonomy == "" )
            return;                 
        
        $.post(
            ajaxurl, 
            {
                'action': 'adq_taxonomy_load',
                'taxonomy': taxonomy,
            }, 
            function(response){                
                $( ".form-table tbody" ).append(response);
                
                $( document.body ).trigger( 'wc-enhanced-select-init' );
                
                $('.adq_'+taxonomy+'_terms').slideDown( 200 );
                
                return;
            },
            'html'
        );
    };
    
    if ( typeof $('#adq_taxonomy_filter').val() != 'undefined' ) {
        
            $("#adq_taxonomy_filter").on( 'change', toggleTermsVisibility );
            toggleTermsVisibility();
    }

    
    var toggleProductAdqOptions = function( el ) {                       
        if (el.is(':checked')) {
                el.next( "div" ).slideUp( 200 );
        }
        else {
                el.next( "div" ).slideDown( 200 );                
        }
    };            
    
    $( '#_adq_inherit_visibility_quote' ).change(function() { toggleProductAdqOptions ($(this)) });
    $( '#_adq_inherit_visibility_price' ).change(function() { toggleProductAdqOptions ($(this)) });
    $( '#_adq_inherit_visibility_cart' ).change(function() { toggleProductAdqOptions ($(this)) });
    $( '#_adq_inherit_allow_product_comments' ).change(function() { toggleProductAdqOptions ($(this)) });    
    
    if (typeof($( '.adq_option_products' )) != 'undefined' && $( '.adq_option_products' ) != null) {
            $(".adq_option_products").find("input[type='checkbox']").each (function () {
                    toggleProductAdqOptions($(this));
            });            
    }
    
    // File inputs
    $('#woocommerce-quotation-addons').on('click','.downloadable_files a.insert',function(){
            $(this).closest('.downloadable_files').find('tbody').append( $(this).data( 'row' ) );
            return false;
    });
    $('#woocommerce-quotation-addons').on('click','.downloadable_files a.delete',function(){
            $(this).closest('tr').remove();
            return false;
    });

});