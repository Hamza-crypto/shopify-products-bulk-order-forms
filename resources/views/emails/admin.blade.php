<!-- resources/views/emails/admin.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>New Order</title>
</head>

<body>
    <h1>New Order</h1>
    <p>Customer Information:</p>
    <ul>
        <li>Name: {{ $customerInfo['name'] }}</li>
        <li>Email: {{ $customerInfo['email'] }}</li>
        <li>Phone: {{ $customerInfo['phone'] }}</li>
    </ul>
    <p>You can download the selected products CSV file from the following link:</p>
    <p><a href="{{ $downloadLink }}">{{ $downloadLink }}</a></p>
    <p>Submission URL: <a href="{{ url('/products/' . $unique_id) }}">{{ url('/products/' . $unique_id) }}</a></p>
</body>

</html>
