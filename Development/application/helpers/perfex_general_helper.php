<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');
/**
 * @deprecated
 */
function add_encryption_key_old()
{
    $CI =& get_instance();
    $key = generate_encryption_key();
    $config_path = APPPATH . 'config/config.php';
    $CI->load->helper('file');
    @chmod($config_path, FILE_WRITE_MODE);
    $config_file = read_file($config_path);
    $config_file = trim($config_file);
    $config_file = str_replace("\$config['encryption_key'] = '';", "\$config['encryption_key'] = '" . $key . "';", $config_file);
    if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $config_file, strlen($config_file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($config_path, FILE_READ_MODE);

    return $key;
}

/**
 * Function that will read file and will replace string
 * @param  string $path path to file
 * @param  string $find find string
 * @param  string $replace replace string
 * @return boolean
 */
function replace_in_file($path, $find, $replace)
{
    $CI =& get_instance();
    $CI->load->helper('file');
    @chmod($path, FILE_WRITE_MODE);
    $file = read_file($path);
    $file = trim($file);
    $file = str_replace($find, $replace, $file);
    $file = trim($file);
    if (!$fp = fopen($path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $file, strlen($file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($path, FILE_READ_MODE);

    return true;
}

/**
 * Check if the document should be RTL or LTR
 * The checking are performed in multiple ways eq Contact/Staff Direction from profile or from general settings *
 * @param  boolean $client_area
 * @return boolean
 */
function is_rtl($client_area = false)
{
    $CI =& get_instance();
    if (is_client_logged_in()) {
        $CI->db->select('direction')->from('tblcontacts')->where('id', get_contact_user_id());
        $direction = $CI->db->get()->row()->direction;
        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_client') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == true) {
        // Client not logged in and checked from clients area
        if (get_option('rtl_support_client') == 1) {
            return true;
        }
    } elseif (is_staff_logged_in()) {
        $CI->db->select('direction')->from('tblstaff')->where('staffid', get_staff_user_id());
        $direction = $CI->db->get()->row()->direction;
        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_admin') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == false) {
        if (get_option('rtl_support_admin') == 1) {
            return true;
        }
    }

    return false;
}

/**
 * Generate encryption key for app-config.php
 * @return stirng
 */
function generate_encryption_key()
{
    $CI =& get_instance();
    // In case accessed from my_functions_helper.php
    $CI->load->library('encryption');
    $key = bin2hex($CI->encryption->create_key(16));

    return $key;
}

/**
 * Function used to validate all recaptcha from google reCAPTCHA feature
 * @param  string $str
 * @return boolean
 */
function do_recaptcha_validation($str = '')
{
    $CI =& get_instance();
    $CI->load->library('form_validation');
    $google_url = "https://www.google.com/recaptcha/api/siteverify";
    $secret = get_option('recaptcha_secret_key');
    $ip = $CI->input->ip_address();
    $url = $google_url . "?secret=" . $secret . "&response=" . $str . "&remoteip=" . $ip;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($res, true);
    //reCaptcha success check
    if ($res['success']) {
        return true;
    } else {
        $CI->form_validation->set_message('recaptcha', _l('recaptcha_error'));

        return false;
    }
}

/**
 * Get current date format from options
 * @return string
 */
function get_current_date_format($php = false)
{
    /**
     * Added By : Vaidehi
     * Dt : 10/31/2017
     * to display brand based date and time setting for all users except sido admin and super admin
     */
    $session_data = get_session_data();
    $is_sido_admin = isset($session_data['is_sido_admin']) ? $session_data['is_sido_admin'] : 2;
    $is_admin = isset($session_data['is_admin']) ? $session_data['is_admin'] : 2;

    if ($is_sido_admin == 0 && $is_admin == 0) {
        $format = get_brand_option('dateformat');
    } else {
        $format = get_option('dateformat');
    }

    $format = explode('|', $format);

    $hook_data = do_action('get_current_date_format', array(
        'format' => $format,
        'php' => $php
    ));

    $format = $hook_data['format'];
    $php = $php;

    if ($php == false) {
        return $format[1];
    } else {
        return $format[0];
    }
}

/**
 * Check if current user is admin
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_admin($staffid = '')
{

    /**
     * Checking for current user?
     */
    if (!is_numeric($staffid)) {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->admin === '1';
        }
        $staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db->select('1')
        ->where('admin', 1)
        ->where('deleted', 0)
        ->where('staffid', $staffid);

    return $CI->db->count_all_results('tblstaff') > 0 ? true : false;

}

/**
 * Added By: Vaidehi
 * Dt: 03/01/2018
 * Check if current user is sido admin
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_sido_admin($staffid = '')
{

    /**
     * Checking for current user?
     */
    if (!is_numeric($staffid)) {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->is_sido_admin === '1';
        }
        $staffid = get_staff_user_id();
    }

    $CI =& get_instance();
    $CI->db->select('1')
        ->where('is_sido_admin', 1)
        ->where('staffid', $staffid);

    return $CI->db->count_all_results('tblstaff') > 0 ? true : false;

}

/**
 * Is user logged in
 * @return boolean
 */
function is_logged_in()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in') && !$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }

    return true;
}

/**
 * Is client logged in
 * @return boolean
 */
function is_client_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('client_logged_in')) {
        return true;
    }

    return false;
}

/**
 * Is staff logged in
 * @return boolean
 */
function is_staff_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('staff_logged_in')) {
        return true;
    }

    return false;
}

/**
 * Return logged staff User ID from session
 * @return mixed
 */
function get_staff_user_id()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('token')) {
        return $CI->session->userdata('staff_user_id');
    } elseif (!$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }
    return $CI->session->userdata('staff_user_id');
}

/**
 * Return logged client User ID from session
 * @return mixed
 */
function get_client_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in')) {
        return false;
    }

    return $CI->session->userdata('client_user_id');
}

function get_contact_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('contact_user_id')) {
        return false;
    }

    return $CI->session->userdata('contact_user_id');
}

/**
 * Get admin url
 * @param string url to append (Optional)
 * @return string admin url
 */
function admin_url($url = '')
{
    $admin_url = do_action('admin_url', ADMIN_URL);

    if ($url == '' || $url == '/') {
        if ($url == '/') {
            $url = '';
        }
        return site_url($admin_url) . '/';
    } else {
        return site_url($admin_url . '/' . $url);
    }
}

/**
 * Outputs language string based on passed line
 * @since  Version 1.0.1
 * @param  string $line language line string
 * @param  string $label sprint_f label
 * @return string        formatted language
 */
function _l($line, $label = '', $log_errors = true)
{
    $CI =& get_instance();

    $hook_data = do_action('before_get_language_text', array('line' => $line, 'label' => $label));
    $line = $hook_data['line'];
    $label = $hook_data['label'];

    if (is_array($label) && count($label) > 0) {
        $_line = vsprintf($CI->lang->line(trim($line), $log_errors), $label);
    } else {
        $_line = @sprintf($CI->lang->line(trim($line), $log_errors), $label);
    }

    $hook_data = do_action('after_get_language_text', array('line' => $line, 'label' => $label, 'formatted_line' => $_line));
    $_line = $hook_data['formatted_line'];
    $line = $hook_data['line'];

    if ($_line != '') {
        if (preg_match('/"/', $_line) && !is_html($_line)) {
            $_line = htmlspecialchars($_line, ENT_COMPAT);
        }

        return $CI->encoding_lib->toUTF8($_line);
    }

    if (mb_strpos($line, '_db_') !== false) {
        return 'db_translate_not_found';
    }

    return $CI->encoding_lib->toUTF8($line);
}

/**
 * Format date to selected dateformat
 * @param  date $date Valid date
 * @return date/string
 */
function _d($date)
{
    if ($date == '' || is_null($date) || $date == '0000-00-00') {
        return '';
    }
    if (strpos($date, ' ') !== false) {
        return _dt($date);
    }
    $format = get_current_date_format();
    $date = strftime($format, strtotime($date));

    return do_action('after_format_date', $date);
}

/**
 * Format datetime to selected datetime format
 * @param  datetime $date datetime date
 * @return datetime/string
 */
function _dt($date, $time_conversion = false, $is_timesheet = false)
{

    if ($date == '' || is_null($date) || $date == '0000-00-00 00:00:00') {
        return '';
    }
    $format = get_current_date_format(true);

    /**
     * Added By : Vaidehi
     * Dt : 10/31/2017
     * to display brand based date and time setting for all users except sido admin and super admin
     */
    $session_data = get_session_data();
    $is_sido_admin = isset($session_data['is_sido_admin']) ? $session_data['is_sido_admin'] : 2;
    $is_admin = isset($session_data['is_admin']) ? $session_data['is_admin'] : 2;

    if ($is_sido_admin == 0 && $is_admin == 0) {
        $hour12 = (get_brand_option('time_format') == 24 ? false : true);
    } else {
        $hour12 = (get_option('time_format') == 24 ? false : true);
    }

    if ($is_timesheet == false) {
        $date = strtotime($date);
    }

    if ($hour12 == false) {
        $tf = '%G:%M:%S';
        $tf = 'G:i';
        if ($is_timesheet == true) {
            $tf = '%G:%M';
            $tf = 'G:i';
        }

        /**
         * Added By : Vaidehi
         * Dt : 10/31/2017
         * to display time, if it needs to be
         */
        if ($time_conversion == false) {
            $date = date($format, $date);
        } else {
            $date = date($format . ' ' . $tf, $date);
        }
    } else {
        /**
         * Added By : Vaidehi
         * Dt : 11/29/2017
         * to display time, if it needs to be
         */
        if ($time_conversion == false) {
            $date = date(get_current_date_format(true), $date);
        } else {
            $date = date(get_current_date_format(true) . ' g:i A', $date);
        }
    }

    return do_action('after_format_datetime', $date);
}
/**
 * Format datetime to selected datetime format
 * @param  datetime $date datetime date
 * @return datetime/string
 */
/**
 * Added By : Vaidehi
 * Dt : 10/31/2017
 * to display time, if it needs to be
 */
function _time($date, $time_conversion = false, $is_timesheet = true)
{
    if ($date == '' || is_null($date) || $date == '0000-00-00 00:00:00') {
        return '';
    }
    $format = get_current_date_format();
    $session_data = get_session_data();
    $is_sido_admin = isset($session_data['is_sido_admin']) ? $session_data['is_sido_admin'] : 2;
    $is_admin = isset($session_data['is_admin']) ? $session_data['is_admin'] : 2;

    if ($is_sido_admin == 0 && $is_admin == 0) {
        $hour12 = (get_brand_option('time_format') == 24 ? false : true);
    } else {
        $hour12 = (get_option('time_format') == 24 ? false : true);
    }

    /*if ($is_timesheet == false) {
        $date = strtotime($date);
    }*/
    $date = strtotime($date);
    if ($hour12 == false) {
        $tf = '%G:%M:%S';
        if ($is_timesheet == true) {
            $tf = '%G:%M';
        }
        $date = date('G:i', $date);

    } else {
        $date = date('g:i A', $date);
    }
    return do_action('after_format_datetime', $date);
}
/**
 * Convert string to sql date based on current date format from options
 * @param  string $date date string
 * @return mixed
 */
function to_sql_date($date, $datetime = false)
{
    if ($date == '') {
        return null;
    }

    $to_date = 'Y-m-d';
    $from_format = get_current_date_format(true);


    $hook_data['date'] = $date;
    $hook_data['from_format'] = $from_format;
    $hook_data['datetime'] = $datetime;

    $hook_data = do_action('before_sql_date_format', $hook_data);

    $date = $hook_data['date'];
    $from_format = $hook_data['from_format'];

    if ($datetime == false) {
        //return date_format(date_create_from_format($from_format, $date), $to_date);
        return date_format(date_create($date),$to_date);
    } else {
        if (strpos($date, ' ') === false) {
            $date .= ' 00:00:00';
        } else {
            $hour12 = (get_option('time_format') == 24 ? false : true);
            if ($hour12 == false) {
                $_temp = explode(' ', $date);
                $time = explode(':', $_temp[1]);
                if (count($time) == 2) {
                    $date .= ':00';
                }
            } else {
                $tmp = _simplify_date_fix($date, $from_format);
                $time = date("G:i", strtotime($tmp));
                $tmp = explode(' ', $tmp);
                $date = $tmp[0] . ' ' . $time . ':00';
            }
        }

        $date = _simplify_date_fix($date, $from_format);
        $d = strftime('%Y-%m-%d %G:%M:%S', strtotime($date));

        return do_action('to_sql_date_formatted', $d);
    }
}

function _simplify_date_fix($date, $from_format)
{
    if ($from_format == 'd/m/Y') {
        $date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $date);
    } elseif ($from_format == 'm/d/Y') {
        $date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
    } elseif ($from_format == 'm.d.Y') {
        $date = preg_replace('#(\d{2}).(\d{2}).(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
    } elseif ($from_format == 'm-d-Y') {
        $date = preg_replace('#(\d{2})-(\d{2})-(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
    }

    return $date;
}

/**
 * Check if passed string is valid date
 * @param  string $date
 * @return boolean
 */
function is_date($date)
{
    if (strlen($date) < 10) {
        return false;
    }

    return (bool)strtotime($date);
}

/**
 * Get locale key by system language
 * @param  string $language language name from (application/languages) folder name
 * @return string
 */
function get_locale_key($language = 'english')
{
    $locale = 'en';
    if ($language == '') {
        return $locale;
    }

    $locales = get_locales();

    if (isset($locales[$language])) {
        $locale = $locales[$language];
    } elseif (isset($locales[ucfirst($language)])) {
        $locale = $locales[ucfirst($language)];
    } else {
        foreach ($locales as $key => $val) {
            $key = strtolower($key);
            $language = strtolower($language);
            if (strpos($key, $language) !== false) {
                $locale = $val;
                // In case $language is bigger string then $key
            } elseif (strpos($language, $key) !== false) {
                $locale = $val;
            }
        }
    }

    $locale = do_action('before_get_locale', $locale);

    return $locale;
}

/**
 * Check if staff user has permission
 * @param  string $permission permission shortname
 * @param  mixed $staffid if you want to check for particular staff
 * @return boolean
 */
function has_permission($permission, $staffid = '', $can = '', $global = true)
{

    $CI =& get_instance();

    /**
     * Maybe permission is function?
     * Example is_admin or is_staff_member
     */
    if (function_exists($permission) && is_callable($permission)) {
        return call_user_func($permission, $staffid);
    }

    /**
     * If user is admin return true
     * Admin have all permissions
     */
    // if (is_admin($staffid)) {
    //     return true;
    // }

    $currentUser = $staffid == '' ? true : false;
    $staffid = ($staffid == '' ? get_staff_user_id() : $staffid);

    $can = ($can == '' ? 'view' : $can);

    /**
     * Stop making query if we are doing checking for current user
     * Current user is stored in $GLOBALS including the permissions
     */
    $get_session_data = get_session_data();
    $hasPermission = false;
    $is_user_admin = $get_session_data['is_admin'];
    if ($is_user_admin == true) {
        $hasPermission = true;
    } else {
        if ($currentUser && isset($GLOBALS['current_user']) && is_array($GLOBALS['current_user']->permission)) {
            $hasPermission = false;
            $roles_data = $teams_data = array();
            $get_session_data = get_session_data();
            $is_user_admin = $get_session_data['is_admin'];

            foreach ($GLOBALS['current_user']->permission as $permObject) {
                $CI->db->select('tblrolepermissions.*,tblpermissions.shortname as permission_name');
                $CI->db->join('tblpermissions', 'tblpermissions.permissionid = tblrolepermissions.permissionid');
                $CI->db->where('roleid', $permObject->role_id);
                $where = '(can_view = 1 or can_view_own = 1 or can_edit = 1 or can_create = 1 or can_delete = 1)';
                $CI->db->where($where);
                $roles_data[] = $CI->db->get('tblrolepermissions')->result();

                // $CI->db->select('tblteampermissions.*,tblpermissions.shortname as permission_name');
                // $CI->db->join('tblpermissions','tblpermissions.permissionid = tblteampermissions.permissionid');
                // $CI->db->where('teamid',$permObject->team_id);
                // $where = '(can_view = 1 or can_view_own = 1 or can_edit = 1 or can_create = 1 or can_delete = 1)';
                // $CI->db->where($where);
                // $teams_data[] = $CI->db->get('tblteampermissions')->result();
            }
            $roles_final_data = array();
            $roles_data = objectToArray($roles_data);
            foreach ($roles_data as $rvalue) {
                foreach ($rvalue as $rpvalue) {
                    if (in_array($rpvalue['permissionid'], array_column($roles_final_data, 'permissionid'))) {
                    } else {
                        $roles_final_data[] = $rpvalue;
                    }
                }
            }

            // $teams_final_data = array();  
            // $teams_data =  objectToArray($teams_data);
            // foreach ($teams_data as $tvalue) {
            //     foreach ($tvalue as $tpvalue) {
            //         if(in_array($tpvalue['permissionid'], array_column($teams_final_data, 'permissionid'))) {
            //         }else{
            //             $teams_final_data[] = $tpvalue;
            //         }
            //     }
            // }
            // $team_role_data = array_merge($teams_final_data,$roles_final_data);
            // $teams_roles_final_data = array();
            // foreach ($team_role_data as $trvalue) {
            //     if(in_array($trvalue['permissionid'], array_column($teams_roles_final_data, 'permissionid'))) {

            //     }else{
            //         $teams_roles_final_data[] = $trvalue;
            //     }
            // }

            /**
             * Added By: Vaidehi
             * Dt: 03/27/2018
             * to allow users to access even if they have no subscriptionpackage selected
             */
            $CI->db->select('tblclients.*');
            $CI->db->where('primary_user_id', $staffid);
            $client = $CI->db->get('tblclients')->row();

            if (!empty($client)) {
                if ($client->packageid == 0) {
                    $global = false;
                } else {
                    $global = true;
                }
            }

            foreach ($roles_final_data as $teams_roles_final) {
                if ($teams_roles_final['permission_name'] == $permission
                    && $teams_roles_final['can_' . $can] == '1') {

                    if ($global == true) {
                        $hasPermission = has_package_permission($teams_roles_final['permission_name']);
                        break;
                    } else {
                        $hasPermission = true;
                        break;
                    }

                }
            }
        }
    }

    return $hasPermission;

    /**
     * Get the permission id
     */
    $CI->db->select('permissionid as id')
        ->where('shortname', $permission);

    $permission = $CI->db->get('tblpermissions')->row();

    /**
     * Permission not exists?
     * Return false
     */
    if (!$permission) {
        return false;
    }
    /**
     * Check if user have permissions
     * @var boolean
     */
    return (total_rows('tblstaffpermissions', array(
        'permissionid' => $permission->id,
        'staffid' => $staffid,
        'can_' . $can => 1
    )) > 0 ? true : false);
}

/**
 * Added By : Vaidehi
 * Dt : 10/23/2017
 */
/**
 * Check if staff user has package permission
 * @param  string $permission permission shortname
 * @param  mixed $staffid if you want to check for particular staff
 * @return boolean
 */
function has_package_permission($permission, $staffid = '')
{
    $CI =& get_instance();

    $staffid = ($staffid == '' ? get_staff_user_id() : $staffid);


    /**
     * Added By: Vaidehi
     * Dt: 03/27/2018
     * to allow users to access even if they have no subscriptionpackage selected
     */
    $checkpermission = true;

    $CI->db->select('tblclients.*');
    $CI->db->where('primary_user_id', $staffid);
    $client = $CI->db->get('tblclients')->row();

    if (!empty($client)) {
        if ($client->packageid == 0) {
            $checkpermission = false;
        } else {
            $checkpermission = true;
        }
    }

    if ($checkpermission) {
        /**
         * Stop making query if we are doing checking for current user
         * Current user is stored in $GLOBALS including the permissions
         */
        $get_session_data = get_session_data();

        $is_user_admin = $get_session_data['is_admin'];

        $is_sido_admin = $get_session_data['is_sido_admin'];

        if ($is_user_admin == true || $is_sido_admin == true) {
            $hasPackagePermission = true;
        } else {
            $package_id = $get_session_data['package_id'];

            $CI->db->select('tblpackagepermissions.*');
            $CI->db->select('tblpermissions.shortname as permission_name');
            $CI->db->join('tblpermissions', 'tblpermissions.permissionid = tblpackagepermissions.permissionid');
            $where = 'can_access = 1 and packageid = ' . $package_id . ' and shortname = "' . $permission . '"';
            $CI->db->where($where);
            $packages_data = $CI->db->get('tblpackagepermissions')->result();

            if (!empty($packages_data)) {
                $packages_final_data = array();
                $packages_data = objectToArray($packages_data);

                if ($packages_data[0]['can_access'] == 1) {
                    $hasPackagePermission = true;
                } else {
                    $hasPackagePermission = false;
                }
            } else {
                $hasPackagePermission = false;
            }
        }
    }
    return $hasPackagePermission;
}

function objectToArray($d)
{
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
        return $d;
    }
}

/**
 * get user account information from session
 * @return int
 */
function get_user_session()
{
    $CI =& get_instance();
    $session_data = $CI->session->userdata;
    $brandid = 0;
    if (isset($session_data['brand_id'])) {
        $brandid = intval($session_data['brand_id']);
    }
    return $brandid;
}

/**
 * get user package information from session
 * @return int
 */
function get_session_data()
{
    $CI =& get_instance();
    $session_data = $CI->session->userdata;
    return $session_data;
}

function team_has_permission($permission, $teamid = '', $can = '')
{

    $CI =& get_instance();

    /**
     * Maybe permission is function?
     * Example is_admin or is_team_member
     */
    if (function_exists($permission) && is_callable($permission)) {
        return call_user_func($permission, $teamid);
    }

    /**
     * If user is admin return true
     * Admin have all permissions
     */
    if (is_admin($teamid)) {
        return true;
    }


    $currentUser = $teamid == '' ? true : false;
    $teamid = ($teamid == '' ? get_team_user_id() : $teamid);
    $can = ($can == '' ? 'view' : $can);

    /**
     * Stop making query if we are doing checking for current user
     * Current user is stored in $GLOBALS including the permissions
     */
    if ($currentUser && isset($GLOBALS['current_user']) && is_array($GLOBALS['current_user']->permissions)) {
        $hasPermission = false;
        foreach ($GLOBALS['current_user']->permissions as $permObject) {
            if ($permObject->permission_name == $permission
                && $permObject->{'can_' . $can} == '1') {
                $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }

    /**
     * Get the permission id
     */
    $CI->db->select('permissionid as id')
        ->where('shortname', $permission);

    $permission = $CI->db->get('tblpermissions')->row();
    /**
     * Permission not exists?
     * Return false
     */
    if (!$permission) {
        return false;
    }
    /**
     * Check if user have permissions
     * @var boolean
     */
    return (total_rows('tblteampermissions', array(
        'permissionid' => $permission->id,
        'teamid' => $teamid,
        'can_' . $can => 1
    )) > 0 ? true : false);
}

/**
 * Function is customer admin
 * @param  mixed $id customer id
 * @param  staff_id $staff_id staff id to check
 * @return boolean
 */
function is_customer_admin($id, $staff_id = '')
{
    return total_rows('tblcustomeradmins', array(
        'customer_id' => $id,
        'staff_id' => is_numeric($staff_id) ? $staff_id : get_staff_user_id()
    )) > 0 ? true : false;
}

/**
 * Check if staff member have assigned customers
 * @param  mixed $staff_id staff id
 * @return boolean
 */
function have_assigned_customers($staff_id = '')
{
    return total_rows('tblcustomeradmins', array(
        'staff_id' => is_numeric($staff_id) ? $staff_id : get_staff_user_id()
    )) > 0 ? true : false;
}

/**
 * Check if contact has permission
 * @param  string $permission permission name
 * @param  string $contact_id contact id
 * @return boolean
 */
function has_contact_permission($permission, $contact_id = '')
{
    $CI =& get_instance();
    if (!class_exists('perfex_base')) {
        $CI->load->library('perfex_base');
    }
    $permissions = $CI->perfex_base->get_contact_permissions();
    // Contact id passed form function
    if ($contact_id != '') {
        $_contact_id = $contact_id;
    } else {
        // Current logged in contact
        $_contact_id = get_contact_user_id();
    }
    foreach ($permissions as $_permission) {
        if ($_permission['short_name'] == $permission) {
            if (total_rows('tblcontactpermissions', array(
                    'permission_id' => $_permission['id'],
                    'userid' => $_contact_id
                )) > 0) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Check if user is staff member
 * In the staff profile there is option to check IS NOT STAFF MEMBER eq like contractor
 * Some features are disabled when user is not staff member
 * @param  string $staff_id staff id
 * @return boolean
 */
function is_staff_member($staff_id = '')
{

    $CI =& get_instance();
    if ($staff_id == '') {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->is_not_staff === '0';
        }
        $staff_id = get_staff_user_id();
    }

    $CI->db->where('staffid', $staff_id)
        ->where('is_not_staff', 0);

    return $CI->db->count_all_results('tblstaff') > 0 ? true : false;

}

/**
 * Load language in admin area
 * @param  string $staff_id
 * @return string return loaded language
 */
function load_admin_language($staff_id = '')
{
    $CI =& get_instance();

    $CI->lang->is_loaded = array();
    $CI->lang->language = array();

    $language = get_option('active_language');
    if (is_staff_logged_in() || $staff_id != '') {
        $staff_language = get_staff_default_language($staff_id);
        if (!empty($staff_language)) {
            if (file_exists(APPPATH . 'language/' . $staff_language)) {
                $language = $staff_language;
            }
        }
    }
    $CI->lang->load($language . '_lang', $language);
    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
        $CI->lang->load('custom_lang', $language);
    }

    $language = do_action('after_load_admin_language', $language);

    return $language;
}

/**
 * Load customers area language
 * @param  string $customer_id
 * @return string return loaded language
 */
function load_client_language($customer_id = '')
{
    $CI =& get_instance();
    $language = get_option('active_language');
    if (is_client_logged_in() || $customer_id != '') {
        $client_language = get_client_default_language($customer_id);
        if (!empty($client_language)) {
            if (file_exists(APPPATH . 'language/' . $client_language)) {
                $language = $client_language;
            }
        }
    }

    $CI->lang->load($language . '_lang', $language);
    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
        $CI->lang->load('custom_lang', $language);
    }

    $language = do_action('after_load_client_language', $language);

    return $language;
}

/**
 * Get current url with query vars
 * @return string
 */
function current_full_url()
{
    $CI =& get_instance();
    $url = $CI->config->site_url($CI->uri->uri_string());

    return $_SERVER['QUERY_STRING'] ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;
}

function value_exists_in_array_by_key($array, $key, $val)
{
    foreach ($array as $item) {
        if (isset($item[$key]) && $item[$key] == $val) {
            return true;
        }
    }

    return false;
}

add_action('check_vault_entries_visibility', '_check_vault_entries_visibility');
function _check_vault_entries_visibility($entries)
{
    $new = array();
    foreach ($entries as $entry) {
        if ($entry['visibility'] != 1) {
            if ($entry['visibility'] == 2 && !is_admin() && $entry['creator'] != get_staff_user_id()) {
                continue;
            } elseif ($entry['visibility'] == 3 && $entry['creator'] != get_staff_user_id() && !is_admin()) {
                continue;
            }
        }
        $new[] = $entry;
    }

    return $new;
}

/**
 * Triggers
 * @param  array $users id of users to receive notifications
 * @return null
 */
function pusher_trigger_notification($users = array())
{

    if (get_option('pusher_realtime_notifications') == 0) {
        return false;
    }

    if (!is_array($users)) {
        return false;
    }

    if (count($users) == 0) {
        return false;
    }

    if (!class_exists('Pusher')) {
        require_once(APPPATH . 'libraries/Pusher.php');
    }

    $app_key = get_option('pusher_app_key');
    $app_secret = get_option('pusher_app_secret');
    $app_id = get_option('pusher_app_id');

    if ($app_key == "" || $app_secret == "" || $app_id == "") {
        return false;
    }

    $pusher_options = do_action('pusher_options', array());

    if (!isset($pusher_options['cluster']) && get_option('pusher_cluster') != '') {
        $pusher_options['cluster'] = get_option('pusher_cluster');
    }

    $pusher = new Pusher(
        $app_key,
        $app_secret,
        $app_id,
        $pusher_options
    );

    $channels = array();
    foreach ($users as $id) {
        array_push($channels, 'notifications-channel-' . $id);
    }

    $channels = array_unique($channels);

    $pusher->trigger($channels, 'notification', array());
}

function days_in_months($date, $months)
{
    $day = date('d', strtotime($date));
    $month = date('m', strtotime($date)) - 1;
    $year = date('Y', strtotime($date));
    $endmonth = $month + $months;
    while ($endmonth > 12) {
        $endmonth = $endmonth - 12;
        $year++;
    }
    $end_date = $endmonth . "/" . $day . "/" . $year;
    $date = new DateTime($end_date);
    //$date->modify('last day of this month');
    return $date->format('m/d/Y');
}

function get_count($table, $rel_type, $rel_id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    if ($table != "tblnotes") {
        $CI->db->where('deleted', 0);
    }
    $CI->db->where('rel_type', $rel_type);
    $CI->db->where('rel_id', $rel_id);

    return $CI->db->count_all_results($table);
}

function get_event_name($type, $id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    if ($type == "project" || $type == "event") {
        $CI->db->where('deleted', 0);
        $CI->db->where('id', $id);
        $event = $CI->db->get('tblprojects')->row();
    } elseif ($type == "lead") {
        $CI->db->where('deleted', 0);
        $CI->db->where('id', $id);
        $event = $CI->db->get('tblleads')->row();
    }
    return $event;
}

function get_all_meeting_reminders($id)
{
    $CI =& get_instance();
    $CI->db->select('*');
    $CI->db->where('meetingid', $id);
    return $CI->db->count_all_results('tblmeetingreminders');
}

function get_meeting_attendees($meeting_id)
{
    $session_data = get_session_data();
    $CI =& get_instance();
    $CI->db->select('*');
    $CI->db->from('tblmeetingusers');
    $CI->db->where('meeting_id', $meeting_id);
    $assignees = $CI->db->get()->result_array();
    $team_member = array();
    $client = array();
    $counter = 0;
    $attendees = array();
    foreach ($assignees as $assignee) {
        $member = get_member_by_id($assignee['user_id']);
        $contact = get_contact_by_id($assignee['contact_id']);
        if (!empty($member)) {
            $attendees[$counter]['id'] = $assignee['user_id'];
            $attendees[$counter]['name'] = $member->firstname . " " . $member->lastname;
            $attendees[$counter]['type'] = "member";
        }
        if (!empty($contact)) {
            $attendees[$counter]['id'] = $assignee['contact_id'];
            $attendees[$counter]['name'] = $contact->firstname . " " . $contact->lastname;
            $attendees[$counter]['type'] = "client";
        }
        $counter++;
    }
    return $attendees;
}

function is_serialized($data, $strict = true)
{
    // if it isn't a string, it isn't serialized.
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    if (strlen($data) < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        $lastc = substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        $semicolon = strpos($data, ';');
        $brace = strpos($data, '}');
        // Either ; or } must exist.
        if (false === $semicolon && false === $brace)
            return false;
        // But neither must be in the first X characters.
        if (false !== $semicolon && $semicolon < 3)
            return false;
        if (false !== $brace && $brace < 4)
            return false;
    }
    $token = $data[0];
    switch ($token) {
        case 's' :
            if ($strict) {
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
            } elseif (false === strpos($data, '"')) {
                return false;
            }
        // or else fall through
        case 'a' :
        case 'O' :
            return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b' :
        case 'i' :
        case 'd' :
            $end = $strict ? '$' : '';
            return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
    }
    return false;
}

function get_setting_type()
{
    $this->db->where('deleted', 0);
    return $this->db->get('tblsocials')->result_array();
}

/**/

function getLatLong($address)
{
    if (!empty($address)) {
        //Formatted address
        $formattedAddr = str_replace(' ', '+', $address);
        //Send request and receive json data by address
        $geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddr . '&sensor=true&key=AIzaSyCjl6sda5qEMb16M1aaj7B5GlwoeHnShBI');
        $output = json_decode($geocodeFromAddr);
        //Get latitude and longitute from json data
        if (isset($output->results[0])) {
            $data['latitude'] = $output->results[0]->geometry->location->lat;
            $data['longitude'] = $output->results[0]->geometry->location->lng;
        }
        //Return latitude and longitude of the given address
        if (!empty($data)) {
            return $data;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_vanue_data($id)
{
    $CI =& get_instance();
    $CI->db->where('deleted', 0);
    $CI->db->where('venueid', $id);
    return $CI->db->get('tblvenue')->row();
}

function get_venueloc_data($id)
{
    $CI =& get_instance();
    $CI->db->where('deleted', 0);
    $CI->db->where('locid', $id);
    return $CI->db->get('tblvenueloc')->row();
}

/*
 * Added By masud
 * 07/08/2018
 * for proposal unique token
 * */
function getProposalToken($length = 8)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

function getcustomerid($clientid)
{
    //Added by Vaidehi on 03/28/2018 to create customer
    $CI =& get_instance();
    $custid_row = $CI->db->query('SELECT IFNULL(`custid`,0) AS custid FROM `tblinvoices` LEFT JOIN `tblinvoicepaymentrecords` ON `tblinvoices`.`id` =  `tblinvoicepaymentrecords`.`invoiceid` WHERE `tblinvoices`.`clientid` = ' . $clientid . ' ORDER BY `tblinvoicepaymentrecords`.`invoiceid` DESC LIMIT 0,1')->row();
    return $custid_row->custid;
}

function get_invoice_proposalid($id)
{
    $CI =& get_instance();
    $CI->db->where('invoice_id', $id);
    $result = $CI->db->get('tblproposalinvoice')->row();
    if (isset($result->proposal_id)) {
        return $result->proposal_id;
    }
    return 0;
}
function get_invoice_status($id)
{
    $CI =& get_instance();
    $CI->db->where('id', $id);
    $result = $CI->db->get('tblinvoices')->row();
    if (isset($result->status)) {
        return $result->status;
    }
    return 0;
}