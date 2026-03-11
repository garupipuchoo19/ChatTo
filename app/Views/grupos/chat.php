<?= view('layout/header') ?>

<div class="container mt-4">

<h3>Chat de grupo</h3>

<a href="/grupos">← Volver</a>

<hr>

<div class="border p-3 mb-3" style="height:300px; overflow:auto;">

<?php foreach($mensajes as $m): ?>

<p>
<strong>
<?= $m['remitente_id']==session()->get('usuario_id') ? 'Yo' : 'Usuario' ?>
</strong>

: <?= esc($m['mensaje']) ?>

</p>

<?php endforeach; ?>

</div>

<form method="post" action="/grupo/enviar">

<input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">

<div class="input-group">

<input type="text" name="mensaje" class="form-control" placeholder="Escribe mensaje">

<button class="btn btn-primary">
Enviar
</button>

</div>

</form>

</div>

<?= view('layout/footer') ?>
