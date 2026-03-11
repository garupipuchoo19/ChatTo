<?= view('layout/header') ?>

<h3 class="mb-3">Chat</h3>

<a href="/usuarios" class="btn btn-secondary mb-3">← Volver</a>

<div class="card">

<div class="card-body" id="chat-box" style="height:350px; overflow:auto;">

<?php foreach($mensajes as $m): ?>

<div class="mb-2">

<strong>
<?= $m['remitente_id'] == session()->get('usuario_id') ? 'Yo' : 'Usuario' ?>:
</strong>

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

</div>

<?php endforeach; ?>

</div>
</div>

<form method="post" action="/enviar" enctype="multipart/form-data" class="mt-3">

<input type="hidden" name="conversacion_id" value="<?= $conversacion_id ?>">
<input type="hidden" name="destino_id" value="<?= $destino_id ?>">

<div class="row">

<div class="col-md-6">
<input 
type="text"
name="mensaje"
class="form-control"
placeholder="Escribe un mensaje">
</div>

<div class="col-md-4">
<input 
type="file"
name="archivo"
class="form-control">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">
Enviar
</button>
</div>

</div>

</form>

<script>

function cargarMensajes(){

fetch("/mensajes/<?= $conversacion_id ?>")
.then(res => res.json())
.then(data => {

let html = "";

data.forEach(m => {

let yo = m.remitente_id == <?= session()->get('usuario_id') ?> ? "Yo" : "Usuario";

html += "<div class='mb-2'><strong>"+yo+":</strong> ";

if(m.tipo == "texto"){
html += m.mensaje;
}

if(m.tipo == "imagen"){
html += "<br><img src='/uploads/"+m.archivo+"' width='200'>";
}

if(m.tipo == "video"){
html += "<br><video width='250' controls><source src='/uploads/"+m.archivo+"'></video>";
}

html += "</div>";

});

document.getElementById("chat-box").innerHTML = html;

});

}

setInterval(cargarMensajes,2000);

</script>

<?= view('layout/footer') ?>
