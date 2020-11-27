<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Hello, {{ $detail['name']}}</h2>
    <p>Follow this link to verify your email address.</p>
    <a href="{{url('api/user/verify', $detail['email'])}}">LINK</a>
</body>
</html>