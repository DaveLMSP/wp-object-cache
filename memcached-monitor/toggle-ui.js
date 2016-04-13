/**
 * memcache monitor javascript
 *
 * Javascript to display debug dialog & toggle visibility of UI elements
 *
 * @author Dave Long
 */

		// Initialize UI & setup click handlers
(function(jQuery) {
	jQuery(document).ready(function() {
		jQuery( '#wp-admin-bar-memcache-show-debug' ).click( function() {
			// Check if dialog exists
			if( jQuery( '#memcache-debug' ).hasClass('ui-dialog-content') ) {
				if( true === jQuery( '#memcache-debug' ).dialog( 'isOpen' ) ) {
					// The dialog is open; close it.
					jQuery( '#memcache-debug' ).dialog( 'close' );
				}
				else {
					// If we're re-opening, see if the dialog needs to be resized
					var newWidth = jQuery( window ).width() * 0.8;
					var newHeight = jQuery( window ).height() * 0.9;
					var dWidth = jQuery( '#memcache-debug' ).dialog( 'option', 'width' );
					var dHeight = jQuery( '#memcache-debug' ).dialog( 'option', 'height' );
					if( newWidth != dWidth ) {
						jQuery( '#memcache-debug' ).dialog( 'option', 'width', newWidth );
					}
					if( newHeight != dHeight ) {
						jQuery( '#memcache-debug' ).dialog( 'option', 'height', newHeight );
					}
					jQuery( '#memcache-debug' ).dialog( 'open' );
				}
			}
			else {
				// Dialog does not exist; create it
				var dWidth = jQuery( window ).width() * 0.8;
				var dHeight = jQuery( window ).height() * 0.9;

				jQuery( '#memcache-debug' ).dialog({
					autoOpen: true,
					dialogClass: 'memcache-ui',
					title: 'Memcached Debug',
					modal: true,
					resizable: false,
					width: dWidth,
					height: dHeight,
					buttons: [{
							'class': 'button button-primary',
							text: 'Done',
							click: function() {
								jQuery( this ).dialog( 'close' );
							}}],
					open: function(event, ui) {
						jQuery( '.ui-dialog-titlebar-close' ).hide();
					},
				});
			}
		});

		// Open / Close Debug UI elements
		jQuery( '.memcache-toggle' ).click( function() {
			var element = jQuery(this).attr('id');
			var target = element.replace( 'toggle-', 'memcache-' );
			jQuery( '#' + target ).toggle();
			if( jQuery( '#' + target ).is( ':visible' ) ) {
				jQuery( '#' + element ).addClass( 'open' );
			}
			else {
				jQuery( '#' + element ).removeClass( 'open' );
			}
			
		});

		// Close Dialog on Window resize
		jQuery(window).on('resize', function(){
			if( jQuery( '#memcache-debug' ).hasClass('ui-dialog-content') ) {
				jQuery( '#memcache-debug' ).dialog( 'close' );
			}
		});
	})
})(jQuery);