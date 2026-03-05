<h2>Chat</h2>

<a href="/usuarios">← Volver</a>

<hr>

<div id="chat-box" style="border:1px solid #ccc; padding:10px; height:300px; overflow:auto;"></div>

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

let remitente = (m.remitente_id == <?= session()->get('usuario_id') ?>) ? "Yo" : "Otro";

html += `<p><strong>${remitente}:</strong>`;

if(m.tipo == "texto"){
html += m.mensaje;
}

if(m.tipo == "imagen"){
html += `<br><img src="/uploads/${m.archivo}" width="200">`;
}

if(m.tipo == "video"){
html += `<br><video width="250" controls>
<source src="/uploads/${m.archivo}">
</video>`;
}

html += `</p>`;

});

document.getElementById("chat-box").innerHTML = html;

});

}

setInterval(cargarMensajes,2000);

cargarMensajes();

</script>
