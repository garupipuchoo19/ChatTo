<form method="post" action="/reset">
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="password" name="password" placeholder="Nueva contraseña" required>
    <button type="submit">Cambiar contraseña</button>
</form>