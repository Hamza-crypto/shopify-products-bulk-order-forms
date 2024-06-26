@extends('layouts.app')

@section('title', 'Upload Shopify Products File')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
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

@section('scripts')
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#products', {
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [
                    [10, 30, 50, 100, -1],
                    [10, 30, 50, 100, "All"]
                ]
            });

            let selectedRows = {};

            // Function to update the hidden input with selected rows
            function updateSelectedRows() {
                let selectedData = JSON.stringify(selectedRows, null, 2); // Pretty print
                $('#hiddenSelectedRows').val(selectedData);
                console.log(selectedData);

            }

            // Event listener for checkboxes
            $(document).on('change', 'input[type="checkbox"][name^="products"]', function() {
                let row = $(this).closest('tr');
                let rowIndex = row.find('input[name$="[handle]"]').val();

                if (this.checked) {
                    selectedRows[rowIndex] = {
                        quantity: row.find('input[name$="[quantity]"]').val(),
                        title: row.find('input[name$="[title]"]').val(),
                        price: row.find('input[name$="[price]"]').val(),
                        sku: row.find('input[name$="[sku]"]').val(),

                    };
                } else {
                    delete selectedRows[rowIndex];
                }
                updateSelectedRows();
            });

            // Ensure that form data is captured correctly
            $('form').on('submit', function(e) {
                updateSelectedRows(); // Ensure the hidden input is updated before submission
                console.log($('#hiddenSelectedRows').val()); // For debugging, remove in production
            });
        });
    </script>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="alert-message">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Select the products and then enter your details to submit the order</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="unique_id" value="{{ $unique_id }}">
                        <input type="hidden" id="hiddenSelectedRows" name="hiddenSelectedRows">
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

                        <div class="row mt-3">
                            <div class="col-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h1>Products</h1>
                                        <table class="table table-bordered mt-3" id="products">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Image</th>
                                                    <th>Title</th>
                                                    <th>Variant</th>
                                                    <th>Description</th>
                                                    <th>Brand</th>
                                                    <th>Price</th>
                                                    <th>SKU</th>
                                                    <th>Quantity</th>
                                                    <th>Select</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $global_count = 0;
                                                @endphp
                                                @foreach ($products as $single_product)
                                                    @foreach ($single_product as $product)
                                                        @php
                                                            if ($single_product[0]['status'] != 'active') {
                                                                continue;
                                                            }
                                                            $global_count++;
                                                        @endphp

                                                        <tr>
                                                            <td>{{ $global_count }}</td>
                                                            <td>
                                                                <img loading="lazy" src="{{ $product['image'] }}"
                                                                    alt="{{ $product['title'] }}"
                                                                    style="width: 200px; height: auto;">
                                                            </td>
                                                            <td><a href="{{ $domain }}/products/{{ $product['handle'] }}"
                                                                    target="_blank">{{ $product['title'] }} </br>
                                                                </a>{{ $product['handle'] }}
                                                            </td>
                                                            <td>{{ $product['variant'] }}</td>
                                                            <td>{!! $product['description'] !!}</td>
                                                            <td>{{ $product['brand'] }}</td>
                                                            <td>{{ $product['price'] }}</td>
                                                            <td>{{ $product['sku'] }}</td>
                                                            <td>
                                                                <input type="number"
                                                                    name="products[{{ $loop->parent->index }}_{{ $loop->index }}][quantity]"
                                                                    class="form-control" value="1">
                                                            </td>
                                                            <td>
                                                                <input type="checkbox"
                                                                    name="products[{{ $loop->parent->index }}_{{ $loop->index }}][selected]">


                                                                <input type="hidden"
                                                                    name="products[{{ $loop->parent->index }}_{{ $loop->index }}][handle]"
                                                                    value="{{ $product['handle'] }}">
                                                                <input type="hidden"
                                                                    name="products[{{ $loop->parent->index }}_{{ $loop->index }}][title]"
                                                                    value="{{ $product['title'] }}">
                                                                <input type="hidden"
                                                                    name="products[{{ $loop->parent->index }}_{{ $loop->index }}][price]"
                                                                    value="{{ $product['price'] }}">
                                                                <input type="hidden"
                                                                    name="products[{{ $loop->parent->index }}_{{ $loop->index }}][sku]"
                                                                    value="{{ $product['sku'] }}">
                                                            </td>
                                                        </tr>
                                                        @php
                                                            if ($product['variant_img'] == '') {
                                                                break;
                                                            }
                                                        @endphp
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
