<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Paypal_gateway extends App_gateway
{
    public function __construct()
    {
        /**
         * Call App_gateway __construct function
         */
        parent::__construct();
        /**
         * REQUIRED
         * Gateway unique id
         * The ID must be alpha/alphanumeric
         */
        $this->setId('paypal');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Paypal');

        /**
         * Add gateway settings
        */
        $this->setSettings(
        array(
            array(
                'name'=>'username',
                'encrypted'=>true,
                'label'=>'settings_paymentmethod_paypal_username',
                ),
            array(
                'name'=>'password',
                'encrypted'=>true,
                'label'=>'settings_paymentmethod_paypal_password',
                ),
            array(
                'name'=>'signature',
                'encrypted'=>true,
                'label'=>'settings_paymentmethod_paypal_signature',
                ),
            //  array(
            //     'name' => 'description_dashboard',
            //     'label' => 'settings_paymentmethod_description',
            //     'type'=>'textarea',
            //     'default_value'=>'Payment for Invoice'
            // ),
            array(
                'name'=>'currencies',
                'label'=>'settings_paymentmethod_currencies',
                'default_value'=>'USD',
                ),
            array(
                'name'=>'test_mode_enabled',
                'type'=>'yes_no',
                'default_value'=>1,
                'label'=>'settings_paymentmethod_testing_mode',
                ),
            )
        );

        /**
         * REQUIRED
         * Hook gateway with other online payment modes
         */
        //add_action('before_add_online_payment_modes', array( $this, 'initMode' ));
    }

    /**
     * REQUIRED FUNCTION
     * @param  array $data
     * @return mixed
     */
    public function process_payment($data)
    {
        //var_dump($data);
        // Process online for PayPal payment start
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername($this->decryptSetting('username'));
        $gateway->setPassword($this->decryptSetting('password'));
        $gateway->setSignature($this->decryptSetting('signature'));

        $gateway->setTestMode($this->getSetting('test_mode_enabled'));
        $gateway->setlogoImageUrl(do_action('paypal_logo_url', site_url('uploads/company/logo.png')));
        $gateway->setbrandName(get_option('companyname'));
        //var_dump($gateway);die;

        $request_data = array(
            'amount' => number_format($data['amount'], 2, '.', ''),
            'returnUrl' => site_url('gateways/paypal/complete_purchase?hash=' . $data['invoice']->hash . '&invoiceid=' . $data['invoiceid']),
            'cancelUrl' => site_url('viewinvoice/' . $data['invoiceid'] . '/' . $data['invoice']->hash),
            'currency' => $data['invoice']->currency_name,
            'description' =>$this->getSetting('description_dashboard') . ' - ' . format_invoice_number($data['invoiceid']),
            );
        try {
            $response = $gateway->purchase($request_data)->send();
            if ($response->isRedirect()) {
                $this->ci->session->set_userdata(array(
                    'online_payment_amount' => number_format($data['amount'], 2, '.', ''),
                    'currency' => $data['invoice']->currency_name,
                    ));
                // Add the token to database
                $this->ci->db->where('id', $data['invoiceid']);
                $this->ci->db->update('tblinvoices', array(
                    'token' => $response->getTransactionReference()
                ));
                $response->redirect();
            } else {
                exit($response->getMessage());
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . '<br />';
            exit('Sorry, there was an error processing your payment. Please try again later.');
        }
    }

    /**
     * Custom function to complete the payment after user is returned from paypal
     * @param  array $data
     * @return mixed
     */
    public function complete_purchase($data)
    {   
        $gateway = Omnipay::create('PayPal_Express');
        $gateway->setUsername($this->decryptSetting('username'));
        $gateway->setPassword($this->decryptSetting('password'));
        $gateway->setSignature($this->decryptSetting('signature'));
        $gateway->setTestMode($this->getSetting('test_mode_enabled'));

        $response       = $gateway->completePurchase(array(
            'transactionReference' => $data['token'],
            'payerId' => $this->ci->input->get('PayerID'),
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            ))->send();

        $paypalResponse = $response->getData();

        return $paypalResponse;
    }

    /**
     * REQUIRED FUNCTION
     * @param  array $data
     * @return mixed
     */
    public function subscription_process_payment($data)
    {
        // Process online for PayPal payment start
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername($this->decryptAppSetting('username'));
        $gateway->setPassword($this->decryptAppSetting('password'));
        $gateway->setSignature($this->decryptAppSetting('signature'));

        $gateway->setTestMode($this->getAppSetting('test_mode_enabled'));
        $gateway->setlogoImageUrl(do_action('paypal_logo_url', site_url('uploads/company/logo.png')));
        $gateway->setbrandName(get_option('companyname'));
        
        //for registration subscription
        if(isset($data['brand_id'])) {
            $request_data = array(
                'amount' => number_format($data['amount'], 2, '.', ''),
                'returnUrl' => site_url('gateways/paypal/complete_subscription_purchase?hash=' . $data['subscription']->hash . '&packageid=' . $data['packageid'] . '&brand_id=' . $data['brand_id'] . '&new_user_id=' . $data['new_user_id']),
                'cancelUrl' => admin_url('subscription'),
                'currency' => 'USD',
                'description' =>_l("paypal_subscrption_title") . ' - ' . $data['subscription']->name,
            );
        } else {
            $request_data = array(
                'amount' => number_format($data['amount'], 2, '.', ''),
                'returnUrl' => site_url('gateways/paypal/complete_subscription_purchase?hash=' . $data['subscription']->hash . '&packageid=' . $data['packageid']),
                'cancelUrl' => admin_url('subscription'),
                'currency' => 'USD',
                'description' =>_l("paypal_subscrption_title") . ' - ' . $data['subscription']->name,
            );
        }
        try {
            $response = $gateway->purchase($request_data)->send();

            if ($response->isRedirect()) {
                $this->ci->session->set_userdata(array(
                    'online_payment_amount' => $data['amount'],
                    'currency' => 'USD',
                    ));
                // Add the token to database
                $this->ci->db->where('packageid', $data['packageid']);
                $this->ci->db->update('tblpackages', array(
                    'token' => $response->getTransactionReference()
                ));
               
                $response->redirect();
            } else {
                exit($response->getMessage());
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . '<br />';
            exit('Sorry, there was an error processing your payment. Please try again later.');
        }
    }


    /**
     * Custom function to complete the payment after user is returned from paypal
     * @param  array $data
     * @return mixed
     */
    public function complete_subscription_purchase($data)
    {
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername($this->decryptAppSetting('username'));
        $gateway->setPassword($this->decryptAppSetting('password'));
        $gateway->setSignature($this->decryptAppSetting('signature'));
        $gateway->setTestMode($this->getAppSetting('test_mode_enabled'));
           
        $response       = $gateway->completePurchase(array(
            'transactionReference' => $data['token'],
            'payerId' => $this->ci->input->get('PayerID'),
            'amount' => number_format($data['amount'], 2, '.', ''),
            'currency' => $data['currency'],
            ))->send();
        //var_dump($response->getData());
        $paypalResponse = $response->getData();

        return $paypalResponse;
    }

}
