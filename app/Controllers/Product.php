<?php

namespace App\Controllers;

use App\Models\Menu_Model;
use App\Models\Stock_Model;
use App\Models\Category_Model;
use App\Models\Transaction_Model;
use App\Models\DetailTransaction_Model;

class Product extends BaseController
{

    protected $data;
    protected $menu_model;
    protected $stock_model;
    protected $category_model;
    protected $transaction_model;
    protected $detail_transaction_model;

    public function __construct()
    {
        $this->menu_model = new menu_model();
        $this->stock_model = new Stock_Model();
        $this->category_model = new Category_Model();
        $this->transaction_model = new Transaction_Model();
        $this->detail_transaction_model = new DetailTransaction_Model();
    }

    public function index($transaction_id = 0)
    {
        $transaction = $this->transaction_model->join("member", "member.id_member = transaksi.id_member", 'left')->where(['id_transaksi' => $transaction_id])->first();
        if ($transaction_id != 0 && !$transaction && $transaction['status'] != "tertunda") {
            session()->setFlashdata("error", "ID Transaksi tidak diketahui");
            return redirect()->route('produk');
        }

        // deal with undone transaction
        $this->data['transaction'] = $transaction;

        $this->data['transaction_details'] = $this->detail_transaction_model->select('menu_yummy.id_menu, menu_yummy.nama_menu, stok_menu.id_stok, stok_menu.stok, jumlah_produk, menu_yummy.harga')->join('stok_menu', 'stok_menu.id_stok = detail_transaksi.id_stok')->join('menu_yummy', 'menu_yummy.id_menu = stok_menu.id_menu')->where(['id_transaksi' => $transaction_id])->get()->getResult();

        $this->data['page_title'] = 'Menu | YummyPOS';

        $this->data['products'] = $this->menu_model->select('id_menu, nama_menu, harga, gambar_menu, kategori.nama_kategori')->join('kategori', 'kategori.id_kategori = menu_yummy.id_kategori')->get()->getResult();

        $this->data['product_stock'] = $this->stock_model->select('id_stok, stok_menu.id_menu, stok')
            ->join('menu_yummy', 'stok_menu.id_menu = menu_yummy.id_menu')->get()->getResult();

        $this->data['categories'] = $this->category_model->select('*')->get()->getResult();

        echo view('templates/header', $this->data);
        echo view('templates/aside', $this->data);
        echo view('product/index', $this->data);
        if (session()->get('role') == "cashier") {
            echo view('product/transaction_aside', $this->data);
        }
        echo view('templates/footer');
    }

    public function search_product()
    {
        $category = $this->request->getPost('category');
        if (!empty($category)) {
            $keyword = $category;
        } else {
            $keyword = $this->request->getPost('keyword');
        }

        $view = !empty($this->request->getPost('view')) ?  '_' . $this->request->getPost('view') : '';

        $this->data['products'] = $this->menu_model->select('id_menu, nama_menu, harga, gambar_menu, kategori.nama_kategori')->join('kategori', 'kategori.id_kategori = menu_yummy.id_kategori')->like('nama_menu', $keyword)->orLike('harga', $keyword)->orLike('kategori.nama_kategori', $keyword)->get()->getResult();


        $this->data['product_stock'] = $this->stock_model->select('id_stok, stok_menu.id_menu, stok')
            ->join('menu_yummy', 'stok_menu.id_menu = menu_yummy.id_menu')->get()->getResult();


        if (empty($view)) {
            return view('product/search_item', $this->data);
        }
        return view('product/search_item' . $view, $this->data);
    }

    public function table_list()
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('produk');
        }

        $this->data['page_title'] = 'Table Menu | YummyPOS';

        $this->data['products'] = $this->menu_model->select('*')->join('kategori', 'kategori.id_kategori = menu_yummy.id_kategori')->get()->getResult();

        $this->data['product_stock'] = $this->stock_model->select('id_stok, stok_menu.id_menu, stok')
            ->join('menu_yummy', 'stok_menu.id_menu = menu_yummy.id_menu')->get()->getResult();

        $this->data['categories'] = $this->category_model->select('*')->get()->getResult();

        echo view('templates/header', $this->data);
        echo view('templates/aside', $this->data);
        echo view('modals/new_product_form');
        echo view('product/table_list', $this->data);
        echo view('templates/footer');
    }

    public function detail($product_id = '')
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('produk');
        }

        $this->data['page_title'] = 'Menu | YummyPOS';

        $this->data['product'] = $this->menu_model->select('*')->join('kategori', 'kategori.id_kategori = menu_yummy.id_kategori')->where(['id_menu' => $product_id])->get()->getResult();

        if (empty($product_id) || empty($this->data['product'])) {
            session()->setFlashdata("error", "ID Produk tidak diketahui");
            return redirect()->route('produk/list_tabel');
        }

        $this->data['product'] = $this->data['product'][0];

        $this->data['product_stock'] = $this->stock_model->select('id_stok, stok_menu.id_menu, stok')
            ->join('menu_yummy', 'stok_menu.id_menu = menu_yummy.id_menu')->where(['stok_menu.id_menu' => $product_id])->get()->getResult();

        $this->data['categories'] = $this->category_model->select('*')->get()->getResult();


        echo view('templates/header', $this->data);
        echo view('templates/aside', $this->data);
        echo view('product/detail', $this->data);
        echo view('modals/edit_product_form', $this->data);
        echo view('templates/footer');
    }

    public function save_product()
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('produk');
        }

        $validationRule = [
            'product-img' => [
                'label' => 'Image File',
                'rules' => [
                    'mime_in[product-img,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                ],
            ],
        ];

        if (!$this->validate($validationRule)) {
            session()->setFlashdata("error", "Gambar produk tidak sesuai format");
            return redirect()->route('produk');
        }

        $update_product = [
            'id_kategori' => $this->request->getPost('product-category'),
            'nama_menu' => $this->request->getPost('product-name'),
            'harga' => $this->request->getPost('product-price'),
            'deskripsi' => $this->request->getPost('product-desc'),
        ];

        $image_data = $this->request->getFile('product-img');
        if (($image_data)->isValid()) {
            $image_data = file_get_contents($image_data->getTempName());
            $update_product['gambar_menu'] = $image_data;
        }

        $insert_product = [
            'id_kategori' => $this->request->getPost('product-category'),
            'nama_menu' => $this->request->getPost('product-name'),
            'harga' => $this->request->getPost('product-price'),
            'deskripsi' => $this->request->getPost('product-desc'),
            'gambar_menu' => $image_data
        ];

        $product_id = $this->request->getPost('product-id');
        if (!empty($product_id)) {
            $update = $this->menu_model->where(['id_menu' => $product_id])->set($update_product)->update();

            if ($update) {
                session()->setFlashdata("warning", "Data produk berhasil diubah");
                return redirect()->to("produk/detail/" . $product_id);
            }
        }

        $save_product = $this->menu_model->insert($insert_product);

        $product_id = $this->menu_model->getInsertID();

        $insert_product_stock = [
            'id_menu' => $product_id,
            'stok' => $this->request->getPost('product-stock'),
        ];

        $save_product_stock = $this->stock_model->insert($insert_product_stock);

        if ($save_product && $save_product_stock) {
            session()->setFlashdata("success", "Produk baru berhasil ditambah");
            return redirect()->route('produk/list_tabel');
        }
    }

    function delete_product($product_id = "")
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('produk');
        }

        if (empty($product_id) || !$this->menu_model->where(['id_menu' => $product_id])->first()) {
            session()->setFlashdata("error", "ID Produk tidak diketahui");
            return redirect()->route('produk');
        }

        $this->menu_model->where(['id_menu' => $product_id])->delete();

        session()->setFlashdata("success", "Produk berhasil dihapus");
        return redirect()->route('produk/list_tabel');
    }

    function new_product_stock()
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('produk');
        }

        $insert_product_stock = [
            'id_menu' => $this->request->getPost('product-id'),
            'stok' => $this->request->getPost('product-stock'),
        ];

        $save_product_stock = $this->stock_model->insert($insert_product_stock);

        if ($save_product_stock) {
            session()->setFlashdata("success", "Stok baru berhasil ditambahkan");
            return redirect()->to('produk/detail/' . $insert_product_stock['id_menu']);
        }
    }

    function save_product_stock()
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('produk');
        }

        $product_id = $this->request->getPost('product-id');

        $product_stock = $this->stock_model->select("*")->where(['id_menu' => $product_id])->get()->getResult();

        $stock_id = [];
        for ($i = 0; $i < count($product_stock); $i++) {
            array_push($stock_id, $this->request->getPost($i . '-stock-id'));
        }

        $stock_value = [];
        foreach ($stock_id as $id) {
            $stock_value[$id] = $this->request->getPost($id . '-product-stock');
        }

        foreach ($stock_id as $id) {
            if ($stock_value[$id] > 0) {
                $this->stock_model->where(['id_stok' => $id])->set(['stok' => $stock_value[$id]])->update();
            } else {
                $this->stock_model->where(['id_stok' => $id])->delete();
            }
        }

        session()->setFlashdata("warning", "Stok produk berhasil diubah");
        return redirect()->to('/produk/detail/' . $product_id);
    }
}
