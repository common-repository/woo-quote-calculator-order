<?php
class WooQcoAdmin{
	static function enqueueScripts(){
		wp_register_style( 'wooQoute_NewQuoteCss', plugins_url('css/newQuoteCss.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQoute_NewQuoteCss' );
		wp_register_style( 'wooQoute_Font_Awesome', plugins_url('css/font-awesome.min.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQoute_Font_Awesome' );
		wp_register_style( 'wooQoute_Product_style', plugins_url('css/productTable.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQoute_Product_style' );	
		wp_register_script( 'wooQoute_error_check', plugins_url('js/checkForError.js',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_script( 'wooQoute_error_check' );
	}//EOF

	/******************************************************************/
	/****************Listing of all Quotes********************************/
	/***********************************************************************/
	static function quotesManager(){
		self::enqueueScripts();
		WooQcoModel::quoteManager();
	}//EOF
	
	static function export(){
		self::enqueueScripts();
		WooQcoModel::export();
	} //EOF
	static function import(){
		self::enqueueScripts();
		WooQcoModel::import();
	} //EOF
	static function addEditWooQco(){
			self::enqueueScripts();
		?>
<div id="container">
	<div class="tabs">

	    <input type="radio" name="tabs" id="tab1" checked >
	    <label for="tab1">
	        <i class="fa fa-hand-paper-o"></i><span>FIELDS</span>
	    </label>

	    <input type="radio" name="tabs" id="tab2">
	    <label for="tab2">
	        <i class="fa fa-shopping-basket"></i><span>PRODUCTS</span>
	    </label>

	    <input type="radio" name="tabs" id="tab3">
	    <label for="tab3">
	        <i class="fa fa-superscript"></i><span>FORMULA</span>
	    </label>
	    <label for="tab4" id="WooQcoSaveTab">
	        <i class="fa fa-tachometer"></i><span>Save</span>
	    </label>

	    <div id="tab-content1" class="tab-content">
          <?php  wooQcoQuoteForm::adminForm(); 
		   $quoteID = WooQcoModel::getThisQuoteId();
		   ?>
         <p align="center"> <strong>Quote Name &nbsp;</strong><input id="quoteName" type="text" size="40" value="<?php echo WooQcoModel::WooQcoGetValue("name",$quoteID);?>"/></p>
	    </div> <!-- #tab-content1 -->

	    <div id="tab-content2" class="tab-content">
	     <?php WooQcoProducts::productsSelect(); ?>

	    </div> <!-- #tab-content2 -->

	    <div id="tab-content3" class="tab-content">
        <?php WooQcoFormula::formulaBoxes($quoteID); ?>
        <script language="javascript">
			jQuery('#tab3').click(function() {	
			jQuery('#tab-content3').html('');
			jQuery('#tab-content3').html('<img src="<?php echo plugins_url('images/loading.gif',dirname(__FILE__) );?>"  align="middle" />');
		
				var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
				var data = "data";
    			// This does the ajax request
   				jQuery.ajax({
        		url: ajaxurl,
        		data: {
            		'action' : 'WooQco_formula_load',
					'security' : '<?php echo wp_create_nonce( "woo-sqo-ajax-nonce" ); ?>', //Nonce Check
            		'data' : data,  //Sending data ajaxrequest
					'quoteID' : <?php echo $quoteID;?>
        		},
        		success:function(data) { //Showing notification that data saved
 				jQuery("#tab-content3").html(data);
      		  },
        	error: function(errorThrown){
            console.log(errorThrown);
        	}
				});
		});
		</script>
	    </div> <!-- #tab-content3 -->
 
	</div> <!-- .tabs -->
<h4 align="center">Woo Quote Calculator & Order  :: <span style="font-size:14px !important; vertical-align:middle">Add New Quote</span></h4>
</div>
<script language="javascript">
/***************************************************/
/*************Check Blank form before submit**********/
/********************************************************/
jQuery('#WooQcoSaveTab').click(function() {	
	if(checkForErrors()){
		var error = jQuery('<div>').prop('id', 'error');	 //Overlay
  		error.appendTo('body'); 
		setTimeout(function(){
		error.remove(); //Remove overlay div
			  }, 2000);
		return false;
	}
	var saving = jQuery('<div>').prop('id', 'saving');	 //Overlay

 		  saving.appendTo('body'); //show overlay div succes


	/********************************************************************************/
	/**********************Form Data Initialization for Ajax Submit*******************/
	/**********************************************************************************/
	var quoteName = jQuery('#quoteName').val();
	var labelsData = jQuery('#wooQcoFrm').find("input").serialize(); //Forms data
	var optionsData = jQuery('#wooQcoFrm').find("textarea").serialize(); //Forms data
	var productData = [];
				jQuery('#WooQcoProducts input:checked').each(function() {
    			productData.push(jQuery(this).attr('value')); //Getting all checked items
				});
	var formulaData =  jQuery('#formulaTable').find("input").serialize(); //Forms data
	/**************************************************************************************/
	/*********************************Ajax Submission**************************************/
	/************************************************************************************/
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    			// This does the ajax request
   				jQuery.ajax({
        		url: ajaxurl,
        		data: {
            		'action' : 'WooQco_save_admin_data',
					'security' : '<?php echo wp_create_nonce( "woo-sqo-ajax-nonce" ); ?>', //Nonce Check
					'quoteID'  : '<?php if(isset($_GET['quoteID'])) echo intval($_GET['quoteID']);?>',
					'quoteName' : quoteName,
            		'labelsData' : labelsData,
					'optionsData' : optionsData,
					'productData' : productData,
					'formulaData' : formulaData
        		},
        		success:function(data) { //Showing notification that data saved
					saving.remove(); //Remove overlay div
			  	jQuery(".tabs").append(data); //Output message
      		  },
        	error: function(errorThrown){
            console.log(errorThrown);
        	}
				});
       /***************************************************************************************/
});
</script>
 <?php
	}//EOF
}
?>