<?php
class WooQcoProducts{
	static function enqueueScripts(){
		wp_register_style( 'wooQco_Product_style', plugins_url('css/productTable.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQco_Product_style' );
		wp_register_script( 'wooQco_Product_table_sort', plugins_url('js/tableRowSorter.js',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script( 'wooQco_Product_table_sort' );
	}
static function productsSelect(){
		self::enqueueScripts();
		$quoteID = WooQcoModel::getThisQuoteId();
		?>
             <table id="WooQcoProducts" class="productTable" >
  <thead>
    <tr>
      <th>Select</th>
      <th>Product Id</th>
      <th>Name</th>
      <th>Price</th>
    </tr>
  </thead>
  <tbody><?php
  		$productArray = array();
		if($quoteID) { //If not new quote
		$productArray = WooQcoModel::WooQcoGetValue("products",$quoteID); //Get Saved products from option table
		foreach($productArray as $productID){ //Check if imported data match products else set array to null
			$productCheck = wc_get_product($productID);
			if(!$productCheck->product_type){ 
				$productArray = array();
			}
		}
		foreach($productArray as $productID){ //Doing for saved data
		$productID = intval($productID);
		$productName = get_the_title($productID);
		$product = wc_get_product( $productID );
		$price = $product->get_price_html();
		$image = $product->get_image();
		$url = get_permalink($productID);
		echo' <tr>
      <td data-label="Select">   <input type="checkbox" value="'.$productID.'" ';
		  echo 'checked="checked"';  //Checking the saved one
	  echo ' /></td>
      <td data-label="Product Id">'.$productID.'</td>
	  <td data-label="Name"><a href="'.$url.'" target="_blank">'.$productName." ".$image.'</a></td>
      <td data-label="Price">'.$price.'</td>
    </tr>';
		} //End Foreach
		
		}
		$params = array( //Custom Query for Woocommerce products
  	   		'posts_per_page' => -1,
    	    'post_type' => 'product'
		);
		$wc_query = new WP_Query($params); 
		if ($wc_query->have_posts()) : 
		while ($wc_query->have_posts()) : 
        $wc_query->the_post(); 
		$productID = get_the_id();  
		if(!in_array($productID,$productArray)){ //Only unsaved products now
			$productName = get_the_title();
			$product = wc_get_product( $productID );
			$price = $product->get_price_html();
			$image = $product->get_image();
			$url = get_permalink($productID);
			echo' <tr>
      		<td data-label="Select">   <input type="checkbox" value="'.$productID.'" ';
	  		echo ' /></td>
      		<td data-label="Product Id">'.$productID.'</td>
	  		<td data-label="Name"><a href="'.$url.'" target="_blank">'.$productName." ".$image.'</a></td>
      		<td data-label="Price">'.$price.'</td>
    		</tr>';
			}
		endwhile;
		wp_reset_postdata();
 		endif;
		?>
  </tbody>
</table>


        <?php
		 WooQcoModel::ajaxCallScript(".WooQcoFrmBtn", "", "WooQcoProductsSave", "var data = [];
				jQuery('#WooQcoProducts input:checked').each(function() {
    			data.push(jQuery(this).attr('value')); //Getting all checked items
				});"); 
				//Ajax call function (button, form, action ,additional condition)
	} //EOF
	
}
?>