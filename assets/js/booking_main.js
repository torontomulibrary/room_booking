//Written by Steven Marsden
//Jan 2015 
//Ryerson University Library

$('#filter_link').on('click', function(){
	$('#filter_container').toggle();
});

$('.role_title_collapse a').on('click', function(){
	$(this).parent().parent().next().toggle();
});

//Create the filter functionality
$(".filter_checkbox").change(function() {
		//Reset all the filters, then re-apply them
		$('.room_name').parent().css('display','table-row');
		
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
