<!DOCTYPE html>
<html>
<head>
<title>Registro</title>

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
    background:#2196F3;
    color:white;
    border:none;
    border-radius:5px;
}

button:hover{
    background:#1976D2;
}

a{
    display:block;
    text-align:center;
    margin-top:10px;
}

</style>

</head>
<body>

<div class="card">

<h2>Registro</h2>

<form method="post" action="/guardar-registro">

<input type="text" name="nombre" placeholder="Nombre" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Registrarse</button>

</form>

<a href="/login">Ir a Login</a>

</div>

</body>
</html>