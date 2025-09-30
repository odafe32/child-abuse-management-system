<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $meta_title }}</title>

    <!-- Updated Meta Description -->
    <meta name="description" content="CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.">

    <meta name="author" content="CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.." />
    <meta content="CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively." name="description" />
    <meta content="{{ $meta_title }}" property="og:title" />
    <meta content="CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.. />
    <meta content="{{ $meta_title }}" property="twitter:title" />
    <meta content="CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.." />
    <meta content="{{ $meta_image }}" property="og:image" />
    <meta content="{{ $meta_image }}" property="twitter:image" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:type" content="website" />
    <meta content="summary_large_image" name="twitter:card" />
    <meta content="Teranium Co" name="generator" />

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ url('logo.png') }}" />

  <!-- Vendor css (Require in all Page) -->
     <link href="{{ url('assets/css/vendor.min.css?v=' .env('CACHE_VERSION')) }}" rel="stylesheet" type="text/css" />

     <!-- Icons css (Require in all Page) -->
     <link href="{{ url('assets/css/icons.min.css?v=' .env('CACHE_VERSION')) }}" rel="stylesheet" type="text/css" />

     <!-- App css (Require in all Page) -->
     <link href="{{ url('assets/css/app.min.css?v=' .env('CACHE_VERSION')) }}" rel="stylesheet" type="text/css" />

     <!-- Theme Config js (Require in all Page) -->
     <script src="{{ url('assets/js/config.min.js?v=' .env('CACHE_VERSION')) }}"></script>



</head>

<body class="authentication-bg">
    {{ csrf_field() }}
     <section class="h-screen flex items-center justify-center bg-no-repeat inset-0 bg-cover bg-[url('../images/bg.html')]">

            @yield('content')
     </section>




      <!-- Vendor Javascript (Require in all Page) -->
     <script src="{{ url('assets/js/vendor.js?v=' .env('CACHE_VERSION')) }}"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="{{ url('assets/js/app.js?v=' .env('CACHE_VERSION')) }}"></script>

</body>

</html>
