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
	
	
	var isHoveringImage = false;
	var intervalTimer;
	var LeftPosition;
	var LoadedImageSrc = '';
	
	$('.card img').mouseenter(function (e) {
		if (isHoveringImage) {
			return;
		}
		console.log('mouseenter');
		clearTimeout(intervalTimer);
		// Show the floating image with a fixed top position when hovering over the thumbnail
		var hoverSrc = $(this).data('hover-src');
		if (hoverSrc === LoadedImageSrc) {
			return;
		}
		LoadedImageSrc = hoverSrc;
		
		var offsetX = 15; // The offset to the right of the cursor
		leftPosition = e.pageX + offsetX;
		var windowWidth = $(window).width();
		
		$('#floating-image-container').css({
			display: 'block',
			left: leftPosition,
			top: $(window).scrollTop() + 10 // Keep it fixed at the top of the viewport with a slight margin
		});
		$('#floating-image').attr('src', '');
		
		$('#floating-image').attr('src', hoverSrc).on('load', function () {
			var imageWidth = $('#floating-image-container').outerWidth();
			
			// Check if the floating image would go out of the viewport
			if (leftPosition + imageWidth > windowWidth) {
				// If it does, position it to the left of the cursor instead
				leftPosition = e.pageX - offsetX - imageWidth;
			}
			
			$('#floating-image-container').css({
				display: 'block',
				left: leftPosition,
				top: $(window).scrollTop() + 10 // Keep it fixed at the top of the viewport with a slight margin
			});
		});
	}).mouseleave(function () {
		console.log('mouseleave');
		// Set a timeout to hide the floating image, allows checking for hovering over the floating image
		clearTimeout(intervalTimer);
		intervalTimer = setTimeout(function () {
			if (!isHoveringImage) {
				$('#floating-image-container').hide();
			}
		}, 500); // Delay to allow for quick mouse movement between elements
	});
	
	$('#floating-image-container').mouseenter(function () {
		clearTimeout(intervalTimer);
		isHoveringImage = true;
	}).mouseleave(function () {
		isHoveringImage = false;
	});
	
	$(document).mousemove(function (e) {
		if ($('#floating-image-container').is(':visible')) {
			var imageWidth = $('#floating-image-container').outerWidth();
			var windowWidth = $(window).width();
			var offsetX = 15; // The offset to the right of the cursor
			leftPosition = e.pageX + offsetX;
			
			// Check if the floating image would go out of the viewport
			if (leftPosition + imageWidth > windowWidth) {
				// If it does, position it to the left of the cursor instead
				leftPosition = e.pageX - offsetX - imageWidth;
			}
			
			$('#floating-image-container').css({
				left: leftPosition,
				top: $(window).scrollTop() + 10 // Keep it fixed at the top of the viewport with a slight margin
			});
		}
	});
	
});
