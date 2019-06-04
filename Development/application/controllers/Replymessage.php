<?php
    /**
    * Added By: Vaidehi
    * Dt: 10/03/2017
    * for handling client/account registration
    */
    defined('BASEPATH') or exit('No direct script access allowed');
    class Replymessage extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            //$this->load->model('Replymessage_model');
        }

        //load sign up form
        public function index()
        {
            $path = $_SERVER['QUERY_STRING'];
            echo $path;exit;
            //echo "<pre>";print_r($_SERVER);exit;
            //localhost/SimplyIDo/Development/replymessage/?bWlkPTM1PSZjaWQ9Mjc=
            // $message_id = $this->input->get('mid');
            // $contact_id = $this->input->get('cid');
            // if(isset($message_id) && $message_id != ""){
            //     $message_id = base64_decode($message_id);
            // }
            // if(isset($contact_id) && $contact_id != ""){
            //     $contact_id = base64_decode($contact_id);
            // }
            // echo base64_encode("mid=35=&cid=27");
            // echo "<br>".($contact_id);exit;
        }
    }
?>