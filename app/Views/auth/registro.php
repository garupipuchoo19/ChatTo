<?= view('layout/header') ?>

<div class="row justify-content-center">

<div class="col-md-4">

<div class="card shadow">

<div class="card-body">

<h3 class="text-center mb-4">Crear cuenta</h3>

<form method="post" action="/guardar-registro">

<div class="mb-3">
<input 
type="text" 
name="nombre"
class="form-control"
placeholder="Nombre"
required>
</div>

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

<button class="btn btn-success w-100">
Registrarse
</button>

</form>

<hr>

<a href="/login" class="btn btn-primary w-100">
Ir al login
</a>

</div>
</div>

</div>
</div>

<?= view('layout/footer') ?>
