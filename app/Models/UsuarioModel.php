<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombre',
        'email',
        'password',
        'reset_token',
        'reset_expira'
    ];

    protected $useTimestamps = true;
}