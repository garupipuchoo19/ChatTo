<!DOCTYPE html>
<html>
<head>

<title>Registro</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
font-family:Segoe UI;
}

.card{
border-radius:12px;
border:none;
}

.btn-main{
background:#ff8c42;
color:white;
border:none;
}

.btn-main:hover{
background:#e6762f;
}

</style>

</head>

<body>

<div class="container vh-100 d-flex align-items-center justify-content-center">

<div class="card shadow-lg p-4" style="width:420px">

<h3 class="text-center mb-4">Crear cuenta</h3>

<form method="post" action="/guardar-registro">

<div class="mb-3">

<label>Nombre</label>

<input type="text" name="nombre" class="form-control" required>

</div>

<div class="mb-3">

<label>Email</label>

<input type="email" name="email" class="form-control" required>

</div>

<div class="mb-3">

<label>Contraseña</label>

<input type="password" name="password" class="form-control" required>

</div>

<button class="btn btn-main w-100">
Registrarse
</button>

</form>

<div class="text-center mt-3">

<a href="/login">Volver al login</a>

</div>

</div>

</div>

</body>
</html>