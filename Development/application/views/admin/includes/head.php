<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-83450906-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-83450906-1');
    </script>


    <?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1"/>
    <?php
    /**
     * Added By : Vaidehi
     * Dt : 10/23/2017
     * to display custom theme style for all users except sido admin and super admin
     */
    $session_data = get_session_data();
    $is_sido_admin = isset($session_data['is_sido_admin']) ? $session_data['is_sido_admin'] : 2;
    $is_admin = isset($session_data['is_admin']) ? $session_data['is_admin'] : 2;

    if ($is_sido_admin == 0 && $is_admin == 0) {
        if (get_brand_option('favicon') != '') {
            $favicon = get_brand_option('favicon');
            $faviconImagePath = FCPATH . 'uploads/brands/' . $favicon;
            $src = base_url('assets/images/favicon.png');
            if (file_exists($faviconImagePath)) {
                $src = base_url('uploads/brands/' . $favicon);
                $faviconImagePath = FCPATH . 'uploads/brands/round_' . $favicon;
                if (file_exists($faviconImagePath)) {
                    $src = base_url('uploads/brands/round_' . $favicon);
                }
            }
            ?>
            <link href="<?php echo $src; ?>"
                  rel="shortcut icon">
            <?php
        } else {
            if (get_option('favicon') != '')
                ?>
                <link href="<?php echo base_url('uploads/company/' . get_option('favicon')); ?>" rel="shortcut icon">
        <?php }
    } else {
        if (get_option('favicon') != '')
            ?>
            <link href="<?php echo base_url('uploads/company/' . get_option('favicon')); ?>" rel="shortcut icon">
        <?php
    }
    ?>
    <title><?php if (isset($title)) {
            echo $title;
        } else {
            echo get_option('companyname');
        } ?></title>
    <?php echo app_stylesheet('assets/css', 'reset.css'); ?>
    <link href='<?php echo base_url('assets/plugins/roboto/roboto.css?v=' . get_app_version()); ?>' rel='stylesheet'>
    <link href="<?php echo base_url('assets/plugins/bootstrap/css/bootstrap.min.css?v=' . get_app_version()); ?>"
          rel="stylesheet">
    <?php if ($isRTL === 'true') { ?>
        <link href="<?php echo base_url('assets/plugins/bootstrap-arabic/css/bootstrap-arabic.min.css?v=' . get_app_version());; ?>"
              rel="stylesheet">
    <?php } ?>
    <link href="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui.min.css?v=' . get_app_version()); ?>"
          rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css?v=' . get_app_version()); ?>"
          rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,600,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Pinyon+Script" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/lightGallery/dist/css/lightgallery.css?v=' . get_app_version()); ?>"
          rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/app-build/vendor.css?v=' . get_app_version()); ?>" rel="stylesheet">
    <?php if (isset($calendar_assets)) { ?>
        <link href='<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.css?v=' . get_app_version()); ?>'
              rel='stylesheet'/>
    <?php } ?>
    <link href='<?php echo base_url('assets/plugins/lightbox/css/lightbox.min.css?v=' . get_app_version()); ?>'
          rel='stylesheet'/>
    <?php if (isset($form_builder_assets)) { ?>
        <link href='<?php echo base_url('assets/plugins/form-builder/form-builder.min.css?v=' . get_app_version()); ?>'
              rel='stylesheet'/>
    <?php } ?>
    <?php if (isset($projects_assets)) { ?>
        <link href='<?php echo base_url('assets/plugins/jquery-comments/css/jquery-comments.css?v=' . get_app_version()); ?>'
              rel='stylesheet'/>
        <link href='<?php echo base_url('assets/plugins/gantt/css/style.css?v=' . get_app_version()); ?>'
              rel='stylesheet'/>
    <?php } ?>
    <?php if (isset($files_assets)) { ?>
        <link rel="stylesheet" type="text/css" media="screen"
              href="<?php echo base_url('assets/plugins/elFinder/css/elfinder.min.css?v=' . get_app_version()); ?>">
        <link rel="stylesheet" type="text/css" media="screen"
              href="<?php echo base_url('assets/plugins/elFinder/themes/windows-10/css/theme.css?v=' . get_app_version()); ?>">
    <?php } ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.1/jquery.toast.min.css?v=<?php echo get_app_version(); ?>"
          rel="stylesheet"/>
    <link href="<?php echo base_url('assets/css/sweetalert2.css?v=' . get_app_version()); ?>" rel="stylesheet"/>

    <?php echo app_stylesheet('assets/css', 'style.css'); ?>
    <?php echo app_stylesheet('assets/plugins/Croppie-master', 'croppie.css'); ?>
    <?php if (file_exists(FCPATH . 'assets/css/custom.css')) { ?>
        <link href="<?php echo base_url('assets/css/custom.css?v=' . get_app_version()); ?>" rel="stylesheet">
    <?php } ?>
    <!-- <link href="<?php //echo base_url('assets/css/pace.css'); ?>" rel="stylesheet" /> -->

    <?php render_custom_styles(array('general', 'tabs', 'buttons', 'admin', 'modals', 'tags')); ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php render_admin_js_variables(); ?>
    <script>
        appLang['datatables'] = <?php echo json_encode(get_datatables_language_array()); ?>;

        var total_unread_notifications = <?php echo $unread_notifications; ?>,
            proposal_templates = <?php echo json_encode(get_proposal_templates()); ?>,
            availableTags = <?php echo json_encode(get_tags_clean()); ?>,
            availableTagsIds = <?php echo json_encode(get_tags_ids()); ?>,
            bs_fields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'],
            locale = '<?php echo $locale; ?>',
            isRTL = '<?php echo $isRTL; ?>',
            tinymce_lang = '<?php echo get_tinymce_language(get_locale_key($app_language)); ?>',
            months_json = '<?php echo json_encode(array(_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December'))); ?>',
            _table_api, taskid, task_tracking_stats_data, taskAttachmentDropzone, leadAttachmentsDropzone,
            newsFeedDropzone, expensePreviewDropzone, autocheck_notifications_timer_id = 0, task_track_chart,
            cfh_popover_templates = {};
    </script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <?php do_action('app_admin_head'); ?>
</head>

<?php
$preview = "";
if ((isset($_GET['preview'])) || ($this->uri->segment(2) == "proposaltemplates" && $this->uri->segment(3) == "view")) {
    $preview = "preview";
}
?>
<body <?php if ($isRTL === 'true') {
    echo 'dir="rtl"';
} ?> class="<?php echo 'page' . ($this->uri->segment(2) ? '-' . $this->uri->segment(2) : '') . '-' . $this->uri->segment(1); ?> admin <?php if (isset($bodyclass)) {
    echo $bodyclass . ' ';
} ?><?php if ($this->session->has_userdata('is_mobile') && $this->session->userdata('is_mobile') == true) {
    echo 'hide-sidebar ';
} ?><?php if ($isRTL === 'true') {
    echo 'rtl';
} ?> <?php echo $preview ?> ">
<?php do_action('after_body_start'); ?>
