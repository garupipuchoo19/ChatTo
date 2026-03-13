<!DOCTYPE html>
<html>
<head>

<title>Usuarios</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
}

.navbar{
background:#111;
}

.navbar-brand{
color:white;
}

.btn-main{
background:#ff8c42;
color:white;
border:none;
}

</style>

</head>

<body>

<nav class="navbar navbar-dark px-3">

<span class="navbar-brand">
ChatTo
</span>

<a href="/logout" class="btn btn-sm btn-light">
Salir
</a>

</nav>

<div class="container mt-4" style="max-width:600px">

<p>
Hola <strong><?= session()->get('usuario_nombre') ?></strong>
</p>

<a href="/ia" class="btn btn-main mb-3">
Chat con IA
</a>

<div class="card shadow-sm">

<ul class="list-group list-group-flush">

<?php if(empty($usuarios)): ?>

<li class="list-group-item">
No hay usuarios
</li>

<?php else: ?>

<?php foreach($usuarios as $u): ?>

<li class="list-group-item d-flex justify-content-between align-items-center">

<?= esc($u['nombre']) ?>

<a href="/chat/<?= $u['id'] ?>" class="btn btn-dark btn-sm">
Abrir chat
</a>

</li>

<?php endforeach; ?>

<?php endif; ?>

</ul>

</div>

</div>

</body>
</html>