<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    public function registro()
    {
        return view('auth/registro');
    }

    public function guardarRegistro()
    {
        $usuarioModel = new UsuarioModel();

        $passwordHash = password_hash(
            $this->request->getPost('password'),
            PASSWORD_BCRYPT
        );

        $usuarioModel->insert([
            'nombre' => $this->request->getPost('nombre'),
            'email' => $this->request->getPost('email'),
            'password' => $passwordHash
        ]);

        return redirect()->to('/login');
    }

    public function login()
    {
        return view('auth/login');
    }

    public function validarLogin()
    {
        $usuarioModel = new UsuarioModel();

        $usuario = $usuarioModel
            ->where('email', $this->request->getPost('email'))
            ->first();

        if ($usuario && password_verify(
            $this->request->getPost('password'),
            $usuario['password']
        )) {
            session()->set([
                'usuario_id' => $usuario['id'],
                'usuario_nombre' => $usuario['nombre'],
                'logueado' => true
            ]);

            return redirect()->to('/usuarios');
        }

        return redirect()->back()->with('error', 'Credenciales incorrectas');
    }

    public function sendReset()
    {
        $email = $this->request->getPost('email');

        $db = \Config\Database::connect();
        $user = $db->table('usuarios')->where('email', $email)->get()->getRowArray();

        if (!$user) {
            return "Si el email existe, se enviará un enlace de recuperación";
        }

        // token seguro
        $token = bin2hex(random_bytes(32));

        $db->table('usuarios')->where('id',$user['id'])->update([
            'reset_token' => $token,
            'reset_expira' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ]);

        // link
        $link = base_url('/reset/'.$token);

        // servicio email
        $emailService = \Config\Services::email();

        $emailService->setTo($email);
        $emailService->setSubject('Recuperar contraseña');

        $emailService->setMessage("
        <h3>Recuperar contraseña</h3>
        <p>Haz clic en el enlace:</p>
        <a href='$link'>$link</a>
        ");

        // enviar
        if (!$emailService->send()) {
            echo $emailService->printDebugger(['headers']);
            exit;
        }

        return "Si el email existe, se enviará un enlace de recuperación";
    }

    public function resetForm($token)
    {
        $db = \Config\Database::connect();

        $user = $db->table('usuarios')
            ->where('reset_token',$token)
            ->where('reset_expira >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$user) {
            return "Token inválido o expirado";
        }

        return view('auth/reset', ['token'=>$token]);
    }

    public function resetPassword()
    {
        $token = $this->request->getPost('token');
        $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        $db = \Config\Database::connect();

        $user = $db->table('usuarios')
            ->where('reset_token',$token)
            ->where('reset_expira >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$user) {
            return "Token inválido o expirado";
        }

        $db->table('usuarios')->where('id',$user['id'])->update([
            'password'=>$password,
            'reset_token'=>null,
            'reset_expira'=>null
        ]);

        return redirect()->to('/login')->with('success','Contraseña actualizada');
    }
    public function forgotForm()
    {
        return view('auth/forgot');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}