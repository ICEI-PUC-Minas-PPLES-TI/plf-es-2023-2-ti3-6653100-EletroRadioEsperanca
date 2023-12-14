<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de login</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: white;
        }

        .imputLogin {
            background-color: rgba(0, 0, 0, 0.6);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 80px;
            border-radius: 15px;
            color: #fff;
        }

        input {
            padding: 15px;
            border: none;
            outline: none;
            font-size: 15px;
        }

        .inputSubmit {
            background-color: greenyellow;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 10px;
            color: black;
            font-size: 15px;

        }

        .inputSubmit:hover {
            background-color: green;
            cursor: pointer;
        }

        .linkButton {
            background-color: greenyellow;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 10px;
            color: black;
        }

        /* Container para alinhar os botões à direita */
        .buttonContainer {
            text-align: right;
            padding: 20px;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>

<body>
    <a href="../index.php" class="logo">
        <img src="../assets/img/Eletro Radio - logo menor.jpg" alt="Logo" style="height: 90px;">
        <!-- Ajuste o 'src' e 'height' conforme necessário -->
    </a>
    <div class="buttonContainer">
        <a href="loginFuncionario.php" class="linkButton">Login funcionario</a>
        <a href="loginAdm.php" class="linkButton">Login administrador</a>
    </div>
    <div class="imputLogin">
        <h1>Login</h1>
        <form action="testLogin.php" method="POST">
            <input type="text" name="email" placeholder="Email">
            <br><br>
            <input type="password" name="senha" placeholder="Senha">
            <br><br>
            <input class="inputSubmit" type="submit" name="submit" value="Entrar">
            <br><br>
            <input class="inputSubmit" type="button" name="submit" value="Registrar-se"
                onclick="redirecionarParaRegistrar()">

            <script>
                function redirecionarParaRegistrar() {
                    window.location.href = 'formulario.php';
                }
            </script>
        </form>
    </div>
</body>

</html>