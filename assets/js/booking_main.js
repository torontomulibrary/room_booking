//Written by Steven Marsden
//Jan 2015 
//Ryerson University Library

$('#filter_link').on('click', function(){
	$('#filter_container').toggle();
	$('#filter_container').jScrollPane({
			horizontalDragMinWidth: 70,
			horizontalDragMaxWidth: 70
	});
});

$('.role_title_collapse a').on('click', function(){
	$(this).parent().parent().next().toggle();
});

//Uncheck the rest of the buildings if you select one
$('.building_checkbox').change(function(){
	var before = this.checked; 
	
	$('.building_checkbox').each(function(){
		this.checked = false;
	});
	
	//Set it to the intended state
	if(before) this.checked = true;
	else this.checked = false;
});

//Uncheck the rest of seating options if you select one
$('.seat_checkbox').change(function(){
	var before = this.checked; 
	
	$('.seat_checkbox').each(function(){
		this.checked = false;
	});
	
	//Set it to the intended state
	if(before) this.checked = true;
	else this.checked = false;
});


//Create the filter functionality
$(".filter_checkbox").change(function() {
		//Reset all the filters, then re-apply them
		$('.room_name').parent().css('display','table-row');
		
		//Handle the seating filters
		$('.room_row').each(function(index,element){
			var currentRoom = this;
			
			$('.seat_checkbox:checked').each(function(){			
				var currentCheckbox = this;
				var foundSeats = false;
				
				//Iterate each resource the room contains
				$(currentRoom).each(function(i,e){
					if($(this).data('seats') >= $(currentCheckbox).data('minseats') && $(this).data('seats') <= $(currentCheckbox).data('maxseats')){
						foundSeats = true;
					}
				});
				
				//Resource was not found in that room
				if(foundSeats == false){
					$(currentRoom).css('display','none');
				}
			});
		});
		
		//Handle the building filters
		$('.room_row').each(function(index,element){
			var currentRoom = this;
			
			$('.building_checkbox:checked').each(function(){			
				var currentCheckbox = this;
				var foundBuilding = false;
				
				//Iterate each resource the room contains
				$(currentRoom).each(function(i,e){
					if($(this).data('buildingid') == $(currentCheckbox).val()){
						foundBuilding = true;
					}
				});
				
				//Resource was not found in that room
				if(foundBuilding == false){
					$(currentRoom).css('display','none');
				}
			});
		});
		
		//Handle the resource filters
		$('.room_resources').each(function(index,element){
			var currentRoom = this;
			
			//Iterate all the selected filters
			$('.resource_checkbox:checked').each(function(){
				var currentCheckbox = this;
				var foundResource = false;
				
				//Iterate each resource the room contains
				$(currentRoom).children().each(function(i,e){
					if($(this).data('resourceid') == $(currentCheckbox).val()){
						foundResource = true;
					}
				});
				
				//Resource was not found in that room
				if(foundResource == false){
					$(currentRoom).parents().eq(1).css('display','none');
				}
			});
			
			
			
		});
 
});
