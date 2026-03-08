<?php

namespace App\Controllers;

class IAController extends BaseController
{

    public function index()
    {
        return view('ia/chat');
    }

    public function chat()
    {
        $mensaje = $this->request->getPost('mensaje');

        if (!$mensaje) {
            return $this->response->setJSON([
                "respuesta" => "Mensaje vacío"
            ]);
        }

        $apiKey = env('GEMINI_API_KEY');

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=" . $apiKey;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => $mensaje
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        curl_close($ch);

        $resultado = json_decode($response, true);

        $respuesta = "No hubo respuesta";

        if (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
            $respuesta = $resultado['candidates'][0]['content']['parts'][0]['text'];
        } elseif (isset($resultado['error']['message'])) {
            $respuesta = $resultado['error']['message'];
        }

        return $this->response->setJSON([
            "respuesta" => $respuesta
        ]);
    }
}