<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 01-08-2018
 * Time: 06:32 PM
 */

?>
<div class="<?php echo strtolower($title) ?>_header psl_section_head ">
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
    <?php if (isset($rel_content)) {
    $ename = $rel_content->name;
    //$edate = date('l, F d, Y', strtotime($rel_content->eventstartdatetime));
    $edate = date('m/d/Y', strtotime($rel_content->eventstartdatetime));
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
                            $pl_logo= lead_profile_image($rel_content->id, array(), 'thumb');
                        } else {
                            $pl_init= substr($ename, 0, 1);
                        }
                    } else {

                        if (isset($rel_content->project_profile_image)) {
                            $profileImagePath = 'uploads/project_profile_images/' . $rel_content->id . '/thumb_' . $rel_content->project_profile_image;
                        }

                        if (isset($profileImagePath) && file_exists($profileImagePath)) {
                            $pl_logo= project_profile_image($rel_content->id, array(), 'thumb');
                        } else {
                            $pl_init= substr($ename, 0, 1);
                        }
                    } 
                        if(isset($pl_logo)){ ?>
                        <div class="pl_logo_blk"><?php echo $pl_logo; ?></div>
                        <?php }else{ ?>
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
            <?php if (strtolower($title) == 'agreement') { ?>
                <div class="proposal_status inline-block"><span
                            class="label-success p7 inline-block pull-right text-center"><?php echo strtoupper($proposal->status); ?></span>
                </div>
            <?php } ?>
            <div class="pl_inner inline-block">
                <div class="proposal"><strong>#<?php echo strtoupper(substr($title, 0, 1)) ?>
                        -</strong><?php echo isset($proposal) ? $proposal->proposal_version : 00001 ?>
                </div>
                <div class="issued"><strong>ISSUED :</strong>
                    <?php //echo isset($proposal) ? $proposal->issued_date : date('l, F d, Y') ?>
                    <?php echo isset($proposal) ? date('m/d/Y', strtotime($proposal->issued_date)): date('m/d/Y') ?>
                </div>
                <div class="validity">
                    <strong>Due:</strong>
                    <?php //echo isset($proposal) ? $proposal->valid_date : date('l, F d, Y', strtotime('+10 days')) ?>
                    <?php echo isset($proposal) ? date('m/d/Y', strtotime($proposal->valid_date)) : date('m/d/Y', strtotime('+10 days')) ?>
                </div>
            </div>

        </div>
    </div>
    <?php if (strtolower($title) == 'quote') { ?>
        <div class="row pl_address_blk">
            <div class="col-sm-4"></div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4 proposal_status"><span
                        class="label-success p7 inline-block pull-right text-center"><?php echo strtoupper($proposal->status); ?></span>
            </div>
        </div>
    <?php } ?>
    <?php } ?>
</div>
