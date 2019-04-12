<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setup extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /* View all settings */
    public function index()
    {
        $data = array();
        $brandid = get_user_session();
        $session_data   = get_session_data();
        $is_sido_admin  = $session_data['is_sido_admin'];
        $is_admin  = $session_data['is_admin'];
        $data['brandid'] = $brandid;
        $data['is_sido_admin'] = $is_sido_admin;
        $data['is_admin'] = $is_admin;
        
        $this->load->view('admin/setup/setup', $data);
    }
}