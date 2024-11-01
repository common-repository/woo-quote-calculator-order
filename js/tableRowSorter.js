jQuery(".productTable tbody").sortable({
	start: function(event, ui){        
   ui.item.css('background-color','#F8F9F9');
},
stop: function(event, ui){ 
   	ui.item.css('background-color','#ffffff');
}
}).disableSelection();