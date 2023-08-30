<?php

namespace App\Controllers;


use App\Models\Menu_Model;
use App\Models\Member_Model;
use App\Models\CashierEmployee_Model;
use App\Models\Admin_Model;
use App\Models\Transaction_Model;


class Dashboard extends BaseController
{

    protected $menu_model;
    protected $member_model;
    protected $cashieremployee_model;
    protected $admin_model;

    protected $data;

    public function __construct()
    {
        $this->menu_model = new Menu_Model();
        $this->member_model = new Member_Model();
        $this->cashieremployee_model = new CashierEmployee_Model();
        $this->admin_model = new Admin_Model();
    }

    public function index()
    {
        $this->data['page_title'] = 'Dashboard';
        $this->data['total_menu'] = count($this->menu_model->select('*')->get()->getResult());
        $this->data['total_member'] = count($this->member_model->select('*')->get()->getResult());
        $this->data['total_cashieremployee'] = count($this->cashieremployee_model->select('*')->get()->getResult());
        echo view('templates/header', $this->data);
        echo view('templates/aside', $this->data);
        echo view('dashboard/index', $this->data);
        echo view('templates/footer');
    }
}
