<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Cron extends CRM_Controller
{
    public function __construct()
    {
        parent::__construct();
        update_option('cron_has_run_from_cli', 1);
        $this->load->model('cron_model');

        $this->load->model('tasks_model');
        $this->load->model('emails_model');
    }

    public function index()
    {
        $last_cron_run = get_option('last_cron_run');
        if ($last_cron_run == '' || (time() > ($last_cron_run + 300))) {
            do_action('before_cron_run');
            //$this->getcronmeetings();
            $this->cron_model->run();
            do_action('after_cron_run');
        }
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/04/2018
    * to get tasks which are due tomorrow
    */
    public function getcrontasks() {
        $tasks = $this->tasks_model->get_crontasks();

        //send email to each person
        foreach ($tasks as $task) {
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_task_reminder_merge_field($task));
            
            $send         = $this->emails_model->send_email_template('task-reminder', $task['email'], $merge_fields);

            if(!$send) {
                logCronActivity('Reminder email could not be sent to [Email Address:' . $task['email'] . ' for task: ' . $task['name'] . ' Task ID : ' . $task['id'] . ']');
            } else {
                logCronActivity('Reminder email sent to [Email Address:' . $task['email'] . ' for task: ' . $task['name'] . ' Task ID : ' . $task['id'] . ']'); 
            }
        }
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/04/2018
    * to get meetings which are there tomorrow
    */
    public function getcronmeetings() {
        $meetings = $this->meetings_model->get_cronmeetings();

        //send email to each person
        foreach ($meetings as $meeting) {
            
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_meeting_reminder_merge_field($meeting));
           
            $send         = $this->emails_model->send_email_template('meeting-reminder', $meeting['email'], $merge_fields);

            if(!$send) {
                if(!empty($meeting['contact_id'])) {
                    logCronActivity('Reminder email could not be sent to [Email Address:' . $meeting['contactemail'] . ' for meeting: ' . $meeting['name'] . ' Meeting ID : ' . $meeting['meetingid'] . ']');
                } else {
                    logCronActivity('Reminder email could not be sent to [Email Address:' . $meeting['email'] . ' for meeting: ' . $meeting['name'] . ' Meeting ID : ' . $meeting['meetingid'] . ']');
                }
            } else {
                logCronActivity('Reminder email sent to [Email Address:' . $meeting['email'] . ' for meeting: ' . $meeting['name'] . ' Meeting ID : ' . $meeting['meetingid'] . ']'); 
            }
        }
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/05/2018
    * to get events which are there tomorrow
    */
    public function getcronevents() {
        $events = $this->projects_model->get_cronevents();
        
        //send email to each person
        foreach ($events as $event) {
            //get all vendors for event who have accepted invite
            $vendors = $this->projects_model->get_project_invites($event['id'], 3);
            
            if(count($vendors) > 0) {
                //send email to each vendor
                foreach ($vendors as $vendor) {
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_event_reminder_merge_field($event, 'vendors', $vendor));

                    $send         = $this->emails_model->send_email_template('event-reminder', $vendor['email'], $merge_fields);

                    if(!$send) {
                        logCronActivity('Reminder email could not be sent to [Email Address:' . $vendor['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']');
                    } else {
                        logCronActivity('Reminder email sent to [Email Address:' . $vendor['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']'); 
                    }
                }
            }

            //get all collaborators for event who have accepted invite
            $collaborators = $this->projects_model->get_project_invites($event['id'], 4);
            
            if(count($collaborators) > 0) {
                //send email to each collaborator
                foreach ($collaborators as $collaborator) {
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_event_reminder_merge_field($event, 'collaborators', $collaborator));

                    $send         = $this->emails_model->send_email_template('event-reminder', $collaborator['email'], $merge_fields);

                    if(!$send) {
                        logCronActivity('Reminder email could not be sent to [Email Address:' . $collaborator['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']');
                    } else {
                        logCronActivity('Reminder email sent to [Email Address:' . $collaborator['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']'); 
                    }
                }
            }

            //get all venues for event who have accepted invite
            $venues = $this->projects_model->get_project_invites($event['id'], 5);
            
            if(count($venues) > 0) {
                //send email to each collaborator
                foreach ($venues as $venue) {
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_event_reminder_merge_field($event, 'venues', $venue));

                    $send         = $this->emails_model->send_email_template('event-reminder', $venue['venueemail'], $merge_fields);

                    if(!$send) {
                        logCronActivity('Reminder email could not be sent to [Email Address:' . $venue['venueemail'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']');
                    } else {
                        logCronActivity('Reminder email sent to [Email Address:' . $venue['venueemail'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']'); 
                    }
                }
            }
            
            //send email to team member
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_event_reminder_merge_field($event, 'staff'));

            $send         = $this->emails_model->send_email_template('event-reminder', $event['email'], $merge_fields);

            if(!$send) {
                logCronActivity('Reminder email could not be sent to [Email Address:' . $event['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']');
            } else {
                logCronActivity('Reminder email sent to [Email Address:' . $event['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']'); 
            }
        }

        //send email to contact associated for event
        foreach ($events as $event) {
            if($event['contactemail'] != '') {
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_event_reminder_merge_field($event, 'contact'));

                $send         = $this->emails_model->send_email_template('event-reminder', $event['email'], $merge_fields);

                if(!$send) {
                    logCronActivity('Reminder email could not be sent to [Email Address:' . $event['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']');
                } else {
                    logCronActivity('Reminder email sent to [Email Address:' . $event['email'] . ' for event: ' . $event['name'] . ' Event ID : ' . $event['id'] . ']'); 
                }
            }
        }
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/05/2018
    * to get subscriptions which are due tomorrow
    */
    public function getcronsubscriptions() {
        $subscriptions = $this->staff_model->get_cronsubscriptions();

        //send email to each person
        foreach ($subscriptions as $subscription) {
            
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_subscription_reminder_merge_field($subscription));
            
            $send         = $this->emails_model->send_email_template('subscription-reminder', $subscription['email'], $merge_fields);

            if(!$send) {
                logCronActivity('Reminder email could not be sent to [Email Address:' . $subscription['email'] . ' to staff: ' . $subscription['firstname'] . ' Staff ID : ' . $subscription['staffid'] . ']');
            } else {
                logCronActivity('Reminder email sent to [Email Address:' . $subscription['email'] . ' to staff: ' . $subscription['firstname'] . ' Staff ID : ' . $subscription['staffid'] . ']'); 
            }
        }
    }
}
