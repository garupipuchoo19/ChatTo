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

    $mensaje = $this->request->getPost('mensaje');
    $archivo = $this->request->getFile('archivo');

    $tipo = 'texto';
    $nombreArchivo = null;

    if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {

        $extension = $archivo->getExtension();

        $nombreArchivo = $archivo->getRandomName();
        $archivo->move('uploads', $nombreArchivo);

        if (in_array($extension,['jpg','jpeg','png','gif'])) {
            $tipo = 'imagen';
        }

        if (in_array($extension,['mp4','mov','webm'])) {
            $tipo = 'video';
        }
    }

    $mensajeCifrado = null;

    if($mensaje){
        $mensajeCifrado = base64_encode($encrypter->encrypt($mensaje));
    }

    $db->table('mensajes')->insert([

        'conversacion_id' => $this->request->getPost('conversacion_id'),
        'remitente_id' => session()->get('usuario_id'),
        'mensaje_cifrado' => $mensajeCifrado,
        'archivo' => $nombreArchivo,
        'tipo' => $tipo,
        'created_at' => date('Y-m-d H:i:s')

    ]);

    return redirect()->to('/chat/'.$this->request->getPost('destino_id'));
}

public function obtenerMensajes($conversacionId)
{
    $db = \Config\Database::connect();
    $encrypter = \Config\Services::encrypter();

    $mensajes = $db->table('mensajes')
        ->where('conversacion_id', $conversacionId)
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();

    foreach ($mensajes as &$m) {
        if (!empty($m['mensaje_cifrado'])) {
            try {
                $m['mensaje'] = $encrypter->decrypt(base64_decode($m['mensaje_cifrado']));
            } catch (\Exception $e) {
                $m['mensaje'] = '[Error]';
            }
        }
    }

    return $this->response->setJSON($mensajes);
}


}