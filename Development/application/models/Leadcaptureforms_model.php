<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LeadCaptureForms_model extends CRM_Model
{
    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get form and all questions by id
     * @param mixed $id form id
     * @return object
     */
    public function getform($id = '')
    {
        $this->db->where('id', $id);
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        $form = $this->db->get('tblleadcaptureforms')->row();

        if (!$form) {
            return false;
        }

        $this->db->where('form_id', $form->id);
        $this->db->order_by('question_order', 'asc');
        $questions = $this->db->get('tblformquestions')->result_array();
        $i = 0;
        foreach ($questions as $question) {
            $this->db->where('questionid', $question['questionid']);
            $box = $this->db->get('tblformquestionboxes')->row();
            $questions[$i]['boxid'] = $box->boxid;
            $questions[$i]['boxtype'] = $box->boxtype;
            if ($box->boxtype == 'checkbox' || $box->boxtype == 'radio' || $box->boxtype == 'select' || $box->boxtype == 'heading' || $box->boxtype == 'image') {
                $this->db->order_by('questionboxdescriptionid', 'asc');
                $this->db->where('boxid', $box->boxid);
                $boxes_description = $this->db->get('tblformquestionboxesdescription')->result_array();
                if (count($boxes_description) > 0) {
                    $questions[$i]['box_descriptions'] = array();
                    foreach ($boxes_description as $box_description) {
                        $questions[$i]['box_descriptions'][] = $box_description;
                    }
                }
            }
            $i++;
        }
        $form->questions = $questions;

        return $form;
    }

    /**
     * Add new form
     * @param mixed $data
     */
    public function addform($data)
    {
        $form = $this->check_form_name_exists($data['name'], '');
        if ($form->id <= 0 || empty($form)) {
            $data['brandid'] = get_user_session();
            $data['createdby'] = $this->session->userdata['staff_user_id'];
            $data['createddate'] = date('Y-m-d H:i:s');

            $this->db->insert('tblleadcaptureforms', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {

                logActivity('New form Template Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

                return $insert_id;
            }
        }
        return false;
    }

    /**
     * Update form
     * @param array $data form data
     * @param mixed $id form id
     * @return boolean
     */
    public function updateform($data, $id)
    {
        $form = $this->check_form_name_exists($data['name'], $id);

        if (empty($form)) {
            $affectedRows = 0;

            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['updateddate'] = date('Y-m-d H:i:s');
            $this->db->where('id', $id);
            $this->db->update('tblformtemplate', array(
                'name' => $data['name'],
                'updatedby' => $data['updatedby'],
                'updateddate' => $data['updateddate']
            ));
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            if ($affectedRows > 0) {
                logActivity('form Template Updated [ID: ' . $id . '.' . $data['name'] . ']');

                return true;
            }
        }
        return false;
    }

    /**
     * Delete form
     * @param mixed $id form id
     * @return mixed
     */
    public function deleteform($id)
    {
        $affectedRows = 0;
        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblformtemplate', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('form Template Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }

    /**
     * Get form id
     * @param mixed $id form id
     * @return mixed if id passed return object else array
     */
    public function check_form_name_exists($name, $id)
    {
        $brandid = get_user_session();
        if ($id > 0) {
            $where = array('id !=' => $id, 'name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        } else {
            $where = array('name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        }
        return $this->db->where($where)->get('tblleadcaptureforms')->row();
    }

    /**
     * Remove question
     * @param mixed $questionid questionid
     * @return boolean
     */
    public function remove_field($questionid)
    {
        $affectedRows = 0;
        $this->db->where('questionid', $questionid);
        $this->db->delete('tblformquestionboxesdescription');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        $this->db->where('questionid', $questionid);
        $this->db->delete('tblformquestionboxes');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        $this->db->where('questionid', $questionid);
        $this->db->delete('tblformquestions');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            logActivity('form Question Deleted [' . $questionid . ']');

            return true;
        }

        return false;
    }

    /**
     * Remove question box description / radio/checkbox
     * @param mixed $questionboxdescriptionid question box description id
     * @return boolean
     */
    public function remove_box_description($questionboxdescriptionid)
    {
        $this->db->where('questionboxdescriptionid', $questionboxdescriptionid);
        $this->db->delete('tblformquestionboxesdescription');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add form box description radio/checkbox
     * @param mixed $questionid question id
     * @param mixed $boxid main box id
     * @param string $description box question
     */
    public function add_box_description($questionid, $boxid, $description = '')
    {
        $this->db->insert('tblformquestionboxesdescription', array(
            'questionid' => $questionid,
            'boxid' => $boxid,
            'description' => $description
        ));

        return $this->db->insert_id();
    }

    /**
     * Private function for insert question
     * @param mixed $id
     * @param string $question question
     * @return mixed
     */
    private function insert_question($id, $question = '')
    {
        $this->db->insert('tblformquestions', array(
            'form_id' => $id,
            'question' => $question
        ));
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New form Question Added [ID: ' . $id . ']');
        }

        return $insert_id;
    }

    /**
     * Add new question type
     * @param string $type checkbox/textarea/radio/input
     * @param mixed $questionid question id
     * @return mixed
     */
    private function insert_question_type($type, $questionid)
    {
        $this->db->insert('tblformquestionboxes', array(
            'boxtype' => $type,
            'questionid' => $questionid
        ));

        return $this->db->insert_id();
    }

    /**
     * Add new question / ajax
     * @param array $data $_POST question data
     */
    public function add_question($data)
    {

        $questionid = $this->insert_question($data['id']);
        if ($questionid) {
            $boxid = $this->insert_question_type($data['type'], $questionid);
            $response = array(
                'questionid' => $questionid,
                'boxid' => $boxid
            );
            if ($data['type'] == 'checkbox' or $data['type'] == 'radio' or $data['type'] == 'select' or $data['type'] == 'heading' or $data['type'] == 'image') {
                $description = "";
                if ($data['type'] == 'heading') {
                    $description = "h1";
                }
                $questionboxdescriptionid = $this->add_box_description($questionid, $boxid, $description);
                array_push($response, array(
                    'questionboxdescriptionid' => $questionboxdescriptionid
                ));
            }
            $questions = $this->get_question($questionid);
            $qdata['field'] = $questions;
            $qdata['qindex'] = $data['qindex'];
            return $this->load->view('admin/leadcaptureforms/field', $qdata);
        } else {
            return false;
        }
    }

    /**
     * Update question / ajax
     * @param array $data $_POST question data
     * @return boolean
     */
    public function update_question($data)
    {
        $_required = 1;
        if ($data['question']['required'] == 'false') {
            $_required = 0;
        }
        $affectedRows = 0;
        $this->db->where('questionid', $data['questionid']);
        $this->db->update('tblformquestions', array(
            'question' => $data['question']['value'],
            'required' => $_required
        ));
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if (isset($data['boxes_description'])) {
            foreach ($data['boxes_description'] as $box_description) {
                $this->db->where('questionboxdescriptionid', $box_description[0]);
                $this->db->update('tblformquestionboxesdescription', array(
                    'description' => $box_description[1]
                ));
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
        }
        if ($affectedRows > 0) {
            logActivity('form Question Updated [QuestionID: ' . $data['questionid'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Reorder quesions / ajax
     * @param mixed $data order and question id
     */
    public function update_questions_orders($data)
    {
        foreach ($data['data'] as $question) {
            $this->db->where('questionid', $question[0]);
            $this->db->update('tblformquestions', array(
                'question_order' => $question[1]
            ));
        }
    }

    /**
     * Get quesion box id
     * @param mixed $questionid questionid
     * @return integer
     */
    private function get_question_box_id($questionid)
    {
        $this->db->select('boxid');
        $this->db->from('tblformquestionboxes');
        $this->db->where('questionid', $questionid);
        $box = $this->db->get()->row();

        return $box->boxid;
    }

    function upload_image($data)
    {
        $affectedRows = 0;
        $this->db->where('questionboxdescriptionid', $data['desc_id']);
        $this->db->update('tblformquestionboxesdescription', array(
            'description' => $data['description']
        ));
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        return $affectedRows;
    }

    function get_question($questionid)
    {
        $this->db->where('questionid', $questionid);
        $questions = (array)$this->db->get('tblformquestions')->row();
        $this->db->where('questionid', $questions['questionid']);
        $box = $this->db->get('tblformquestionboxes')->row();
        $questions['boxid'] = $box->boxid;
        $questions['boxtype'] = $box->boxtype;
        if ($box->boxtype == 'checkbox' || $box->boxtype == 'radio' || $box->boxtype == 'select' || $box->boxtype == 'heading' || $box->boxtype == 'image') {
            $this->db->order_by('questionboxdescriptionid', 'asc');
            $this->db->where('boxid', $box->boxid);
            $boxes_description = $this->db->get('tblformquestionboxesdescription')->result_array();
            if (count($boxes_description) > 0) {
                $questions['box_descriptions'] = array();
                foreach ($boxes_description as $box_description) {
                    $questions['box_descriptions'][] = $box_description;
                }
            }
        }
        return $questions;
    }

    function copy_question($data)
    {
        $questionid = $data['id'];
        $questions = $this->get_question($questionid);
        $qdata['question'] = $questions;
        /*
                echo"<pre>";
                print_r($questions);
                die('<--here');*/
    }

    /**
     * Added By : Masud
     * Dt : 06/24/2019
     * get all forms
     */
    function get_forms($limit = "", $page = "", $is_kanban = false, $search = "")
    {
        $this->db->select('*');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        if (!empty($search)) {
            $this->db->like('name', $search);
        }
        $this->db->order_by('id', 'desc');
        if ($is_kanban == true && $limit > 0) {
            $start = ($page - 1) * $limit;
            $this->db->limit($limit, $start);
        }
        $FinalResujlt = $this->db->get('tblleadcaptureforms')->result_array();
        return $FinalResujlt;
    }
}