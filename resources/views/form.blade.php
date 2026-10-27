<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<form action="{{ route('data.store') }}" method="POST">
    @csrf
    <label>ឈ្មោះអាហារ</label>
    <input type="text" name="name">
    <label>តម្លៃ</label>
    <input type="number" name="price">
    <input type="submit" value="រក្សាទុក">
</form>   
</body>
</html>