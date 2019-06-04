<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stripe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function complete_purchase()
    {
        if ($this->input->post()) {
            $data      = $this->input->post();
            $total     = $this->input->post('total');
            $this->load->model('invoices_model');
            $invoice             = $this->invoices_model->get($this->input->post('invoiceid'));
            check_invoice_restrictions($invoice->id, $invoice->hash);
            load_client_language($invoice->clientid);

            $data['amount']      = $total;
            $data['description'] = $this->stripe_gateway->getSetting('description_dashboard', 'Stripe') . ' - ' . format_invoice_number($invoice->id);

            $data['currency']    = $invoice->currency_name;
            $data['clientid']    = $invoice->clientid;
            $oResponse      = $this->stripe_gateway->finish_payment($data);
            if (!empty($oResponse->status)) {
                //$transactionid  = $oResponse->getTransactionReference();
                $transactionid  = $oResponse->id;
                //$oResponse = $oResponse->getData();
                if ($oResponse->status == 'succeeded') {

                    $success = $this->stripe_gateway->addPayment(
                    array(
                      'amount'          => ($oResponse->amount / 100),
                      'invoiceid'       => $invoice->id,
                      'transactionid'   => $transactionid,
                      'custid'          => $oResponse->customer
                      ));

                    if ($success) {
                        set_alert('success', _l('online_payment_recorded_success'));
                    } else {
                        set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                    }

                    redirect(site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash));
                }
            //} elseif (!empty($oResponse->isRedirect())) {
                //$oResponse->redirect();
            } else {
                $message = $oResponse->error->param . " ". $oResponse->error->message;
                set_alert('danger', $message);
                redirect(site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash));
            }
        }
    }

    public function make_payment()
    {
        check_invoice_restrictions($this->input->get('invoiceid'), $this->input->get('hash'));
        $this->load->model('invoices_model');
        $invoice      = $this->invoices_model->get($this->input->get('invoiceid'));
        load_client_language($invoice->clientid);
        $data['invoice']      = $invoice;
        $data['total']        = $this->input->get('total');
        $data['custid']       = $this->input->get('custid');
        //$data['charge']       = $invoice->transaction_charge;
        
        echo $this->get_view($data);
    }
    
    public function get_view($data = array()){ ?>
        <?php echo payment_gateway_head(_l('payment_for_invoice') . ' ' . format_invoice_number($data['invoice']->id)); ?>
        <body class="gateway-stripe">
            <div class="container">
                <div class="col-md-8 col-md-offset-2 mtop30">
                  <div class="mbot30 text-center">
                      <?php echo get_brand_logo('','',$data['invoice']->brandid); ?>
                    </div>
                    <div class="row">
                        <div class="panel_s">
                            <div class="panel-body">
                               <h4 class="no-margin">
                                  <?php echo _l('payment_for_invoice'); ?>
                                  <a href="<?php echo site_url('viewinvoice/'. $data['invoice']->id . '/' . $data['invoice']->hash); ?>">
                                  <?php echo format_invoice_number($data['invoice']->id); ?>
                                  </a>
                              </h4>
                              <hr />
                              <p>
                                  <span class="bold">
                                    <?php echo _l('payment_total',format_money($data['total'],$data['invoice']->symbol)); ?>
                                  </span>
                              </p>
                              <?php
                              $form = '<form action="' . site_url('gateways/stripe/complete_purchase') . '" method="POST">
                                <script
                                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                data-key="' . $this->stripe_gateway->getSetting('api_publishable_key') . '"
                                data-amount="' . ($data['total'] * 100). '"
                                data-name="' . get_brand_option('companyname', $data['invoice']->brandid) . '"
                                data-description=" '. _l('payment_for_invoice') . ' ' . format_invoice_number($data['invoice']->id) . '";
                                data-locale="auto"
                                data-currency="'.$data['invoice']->currency_name.'"
                                >
                            </script>
                            '.form_hidden('invoiceid',$data['invoice']->id).'
                            '.form_hidden('total',$data['total']).'
                            '.form_hidden('custid',$data['custid']).'
                        </form>';
                        echo $form;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo payment_gateway_scripts(); ?>
        <script>
            $(function(){
                $('.stripe-button-el').click();
            });
        </script>
        <?php echo payment_gateway_footer(); ?>
    <?php
    } 

    public function subscription_make_payment()
    {
        $this->load->model('packages_model');
        $invoice      = $this->packages_model->get($this->input->get('packageid'));
        
        $data['invoice']      = $this->input->get('packageid');
        $data['total']        = $this->input->get('total');
        $data['hash']         = $this->input->get('hash');
        $data['custid']       = $this->input->get('custid');
        $data['packagename']  = $invoice->name;

        //for registration subscription
        if(!empty($this->input->get('brand_id'))) {
            $data['brand_id']       = $this->input->get('brand_id');
            $data['new_user_id']    = $this->input->get('new_user_id');
        } else {
            $data['brand_id']       = 0;
            $data['new_user_id']    = 0;
        }
        
        echo $this->get_subscription_view($data);
    }

    public function get_subscription_view($data = array()){  ?>
        <?php echo subscription_payment_gateway_head(_l('payment_for_subscription') . ' ' . $data['packagename']); ?>
        <body class="gateway-stripe">
            <div class="container">
                <div class="col-md-8 col-md-offset-2 mtop30">
                  <div class="mbot30 text-center">
                      <?php echo get_company_logo('',''); ?>
                    </div>
                    <div class="row">
                        <div class="panel_s">
                            <div class="panel-body">
                               <h4 class="no-margin">
                                  <?php echo _l('payment_for_subscription'); ?>
                                  <a href="<?php echo site_url('viewinvoice/'. $data['invoice'] . '/' . $data['hash']); ?>">
                                  <?php echo $data['packagename']; ?>
                                  </a>
                              </h4>
                              <hr />
                              <p>
                                  <span class="bold">
                                    <?php echo _l('payment_total')."$".$data['total']; ?>
                                  </span>
                              </p>
                              <?php
                              $form = '<form action="' . site_url('gateways/stripe/complete_subscription_purchase') . '" method="POST">
                                <script
                                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                data-key="' . $this->stripe_gateway->getAppSetting('api_publishable_key') . '"
                                data-amount="' . ($data['total'] * 100). '"
                                data-description=" '. _l('payment_for_subscription') . ' - ' . $data['packagename'] . '"
                                data-locale="auto"
                                data-currency="USD"
                                >
                            </script>
                            '.form_hidden('invoiceid',$data['invoice']).'
                            '.form_hidden('total',$data['total']).'
                            '.form_hidden('custid',$data['custid']).'
                            '.form_hidden('brand_id',$data['brand_id']).'
                            '.form_hidden('new_user_id',$data['new_user_id']).'
                        </form>';
                        echo $form;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo payment_gateway_scripts(); ?>
        <script>
            $(function(){
                $('.stripe-button-el').click();
            });
        </script>
        <?php echo payment_gateway_footer(); ?>
    <?php
    } 

    public function complete_subscription_purchase()
    {
        if ($this->input->post()) {
            $data      = $this->input->post();
            $total     = $this->input->post('total');
            $this->load->model('invoices_model');
            $invoice             = $this->packages_model->get($this->input->post('invoiceid'));
            //check_invoice_restrictions($invoice->id, $invoice->hash);
            //load_client_language($invoice->clientid);
            
            $data['amount']      = $total;
            $data['description'] = "Payment subscription" . ' - ' . $invoice->name;
            $data['method']      = 'Subscription';
            $data['currency']    = "USD";
            $data['clientid']    = $invoice->packageid;
            $data['packagename'] = $invoice->name;
            $oResponse           = $this->stripe_gateway->finish_subscription_payment($data);
            
            if(!empty($oResponse->error)) {
                $message = $oResponse->error->param . " ". $oResponse->error->message;
                set_alert('danger', $message);
                redirect(site_url('admin/subscription'));
            } else {
                $response_data       = $oResponse->getData();
                
                if (!empty($response_data['id'])) {
                    //$transactionid  = $oResponse->getTransactionReference();
                    //$oResponse = $oResponse->getData();
                    if ($response_data['status'] == 'succeeded') {
                        $subscription = $this->stripe_gateway->get_customer_details($response_data['customer']);

                        $CI =& get_instance();
                        $sess_id = $CI->session->userdata('brand_id');
                        //for registration subscription
                        if(empty($sess_id)) {
                            $return_data = $this->stripe_gateway->addPayment(
                            array(
                                'amount'          => ($response_data['amount'] / 100),
                                'packageid'       => $invoice->packageid,
                                'transactionid'   => $response_data['id'],
                                'custid'          => $response_data['customer'],
                                'subscriptionid'  => $subscription,
                                'brand_id'        => $data['brand_id'],
                                'new_user_id'     => $data['new_user_id']
                            ),"subscription");
                        } else {
                            $success = $this->stripe_gateway->addPayment(
                            array(
                                'amount'          => ($response_data['amount'] / 100),
                                'packageid'       => $invoice->packageid,
                                'transactionid'   => $response_data['id'],
                                'custid'          => $response_data['customer'],
                                'subscriptionid'  => $subscription
                            ),"subscription");

                            if ($success) {
                                set_alert('success', _l('online_payment_recorded_success'));
                            } else {
                                set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                            }

                            redirect(admin_url('subscription/subscription_option'));
                        }
                    }
                } elseif ($oResponse->isRedirect()) {
                    $oResponse->redirect();
                } else {
                    set_alert('danger', $oResponse->getMessage());
                    redirect(site_url('admin/subscription'));
                }   
            }
        }
    }
}
