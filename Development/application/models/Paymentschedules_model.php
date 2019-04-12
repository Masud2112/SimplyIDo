<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Paymentschedules_model extends CRM_Model
{
    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new employee paymentschedule
     * @param mixed $data
     */
    public function addpaymentschedule($data)
    {

        $brandid = get_user_session();
        if (isset($data['brandid']) && $data['brandid'] > 0) {
            $brandid = $data['brandid'];
        }
        $paymentschedules = array();
        if ($data['is_template'] == 1) {
            $paymentschedules = $this->paymentschedules_model->check_paymentschedule_name_exists($data['name'], '', $brandid);
        }

        //if($paymentschedules->templateid <= 0 || empty($paymentschedules)) {
        if ((isset($paymentschedules->templateid) && $paymentschedules->templateid <= 0) || empty($paymentschedules)) {
            $payment_schedule = $data['payment_schedule'];
            unset($data['payment_schedule']);
            $data['brandid'] = $brandid;
            $data['created_by'] = $this->session->userdata['staff_user_id'];
            $data['datecreated'] = date('Y-m-d H:i:s');
            $this->db->insert('tblpaymenttemplates', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                foreach ($payment_schedule as $pvalue) {
                    unset($pvalue['paymentdetailid']);
                    $pvalue['order'] = isset($pvalue['order']) ? $pvalue['order'] : 0;
                    if ($pvalue['duedate_type'] == 'custom') {
                        if ($pvalue['price_type'] == "fixed_amount") {
                            $pvalue['price_percentage'] = "";
                        } elseif ($pvalue['price_type'] == "divide_equally") {
                            $pvalue['price_percentage'] = "";
                            $pvalue['price_amount'] = "";
                        } elseif ($pvalue['price_type'] == "percentage") {
                            $pvalue['price_amount'] = "";
                        }
                        $this->db->insert('.tblpaymenttemplatedetails', array(
                            'paymentscheduleid' => $insert_id,
                            'duedate_type' => $pvalue['duedate_type'],
                            'duedate_number' => $pvalue['duedate_number'],
                            'custom_range_duration' => $pvalue['custom_range_duration'],
                            'duedate_criteria' => $pvalue['duedate_criteria'],
                            'price_type' => $pvalue['price_type'],
                            'price_amount' => $pvalue['price_amount'],
                            'price_percentage' => $pvalue['price_percentage'],
                            'order' => $pvalue['order']
                        ));
                    } else {
                        $duedate_date = date('Y-m-d');
                        if (isset($pvalue['duedate_date']) && !empty($pvalue['duedate_date'])) {
                            $pvalue['duedate_date'] = date_create($pvalue['duedate_date']);
                            $duedate_date = date_format($pvalue['duedate_date'], 'Y-m-d');
                        }
                        if ($pvalue['price_type'] == "fixed_amount") {
                            $pvalue['price_percentage'] = "";
                        } elseif ($pvalue['price_type'] == "divide_equally") {
                            $pvalue['price_percentage'] = "";
                            $pvalue['price_amount'] = "";
                        } elseif ($pvalue['price_type'] == "percentage") {
                            $pvalue['price_amount'] = "";
                        }
                        $this->db->insert('tblpaymenttemplatedetails', array(
                            'paymentscheduleid' => $insert_id,
                            'duedate_type' => $pvalue['duedate_type'],
                            'duedate_date' => $duedate_date,
                            'duedate_criteria' => $pvalue['duedate_criteria'],
                            'price_type' => $pvalue['price_type'],
                            'price_amount' => $pvalue['price_amount'],
                            'price_percentage' => $pvalue['price_percentage'],
                            'order' => $pvalue['order']
                        ));
                    }

                }
                logActivity('New Payment Schedule Template Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

                return $insert_id;
            }
        }
        return false;
    }

    /**
     * Update employee paymentschedule
     * @param  array $data paymentschedule data
     * @param  mixed $id paymentschedule id
     * @return boolean
     */
    public function updatepaymentschedule($data, $id)
    {
        /*echo "<pre>";
        print_r($data);
        die('<--here');*/
        $brandid = get_user_session();
        $paymentsched = (array)$this->getpaymentschedules($id);

        if (isset($data['brandid']) && $data['brandid'] > 0) {
            $brandid = $data['brandid'];
        }
        if (!isset($data['name'])) {
            $data['name'] = $paymentsched['name'];
        }
        $paymentschedules = array();
        if (isset($data['is_template']) && $data['is_template'] == 1) {
            $paymentschedules = $this->paymentschedules_model->check_paymentschedule_name_exists($data['name'], $id, $brandid);
        }
        if ((isset($paymentschedules->templateid) && $paymentschedules->templateid <= 0) || empty($paymentschedules)) {
            $payment_schedule = $data['payment_schedule'];
            unset($data['payment_schedule']);
            $affectedRows = 0;
            unset($data['paymentscheduleid']);
            $data['updated_by'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $this->db->where('templateid', $id);
            $this->db->update('tblpaymenttemplates', $data);
            $ptemplae_updated = $this->db->affected_rows();
            $this->db->where('paymentscheduleid', $id);
            $this->db->delete('tblpaymenttemplatedetails');
            if ($ptemplae_updated > 0) {
                foreach ($payment_schedule as $pvalue) {
                    /*if (isset($pvalue['price_type']) && isset($pvalue['duedate_type']) && isset($pvalue['duedate_criteria '])) {*/
                        $price_amount = $pvalue['price_amount'];
                        $duedate_date = date('Y-m-d');
                        $pvalue['order'] = isset($pvalue['order']) ? $pvalue['order'] : 0;
                        if (isset($pvalue['duedate_date']) && !empty($pvalue['duedate_date'])) {
                            $pvalue['duedate_date'] = date_create($pvalue['duedate_date']);
                            $duedate_date = date_format($pvalue['duedate_date'], 'Y-m-d');
                        }
                        if (empty($pvalue['payment_method'])) {
                            $pvalue['payment_method'] = "cash";
                        }
                        if (isset($pvalue['duedate_type']) && $pvalue['duedate_type'] == 'custom') {
                            if ($pvalue['price_type'] == "fixed_amount") {
                                $pvalue['price_percentage'] = "";
                            } elseif ($pvalue['price_type'] == "divide_equally") {
                                $pvalue['price_percentage'] = "";
                                $pvalue['price_amount'] = "";
                            } elseif ($pvalue['price_type'] == "percentage") {
                                $pvalue['price_amount'] = "";
                            }
                            if ($price_amount > 0) {
                                $pvalue['price_amount'] = $price_amount;
                            }
                            $this->db->insert('tblpaymenttemplatedetails', array(
                                'paymentscheduleid' => $id,
                                'duedate_type' => $pvalue['duedate_type'],
                                'duedate_number' => $pvalue['duedate_number'],
                                'custom_range_duration' => $pvalue['custom_range_duration'],
                                'duedate_criteria' => $pvalue['duedate_criteria'],
                                'price_type' => $pvalue['price_type'],
                                'price_amount' => $pvalue['price_amount'],
                                'price_percentage' => $pvalue['price_percentage'],
                                'payment_method' => $pvalue['payment_method'],
                                'order' => $pvalue['order']
                            ));
                        } else {
                            if ($pvalue['price_type'] == "fixed_amount") {
                                $pvalue['price_percentage'] = "";
                            } elseif ($pvalue['price_type'] == "divide_equally") {
                                $pvalue['price_percentage'] = "";
                                $pvalue['price_amount'] = "";
                            } elseif ($pvalue['price_type'] == "percentage") {
                                $pvalue['price_amount'] = "";
                            }
                            if ($price_amount > 0) {
                                $pvalue['price_amount'] = $price_amount;
                            }
                            $this->db->insert('tblpaymenttemplatedetails', array(
                                'paymentscheduleid' => $id,
                                'duedate_type' => $pvalue['duedate_type'],
                                'duedate_date' => $duedate_date,
                                'duedate_criteria' => $pvalue['duedate_criteria'],
                                'price_type' => $pvalue['price_type'],
                                'price_amount' => $pvalue['price_amount'],
                                'price_percentage' => $pvalue['price_percentage'],
                                'payment_method' => $pvalue['payment_method'],
                                'order' => $pvalue['order']
                            ));
                        /*}*/
                    }
                }
                $affectedRows++;
            }

            if ($affectedRows > 0) {
                logActivity('Payment Schedule Template Updated [ID: ' . $id . '.' . $data['name'] . ']');

                return true;
            }
        }
        return false;
    }

    /**
     * Get employee paymentschedule by id
     * @param  mixed $id Optional paymentschedule id
     * @return mixed     array if not id passed else object
     */
    public function getpaymentschedules($id = '', $istemplate = "")
    {
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('templateid', $id);
            $paymenttemplate = $this->db->get('tblpaymenttemplates')->row();

            $this->db->where('paymentscheduleid', $id);
            //$this->db->order_by('duedate_date', 'asc');
            $this->db->order_by('order', 'asc');
            $paymenttemplateschedules = $this->db->get('tblpaymenttemplatedetails')->result_array();

            $paymenttemplate->schedules = $paymenttemplateschedules;
            return $paymenttemplate;
        }
        if ($istemplate == 1) {
            $this->db->where('is_template', 1);
        }
        $this->db->order_by('datecreated', 'desc');
        $result = $this->db->get('tblpaymenttemplates')->result_array();
        return $result;
    }

    /**
     * Delete employee paymentschedule
     * @param  mixed $id paymentschedule id
     * @return mixed
     */
    public function deletepaymentschedule($id)
    {
        //$current = $this->getpaymentschedules($id);
        // Check first if paymentschedule is used in table
        // if (is_reference_in_table('paymentschedule_id', 'tblroleuserpaymentschedule', $id)) {
        //     return array(
        //         'referenced' => true
        //     );
        // }
        $affectedRows = 0;
        // $this->db->where('templateid', $id);
        // $this->db->delete('tblpaymenttemplates');
        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('templateid', $id);
        $this->db->update('tblpaymenttemplates', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            logActivity('Payment Schedule Template Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }

    /**
     * Get paymentschedule id
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function check_paymentschedule_name_exists($name, $id = 0, $brandid = "")
    {
        if (empty($brandid)) {
            $brandid = get_user_session();
        }
        if ($id > 0) {
            $where = array('templateid !=' => $id, 'name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid, 'is_template' => 1);
        } else {
            $where = array('name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid, 'is_template' => 1);
        }
        return $this->db->where($where)->get('tblpaymenttemplates')->row();
    }

    public function getpaymentschedule($id)
    {
        /*$this->db->where('brandid', get_user_session());*/
        $this->db->where('deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('templateid', $id);
            $paymenttemplate = $this->db->get('tblpaymenttemplates')->row();

            $this->db->where('paymentscheduleid', $id);
            $this->db->order_by('order', 'asc');
            $paymenttemplateschedules = $this->db->get('tblpaymenttemplatedetails')->result_array();

            $paymenttemplate->schedules = $paymenttemplateschedules;
            return $paymenttemplate;
        }
        $this->db->order_by('datecreated', 'desc');
        return $this->db->get('tblpaymenttemplates')->result_array();
    }

    public function updateschedule($data, $id)
    {

        $brandid = get_user_session();
        $paymentsched = (array)$this->getpaymentschedules($id);

        if (isset($data['brandid']) && $data['brandid'] > 0) {
            $brandid = $data['brandid'];
        }
        if (!isset($data['name'])) {
            $data['name'] = $paymentsched['name'];
        }
        $paymentschedules = array();
        if (isset($data['is_template']) && $data['is_template'] == 1) {
            $paymentschedules = $this->paymentschedules_model->check_paymentschedule_name_exists($data['name'], $id, $brandid);
        }
        if ((isset($paymentschedules->templateid) && $paymentschedules->templateid <= 0) || empty($paymentschedules)) {
            $payment_schedule = $data['payment_schedule'];
            unset($data['payment_schedule']);
            $affectedRows = 0;
            unset($data['paymentscheduleid']);
            $data['updated_by'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $this->db->where('templateid', $id);
            $this->db->update('tblpaymenttemplates', $data);
            $ptemplae_updated = $this->db->affected_rows();
            /*$this->db->where('paymentscheduleid', $id);
            $this->db->delete('tblpaymenttemplatedetails');*/
            if ($ptemplae_updated > 0) {
                foreach ($payment_schedule as $pvalue) {
                    $price_amount = $pvalue['price_amount'];
                    $duedate_date = date('Y-m-d');
                    if (isset($pvalue['duedate_date']) && !empty($pvalue['duedate_date'])) {
                        $pvalue['duedate_date'] = date_create($pvalue['duedate_date']);
                        $duedate_date = date_format($pvalue['duedate_date'], 'Y-m-d');
                    }
                    if (empty($pvalue['payment_method'])) {
                        $pvalue['payment_method'] = "cash";
                    }
                    if ($pvalue['duedate_type'] == 'custom') {
                        if ($pvalue['price_type'] == "fixed_amount") {
                            $pvalue['price_percentage'] = "";
                        } elseif ($pvalue['price_type'] == "divide_equally") {
                            $pvalue['price_percentage'] = "";
                            $pvalue['price_amount'] = "";
                        } elseif ($pvalue['price_type'] == "percentage") {
                            $pvalue['price_amount'] = "";
                        }
                        if ($price_amount > 0) {
                            $pvalue['price_amount'] = $price_amount;
                        }
                        if ($pvalue['paymentdetailid'] > 0) {
                            $this->db->where('paymentdetailid', $pvalue['paymentdetailid']);
                            $this->db->update('tblpaymenttemplatedetails', array(
                                'paymentscheduleid' => $id,
                                'duedate_type' => $pvalue['duedate_type'],
                                'duedate_number' => $pvalue['duedate_number'],
                                'custom_range_duration' => $pvalue['custom_range_duration'],
                                'duedate_criteria' => $pvalue['duedate_criteria'],
                                'price_type' => $pvalue['price_type'],
                                'price_amount' => $pvalue['price_amount'],
                                'price_percentage' => $pvalue['price_percentage'],
                                'payment_method' => $pvalue['payment_method'],
                                'order' => $pvalue['order']
                            ));
                        } else {
                            $this->db->insert('tblpaymenttemplatedetails', array(
                                'paymentscheduleid' => $id,
                                'duedate_type' => $pvalue['duedate_type'],
                                'duedate_number' => $pvalue['duedate_number'],
                                'custom_range_duration' => $pvalue['custom_range_duration'],
                                'duedate_criteria' => $pvalue['duedate_criteria'],
                                'price_type' => $pvalue['price_type'],
                                'price_amount' => $pvalue['price_amount'],
                                'price_percentage' => $pvalue['price_percentage'],
                                'payment_method' => $pvalue['payment_method'],
                                'order' => $pvalue['order']
                            ));
                        }
                    } else {
                        if ($pvalue['price_type'] == "fixed_amount") {
                            $pvalue['price_percentage'] = "";
                        } elseif ($pvalue['price_type'] == "divide_equally") {
                            $pvalue['price_percentage'] = "";
                            $pvalue['price_amount'] = "";
                        } elseif ($pvalue['price_type'] == "percentage") {
                            $pvalue['price_amount'] = "";
                        }
                        if ($price_amount > 0) {
                            $pvalue['price_amount'] = $price_amount;
                        }
                        if ($pvalue['paymentdetailid'] > 0) {
                            $this->db->where('paymentdetailid', $pvalue['paymentdetailid']);
                            $this->db->update('tblpaymenttemplatedetails', array(
                                'paymentscheduleid' => $id,
                                'duedate_type' => $pvalue['duedate_type'],
                                'duedate_date' => $duedate_date,
                                'duedate_criteria' => $pvalue['duedate_criteria'],
                                'price_type' => $pvalue['price_type'],
                                'price_amount' => $pvalue['price_amount'],
                                'price_percentage' => $pvalue['price_percentage'],
                                'payment_method' => $pvalue['payment_method'],
                                'order' => $pvalue['order']
                            ));
                        } else {
                            $this->db->insert('tblpaymenttemplatedetails', array(
                                'paymentscheduleid' => $id,
                                'duedate_type' => $pvalue['duedate_type'],
                                'duedate_date' => $duedate_date,
                                'duedate_criteria' => $pvalue['duedate_criteria'],
                                'price_type' => $pvalue['price_type'],
                                'price_amount' => $pvalue['price_amount'],
                                'price_percentage' => $pvalue['price_percentage'],
                                'payment_method' => $pvalue['payment_method'],
                                'order' => $pvalue['order']
                            ));
                        }
                    }

                }
                $affectedRows++;
            }

            if ($affectedRows > 0) {
                logActivity('Payment Schedule Template Updated [ID: ' . $id . '.' . $data['name'] . ']');

                return true;
            }
        }
        return false;
    }

    function updatepayment($paymentid, $pscheduleid)
    {
        if ($paymentid == 0) {
            $this->db->where('paymentscheduleid', $pscheduleid);
            $this->db->update('tblpaymenttemplatedetails', array(
                'status' => 1
            ));
        } else {
            $this->db->where('paymentdetailid', $paymentid);
            $this->db->update('tblpaymenttemplatedetails', array(
                'status' => 1
            ));
        }
    }
}