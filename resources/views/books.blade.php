<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <!-- resources/views/books.blade.php -->
<ul>
    @foreach ($data as $book)
        <li>{{ $book->title }} - {{ $book->author }} ({{ $book->publication_year }})</li>
    @endforeach
</ul>

{{ $data->appends(request()->input())->links() }}

</body>
</html>