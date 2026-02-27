<h2>Login</h2>

<?php if(session()->getFlashdata('error')): ?>
    <p style="color:red;">
        <?= session()->getFlashdata('error') ?>
    </p>
<?php endif; ?>

<form method="post" action="/validar-login">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Ingresar</button>
</form>

<a href="/registro">Crear cuenta</a>