<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $general::getGeneralValue('app', 'name') }}</title>

        @php $public = url('/'); @endphp

	<!-- Bootstrap CSS file -->
	<link rel="stylesheet" href="{{ $public }}/vendor/adminlte/dist/css/adminlte.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- Starter CMS CSS file -->
	<link rel="stylesheet" href="{{ $public }}/css/style.css">
    </head>
    <body>

	<div class="container">
	    <!-- Header -->
	    <header id="layout-header">
                @include('pages.site.header')
	    </header>

	    <!-- Content -->
	    <section id="layout-content" class="pt-4">
                @include('pages.'.$page)
	    </section>

	    <!-- Footer -->
	    <footer id="layout-footer" class="page-footer pt-4">
                @include('pages.site.footer')
	    </footer>
	</div>

    <!-- JS files: jQuery first, then Bootstrap JS -->
    <script src="{{ $public }}/vendor/adminlte/plugins/jquery/jquery.min.js"></script>
    <script src="{{ $public }}/vendor/adminlte/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- Adds possible extra js scripts pushed by pages and partials. -->
    @stack ('scripts')
    </body>
</html>
