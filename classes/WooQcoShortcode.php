<?php
add_shortcode("wooqco",array('WooQcoShortcode','shortcode'));
class WooQcoShortcode{
	static function enqueueScripts(){
		wp_register_style( 'wooQco_Product_style', plugins_url('css/wooQcoFrontEnd.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQco_Product_style' );
	}
	static function frontEnd($atts){
		self::enqueueScripts();
		$quoteID = $atts['id'];
		?>
		<form id="WooQcoForm">
        <?php
		/*****************************************************************/
		/****************Retrieve Saved Data*********************************/
		/**********************************************************************/
		$totalFields = count(WooQcoModel::WooQcoGetValue("labels",$quoteID));
		$productsArray =  WooQcoModel::WooQcoGetValue("products",$quoteID);
		/************************************************************************/
		/************************************************************************/
		for($fieldNum = 1; $fieldNum<=$totalFields; $fieldNum++){
			$labelArray =  WooQcoModel::WooQcoGetValue('labels',$quoteID);	 //label
			$options = WooQcoModel::WooQcoGetValue('options',$quoteID );//options	
			$optionsArray = explode("\n", $options['options'.$fieldNum]);//Options array
			echo '<label>'.$labelArray['label'.$fieldNum].'</label>';
			echo '<select id="WooQcoField'.($fieldNum).'">';
			//$optionsArray = $options;//Options array
			for($i=0;$i<count($optionsArray);$i++){
		    	echo '<option ';
		    	if(preg_match('/\(([0-9. ]+?)\)/',$optionsArray[$i] ,$matches)){ // check any match found forvalue between parenthesis() or not
 					 $value = floatval(str_replace(array('(',')'),array('',''),$matches[0]));
					$optionsArray[$i] = str_replace($matches[0],"",$optionsArray[$i]);
			  		echo 'value="'.$value.'">';
		   			}
				else{
		 	  		echo 'value="'.$optionsArray[$i].'">';
					}
				echo $optionsArray[$i].'</option>';
			} //End main for
			echo '</select>';
		}
		?>
         <input TYPE="button" class="WooQcoFormsubmit" onclick="return false;" VALUE="Get Qoute">
</form>
<div class="WooQcouoteResult" >
</div>
<div class="WooQcoCartResult" >
</div>
<script>
jQuery('.WooQcoFormsubmit').click(function() {
	jQuery('.WooQcouoteResult').html('<img src="<?php echo plugins_url('images/processing.gif',dirname(__FILE__) );?>" />');
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    // We'll pass this variable to the PHP function example_ajax_reques

	var data=[];
	<?php for($fieldNum = 1; $fieldNum<=$totalFields; $fieldNum++) {
			echo 'data['.($fieldNum-1).'] = jQuery( "#WooQcoField'.$fieldNum.'").val();
			';
		}
	?>  
    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action':'WooQco_ajax_request_frontend',
			'security' : '<?php echo wp_create_nonce( "woo-sqo-ajax-nonce" ); ?>', //Nonce Check
			'data' : data,
			'quoteID' : <?php echo $quoteID;?>
        },
        success:function(data) {
            // This outputs the result of the ajax request
            jQuery('.WooQcouoteResult').html(data);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });  
              
});
</script>


<?php
		
	}//EOF
	static function shortcode( $atts ){
	ob_start(); // begin output buffering
	//$productsID = $atts['order'];
	self::frontEnd($atts);
	$output = ob_get_contents(); // end output buffering
    ob_end_clean(); // grab the buffer contents and empty the buffer
	return $output;
	} //EOF
}
?>