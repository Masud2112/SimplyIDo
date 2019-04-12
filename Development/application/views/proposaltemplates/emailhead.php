<?php
/**
 * Created by PhpStorm.
 * User: Masud
 * Date: 21-12-2018
 * Time: 16:40
 */
?>
<?php
if (isset($emailtemp) && $emailtemp == 1) { ?>
    <!-- <link rel="stylesheet" type="text/css" href="<?php /*echo site_url('assets/plugins/bootstrap/css/bootstrap.css');*/
    ?>">
    <link rel="stylesheet" type="text/css" href="<?php /*echo site_url('assets/css/emailcss.css');*/
    ?>">-->

    <style type="text/css">
        /*@import url("



        <?php echo site_url('assets/plugins/bootstrap/css/bootstrap.css');?>



                                ");
                                        @import url("



        <?php echo site_url('assets/css/emailcss.css');?>    ");*/
        .pl_logo_blk {
            height: 130px;
            width: 130px;
            border-radius: 50%;
            border: 3px solid #fff;
            background: #e0e0e0;
            text-align: center;
            display: inline-block;
            overflow: hidden;
            margin-top: -85px;
            position: relative;
            margin-left: 20px
        }

        img {
            max-width: 100%;
        }

        .row {
            margin-right: -5px;
            margin-left: -5px
        }

        .psl_section_head_blk {
            margin-bottom: 10px
        }

        h3 {
            font-weight: 500;
            color: #2c2c2c;
            font-family: "Open Sans", -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif
        }

        h3, .h3 {
            font-size: 24px;
        }

        .text-right {
            text-align: right;
        }

        .img-responsive, .thumbnail > img, .thumbnail a > img, .carousel-inner > .item > img, .carousel-inner > .item > a > img {
            display: block;
            max-width: 100%;
            height: auto;
        }

        .col-md-6 {
            width: 50%;
            float: left;
        }

        .col-md-8 {
            width: 66.66666667%;
        }

        .psl_section_head .pl_head {
            float: left;
        }

        .psl_section_head .project_lead .pl_inner {
            float: left;
            padding-left: 15px;
            padding-top: 10px;
            text-align: left;
        }

        .psl_section_head .project_lead .pl_inner .ename {
            font-weight: 700;
            font-size: 15px;
        }

        .clearfix {
            clear: both;
        }

        #previewemailbody {
            padding: 0 20px;
        }

        div#previewemailbody .token {
            display: inline-block;
        }

        a {
            border-radius: 4px;
            padding: 8px 12px;
        }

        a {
            text-transform: uppercase;
            font-size: 14px;
            outline-offset: 0;
            transition: all .15s ease-in-out;
            -o-transition: all .15s ease-in-out;
            -moz-transition: all .15s ease-in-out;
            -webkit-transition: all .15s ease-in-out;
            font-weight: 700
        }

        a {
            font-weight: 700;
            border: 1px solid;
            background-color: #5bc0de;
            border-color: #46b8da;
            min-height: 38px
        }

        a {
            color: #fff;
            background-color: #5bc0de;
            border-color: #46b8da;
            outline: none !important;
            text-decoration: none;
        }

        .banner-block.copLogo_blk {
            width: 120px;
        }
    </style>
<?php }
if (isset($proposal)) {
    $nextproposalnumber = $proposal->proposal_version;
    $format = $proposal->number_format;
} else {
    $nextproposalnumber = get_brand_option('next_proposal_number');
    $format = get_brand_option('invoice_number_format');
}

$pad_length = 2;
if ($format == 1) {
    // Number based
    $prefix = "";
    $pad_length = 6;
} else if ($format == 2) {
    if (isset($rel_content) && !empty($rel_content->eventstartdatetime)) {
        $prefix = date('Y', strtotime($proposal->datecreated));
    } else {
        $prefix = date('Y', strtotime($proposal->datecreated));
    }

} else if ($format == 3) {
    if (isset($rel_content) && !empty($rel_content->eventstartdatetime)) {
        $prefix = date('Ymd', strtotime($rel_content->eventstartdatetime));
    } else {
        $prefix = date('Ymd', strtotime($proposal->datecreated));
    }
} else if ($format == 4) {
    if (isset($rel_content) && !empty($rel_content)) {
        $event_date = date('Ymd', strtotime($rel_content->eventstartdatetime));
        $prefix = $event_date . "/" . str_pad($rel_id, $pad_length, '0', STR_PAD_LEFT) . "/";
    } else {
        $event_date = date('Ymd', strtotime($proposal->datecreated));
        $prefix = $event_date . "/" . str_pad($rel_id, $pad_length, '0', STR_PAD_LEFT) . "/";
    }
} else {
    $prefix = date('Ymd', strtotime($proposal->datecreated));
}

$proposalversion = $prefix . str_pad($nextproposalnumber, $pad_length, '0', STR_PAD_LEFT);
if (strtolower($title) == "invoice") {
    $invoice_prefix = get_brand_option('invoice_prefix');
    //$proposalversion = "P" . $proposalversion . "/" . str_pad($nextinvoice->number, $pad_length, '0', STR_PAD_LEFT);;
}

$company_logo = get_brand_option('company_logo', $proposal->brandid);
$company_name = get_brand_option('companyname', $proposal->brandid);
if ($company_logo != '') {
    $company_logo = '<img width="120" src="' . base_url('uploads/brands/' . $company_logo) . '" class="img-responsive" alt="' . $company_name . '" >';
} else if ($company_name != '') {
    $company_logo = $company_name;
} else {
    $company_logo = '';
}
?>
<table width="100%">
    <tr>
        <td align="center">
            <table style="width: 680px;text-align: left;margin: auto">
                <tr>
                    <td>
                        <table class="psl_section_head_blk" style="width: 100%;">
                            <tr>
                                <td style="width: 50%; text-align: left;">
                                    <div class="banner-block copLogo_blk">
                                        <?php echo $company_logo; ?>
                                    </div>
                                </td>
                                <td style="width: 50%; text-align: right;">
                                    <h3 class="text-right"
                                        style="font-weight: 500; color: #2c2c2c; font-family: 'Open Sans',-apple-system,system-ui,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif; font-size: 24px; text-align: right;">
                                        <strong><?php echo strtoupper($title) ?></strong>
                                    </h3>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width: 680px">
                        <?php $src = base_url() . "assets/images/default_banner.jpg"; ?>
                        <img width="680" src="<?php echo $src; ?>" style="width: 680px!important; max-width: 680px !important;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        if ((isset($_GET['pid']) && $_GET['pid'] > 0) || (isset($_GET['pid']) && $_GET['pid'] > 0) || isset($token)) {
                            if (isset($rel_content)) {
                                $ename = $rel_content->name;
                                $edate = date('l, F d, Y', strtotime($rel_content->eventstartdatetime));
                                //$edate = date('m/d/Y', strtotime($rel_content->eventstartdatetime));
                                $edate_end = _dt($rel_content->eventenddatetime);

                                $esdate = date_create($rel_content->eventstartdatetime);
                                $esdate = date_format($esdate, "h:i A");

                                $eenddate = date_create($rel_content->eventenddatetime);
                                $eenddate = date_format($eenddate, "h:i A");
                                if (strtotime($edate) > strtotime($edate_end)) {
                                    $edate_end = _dt($rel_content->eventstartdatetime);
                                } else {
                                    $edate_end = "";
                                }
                                ?>
                                <table style="width: 100%">
                                    <tr class="project_lead">
                                        <td style="width: 180px">
                                            <!--<div class="pl_circle">-->
                                            <?php if (isset($lead)) {
                                                $profileImagePath = 'uploads/lead_profile_images/' . $rel_content->id . '/thumb_' . $rel_content->profile_image;
                                                if (file_exists($profileImagePath)) {
                                                    $pl_logo = lead_profile_image($rel_content->id, array(), 'thumb');
                                                } else {
                                                    $pl_init = substr($ename, 0, 1);
                                                }
                                            } else {

                                                if (isset($rel_content->project_profile_image)) {
                                                    $profileImagePath = 'uploads/project_profile_images/' . $rel_content->id . '/thumb_' . $rel_content->project_profile_image;
                                                }

                                                if (isset($profileImagePath) && file_exists($profileImagePath)) {
                                                    $pl_logo = project_profile_image($rel_content->id, array(), 'thumb');
                                                } else {
                                                    $pl_init = substr($ename, 0, 1);
                                                }
                                            }
                                            if (isset($pl_logo)) { ?>
                                                <!--<div class="pl_logo_blk"
                                                     style="height: 130px; width: 130px; border-radius: 50%; border: 3px solid #fff; background: #e0e0e0; text-align: center; display: inline-block; overflow: hidden; margin-top: -85px; position: relative; margin-left: 20px;">-->
                                                <?php echo $pl_logo; ?>
                                                <!--</div>-->
                                            <?php } else { ?>
                                                <!--<div class="pl_logo_blk">--><?php echo $pl_init; ?><!--</div>-->
                                            <?php } ?>
                                            <!--</div>-->
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <table style="width: 100%">
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $ename; ?></strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?php echo $edate; ?><?php !empty($edate_end) ? " - " . $edate_end : "" ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        if ($esdate != "") {
                                                            echo $esdate;
                                                            if ($eenddate != "") {
                                                                echo "-" . $eenddate;
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            <?php }
                        } ?>
                    </td>
                </tr>
                <tr style="height: 10px">
                    <td></td>
                </tr>
                <tr style="border-top: 1px solid">
                    <td></td>
                </tr>
                <tr style="height: 10px">
                    <td></td>
                </tr>
                <tr>
                    <td id="previewemailbody">
                        #emailbody
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


