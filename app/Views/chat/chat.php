<?= view('layout/header') ?>

<h3 class="mb-3">Chat</h3>

<a href="/usuarios" class="btn btn-secondary mb-3">
← Volver
</a>

<div class="card">

<div class="card-body" style="height:300px; overflow:auto;">

<?php foreach($mensajes as $m): ?>

<div class="mb-2">

<strong>
<?= $m['remitente_id'] == session()->get('usuario_id') ? 'Yo' : 'Usuario' ?>:
</strong>

<?= esc($m['mensaje']) ?>

</div>

<?php endforeach; ?>

</div>

</div>

<form method="post" action="/enviar" class="mt-3">

<input type="hidden" name="conversacion_id" value="<?= $conversacion_id ?>">
<input type="hidden" name="destino_id" value="<?= $destino_id ?>">

<div class="row">

<div class="col-md-10">
<input 
type="text" 
name="mensaje" 
class="form-control"
placeholder="Escribe un mensaje">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">
Enviar
</button>
</div>

</div>

</form>

<?= view('layout/footer') ?>
