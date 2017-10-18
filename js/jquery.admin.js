/*-----------------------------------------------------------------------------------*/
/*	Custom Header JS
/*-----------------------------------------------------------------------------------*/

jQuery( document ).ready( function( $ ) {
	"use strict";
	
	var myOptions = {
	    // a callback to fire whenever the color changes to a valid color
	    change: function( event, ui ) {
			if ( $( '#zh_custom_color' ).is( ':checked' ) ) {
				$( '.zh-sortable rect' ).css({ fill: ui['color'] }); // Set the icons to the choosen color on the settings page 
			}
	    },
	};
	
	$( '.zh-color-field' ).wpColorPicker( myOptions );
		
	$( '#zh_custom_color' ).change( function() {
	    if ( $( this ).is( ':checked' ) ) {
		    var hexColor = $( '.zh-color-field' ).val();
		    var isValidColor  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test( hexColor );
	    	
	    	if ( isValidColor ) { 
		    	$( '.zh-sortable rect' ).css({ fill: hexColor }); 
		    }
	    }
	    else {
		    $( '.zh-sortable rect' ).css({ fill: '' });
		}
	});
	    
    $( '.zh-sortable' ).sortable({
		cursor: 'move'
	}); 
	
});