<!DOCTYPE html>
<html>
<head>
<title>Cambiar contraseña</title>

<style>

body{
font-family:Arial;
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

input{
width:100%;
padding:10px;
margin:10px 0;
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
}

</style>

</head>
<body>

<div class="card">

<h2>Nueva contraseña</h2>

<form method="post" action="/reset">

<input type="hidden" name="token" value="<?= $token ?>">

<input type="password" name="password" placeholder="Nueva contraseña" required>

<button type="submit">Cambiar contraseña</button>

</form>

</div>

</body>
</html>