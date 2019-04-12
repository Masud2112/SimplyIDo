<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 28-02-2018
 * Time: 16:38
 */

if (isset($_GET['pid']) && $_GET['pid'] > 0) {
    $rel_link = "?pid=" . $_GET['pid'];
} elseif (isset($_GET['lid']) && $_GET['lid'] > 0) {
    $rel_link = "?lid=" . $_GET['lid'];
} else {
    $rel_link = "";
}
/*$merge_fields = array();
$merge_fields = array_merge($merge_fields, get_staff_merge_fields($proposal->created_by, ""));

if (isset($tasks) && !empty($tasks)) {
    $merge_fields = array_merge($merge_fields, get_task_merge_fields($tasks[0]['id'], ""));
}
$merge_fields = array_merge($merge_fields, get_proposal_merge_fields($proposal->templateid));
$merge_fields = array_merge($merge_fields, get_agreement_meetings_merge_fields($proposal->rel_type, $proposal->rel_id));
$merge_fields = array_merge($merge_fields, get_lead_merge_fields($proposal->rel_id));
$merge_fields = array_merge($merge_fields, get_project_merge_fields($proposal->rel_id, array(
    'customer_template' => true
)));
if (isset($clients)) {
    foreach ($clients as $client) {
        $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($client['id'], $client['id']));
    }
}
$merge_fields = array_merge($merge_fields, get_agreement_other_merge_fields());
foreach ($merge_fields as $oldkey => $merge_field) {
    $newkey = str_replace("{", "", $oldkey);
    $newkey = str_replace("}", "", $newkey);
    $newkey = explode("_", $newkey);
    $i = 0;
    foreach ($newkey as $key) {
        $key = str_replace("teammember", 'Member', $key);
        $key = str_replace("contact", 'Client', $key);
        $newkey[$i] = ucfirst($key);
        $i++;
    }
    $newkey = implode("", $newkey);
    $newkey = str_replace("teammember", "Member", $newkey);
    $merge_fields[$newkey] = $merge_field;
    unset($merge_fields[$oldkey]);
}
$proposal->agreement = _parse_agreement_template_merge_fields($proposal->agreement, $merge_fields);

$removed_sections = array();
if (isset($proposal)) {
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}*/
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('agreement', $removed_sections) ? "removed_section" : "";
    $checked = in_array('agreement', $removed_sections) ? "checked" : "";
}
?>
<div id="agreement" class="<?php echo $class ?>">
    <?php
    $this->load->view('proposaltemplates/psl_section_head', array('title' => "agreement"));
    ?>
    <div class="agreement-page-inner">
        <div class="row">
            <div class="files_header col-sm-12">
                <div class="">
                    <h4 class=""><b>Agreement</b></h4>
                </div>
                <div class="section_body">
                    <div class="agreement_form">
                        <div class="agreement_txt">


                            <?php
                            $services = '<div class="selected_services"></div>';
                            $paymentschedule = '';

                            $paymentschedule .= '<div class="paymentschedule">';
                            $paymentschedule .= '<h5><b>PAYMENT SCHEDULE</b></h5>';
                            if (isset($proposal) && $proposal->ps_template > 0) {
                                $pmt_sdl_template = $proposal->pmt_sdl_template;
                                $schedules = $pmt_sdl_template['paymentschedule']->schedules;
                                $no_payments = $total_payments = count($schedules);
                                $count = 1;
                                $paymentschedule .= '<ul>';
                                //$schedules = array_reverse($schedules);
                                foreach ($schedules as $key => $schedule) {
                                    if ($schedule['duedate_type'] == "upon_signing") {
                                        $schedules['temp'] = $schedules[0];
                                        $schedules[0] = $schedules[$key];
                                        $schedules[$key] = $schedules['temp'];
                                        unset($schedules['temp']);
                                    }

                                }
                                ?>

                                <?php foreach ($schedules as $key => $schedule) {
                                    if ($schedule['duedate_type'] == "upon_signing") {
                                        $due = " upon acceptance";
                                    } elseif ($schedule['duedate_type'] == "midway") {
                                        $due = " midway";
                                    } elseif ($schedule['duedate_type'] == "custom") {
                                        if (empty($schedule['duedate_number'])) {
                                            $schedule['duedate_number'] = 10;
                                        }
                                        if ($schedule['duedate_criteria'] == "beforeproject") {
                                            $due = $schedule['duedate_number'] . " days before the project";
                                        } elseif ($schedule['duedate_criteria'] == "afterproject") {
                                            $due = $schedule['duedate_number'] . " days after the project";
                                        } else {
                                            $due = $schedule['duedate_number'] . " days after invoice has been sent";
                                        }
                                    } elseif ($schedule['duedate_type'] == "fixed_date") {
                                        $due = " on(" . $schedule['duedate_date'] . ")";
                                    } else {
                                        $due = " on project date";
                                    }
                                    if ($count == 1) {
                                        $amunt_type = "total due";
                                    } else {
                                        $amunt_type = "remaining balance due";
                                    }
                                    if ($schedule['price_type'] == "divide_equally") {
                                        $percentage = (100 / $total_payments);
                                        if (is_float($percentage)) {
                                            $percentage = number_format((float)$percentage, 2, '.', '') . "%";
                                        } else {
                                            $percentage = $percentage . "%";
                                        }
                                    } elseif ($schedule['price_type'] == "percentage") {
                                        $percentage = $schedule['price_percentage'] . "%";
                                        if ($count == $no_payments) {
                                            $percentage = "100%";
                                        }
                                    } else {
                                        $percentage = "$" . $schedule['price_amount'];
                                    }
                                    /*echo "<pre>";
                                    print_r($schedule);*/
                                    $paymentschedule .= '<li class="">';
                                    $paymentschedule .= '<strong>';
                                    $paymentschedule .= 'Payment' . ($key + 1) . ': ';
                                    $paymentschedule .= '</strong>';
                                    $paymentschedule .= $percentage . ' of the ' . $amunt_type . $due;
                                    $paymentschedule .= ':<span id="payment-' . $schedule['paymentdetailid'] . '" class="payment-price"></span>';
                                    $paymentschedule .= '</li>';
                                    $total_payments--;
                                    $count++;
                                }
                                $paymentschedule .= '</ul>';
                            } else {
                                if (isset($rec_payment) && !empty($rec_payment)) {
                                    $this->load->view('proposaltemplates/rec_payment_temp', $rec_payment);
                                }
                            }
                            $paymentschedule .= '</div>';
                            ?>

                            <?php
                            if (isset($proposal)) {
                                $proposal->agreement = str_replace('#paymentschedule', $paymentschedule, $proposal->agreement);
                                $proposal->agreement = str_replace('#services', $services, $proposal->agreement);
                                echo html_entity_decode($proposal->agreement);
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (!in_array('signatures', $removed_sections)) {
            $this->load->view('proposaltemplates/signatures');
        }
        ?>
    </div>
</div>
<div class="proposal_actions text-center mbot25">
    <?php if ($proposal->status != "decline") { ?>

        <div class="inline-block <?php echo isset($token) ? "" : "disabled" ?>">
            <a class="btn btn-decline <?php echo isset($token) ? "" : "disabled" ?>"
               href="<?php echo site_url('proposaltemplates/updatestatus/decline/' . $proposal->templateid) ?>">
                <i class="fa fa-remove" aria-hidden="true"></i>
                <?php echo _l('decline'); ?>
            </a>
        </div>
    <?php } ?>
    <div class="inline-block">
        <a class="btn btn-info"
           href="<?php echo $proposal->status == "draft" ? admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link : admin_url('proposaltemplates') . $rel_link; ?>"
           onclick="self.close()">
            <i class="fa fa-reply" aria-hidden="true"></i>
            <?php echo _l('exit_proposal'); ?>
        </a>
    </div>
    <div class="inline-block">
        <a class="btn proposal_step slickNext btn-primary" href="#quote"
           data-tab="quote">
            <i class="fa fa-angle-left mleft10" aria-hidden="true"></i>
            <?php echo _l('quote'); ?>

        </a>
    </div>
    <div class="inline-block">
        <?php /*if ($proposal->status != "decline") {*/
        /*if (!isset($proposal->feedback) || (isset($proposal->feedback) && $proposal->feedback->is_invoiced == 0)) {*/
        $accepted = array();
        /*if (isset($proposal->feedback)) {
            $accepted = !empty($proposal->feedback->accepted) ? json_decode($proposal->feedback->accepted) : array();
            $accepted = array_unique($accepted);
        }*/
        if (is_staff_logged_in() && $authtype=="member") { ?>
            <div class="inline-block <?php echo !empty($current_signer) && $current_signer['image'] != "" ? "disabled" : "" ?>">
                <a href="javascript:void(0)" data-signer="<?php echo $authclient; ?>"
                   data-proposal="<?php echo $proposal->templateid; ?>"
                   class="btn add_member_sign <?php echo !empty($current_signer) && $current_signer['image'] != "" ? "disabled" : "" ?> btn-primary"><?php echo _l('accept'); ?>
                </a>
            </div>
            <?php if($proposal->status == "draft" ){ ?>
                <a href="<?php echo site_url('proposal/createemail/'.$proposal->templateid.$rel_link)?>" class="btn btn-info">Create Email</a>
            <?php } ?>
        <?php } else {
        if ($proposal->isclosed == 0 || $proposal->isarchieve == 0) {
        ?>
    <input data-signer="<?php echo $authclient; ?>"
           type="submit"
           class="btn proposal_accept btn-primary"
           value="<?php echo _l('accept'); ?>" <?php echo in_array($authclient, $accepted) ? "disabled" : ""; ?>
    >
        <?php }
        }
        /*} else { */ ?><!--
                <a class="btn proposal_step slickNext btn-primary" href="#invoice"
                   data-tab="invoice">
                    <?php /*echo _l('invoice'); */ ?>
                    <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                </a>
            --><?php /*}*/
        /*if (!empty($current_signer) && $current_signer['image'] == "" && isset($proposal->feedback) && $proposal->feedback->is_invoiced == 1) {
            $accepted = !empty($proposal->feedback->accepted) ? json_decode($proposal->feedback->accepted) : array();
            */ ?><!--
                <a href="javascript:void(0)" data-signer="<?php /*echo $authclient; */ ?>"
                   data-proposal="<?php /*echo $proposal->templateid; */ ?>"
                   class="btn add_member_sign btn-primary"><?php /*echo _l('accept'); */ ?></a>
            --><?php /*}*/
        /*} */ ?>
    </div>
</div>
<!--</div>-->


