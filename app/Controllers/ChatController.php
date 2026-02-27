<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ChatController extends BaseController
{
    public function chat($usuarioDestinoId)
    {
        if (!session()->get('logueado')) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        $miId = session()->get('usuario_id');

        // Buscar conversación existente
        $builder = $db->table('conversaciones');
        $conversacion = $builder
            ->groupStart()
                ->where('usuario1_id', $miId)
                ->where('usuario2_id', $usuarioDestinoId)
            ->groupEnd()
            ->orGroupStart()
                ->where('usuario1_id', $usuarioDestinoId)
                ->where('usuario2_id', $miId)
            ->groupEnd()
            ->get()
            ->getRowArray();

        // Si no existe, crearla
        if (!$conversacion) {
            $builder->insert([
                'usuario1_id' => $miId,
                'usuario2_id' => $usuarioDestinoId,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $conversacionId = $db->insertID();
        } else {
            $conversacionId = $conversacion['id'];
        }

        // Obtener mensajes
        $mensajes = $db->table('mensajes')
            ->where('conversacion_id', $conversacionId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        return view('chat/chat', [
            'conversacion_id' => $conversacionId,
            'mensajes' => $mensajes,
            'destino_id' => $usuarioDestinoId
        ]);
    }
    public function enviar()
    {
        $db = \Config\Database::connect();

        $mensaje = $this->request->getPost('mensaje');

        $db->table('mensajes')->insert([
            'conversacion_id' => $this->request->getPost('conversacion_id'),
            'remitente_id' => session()->get('usuario_id'),
            'mensaje' => $mensaje,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/chat/'.$this->request->getPost('destino_id'));
    }
}