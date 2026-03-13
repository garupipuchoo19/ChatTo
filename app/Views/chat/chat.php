<!DOCTYPE html>
<html>
<head>

<title>Chat</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
font-family:Segoe UI;
}

.navbar{
background:#111;
}

#chat-box{
height:420px;
overflow-y:auto;
background:white;
padding:20px;
border-radius:10px;
}

.bubble-me{
background:#ff8c42;
color:white;
padding:10px 15px;
border-radius:15px;
display:inline-block;
max-width:70%;
}

.bubble-other{
background:#e9ecef;
padding:10px 15px;
border-radius:15px;
display:inline-block;
max-width:70%;
}

.btn-main{
background:#ff8c42;
border:none;
color:white;
}

.btn-main:hover{
background:#e6762f;
}

</style>

</head>

<body>

<nav class="navbar navbar-dark px-3">

<span class="navbar-brand">
Chat
</span>

<a href="/usuarios" class="btn btn-sm btn-light">
Volver
</a>

</nav>

<div class="container mt-4" style="max-width:700px">

<div id="chat-box" class="shadow-sm mb-3">

<?php foreach($mensajes as $m): ?>

<?php $yo = $m['remitente_id'] == session()->get('usuario_id'); ?>

<div class="<?= $yo ? 'text-end' : 'text-start' ?> mb-3">

<span class="<?= $yo ? 'bubble-me' : 'bubble-other' ?>">

<?php if($m['tipo']=='texto'): ?>

<?= esc($m['mensaje']) ?>

<?php elseif($m['tipo']=='imagen'): ?>

<br>
<img src="/uploads/<?= $m['archivo'] ?>" width="200">

<?php elseif($m['tipo']=='video'): ?>

<video width="250" controls>
<source src="/uploads/<?= $m['archivo'] ?>">
</video>

<?php endif; ?>

</span>

</div>

<?php endforeach; ?>

</div>

<form method="post" action="/enviar" enctype="multipart/form-data">

<input type="hidden" name="conversacion_id" value="<?= $conversacion_id ?>">
<input type="hidden" name="destino_id" value="<?= $destino_id ?>">

<div class="input-group">

<input 
type="text"
name="mensaje"
class="form-control"
placeholder="Escribe un mensaje..."
>

<input 
type="file"
name="archivo"
class="form-control"
>

<button class="btn btn-main">
Enviar
</button>

</div>

</form>

</div>

<script>

function cargarMensajes(){

fetch("/mensajes/<?= $conversacion_id ?>")
.then(res => res.json())
.then(data => {

let html = "";

data.forEach(m => {

let yo = m.remitente_id == <?= session()->get('usuario_id') ?>;

html += `<div class="${yo?'text-end':'text-start'} mb-3">`;

html += `<span class="${yo?'bubble-me':'bubble-other'}">`;

if(m.tipo=="texto"){
html+=m.mensaje;
}

if(m.tipo=="imagen"){
html+=`<br><img src="/uploads/${m.archivo}" width="200">`;
}

if(m.tipo=="video"){
html+=`<video width="250" controls><source src="/uploads/${m.archivo}"></video>`;
}

html += "</span></div>";

});

document.getElementById("chat-box").innerHTML = html;

});

}

setInterval(cargarMensajes,2000);

</script>

</body>
</html>