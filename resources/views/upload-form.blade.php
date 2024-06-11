<!DOCTYPE html>
<html>

<head>
    <title>Upload Excel File</title>
</head>

<body>
    <form action="/upload" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>
</body>

</html>
