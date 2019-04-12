<?php
$CI =& get_instance();
$session_data       = get_session_data();
if(isset($session_data['is_sido_admin']) || isset($session_data['is_admin'])) {
    $is_sido_admin      = $session_data['is_sido_admin'];
    $is_admin           = $session_data['is_admin'];

    if($is_sido_admin == 1 || $is_admin == 1) {
        $config['useragent']    = "CodeIgniter";
        $config['mailpath']     = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
        $config['wordwrap']     = TRUE;
        $config['mailtype']     = 'html';
        $charset = strtoupper(get_option('smtp_email_charset'));
        $charset = trim($charset);
        if($charset == ''){
            $charset = 'UTF-8';
        }
        $config['charset']      = $charset;
        $config['newline']      = "\r\n";
        $config['crlf']         = "\r\n";
        $config['protocol']     = get_option('email_protocol');
        $config['smtp_host']    = trim(get_option('smtp_host'));
        $config['smtp_port']    = trim(get_option('smtp_port'));
        $config['smtp_timeout'] = '30';
        $config['smtp_user']    = trim(get_option('smtp_email'));
        $config['smtp_pass']    = $CI->encryption->decrypt(get_option('smtp_password'));
        $config['smtp_crypto'] = get_option('smtp_encryption');
    }
}else{
    $config['useragent']    = "CodeIgniter";
    $config['mailpath']     = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
    $config['wordwrap']     = TRUE;
    $config['mailtype']     = 'html';
    $charset = strtoupper(get_brand_option('smtp_email_charset'));
    $charset = trim($charset);
    if($charset == ''){
        $charset = 'UTF-8';
    }
    $config['charset']      = $charset;
    $config['newline']      = "\r\n";
    $config['crlf']         = "\r\n";
    $config['protocol']     = get_brand_option('email_protocol');
    $config['smtp_host']    = trim(get_brand_option('smtp_host'));
    $config['smtp_port']    = trim(get_brand_option('smtp_port'));
    $config['smtp_timeout'] = '30';
    $config['smtp_user']    = trim(get_brand_option('smtp_email'));
    $config['smtp_pass']    = $CI->encryption->decrypt(get_brand_option('smtp_password'));
    $config['smtp_crypto'] = get_brand_option('smtp_encryption');
}