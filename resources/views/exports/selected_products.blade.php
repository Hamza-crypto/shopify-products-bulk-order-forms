<!-- resources/views/products.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>Select Products</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <div class="container mt-5">
        <form action="{{ route('products.submit') }}" method="POST">
            @csrf
            <h1>Products</h1>
            <button type="submit" class="btn btn-primary sticky-top">Submit</button>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                        <tr>
                            <td>{{ $product['title'] }} <input type="hidden" name="title_{{ $index }}"
                                    value="{{ $product['title'] }}"></td>
                            <td><img src="{{ $product['image_src'] }}" alt="{{ $product['title'] }}"
                                    width="100"><input type="hidden" name="image_{{ $index }}"
                                    value="{{ $product['image_src'] }}"></td>
                            <td>{{ $product['price'] }} <input type="hidden" name="price_{{ $index }}"
                                    value="{{ $product['price'] }}"></td>
                            <td><input type="number" name="quantities[{{ $index }}]" value="1"
                                    min="1"></td>
                            <td><input type="checkbox" name="products[]" value="{{ $index }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</body>

</html>
