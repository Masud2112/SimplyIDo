<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Handles uploads error with translation texts
 * @param  mixed $error type of error
 * @return mixed
 */
function _perfex_upload_error($error)
{
    $phpFileUploadErrors = array(
        0 => _l('file_uploaded_success'),
        1 => _l('file_exceeds_max_filesize'),
        2 => _l('file_exceeds_maxfile_size_in_form'),
        3 => _l('file_uploaded_partially'),
        4 => _l('file_not_uploaded'),
        6 => _l('file_missing_temporary_folder'),
        7 => _l('file_failed_to_write_to_disk'),
        8 => _l('file_php_extension_blocked'),
    );

    if (isset($phpFileUploadErrors[$error]) && $error != 0) {
        return $phpFileUploadErrors[$error];
    }

    return false;
}

/**
 * Newsfeed post attachments
 * @param  mixed $postid Post ID to add attachments
 * @return array  - Result values
 */
function handle_newsfeed_post_attachments($postid)
{
    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = get_upload_path_by_type('newsfeed') . $postid . '/';
    $CI =& get_instance();
    if (isset($_FILES['file']['name'])) {
        do_action('before_upload_newsfeed_attachment', $postid);
        $uploaded_files = false;
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $file_uploaded = true;
                $attachment = array();
                $attachment[] = array(
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                );
                $CI->misc_model->add_attachment_to_database($postid, 'newsfeed_post', $attachment);
            }
        }
        if ($file_uploaded == true) {
            echo json_encode(array(
                'success' => true,
                'postid' => $postid
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'postid' => $postid
            ));
        }
    }
}

/**
 * Handles upload for project files
 * @param  mixed $project_id project id
 * @return boolean
 */
function handle_project_file_uploads($project_id)
{
    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        do_action('before_upload_project_attachment', $project_id);
        $path = get_upload_path_by_type('project') . $project_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                if (is_client_logged_in()) {
                    $contact_id = get_contact_user_id();
                    $staffid = 0;
                } else {
                    $staffid = get_staff_user_id();
                    $contact_id = 0;
                }
                $data = array(
                    'project_id' => $project_id,
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                    'dateadded' => date('Y-m-d H:i:s'),
                    'staffid' => $staffid,
                    'contact_id' => $contact_id,
                    'subject' => $filename,
                );
                if (is_client_logged_in()) {
                    $data['visible_to_customer'] = 1;
                } else {
                    $data['visible_to_customer'] = ($CI->input->post('visible_to_customer') == 'true' ? 1 : 0);
                }
                $CI->db->insert('tblprojectfiles', $data);

                $insert_id = $CI->db->insert_id();
                if ($insert_id) {
                    $CI->load->model('projects_model');
                    $CI->projects_model->new_project_file_notification($insert_id, $project_id);

                    if (is_image($newFilePath)) {
                        create_img_thumb($path, $filename);
                    }

                } else {
                    unlink($newFilePath);

                    return false;
                }


                return true;
            }
        }
    }

    return false;
}

/**
 * Handle contract attachments if any
 * @param  mixed $contractid
 * @return boolean
 */
function handle_contract_attachment($id)
{
    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        do_action('before_upload_contract_attachment', $id);
        $path = get_upload_path_by_type('contract') . $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $attachment = array();
                $attachment[] = array(
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                );
                $CI->misc_model->add_attachment_to_database($id, 'contract', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * Handle lead attachments if any
 * @param  mixed $leadid
 * @return boolean
 */
function handle_lead_attachments($leadid, $index_name = 'file', $form_activity = false)
{
    if (isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name']) && $form_activity) {
        return;
    }

    if (isset($_FILES[$index_name]) && _perfex_upload_error($_FILES[$index_name]['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES[$index_name]['error']);
        die;
    }
    $CI =& get_instance();
    if (isset($_FILES[$index_name]['name']) && $_FILES[$index_name]['name'] != '') {
        do_action('before_upload_lead_attachment', $leadid);
        $path = get_upload_path_by_type('lead') . $leadid . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES[$index_name]['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            if (!_upload_extension_allowed($_FILES[$index_name]["name"])) {
                header('HTTP/1.0 400 Bad error');
                echo "File type not supported!";
                die;
            }

            _maybe_create_upload_path($path);

            $filename = unique_filename($path, $_FILES[$index_name]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->model('leads_model');
                $data = array();
                $data[] = array(
                    'file_name' => $filename,
                    'filetype' => $_FILES[$index_name]["type"],
                );
                $CI->leads_model->add_attachment_to_database($leadid, $data, false, $form_activity);

                return true;
            }
        }
    }

    return false;
}

/**
 * Handle lead attachments if any
 * @param  mixed $leadid
 * @return boolean
 */
function handle_lead_existing_attachments($leadid, $index_name = 'file', $form_activity = false)
{
    $CI =& get_instance();
    if (isset($_POST['file_path']) && $_POST['file_path'] != '') {
        $file_path = $_POST['file_path'];
        $path = get_upload_path_by_type('lead') . $leadid . '/';
        $choosepath = get_upload_path_by_type('file');
        $exist_file_name = substr($file_path, strrpos($file_path, '/') + 1);
        if (ENVIRONMENT == 'development') {
            $exist_file_name = substr($file_path, strrpos($file_path, '\\') + 1);
        }
        //$final_path = substr($file_path, strpos($file_path, "/") + 1);  
        if (!_upload_extension_allowed($exist_file_name)) {
            echo "File type not supported!";
            exit;
        }
        _maybe_create_upload_path($path);
        $imagePath = $choosepath . $file_path;
        $file_type = "";
        $newPath = $path;
        $filename = unique_filename($path, $exist_file_name);
        $newFilePath = $path . $filename;

        $copied = copy($imagePath, $newFilePath);
        if ((!$copied)) {
            echo "File not uploaded!";
            exit;
        } else {
            $CI =& get_instance();
            $CI->load->model('leads_model');
            $data = array();
            $data[] = array(
                'file_name' => $filename,
                'filetype' => $file_type
            );
            $CI->leads_model->add_attachment_to_database($leadid, $data, false, $form_activity);
            echo "success";
            exit;
        }

    }

    return false;
}

/**
 * Handle project attachments if any
 * @param  mixed $projectid
 * @return boolean
 */
function handle_project_attachments($projectid, $index_name = 'file', $form_activity = false)
{
    if (isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name']) && $form_activity) {
        return;
    }

    if (isset($_FILES[$index_name]) && _perfex_upload_error($_FILES[$index_name]['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES[$index_name]['error']);
        die;
    }
    $CI =& get_instance();
    if (isset($_FILES[$index_name]['name']) && $_FILES[$index_name]['name'] != '') {
        do_action('before_upload_project_attachment', $projectid);
        $path = get_upload_path_by_type('project') . $projectid . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES[$index_name]['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            if (!_upload_extension_allowed($_FILES[$index_name]["name"])) {
                header('HTTP/1.0 400 Bad error');
                echo "File type not supported!";
                die;
            }

            _maybe_create_upload_path($path);

            $filename = unique_filename($path, $_FILES[$index_name]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->model('projects_model');
                $data = array();
                $data[] = array(
                    'file_name' => $filename,
                    'filetype' => $_FILES[$index_name]["type"],
                );
                $CI->projects_model->add_attachment_to_database($projectid, $data, false, $form_activity);

                return true;
            }
        }
    }

    return false;
}

function handle_event_attachments($eventid, $index_name = 'file', $form_activity = false)
{
    if (isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name']) && $form_activity) {
        return;
    }

    if (isset($_FILES[$index_name]) && _perfex_upload_error($_FILES[$index_name]['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES[$index_name]['error']);
        die;
    }
    $CI =& get_instance();
    if (isset($_FILES[$index_name]['name']) && $_FILES[$index_name]['name'] != '') {
        do_action('before_upload_project_attachment', $eventid);
        $path = get_upload_path_by_type('project') . $eventid . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES[$index_name]['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            if (!_upload_extension_allowed($_FILES[$index_name]["name"])) {
                header('HTTP/1.0 400 Bad error');
                echo "File type not supported!";
                die;
            }

            _maybe_create_upload_path($path);

            $filename = unique_filename($path, $_FILES[$index_name]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->model('projects_model');
                $data = array();
                $data[] = array(
                    'file_name' => $filename,
                    'filetype' => $_FILES[$index_name]["type"],
                );
                $CI->projects_model->add_attachment_to_database($eventid, $data, false, $form_activity, "event");

                return true;
            }
        }
    }

    return false;
}

/**
 * Handle project attachments if any
 * @param  mixed $projectid
 * @return boolean
 */
function handle_project_existing_attachments($projectid, $index_name = 'file', $form_activity = false)
{
    $CI =& get_instance();
    if (isset($_POST['file_path']) && $_POST['file_path'] != '') {
        $file_path = $_POST['file_path'];
        $path = get_upload_path_by_type('project') . $projectid . '/';
        $choosepath = get_upload_path_by_type('file');
        $exist_file_name = substr($file_path, strrpos($file_path, '/') + 1);
        if (ENVIRONMENT == 'development') {
            $exist_file_name = substr($file_path, strrpos($file_path, '\\') + 1);
        }
        //$final_path = substr($file_path, strpos($file_path, "/") + 1);  
        if (!_upload_extension_allowed($exist_file_name)) {
            echo "File type not supported!";
            exit;
        }
        _maybe_create_upload_path($path);
        $imagePath = $choosepath . $file_path;
        $file_type = "";
        $newPath = $path;
        $filename = unique_filename($path, $exist_file_name);
        $newFilePath = $path . $filename;

        $copied = copy($imagePath, $newFilePath);
        if ((!$copied)) {
            echo "File not uploaded!";
            exit;
        } else {
            $CI =& get_instance();
            $CI->load->model('projects_model');
            $data = array();
            $data[] = array(
                'file_name' => $filename,
                'filetype' => $file_type
            );
            $CI->projects_model->add_attachment_to_database($projectid, $data, false, $form_activity);
            echo "success";
            exit;
        }

    }

    return false;
}

function handle_event_existing_attachments($projectid, $index_name = 'file', $form_activity = false)
{
    $CI =& get_instance();
    if (isset($_POST['file_path']) && $_POST['file_path'] != '') {
        $file_path = $_POST['file_path'];
        $path = get_upload_path_by_type('project') . $projectid . '/';
        $choosepath = get_upload_path_by_type('file');
        $exist_file_name = substr($file_path, strrpos($file_path, '/') + 1);
        if (ENVIRONMENT == 'development') {
            $exist_file_name = substr($file_path, strrpos($file_path, '\\') + 1);
        }
        //$final_path = substr($file_path, strpos($file_path, "/") + 1);  
        if (!_upload_extension_allowed($exist_file_name)) {
            echo "File type not supported!";
            exit;
        }
        _maybe_create_upload_path($path);
        $imagePath = $choosepath . $file_path;
        $file_type = "";
        $newPath = $path;
        $filename = unique_filename($path, $exist_file_name);
        $newFilePath = $path . $filename;

        $copied = copy($imagePath, $newFilePath);
        if ((!$copied)) {
            echo "File not uploaded!";
            exit;
        } else {
            $CI =& get_instance();
            $CI->load->model('projects_model');
            $data = array();
            $data[] = array(
                'file_name' => $filename,
                'filetype' => $file_type
            );
            $CI->projects_model->add_attachment_to_database($projectid, $data, false, $form_activity, "event");
            echo "success";
            exit;
        }

    }

    return false;
}

function handle_task_attachments_array($taskid, $index_name = 'attachments', $type = '')
{
    $uploaded_files = array();

    /**
     * Modified By: Vaidehi
     * Dt: 02/14/2018
     * for venue attachments
     */
    if (isset($type) && $type == 'venueimages') {
        $path = get_upload_path_by_type('venue_attachments') . $taskid . '/';
    } else {
        $path = get_upload_path_by_type('task') . $taskid . '/';
    }

    if (isset($_FILES[$index_name])) {

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {

                if (!_upload_extension_allowed($_FILES[$index_name]["name"][$i])) {
                    continue;
                }
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES[$index_name]["name"][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, array(
                        'file_name' => $filename,
                        'filetype' => $_FILES[$index_name]["type"][$i]
                    ));
                    if (is_image($newFilePath)) {
                        create_img_thumb($path, $filename);
                    }
                }
            }
        }
    }

    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/* Added by Purvi on 11-20-2017 for messages attachment */
function handle_message_attachments_array($messageid, $index_name = 'attachments')
{
    $uploaded_files = array();
    $path = get_upload_path_by_type('message') . $messageid . '/';


    if (isset($_FILES[$index_name])) {

        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {

                if (!_upload_extension_allowed($_FILES[$index_name]["name"][$i])) {
                    continue;
                }
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES[$index_name]["name"][$i]);
                $newFilePath = $path . $filename;

                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, array(
                        'file_name' => $filename
                    ));
                }
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Check for task attachment
 * @since Version 1.0.1
 * @param  mixed $taskid
 * @return mixed           false if no attachment || array uploaded attachments
 */
function handle_tasks_attachments($taskid, $index_name = 'file', $form_activity = false)
{
    if (isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name']) && $form_activity) {
        return;
    }

    if (isset($_FILES[$index_name]) && _perfex_upload_error($_FILES[$index_name]['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES[$index_name]['error']);
        die;
    }

    $path = get_upload_path_by_type('task') . $taskid . '/';
    $uploaded_files = array();
    if (isset($_FILES[$index_name]['name']) && $_FILES[$index_name]['name'] != '') {
        do_action('before_upload_task_attachment', $taskid);


        // Get the temp file path
        $tmpFilePath = $_FILES[$index_name]['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            if (!_upload_extension_allowed($_FILES[$index_name]["name"])) {
                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES[$index_name]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                array_push($uploaded_files, array(
                    'file_name' => $filename,
                    'filetype' => $_FILES[$index_name]["type"]
                ));

                if (is_image($newFilePath)) {
                    create_img_thumb($path, $filename);
                }
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Invoice attachments
 * @since  Version 1.0.4
 * @param  mixed $invoiceid invoice ID to add attachments
 * @return array  - Result values
 */
function handle_sales_attachments($rel_id, $rel_type)
{
    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }

    $path = get_upload_path_by_type($rel_type) . $rel_id . '/';

    $CI =& get_instance();
    if (isset($_FILES['file']['name'])) {
        $uploaded_files = false;
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $type = $_FILES["file"]["type"];
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $file_uploaded = true;
                $attachment = array();
                $attachment[] = array(
                    'file_name' => $filename,
                    'filetype' => $type,
                );
                $insert_id = $CI->misc_model->add_attachment_to_database($rel_id, $rel_type, $attachment);
                // Get the key so we can return to ajax request and show download link
                $CI->db->where('id', $insert_id);
                $_attachment = $CI->db->get('tblfiles')->row();
                $key = $_attachment->attachment_key;

                if ($rel_type == 'invoice') {
                    $CI->load->model('invoices_model');
                    $CI->invoices_model->log_invoice_activity($rel_id, 'invoice_activity_added_attachment');
                } elseif ($rel_type == 'estimate') {
                    $CI->load->model('estimates_model');
                    $CI->estimates_model->log_estimate_activity($rel_id, 'estimate_activity_added_attachment');
                }
            }
        }
        if ($file_uploaded == true) {
            echo json_encode(array(
                'success' => true,
                'attachment_id' => $insert_id,
                'filetype' => $type,
                'rel_id' => $rel_id,
                'file_name' => $filename,
                'key' => $key,
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'rel_id' => $rel_id,
                'file_name' => $filename
            ));
        }
    }
}

/**
 * Client attachments
 * @since  Version 1.0.4
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function handle_client_attachments_upload($id, $customer_upload = false)
{
    $path = get_upload_path_by_type('customer') . $id . '/';
    $CI =& get_instance();
    if (isset($_FILES['file']['name'])) {
        do_action('before_upload_client_attachment', $id);
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $attachment = array();
                $attachment[] = array(
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                );
                if (is_image($newFilePath)) {
                    create_img_thumb($newFilePath, $filename);
                }

                if ($customer_upload == true) {
                    $attachment[0]['staffid'] = 0;
                    $attachment[0]['contact_id'] = get_contact_user_id();
                    $attachment['visible_to_customer'] = 1;
                }

                $CI->misc_model->add_attachment_to_database($id, 'customer', $attachment);
            }
        }
    }
}

/**
 * Handles upload for expenses receipt
 * @param  mixed $id expense id
 * @return void
 */
function handle_expense_attachments($id)
{
    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = get_upload_path_by_type('expense') . $id . '/';
    $CI =& get_instance();

    if (isset($_FILES['file']['name'])) {
        do_action('before_upload_expense_attachment', $id);
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename = $_FILES["file"]["name"];
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $attachment = array();
                $attachment[] = array(
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                );

                $CI->misc_model->add_attachment_to_database($id, 'expense', $attachment);
            }
        }
    }
}

/**
 * Check for ticket attachment after inserting ticket to database
 * @param  mixed $ticketid
 * @return mixed           false if no attachment || array uploaded attachments
 */
function handle_ticket_attachments($ticketid, $index_name = 'attachments')
{
    $path = get_upload_path_by_type('ticket') . $ticketid . '/';
    $uploaded_files = array();

    if (isset($_FILES[$index_name])) {
        _file_attachments_index_fix($index_name);

        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            do_action('before_upload_ticket_attachment', $ticketid);
            if ($i <= get_option('maximum_allowed_ticket_attachments')) {
                // Get the temp file path
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Getting file extension
                    $path_parts = pathinfo($_FILES[$index_name]["name"][$i]);
                    $extension = $path_parts['extension'];

                    $extension = strtolower($extension);
                    $allowed_extensions = explode(',', get_option('ticket_attachments_file_extensions'));
                    $allowed_extensions = array_map('trim', $allowed_extensions);
                    // Check for all cases if this extension is allowed
                    if (!in_array('.' . $extension, $allowed_extensions)) {
                        continue;
                    }
                    _maybe_create_upload_path($path);
                    $filename = unique_filename($path, $_FILES[$index_name]["name"][$i]);
                    $newFilePath = $path . $filename;
                    // Upload the file into the temp dir
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        array_push($uploaded_files, array(
                            'file_name' => $filename,
                            'filetype' => $_FILES[$index_name]["type"][$i]
                        ));
                    }
                }
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Check for company logo upload
 * @return boolean
 */
function handle_company_logo_upload()
{
    if (isset($_FILES['company_logo']) && _perfex_upload_error($_FILES['company_logo']['error'])) {
        set_alert('warning', _perfex_upload_error($_FILES['company_logo']['error']));

        return false;
    }
    if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
        do_action('before_upload_company_logo_attachment');
        $path = get_upload_path_by_type('company');
        // Get the temp file path
        $tmpFilePath = $_FILES['company_logo']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["company_logo"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
                'gif',
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }

            // Setup our new file path
            $filename = 'logo' . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('company_logo', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added By : Vaidehi
 * Dt : 10/13/2017
 * for brand options
 */
/**
 * Check for brands logo upload
 * @return boolean
 */
function handle_brand_logo_upload()
{
    if (isset($_FILES['company_logo']) && _perfex_upload_error($_FILES['company_logo']['error'])) {
        set_alert('warning', _perfex_upload_error($_FILES['company_logo']['error']));

        return false;
    }
    if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
        $session_data = get_session_data();

        $filenm = $session_data['brand_id'] . "-" . strtotime(date("Y-m-d H:i:s"));

        do_action('before_upload_company_logo_attachment');
        $path = get_upload_path_by_type('brands');
        // Get the temp file path
        $tmpFilePath = $_FILES['company_logo']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["company_logo"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
                'gif',
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }

            // Setup our new file path
            $filename = 'logo-' . $filenm . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            if (isset($_POST['brandimagebase64']) && !empty($_POST['brandimagebase64'])) {
                $data = $_POST['brandimagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path .= 'round_' . $filename;
                file_put_contents($path, $data);
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_brand_option('company_logo', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added By : Masud
 * Dt : 04/02/2019
 * for brand options
 */
/**
 * Check for brands logo upload
 * @return boolean
 */
function handle_brand_icon_upload()
{
    if (isset($_FILES['company_icon']) && _perfex_upload_error($_FILES['company_icon']['error'])) {
        set_alert('warning', _perfex_upload_error($_FILES['company_icon']['error']));

        return false;
    }
    if (isset($_FILES['company_icon']['name']) && $_FILES['company_icon']['name'] != '') {
        $session_data = get_session_data();

        $filenm = $session_data['brand_id'] . "-" . strtotime(date("Y-m-d H:i:s"));

        do_action('before_upload_company_logo_attachment');
        $path = get_upload_path_by_type('brands');
        // Get the temp file path
        $tmpFilePath = $_FILES['company_icon']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["company_icon"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
                'gif',
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }
            // Setup our new file path
            $filename = 'icon-' . $filenm . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            if (isset($_POST['imagebase64']) && !empty($_POST['imagebase64'])) {
                $data = $_POST['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path .= 'round_' . $filename;
                file_put_contents($path, $data);
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_brand_option('company_icon', $filename);

                return true;
            }
        }
    }
    return false;
}
/**
 * Added By : Vaidehi
 * Dt : 10/18/2017
 * for brand options
 */
/**
 * Check for brands banner
 * @return boolean
 */
function handle_brand_banner_upload()
{
    if (isset($_FILES['banner']) && _perfex_upload_error($_FILES['banner']['error'])) {
        set_alert('warning', _perfex_upload_error($_FILES['banner']['error']));

        return false;
    }
    if (isset($_FILES['banner']['name']) && $_FILES['banner']['name'] != '') {
        $session_data = get_session_data();

        $filenm = $session_data['brand_id'] . "-" . strtotime(date("Y-m-d H:i:s"));

        do_action('before_upload_banner_attachment');
        $path = get_upload_path_by_type('brands');
        // Get the temp file path
        $tmpFilePath = $_FILES['banner']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["banner"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
                'gif',
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }

            // Setup our new file path
            $filename = 'banner-' . $filenm . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            if (isset($_POST['bannerbase64']) && !empty($_POST['bannerbase64'])) {
                $data = $_POST['bannerbase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path .= 'croppie_' . $filename;
                file_put_contents($path, $data);
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_brand_option('banner', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Check for company logo upload
 * @return boolean
 */
function handle_company_signature_upload()
{
    if (isset($_FILES['signature_image']) && _perfex_upload_error($_FILES['signature_image']['error'])) {
        set_alert('warning', _perfex_upload_error($_FILES['signature_image']['error']));

        return false;
    }
    if (isset($_FILES['signature_image']['name']) && $_FILES['signature_image']['name'] != '') {
        do_action('before_upload_signature_image_attachment');
        $path = get_upload_path_by_type('company');
        // Get the temp file path
        $tmpFilePath = $_FILES['signature_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["signature_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);

            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }
            // Setup our new file path
            $filename = 'signature' . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('signature_image', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added By : Vaidehi
 * Dt : 10/13/2017
 * for brand options
 */
/**
 * Check for brands logo upload
 * @return boolean
 */
function handle_brand_signature_upload()
{
    if (isset($_FILES['signature_image']) && _perfex_upload_error($_FILES['signature_image']['error'])) {
        set_alert('warning', _perfex_upload_error($_FILES['signature_image']['error']));

        return false;
    }
    if (isset($_FILES['signature_image']['name']) && $_FILES['signature_image']['name'] != '') {
        $session_data = get_session_data();

        $filenm = $session_data['brand_id'] . "-" . strtotime(date("Y-m-d H:i:s"));

        do_action('before_upload_signature_image_attachment');
        $path = get_upload_path_by_type('brands');
        // Get the temp file path
        $tmpFilePath = $_FILES['signature_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["signature_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);

            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }
            // Setup our new file path
            $filename = 'signature' . $filenm . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_brand_option('signature_image', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Handle company favicon upload
 * @return boolean
 */
function handle_favicon_upload()
{
    if (isset($_FILES['favicon']['name']) && $_FILES['favicon']['name'] != '') {
        do_action('before_upload_favicon_attachment');
        $path = get_upload_path_by_type('company');
        // Get the temp file path
        $tmpFilePath = $_FILES['favicon']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["favicon"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            // Setup our new file path
            $filename = 'favicon' . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('favicon', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added By : Vaidehi
 * Dt : 10/13/2017
 * for brand options
 */
/**
 * Handle brands favicon upload
 * @return boolean
 */
function handle_brand_favicon_upload()
{
    if (isset($_FILES['favicon']['name']) && $_FILES['favicon']['name'] != '') {
        do_action('before_upload_favicon_attachment');
        $path = get_upload_path_by_type('brands');
        // Get the temp file path
        $tmpFilePath = $_FILES['favicon']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            $session_data = get_session_data();

            $filenm = $session_data['brand_id'] . "-" . strtotime(date("Y-m-d H:i:s"));

            // Getting file extension
            $path_parts = pathinfo($_FILES["favicon"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            // Setup our new file path
            $filename = 'favicon-' . $filenm . '.' . $extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);

            if (isset($_POST['favicon64']) && !empty($_POST['favicon64'])) {
                $data = $_POST['favicon64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path .= 'round_' . $filename;
                file_put_contents($path, $data);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_brand_option('favicon', $filename);

                return true;
            }
        }
    }

    return false;
}

/**
 * Check for staff profile image
 * @return boolean
 */
function handle_staff_profile_image_upload($staff_id = '')
{
    if (!is_numeric($staff_id)) {
        $staff_id = get_staff_user_id();
    }
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        do_action('before_upload_staff_profile_image');
        $path = get_upload_path_by_type('staff') . $staff_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
            $newFilePath = $path . '/' . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 50;
                $config['height'] = 50;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('staffid', $staff_id);
                $CI->db->update('tblstaff', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added by Purvi on 10-13-2017
 * Check for addressbook profile image
 * @return boolean
 */
function handle_addressbook_profile_image_upload($addressbook_id = '')
{

    if (!is_numeric($addressbook_id)) {
        $addressbook_id = "";
    }
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        $path = get_upload_path_by_type('addressbook') . $addressbook_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
            $newFilePath = $path . '/' . $filename;

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('addressbookid', $addressbook_id);
                $CI->db->update('tbladdressbook', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added by Purvi on 10-30-2017
 * Check for lead profile image
 * @return boolean
 */
function handle_lead_profile_image_upload($lead_id = '')
{
    if (!is_numeric($lead_id)) {
        $lead_id = "";
    }
    if (isset($_FILES['lead_profile_image']['name']) && $_FILES['lead_profile_image']['name'] != '') {
        $path = get_upload_path_by_type('lead_profile_image') . $lead_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['lead_profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["lead_profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["lead_profile_image"]["name"]);
            $newFilePath = $path . '/' . $filename;

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('id', $lead_id);
                $CI->db->update('tblleads', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added By : Vaidehi
 * Dt : 12/19/2017
 * Check for project profile image
 * @return boolean
 */
function handle_project_profile_image_upload($project_id = '')
{

    if (!is_numeric($project_id)) {
        $project_id = "";
    }
    if (isset($_FILES['project_profile_image']['name']) && $_FILES['project_profile_image']['name'] != '') {
        $path = get_upload_path_by_type('project_profile_image') . $project_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['project_profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["project_profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["project_profile_image"]["name"]);
            $newFilePath = $path . '/' . $filename;

            if (isset($_POST['imagebase64'])) {
                $data = $_POST['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                //$path = get_upload_path_by_type('project_profile_image') . $project_id . '/';
                //_maybe_create_upload_path($path);
                //$filename = unique_filename($path, $_FILES["project_profile_image"]["name"]);
                $path .= 'round_' . $filename;
                file_put_contents($path, $data);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;

                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('id', $project_id);
                $CI->db->update('tblprojects', array(
                    'project_profile_image' => $filename
                ));
                // Remove original image
                //unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added By : Masud
 * Dt : 12/19/2017
 * Check for project profile image
 * @return boolean
 */
function handle_project_cover_image_upload($project_id = '')
{

    if (!is_numeric($project_id)) {
        $project_id = "";
    }
    if (isset($_FILES['projectcoverimage']['name']) && $_FILES['projectcoverimage']['name'] != '') {
        $path = get_upload_path_by_type('project_cover_image') . $project_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['projectcoverimage']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["projectcoverimage"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["projectcoverimage"]["name"]);
            $newFilePath = $path . '/' . $filename;

            if (isset($_POST['bannerbase64']) && !empty($_POST['bannerbase64'])) {
                $data = $_POST['bannerbase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path = $path . 'croppie_' . $filename;
                file_put_contents($path, $data);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;

                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('id', $project_id);
                $CI->db->update('tblprojects', array(
                    'projectcoverimage' => $filename
                ));
                // Remove original image
                //unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added by Purvi on 12-12-2017
 * Check for proposal template banner image
 * @return boolean
 */
function handle_proposaltemplate_banner_upload($templateid = '')
{
    if (!is_numeric($templateid)) {
        $templateid = "";
    }

    if (isset($_FILES['banner']['name']) && $_FILES['banner']['name'] != '') {
        $session_data = get_session_data();

        do_action('before_upload_proposaltemplate_banner_attachment');
        $path = get_upload_path_by_type('proposaltemplate') . $templateid . '/';

        // Get the temp file path
        $tmpFilePath = $_FILES['banner']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["banner"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
                'gif',
            );

            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }

            // Setup our new file path
            //$filename    = 'banner-' . $filenm .  '.' . $extension;
            $filename = unique_filename($path, $_FILES["banner"]["name"]);
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);

            if (isset($_POST['bannerbase64']) && !empty($_POST['bannerbase64'])) {
                $data = $_POST['bannerbase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path = $path . 'croppie_' . $filename;
                file_put_contents($path, $data);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->db->where('templateid', $templateid);
                $CI->db->update('tblproposaltemplates', array(
                    'banner' => $filename
                ));
                // Remove original image
                //unlink($tmpFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Check for staff profile image
 * @return boolean
 */
function handle_contact_profile_image_upload($contact_id = '')
{
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        do_action('before_upload_contact_profile_image');
        if ($contact_id == '') {
            $contact_id = get_contact_user_id();
        }
        $path = get_upload_path_by_type('contact_profile_images') . $contact_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;
                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();

                $CI->db->where('id', $contact_id);
                $CI->db->update('tblcontacts', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added by Avni on 11/29/2017
 * Check for Line Items image
 * @return boolean
 */
function handle_line_items_image_upload($item_id = '')
{
    if (!is_numeric($item_id)) {
        $item_id = "";
    }
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        $path = get_upload_path_by_type('line_items_image') . $item_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (isset($_POST['imagebase64']) && !empty($_POST['imagebase64'])) {
                $data = $_POST['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                /*$path = get_upload_path_by_type('venue_logo') . $venue_id . '/';
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES["venuelogo"]["name"]);*/
                $path = $path . '/round_' . $filename;
                file_put_contents($path, $data);
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('id', $item_id);
                $CI->db->update('tblitems', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                //unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added By: Vaidehi
 * Dt: 02/14/2018
 * Check for venue logo image
 * @return boolean
 */
function handle_venue_image_upload($venue_id = '')
{
    if (!is_numeric($venue_id)) {
        $venue_id = "";
    }

    //for venue logo
    if (isset($_FILES['venuelogo']['name']) && $_FILES['venuelogo']['name'] != '') {
        $path = get_upload_path_by_type('venue_logo') . $venue_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['venuelogo']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["venuelogo"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["venuelogo"]["name"]);
            $newFilePath = $path . '/' . $filename;

            if (isset($_POST['imagebase64']) && !empty($_POST['imagebase64'])) {
                $data = $_POST['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                /*$path = get_upload_path_by_type('venue_logo') . $venue_id . '/';
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES["venuelogo"]["name"]);*/
                $path = $path . '/round_' . $filename;
                file_put_contents($path, $data);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('venueid', $venue_id);
                $CI->db->update('tblvenue', array(
                    'venuelogo' => $filename
                ));
                // Remove original image
                //unlink($newFilePath);


                return true;
            }
        }
    }

    return false;
}

/**
 * Added By: Vaidehi
 * Dt: 02/14/2018
 * Check for venue cover image
 * @return boolean
 */
function handle_venue_cover_image_upload($venue_id = '')
{
    if (!is_numeric($venue_id)) {
        $venue_id = "";
    }

    //for venue cover image
    if (isset($_FILES['venuecoverimage']['name']) && $_FILES['venuecoverimage']['name'] != '') {
        $path = get_upload_path_by_type('venue_coverimage') . $venue_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['venuecoverimage']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["venuecoverimage"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["venuecoverimage"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (isset($_POST['bannerbase64']) && !empty($_POST['bannerbase64'])) {
                $data = $_POST['bannerbase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                /*$path = get_upload_path_by_type('venue_logo') . $venue_id . '/';
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES["venuelogo"]["name"]);*/
                $path = $path . 'croppie_' . $filename;
                file_put_contents($path, $data);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('venueid', $venue_id);
                $CI->db->update('tblvenue', array(
                    'venuecoverimage' => $filename
                ));
                // Remove original image
                //unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Handle upload for project discussions comment
 * Function for jquery-comment plugin
 * @param  mixed $discussion_id discussion id
 * @param  mixed $post_data additional post data from the comment
 * @param  array $insert_data insert data to be parsed if needed
 * @return arrray
 */
function handle_project_discussion_comment_attachments($discussion_id, $post_data, $insert_data)
{
    if (isset($_FILES['file']['name']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo json_encode(array('message' => _perfex_upload_error($_FILES['file']['error'])));
        die;
    }

    if (isset($_FILES['file']['name'])) {
        do_action('before_upload_project_discussion_comment_attachment');
        $path = PROJECT_DISCUSSION_ATTACHMENT_FOLDER . $discussion_id . '/';
        // Check for all cases if this extension is allowed
        if (!_upload_extension_allowed($_FILES["file"]["name"])) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array('message' => _l('file_php_extension_blocked')));
            die;
        }

        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $insert_data['file_name'] = $filename;

                if (isset($_FILES['file']['type'])) {
                    $insert_data['file_mime_type'] = $_FILES['file']['type'];
                } else {
                    $insert_data['file_mime_type'] = get_mime_by_extension($filename);
                }
            }
        }
    }

    return $insert_data;
}

function create_img_thumb($path, $filename, $width = 300, $height = 300)
{
    $CI = &get_instance();

    $source_path = rtrim($path, '/') . '/' . $filename;
    $target_path = $path;
    $config_manip = array(
        'image_library' => 'gd2',
        'source_image' => $source_path,
        'new_image' => $target_path,
        'maintain_ratio' => TRUE,
        'create_thumb' => TRUE,
        'thumb_marker' => '_thumb',
        'width' => $width,
        'height' => $height
    );

    $CI->image_lib->initialize($config_manip);
    $CI->image_lib->resize();
    $CI->image_lib->clear();
}

function _upload_extension_allowed($filename)
{
    $path_parts = pathinfo($filename);
    $extension = isset($path_parts['extension']) ? $path_parts['extension'] : "";
    $extension = strtolower($extension);
    $allowed_extensions = explode(',', get_option('allowed_files'));
    $allowed_extensions = array_map('trim', $allowed_extensions);
    // Check for all cases if this extension is allowed
    if (!in_array('.' . $extension, $allowed_extensions)) {
        return false;
    }

    return true;
}

function _file_attachments_index_fix($index_name)
{
    if (isset($_FILES[$index_name]['name']) && is_array($_FILES[$index_name]['name'])) {
        $_FILES[$index_name]['name'] = array_values($_FILES[$index_name]['name']);
    }

    if (isset($_FILES[$index_name]['type']) && is_array($_FILES[$index_name]['type'])) {
        $_FILES[$index_name]['type'] = array_values($_FILES[$index_name]['type']);
    }

    if (isset($_FILES[$index_name]['tmp_name']) && is_array($_FILES[$index_name]['tmp_name'])) {
        $_FILES[$index_name]['tmp_name'] = array_values($_FILES[$index_name]['tmp_name']);
    }

    if (isset($_FILES[$index_name]['error']) && is_array($_FILES[$index_name]['error'])) {
        $_FILES[$index_name]['error'] = array_values($_FILES[$index_name]['error']);
    }

    if (isset($_FILES[$index_name]['size']) && is_array($_FILES[$index_name]['size'])) {
        $_FILES[$index_name]['size'] = array_values($_FILES[$index_name]['size']);
    }
}

function _maybe_create_upload_path($path)
{
    if (!file_exists($path)) {
        mkdir($path);
        fopen($path . 'index.html', 'w');
    }
}

/**
 * Function that return full path for upload based on passed type
 * @param  string $type
 * @return string
 */
function get_upload_path_by_type($type)
{
    switch ($type) {
        case 'lead':
            return LEAD_ATTACHMENTS_FOLDER;
            break;
        case 'expense':
            return EXPENSE_ATTACHMENTS_FOLDER;
            break;
        case 'project':
            return PROJECT_ATTACHMENTS_FOLDER;
            break;
        case 'proposal':
            return PROPOSAL_ATTACHMENTS_FOLDER;
            break;
        case 'estimate':
            return ESTIMATE_ATTACHMENTS_FOLDER;
            break;
        case 'invoice':
            return INVOICE_ATTACHMENTS_FOLDER;
            break;
        case 'task':
            return TASKS_ATTACHMENTS_FOLDER;
            break;
        case 'contract':
            return CONTRACTS_UPLOADS_FOLDER;
            break;
        case 'customer':
            return CLIENT_ATTACHMENTS_FOLDER;
            break;
        case 'staff':
            return STAFF_PROFILE_IMAGES_FOLDER;
            break;
        case 'company':
            return COMPANY_FILES_FOLDER;
            break;
        case 'ticket':
            return TICKET_ATTACHMENTS_FOLDER;
            break;
        case 'contact_profile_images':
            return CONTACT_PROFILE_IMAGES_FOLDER;
            break;
        case 'newsfeed':
            return NEWSFEED_FOLDER;
            break;

        /**
         * Added By : Vaidehi
         * Dt : 10/13/2017
         * for brand images
         */
        case 'brands':
            return BRAND_IMAGES_FOLDER;
            break;
        /**
         * Added By : Purvi
         * Dt : 10/16/2017
         * for addressbook images
         */
        case 'addressbook':
            return ADDRESSBOOK_PROFILE_IMAGES_FOLDER;
            break;
        /**
         * Added By : Purvi
         * Dt : 10/30/2017
         * for lead profile image
         */
        case 'lead_profile_image':
            return LEAD_PROFILE_IMAGES_FOLDER;
            break;

        /**
         * Added By : Purvi
         * Dt : 11/14/2017
         * for file images
         */
        case 'file':
            return FILE_ATTACHMENTS_FOLDER;
            break;

        /**
         * Added By : Purvi
         * Dt : 11/20/2017
         * for message images
         */
        case 'message':
            return MESSAGE_ATTACHMENTS_FOLDER;
            break;

        /**
         * Added By : Avni
         * Dt : 11/29/2017
         * for line items image
         */
        case 'line_items_image':
            return LINE_ITEMS_IMAGES_FOLDER;
            break;

        /**
         * Added By : Purvi
         * Dt : 12/12/2017
         * for message images
         */
        case 'proposaltemplate':
            return PROPOSALTEMPLATE_ATTACHMENTS_FOLDER;
            break;

        /**
         * Added By : Masud
         * Dt : 02/22/2018
         * for Prposal gallery images
         */
        case 'proposalgallery':
            return PROPOSALTEMPLATE_GALLERY_FOLDER;
            break;

        case 'proposalfiles':
            return PROPOSALTEMPLATE_FILES_FOLDER;
            break;

        case 'proposal_banner_images':
            return PROPOSALTEMPLATE_BANNER_FOLDER;
            break;

        /**
         * Added By : Masud
         * Dt : 02/22/2018
         * for Prposal gallery images
         */
        case 'proposalsignature':
            return PROPOSALTEMPLATE_SIGNATURE_FOLDER;
            break;

        /**
         * Added By : Vaidehi
         * Dt : 12/20/2017
         * for project profile image
         */
        case 'project_profile_image':
            return PROJECT_PROFILE_IMAGES_FOLDER;
            break;

        /**
         * Added By : Masud
         * Dt : 02/26/2019
         * for project profile image
         */
        case 'project_cover_image':
            return PROJECT_COVER_IMAGES_FOLDER;
            break;

        /**
         * Added By : Masud
         * Dt : 02/05/2018
         * for product services package image
         */
        case 'product_services_package_image':
            return PROJECT_SERVICES_PACKAGE_IMAGES_FOLDER;
            break;
        /**
         * Added By : Masud
         * Dt : 03/21/2018
         * for Questionnaire image
         */
        case 'questionnaire':
            return QUESTIONNAIRE_FOLDER;
            break;

        /**
         * Added By : Vaidehi
         * Dt : 02/14/2018
         * for venue logo image
         */
        case 'venue_logo':
            return VENUE_LOGO_IMAGES_FOLDER;
            break;

        /**
         * Added By : Vaidehi
         * Dt : 02/14/2018
         * for venue cover image
         */
        case 'venue_coverimage':
            return VENUE_COVER_IMAGES_FOLDER;
            break;
        /**
         * Added By : Masud
         * Dt : 07/10/2018
         * for venue Location image
         */
        case 'venue_locimage':
            return VENUE_LOC_IMAGES_FOLDER;
            break;

        /**
         * Added By : Vaidehi
         * Dt : 02/14/2018
         * for venue and/or site location images/folders
         */
        case 'venue_attachments':
            return VENUE_IMAGES_FOLDER;
            break;

        default:
            return false;
    }
}

/**
 * Added By : Masud
 * Dt : 01/02/2018
 * Check for group profile image
 * @return boolean
 */
function handle_group_image_upload($group_id = '')
{
    if (!is_numeric($group_id)) {
        $group_id = "";
    }
    if (isset($_FILES['group_image']['name']) && $_FILES['group_image']['name'] != '') {
        $path = get_upload_path_by_type('product_services_package_image') . $group_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['group_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["group_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["group_image"]["name"]);
            $newFilePath = $path . '/' . $filename;

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;

                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('id', $group_id);
                $CI->db->update('tblitems_groups', array(
                    'group_image' => $filename
                ));
                // Remove original image
                //unlink($newFilePath);

                return true;
            }
        }
    }

    return false;
}

/**
 * Added by Masud on 02-22-2018
 * Check for proposal template banner image
 * @return boolean
 */
function handle_proposaltemplate_gallery_upload($templateid = '', $gdata)
{
    $error_flag = "";
    if (!is_numeric($templateid)) {
        $templateid = "";
    }

    if (isset($_FILES['pimage']['name']) && $_FILES['pimage']['name'] != '') {

        if ($_FILES['pimage']['size'] > 2 * MB) {
            $error_flag = "size";
            return $error_flag;
        }
        $session_data = get_session_data();

        do_action('before_upload_proposaltemplate_gallery_attachment');
        $path = get_upload_path_by_type('proposalgallery') . $templateid . '/';
        //die('<--here');
        // Get the temp file path
        $tmpFilePath = $_FILES['pimage']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES['pimage']["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png',
                'gif',
            );
            if (!in_array($extension, $allowed_extensions)) {
                $error_flag = "ext";
                return $error_flag;
            }

            // Setup our new file path
            //$filename    = 'banner-' . $filenm .  '.' . $extension;
            $filename = unique_filename($path, $_FILES['pimage']["name"]);
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            if (isset($_POST['imagebase64'])) {
                $data = $_POST['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path = get_upload_path_by_type('proposalgallery') . $templateid . '/';
                /* _maybe_create_upload_path($path);
                 $filename =$_FILES['pimage']['name'];
                 $filename = unique_filename($path, $filename);*/
                $path .= 'round_' . $filename;
                file_put_contents($path, $data);
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;

                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $gdata['name'] = $filename;
                $CI->db->insert('tblproposal_gallery_files', $gdata);
                /*$CI =& get_instance();
                $CI->db->where('templateid', $templateid);
                $CI->db->update('tblproposaltemplates', array(
                    'banner' => $filename
                ));*/
                // Remove original image
                //unlink($tmpFilePath);

                return true;
            }
        }
    }


    if (isset($_FILES['pfile']['name']) && $_FILES['pfile']['name'] != '') {
        if ($_FILES['pfile']['size'] > 1 * GB) {
            $error_flag = "size";
            return $error_flag;
        }
        $session_data = get_session_data();

        do_action('before_upload_proposaltemplate_gallery_attachment');
        $path = get_upload_path_by_type('proposalfiles') . $templateid . '/';
        //die('<--here');
        // Get the temp file path
        $tmpFilePath = $_FILES['pfile']['tmp_name'];

        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES['pfile']["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array();

            // Setup our new file path
            //$filename    = 'banner-' . $filenm .  '.' . $extension;
            $filename = unique_filename($path, $_FILES['pfile']["name"]);
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
                if (in_array($extension, $allowed_extensions)) {
                    $CI->load->library('image_lib');
                    $config = array();
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $newFilePath;
                    $config['new_image'] = 'thumb_' . $filename;
                    $config['maintain_ratio'] = true;
                    $config['width'] = 160;
                    $config['height'] = 160;

                    $CI->image_lib->initialize($config);

                    $CI->image_lib->resize();
                    $CI->image_lib->clear();

                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $newFilePath;
                    $config['new_image'] = 'small_' . $filename;
                    $config['maintain_ratio'] = true;
                    $config['width'] = 32;
                    $config['height'] = 32;

                    $CI->image_lib->initialize($config);
                    $CI->image_lib->resize();
                }
                $data['name'] = $filename;
                $CI->db->insert('tblproposal_gallery_files', $data);
                /*$CI =& get_instance();
                $CI->db->where('templateid', $templateid);
                $CI->db->update('tblproposaltemplates', array(
                    'banner' => $filename
                ));*/
                // Remove original image
                //unlink($tmpFilePath);

                return true;
            }
        }
    }

    return false;
}

function upload_signature($signature, $id)
{

    $path = get_upload_path_by_type('proposalsignature') . $id . '/';
    // Get the temp file path
    $tmpFilePath = $signature['tmp_name'];
    // Make sure we have a filepath
    if (!empty($tmpFilePath) && $tmpFilePath != '') {
        // Getting file extension
        $path_parts = pathinfo($signature["name"]);
        $extension = $path_parts['extension'];
        $extension = strtolower($extension);
        $allowed_extensions = array(
            'jpg',
            'jpeg',
            'png',
            'gif',
        );
        if (!in_array($extension, $allowed_extensions)) {
            $error_flag = "ext";
            return $error_flag;
        }
        $filename = unique_filename($path, $signature["name"]);
        $newFilePath = $path . $filename;
        _maybe_create_upload_path($path);

        // Upload the file into the company uploads dir
        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
            /*$CI =& get_instance();
            $data['name'] = $filename;
            $this->db->where('templateid', $data['proposal_id']);
            $this->db->update('tblproposaltemplates', $pdata);
            $CI->db->update('tblproposal_gallery_files', $data);*/
            return true;
        }
    }
}

/**/

function handle_venue_attachments($venueid, $index_name = 'file', $form_activity = false)
{
    if (isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name']) && $form_activity) {
        return;
    }

    if (isset($_FILES[$index_name]) && _perfex_upload_error($_FILES[$index_name]['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES[$index_name]['error']);
        die;
    }

    $path = get_upload_path_by_type('venue_attachments') . $venueid . '/';
    $uploaded_files = array();
    if (isset($_FILES[$index_name]['name']) && $_FILES[$index_name]['name'] != '') {
        do_action('before_upload_task_attachment', $venueid);


        // Get the temp file path
        $tmpFilePath = $_FILES[$index_name]['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            if (!_upload_extension_allowed($_FILES[$index_name]["name"])) {
                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES[$index_name]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                array_push($uploaded_files, array(
                    'file_name' => $filename,
                    'filetype' => $_FILES[$index_name]["type"]
                ));

                if (is_image($newFilePath)) {
                    create_img_thumb($path, $filename);
                }
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Added By: Masud
 * Dt: 07/10/2018
 * Check for venue loc image
 * @return boolean
 */
function handle_venue_loc_image_upload($loc_id = '')
{
    if (!is_numeric($loc_id)) {
        $venue_id = "";
    }

    //for venue cover image
    if (isset($_FILES['loccoverimage']['name']) && $_FILES['loccoverimage']['name'] != '') {
        $path = get_upload_path_by_type('venue_locimage') . $loc_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['loccoverimage']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($_FILES["loccoverimage"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES["loccoverimage"]["name"]);
            $newFilePath = $path . '/' . $filename;
            if (isset($_POST['imagebase64']) && !empty($_POST['imagebase64'])) {
                $data = $_POST['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path .= 'croppie_' . $filename;
                file_put_contents($path, $data);
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;

                $CI->image_lib->initialize($config);

                $CI->image_lib->resize();
                $CI->image_lib->clear();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('locid', $loc_id);
                $CI->db->update('tblvenueloc', array(
                    'loccoverimage' => $filename
                ));
                return true;
            }
        }
    }

    return false;
}

function handle_venue_loc_attachments($venueid, $locid, $index_name = 'file', $form_activity = false)
{
    if (isset($_FILES[$index_name]) && empty($_FILES[$index_name]['name']) && $form_activity) {
        return;
    }

    if (isset($_FILES[$index_name]) && _perfex_upload_error($_FILES[$index_name]['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES[$index_name]['error']);
        die;
    }

    $path = get_upload_path_by_type('venue_attachments') . '/locations/' . $locid . '/';
    $uploaded_files = array();
    if (isset($_FILES[$index_name]['name']) && $_FILES[$index_name]['name'] != '') {
        // Get the temp file path
        $tmpFilePath = $_FILES[$index_name]['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            if (!_upload_extension_allowed($_FILES[$index_name]["name"])) {
                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $_FILES[$index_name]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                array_push($uploaded_files, array(
                    'file_name' => $filename,
                    'filetype' => $_FILES[$index_name]["type"]
                ));

                if (is_image($newFilePath)) {
                    create_img_thumb($path, $filename);
                }
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }

    return false;
}

/**
 * Added by Masud on 10-9-2018
 * Check for Multiple addressbook profile image
 * @return boolean
 */
function handle_multiple_addressbook_profile_image_upload($addressbook_id, $file)
{
    if (!is_numeric($addressbook_id)) {
        $addressbook_id = "";
    }
    if (isset($file['profile_image']['name']) && $file['profile_image']['name'] != '') {
        $path = get_upload_path_by_type('addressbook') . $addressbook_id . '/';
        // Get the temp file path
        $tmpFilePath = $file['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts = pathinfo($file["profile_image"]["name"]);
            $extension = $path_parts['extension'];
            $extension = strtolower($extension);
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', _l('file_php_extension_blocked'));

                return false;
            }
            _maybe_create_upload_path($path);
            $filename = unique_filename($path, $file["profile_image"]["name"]);
            $newFilePath = $path . '/' . $filename;

            if (isset($file['imagebase64']) && !empty($file['imagebase64'])) {
                $data = $file['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path .= 'round_' . $filename;
                file_put_contents($path, $data);
            }

            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->load->library('image_lib');
                $config = array();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'thumb_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 160;
                $config['height'] = 160;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library'] = 'gd2';
                $config['source_image'] = $newFilePath;
                $config['new_image'] = 'small_' . $filename;
                $config['maintain_ratio'] = true;
                $config['width'] = 32;
                $config['height'] = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('addressbookid', $addressbook_id);
                $CI->db->update('tbladdressbook', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                //unlink($newFilePath);
                return true;
            }
        }
    }

    return false;
}