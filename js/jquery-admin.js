/**
 * Setup WordPress' default color picker and jQuery UI sortable script for 
 * custom ordering of the social sharing buttons. 
 *
 * @summary   Initialize WordPress-registered scripts on the settings page.
 *
 * @since     1.0.0
 * @required  jquery.js
 * @requires  wp-color-picker
 * @requires  jquery-ui-sortable
 */

jQuery( document ).ready( function( $ ) {
	"use strict";

	var $customColorsCheckbox = $( '#zh_custom_color' );
	var $colorField = $( '.zh-color-field' );
	var $sortableList = $( '.zh-sortable' );
	var $iconBackgrounds = $( '.zh-sortable rect' );
	
	var myOptions = {
	    // a callback to fire whenever the color changes to a valid color inside the color picker field.
	    change: function( event, ui ) {
	    	// Set the icons to the chosen color, if the "Enable custom colors" checkbox is selected.
			if ( $customColorsCheckbox.is( ':checked' ) ) {
				$iconBackgrounds.css({ fill: ui['color'] });  
			}
	    },
	};
	
	// Initialize the color picker field.
	$colorField.wpColorPicker( myOptions );
		 
	/*
	 * Toggle the social icon's colors, depending on the current state of 
	 * "Enable custom colors" checkbox.
	 */
	$customColorsCheckbox.change( function() {
	    if ( $( this ).is( ':checked' ) ) {
		    var hexColor = $colorField.val();
		    var isValidColor  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test( hexColor );
	    	
	    	if ( isValidColor ) { 
		    	$iconBackgrounds.css({ fill: hexColor }); 
		    }
	    }
	    else {
		    $iconBackgrounds.css({ fill: '' });
		}
	});
	  
	// Initialize jQuery UI sortable on social buttons reordering option.  
    $sortableList.sortable({
		cursor: 'move'
	}); 
	
});