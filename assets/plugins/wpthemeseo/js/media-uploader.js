(function($) {
	$(document).ready(function() {

		function wpthemeseo_add_file(event, selector) {
		
			var upload = $(".uploaded-file"), frame;
			var $el = $(this);

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}

			// Create the media frame.
			frame = wp.media({
				// Set the title of the modal.
				title: $el.data('choose'),

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update'),
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				}
			});

			// When an image is selected, run a callback.
			frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = frame.state().get('selection').first();
				frame.close();
				selector.find('.upload').val(attachment.attributes.url);
				if ( attachment.attributes.type == 'image' ) {
					selector.find('.screenshot').empty().hide().append('<img src="' + attachment.attributes.url + '"><a class="remove-image">Remove</a>').slideDown('fast');
				}
				selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(wpthemeseo_l10n.remove);
				selector.find('.ws-background-properties').slideDown();
				selector.find('.remove-image, .remove-file').on('click', function() {
					wpthemeseo_remove_file( $(this).parents('.section') );
				});
			});

			// Finally, open the modal.
			frame.open();
		}
        
		function wpthemeseo_remove_file(selector) {
			selector.find('.remove-image').hide();
			selector.find('.upload').val('');
			selector.find('.ws-background-properties').hide();
			selector.find('.screenshot').slideUp();
			selector.find('.remove-file').unbind().addClass('upload-button').removeClass('remove-file').val(wpthemeseo_l10n.upload);
			// We don't display the upload button if .upload-notice is present
			// This means the user doesn't have the WordPress 3.5 Media Library Support
			if ( $('.section-upload .upload-notice').length > 0 ) {
				$('.upload-button').remove();
			}
			selector.find('.upload-button').on('click', function() {
				wpthemeseo_add_file(event, $(this).parents('.section'));
			});
		}
		
		$('.remove-image, .remove-file').on('click', function() {
			wpthemeseo_remove_file( $(this).parents('.section') );
        });
        
        $('.upload-button').click( function( event ) {
        	wpthemeseo_add_file(event, $(this).parents('.section'));
        });
        
    });
	
})(jQuery);