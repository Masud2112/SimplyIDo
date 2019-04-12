 <?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calendar extends Admin_controller
{
   
    public function __construct()
    {
        parent::__construct();
        $this->load->model('calendar_model');
    }
 /* Calendar functions */
    public function index()
    {
        $data['title']                = _l('calendar');
        // To load js files
        $data['calendar_assets']      = true;
        $this->load->view('admin/calendar/calendar', $data);
    }

    public function get_calendar_data()
    {      
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->calendar_model->get_calendar_data(
                $this->input->post('start'),
                $this->input->post('end'),
                '',
                '',
                $this->input->post()
            ));
            die();
        }
    }
    function get_calendar_event_data(){
        if ($this->input->is_ajax_request()) {
            $data=$this->input->post();
            $event = $this->calendar_model->get_calendar_event_data($data);
            $result['event']=$event;
            return $this->load->view('admin/calendar/calendar_event_list', $result);
            die();
        }
    }
}