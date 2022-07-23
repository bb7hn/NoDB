<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Table</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@600&display=swap" rel="stylesheet">
    <style>
        html{
            font-family: 'Fira Code', monospace;
        }
    </style>
</head>
<body>
<h1 id="count">Data count</h1>
<script>
    const getCount = ()=>{
        
        fetch('/test/users.nodb').then(e=>e.json()).then(e=>{
            let val = e.primary.lastVal;
            document.getElementById('count').innerHTML = `Data count => ${val.toLocaleString('en-US')} Records`;
        });
        setTimeout(() => {
            getCount();
        }, 400);
    };
    getCount();
</script>
</body>
</html>