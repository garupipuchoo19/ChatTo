<form method="post" action="/forgot">
    <input type="email" name="email" placeholder="Tu email" required>
    <button type="submit">Recuperar contraseña</button>
</form><?= view('layout/header') ?>

<div class="row justify-content-center">

<div class="col-md-4">

<div class="card shadow">

<div class="card-body">

<h3 class="text-center mb-4">Recuperar contraseña</h3>

<form method="post" action="/forgot">

<div class="mb-3">
<input 
type="email" 
name="email" 
class="form-control"
placeholder="Tu correo"
required>
</div>

<button class="btn btn-warning w-100">
Enviar enlace
</button>

</form>

<hr>

<a href="/login" class="btn btn-secondary w-100">
Volver al login
</a>

</div>
</div>

</div>
</div>

<?= view('layout/footer') ?>
