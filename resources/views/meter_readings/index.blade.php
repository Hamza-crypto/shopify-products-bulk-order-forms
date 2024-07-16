<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meter Reading Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Meter Reading Form</h1>
        <form action="{{ route('store-meter-reading') }}" method="POST" class="mb-5">
            @csrf
            <div class="form-group">
                <label for="meter_name">Meter Name:</label>
                <select name="meter_name" id="meter_name" class="form-control" required>
                    <option value="meter1">Meter 1</option>
                    <option value="meter2">Meter 2</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reading_value">Reading Value:</label>
                <input type="number" name="reading_value" id="reading_value" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <hr>

        @include('meter_readings.table')
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>



</html>
