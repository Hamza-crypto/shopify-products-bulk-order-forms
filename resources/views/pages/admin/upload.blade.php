<!-- resources/views/pages/admin/upload.blade.php -->

@extends('layouts.app')

@section('title', 'Upload Shopify Products File')


@section('content')


    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">

            <div class="alert-message">
                {!! session('success') !!}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    @include('pages.admin.display_products_page_with_images')

    @include('pages.admin.generate_csv_with_img')
    @include('pages.admin.download_all_images')
@endsection
