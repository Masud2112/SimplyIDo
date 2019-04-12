<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 05-03-2018
 * Time: 15:33
 */
extract($signer);
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
if ($signer_type == "member") {
    $ppic = staff_pdf_profile_image($signer_id);
} else {
    $ppic = addressbook_pdf_profile_image($signer_id);
}
$can_sign = "cannotsigne";
$readonly = "readonly";
if (is_staff_logged_in()) {
    $clientemail = get_staff_email(get_staff_user_id());
    $clientAddressbookid = get_addressbook_id_by_email($clientemail);
    if ((get_staff_user_id() == $signer_id && $signer_type == "member") || ($signer_type == "client" && $clientAddressbookid == $signer_id)) {
        $can_sign = "cansigne";
        $readonly = "";
    }
} else {

    if (isset($authclient) && $signer_id == $authclient && $signer_type == $authtype) {
        $can_sign = "cansigne";
        $readonly = "";
    } else {
        $can_sign = "cannotsigne";
        $disabled = "";
        $readonly = "readonly";
    }
}
if ($image != "") {
    $readonly = "readonly";
}
if (!empty($image) && isset($sign_date) && !empty($sign_date)) {
    $sign_date = $sign_date;
} else {
    $sign_date = date('F d, Y');

}
?>
<td width="50%">
    <table width="100%">
        <tr class="signinner">
            <td align="center" width="80px" style="line-height: 80px">
                <?php echo $ppic;//$first . $last ?>
            </td>
            <td width="80%">
                <table width="100%" style="border-bottom: 1px solid #ccc">
                    <tr style="height:150px">
                        <td align="center"><?php echo isset($image) ? $image : "" ?></td>
                    </tr>
                </table>
                <table width="100%">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="left"><?php echo $name; ?><br/>
                            <?php
                            if ($designation == "Account Owner") {
                                $designation = get_brand_option('companyname');
                            }
                            echo $designation; ?>
                            <?php if ($counter_signer == 1) { ?>
                                <br/>
                                <?php echo _l('counter_signer') ?>
                            <?php } ?>
                        </td>
                        <td align="right"><?php echo $sign_date; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</td>