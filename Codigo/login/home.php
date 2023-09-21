<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | RMEE</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(100deg, #def880 0, #caf67f 12.5%, #b2f07c 25%, #93e675 37.5%, #6cd86c 50%, #38ca65 62.5%, #00bf64 75%, #00b869 87.5%, #00b472 100%);              text-align: center;
            color: white;
        }
        .box{
            display: flex;
            flex-direction: column;
            align-items: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
        }
        .box1{ 
            width: 100%;
            height: 100px;
            padding-left: 50px;
            box-sizing: border-box;         
            background-color: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 10px;
        }
        .box2{  
            width: 100%;
            height: 100px;
            padding-left: 50px;
            box-sizing: border-box;    
            background-color: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 10px;
        }
        a{
            text-decoration: none;
            color: white;
            border: 3px solid green;
            border-radius: 10px;
            padding: 10px;
        }
        a:hover{
            background-color: greenyellow;
        }
    </style>
</head>
<body> 
<div class="box">
    <div class="box1">
        <a href="login.php">Login Cliente</a>
        <a href="formulario.php">Cadastro Cliente</a>
    </div>
    <div class="box2">
        <a href="loginFuncionario.php">Login Funcionário</a>
        <a href="loginAdm.php">Login ADM</a>
    </div>
    </div>
</body>
</html>