( function( $ ) {
	'use strict';
	
	var adminAjaxRequest = function( formdata, _action ){
		console.log(formdata)
		$.ajax({
			type: 'POST',
			dataType: 'json', 
			url: scriptObject.ajaxurl,
			data: {
				action: _action,
				data: formdata, 
				security: scriptObject.security,
			},

			success: function(response){
				if (response.success){
					console.log('data sent');
				}

				//grab dom elements
				let to_delete = response.data.to_delete;
				let toFadeOut = [];
				for ( var i = 0; i < to_delete.length; i++ ){
					toFadeOut.push( $('tr#' + to_delete[i])[0] );
				}
				$( toFadeOut ).fadeOut( 1000, function(){
					$( this ).remove();
				});

			},
			error: function(jqXHR, status, errorThrown){

				console.log( errorThrown );

			}
		});
	};

	$('#fcw-update-entries').click( function(){
		// grab all checked boxes
		let entry_id_array = [];
		$('.fcw-row-checkbox').each( function(){
		    var $this = $(this);
		    if ( $this.is( ':checked' )){
		        entry_id_array.push( $this.val() );
		    }
		});

		// make form data
		let formdata = {
			'entry_id_array' : entry_id_array
		}

		// send form data
		adminAjaxRequest(formdata, 'update_entry' );

	});
	

	$('#fcw-select-all').change(function () {
    	$('.fcw-row-checkbox')
           .prop( 'checked', this.checked );
  	});

})(jQuery);