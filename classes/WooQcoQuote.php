<?php
class wooQcoQuoteForm{
	static $quoteID;
	static function enqueueScripts(){
		wp_register_style( 'wooQco_Form_style', plugins_url('css/wooQco_Form.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQco_Form_style' );
		wp_register_style( 'wooQco_NewQuoteCss', plugins_url('css/NewQuoteCss.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQco_NewQuoteCss' );
		wp_register_style( 'wooQco_Font_Awesome', plugins_url('css/font-awesome.min.css',dirname(__FILE__) ), false, null, 'all');
		wp_enqueue_style( 'wooQco_Font_Awesome' );

	}

	static function adminForm(){
		self::enqueueScripts();
		self::$quoteID = WooQcoModel::getThisQuoteId();
		echo '<form id="wooQcoFrm">';
		$totalbox = count((array)(WooQcoModel::WooQcoGetValue("labels",self::$quoteID)));//Get number of fields from options table
		($totalbox===-1)?$totalbox=0:$totalbox; //If no field set to -1
		for($boxNumber = 1; $boxNumber<=$totalbox; $boxNumber++){
			self::formBox($boxNumber);
		}
		echo '<div class="WooQcoPlusbox" title="Add New Field"></div>';
		echo '</form>';
		?>
            
        <script language="javascript">
		jQuery( document ).ready(function() { 
		var totalBoxCount= jQuery('#wooQcoFrm .WooQcoboxContainer').length;
		if(!totalBoxCount){ //Check if there are atleast one box
				jQuery(".WooQcoFrmBtn").attr("disabled", "true");
				jQuery(".saveMsg").html('<span style="color:red">No field to save!</span>');
				}
			var str='';
			jQuery('.WooQcoPlusbox').click(function() {		//Actions for clicking The Plus button
				var totalBoxCount= jQuery('#wooQcoFrm .WooQcoboxContainer').length;
				var newID = totalBoxCount+1; //This is the new box id
				jQuery('<?php self::formBox(''); ?>').insertBefore(jQuery( ".WooQcoPlusbox" )).hide().show('slow');  //Adding new div before the plus button
				jQuery('#boxNum').text(newID);  //Adding new title and ID to the new box
				jQuery('#boxNum').attr("id","boxNum"+newID);
				jQuery('#WooQcoboxContainer .WooQcoboxContainerBody input').attr("name","label"+newID);
				jQuery('#WooQcoboxContainer .WooQcoboxContainerBody textarea').attr("name","options"+newID);
				jQuery('#WooQcoboxContainer .WooQcoboxContainerTitle .closeBtn').attr("id",newID);
				jQuery('#WooQcoboxContainer').attr("id","WooQcoboxContainer"+newID);				
				
			});
		
			jQuery( document ).on( 'click', '.closeBtn', function()  { //Actions for clicking close button
				var thisID = jQuery(this).attr('id');  //Id of the box which close button was clicked for this action
				var totalboxCount= jQuery('#wooQcoFrm .WooQcoboxContainer').length;
				jQuery('#WooQcoboxContainer'+thisID).hide('slow', function(){ jQuery(this).remove(); });  //Removing this box clicking on close button
					for(var nextBox=parseInt(thisID)+1; nextBox<=totalboxCount; nextBox++){ //Adding new title and ID to elements of all boxes
						jQuery('#boxNum'+nextBox).text(nextBox-1);
						jQuery('#boxNum'+nextBox).attr("id","boxNum"+(nextBox-1));
						jQuery('#WooQcoboxContainer'+nextBox+' .WooQcoboxContainerBody input').attr("name","label"+(nextBox-1));
						jQuery('#WooQcoboxContainer'+nextBox+' .WooQcoboxContainerBody textarea').attr("name","options"+(nextBox-1));
						jQuery('#WooQcoboxContainer'+nextBox+' .WooQcoboxContainerTitle .closeBtn').attr("id",(nextBox-1));
						jQuery('#WooQcoboxContainer'+nextBox).attr("id","WooQcoboxContainer"+(nextBox-1));
					}	
								
				});				
		});
		</script>
      <?php WooQcoModel::ajaxCallScript(".WooQcoFrmBtn", "#wooQcoFrm", "WooQcoDataSave", "if(!checkEmptyField())return false;
	   else if(!checkIfAnyBox())return false;"); 
				//Ajax call function (button, form, action ,additional condition)
         	
	} //EOF
		
	
	static function formBox($boxNumber){ //Each box containing form with saved values if any
		$closeButton = plugins_url('images/close-icon.png',dirname(__FILE__) );
		
		//Retrieving default field value from the Options table
		$labelArray = WooQcoModel::WooQcoGetValue('labels', self::$quoteID);	 //label
		$optionsArray = WooQcoModel::WooQcoGetValue('options', self::$quoteID);//options
		//******************************************************************************/
		
		echo '<div id="WooQcoboxContainer'.$boxNumber.'" class="WooQcoboxContainer">';
		echo '<div class="WooQcoboxContainerTitle">';
		echo '<img id ="'.$boxNumber.'" class="closeBtn" src="'.$closeButton.'" /><h1>Field <span id="boxNum'.$boxNumber.'">'.$boxNumber.'</span></h1>';
		echo '</div>';
		echo '<div class="WooQcoboxContainerBody">'; 
		echo '<strong>Label</strong><br/><input id="label'.$boxNumber.'" name="label'.$boxNumber.'" type="text" value="';
		if($labelArray && $boxNumber){
			echo $labelArray['label'.($boxNumber)];
			}
		echo '" /><br />';
		echo '<strong>Options</strong><br/><textarea id=options'.$boxNumber.'" onselectstart="return false;" name="options'.$boxNumber.'" cols="40" rows="6">';
				if($optionsArray && $boxNumber){
			echo $optionsArray['options'.($boxNumber)];
			}
		echo '</textarea>';
		echo '</div>';
		echo '</div>';
	}

} //End of class