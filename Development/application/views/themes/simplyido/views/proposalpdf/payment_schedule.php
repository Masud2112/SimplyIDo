<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 06-03-2018
 * Time: 16:04
 */
$removed_sections = array();
if (isset($proposal)) {
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('payments', $removed_sections) ? "removed_section" : "";
    $checked = in_array('payments', $removed_sections) ? "checked" : "";
}
/**/
?><table id="payments" class="<?php echo $class ?>">
    <tr style="height: 50px; line-height: 50px" class="row">
        <th style="height: 50px; line-height: 50px" class="files_header col-sm-12">
            <b>PAYMENT SCHEDULE</b>
        </th>
    </tr>
    <tr>
        <td class="section_body">
            <?php if (isset($proposal) && $proposal->ps_template > 0) {
                $pmt_sdl_template = $proposal->pmt_sdl_template;
                include "paymentschedule_temp.php";
            } else {
                if (isset($rec_payment) && !empty($rec_payment)) {
                    include "rec_payment_temp.php";
                }
            } ?>
        </td>
    </tr>
</table>
