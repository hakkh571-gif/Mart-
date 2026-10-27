<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
       th,td{
        border: 4px solid black;
       }
    </style>
</head>
<body>
 <table>
    <thead>
        <tr>
            <th>Food Name</th>
            <th>Food Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($foods as $food)
        <tr>
            <td>{{ $food->name }}</td>
            <td>{{ $food->price }} 😍៛</td>
            <td>Edit</td>
        </tr>
        @endforeach
    </tbody> 
    </table>  
</body>
</html>