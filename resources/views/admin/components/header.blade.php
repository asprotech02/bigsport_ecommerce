<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>{{ $title ?? 'Dashboard' }}</title>

    <link href="{{ asset('assets/admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')
</head>

<style>
.select2-container--default .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px !important;
}

.select2-selection__rendered {
    margin-top: 2px;
}
</style>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">