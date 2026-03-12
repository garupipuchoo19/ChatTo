<?= view('layout/header') ?>

<h2>Crear Grupo</h2>

<form action="/grupos/guardar" method="post">

<div class="mb-3">
<label>Nombre del grupo</label>
<input type="text" name="nombre" class="form-control" required>
</div>

<h4>Seleccionar usuarios</h4>

<?php foreach($usuarios as $u): ?>

<div class="form-check">

<input class="form-check-input" 
type="checkbox" 
name="usuarios[]" 
value="<?= $u['id'] ?>">

<label class="form-check-label">
<?= esc($u['nombre']) ?>
</label>

</div>

<?php endforeach; ?>

<br>

<button class="btn btn-success">
Crear grupo
</button>

</form>

<?= view('layout/footer') ?>
