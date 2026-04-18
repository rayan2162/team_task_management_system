<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    this is user page
    1. user can visit this page only if they are logged in
    2. admin cant visit this page
    3. without login noone can visit this page
    <auth()->user() ? 'Logged in' : 'Not logged in'>
        
    
</body>
</html>