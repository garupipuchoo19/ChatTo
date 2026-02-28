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
        $encrypter = \Config\Services::encrypter();

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

        // 🔐 DESCIFRAR MENSAJES (ANTES DEL RETURN)
        foreach ($mensajes as &$m) {
            if (!empty($m['mensaje_cifrado'])) {
                try {
                    $m['mensaje'] = $encrypter->decrypt(
                        base64_decode($m['mensaje_cifrado'])
                    );
                } catch (\Exception $e) {
                    $m['mensaje'] = '[No se pudo descifrar]';
                }
            }
        }

        return view('chat/chat', [
            'conversacion_id' => $conversacionId,
            'mensajes' => $mensajes,
            'destino_id' => $usuarioDestinoId
        ]);
    }

    public function enviar()
    {
        $db = \Config\Database::connect();
        $encrypter = \Config\Services::encrypter();

        $mensajePlano = $this->request->getPost('mensaje');

        $mensajeCifrado = base64_encode(
            $encrypter->encrypt($mensajePlano)
        );

        $db->table('mensajes')->insert([
            'conversacion_id' => $this->request->getPost('conversacion_id'),
            'remitente_id' => session()->get('usuario_id'),
            'mensaje_cifrado' => $mensajeCifrado,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/chat/'.$this->request->getPost('destino_id'));
    }
}