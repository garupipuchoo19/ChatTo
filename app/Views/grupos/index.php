<?= view('layout/header') ?>

<div class="container mt-4">

<h3>Grupos</h3>

<ul class="list-group">

<?php foreach($grupos as $g): ?>

<li class="list-group-item d-flex justify-content-between">

<?= esc($g['nombre']) ?>

<a href="/grupo/<?= $g['id'] ?>" class="btn btn-primary btn-sm">
Entrar
</a>

</li>

<?php endforeach; ?>

</ul>

</div>

<?= view('layout/footer') ?>
