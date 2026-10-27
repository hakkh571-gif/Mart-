<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('data.store') }}" method="POST">
        @csrf
        <label for="name">Food Name:</label>
        <input type="text" name="name" id="name" required>
        <br>
        <label for="price">Food Price:</label>
        <input type="number" name="price" id="price" required>
        <br>
        <button type="submit">Add Food</button>
    </form>
</body>
</html>