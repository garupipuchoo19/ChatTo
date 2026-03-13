<!DOCTYPE html>
<html>
<head>

<title>Chat IA</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
font-family:Segoe UI;
}

.navbar{
background:#111;
}

#chat{
height:420px;
overflow-y:auto;
background:white;
border-radius:10px;
padding:20px;
}

.bubble-user{
background:#ff8c42;
color:white;
padding:10px 15px;
border-radius:15px;
display:inline-block;
max-width:70%;
}

.bubble-ia{
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
Chat IA
</span>

<a href="/usuarios" class="btn btn-sm btn-light">
Volver
</a>

</nav>

<div class="container mt-4" style="max-width:700px">

<div id="chat" class="shadow-sm mb-3"></div>

<div class="input-group">

<input 
type="text"
id="mensaje"
class="form-control"
placeholder="Escribe un mensaje..."
>

<button
id="btnEnviar"
class="btn btn-main"
onclick="enviar()"
>
Enviar
</button>

</div>

</div>

<script>

function enviar(){

let boton = document.getElementById("btnEnviar");
boton.disabled = true;

let mensaje = document.getElementById("mensaje").value;

fetch("/ia/chat",{
method:"POST",
headers:{
"Content-Type":"application/x-www-form-urlencoded"
},
body:"mensaje="+mensaje
})
.then(res=>res.json())
.then(data=>{

let chat = document.getElementById("chat");

chat.innerHTML += `
<div class="text-end mb-3">
<span class="bubble-user">${mensaje}</span>
</div>
`;

chat.innerHTML += `
<div class="text-start mb-3">
<span class="bubble-ia">${data.respuesta}</span>
</div>
`;

document.getElementById("mensaje").value="";

chat.scrollTop = chat.scrollHeight;

boton.disabled = false;

});

}

</script>

</body>
</html>