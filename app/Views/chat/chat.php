<h2>Chat</h2>

<a href="/usuarios">← Volver</a>

<hr>

<div style="border:1px solid #ccc; padding:10px; height:300px; overflow:auto;">

<?php foreach($mensajes as $m): ?>

<p>
<strong><?= $m['remitente_id'] == session()->get('usuario_id') ? 'Yo' : 'Otro' ?>:</strong>

<?php if($m['tipo'] == 'texto'): ?>

    <?= esc($m['mensaje']) ?>

<?php elseif($m['tipo'] == 'imagen'): ?>

    <br>
    <img src="/uploads/<?= $m['archivo'] ?>" width="200">

<?php elseif($m['tipo'] == 'video'): ?>

    <br>
    <video width="250" controls>
        <source src="/uploads/<?= $m['archivo'] ?>">
    </video>

<?php endif; ?>

</p>

<?php endforeach; ?>

</div>

<hr>

<form method="post" action="/enviar" enctype="multipart/form-data">

<input type="hidden" name="conversacion_id" value="<?= $conversacion_id ?>">
<input type="hidden" name="destino_id" value="<?= $destino_id ?>">

<input type="text" name="mensaje" placeholder="Mensaje">

<br><br>

<input type="file" name="archivo">

<br><br>

<button type="submit">Enviar</button>

</form>
