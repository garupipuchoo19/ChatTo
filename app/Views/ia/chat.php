<!DOCTYPE html>
<html>
<head>
<title>Chat IA</title>
</head>
<body>

<h2>Chat con IA</h2>

<div id="chat" style="border:1px solid #ccc;height:300px;overflow:auto;padding:10px"></div>

<input type="text" id="mensaje" placeholder="Escribe algo...">
<button onclick="enviar()">Enviar</button>

<script>

function enviar(){

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

        chat.innerHTML += "<p><b>Tú:</b> "+mensaje+"</p>";
        chat.innerHTML += "<p><b>IA:</b> "+data.respuesta+"</p>";

        document.getElementById("mensaje").value="";

        chat.scrollTop = chat.scrollHeight;

    });

}

</script>

</body>
</html>