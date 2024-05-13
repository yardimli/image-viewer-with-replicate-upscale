$(document).ready(function () {
	$('.update-note').click(function () {
		var imageId = $(this).data('image-id');
		var notes = $(this).prev('.card-text').find('.image-note').val();
		$.ajax({
			url: '/images/' + imageId + '/update-notes',
			method: 'POST',
			data: {
				"_token": csrf_token,
				notes: notes
			},
			success: function (response) {
				alert('Notes updated successfully!');
			}
		});
	});
	
	$('.upscale-image').click(function () {
		var imageId = $(this).data('image-id');
		var imageUrl = $(this).data('image-url');
		$('#upscale_result_' + imageId).html('Starting Upscaling... ');
		$.ajax({
			url: '/images/' + imageId + '/upscale',
			method: 'POST',
			data: {
				"_token": csrf_token,
				"image_url": imageUrl
			},
			success: function (response) {
				$('#upscale_result_' + imageId).html('Upscaling in progress... ');
				console.log(response.upscale_result); // You might want to do something with the result
				// Start polling for status
				var checkStatus = function() {
					$.ajax({
						url: '/images/' + imageId + '/upscale-status/' + response.prediction_id,
						method: 'GET',
						success: function(statusResponse) {
							if (statusResponse.message === 'Image upscaled successfully.') {
								// alert('Upscaling Complete!');
								clearInterval(statusInterval); // Stop polling
								// Update the UI here, e.g., displaying the upscaled image link
								$('#upscale_result_' + imageId).html('<a href="' + statusResponse.upscale_result + '" target="_blank">View Upscaled Image</a>');
							} else if (statusResponse.message === 'Upscale in progress.') {
								$('#upscale_result_' + imageId).html('Upscaling in progress... '+statusResponse.status);
								
							}
							else if (statusResponse.message === 'Image upscale failed.') {
								alert('Upscaling Failed');
								clearInterval(statusInterval); // Stop polling
							}
						}
					});
				};
				
				var statusInterval = setInterval(checkStatus, 5000);
				
			}
		});
	});
	
	var LoadedImageSrc = '';
	
	// Mouse enter event on the card image
	$('.card img').mouseenter(function () {
		var hoverSrc = $(this).data('hover-src');
		var cardOffset = $(this).closest('.card').offset();
		var cardWidth = $(this).closest('.card').outerWidth();
		var windowWidth = $(window).width();
		var floatingImgWidth = 700; // Assuming a fixed floating image width
		
		if (hoverSrc === LoadedImageSrc) {
			return;
		}
		LoadedImageSrc = hoverSrc;
		$('#floating-image').attr('src', '');
		
		
		// If there's enough space on the right side of the card, display the image there, otherwise to the left.
		if (windowWidth - (cardOffset.left + cardWidth) > floatingImgWidth) {
			// Display to the right
			$('#floating-image-container').css({
				left: cardOffset.left + cardWidth + 10, // 10px gap from the card
				top:  $(window).scrollTop() + 10
			});
		} else {
			// Display to the left (ensure it does not go off-screen)
			var leftPosition = cardOffset.left - floatingImgWidth - 10; // 10px gap from the card
			if (leftPosition < 0) {
				leftPosition = 10; // Fallback to display it at the start of the window if it goes off-screen
			}
			$('#floating-image-container').css({
				left: leftPosition,
				top: $(window).scrollTop() + 10
			});
		}
		
		// Set the source of the floating image and display it
		$('#floating-image').attr('src', hoverSrc);
		$('#floating-image-container').show();
	}).mouseleave(function () {
		// Hide the floating image when mouse leaves the card image
		LoadedImageSrc = '';
		$('#floating-image-container').hide();
	});
	
	// Additional event to hide the floating image when mouse enters and leaves the floating container (for safety)
	$('#floating-image-container').mouseenter(function () {
		$(this).show(); // Keep showing the floating image when over it
	}).mouseleave(function () {
		LoadedImageSrc = '';
		$(this).hide(); // Hide when leaving the floating image
	});
	
	//hide the floating image when user scrolls
	$(window).scroll(function () {
		$('#floating-image-container').hide();
		LoadedImageSrc = '';
	});
	
});
