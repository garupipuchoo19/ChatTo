<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
font-family:Segoe UI;
}

.card{
border:none;
border-radius:12px;
}

.btn-main{
background:#ff8c42;
color:white;
border:none;
}

.btn-main:hover{
background:#e6762f;
}

.title{
color:#111;
font-weight:600;
}

</style>

</head>

<body>

<div class="container vh-100 d-flex align-items-center justify-content-center">

<div class="card shadow-lg p-4" style="width:420px">

<h3 class="text-center title mb-4">
Acceso al sistema
</h3>

<?php if(session()->getFlashdata('error')): ?>

<div class="alert alert-danger">
<?= session()->getFlashdata('error') ?>
</div>

<?php endif; ?>

<form method="post" action="/validar-login">

<div class="mb-3">

<label class="form-label">Correo</label>

<input type="email" name="email" class="form-control" required>

</div>

<div class="mb-3">

<label class="form-label">Contraseña</label>

<input type="password" name="password" class="form-control" required>

</div>

<button class="btn btn-main w-100">
Entrar
</button>

</form>

<div class="text-center mt-3">

<a href="/registro">Crear cuenta</a>

<br>

<a href="/forgot">Recuperar contraseña</a>

</div>

</div>

</div>

</body>
</html>