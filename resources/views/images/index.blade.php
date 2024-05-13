@extends('layouts.app')

@section('content')
	<div id="floating-image-container" style="display: none; position: absolute; z-index: 1050;">
		<img id="floating-image" src="" alt="Hovered Image" style="width: 700px; height:700px; max-width: 700px; max-height: 700px;">
	</div>
	
	<div class="container">
		<h1 class="text-center mb-4">Image Gallery</h1>
		<a href="{{ route('images.scan') }}" class="btn btn-primary">Rescan Storage Folder</a>
		<div class="row mt-4">
			@foreach($images as $image)
				<div class="col-md-3 mb-3">
					<div class="card">
						<div class="card-header">
							{{ $image->folder }} <br>
							<div title="{{ $image->image_name }}" style="height: 20px; overflow: hidden; white-space: nowrap;">
							{{ $image->image_name }}
							</div>
						</div>
						<div class="card-body">
							<h6 class="card-title"></h6>
							<img src="{{ route('image.display', ['my_image' => $image->id, 'width' => 300]) }}"
							     data-hover-src="{{ route('image.display', ['my_image' => $image->id, 'width' => 700]) }}"
							     class="img-fluid mb-2" alt="{{ $image->notes }}" style="width: 100%; min-height: 200px;">
							
							<p class="card-text">
								<input type="text" class="form-control image-note" data-image-id="{{ $image->id }}"
								       value="{{ $image->notes }}">
							<div class="upscale-result" id="upscale_result_{{ $image->id }}">
								@if(!empty($image->upscale_name))
									<a href="{{ asset('storage/upscaled/'.$image->upscale_name) }}" target="_blank">View Upscaled Image</a>
								@endif
							</div>
							</p>
							<button class="btn btn-success update-note" data-image-id="{{ $image->id }}">Update Notes</button>
							<button class="btn btn-info upscale-image" data-image-id="{{ $image->id }}" data-image-url="{{ env('APP_URL') . '/' . ('storage/images/'.$image->folder.'/'.$image->image_name) }}">Upscale Image</button>
						</div>
					</div>
				</div>
			@endforeach
		</div>
		<!-- Pagination -->
		<div class="row">
			<div class="col-12">
				{{ $images->links('pagination::bootstrap-5')  }}
			</div>
		</div>
	</div>
@endsection
