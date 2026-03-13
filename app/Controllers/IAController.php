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

        // Obtener variables del .env
        $apiKey = getenv('GEMINI_API_KEY');
        $modelo = getenv('GEMINI_MODEL');
        $baseUrl = getenv('GEMINI_URL');

        $url = $baseUrl . "/" . $modelo . ":generateContent";

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
            "Content-Type: application/json",
            "x-goog-api-key: " . $apiKey
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if(curl_errno($ch)){
            curl_close($ch);

            return $this->response->setJSON([
                "respuesta" => "Error de conexión con la IA"
            ]);
        }

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