<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>

body{
    font-family: Arial;
    background:#f4f6f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    background:white;
    padding:30px;
    width:300px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
}

input{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    width:100%;
    padding:10px;
    background:#4CAF50;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#45a049;
}

a{
    display:block;
    text-align:center;
    margin-top:10px;
}

.error{
    color:red;
    text-align:center;
}

</style>

</head>
<body>

<div class="card">

<h2>Login</h2>

<?php if(session()->getFlashdata('error')): ?>
<p class="error">
<?= session()->getFlashdata('error') ?>
</p>
<?php endif; ?>

<form method="post" action="/validar-login">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Ingresar</button>

</form>

<a href="/registro">Crear cuenta</a>
<a href="/forgot">¿Olvidaste tu contraseña?</a>

</div>

</body>
</html>