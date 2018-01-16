

console.log("ASDASDASD");

if (typeof jQuery == 'undefined') {

  console.log("JQUERY NOT LOADED");
}


$('.remove-button').click(function(){
	var rowID = $(this).attr('id');
	console.log("Removing: " + rowID);
	$('tr#'+rowID).toggle();
});