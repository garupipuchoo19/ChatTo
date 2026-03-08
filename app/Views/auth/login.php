<?= view('layout/header') ?>

<div class="row justify-content-center">

<div class="col-md-4">

<div class="card shadow">

<div class="card-body">

<h3 class="text-center mb-4">Iniciar sesión</h3>

<?php if(session()->getFlashdata('error')): ?>

<div class="alert alert-danger">
<?= session()->getFlashdata('error') ?>
</div>

<?php endif; ?>

<form method="post" action="/validar-login">

<div class="mb-3">
<input 
type="email" 
name="email" 
class="form-control" 
placeholder="Correo"
required>
</div>

<div class="mb-3">
<input 
type="password" 
name="password" 
class="form-control" 
placeholder="Contraseña"
required>
</div>

<button class="btn btn-primary w-100">
Ingresar
</button>

</form>

<hr>

<a href="/registro" class="btn btn-success w-100">
Crear cuenta
</a>

</div>
</div>

</div>

</div>

<?= view('layout/footer') ?>
