<?php
/**
 * Created by PhpStorm.
 * User: Masud
 * Date: 21-12-2018
 * Time: 16:40
 */
?>
<?php
/*echo "<pre>";
print_r($proposal);
die('<--here');*/
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
?>
<?php $this->load->view('proposaltemplates/includes/head'); ?>
    <div class="wrapper">
        <div class="content">
            <div class="emailinputs">
                <div class="<?php echo strtolower($title) ?>_header psl_section_head wrapper">
                    <div class="row psl_section_head_blk">
                        <div class="col-md-6">
                            <div class="banner-block copLogo_blk">
                                <?php echo get_brand_logo_img($proposal->brandid); ?>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <h3 class="text-right"><strong><?php echo strtoupper($title) ?></strong></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="banner-block psl_banner_blk">
                                <?php $src = base_url() . "assets/images/default_banner.jpg"; ?>
                                <img src="<?php echo $src; ?>"/>
                            </div>
                        </div>
                    </div>
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
                            <div class="row ">
                                <div class="project_lead col-sm-6">
                                    <div class="pl_head">
                                        <div class="pl_circle">
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
                                                <div class="pl_logo_blk"><?php echo $pl_logo; ?></div>
                                            <?php } else { ?>
                                                <div class="pl_init_blk"><?php echo $pl_init; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="pl_inner">
                                        <div class="ename"><?php echo $ename; ?></div>
                                        <div class="edate"><?php echo $edate; ?><?php !empty($edate_end) ? " - " . $edate_end : "" ?></div>
                                        <div class="e_st_en_date">
                                            <span class="esdate"><?php echo $esdate; ?></span>
                                            - <span class="eenddate"><?php echo $eenddate; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 venues text-right">

                                </div>
                            </div>
                            <?php if (strtolower($title) == 'quote') { ?>
                                <div class="row pl_address_blk">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4"></div>

                                </div>
                            <?php } ?>
                        <?php }
                    } ?>
                </div>
                <div class="clearfix"></div>
                <hr/>
                <div id="previewemailbody">
                    <p>Please use the button below to view the Proposal. We have included items discussed as well as some additional options that may be of interest.</p>
                    <p>If you have any questions you can reach us anytime by text, email, or phone. Looking forward to a fantastic event!</p>
                    <a href="<?php echo site_url('proposal/view/'.$token)?>" class="btn btn-info" target="_blank">
                        <i class="fa fa-file-text-o mright5"></i><?php echo $proposal->name; ?>
                    </a><br /><br /><br />
                    <p>Musically yours,</p>


                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('proposaltemplates/includes/scripts'); ?>