<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Payments extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payments_model');
    }

    /* In case if user go only on /payments*/
    public function index($clientid = false)
    {
        $this->list_payments($clientid);
    }

    /* List all invoice paments */
    public function list_payments($clientid = false)
    {
        if (!has_permission('account_setup', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('account_setup');
        }

        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('payments', array(
                'clientid' => $clientid
            ));
        }
        $data['title'] = _l('payments');
        $this->load->view('admin/payments/manage', $data);
    }

    /* Update payment data */
    public function payment($id = '')
    {
        if (!has_permission('account_setup', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('payments');
        }

        if (!$id) {
            redirect(admin_url('payments'));
        }
        if ($this->input->post()) {
            if (!has_permission('account_setup', '', 'edit', true)) {
                access_denied('Update Payment');
            }
            $success = $this->payments_model->update($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('payment')));
            }
            redirect(admin_url('payments/payment/' . $id));
        }
        $data['payment'] = $this->payments_model->get($id);
        if (!$data['payment']) {
            blank_page(_l('payment_not_exists'));
        }
        $this->load->model('invoices_model');
        $data['invoice'] = $this->invoices_model->get($data['payment']->invoiceid);
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', array(), true, true);
        $i                     = 0;
        foreach ($data['payment_modes'] as $mode) {
            if ($mode['active'] == 0 && $data['payment']->paymentmode != $mode['id']) {
                unset($data['payment_modes'][$i]);
            }
            $i++;
        }
        $data['title'] = _l('payment_receipt') . ' - ' . format_invoice_number($data['payment']->invoiceid);
        $this->load->view('admin/payments/payment', $data);
    }

    /**
     * Generate payment pdf
     * @since  Version 1.0.1
     * @param  mixed $id Payment id
     */
    public function pdf($id)
    {
        if (!has_permission('account_setup', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('View Payment');
        }
        $payment = $this->payments_model->get($id);
        $this->load->model('invoices_model');
        $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
        if (isset($payment->invoice_data->client->phone->phone)) {                      
            $payment->invoice_data->phonenumber = $payment->invoice_data->client->phone->phone;
        }else {
            $payment->invoice_data->phonenumber = '';
        }
        try {
            $paymentpdf            = payment_pdf($payment);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if(strpos($message, 'Unable to get the size of the image') !== FALSE) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type                  = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        
        $paymentpdf->Output(mb_strtoupper(slug_it(_l('payment') . '-' . $payment->paymentid)) . '.pdf', $type);
    }

    /* Delete payment */
    public function delete($id)
    {
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('Delete Payment');
        }
        if (!$id) {
            redirect(admin_url('payments'));
        }
        $response = $this->payments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }
        redirect(admin_url('payments'));
    }
}
