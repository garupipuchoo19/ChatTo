<h2>Usuarios disponibles</h2>

<style>

body{
font-family:Arial;
background:#f4f6f9;
}

.container{
width:400px;
margin:auto;
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 0 10px rgba(0,0,0,0.1);
}

.usuario{
display:flex;
justify-content:space-between;
align-items:center;
padding:10px;
border-bottom:1px solid #eee;
}

button{
background:#2196F3;
color:white;
border:none;
padding:6px 12px;
border-radius:5px;
cursor:pointer;
}

button:hover{
background:#1976D2;
}

.top{
display:flex;
justify-content:space-between;
margin-bottom:10px;
}

</style>

<div class="container">

<div class="top">
<p>Hola <b><?= session()->get('usuario_nombre') ?></b></p>
<a href="/logout">Cerrar sesión</a>
</div>

<hr>

<a href="/ia">
<button>Chat IA</button>
</a>

<br><br>

<?php if(empty($usuarios)): ?>

<p>No hay usuarios aún</p>

<?php else: ?>

<?php foreach($usuarios as $u): ?>

<div class="usuario">

<span><?= esc($u['nombre']) ?></span>

<a href="/chat/<?= $u['id'] ?>">
<button>Chatear</button>
</a>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>