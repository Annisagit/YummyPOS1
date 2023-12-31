<?php

namespace App\Controllers;


use App\Models\CashierEmployee_Model;
use App\Models\Admin_Model;

class CashierEmployee extends BaseController
{

    protected $cashier_employee_model;
    protected $admin_model;

    protected $data;

    public function __construct()
    {
        $this->cashier_employee_model = new CashierEmployee_Model();
        $this->admin_model = new Admin_Model();
    }

    public function index()
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('dashboard');
        }

        $this->data['page_title'] = 'Kasir';

        $this->data['cashiers'] = $this->cashier_employee_model->select('*')->get()->getResult();

        echo view('templates/header', $this->data);
        echo view('templates/aside');
        echo view('cashier/index', $this->data);
        echo view('modals/new_cashier_form');
        echo view('templates/footer');
    }

    public function search_cashier()
    {
        $keyword = $this->request->getPost('keyword');

        $this->data['cashiers'] = $this->cashier_employee_model->select('*')->like('nama_lengkap', $keyword)->orLike('no_telepon', $keyword)->orLike('email', $keyword)->get()->getResult();

        return view('cashier/search_item', $this->data);
    }

    public function detail($cashier_id = "")
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('dashboard');
        }

        $this->data['page_title'] = "Kasir";

        if (empty($cashier_id) || count($this->cashier_employee_model->select("*")->where(['id_kasir' => $cashier_id])->get()->getResult()) == 0) {
            session()->setFlashdata("warning", "ID Kasir tidak diketahui");
            return redirect()->route('kasir');
        }

        $this->data['cashier'] = $this->cashier_employee_model->select("*")->where(['id_kasir' => $cashier_id])->get()->getResult()[0];

        echo view('templates/header', $this->data);
        echo view('templates/aside');
        echo view('cashier/detail', $this->data);
        echo view('modals/edit_cashier_form', $this->data);
        echo view('templates/footer');
    }


    public function save_cashier_employee()
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('dashboard');
        }

        $validationRule = [
            'employee-photo' => [
                'label' => 'Image File',
                'rules' => [
                    'mime_in[employee-photo,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                ],
            ],
        ];

        if (!$this->validate($validationRule)) {
            session()->setFlashdata("warning", "Foto profil kasir tidak sesuai format");
            return redirect()->route('kasir');
        }

        $update_cashier = [
            'username' => $this->request->getPost('employee-username'),
            'nama_lengkap' => $this->request->getPost('employee-full-name'),
            'nik' => $this->request->getPost('employee-nik'),
            'no_telepon' => $this->request->getPost('employee-phone-number'),
            'alamat' => $this->request->getPost('employee-address'),
        ];


        $email = $this->request->getPost('employee-email');

        if (!empty($email)) {
            $email_is_exist = count($this->cashier_employee_model->select('*')->where(['email' => $email])->get()->getResult());
            $email_is_exist_in_admin = count($this->admin_model->select('*')->where(['email' => $email])->get()->getResult());

            if ($email_is_exist > 0 || $email_is_exist_in_admin > 0) {
                return redirect()->route('kasir');
            }

            $update_cashier['email'] = $email;
        }

        $password = $this->request->getPost('employee-password');
        if (!empty($password)) {
            $password = password_hash((string) $password, PASSWORD_DEFAULT);
            $update_cashier['password'] = $password;
        }


        $image_data = $this->request->getFile('employee-photo');
        if (($image_data)->isValid()) {
            $image_data = file_get_contents($image_data->getTempName());
            $update_cashier['foto_profil'] = $image_data;
        }


        $cashier_id = $this->request->getPost('cashier-id');
        if (!empty($cashier_id)) {
            $update = $this->cashier_employee_model->where(['id_kasir' => $cashier_id])->set($update_cashier)->update();

            if ($update) {
                session()->setFlashdata("warning", "Data kasir berhasil diubah");
                return redirect()->to('kasir/detail/' . $cashier_id);
            }
        }

        $insert_cashier = [
            'username' => $this->request->getPost('employee-username'),
            'email' => $this->request->getPost('employee-email'),
            'password' => $password,
            'nama_lengkap' => $this->request->getPost('employee-full-name'),
            'nik' => $this->request->getPost('employee-nik'),
            'no_telepon' => $this->request->getPost('employee-phone-number'),
            'alamat' => $this->request->getPost('employee-address'),
            'foto_profil' => $image_data,
        ];

        $save = $this->cashier_employee_model->insert($insert_cashier);

        if ($save) {
            session()->setFlashdata("success", "Kasir baru berhasil ditambah");
            return redirect()->route('kasir');
        }
    }

    public function delete_cashier_employee($cashier_id = "")
    {
        if (session()->get('role') == "cashier") {
            return redirect()->route('dashboard');
        }

        if (empty($cashier_id) || !$this->cashier_employee_model->where(['id_kasir' => $cashier_id])->first()) {
            session()->setFlashdata("error", "ID Kasir tidak diketahui");
            return redirect()->route('kasir');
        }

        $this->cashier_employee_model->where(['id_kasir' => $cashier_id])->delete();

        session()->setFlashdata("success", "Kasir berhasil dihapus");
        return redirect()->route('kasir');
    }
}
