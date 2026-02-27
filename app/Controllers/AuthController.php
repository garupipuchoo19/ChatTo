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

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}