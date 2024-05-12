@extends('layouts.app')

@section('content')
	<div id="floating-image-container" style="display: none; position: absolute; z-index: 1050;">
		<img id="floating-image" src="" alt="Hovered Image" style="width: 600px; height:600px; max-width: 600px; max-height: 600px;">
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
							{{ $image->image_name }}
						</div>
						<div class="card-body">
							<h6 class="card-title"></h6>
							<img src="{{ route('image.display', ['my_image' => $image->id, 'width' => 300]) }}"
							     data-hover-src="{{ route('image.display', ['my_image' => $image->id, 'width' => 600]) }}"
							     class="img-fluid mb-2" alt="{{ $image->notes }}" style="width: 100%; min-height: 200px;">
							
							<p class="card-text">
								<input type="text" class="form-control image-note" data-image-id="{{ $image->id }}"
								       value="{{ $image->notes }}">
							</p>
							<button class="btn btn-success update-note" data-image-id="{{ $image->id }}">Update Notes</button>
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
