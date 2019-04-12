<?php
/**/

if (isset($_GET['pid']) && $_GET['pid'] > 0) {
    $rel_link = "?pid=" . $_GET['pid'];
} elseif (isset($_GET['lid']) && $_GET['lid'] > 0) {
    $rel_link = "?lid=" . $_GET['lid'];
} else {
    $rel_link = "";
}
$removed_sections = array();
if (isset($proposal)) {
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('signatures', $removed_sections) ? "removed_section" : "";
    $checked = in_array('signatures', $removed_sections) ? "checked" : "";
}
$signed = 0;
if (isset($proposal->feedback)) {
    $signed = $proposal->feedback->total_signed;
}
$total_signed = 0;
$client_signed = 0;
$current_signer = array();
/**/
?>
<div id="signatures" class="<?php echo $class ?>">
    <div class="row">
        <div class="signature_header gallery_header col-sm-12">
            <!--<div class="row mbot10">
                <div class="col-sm-6"><h4><i class="fa fa-pencil mright10"></i><b>Signatures</b></h4></div>
                <div class="col-sm-6 col-right">
                </div>
            </div>-->
            <p><small><em><?php echo _l('signature_note'); ?></em></small></p>
            <div class="section_body">
                <div class="clearfix"></div>
                <div class="signatures_list row">
                    <!--<div class="hrow"><span class="hTxt"><?php /*echo isset($signatures)? count($signatures):"0" */ ?> Items</span> <i class="fa fa-caret-up"></i></div>-->
                    <div class="row rowWrap">
                        <?php if (isset($signatures) && count($signatures) > 0 && !empty($signatures)) {
                            $total_signer = count($signatures);
                            foreach ($signatures as $key => $signer) {
                                if (isset($signer['image']) && !empty($signer['image']) && $signer['signer_type'] == 'client') {
                                    $client_signed++;
                                }
                            }
                            foreach ($signatures as $key => $signer) {
                                $signer_id = $signer['signer_id'];
                                $signer_type = $signer['signer_type'];
                                if (get_staff_user_id() == $signer_id && $signer_type == "member") {
                                    $current_signer = $signatures[$key];
                                }
                                if (!empty($signer['image'])) {
                                    $total_signed++;
                                }
                                $signer['disabled'] = "active";
                                if ($signer['counter_signer'] == 1) {
                                    $signer['disabled'] = "disabled";
                                    if ($client_signed > 0) {
                                        $signer['disabled'] = "active";
                                    }
                                }
                                $signer['id'] = $key;
                                ?>
                                <?php $this->load->view('proposaltemplates/single_signature', $signer); ?>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="total_signed" value="<?php echo $signed; ?>" class="total_signed">