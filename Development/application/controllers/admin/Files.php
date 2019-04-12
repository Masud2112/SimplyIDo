<?php
defined('BASEPATH') or exit('No direct script access allowed');

@ini_set('memory_limit', '128M');
@ini_set('max_execution_time', 360);

class Files extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('files_model');
    }
   
    // Moved here from version 1.0.5
    public function index()
    {
        if (!has_permission('files', '', 'view', true)) {
            access_denied('files');
        }
        $data['files_assets'] = true;
        $data['title']        = _l('media_files');
        if($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            $data['files']       = $this->leads_model->get_files($leadid);
            if($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }          
        }
        if($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            $data['files']       = $this->projects_model->get_files($projectid);
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }            
        }
        if($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            } 
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }   

            $data['files']       = $this->projects_model->get_event_files($projectid);
            $data['totalfiles']       = $this->projects_model->get_event_files($projectid);
        }

        /**
         * Added By : Masud
         * Dt : 06/28/2018
         * kanban view for Files
         */

        $data['switch_files_kanban'] = true;
        if ($this->session->has_userdata('files_kanban_view') && $this->session->userdata('files_kanban_view') == 'true') {
            $data['switch_files_kanban'] = false;
            $data['bodyclass']     = 'kan-ban-body';
        }
		
		/*if(is_mobile()){
			$this->session->set_userdata(array('files_kanban_view' => 0));
		}*/
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                $data['kanban']= $this->input->get('kanban');
                if($this->input->get('limit')) {
                    $data['limit'] = $this->input->get('limit');
                }
                if($this->input->get('page')) {
                    $data['page'] = $this->input->get('page');
                }
                if($this->input->get('search')) {
                    $data['search'] = $this->input->get('search');
                }
                if($this->input->get('pid')) {
                    $projectid = $this->input->get('pid');
                    $data['files']       = $this->projects_model->get_files($projectid,$this->input->get('limit'),$this->input->get('page'),$this->input->get('kanban'));
                    $data['totalfiles']       = $this->projects_model->get_files($projectid);
                }elseif($this->input->get('lid')) {
                    $leadid = $this->input->get('lid');
                    $data['files']       = $this->leads_model->get_files($leadid,$this->input->get('limit'),$this->input->get('page'),$this->input->get('kanban'));
                    $data['totalfiles']       = $this->leads_model->get_files($leadid);
                }
                echo $this->load->view('admin/files/kan-ban', $data, true);
                die();
            }
        }
        $this->load->view('admin/files/files', $data);
    }

    public function elfinder_init()
    {

        if (!has_permission('files', '', 'create', true)) {
            access_denied('files');
        }

        $files_folder = $this->perfex_base->get_media_folder();
        $filesPath = FCPATH . $files_folder;

        if (!is_dir($filesPath)) {
            mkdir($filesPath);
        }

        if (!file_exists($filesPath . '/index.html')) {
            fopen($filesPath . '/index.html', 'w');
        }

        $this->load->helper('path');

        $root_options = array(
            'driver' => 'LocalFileSystem',
            'path' => set_realpath($files_folder),
            'URL' => site_url($files_folder) . '/',
            //'debug'=>true,
            'uploadMaxSize' => get_option('media_max_file_size_upload') . 'M',
            'accessControl' => 'access_control_files',
            'uploadDeny'=>array(
                'application/x-httpd-php',
                'application/php',
                'application/x-php',
                'text/php',
                'text/x-php',
                'application/x-httpd-php-source',
                'application/perl',
                'application/x-perl',
                'application/x-python',
                'application/python',
                'application/x-bytecode.python',
                'application/x-python-bytecode',
                'application/x-python-code',
                'wwwserver/shellcgi', // CGI
            ),
            'uploadAllow'=>array('image/png','image/jpeg','image/jpg','application/pdf','application/msword','application/excel','application/vnd.ms-excel','application/x-excel','application/x-msexcel','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'uploadOrder' => array(
                'allow',
                'deny'
            ),
            'attributes' => array(
                array(
                    'pattern' => '/.tmb/',
                    'hidden' => true
                ),
                array(
                    'pattern' => '/.quarantine/',
                    'hidden' => true
                )
            )
        );
        if (!is_admin()) {
            $this->db->select('media_path_slug,staffid,firstname,lastname')
            ->from('tblstaff')
            ->where('staffid', get_staff_user_id());
            $user = $this->db->get()->row();
            $path = set_realpath($files_folder . '/' . $user->media_path_slug);
            if (empty($user->media_path_slug)) {
                $this->db->where('staffid', $user->staffid);
                $slug = slug_it($user->firstname . ' ' . $user->lastname);
                $this->db->update('tblstaff', array(
                    'media_path_slug' => $slug
                ));
                $user->media_path_slug = $slug;
                $path                  = set_realpath($files_folder . '/' . $user->media_path_slug);
            }
            if (!is_dir($path)) {
                mkdir($path);
            }
            // if (!file_exists($path . '/index.html')) {
            //     fopen($path . '/index.html', 'w');
            // }
            array_push($root_options['attributes'], array(
                'pattern' => '/.(' . $user->media_path_slug . '+)/', // Prevent deleting/renaming folder
                'read' => true,
                'write' => true,
                'locked' => true
            ));
            $root_options['path'] = $path;
            $root_options['URL']  = site_url($files_folder . '/' . $user->media_path_slug) . '/';
        }

        // $publicRootPath = $files_folder.'/public';
        // $public_root = $root_options;
        // $public_root['path'] = set_realpath($publicRootPath);

        // $public_root['URL'] = site_url($files_folder) . '/public';
        // unset($public_root['attributes'][3]);

        // if (!is_dir($publicRootPath)) {
        //     mkdir($publicRootPath);
        // }

        // if (!file_exists($publicRootPath . '/index.html')) {
        //     fopen($publicRootPath . '/index.html', 'w');
        // }

        $opts = array(
            'roots' => array(
                $root_options,
               // $public_root
            )
        );

        $opts = do_action('before_init_media', $opts);
        $this->load->library('elfinder_lib', $opts);
    }

    /**
     * Added By : Masud
     * Dt : 06/28/2018
     * kanban view for Files
     */

    function switch_files_kanban($set="")
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }

        $this->session->set_userdata(array(
            'files_kanban_view' => $set
        ));

        redirect($_SERVER['HTTP_REFERER']);
    }
}