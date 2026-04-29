(function ($) {
    $.each(['show', 'hide'], function (i, ev) {
        var el = $.fn[ev];
        $.fn[ev] = function () {
            this.trigger(ev);
            return el.apply(this, arguments);
        };
    });
})(jQuery);
    

jQuery(document).ready(function($){
    /* AJAX functions */
        
    $(document).on('click', '#add_to_quote', function(){             
        
        $('input[name=add-to-cart]').remove();
        
        var $thisbutton = $(this);
        var $container = $( "." + $(this).data('button') );
        
        $thisbutton.removeClass( 'added' );
        $thisbutton.addClass( 'loading' );

        //var data = new FormData($('form.cart')[0]);
        var data = new FormData( $(this).parents( "form" )[0] );        
        data.append('product_type', $(this).data('product-type'));
        data.append('product_id', $(this).data('product-id'));
        data.append('is_quote', $(this).data('is_quote'));
                
        $.ajax({
            url: adqAjax.ajaxurl+'?action=add_to_quote', 
            type: 'POST',
            // Form data
            data: data,
            async: false,
            success: function(response){                                                     

                        if( response.message != '') {
                                $thisbutton.removeClass( 'loading' );
                                $thisbutton.addClass( 'added' );
                                
                                $container.parent().append(response.message);
                        }
                        else { 
                            var this_page = window.location.toString();  
                            
                            if( adqAjax.redirectUrl && response.result != 0) {
                                this_page = adqAjax.redirectUrl; 
                            }
                            
                            window.location = this_page;
                        }
                        
                        return;
                    },            
            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false,          
            dataType: 'json'
        });
        return false;
    });
    
    
    $(document).on('click', '#add_to_quote_loop', function(){    
        
        var $thisbutton = $( this );
        
        $thisbutton.removeClass( 'added' );
        $thisbutton.addClass( 'loading' );
        
        $.post(
            adqAjax.ajaxurl, 
            {
                'action': 'add_to_quote',
                'product_id' : $(this).data('product-id'),
                'quantity' : $(this).data('quantity'),
                'is_quote' : $(this).data('is_quote'),
            }, 
            function(response) {     
                
                    if( response.message != '') {
                            $thisbutton.removeClass( 'loading' );
                            $thisbutton.addClass( 'added' );
                            
                            $thisbutton.parent('.single_add_to_quote_loop').parent().append(response.message);
                    }
                    else { 
                        var this_page = window.location.toString();  

                        if( adqAjax.redirectUrl && response.result != 0) {
                            this_page = adqAjax.redirectUrl; 
                        }

                        window.location = this_page;
                    }
                    
                    return;
            },
            'json'
        );
        return false;
    });
    
    
    $(document).on('click', '#remove_all_items', function(){
            
            $.post(
                adqAjax.ajaxurl, 
                {
                    'action': 'remove_all_list',
                }, 
                function(response){                    
                    var this_page = window.location.toString();                

                    window.location = this_page;
                    return;                    
                },
                'json'
            );
                
            return false;
    });


    $(document).on('click', '.remove_quote_item', function(){
            removeItem ( $(this), true );
            
            return false;
    });
    
    $(document).on('change', '.adq_qty_list', function(){                                 
        updateQuantity ( $(this), $(this).val() );
        
        return false;
    });
    
    $(document).on('click', 'input.minus', function(){ 
            var item = $(this).next();
            updateQuantity ( item, parseInt( item.val() ) - 1 );

            return false;
    });
    
    $(document).on('click', 'input.plus', function(){   
            var item = $(this).prev();
            updateQuantity ( item, parseInt( item.val() ) + 1 );

            return false;
    });
    
    function updateQuantity (item, qty) {
            $.post(
                adqAjax.ajaxurl, 
                {
                    'action': 'add_quantity',
                    'cart_item_key' : item.data('cart_item_key'),
                    'product_id' : item.data('product_id'),
                    'quantity' : qty,
                }, 
                function(response){
                    var this_page = window.location.toString();                

                    window.location = this_page;
                    return;
                },
                'json'
            );
    }
    
    function removeItem (item, redirect) {            
            $.post(
                adqAjax.ajaxurl, 
                {
                    'action': 'remove_from_list',
                    'cart_item_key' : item.data('cart_item_key'),
                    'product_id' : item.data('product_id')
                }, 
                function(response){
                    if(redirect) {
                        var this_page = window.location.toString();                

                        window.location = this_page;
                        return;
                    }
                },
                'json'
            );
    }
    //cart_item_key
    $(document).on('keyup', '.quote_product_meta', function(){  
        if( ajaxReq ) {
            ajaxReq.abort();
        }
        
        var ajaxReq = $.post(
            adqAjax.ajaxurl, 
            {
                'action': 'add_quote_item_meta',
                'cart_item_key': $(this).data('cart_item_key'),
                'meta_value': $(this).val(),
                'meta_key': $(this).attr('name')
            }, 
            function(response){
            },
            'json'
        );
        return false;
    });
    
    
    /**
    * Order Notes Panel
    */
    var wc_meta_boxes_order_notes = {
           init: function() {
                   $( 'div.add_note' )
                           .on( 'click', 'a.add_note', this.add_order_note )
                           .on( 'click', 'a.delete_note', this.delete_order_note );

           },

           add_order_note: function() {
                   if ( ! $( 'textarea#add_order_note' ).val() ) {
                           return;
                   }

                   $( 'div.add_note' ).block({
                           message: null,
                           overlayCSS: {
                                   background: '#fff',
                                   opacity: 0.6
                           }
                   });

                   var data = {
                           action:    'adq_add_order_note',
                           post_id:   $(this).data('post_id'),
                           note:      $( 'textarea#add_order_note' ).val(),
                           note_type: $( 'input#order_note_type' ).val(),
                           security:  adqAjax.add_order_note_nonce,                           
                   };

                   $.post( adqAjax.ajaxurl, data, function( response ) {
                            $( 'ol.commentlist' ).prepend( response );
                            $( 'div.add_note' ).unblock();
                            $( '#add_order_note' ).val( '' );
                           
                            var this_page = window.location.toString();
                            window.location = this_page;
                   });

                   return false;
           },
    };
    
    wc_meta_boxes_order_notes.init();
    
    
    
    /* NOT Ajax */
    
    $( 'a.showlogin' ).click( function() {
            $( 'form.login' ).slideToggle();

            return false;
    });       
    
    $('a.added_to_quote').slideDown( 200 );
    
    $('a.showbilling').click( function() {
            $( 'div.adq-billing' ).slideToggle();
            return false;
    });
    
    $('a.show_product_meta').click( function () {            
            $('.product_meta_'+$(this).data('cart_item_key')).slideToggle( 200 );
            $(this).slideToggle('fast');
            return false;
    });
    
    var toggleShippingAddress = function() {
        if ($('#ship-to-different-address-checkbox').is(':checked')) {
                $('div.shipping_address').slideDown( 200 );
        }
        else {
                $('field-hiddendiv.shipping_address').slideUp( 200 );
        }
    };
    
    var toggleAdqShipping = function() {
        if ($('#adq_shipping_method').is(':checked')) {
                $('.adq-shipping').slideDown( 200 );
        }
        else {
                $('.adq-shipping').slideUp( 200 );
        }
    };
    
    $( '#ship-to-different-address-checkbox' ).on( 'change', toggleShippingAddress );
    
    $( '#adq_shipping_method' ).on( 'click', toggleAdqShipping );
    
    if (typeof($( '#adq_shipping_method' )) != 'undefined' && $( '#adq_shipping_method' ) != null) {
            toggleAdqShipping();
            toggleShippingAddress();
    }
    
    $( 'input#createaccount' ).change( function() {
            $( 'div.create-account' ).hide();

            if ( $( this ).is( ':checked' ) ) {
                    $( 'div.create-account' ).slideDown();
            }
    }).change();
    
    $( 'body' ).on("country_to_state_changing", function(){
            $(".adq-billing .field-hidden").each( function() {    
                    $(this).hide();
            });
    });
    
});