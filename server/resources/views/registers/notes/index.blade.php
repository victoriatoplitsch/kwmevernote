<!DOCTYPE html>
<html>
<head>
    <title>KWM Evernote</title>
</head>
<body>
<ul>
    @foreach($notes as $note)
        <li><a href="notes/{{$notes->id}}">{{$notes->title}} {{$notes->description}} {{$register->created_at}}</a></li>
    @endforeach
</ul>
</body>
</html>
