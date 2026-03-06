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

        $apiKey = getenv('OPENAI_API_KEY');

        $client = \Config\Services::curlrequest();

        $response = $client->post(
            'https://api.openai.com/v1/chat/completions',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => 'gpt-4.1-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $mensaje
                        ]
                    ]
                ]
            ]
        );

        $data = json_decode($response->getBody(), true);

        return $this->response->setJSON([
            'respuesta' => $data['choices'][0]['message']['content']
        ]);
    }
}