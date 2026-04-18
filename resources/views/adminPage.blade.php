<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    this is admin page
    1. only admin can visit this page only if they are logged in
    2. user cant visit this page
    3. without login noone can visit this page
    <auth()->user() ? 'Logged in' : 'Not logged in'>
        
    
</body>
</html>