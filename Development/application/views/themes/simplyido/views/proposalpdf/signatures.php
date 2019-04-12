<?php
/**/
/*if (isset($_GET['pid']) && $_GET['pid'] > 0) {
    $rel_link = "?pid=" . $_GET['pid'];
} elseif (isset($_GET['lid']) && $_GET['lid'] > 0) {
    $rel_link = "?lid=" . $_GET['lid'];
} else {
    $rel_link = "";
}*/
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
?>
<?php echo _l('signature_note'); ?><br/><br/><br/><br/>
<table width="100%">
    <tr><?php if (isset($signatures) && count($signatures) > 0 && !empty($signatures)) {
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
                <?php include "single_signature.php"; ?>
                <?php
                if (count($signatures) > 2 && ($key) % 2 > 0 && ($key+1) < count($signatures)) {
                    echo "</tr><tr>";
                }
            }
        } ?>
    </tr>
</table>
<?php /*die('<--here')*/?>
