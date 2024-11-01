<?php
class WooQcoFormula{
		static function enqueueScripts(){
		wp_register_style( 'wooQoute_Product_style', plugins_url('css/productTable.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQoute_Product_style' );

	}
	static function formulaBoxes($quoteID){
		self::enqueueScripts();
		if(!$quoteID){ //This is new quote we save it before formula load
		echo "Save the quote first!";
		}
		else{ // Edit form section
		/********Validate and retrieve saved data******************/
		$totalFields = sizeof(WooQcoModel::WooQcoGetValue('labels',$quoteID ));//Get number of fields from option table
		$productArray = WooQcoModel::WooQcoGetValue('products', $quoteID); //Get Saved products from option table
	?>
    <table id="WooQcoProducts" class="smallTable">
  <thead>
    <tr>
      <th>Field No.</th>
      <th>Field Label</th>
      <th>Field Values</th>
      <th>Variable to Use</th>
    </tr>
  </thead>
  <tbody>
  <?php
	for($fieldNo = 1; $fieldNo<=$totalFields; $fieldNo++ ){
		$labelArray =  WooQcoModel::WooQcoGetValue('labels',$quoteID);	 //label
		$options = WooQcoModel::WooQcoGetValue('options',$quoteID );//options	
		$optionsArray = explode("\n", $options['options'.$fieldNo]);//Options array
		echo ' <tr>
      <td data-label="Field No."> '.$fieldNo.'</td>
      <td data-label="Field Label">'.$labelArray['label'.$fieldNo].'</td>
	   <td data-label="Field Values"><select>';
	   for($i=0;$i<count($optionsArray);$i++){
		   echo '<option>';
		   /*if(preg_match('/\(([0-9]+?)\)/',$optionsArray[$i] ,$matches)){ // check any match found forvalue between parenthesis() or not
 			  $value = intval(str_replace(array('(',')'),array('',''),$matches[0]));
			  echo $value;
		   	}
			else*/
			{
		   		echo $optionsArray[$i];
			}
		   echo '</option>';
	   }
	   echo '</select></td>
	  <td data-label="Variable to Use"><strong>f'.$fieldNo.'<strong></td>
      </tr>';		
		}//End for
		?>
        
  </tbody>
</table>
<h2 align="center">Now use Variables and Save Formula for Number of Product</h2>
<form id="formulaTable">
 <table id="WooQcoProducts" class="formulaTable">
  <thead>
    <tr>
      <th>Product Id</th>
      <th>Product Name</th>
      <th>Formula</th>
    </tr>
  </thead>
  <tbody>
  <?php
	$productArray = WooQcoModel::WooQcoGetValue('products', $quoteID);	 //Get Saved products from option table  
	$formulas = WooQcoModel::WooQcoGetValue('formula', $quoteID); //Get saved formulas
	
	for($productNo = 0; $productNo<count($productArray); $productNo++ ){	
		echo ' <tr>
      <td data-label="Product Id."> '.$productArray[$productNo].'</td>
      <td data-label="Product Name"><a href="'.get_permalink($productArray[$productNo]).'" target="_blank">'.get_the_title($productArray[$productNo]).'</a></td>
	  <td data-label="Formula"><input name="'.$productArray[$productNo].'" type="text" value="';
	  if($formulas){
		  if(isset($formulas[$productArray[$productNo]])) echo $formulas[$productArray[$productNo]];
	  	}
	  echo '" /></td>
      </tr>';		
		}//End for
		?>
        
  </tbody>
</table><br />
 </form>

 <?php
 
 	WooQcoModel::ajaxCallScript(".WooQcoFrmBtn", "#formulaTable","WooQcoFormulaSave","if(!checkEmptyField())return false;"); 
		}//End else
				//Ajax call function (button, form, action ,additional condition)
	} //EOF
}
?>