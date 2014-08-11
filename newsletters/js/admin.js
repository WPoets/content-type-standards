/*
 * Attaches the image uploader to the input field
 */
jQuery( function($){
	// Uploading files
	var newsletter_file_frame;
	var newsletter_file_path;

	jQuery(document).on( 'click', '#newsletter-file-button', function( event ){

		var $el = $(this);

		newsletter_file_path = $('#newsletter-file');

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( newsletter_file_frame ) {
			newsletter_file_frame.open();
			return;
		}

		var newsletter_file_states = [
			// Main states.
			new wp.media.controller.Library({
				library:   wp.media.query(),
				multiple:  true,
				title:     $el.data('choose'),
				priority:  20,
				filterable: 'uploaded',
			})
		];

		// Create the media frame.
		newsletter_file_frame = wp.media.frames.newsletter_file = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),
			library: {
				type: ''
			},
			button: {
				text: $el.data('update'),
			},
			multiple: true,
			states: newsletter_file_states,
		});

		// When an image is selected, run a callback.
		newsletter_file_frame.on( 'select', function() {

			var file_path = '';
			var selection = newsletter_file_frame.state().get('selection');

			selection.map( function( attachment ) {

				attachment = attachment.toJSON();

				if ( attachment.url )
					file_path = attachment.url

			} );

			newsletter_file_path.val( file_path );
		});

		// Finally, open the modal.
		newsletter_file_frame.open();
	});
});