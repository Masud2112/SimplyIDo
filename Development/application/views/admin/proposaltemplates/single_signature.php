<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 05-03-2018
 * Time: 15:33
 */

$flname = isset($name) ? explode(' ', $name) : array();
$flname = array_filter($flname);
$flname = array_values($flname);
$first = !empty($flname) ? substr($flname[0], 0, 1) : "";
$last = "";
if (count($flname) > 0) {
    $last = substr($flname[1], 0, 1);
}
if($signer_type == "member"){
    $ppic = staff_profile_image($signer_id,array('signerPic'));
}else{
    $ppic = addressbook_profile_image($signer_id,array('signerPic'));
}
?>
<div id="single_signature_<?php echo $id ?>" class="single_signature col-sm-6">
    <div class="row">
        <div class="signinner">
            <input type="hidden" name="signatures[<?php echo $id ?>][signer_id]" value="<?php echo $signer_id; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][name]" value="<?php echo $name; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][designation]" value="<?php echo $designation; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][counter_signer]" class="counter_signer"
                   value="<?php echo $counter_signer; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][signer_type]" class="signer_type"
                   value="<?php echo $signer_type; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][image]" class="signer_image"
                   value="<?php echo isset($image) ? $image : ""; ?>">
            <div class="col-sm-3 text-center"><span
                        class="signPic"><?php echo $ppic //$first . $last ?></span><?php if ($counter_signer == 1) { ?>
                    <span class="txtCS">Counter Signer</span>
                <?php } ?></div>
            <div class="col-sm-9">
                <a href="javascript:void(0)" class="proosal_sign_remove" data-sid="<?php echo $signer_id; ?>"
                   data-pid="#single_signature_<?php echo $id ?>">
                    <i class="fa fa-close"></i>
                </a>
                <div class="pull-left">
                    <span class="txtName"><?php echo $name; ?></span>
                    <span class="txtDesg"><?php
                        if($designation == "Account Owner"){
                            $designation = get_brand_option('companyname');
                        }
                        echo $designation; ?></span>
                </div>
                <div class="pull-right">
                    <span class="sign-date">TBD</span>
                </div>
            </div>
        </div>
    </div>
</div>
