<!-- resources/views/pages/admin/upload.blade.php -->

@extends('layouts.app')

@section('title', 'Upload Shopify Products File')

@section('styles')
    <style>
        .sticky {
            position: -webkit-sticky;
            /* For Safari */
            position: sticky;
            top: 0;
            z-index: 1000;
            /* Adjust z-index as needed */
        }
    </style>

@endsection
@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="alert-message">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="row ">
        <div class="col-12 col-lg-12">
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">Select the products and then enter your details to submit the order</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('customer.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="unique_id" value="{{ $unique_id }}">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary sticky-top">Submit</button>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-body">


                    <h1>Products</h1>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Wholesale Price</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                {{-- @php
                                    if ($loop->index > 3) {
                                        break;
                                    }
                                @endphp --}}
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>
                                        @if ($product['image_src'])
                                            <img src="{{ $product['image_src'] }}" alt="{{ $product['title'] }}"
                                                style="width: 100px; height: auto;">
                                        @else
                                            No Image
                                        @endif
                                    </td>

                                    <td><a href="{{ $domain }}/products/{{ $product['handle'] }}"
                                            target="_blank">{{ $product['title'] }}</a></td>

                                    <td>{!! $product['description'] !!}</td>

                                    <td>{{ $product['price'] }}</td>
                                    <td>{{ $product['wholesale_price'] }}</td>
                                    <td>{{ $product['sku'] }}</td>

                                    <td>
                                        <input type="number" name="products[{{ $loop->index }}][quantity]"
                                            class="form-control" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="products[{{ $loop->index }}][selected]">


                                        <input type="hidden" name="products[{{ $loop->index }}][title]"
                                            value="{{ $product['title'] }}">
                                        <input type="hidden" name="products[{{ $loop->index }}][price]"
                                            value="{{ $product['price'] }}">
                                        <input type="hidden" name="products[{{ $loop->index }}][sku]"
                                            value="{{ $product['sku'] }}">
                                    </td>
                                </tr>
                            @endforeach
                            </form>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>


@endsection
