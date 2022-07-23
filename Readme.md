<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NoDB</title>
<link id="faviconTag" rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<!-- import google fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@600&display=swap" rel="stylesheet">
<!-- import google fonts -->
<link rel="stylesheet" href="https://batuhanozen.com/css/reset.css">
<style>
    html,body{
        font-family: 'Fira Code', monospace;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }
    hr{
        display: flex;
        width: 100%;
    }
    .title{
        display: flex;
        align-items: center;
    }
    .logo{
        width: 120px;height: 120px;
        border-radius: 50%;
        margin-left: 15px;
    }
    .container{
        display: flex;
        flex-direction: column;
    }
    td{
        border: 1px solid #000;
        padding: 9px;
    }
</style>
<div class="container">
    <h1 style="user-select:none;display:flex;align-items:center;font-size:4.8em"><img id="logo" class="logo" src="./img/NoDB-Light.png" alt="NoDB Logo">DB</h1>
    <h1 class="title">Definition</h1>
    <hr/>
    <table>
        <tr>
            <td>
                <b>NoDB</b> is a
            </td>
            <td> => </td>
            <td>
                <b>file based</b> database.
            </td>
        </tr>
        <tr>
            <td>
                It keeps content with 
            </td>
            <td> => </td>
            <td>
                indexes for every single table.
            </td>
        </tr>
        <tr>
            <td>
                No db works with
            </td>
            <td> 
                => 
            </td>
            <td>
                sql queries.
            </td>
        </tr>
        <tr>
            <td>
                It is not a
            </td>
            <td> 
                => 
            </td>
            <td>
                strong and completed database at the moment.
            </td>
        </tr>
        <tr>
            <td>
                It's Still
            </td>
            <td> 
                => 
            </td>
            <td>
                in development...
            </td>
        </tr>
    </table>
</div>
<script src="./readme.js"></script>
</body>
</html>