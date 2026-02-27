<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuariosController extends BaseController
{
    public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        $usuarioModel = new UsuarioModel();

        $usuarios = $usuarioModel
            ->where('id !=', session()->get('usuario_id'))
            ->findAll();

        return view('usuarios/lista', ['usuarios' => $usuarios]);
    }
}