<?php

namespace App\Controllers;

class GrupoController extends BaseController
{

    public function index()
    {
        $db = \Config\Database::connect();

        $grupos = $db->table('grupos')->get()->getResultArray();

        return view('grupos/index', [
            'grupos' => $grupos
        ]);
    }

    public function chat($grupo_id)
    {
        $db = \Config\Database::connect();

        $mensajes = $db->table('mensajes')
        ->where('grupo_id', $grupo_id)
        ->orderBy('created_at','ASC')
        ->get()
        ->getResultArray();

        return view('grupos/chat',[
            'mensajes'=>$mensajes,
            'grupo_id'=>$grupo_id
        ]);
    }

    public function enviar()
    {
        $db = \Config\Database::connect();

        $db->table('mensajes')->insert([
            'grupo_id'=>$this->request->getPost('grupo_id'),
            'remitente_id'=>session()->get('usuario_id'),
            'mensaje'=>$this->request->getPost('mensaje')
        ]);

        return redirect()->back();
    }

}
