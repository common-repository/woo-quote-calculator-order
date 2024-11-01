<?php 
add_action( 'wp_ajax_WooQco_save_admin_data', array('WooQcoModel','saveAdminData') );
add_action( 'wp_ajax_WooQcoDataSave', array('WooQcoModel','fieldDataSave') );
add_action( 'wp_ajax_WooQcoProductsSave', array('WooQcoModel','productsSave') );
add_action( 'wp_ajax_WooQcoFormulaSave', array('WooQcoModel','formulaSave') );
add_action( 'wp_ajax_WooQco_formula_load', array('WooQcoModel','formulaLoad') );
add_action( 'wp_ajax_WooQco_ajax_request_frontend',  array('WooQcoModel','ajaxFrontend') );
add_action( 'wp_ajax_nopriv_WooQco_ajax_request_frontend',  array('WooQcoModel','ajaxFrontend') );
add_action( 'wp_ajax_WooQco_ajax_request_add_to_cart',  array('WooQcoModel','ajaxAddToCart') );
add_action( 'wp_ajax_nopriv_WooQco_ajax_request_add_to_cart',  array('WooQcoModel','ajaxAddToCart') );

class WooQcoModel{
	static $tableName = "woo_quote_calculator";
	
	/*******************************************************************************************/
	/**********************Ajax Data Save From Admin*********************************************/
	/***********************************************************************************************/
	static function saveAdminData(){
	if ( isset($_REQUEST['quoteID']) && isset($_REQUEST['quoteName']) && isset($_REQUEST['labelsData']) && isset($_REQUEST['optionsData']) && isset($_REQUEST['productData'])  && isset($_REQUEST['optionsData']) && check_ajax_referer( 'woo-sqo-ajax-nonce', 'security' ) ) {
		global $wpdb;
		$dbTable = $wpdb->prefix.self::$tableName;
		$quoteName = sanitize_text_field($_REQUEST['quoteName']);	//Sanitization of fields
		parse_str($_REQUEST['labelsData'], $labelsData); //Unserialize submitted data
		parse_str($_REQUEST['optionsData'], $optionsData);
		parse_str($_REQUEST['formulaData'], $formulaData); //Unserialize submitted data
		$labelsData = maybe_serialize($labelsData);
		$optionsData = maybe_serialize($optionsData);
		$productData = maybe_serialize($_REQUEST['productData']);
		$formulaData = maybe_serialize($formulaData);
		$date = date("F j, Y, g:i a");
		
		if(!($quoteID = intval($_REQUEST['quoteID']))){ //New Quote Insert
			$result = $wpdb->insert( $dbTable, array('id' => '','name' => $quoteName,'labels' => $labelsData ,'options' => $optionsData ,'products' => $productData ,'formula' => $formulaData, 'date' => $date) ) or die(mysql_error());
			?> 
            <script language="javascript"> // Adding the saved quote id in url
			var id = 'quoteID=<?php echo $wpdb->insert_id; ?>';
			jQuery(location).attr('href', function(index, attr) {
    			return attr +  '&' + id;
			});
			
			</script>
            <?php
			
		}
		
		else { //Update an Existing Quote
			$result = $wpdb->update( $dbTable, array('name' => $quoteName,'labels' => $labelsData ,'options' => $optionsData ,'products' => $productData ,'formula' => $formulaData, 'date' => $date), array( 'id' => $quoteID )) or die(mysql_error());
		}



	} //Endif
	die();
	}//EOF
	/*******************************************************************************************/
	/**********************GET quote ID from URL*********************************************/
	/***********************************************************************************************/
	static function getThisQuoteId(){
		if(isset($_GET['quoteID']))
			return intval($_GET['quoteID']);
		else
			return NULL;
	}//EOF
	
	/*******************************************************************************************/
	/**********************Ajax data submit from Formula tab****************************************/
	/***********************************************************************************************/	
	static function formulaLoad(){
		if ( self::verifyAjaxRequest()){
			WooQcoFormula::formulaBoxes($_REQUEST['quoteID']); 
		}
		die();	
	}//EOF

	/*******************************************************************************************/
	/**********************Listing All Quote UI *************************************************/
	/***********************************************************************************************/	
	static function quoteManager(){
		if(isset($_GET["delete"])) { //Delete this item
		self::deleteById($_GET["delete"]);
		}
		echo '<div id="container"  style="background:white; padding:25px;">';
		echo '<h4 align="center">Woo Quote Calculator & Order :: <span style="font-size:14px !important; vertical-align:middle">All Quotes</span></h4>';
		global $wpdb;
		$dbTable = $wpdb->prefix.self::$tableName;
		$result = $wpdb->get_results( "select * from $dbTable order by id DESC");
		$i = 1;
			?>
            
			 <table id="WooQcoProducts">
  <thead>
    <tr>
      <th>No</th>
      <th>Quote Name</th>
      <th>Shortcode</th>
      <th>Date Created</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $limit = 12;
  $page = 1;
  if(isset($_GET["pagination"])) {
	  $page = $_GET["pagination"];
	  $start = (($_GET["pagination"]-1)*$limit)+1;
  }
  else {
	  $start = 1;
  }
  
	$end = $start+$limit;
	foreach($result as $quote){
		if($i>=$start && $i<$end){ // Pagination
			echo' <tr>
      <td data-label="No">'.$i.'</td>
      <td data-label="Quote Name"><strong>'.$quote->name.'<strong></td>
	  <td data-label="Quote Name"><input type="text" class="shortcodeInput" onClick="this.select();" value=\'[wooqco id="'.$quote->id.'"]\' /></td>
	  <td data-label="Date Created">'.$quote->date.'</td>
      <td data-label="Action"><a href="admin.php?page=WooQco-newquote&quoteID='.$quote->id.'">Edit</a> &nbsp;|&nbsp; <a href="admin.php?page=WooQco-export&id='.$quote->id.'">Export</a> &nbsp;|&nbsp; <a href="admin.php?page=woo-qco&delete='.$quote->id.'" onclick="return confirm(\'Are you sure?\')">Delete</a></td>
    </tr>';
		} //Pagination end
		$i++;
	}
		?>
  </tbody>
</table>
<?php if($i ==1 ){  //No data found
 echo '<br /><span style="font-size:13px;">No quotes yet!</span>';
}
?>
 <!--Pagination-->
 
            <?php if ($i>12){ ?>
	<div class="pagination">
    	<?php
		$pageNo = 1;echo "Page >> ";
		$i--;
		while($i/$limit >0) { ?>
         <?php if($pageNo == $page) {
			 echo '<span class="uipage active">'.$pageNo.'</span>';
		 } else {
			 echo '<a href="admin.php?page=woo-sqo&pagination='.$pageNo.'" class="uipage">'.$pageNo.'</a>';
		 }
		$pageNo++; 
		 $i-=$limit; } //endwhile
	
	 } //endif ?>
	
	</div> 
  
    <!--Pagination End-->
</div>

<?php

	}//EOF
	
	/*******************************************************************************************/
	/**********************Output product after button click ****************************************/
	/***********************************************************************************************/
	static function ajaxFrontend(){
		if ( self::verifyAjaxRequest()){
			$fieldsArray = $_REQUEST['data'];
			self::output($fieldsArray,$_REQUEST['quoteID']); //Output data table
			} //End main if
		// Always die in functions echoing ajax content
   		die();	
	}//EOF
	static function output($fieldsArray,$quoteID){
			require_once("math/eos.class.php");
			require_once("math/stack.class.php");
			$equation = new eqEOS(); //Math class to solve the formula
			$formulaArray = self::WooQcoGetValue('formula',$quoteID);
			$productIDArray = self::WooQcoGetValue('products',$quoteID);
			 echo '<div id="WooQcoProducts">
			 	<table>
				  <thead>
    				<tr>
      					<th>Product Name</th>
      					<th>Price</th>
      					<th>Required</th>
      					<th>Total</th>
   					 </tr>
  				</thead>
				<tbody>';
				$totalPrice = 0;
				$productNumberArray = array();
				$countProductNumber = 0;
				foreach($productIDArray as $productID){
					$count = 1;
					if(isset($formulaArray[intval($productID)])&& $formulaArray[intval($productID)]){
						$formulaToVal = $formulaArray[intval($productID)];
						}
					else{
						$formulaToVal = 0;
						}
					foreach($fieldsArray as $field){
						if(!intval($field)){  //Empty field set field val to 0 
							$field = 0;
						}
						$formulaToVal = str_replace("f$count",$field,$formulaToVal);
						$count++;
					}
					$productName = get_the_title($productID);
					$product = wc_get_product( $productID );
					$price = $product->get_price();
					$currency = get_woocommerce_currency_symbol();
					$image = $product->get_image();
					$url = get_permalink($productID);
					$productTotal = ceil(max($equation->solveIF($formulaToVal),0)); //Calculating the amount of product needed
					$productNumberArray[$countProductNumber] = $productTotal;
					$countProductNumber++;
					$totalPrice+= $price*$productTotal;
					if (!$productTotal) continue;
					echo '<tr>';
					echo '<td data-label="Product Name"><a href="'.$url.'" target="_blank">'.$productName.'<br />'.$image.'</a></td>';
					echo '<td data-label="Price">'.$currency.' '.$price.'</td>';
					echo '<td data-label="Required">'.$productTotal.'</td>';
					echo '<td data-label="Total">'.$currency.' '.($price*$productTotal).'</td>';
					echo '</tr>';

				}
				echo '<tr>';
					echo '<td colspan="4" data-label="" style="text-align:right"><strong>Total Cost : </strong>'.$currency.' '.$totalPrice.' <input TYPE="button" onclick="return false;" class="WooQcoaddtoCart" VALUE="Add to Cart" /></td>';
					echo '</tr>';
			echo '</tbody>
				</table>
				</div>';
?>
                <script language="javascript">
jQuery('.WooQcoaddtoCart').click(function($) {
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    // We'll pass this variable to the PHP function example_ajax_request
	var productIDs=[];
	var productNumbers=[];
<?php
	$i=0;
	foreach($productIDArray as $productID){
		echo "productIDs[$i]=$productID
		";
		$i++;
	}$i=0;
	foreach($productNumberArray as $productNumber){
		echo "productNumbers[$i]=$productNumber
		";
		$i++;
	}
?>
    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
        'action':'WooQco_ajax_request_add_to_cart',
		'security' : '<?php echo wp_create_nonce( "woo-sqo-ajax-nonce" ); ?>', //Nonce Check
        'productIDs' : productIDs,
	    'productNumbers' : productNumbers
        },
        success:function(data) {
            // This outputs the result of the ajax request
            jQuery('.WooQcoCartResult').html(data);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });  
              
});
</script>               
<?php

	}//EOF

	/*******************************************************************************************/
	/**********************Action to add products in cart*********************************************/
	/***********************************************************************************************/
	static function ajaxAddToCart(){
		if (isset($_REQUEST['productIDs']) && isset($_REQUEST['productNumbers']) && check_ajax_referer( 'woo-sqo-ajax-nonce', 'security' )){
			global $woocommerce;
			$woocommerce->session->set_customer_session_cookie(true);
			$productIDs = $_REQUEST['productIDs'];
			$productNumbers = $_REQUEST['productNumbers'];
			$count = 0;
			foreach($productIDs as $id){
					$woocommerce->cart->add_to_cart(intval($id), intval($productNumbers[$count]));
					$count++;
				}
?>
		<script language="javascript">
			window.location = "<?php echo $woocommerce->cart->get_cart_url(); ?>";
		</script>
<?php

			} //End main if
		// Always die in functions echoing ajax content
   		die();	
	}

	/*******************************************************************************************/
	/**********************Export All quotes /Single quote to CSV ***********************************/
	/***********************************************************************************************/
	static function export(){
		echo '<div id="container"  style="background:white; padding:25px;">';
		echo '<h4 align="center">Woo Quote Calculator & Order  :: <span style="font-size:14px !important; vertical-align:middle">Export</span></h4>';
		global $wpdb;
		$dbTable = $wpdb->prefix.self::$tableName;
		if(isset($_GET["id"])){	 //Single quote export
			$fileName = "woo-simple-quote-".$_GET["id"]."-".date('Y-m-d').".csv";
			$query = "SELECT *, NULL AS id FROM $dbTable where id={$_GET['id']}";
		}
		else{  //Entire table export
			$fileName = "woo-simple-quote-". date('Y-m-d').".csv";
			$query = "SELECT *, NULL AS id FROM $dbTable";
		}
		$fp = fopen($fileName, 'w');
		$result = $wpdb->get_results($query, ARRAY_A); //Get array
		$i=0;
		foreach($result as $row)
		{
     	   fputcsv($fp, $row);  //Writing rows to csv
		   $i++;
    }  
    fclose($fp);
	echo '<strong>Export Completed!</strong><a class="button-primary" style="margin:30px;" href="'.$fileName.'" download>Download Now</a>';
	echo '</div>';
	} //EOF

	/*******************************************************************************************/
	/**********************Import CSV file to Quote DB*********************************************/
	/***********************************************************************************************/
	static function import(){
		echo '<div id="container"  style="background:white; padding:25px;">';
		echo '<h4 align="center">Woo Quote Calculator & Order  :: <span style="font-size:14px !important; vertical-align:middle">Import</span></h4>';
				
		global $wpdb;
		$dbTable = $wpdb->prefix.self::$tableName;
	?>
 	<form action="admin.php?page=WooQco-import" name="uploadFrm" method="post" enctype="multipart/form-data" >
	<input type="file" name="importFile">
    <input name="Submit" class="button-primary" value="Import" type="submit" />
    </form>
    <?php
	/****************************************************/
	/************File Processing**************************/
	/******************************************************/
				if(isset($_FILES["importFile"]) && $_FILES["importFile"]["name"] ){
					$mimes = array('application/vnd.ms-excel','text/csv','text/tsv');
					if(!in_array($_FILES['importFile']['type'],$mimes)){ //Return if file is not a valid csv file
							echo '<h5>&#10060; ! File is not a valid CSV file.</h5>';
					}
					else{
						// open the csv file
						if(!$fp = fopen($_FILES['importFile']['tmp_name'],"r")){echo '<h5>&#10060; ! Failed to open File.</h5>';}
						//parse the csv file row by row
						while(($row = fgetcsv($fp,"500",",")) != FALSE)
						{
  							  //insert csv data into mysql table
   							 $sql = "INSERT INTO $dbTable (id, name, labels, options, products, formula, date) VALUES('" . implode("','",$row) . "')";
							 $result = $wpdb->get_results($sql);
						}
						fclose($fp);
						echo '<h5><img style="vertical-align:bottom !important" src="'. plugins_url( 'images/button_check.png', dirname(__FILE__) ).'" /> imported successfully</h5>';
					}
				}
				
	
	echo '</div>';	
		
	}//EOF

	/*******************************************************************************************/
	/**********************Retrive Value from Database by field name*********************************/
	/***********************************************************************************************/
	static function WooQcoGetValue($fieldName, $id){ //Get field from database by quote ID
		if(!$id){
			return NULL;
		}
		global $wpdb;
		$dbTable = $wpdb->prefix.self::$tableName;
		$var = $wpdb->get_var("SELECT $fieldName FROM $dbTable where id = $id");
		if($fieldName == 'name') return $var;
		else return unserialize($var);
	}//EOF
	
	
	/*******************************************************************************************/
	/**********************Ajax Nonce ceheck*********************************************/
	/***********************************************************************************************/
	static function verifyAjaxRequest(){ //Verifying Ajax Nonce + Variable existance
			if ( isset($_REQUEST) && check_ajax_referer( 'woo-sqo-ajax-nonce', 'security' ) ) { //Check if ajax valid receive + nonce
				if( isset($_REQUEST['data']))return true;
				else return false;
			}
			return false;
	}

	/*******************************************************************************************/
	/**********************Delete single element from DB*********************************************/
	/***********************************************************************************************/
	static function deleteById($id){
		global $wpdb;
		$dbTable = $wpdb->prefix.self::$tableName;
		$result = $wpdb->query("delete from $dbTable where id=$id");
	}//EOF

	/*******************************************************************************************/
	/**********************Function to call anthing using ajax*********************************************/
	/***********************************************************************************************/
	static function ajaxCallScript($button,$form, $action,$additional){
		?>
            <script language="javascript">
			jQuery( document ).ready(function() { 
         		/*************************************************/
			/****************************Ajax Submission************************/
			/********************************************************************/
			jQuery('<?php echo $button; ?>').click(function() {
				<?php echo $additional; //Additional statement print?>
				var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
				<?php if($form){ ?>
				var data = jQuery('<?php echo $form ;?>').serialize();
				<?php } //Only is this is a form submission
				?>
    			// This does the ajax request
   				jQuery.ajax({
        		url: ajaxurl,
        		data: {
            		'action' : '<?php echo $action;?>',
					'security' : '<?php echo wp_create_nonce( "woo-sqo-ajax-nonce" ); ?>', //Nonce Check
            		'data' : data  //Sending data ajaxrequest
        		},
        		success:function(data) { //Showing notification that data saved
      		  },
        	error: function(errorThrown){
            console.log(errorThrown);
        	}
				});
			});
				/****************************************End Ajax************/
			});
			</script>
        <?php
	}//EOF
	/*******************************************************************************************/
	/**********************Check if plugin tables exists else create*********************************/
	/***********************************************************************************************/
	static function checkIfPluginTableExists(){
		global $wpdb;
		$dbTable = $wpdb->prefix.self::$tableName;
		if( $wpdb->get_var("SHOW TABLES LIKE '" . $dbTable  . "'") === $dbTable . '' ){
			return true; //Table exists
		}
		else{
				$sqlQuery="CREATE TABLE ".$dbTable." (
	 id INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY ( id ) ,
  name VARCHAR( 200 ),
  labels VARCHAR( 200 ),
  options text,
  products VARCHAR( 400 ),
  formula text,
  date VARCHAR( 30 )
) COLLATE utf8_general_ci;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sqlQuery );
		}
		
				} //EOF
}
?>