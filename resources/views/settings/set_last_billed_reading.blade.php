<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Last Billed Reading Form</title>
</head>

<body>
    <h1>Last Billed Reading Form</h1>
    <form action="{{ route('store-last-billed-reading') }}" method="POST">
        @csrf
        <label for="meter_name">Meter Name:</label>
        <select name="meter_name" id="meter_name" required>
            <option value="meter1">Meter 1</option>
            <option value="meter2">Meter 2</option>
        </select>
        <br>
        <label for="reading_value">Reading Value:</label>
        <input type="number" name="reading_value" id="reading_value" required>
        <br>
        <button type="submit">Submit</button>
    </form>
</body>

</html>
