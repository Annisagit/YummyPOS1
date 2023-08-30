<?php

namespace App\Models;

use CodeIgniter\Model;

class Menu_Model extends Model
{

    protected $table = "menu_yummy";

    protected $allowedFields = ['id_menu', 'id_kategori', 'nama_menu', 'deskripsi', 'harga', 'stok', 'gambar_menu'];
}
