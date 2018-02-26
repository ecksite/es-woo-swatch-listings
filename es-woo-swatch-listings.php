<?php
/**
 * Plugin Name:       esWooSwatchListings
 * Plugin URI:        https://www.ecksite.de
 * Description:       Simple extension for Wordpress with WooCommerce + WooCommerce Variation Swatches (by Themealien) installed. Make single attribute swatches (Type: Color or Image) appear in product listings. Needs WooCommerce + WooCommerce Variation Swatches (by Themealien) in order to work.
 * Version:           1.0.0
 * Author:            ecksite
 * Author URI: 		  https://www.ecksite.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 4.5
 * Tested up to: 	  4.8
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


add_action( 'woocommerce_after_shop_loop_item', 'es_display_product_color_options', 9 );
function es_display_product_color_options(){
	global $woocommerce, $product;

	//Check: is plugin WooCommerce Variation Swatches installed and activated??
	if( !class_exists('TA_WC_Variation_Swatches') ){ return;}

	//Check: is product with variation?
	if( !$product->has_child() ){return;} 

	$product_variable 		= new WC_Product_Variable( $product->get_id() );
	$variation_atts 		= $product_variable->get_variation_attributes(); 		//the variation attributes
	$variations 			= $product_variable->get_available_variations(); 		//the variations themselves

	//Check: more than one variation attribute?
	if( count($variation_atts) >  1 ) {return;} 

	reset($variation_atts);
	$variation_attr_taxonomy = key($variation_atts);

	$swatch_types = TA_WCVS()->types;												//gets supported swatch taxonomy types
	$attr         = TA_WCVS()->get_tax_attribute( $variation_attr_taxonomy );		//gets type of swatch taxonomy
	
	//Check: is supported taxonomy (WooCommerce Variation Swatches type)? 
	if ( empty( $attr ) || !array_key_exists( $attr->attribute_type, $swatch_types )) { return;}

	$product_atts		 	= $product->get_attributes(); //needed for options
	$product_atts 			= $product_atts[$variation_attr_taxonomy];
	$swatch_terms 			= $product_atts['options'];

	switch ($attr->attribute_type) {
			
		case 'color':
		
			$match_array = array();	
			foreach ( $variations as $key => $variation ) {
				$term = get_term_by( 'slug', $variation['attributes']['attribute_' . $variation_attr_taxonomy] , $variation_attr_taxonomy);
				$term_id = intval($term->term_id);
				$match_array[$term_id] = $variation['image']['thumb_src'];
			}
			
			echo '<div class="es-swatches-wrap" data-es-woo-swatches-product="'. $product->get_id() .'">';
			echo "<div class='es-swatches-container es-swatches-options'>";

			foreach ($swatch_terms as $swatch_term_id) {
				es_woo_swatches_listings_before_picker_item($match_array[$swatch_term_id]);
				echo '<span class="es-swatches-color-options es-swatch es-swatch-color" style="background-color:' . get_term_meta($swatch_term_id,'color',true) . '"></span>';
				es_woo_swatches_listings_after_picker_item();
			}
			
			echo "</div>";
			echo '</div>';

			break;

		case 'image':
			$match_array = array();	
			foreach ( $variations as $key => $variation ) {
				$term = get_term_by( 'slug', $variation['attributes']['attribute_' . $variation_attr_taxonomy] , $variation_attr_taxonomy);
				$term_id = intval($term->term_id);
				$match_array[$term_id] = $variation['image']['thumb_src'];
			}
			
			echo '<div class="es-swatches-wrap" data-es-woo-swatches-product="'. $product->get_id() .'">';
			echo "<div class='es-swatches-container es-swatches-options'>";

			foreach ($swatch_terms as $swatch_term_id) {
				es_woo_swatches_listings_before_picker_item($match_array[$swatch_term_id]);
				echo '<span class="es-swatches-color-options es-swatch es-swatch-image" style="background: url(' . wp_get_attachment_thumb_url( get_term_meta($swatch_term_id,'image',true) ). ') center center no-repeat; background-size: 50px auto;"></span>';
				es_woo_swatches_listings_after_picker_item();
			}
			
			echo "</div>";
			echo '</div>';

			break;
		default: break;
	}

}

function es_woo_swatches_listings_before_picker_item($imgurl) {
	$variation_image_url = $imgurl;
	?>
	<span class="es-swatches-option-item es-swatches" data-es-woo-swatches-variation-image="<?php echo esc_attr($variation_image_url); ?>">
	<?php
}

function es_woo_swatches_listings_after_picker_item() {
	?>
	</span>
	<?php
}


add_action( 'wp_enqueue_scripts', 'es_woo_swatch_scripts');
function es_woo_swatch_scripts(){
	wp_enqueue_script( 'es-woo-swatch-listings_script', plugins_url( 'assets/js/frontend.js', __FILE__ ), array('jquery'), '', true );
	wp_enqueue_style( 'es-woo-swatch-listings_style', plugins_url( 'assets/css/frontend.css', __FILE__ ));
}
