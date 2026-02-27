<h2>Usuarios disponibles</h2>

<p>Hola <?= session()->get('usuario_nombre') ?></p>

<a href="/logout">Cerrar sesión</a>

<hr>

<?php if(empty($usuarios)): ?>
    <p>No hay usuarios aún</p>
<?php else: ?>
    <ul>
        <?php foreach($usuarios as $u): ?>
            <li>
                <?= esc($u['nombre']) ?>
                <a href="/chat/<?= $u['id'] ?>">Chatear</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>