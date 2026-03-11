<?= view('layout/header') ?>

<h3 class="mb-3">Usuarios disponibles</h3>

<p class="mb-3">
Hola <strong><?= session()->get('usuario_nombre') ?></strong>
</p>

<a href="/logout" class="btn btn-danger mb-3">
Cerrar sesión
</a>

<hr>

<a href="/ia">
<button>IA</button>
</a>

<?php if(empty($usuarios)): ?>

<div class="alert alert-warning">
No hay usuarios aún
</div>

<?php else: ?>

<div class="card">

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
