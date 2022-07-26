<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script>
        ws = new WebSocket("ws://127.0.0.3:3000");
        ws.onopen = function() {
            console.log("connected successfully");
            let data = {
                method:'create',
                data: {
                    test:'test'
                }
            };
            ws.send(JSON.stringify(data));
        };
        ws.onmessage = function(e) {
            console.log(e.data);
        };
    </script>
</body>
</html>