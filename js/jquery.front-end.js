/*-----------------------------------------------------------------------------------*/
/*	Custom Header JS
/*-----------------------------------------------------------------------------------*/

jQuery( document ).ready( function( $ ) {
	"use strict";
	
	// Open Share Window
	$(document.body).on('click', '.zh-social-sharing-links', function () {
		var top = $(window).height() / 2 - 450 / 2, left = $(window).width() / 2 - 550 / 2,
			new_window = window.open($(this).attr('href'), '', 'scrollbars=1, height=450, width=550, top=' + top + ', left=' + left);
		if ( window.focus ) {
			new_window.focus();
		}
		return false;
	});
	
});