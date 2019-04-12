<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Questionnaire extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        // Model is autoloaded
    }

    /* List all Questionnaire */
    public function index()
    {        
        if (!has_permission('questionnaire', '', 'view', true)) {
            access_denied('questionnaire');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('questionnaire');
        }
        $data['title'] = _l('all_questionnaire');
        $this->load->view('admin/questionnaire/manage', $data);
    }

    /* Add new questionnaire or edit existing one */
    public function questionnaire($id = '')
    {
        if (!has_permission('questionnaire', '', 'view', true)) {
            access_denied('questionnaire');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('questionnaire', '', 'create', true)) {
                    access_denied('questionnaire');
                }
                
                $id = $this->questionnaires_model->addquestionnaires($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('questionnaire')));                    
                    redirect(admin_url('questionnaire'));
                } else {
                    set_alert('danger', _l('problem_questionnaire_adding', _l('questionnaire_lowercase')));
                    redirect(admin_url('questionnaire/questionnaire/' . $id));
                }

            } else {
                if (!has_permission('questionnaire', '', 'edit', true)) {
                    access_denied('questionnaire');
                }
                
                $success = $this->questionnaires_model->updatequestionnaire($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('questionnaire')));
                    redirect(admin_url('questionnaire'));
                } else {
                    set_alert('danger', _l('problem_questionnaire_updating', _l('questionnaire_lowercase')));
                    redirect(admin_url('questionnaire/questionnaire/' . $id));
                }
                redirect(admin_url('questionnaire/questionnaire/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('questionnaire'));
        } else {
            $questionnaire                     = $this->questionnaires_model->getquestionnaire($id);
            
            $data['questionnaire']             = $questionnaire;
            $title                    = _l('edit', _l('questionnaire')) . ' ' . $questionnaire->name;
        }
        $data['title']       = $title;
        $this->load->view('admin/questionnaire/questionnaire', $data);
    }

    /* Delete questionnaire from database */
    public function deletequestionnaire($id)
    {
        if (!has_permission('questionnaire', '', 'delete', true)) {
            access_denied('questionnaire');
        }
        if (!$id) {
            redirect(admin_url('questionnaire'));
        }
        $response = $this->questionnaires_model->deletequestionnaire($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('questionnaire')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('questionnaire_lowercase')));
        }
        //redirect(admin_url('agreements'));
    }

    // Ajax
    /* Remove questionnaire question */
    public function remove_question($questionid)
    {
        if (!has_permission('questionnaire', '', 'edit', true)) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied')
            ));
            die();
        }
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->questionnaires_model->remove_question($questionid),
            ));
        }
    }

    /* Removes questionnaire checkbox/radio description*/
    public function remove_box_description($questionboxdescriptionid)
    {
        if (!has_permission('questionnaire', '', 'edit', true)) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied')
            ));
            die();
        }
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->questionnaires_model->remove_box_description($questionboxdescriptionid),
            ));
        }
    }

    /* Add box description */
    public function add_box_description($questionid, $boxid)
    {
        if (!has_permission('questionnaire', '', 'edit', true)) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied')
            ));
            die();
        }
        if ($this->input->is_ajax_request()) {
            $boxdescriptionid = $this->questionnaires_model->add_box_description($questionid, $boxid);
            echo json_encode(array(
                'boxdescriptionid' => $boxdescriptionid,
            ));
        }
    }

    /* New question */
    public function add_question()
    {
        if (!has_permission('questionnaire', '', 'edit', true)) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied')
            ));
            die();
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                /*echo json_encode(array(
                    'data' => $this->questionnaires_model->add_question($this->input->post()),
                    //'survey_question_only_for_preview' => _l('survey_question_only_for_preview'),
                    'question_required' => _l('survey_question_required'),
                    'question_string' => _l('question_string')
                ));*/
                return $question=$this->questionnaires_model->add_question($this->input->post());
                die();
            }
        }
    }

    /* Update question */
    public function update_question()
    {
        if (!has_permission('questionnaire', '', 'edit', true)) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied')
            ));
            die();
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->questionnaires_model->update_question($this->input->post());
            }
        }
    }

    /* Reorder questionnaire */
    public function update_questions_orders()
    {
        if (has_permission('questionnaire', '', 'edit', true)) {
            if ($this->input->is_ajax_request()) {
                if ($this->input->post()) {
                    $this->questionnaires_model->update_questions_orders($this->input->post());
                }
            }
        }
    }

    /*
        Added by Masud on 03-20-2018 for questionnair view and preview;

    */
    public function viewquestionnaire($id)
    {
        if (!has_permission('questionnaire', '', 'view', true)) {
            access_denied('questionnaire');
        }
        if ($this->input->post()) {
            $data =$this->input->post();
            $file_ary = array();
            $file_count = count($_FILES['answers']['name']);
            $file_keys = array_keys($_FILES['answers']);
            $file_qids= array_keys($_FILES['answers']['name']);
            for ($i=0; $i<$file_count; $i++) {
                $qid = $file_qids[$i];
                if(!empty($_FILES['answers']['name'][$qid]['answer'])){
                    foreach ($file_keys as $key) {
                        $file_ary[$qid]['answer'][$key] = $_FILES['answers'][$key][$qid]['answer'];
                    }
                }
            }
            /*echo"<pre>";
            foreach ($file_ary as $question=>$file){
                $f = $file['answer'];
            }
            print_r($data);
            die('<--here');*/
            if ($id == '') {
                if (!has_permission('questionnaire', '', 'create', true)) {
                    access_denied('questionnaire');
                }

                $id = $this->questionnaires_model->addquestionnaires($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('questionnaire')));
                    redirect(admin_url('questionnaire'));
                } else {
                    set_alert('danger', _l('problem_questionnaire_adding', _l('questionnaire_lowercase')));
                    redirect(admin_url('questionnaire/questionnaire/' . $id));
                }

            } else {
                if (!has_permission('questionnaire', '', 'edit', true)) {
                    access_denied('questionnaire');
                }

                $success = $this->questionnaires_model->updatequestionnaire($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('questionnaire')));
                    redirect(admin_url('questionnaire'));
                } else {
                    set_alert('danger', _l('problem_questionnaire_updating', _l('questionnaire_lowercase')));
                    redirect(admin_url('questionnaire/questionnaire/' . $id));
                }
                redirect(admin_url('questionnaire/questionnaire/' . $id));
            }
        }
        $questionnaire                     = $this->questionnaires_model->getquestionnaire($id);
        $data['questionnaire']             = $questionnaire;
        $title                    = $questionnaire->name;
        $data['title']       = $title;
        $this->load->view('admin/questionnaire/viewquestionnaire/viewquestionnaire', $data);
    }
    function upload_image(){
        $data = $this->input->post();
        $image = $_FILES['file'];
        $path = get_upload_path_by_type('questionnaire') . 'image/'.$data['questionid'].'/';
        $tmpFilePath = $image['tmp_name'];
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($image["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                echo "ext";
                die();
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $image["name"]);
            $data['description'] = $filename;
            $newFilePath = $path . '/' . $filename;
            $oldFilePath = $path . '/' . $data['image'];
            if(!empty($data['image'])){
                unlink($oldFilePath);
            }
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $result = $this->questionnaires_model->upload_image($data);
                if($result > 0){
                    echo 'success#'.$filename;
                    die();
                }
            }
            return false;
        }

    }
    /* Copy question */
    public function copy_question()
    {
        if (!has_permission('questionnaire', '', 'edit', true)) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied')
            ));
            die();
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                return $question=$this->questionnaires_model->copy_question($this->input->post());
                die();
            }
        }
    }
}
