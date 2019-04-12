<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Omnipay\Omnipay;

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

class Stripe_gateway extends App_gateway
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
        $this->setId('stripe');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Stripe');

        /**
         * Add gateway settings
        */
        $this->setSettings(array(
            array(
                'name' => 'api_secret_key',
                'encrypted' => true,
                'label' => 'settings_paymentmethod_stripe_api_secret_key'
            ),
            array(
                'name' => 'api_publishable_key',
                'label' => 'settings_paymentmethod_stripe_api_publishable_key'
            ),
            // array(
            //     'name' => 'description_dashboard',
            //     'label' => 'settings_paymentmethod_description',
            //     'type'=>'textarea',
            //     'default_value'=>'Payment for Invoice'
            // ),
            array(
                'name' => 'currencies',
                'label' => 'settings_paymentmethod_currencies',
                'default_value' => 'USD',
            ),
            array(
                'name' => 'test_mode_enabled',
                'type' => 'yes_no',
                'default_value' => 1,
                'label' => 'settings_paymentmethod_testing_mode'
            )
        ));

        /**
         * REQUIRED
         * Hook gateway with other online payment modes
         */
        add_action('before_add_online_payment_modes', array( $this, 'initMode' ));


    }

    public function process_payment($data)
    {
        redirect(site_url('gateways/stripe/make_payment?invoiceid=' . $data['invoiceid'] . '&total=' . $data['amount'] . '&hash=' . $data['invoice']->hash . '&custid=' . $data['custid']));
    }

    public function finish_payment($data)
    {
        $CI =& get_instance();
        $getaccount_row = $CI->db->query('SELECT `accountid` FROM `tblinvoicetransactioncharge` WHERE `isaccepted` = 1 AND `brandid` = ' . get_user_session())->row();
        $accountid = $getaccount_row->accountid;

        $fee_amount         = (($data['amount'] * 2.9) / 100 ) + 0.30;
        $transfer_amount    = round(((($data['amount'] * 3) / 100 ) - $fee_amount),2) * 100;

        // Process online for stripe payment start
        $gateway = Omnipay::create('Stripe');    
        $gateway->setApiKey($this->decryptSetting('api_secret_key'));

        $gateway2 = Omnipay::create('Stripe');    
        $gateway2->setApiKey($this->decryptAppSetting('api_secret_key'));
        $sido_account_key = $this->decryptAppSetting('api_secret_key');

        //set authorization bearer
        $authorization  = "Authorization: Bearer ".$sido_account_key;

        //set account id in header
        $account_access = "Stripe-Account: ".$accountid;
        
        if(!empty($accountid)) {

            if($data['custid'] == '0') {
                if(isset($data['stripeEmail'])){
                    $customer = array(
                        'description'       => $data['description'],
                        'email'             => $data['stripeEmail'],
                        'source'            => $data['stripeToken']
                    );
                }else{
                    $customer = array(
                        'description'       => $data['description'],
                        'source'            => $data['stripeToken']
                    );
                }
                $response = $gateway->createCustomer($customer)->send();
                if ($response->isSuccessful()) {
                    $customer_response = $response->getData();
                    
                    // Find the customer ID
                    $customer_id = $response->getCustomerReference();
                    //prepare request body
                    $token_request_body = array(
                        'amount'            => $data['amount'] * 100,
                        'currency'          => $data['currency'],
                        'application_fee'   => ($transfer_amount > 0) ? $transfer_amount : 0,
                        'customer'          => $customer_id 
                    );

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,"https://api.stripe.com/v1/charges");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, $account_access ));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $server_output = curl_exec ($ch);

                    if(curl_error($ch)) {
                        $error = curl_error($ch);
                        return $error;
                    }

                    curl_close ($ch);

                    $oResponse = json_decode($server_output);
                    
                    return $oResponse;
                } else {
                    return $response->getMessage();
                }
            } else {
                $response = $gateway->fetchCustomer(array(
                    'customerReference'       =>  $data['custid']
                ))->send();

                $customer = $response->getData();
                //$source = $customer['default_source'];
                
                //prepare request body
                $token_request_body = array(
                    'amount'            => $data['amount'] * 100,
                    'currency'          => $data['currency'],
                    'application_fee'   => ($transfer_amount > 0) ? $transfer_amount : 0,
                    'customer'          => $data['custid'] 
                );
                
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL,"https://api.stripe.com/v1/charges");
                curl_setopt($ch, CURLOPT_POST, 1);
                //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, $account_access ));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $server_output = curl_exec ($ch);

                if(curl_error($ch)) {
                    $error = curl_error($ch);
                    return $error;
                }

                curl_close ($ch);
                $oResponse = json_decode($server_output);
                
                return $oResponse;
            }
        } else {
            $transact_error = new stdClass();
            $transact_error->error->param  = "connected_account";
            $transact_error->error->message = "Account not connected to SiDO platform";
            return $transact_error;
        }
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/26/2018
    * to create subscription payment
    */
    public function finish_subscription_payment($data)
    {
        // Process online for stripe payment start
        $gateway = Omnipay::create('Stripe');

        $gateway->setApiKey($this->decryptAppSetting('api_secret_key'));
        
        //get all plans for subscription in stripe
        $authorization  = 'Authorization: Bearer '.$this->decryptAppSetting('api_secret_key');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.stripe.com/v1/plans");
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        if(curl_error($ch)) {
            $error = curl_error($ch);
            return $error;
        }

        curl_close ($ch);
        $plan_response = json_decode($server_output);
        
        $plan_details = null;
        //loop through plans and get subscribed plan id
        foreach($plan_response->data as $response) {
            if ($data['packagename'] == $response->nickname) {
                $plan_details = $response;
                break;
            }
        }

        //if stripe plan id exists, create customer and subscription
        if(!empty($plan_details->id)) {
            if($data['custid'] == 0) {
                $response = $gateway->createCustomer(array(
                    'description'       =>  $data['description'],
                    'email'             =>  $data['stripeEmail'],
                    'source'            => $data['stripeToken']
                ))->send();

                if ($response->isSuccessful()) {
                    // Find the customer ID
                    $customer_id = $response->getCustomerReference();
                    

                    //create subscription for customer
                    $sub_response = $subscription_response = $gateway->createSubscription(array(
                        'customerReference'     => $customer_id,
                        'plan'                  => $plan_details->id
                    ))->send();
                     
                    $subscription_data  = $sub_response->getData();

                    $subscription_id    = $subscription_data['id'];
                    //plan id
                    if(isset($subscription_id)) {
                        $oResponse = $gateway->purchase(array(
                            'amount'            => number_format($data['amount'], 2, '.', ''),
                            'metadata'          => array(
                                'ClientID'      => $data['clientid']
                            ),
                            'description'       => $data['description'],
                            'currency'          => $data['currency'],
                            //'token'             => $data['stripeToken'],
                            'customerReference' => $customer_id
                        ))->send();
                        
                        return $oResponse;
                    } else {
                        return $sub_response->getMessage();
                    }
                } else {
                    return $response->getMessage();
                }    
            } else {
                //update subscription for customer
                $sub_response = $subscription_response = $gateway->updateSubscription(array(
                    'customerReference'     => $data['custid'],
                    'plan'                  => 'plan_CZ3KcGBXzVL7T2'
                ))->send();
                
                $subscription_data  = $sub_response->getData();
                $subscription_id    = $subscription_data['id'];
                //plan id
                if(isset($subscription_id)) {
                    $oResponse = $gateway->purchase(array(
                        'amount'        => number_format($data['amount'], 2, '.', ''),
                        'metadata'      => array(
                            'ClientID'  => $data['clientid']
                        ),
                        'description'   => $data['description'],
                        'currency'      => $data['currency'],
                        'token'         => $data['stripeToken'],
                        'custid'        => $customer_id
                    ))->send();

                    return $oResponse;
                } else {
                    return $sub_response->getMessage();
                    }
            }
        } else {
            //throw error if plan does not exists
            $oResponse = new stdClass();
            $oResponse->error->param  = "no_plan";
            $oResponse->error->message = "No Plans exists in Stripe Account";
            return $oResponse;
        }
    }

    public function subscription_process_payment($data)
    {
        //for registration subscription
        if(isset($data['brand_id'])) {
            redirect(site_url('gateways/stripe/subscription_make_payment?packageid=' . $data['invoiceid'] . '&total=' . $data['amount'] . '&hash='. $data['subscription']->hash . '&custid=' . $data['custid'] . '&brand_id=' . $data['brand_id'] . '&new_user_id=' . $data['new_user_id']));
        } else {
            redirect(site_url('gateways/stripe/subscription_make_payment?packageid=' . $data['invoiceid'] . '&total=' . $data['amount'] . '&hash='. $data['subscription']->hash . '&custid=' . $data['custid']));
        }
    }
    
    /**
    * Added By: Vaidehi
    * Dt: 03/27/2018
    * to cancel subscription
    */
    public function cancel_subscription($data)
    {
        // Process online for stripe payment start
        $gateway = Omnipay::create('Stripe');

        $gateway->setApiKey($this->decryptSetting('api_secret_key'));

        $response = $gateway->cancelSubscription(array(
            'customerReference'     => $data['custid'],   
            'subscriptionReference' => $data['subscriptionid']
        ))->send();

        $oResponse = $response->getData();
        return $oResponse;
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/27/2018
    * to get customer details
    */
    public function get_customer_details($data)
    {
         // Process online for stripe payment start
        $gateway = Omnipay::create('Stripe');

        $gateway->setApiKey($this->decryptAppSetting('api_secret_key'));

        $response = $gateway->fetchCustomer(array(
            'customerReference'       =>  $data
        ))->send();
        
        $customer = $response->getData();

        $subscription = $customer['subscriptions']['data'][0]['id'];

        return $subscription;
    }
}
