$(document).ready(function () {
	$('.click-to-enlarge').click(function () {
		var imageUrl = $(this).attr('data-hover-src');
		var notes = $(this).attr('data-notes');
		var albumFilename = $(this).attr('data-album-filename');
		var imageKeywords = $(this).attr('data-image-keywords');
		var imageId = $(this).attr('data-image-id');
		var originalImageUrl = $(this).attr('data-image-url');
		
		console.log(imageUrl, notes, imageId, originalImageUrl);
		
		$('#modalImage').attr('src', imageUrl);
		$('.update-note-text').val(notes);
		$('.album-filename-text').val(albumFilename);
		$('.image-keywords-text').val(imageKeywords);
		
		$('.update-note-modal').attr('data-image-id', imageId);
		$('.upscale-image-modal').attr('data-image-id', imageId)
		$('.upscale-image-modal').attr('data-image-url', originalImageUrl);
		
		// Set the upscale result
		var upscaleResultHtml = $('#upscale_result_' + imageId).html();
		$('#upscale_result_modal').html(upscaleResultHtml);
		$("#add-note-results").html('');
		
		$('#imageModal').modal('show');
	});
	
	$('#imageModal').on('click', '.update-note-modal', function () {
		var imageId = $(this).attr('data-image-id');
		var notes = $('.update-note-text').val();
		var albumFilename = $('.album-filename-text').val();
		var imageKeywords = $('.image-keywords-text').val();
		$.ajax({
			url: '/images/' + imageId + '/update-notes',
			method: 'POST',
			data: {
				"_token": csrf_token,
				notes: notes,
				album_filename: albumFilename,
				image_keywords: imageKeywords
			},
			success: function (response) {
				$("#add-note-results").html('Notes updated successfully!');
				$('#upscale_notes_' + imageId + '').html(notes);
			}
		});
	});
	
	
	$('#imageModal').on('click', '.upscale-image-modal', function () {
		var imageId = $(this).attr('data-image-id');
		var imageUrl = $(this).attr('data-image-url');
		console.log('start upscaling', imageId, imageUrl);
		
		$('#upscale_result_modal').html('Starting Upscaling... ');
		$('#upscale_result_' + imageId).html('Upscaling in progress... ');
		
		$.ajax({
			url: '/images/' + imageId + '/upscale',
			method: 'POST',
			data: {"_token": csrf_token, "image_url": imageUrl},
			success: function (response) {
				$('#upscale_result_modal').html('Upscaling in progress... ');
				$('#upscale_result_' + imageId).html('Upscaling in progress... ');
				console.log(response.upscale_result); // You might want to do something with the result
				
				// Start polling for status
				var checkStatus = function () {
					$.ajax({
						url: '/images/' + imageId + '/upscale-status/' + response.prediction_id,
						method: 'GET',
						success: function (statusResponse) {
							if (statusResponse.message === 'Image upscaled successfully.') {
								clearInterval(statusInterval); // Stop polling
								var upscaleResult = '<a href="' + statusResponse.upscale_result + '" target="_blank">View Upscaled Image</a>';
								$('#upscale_result_' + imageId).html(upscaleResult);
							} else if (statusResponse.message === 'Upscale in progress.') {
								$('#upscale_result_' + imageId).html('Upscaling in progress... ' + statusResponse.status);
							} else if (statusResponse.message === 'Image upscale failed.') {
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
	
	
});
