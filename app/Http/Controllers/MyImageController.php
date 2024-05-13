<?php

	namespace App\Http\Controllers;

	use App\Models\MyImage;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\Log;
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

			foreach ($images as $image) {
				// Check if upscale_name is null but upscale_result has a prediction ID
				if (empty($image->upscale_name) && !empty($image->upscale_result)) {
					$upscaleData = json_decode($image->upscale_result, true);
					$predictionId = $upscaleData['id'] ?? null;
					if ($predictionId) {
						$response = Http::withHeaders([
							'Authorization' => 'Bearer ' . env('REPLICATE_API_TOKEN'),
						])->get("https://api.replicate.com/v1/predictions/{$predictionId}");

						$body = $response->json();
						Log::info('Upscale Status Check in Index');
						Log::info($body);

						// Check if the status is succeeded and update accordingly
						if ($body['status'] === 'succeeded') {
							$upscaledImageUrl = $body['output'][0]; // Adjust based on actual API response
							$imageName = "{$image->id}_upscaled.jpg";
							$storagePath = "public/upscaled/{$imageName}";

							// Download and save the file
							$contents = file_get_contents($upscaledImageUrl);
							Storage::put($storagePath, $contents);

							// Update database with final upscale result and name
							$image->upscale_result = json_encode($body);
							$image->upscale_name = $imageName;
							$image->save();
						} elseif ($body['status'] === 'failed') {
							// Handle failure (optional)
							$image->upscale_name = 'Failed, check logs or database table, try again';
							$image->upscale_result = json_encode($body);
							$image->save();
						}
						// In-progress status will just leave the record as is
					}
				}
			}

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
							'upscale_name' => '',
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

			$body = $response->getBody();
			$content = $body->getContents();


			// Assuming the response has a result URL or some indication of the upscale result
			$my_image->upscale_result = $content ?? '{"result":"Error or no result"}';
			$my_image->save();

			$json_result = json_decode($my_image->upscale_result, true);
			Log::info($my_image->upscale_result);

			return response()->json(['message' => 'Image upscaled successfully.', 'upscale_result' => $json_result, 'prediction_id' => $json_result['id'] ?? null, 'status_url' => $json_result['urls']['get'] ?? null]);
		}

		public function checkUpscaleStatus(Request $request, MyImage $my_image, $prediction_id)
		{
			$response = Http::withHeaders([
				'Authorization' => 'Bearer ' . env('REPLICATE_API_TOKEN'),
				'Content-Type' => 'application/json',
			])->get("https://api.replicate.com/v1/predictions/{$prediction_id}");

			$body = $response->json();
			Log::info($body);

			if ($body['status'] === 'succeeded') {
				$upscaledImageUrl = $body['output'][0]; // Assuming this is the correct path to the output image URL
				$imageName = "{$my_image->id}_upscaled.jpg";
				$storagePath = "public/upscaled/{$imageName}";

				// Download and save the file
				$contents = file_get_contents($upscaledImageUrl);
				Storage::put($storagePath, $contents);

				// Update database with final upscale result and name
				$my_image->upscale_result = json_encode($body);
				$my_image->upscale_name = $imageName; // Assuming you want to save the image name here as well
				$my_image->save();

				return response()->json(['message' => 'Image upscaled successfully.', 'upscale_result' => asset("storage/upscaled/{$imageName}")]);
			} else if ($body['status'] === 'failed') {
				return response()->json(['message' => 'Image upscale failed.', 'error' => $body['error']]);
			}

			// If the status is neither succeeded nor failed, it's still in progress
			return response()->json(['message' => 'Upscale in progress.', 'status' => $body['logs']]);
		}
	}
