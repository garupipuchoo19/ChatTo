<?= view('layout/header') ?>

<h2>Grupos</h2>

<a href="/grupos/crear" class="btn btn-success mb-3">
Crear grupo
</a>

<?php if(empty($grupos)): ?>

<div class="alert alert-warning">
No hay grupos aún
</div>

<?php else: ?>

<ul class="list-group">

<?php foreach($grupos as $g): ?>

<li class="list-group-item d-flex justify-content-between">

<?= esc($g['nombre']) ?>

<a href="/grupo_chat/<?= $g['id'] ?>" class="btn btn-primary btn-sm">
Entrar
</a>

</li>

<?php endforeach; ?>

</ul>

<?php endif; ?>

<?= view('layout/footer') ?>
