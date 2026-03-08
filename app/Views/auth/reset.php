<?= view('layout/header') ?>

<div class="row justify-content-center">

<div class="col-md-4">

<div class="card shadow">

<div class="card-body">

<h3 class="text-center mb-4">Nueva contraseña</h3>

<form method="post" action="/reset">

<input type="hidden" name="token" value="<?= $token ?>">

<div class="mb-3">
<input 
type="password"
name="password"
class="form-control"
placeholder="Nueva contraseña"
required>
</div>

<button class="btn btn-primary w-100">
Cambiar contraseña
</button>

</form>

</div>
</div>

</div>
</div>

<?= view('layout/footer') ?>
