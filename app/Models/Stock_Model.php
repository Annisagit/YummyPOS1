<?php

namespace App\Models;

use CodeIgniter\Model;

class Stock_Model extends Model
{

    protected $table = "stok_menu";

    protected $allowedFields = ['id_stok', 'id_menu', 'stok'];
}
