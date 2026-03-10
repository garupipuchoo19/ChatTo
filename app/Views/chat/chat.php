<h2>Chat</h2>

<a href="/usuarios">← Volver</a>

<style>

body{
font-family:Arial;
background:#f4f6f9;
}

#chat-box{
background:white;
padding:15px;
height:400px;
overflow:auto;
border-radius:10px;
box-shadow:0 0 10px rgba(0,0,0,0.1);
}

.mensaje{
margin:10px 0;
}

.yo{
text-align:right;
}

.yo span{
background:#4CAF50;
color:white;
padding:8px 12px;
border-radius:15px;
display:inline-block;
}

.otro span{
background:#e4e6eb;
padding:8px 12px;
border-radius:15px;
display:inline-block;
}

form{
margin-top:10px;
display:flex;
gap:5px;
}

input[type=text]{
flex:1;
padding:10px;
border:1px solid #ccc;
border-radius:5px;
}

button{
background:#2196F3;
color:white;
border:none;
padding:10px;
border-radius:5px;
cursor:pointer;
}

</style>

<hr>

<div id="chat-box">

<?php foreach($mensajes as $m): ?>

<?php $yo = $m['remitente_id'] == session()->get('usuario_id'); ?>

<div class="mensaje <?= $yo ? 'yo' : 'otro' ?>">

<span>

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

</span>

</div>

<?php endforeach; ?>

</div>

<hr>

<form method="post" action="/enviar" enctype="multipart/form-data">

<input type="hidden" name="conversacion_id" value="<?= $conversacion_id ?>">
<input type="hidden" name="destino_id" value="<?= $destino_id ?>">

<input type="text" name="mensaje" placeholder="Mensaje">

<input type="file" name="archivo">

<button type="submit">Enviar</button>

</form>

<script>

function cargarMensajes(){

fetch("/mensajes/<?= $conversacion_id ?>")
.then(res => res.json())
.then(data => {

let html = "";

data.forEach(m => {

let yo = m.remitente_id == <?= session()->get('usuario_id') ?>;

html += "<div class='mensaje "+(yo?"yo":"otro")+"'><span>";

if(m.tipo == "texto"){
html += m.mensaje;
}

if(m.tipo == "imagen"){
html += "<br><img src='/uploads/"+m.archivo+"' width='200'>";
}

if(m.tipo == "video"){
html += "<br><video width='250' controls><source src='/uploads/"+m.archivo+"'></video>";
}

html += "</span></div>";

});

document.getElementById("chat-box").innerHTML = html;

});

}

setInterval(cargarMensajes,2000);

</script>