<div class="row">
    <?php /*if (isset($proposal) && $proposal->banner != NULL) { */ ?>
    <div class="form-group">
        <div class="col-md-12">
            <div class="banner-block">
                <?php
                $src = "";
                if (!empty($proposal->banner)) {
                    $src = base_url() . "uploads/proposals_images/banner/" . $proposal->templateid . "/" . $proposal->banner;
                } else {
                    $src = base_url() . "assets/images/default_banner.jpg";
                } ?>
                <?php //echo proposaltemplate_banner($proposal->templateid, array('banner', 'img-responsive', 'proposaltemplate-profile-image-thumb')); ?>
                <img src="<?php echo $src; ?>"/>
            </div>
        </div>
    </div>
    <?php /*} */ ?>
</div>
<?php
if (isset($rel_content) && !empty($rel_content)) {
    if (isset($proposal)) {
        if ($proposal->issued_date != '0000-00-00') {
            /*$proposal->issued_date = date('l, F d, Y', strtotime($proposal->issued_date));*/
            $proposal->issued_date = date('m/d/Y', strtotime($proposal->issued_date));
        } else {
            /*$proposal->issued_date = date('l, F d, Y', strtotime($proposal->datecreated));*/
            $proposal->issued_date = date('m/d/Y', strtotime($proposal->datecreated));
        }
        if ($proposal->valid_date != '0000-00-00') {
            /*$proposal->valid_date = date('l, F d, Y', strtotime($proposal->valid_date));*/
            $proposal->valid_date = date('m/d/Y', strtotime($proposal->valid_date));
        } else {
            /*$proposal->valid_date = date('l, F d, Y', strtotime('+10 days', strtotime($proposal->datecreated)));*/
            $proposal->valid_date = date('m/d/Y', strtotime('+10 days', strtotime($proposal->datecreated)));
        }
    }
    $ename = $rel_content->name;
    $edate = date('l, F d, Y', strtotime($rel_content->eventstartdatetime));
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

    if (isset($venue)) {
        $v_name = $venue->venuename;
        $v_location = $venue->venueaddress . " " . $venue->venueaddress2;
        $v_state = $venue->venuestate;
        $v_city = $venue->venuecity;
        $v_zip = $venue->venuezip;
    }
    ?>
    <div class="row topblocks">
        <div class="col-sm-4 project_lead text-center">
            <div class="pl_head">
                <div class="pl_circle">
                    <?php echo isset($lead) ? "L" : "P"; ?>
                </div>
            </div>
            <div class="pl_inner">
                <div class="ename"><?php echo $ename; ?></div>
                <div class="edate"><?php echo $edate; ?><?php !empty($edate_end) ? " - " . $edate_end : "" ?></div>
                <div class="e_st_en_date">
                    <span class="esdate"><?php echo $esdate; ?></span>
                    - <span class="eenddate"><?php echo $eenddate; ?></span>
                </div>
                <?php if (isset($venue)) { ?>
                    <div class="v_name"><?php echo $v_name; ?></div>
                    <div class="v_location"><?php echo $v_location; ?></div>
                    <div class="v_city"><?php echo $v_city . ", " . $v_state . ", " . $v_zip; ?></div>
                <?php } ?>
            </div>
        </div>
        <div class="col-sm-4 clients text-center">
            <div class="pl_head">
                <span class="pl_circle">
                <?php echo "C"; ?>
                </span>
                <?php if (isset($clients) && !empty($clients)) {
                    foreach ($clients as $client) {
                        $name = $client['firstname'] . " " . $client['lastname'];
                        $email = isset($client['email']) ? $client['email'] : "";
                        $phone = isset($client['phone']) ? $client['phone'] : "";

                        ?>
                        <div class="client">
                            <div class="client_name"><?php echo $name; ?></div>
                            <div class="client_email"><?php echo $email; ?></div>
                            <div class="client_phone"><?php echo $phone; ?></div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="col-sm-4 venues text-center">
            <div class="pl_head">
                <span class="pl_circle">
                <?php echo staff_profile_image($staff->staffid); ?>
                </span>
            </div>
            <div class="pl_inner">
                <div class="proposal"><strong>Proposal #:</strong><?php echo isset($proposal) ? $proposal->proposal_version : 00001 ?>
                </div>
                <div class="issued"><strong>Issued :</strong>
                    <?php /*echo isset($proposal) ? $proposal->issued_date : date('l, F d, Y') */?>
                    <?php echo isset($proposal) ? $proposal->issued_date : date('m/d/Y') ?>
                </div>
                <div class="validity">
                    <strong>Valid Until:</strong>
                    <?php /*echo isset($proposal) ? $proposal->valid_date : date('l, F d, Y', strtotime('+10 days')) */?>
                    <?php echo isset($proposal) ? $proposal->valid_date : date('m/d/Y', strtotime('+10 days')) ?>
                </div>
                <!--<div class="vendors-name"><?php /*echo $staff->firstname . " " . $staff->lastname; */?></div>-->
                <div class="vendors-name"><?php echo get_brand_option('invoice_company_name'); ?></div>
                <div class="vendors-email"><?php echo $staff->email; ?></div>
                <div class="vendors-phone"><?php echo $staff->phonenumber; ?></div>
                <!--<div class="vendors-mobile"><?php /*echo $staff->phonenumber; */ ?></div>-->
            </div>
        </div>
    </div>
<?php } ?>
