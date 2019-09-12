//Written by Steven Marsden
//Jan 2015 
//Ryerson University Library

jQuery('#filter_link').on('click', function(){
	jQuery('#filter_container').toggle();
	jQuery('#filter_container').jScrollPane({
			horizontalDragMinWidth: 70,
			horizontalDragMaxWidth: 70
	});
});

jQuery('.role_title_collapse a').on('click', function(){
	jQuery(this).parent().parent().next().toggle();
});

//Uncheck the rest of the buildings if you select one
jQuery('.building_checkbox').change(function(){
	var before = this.checked; 
	
	jQuery('.building_checkbox').each(function(){
		this.checked = false;
	});
	
	//Set it to the intended state
	if(before) this.checked = true;
	else this.checked = false;
});

//Uncheck the rest of seating options if you select one
jQuery('.seat_checkbox').change(function(){
	var before = this.checked; 
	
	jQuery('.seat_checkbox').each(function(){
		this.checked = false;
	});
	
	//Set it to the intended state
	if(before) this.checked = true;
	else this.checked = false;
});


//Create the filter functionality
jQuery(".filter_checkbox").change(function() {
		//Reset all the filters, then re-apply them
		jQuery('.room_name').parent().css('display','table-row');
		
		//Handle the seating filters
		jQuery('.room_row').each(function(index,element){
			var currentRoom = this;
			
			jQuery('.seat_checkbox:checked').each(function(){			
				var currentCheckbox = this;
				var foundSeats = false;
				
				//Iterate each resource the room contains
				jQuery(currentRoom).each(function(i,e){
					if(jQuery(this).data('seats') >= jQuery(currentCheckbox).data('minseats') && jQuery(this).data('seats') <= jQuery(currentCheckbox).data('maxseats')){
						foundSeats = true;
					}
				});
				
				//Resource was not found in that room
				if(foundSeats == false){
					jQuery(currentRoom).css('display','none');
				}
			});
		});
		
		//Handle the building filters
		jQuery('.room_row').each(function(index,element){
			var currentRoom = this;
			
			jQuery('.building_checkbox:checked').each(function(){			
				var currentCheckbox = this;
				var foundBuilding = false;
				
				//Iterate each resource the room contains
				jQuery(currentRoom).each(function(i,e){
					if(jQuery(this).data('buildingid') == jQuery(currentCheckbox).val()){
						foundBuilding = true;
					}
				});
				
				//Resource was not found in that room
				if(foundBuilding == false){
					jQuery(currentRoom).css('display','none');
				}
			});
		});
		
		//Handle the resource filters
		jQuery('.room_resources').each(function(index,element){
			var currentRoom = this;
			
			//Iterate all the selected filters
			jQuery('.resource_checkbox:checked').each(function(){
				var currentCheckbox = this;
				var foundResource = false;
				
				//Iterate each resource the room contains
				jQuery(currentRoom).children().each(function(i,e){
					if(jQuery(this).data('resourceid') == jQuery(currentCheckbox).val()){
						foundResource = true;
					}
				});
				
				//Resource was not found in that room
				if(foundResource == false){
					jQuery(currentRoom).parents().eq(1).css('display','none');
				}
			});
			
			
			
		});
		
		jQuery.post('filter', jQuery('#herp').serialize());
 
});
