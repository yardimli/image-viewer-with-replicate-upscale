<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{{ config('app.name', 'Laravel MyImage Gallery') }}</title>
	<!-- Bootstrap CSS -->
	<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet"> <!-- If you have custom CSS -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<script>
		var csrf_token = "{{ csrf_token() }}";
	</script>
</head>
<body>
<header>
	<!-- Bootstrap Navbar or custom header content here -->
</header>

<main class="py-4">
	@yield('content')
</main>

<!-- jQuery and Bootstrap Bundle (includes Popper) -->
<script src="{{ asset('js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>

<!-- Your custom scripts -->
<script src="{{ asset('js/custom.js') }}"></script> <!-- If you have custom JS -->
</body>
</html>
