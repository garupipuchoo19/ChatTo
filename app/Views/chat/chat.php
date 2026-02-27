<h2>Chat</h2>

<a href="/usuarios">← Volver</a>

<hr>

<div style="border:1px solid #ccc; padding:10px; height:300px; overflow:auto;">
<?php foreach($mensajes as $m): ?>
    <p>
        <strong><?= $m['remitente_id'] == session()->get('usuario_id') ? 'Yo' : 'Otro' ?>:</strong>
        <?= esc($m['mensaje']) ?>
    </p>
<?php endforeach; ?>
</div>

<hr>

<form method="post" action="/enviar">
    <input type="hidden" name="conversacion_id" value="<?= $conversacion_id ?>">
    <input type="hidden" name="destino_id" value="<?= $destino_id ?>">

    <input type="text" name="mensaje" placeholder="Mensaje">
    <button type="submit">Enviar</button>
</form>