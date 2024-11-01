function checkForErrors(){
	var error = 0;
	if(!jQuery('#wooQcoFrm .WooQcoboxContainer').length){ //Check for atleast one field
		error++;
	}
	else if(jQuery("#WooQcoProducts input:checkbox:checked").length < 1){ //Check for atleast one product
		error++;
			}
	else if(jQuery("#quoteName").val() == ""){
		error++;
	}
	return error;	
	alert(jQuery("#quoteName").val());
}