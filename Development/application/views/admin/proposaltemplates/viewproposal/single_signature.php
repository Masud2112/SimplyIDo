<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 05-03-2018
 * Time: 15:33
 */
$flname = explode(' ', $name);
$first = substr($flname[0], 0, 1);
$last = "";
$sign_src = "#";
if (isset($image)) {
    $sign_src = base_url() . 'uploads/proposals_images/signature/' . $proposal->templateid . '/' . $image;
}
if (count($flname) > 0) {
    $last = substr($flname[1], 0, 1);
}

if ($signer_type=="member" && $signer_id == get_staff_user_id()) {
    $can_sign = "cansigne";
} else {
    $can_sign = "cannotsigne";
    $disabled = "";
}
?>
<div id="single_signature_<?php echo $id ?>"
     class="single_signature col-sm-6 <?php echo isset($disabled) ? $disabled : ""; ?> <?php echo $can_sign; ?>">
    <div class="row">
        <div class="signinner">
            <input type="hidden" name="signatures[<?php echo $id ?>][signer_id]" value="<?php echo $signer_id; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][name]" value="<?php echo $name; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][designation]" value="<?php echo $designation; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][counter_signer]"
                   value="<?php echo $counter_signer; ?>">
            <input type="hidden" name="signatures[<?php echo $id ?>][sign_date]"
                   value="<?php echo isset($sign_date) && !empty($sign_date) ? $sign_date : date('d/m/Y'); ?>">
            <!--<input id="signatures_<?php /*echo $id */ ?>" type="hidden" name="signatures[<?php /*echo $id*/ ?>][image]" value="<?php /*echo isset($image)?$image:""*/ ?>">-->
            <input type="hidden" name="signatures[<?php echo $id ?>][signer_type]" class="signer_type"
                   value="<?php echo $signer_type; ?>">
            <div class="col-sm-3 text-center"><span
                        class="signPic"><?php echo $first . $last ?></span><?php if ($counter_signer == 1) { ?>
                    <span class="txtCS">Counter Signer</span>
                <?php } ?></div>
            <div class="col-sm-9">
                <div id="signature_<?php echo $id ?>" class="digital_signature text-center">
                    <div class="add_signature" data-sid="<?php echo $id ?>">
                        <input class="upload_sign <?php $counter_signer == 1 ? "counter_signer" : "" ?>"
                               autocomplete="off" name="signatures[<?php echo $id ?>][image]" type='text'
                               value="<?php echo isset($image) ? $image : "" ?>"
                               placeholder="Add signature" <?php echo !empty($image) ? "readonly" : "" ?> />
                        <?php if (!isset($image) || empty($image)) { ?>
                            <!--<span id="simage_span_<?php /*echo $id */ ?>">Add signature</span>-->
                        <?php } ?>
                        <!--<span><img id="simage_<?php /*echo $id */ ?>" src="<?php /*echo $sign_src; */ ?>" alt="Signature" class="<?php /*echo isset($image)&&!empty($image)?"":"hidden"*/ ?>"/></span>-->
                    </div>
                </div>
                <div class="pull-left">
                    <span class="txtName"><?php echo $name; ?></span>
                    <span class="txtDesg"><?php echo $designation; ?></span>
                </div>
                <div class="pull-right">
                    <span class="sign-date"><b>Date:</b><?php echo isset($sign_date) && !empty($sign_date) ? $sign_date : date('d/m/Y'); ?></span>
                </div>
            </div>
            <?php if ($can_sign == "cannotsigne") { ?>
                <div class="unauthorize">
                    <div class="signer_mesage">
                        <span> You can not sign on behalf of this signer </span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
