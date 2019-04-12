<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 28-02-2018
 * Time: 16:38
 */

$merge_fields = array();
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
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('agreement', $removed_sections) ? "removed_section" : "";
    $checked = in_array('agreement', $removed_sections) ? "checked" : "";
}
?>
<div id="agreement" class="<?php echo $class ?>">
    <?php
    $this->load->view('admin/proposaltemplates/viewproposal/psl_section_head',array('title'=>"agreement"));
    ?>
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
                                    if($count==$no_payments){
                                        $percentage="100%";
                                    }
                                }else{
                                    $percentage="$".$schedule['price_amount'];
                                }
                                /*echo "<pre>";
                                print_r($schedule);*/
                                $paymentschedule .= '<li class="">';
                                $paymentschedule .= '<strong>';
                                $paymentschedule .= 'Payment' . ($key + 1) . ': ';
                                $paymentschedule .= '</strong>';
                                $paymentschedule .= $percentage . ' of the ' . $amunt_type . $due;
                                $paymentschedule .= ':<span id="payment-'.$schedule['paymentdetailid'].'" class="payment-price"></span>';
                                $paymentschedule .= '</li>';
                                $total_payments--;
                                $count++;
                            }
                            $paymentschedule .= '</ul>';
                        } else {
                            if (isset($rec_payment) && !empty($rec_payment)) {
                                $this->load->view('admin/proposaltemplates/viewproposal/rec_payment_temp', $rec_payment);
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
</div>
</div>


