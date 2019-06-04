<?php
/**
 * Added By : Vaidehi
 * Dt : 10/12/2017
 * For Brand Settings Module
 */
defined('BASEPATH') or exit('No direct script access allowed');

header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

class Brand_settings extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payment_modes_model');
        $this->load->model('brand_settings_model');
        $this->load->model('Register_model');
        $this->load->model('brands_model');
    }

    /* View all brand */
    public function index()
    {
        if (!has_permission('account_setup', '', 'view', true)) {
            access_denied('account_setup');
        }
        if ($this->input->post()) {
            if (!has_permission('account_setup', '', 'edit', true)) {
                access_denied('account_setup');
            }

            $BrandIcon_uploaded = (handle_brand_icon_upload() ? true : false);
            $logo_uploaded = (handle_brand_logo_upload() ? true : false);
            $banner_uploaded = (handle_brand_banner_upload() ? true : false);
            $favicon_uploaded = (handle_brand_favicon_upload() ? true : false);
            $signatureUploaded = (handle_brand_signature_upload() ? true : false);

            $post_data = $this->input->post(null, false);
            if(isset($post_data['imagebase64'])){
                unset($post_data['imagebase64']);
            }
            if(isset($post_data['favicon64'])){
                unset($post_data['favicon64']);
            }
            if(isset($post_data['bannerbase64'])){
                unset($post_data['bannerbase64']);
            }
            if(isset($post_data['brandimagebase64'])){
                unset($post_data['brandimagebase64']);
            }
            /*echo "<pre>";
            print_r($_FILES);
            print_r($_POST);
            die('<--here');*/
            /**
             * Added By : Vaidehi
             * Dt : 10/23/2017
             * to update theme style for specific brand
             */
            if (!empty($post_data['settings']['theme_style'])) {
                $theme_data = array();
                foreach ($post_data['settings']['theme_style'] as $key => $value) {
                    $style_data['id'] = $key;
                    $style_data['color'] = $value;

                    array_push($theme_data, $style_data);
                }

                $theme_style_data = json_encode($theme_data, true);
                $this->save_theme_style($theme_style_data);
                unset($post_data['settings']['theme_style']);
            }

            /**
             * Added By: Vaidehi
             * Dt :03/29/2018
             * to get stripe account id for transaction charge` W
             */
            if (!empty($post_data['settings']['paymentmethod_stripe_active'])) {
                if ($post_data['settings']['paymentmethod_stripe_active'] == 1) {
                    $brand_accountrow = $this->db->query('SELECT `accountid` FROM `tblinvoicetransactioncharge` WHERE `isaccepted` = 1 AND `brandid` = ' . get_user_session())->row();

                    if (!empty($brand_accountrow->accountid)) {
                        $success = $this->brand_settings_model->update($post_data);
                    } else {

                        if (isset($post_data['settings']['oauth_code'])) { // Redirect w/ code
                            $code = $post_data['settings']['oauth_code'];
                            $secret_key = $this->encryption->decrypt(get_option('paymentmethod_stripe_api_secret_key'));

                            $token_request_body = array(
                                //'client_id'     => get_option('paymentmethod_stripe_api_client_id'),
                                'grant_type' => 'authorization_code',
                                'code' => $code,
                                'client_secret' => $secret_key
                            );

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, "https://connect.stripe.com/oauth/token");
                            curl_setopt($ch, CURLOPT_POST, 1);
                            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            $server_output = curl_exec($ch);

                            if (curl_error($ch)) {
                                $error = curl_error($ch);
                            }

                            curl_close($ch);

                            $output = json_decode($server_output);;

                            if (!empty($output->error)) {
                                $authorize_request_body = array(
                                    'response_type' => 'code',
                                    'scope' => 'read_write',
                                    'client_id' => get_option('paymentmethod_stripe_api_client_id')
                                );

                                $url = 'https://connect.stripe.com/oauth/authorize' . '?' . http_build_query($authorize_request_body);
                                echo "<a href='$url' class='btn btn-info'>Connect with Stripe</a>";
                                die();
                            } else {
                                $account_id = $output->stripe_user_id;

                                $this->db->query('INSERT INTO `tblinvoicetransactioncharge`(`brandid`, `accountid`, `isaccepted`, `createdby`, `datecreated`) VALUES (' . get_user_session() . ', "' . $account_id . '", 1, ' . get_staff_user_id() . ', "' . date('Y-m-d H:i:s') . '")');
                                unset($post_data['settings']['oauth_code']);

                                $success = $this->brand_settings_model->update($post_data);
                            }
                        } else { // Show OAuth link

                            $authorize_request_body = array(
                                'response_type' => 'code',
                                'scope' => 'read_write',
                                'client_id' => get_option('paymentmethod_stripe_api_client_id')
                            );
                            $url = 'https://connect.stripe.com/oauth/authorize' . '?' . http_build_query($authorize_request_body);
                            echo "<a href='$url' class='btn btn-info'>Connect with Stripe</a>";
                            die();
                        }
                    }
                }
            } else {
                $success = $this->brand_settings_model->update($post_data);
            }

            if (isset($success['brandname'])) {
                if ($success['brandname'] == 'Brand name exists') {
                    set_alert('danger', _l('brand_name_not_change'));
                }
            }

            if ($success['affectedRows'] > 0) {
                set_alert('success', _l('settings_updated'));
            }

            if ($logo_uploaded || $favicon_uploaded || $banner_uploaded) {
                set_debug_alert(_l('logo_favicon_changed_notice'));
            }

            if ($this->input->post('pg') && $this->input->post('pg') == "home") {
                redirect(admin_url());
            }
            // Do hard refresh on general for the logo
            if ($this->input->get('group') == 'general') {
                redirect(admin_url('brand_settings?group=' . $this->input->get('group')), 'refresh');
            } else if ($signatureUploaded) {
                redirect(admin_url('brand_settings?group=pdf&tab=signature'));
            } else {
                redirect(admin_url('brand_settings?group=' . $this->input->get('group')));
            }
        }

        // If pusher notifications are on disable the manually auto check
        if (get_brand_option('pusher_realtime_notifications') == 1) {
            update_brand_option('auto_check_for_new_notifications', '0');
        }

        //$data['title']                                   = _l('options');
        $data['title'] = _l('current_brand_setting');
        if (!$this->input->get('group') || ($this->input->get('group') == 'update' && !is_admin())) {
            $view = 'general';
        } else {
            $view = $this->input->get('group');
        }

        $view = do_action('settings_group_view_name', $view);

        if ($view == 'update') {
            if (!extension_loaded('curl')) {
                $data['update_errors'][] = 'CURL Extension not enabled';
                $data['latest_version'] = 0;
                $data['update_info'] = json_decode("");
            } else {
                $data['update_info'] = $this->misc_model->get_update_info();
                if (strpos($data['update_info'], 'Curl Error -') !== false) {
                    $data['update_errors'][] = $data['update_info'];
                    $data['latest_version'] = 0;
                    $data['update_info'] = json_decode("");
                } else {
                    $data['update_info'] = json_decode($data['update_info']);
                    $data['latest_version'] = $data['update_info']->latest_version;
                    $data['update_errors'] = array();
                }
            }

            if (!extension_loaded('zip')) {
                $data['update_errors'][] = 'ZIP Extension not enabled';
            }

            $data['current_version'] = $this->db->get('tblmigrations')->row()->version;
        }

        if (!is_sido_admin() && !is_admin()) {
            $packagename = $this->brands_model->get_package_type();

            if (count($packagename) > 0) {
                $data['packagename'] = $packagename->name;
            }
        }

        $data['contacts_permissions'] = $this->perfex_base->get_contact_permissions();
        $this->load->library('pdf');

        $data['payment_gateways'] = $this->payment_modes_model->get_online_payment_modes(true);
        $data['view_name'] = $view;

        $groups_path = do_action('settings_groups_path', 'admin/brand_settings/includes');
        $data['brandtypes'] = $this->Register_model->get_brandtypes();
        $data['group_view'] = $this->load->view($groups_path . '/' . $view, $data, true);

        $this->load->view('admin/brand_settings/all', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 10/23/2017
     * to save theme style options
     */
    public function save_theme_style($data)
    {
        do_action('before_save_theme_style');

        if ($data == null) {
            $data = array();
        } else {
            $data = $data;
        }

        update_brand_option('theme_style', $data);
    }

    public function remove_signature_image()
    {
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('account_setup');
        }

        $sImage = get_option('signature_image');
        if (file_exists(get_upload_path_by_type('brands') . '/' . $sImage)) {
            unlink(get_upload_path_by_type('brands') . '/' . $sImage);
        }

        update_brand_option('signature_image', '');

        redirect(admin_url('brand_settings?group=pdf&tab=signature'));
    }

    /* Remove company logo from brand settings / ajax */
    public function remove_company_logo()
    {
        do_action('before_remove_company_logo');
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('account_setup');
        }

        if (file_exists(get_upload_path_by_type('brands') . '/' . get_brand_option('company_logo'))) {
            unlink(get_upload_path_by_type('brands') . '/' . get_brand_option('company_logo'));
            $path = get_upload_path_by_type('brands') . '/round_' . get_brand_option('company_logo');
            if (file_exists($path)) {
                unlink(get_upload_path_by_type('brands') . '/round_' . get_brand_option('company_logo'));
            }
            set_alert('success', _l('logo_removed'));
        }

        update_brand_option('company_logo', '');
        if (!$this->input->is_ajax_request()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        //redirect($_SERVER['HTTP_REFERER']);
    }
    /* Remove company logo from brand settings / ajax */
    public function remove_company_icon()
    {
        do_action('before_remove_company_logo');
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('account_setup');
        }

        if (file_exists(get_upload_path_by_type('brands') . '/' . get_brand_option('company_icon'))) {
            unlink(get_upload_path_by_type('brands') . '/' . get_brand_option('company_icon'));
            $path = get_upload_path_by_type('brands') . '/round_' . get_brand_option('company_icon');
            if (file_exists($path)) {
                unlink(get_upload_path_by_type('brands') . '/round_' . get_brand_option('company_icon'));
            }
            set_alert('success', _l('logo_removed'));
        }

        update_brand_option('company_icon', '');
        if (!$this->input->is_ajax_request()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        //redirect($_SERVER['HTTP_REFERER']);
    }

    public function remove_favicon()
    {
        do_action('before_remove_favicon');
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('account_setup');
        }
        if (file_exists(get_upload_path_by_type('brands') . '/' . get_brand_option('favicon'))) {
            unlink(get_upload_path_by_type('brands') . '/' . get_brand_option('favicon'));
            $path = get_upload_path_by_type('brands') . '/round_' . get_brand_option('favicon');
            if (file_exists($path)) {
                unlink(get_upload_path_by_type('brands') . '/round_' . get_brand_option('favicon'));
            }
            set_alert('success', _l('favicon_removed'));
        }
        update_brand_option('favicon', '');
        if (!$this->input->is_ajax_request()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        //redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Added By : Vaidehi
     * Dt : 10/18/2017
     * Remove banner from brand settings / ajax
     */
    public function remove_banner()
    {
        do_action('before_remove_banner');
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('account_setup');
        }

        if (file_exists(get_upload_path_by_type('brands') . '/' . get_brand_option('banner'))) {
            unlink(get_upload_path_by_type('brands') . '/' . get_brand_option('banner'));
            $path=get_upload_path_by_type('brands') . '/croppie_' . get_brand_option('banner');
            if($path){
                unlink(get_upload_path_by_type('brands') . '/croppie_' . get_brand_option('banner'));
            }
            set_alert('success', _l('banner_removed'));
        }

        update_brand_option('banner', '');
        if (!$this->input->is_ajax_request()) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        //redirect($_SERVER['HTTP_REFERER']);
    }

    public function delete_option($id)
    {
        if (!has_permission('brands', '', 'delete', true)) {
            access_denied('brands');
        }
        echo json_encode(array(
            'success' => delete_option($id)
        ));
    }


}
