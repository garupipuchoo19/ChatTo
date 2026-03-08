<?= view('layout/header') ?>

<h3 class="mb-3">Usuarios disponibles</h3>

<div class="mb-3">
Hola <strong><?= session()->get('usuario_nombre') ?></strong>
</div>

<div class="mb-3">

<a href="/logout" class="btn btn-danger me-2">
Cerrar sesión
</a>

<a href="/ia" class="btn btn-warning">
🤖 IA
</a>

</div>

<hr>

<?php if(empty($usuarios)): ?>

<div class="alert alert-warning">
No hay usuarios aún
</div>

<?php else: ?>

<div class="card shadow">

<div class="card-body">

<ul class="list-group">

<?php foreach($usuarios as $u): ?>

<li class="list-group-item d-flex justify-content-between align-items-center">

<?= esc($u['nombre']) ?>

<a href="/chat/<?= $u['id'] ?>" class="btn btn-primary btn-sm">
Chatear
</a>

</li>

<?php endforeach; ?>

</ul>

</div>
</div>

<?php endif; ?>

<?= view('layout/footer') ?>
