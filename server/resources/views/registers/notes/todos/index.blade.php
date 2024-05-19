<!DOCTYPE html>
<html>
<head>
    <title>KWM Evernote</title>
</head>
<body>
<ul>
    @foreach($todos as $todo)
        <li><a href="todos/{{$todo->id}}">{{$todo->title}} {{$todo->description}} {{$todo->due_date}} {{$todo->is_complete}}</a></li>
    @endforeach
</ul>
</body>
</html>
