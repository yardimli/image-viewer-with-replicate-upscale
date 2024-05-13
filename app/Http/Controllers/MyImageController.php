<?php

	namespace App\Http\Controllers;

	use App\Models\MyImage;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Facades\Storage;
	use Intervention\Image\Drivers\Gd\Driver;
	use Intervention\Image\ImageManager;
	use Intervention\Image\Laravel\Facades\Image;
	use GuzzleHttp\Client;

	class MyImageController extends Controller
	{
		public function index()
		{
			$images = MyImage::paginate(20); // Adjust the number of items per page as needed
			return view('images.index', compact('images'));
		}

		public function scanFolder()
		{
			$directory = 'public/images'; // Base directory
			$files = Storage::allFiles($directory); // Recursively get all files

			foreach ($files as $file) {
				// Filter only JPG and PNG files
				if (preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
					$fileName = basename($file);
					$relativeFolderPath = dirname($file);
					$relativeFolderPath = str_replace($directory . '/', '', $relativeFolderPath); // Get relative path to the base directory

					// Check if the file is already in the database
					$exists = MyImage::where('image_name', $fileName)->where('folder', $relativeFolderPath)->exists();
					if (!$exists) {
						// Add new file to the database
						MyImage::create([
							'image_name' => $fileName,
							'folder' => $relativeFolderPath,
							'notes' => '',
							'upscale_name' => $fileName,
						]);
					}
				}
			}

			// Redirect back to the images index page
			return redirect()->route('images.index');
		}


		public function updateNotes(Request $request, MyImage $my_image)
		{
			$my_image->notes = $request->notes;
			$my_image->save();

			return response()->json(['message' => 'Notes updated successfully.']);
		}

		public function displayImage(MyImage $my_image, $width = 300)
		{
			$path = storage_path('app/public/images/' . $my_image->folder . '/' . $my_image->image_name);
			if (!File::exists($path)) {
				abort(404);
			}

			$file = File::get($path);
			$type = File::mimeType($path);

			$manager = new ImageManager(Driver::class);
			$image = $manager->read($file);
			$image->scaleDown($width, $width);
			$encoded = $image->encodeByMediaType('image/jpeg', progressive: true, quality: 98);

			return response($encoded)->header('Content-Type', 'image/jpeg');
		}


		public function upscaleImage(Request $request, MyImage $my_image)
		{
			$client = new Client();
			$response = $client->post('https://api.replicate.com/v1/predictions', [
				'headers' => [
					'Authorization' => 'Bearer ' . env('REPLICATE_API_TOKEN'),
					'Content-Type' => 'application/json',
				],
				'json' => [
					"version" => "4af11083a13ebb9bf97a88d7906ef21cf79d1f2e5fa9d87b70739ce6b8113d29",
					"input" => [
						"hdr" => 0.2,
						"image" => $request->image_url,
						"prompt" => "4k, enhance",
						"creativity" => 0.3,
						"resemblance" => 1,
						"guidance_scale" => 5,
						"negative_prompt" => ""
					]
				]
			]);

			$body = json_decode((string) $response->getBody(), true);

			// Assuming the response has a result URL or some indication of the upscale result
			$my_image->upscale_result = $body['result'] ?? 'Error or no result';
			$my_image->save();

			return response()->json(['message' => 'Image upscaled successfully.', 'upscale_result' => $my_image->upscale_result]);
		}
	}
