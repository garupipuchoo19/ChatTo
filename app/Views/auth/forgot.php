<!DOCTYPE html>
<html>
<head>

<title>Recuperar contraseña</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
}

.btn-main{
background:#ff8c42;
color:white;
border:none;
}

</style>

</head>

<body>

<div class="container vh-100 d-flex align-items-center justify-content-center">

<div class="card shadow-lg p-4" style="width:420px">

<h3 class="text-center mb-4">Recuperar contraseña</h3>

<form method="post" action="/forgot">

<div class="mb-3">

<label>Email</label>

<input type="email" name="email" class="form-control" required>

</div>

<button class="btn btn-main w-100">
Enviar recuperación
</button>

</form>

</div>

</div>

</body>
</html>