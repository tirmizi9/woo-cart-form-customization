<?php 
/*
Plugin Name: Woocommerce Custom Cart
Plugin URI: http://tirmizi.net/
Description: Woocommerce Custom Cart Customization in specific page
Version: 1.0.0
Author: Syed Muzaffar Tirmizi
Author URI: https://www.upwork.com/freelancers/syedtirmizi
License: GNU Public License v3
*/


add_action('wp_footer', 'mjt_wp_footer_function');
function mjt_wp_footer_function(){
	if(!is_cart() && !is_checkout()){
        ?>
        <script>
            jQuery(document).ready(function($){
                var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
                $('.qty').each(function(){
					var _this = $(this);
					var symbol = '<?php echo get_woocommerce_currency_symbol() ?>';
                    var id = $(this).closest('form.cart').find('.single_add_to_cart_button').val();
                    jQuery.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: {action: "sb_check_cart_existence", id : id},
                        dataType: "json",
                        cache: false,
                        success: function(response){
							console.log('testing ', response);
                            if(response.quantity > 0){
								_this.val(response.quantity);
								var price = parseInt(response.quantity) * parseFloat(response.price);
								var msg = '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:15px"><p><b>'+response.quantity + ' X ' + response.title +  '</b></p><br /></div>';
								_this.closest('form.cart').prepend(msg);
								_this.closest('form.cart').append('<h4 style="margin-top:20px">Total &nbsp;&nbsp;' + symbol + price.toFixed(2)+'</h4>');
                            }else{
								_this.val(1);
                            }
                        }
                    });
                });
            });
        </script>
    	<?php
	}
}
function mjt_check_cart_existence() {
	$id = $_POST['id'];
	$product_cart_id = WC()->cart->generate_cart_id( $id );
   	$in_cart = WC()->cart->find_product_in_cart( $product_cart_id );
	$res = [];
   if ( $in_cart ) {
	   	$cart = WC()->cart->get_cart();
		if( WC()->cart->find_product_in_cart( $product_cart_id )) {
			$_product = wc_get_product( $id );
			$res['quantity'] = $cart[$product_cart_id]['quantity'];
			$res['price'] = $_product->get_price();
			$res['title'] = $_product->get_title();
		}
   }
	else{
		$res['quantity'] = 0;
	}
	echo json_encode($res, true);
    die();
}
add_action( 'wp_ajax_sb_check_cart_existence', 'mjt_check_cart_existence' );
add_action( 'wp_ajax_nopriv_sb_check_cart_existence', 'mjt_check_cart_existence' );