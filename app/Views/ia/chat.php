<!DOCTYPE html>
<html>
<head>
<title>Chat IA</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
display:flex;
flex-direction:column;
align-items:center;
}

h2{
margin-top:20px;
}

#chat{
width:500px;
height:400px;
border-radius:10px;
background:white;
padding:15px;
overflow:auto;
box-shadow:0 0 10px rgba(0,0,0,0.1);
margin-bottom:10px;
}

.mensaje{
margin:8px 0;
}

.user{
text-align:right;
}

.user span{
background:#4CAF50;
color:white;
padding:8px 12px;
border-radius:15px;
display:inline-block;
}

.ia span{
background:#e4e6eb;
padding:8px 12px;
border-radius:15px;
display:inline-block;
}

.input-box{
display:flex;
width:500px;
}

#mensaje{
flex:1;
padding:10px;
border:1px solid #ccc;
border-radius:5px;
}

button{
margin-left:5px;
padding:10px 15px;
border:none;
background:#2196F3;
color:white;
border-radius:5px;
cursor:pointer;
}

button:hover{
background:#1976D2;
}

</style>

</head>
<body>

<h2>Chat con IA</h2>

<div id="chat"></div>

<div class="input-box">
<input type="text" id="mensaje" placeholder="Escribe algo...">
<button id="btnEnviar" onclick="enviar()">Enviar</button>
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

chat.innerHTML += "<div class='mensaje user'><span>"+mensaje+"</span></div>";

chat.innerHTML += "<div class='mensaje ia'><span>"+data.respuesta+"</span></div>";

document.getElementById("mensaje").value="";

chat.scrollTop = chat.scrollHeight;

boton.disabled = false;

});

}

</script>

</body>
</html>