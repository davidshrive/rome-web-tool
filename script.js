$(document).ready(function() {

    $('img.buildingicon').mouseenter(function() {
    	$('img.buildingicon').fadeTo('fast', 0.5);
    });

    $('img.buildingicon').mouseleave(function() {
    	$('img.buildingicon').fadeTo('fast', 1);
    });
});