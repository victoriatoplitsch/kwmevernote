<!DOCTYPE html>
<html>
<head>
    <title>KWM Evernote</title>
</head>
<body>
    <ul>
        @foreach($registers as $register)
            <li><a href="registers/{{$register->id}}">{{$register->name}} {{$register->created_at}} {{$register->updated_at}}</a></li>
        @endforeach
    </ul>
</body>
</html>
