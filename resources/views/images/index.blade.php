@extends('layouts.image-app')

@section('content')
	<div class="container">
		<h1 class="text-center mb-4">Image Gallery</h1>
		
		<div class="row mb-4">
			<div class="col-md-3">
				<form action="{{ route('images.index') }}" method="GET">
					<select name="folder" class="form-select" onchange="this.form.submit()">
						<option value="all" {{ $selectedFolder == 'all' ? 'selected' : '' }}>All Folders</option>
						@foreach($folders as $folder)
							<option value="{{ $folder }}" {{ $selectedFolder == $folder ? 'selected' : '' }}>
								{{ $folder }}
							</option>
						@endforeach
					</select>
				</form>
			</div>
			<div class="col-md-3">
				<form action="{{ route('images.index') }}" method="GET">
					<input type="hidden" name="folder" value="{{ $selectedFolder }}">
					<select name="sort" class="form-select" onchange="this.form.submit()">
						<option value="" {{ $selectedSort == '' ? 'selected' : '' }}>No Sort</option>
						<option value="album_filename_desc" {{ $selectedSort == 'album_filename_desc' ? 'selected' : '' }}>Album Filename</option>
						
						<option value="notes_desc" {{ $selectedSort == 'notes_desc' ? 'selected' : '' }}>Notes</option>
						
						<option value="filename_asc" {{ $selectedSort == 'filename_asc' ? 'selected' : '' }}>Filename Ascending</option>
						<option value="filename_desc" {{ $selectedSort == 'filename_desc' ? 'selected' : '' }}>Filename Descending</option>
						
						<option value="id_asc" {{ $selectedSort == 'id_asc' ? 'selected' : '' }}>Add Order Ascending</option>
						<option value="id_desc" {{ $selectedSort == 'id_desc' ? 'selected' : '' }}>Add Order Descending</option>
					</select>
				</form>
			</div>
			<div class="col-md-6 text-end">
				<form method="POST" action="{{ route('logout') }}" style="display: inline-block;">
					@csrf
					<button type="submit" class="btn btn-danger">Logout</button>
				</form>
				<a href="{{ route('images.scan') }}" class="btn btn-primary">Rescan Storage Folder</a>
			</div>
		</div>
		
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
							<img style="cursor: pointer;" src="{{ route('image.display', ['my_image' => $image->id, 'width' => 300]) }}"
							     data-hover-src="{{ route('image.display', ['my_image' => $image->id, 'width' => 900]) }}"
							     class="img-fluid mb-2 click-to-enlarge" alt="{{ $image->notes }}"
							     style="width: 100%; min-height: 200px;" data-image-id="{{ $image->id }}"
							     data-image-url="{{ env('APP_URL') . '/' . ('storage/images/'.$image->folder.'/'.$image->image_name) }}"
							     data-notes="{{ $image->notes }}">
							
							<p class="card-text">
							<div class="upscale-result" id="upscale_notes_{{ $image->id }}">
								{{ $image->notes }}
								@if($image->album_filename)
									<div>Album: {{ $image->album_filename }}</div>
								@endif
								@if($image->image_keywords)
									<div>Keywords: {{ $image->image_keywords }}</div>
								@endif
							</div>
							<div class="upscale-result" id="upscale_result_{{ $image->id }}">
								@if(!empty($image->upscale_name))
									<a href="{{ asset('storage/upscaled/'.$image->upscale_name) }}" target="_blank">View Upscaled Image</a>
								@endif
							</div>
							</p>
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
	
	<!-- Modal for displaying larger image and buttons -->
	<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true" >
		<div class="modal-dialog modal-lg" style="max-width: 700px;">
			<div class="modal-content">
{{--				<div class="modal-header">--}}
{{--					<h5 class="modal-title" id="imageModalLabel">Image Details</h5>--}}
{{--					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--				</div>--}}
				<div class="modal-body text-center">
					<img id="modalImage" src="" alt="Selected Image" class="img-fluid mb-2" style="max-width: 100%; height:auto;">
					<div id="add-note-results"></div>
					<div class="upscale-result-modal" id="upscale_result_modal"></div>
				</div>
				<div class="modal-footer" style="align-items: start; justify-content: normal;">
					<input type="text" class="form-control image-note-modal" data-image-id="" value="" style="max-width: 300px; display: inline-block;" placeholder="notes">
					<input type="text" class="form-control album-filename-modal" data-image-id="" value="" placeholder="Album Filename" style="max-width: 300px; display: inline-block;">
					<input type="text" class="form-control image-keywords-modal" data-image-id="" value="" placeholder="Image Keywords" style="max-width: 300px; display: inline-block;">
					<button class="btn btn-success update-note-modal" data-image-id="">Update Notes</button>
					<button class="btn btn-info upscale-image-modal" data-image-id="" data-image-url="">Upscale Image</button>
				</div>
			</div>
		</div>
	</div>
@endsection
