<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 17-05-2018
 * Time: 03:24 PM
 */

/*echo "<pre>";
print_r($event);*/
$type = $event->type;
$id = "";
$satrtdatetime = "";
$url="javascript:void(0)";
if ($type == "proposal") {
    $id = $event->templateid;
    $title = $event->name;
    $icon = "fa-file-text-o";
    $datetime = $event->valid_date;
    if ($event->valid_date == "0000-00-00") {
        $datetime = date("Y-m-d h:i:s A", strtotime($event->datecreated . "+10 day"));
    }
    $url=admin_url('proposaltemplates/viewproposal/'.$id);
} elseif ($type == "project") {
    $id = $event->id;
    $title = $event->name;
    $icon = "fa-book";
    $datetime = $event->eventenddatetime;
    $satrtdatetime = $event->eventstartdatetime;
    $location = isset($event->venuename)?$event->venuename:"N\A";
} elseif ($type == "lead") {
    $id = $event->id;
    $title = $event->name;
    $icon = "fa-tty";
    $datetime = $event->eventenddatetime;
    $satrtdatetime = $event->eventstartdatetime;
    $location = isset($event->venuename)?$event->venuename:"N\A";
} elseif ($type == "meeting") {
    $id = $event->meetingid;
    $title = $event->name;
    $icon = "fa-users";
    $datetime = $event->end_date;

    $satrtdatetime = $event->start_date;
    $location = $event->location;
} elseif ($type == "invoice") {
    $id = $event->id;
    $title = format_invoice_number($id);
    $icon = "fa-money";
    $datetime = $event->duedate;
    $url=admin_url('invoices/list_invoices#'.$id);
} elseif ($type == "task") {
    $id = $event->id;
    $title = $event->name;
    $icon = "fa-tasks";
    $datetime = $event->duedate;
    $satrtdatetime = $event->startdate;
    if ($event->startdate == "0000-00-00") {
        $satrtdatetime = $event->dateadded;
        $url=admin_url('tasks/dashboard/'.$id);
    }
}
$popupTitle = "<i class='fa " . $icon . "'></i>  " . ucfirst($type);

$eventmonth = date("M", strtotime($satrtdatetime));
$eventday = date("j", strtotime($satrtdatetime));
$eventweekday = strtoupper(date("D", strtotime($satrtdatetime)));
$eventyear = date("Y", strtotime($satrtdatetime));
$full_date = _d($satrtdatetime);
$full_time = _time($satrtdatetime);

$eventEndweekday = strtoupper(date("D", strtotime($datetime)));
$fullEnddate = _d($datetime);
$fullEndtime = _time($datetime);
$datetime = strtoupper(date('D, ', strtotime($datetime))).$fullEnddate . " at " . $fullEndtime;
?>
<div class="mtop10 eventlistrow">
    <div class="icon_section mright10">
        <?php if ($type == "task" || $type == "lead" || $type == "project" || $type == "meeting") { ?>
            <a href="javascript:void(0)" class="" title="<?php echo $popupTitle; ?>" rel="popover"
               data-popover-content="#calendarPopup_<?php echo $type . $id; ?>"><i
                    class="fa <?php echo $icon; ?> menu-icon"></i></a>
        <?php } else { ?>
            <a href="<?php echo $url; ?>"><i class="fa <?php echo $icon; ?> menu-icon"></i></a>
        <?php } ?>

        <?php if ($type == "task" || $type == "lead" || $type == "project" || $type == "meeting") { ?>
            <div id="calendarPopup_<?php echo $type . $id; ?>" class="pinePopUp hide">
                <div class="col-sm-2 popupover_date">
                    <div class="carddate-block">
                        <div class="card_date"
                             title="<?php echo $eventyear; ?>">
                            <div class="card_month">
                                <small><?php echo $eventmonth; ?></small>
                            </div>
                            <div class="card_d">
                                <strong><?php echo date('d', strtotime($satrtdatetime)) ?></strong>
                            </div>
                            <div class="card_day">
                                <small><?php echo $eventweekday ?></small>
                            </div>
                        </div>
                        <div class="card_year">
                            <small><?php echo $eventyear ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-10 popupover_content">
                    <h4 class="mtop0 mbot5">
                        <strong><?php echo $title ?></strong>
                    </h4>
                    <?php if ($type == "project" || $type == "lead" || $type == "meeting") { ?>
                        <div class="mbot5">
                            <i class="fa fa-map-marker"></i>
                            <?php echo isset($location) ? $location : "N/A"; ?>
                        </div>
                    <?php } ?>
                    <!--<div class="mbot5"><i
                                class="fa fa-clock-o"></i> <?php /*echo $full_time; */ ?>
                    </div>-->
                    <?php if ($type == "lead") { ?>
                        <div class="mbot5"><i
                                class="fa fa-calendar-o"></i> <?php echo $eventweekday . ", " . $full_date . " at " . $full_time; ?>
                        </div>
                        <div class="mbot5 mleft30"><strong>--to--</strong></div>
                        <div class="mbot5">
                            <i class="fa fa-calendar-o"></i>
                            <?php echo isset($datetime) ? $eventEndweekday . ", " . $fullEnddate . " at " . $fullEndtime : "N/A"; ?>
                        </div>
                    <?php } else { ?>
                        <div class="mbot5"><i
                                class="fa fa-calendar-o"></i> <?php echo isset($datetime) ? $eventEndweekday . ", " . $fullEnddate . " at " . $fullEndtime : "N/A"; ?>
                            <br /><strong>
                                <?php
                                if (strtotime("now") > strtotime($fullEnddate)) {
                                    echo "(" . time_ago($fullEnddate) . ")";
                                }else{
                                    echo "(" . after_time($fullEnddate) . ")";
                                } ?>
                            </strong>
                        </div>
                    <?php } ?>
                    <?php if ($type == "meeting") { ?>
                        <div class="mbot5"><span class="tooltip_description">
                                                        <?php echo $event->description; ?>
                                                    </span></div>
                    <?php } ?>
                    <!--<div class="mbot5"><span class="tooltip_status">
                                                        <?php /*echo $single_msg_pin['msg_status']; */ ?>
                                                    </span></div>-->
                    <div class="mbot5"><span
                            class="no-img staff-profile-image-small">
                                                      <?php echo isset($event->assigned) ? staff_profile_image($event->assigned, array('staff-profile-image-small')) : staff_profile_image(0, array('staff-profile-image-small')); ?>
                                                    </span>
                        <span><?php
                            if (isset($event->assigned) && $event->assigned > 0) {
                                echo isset($event->firstname) ? $event->firstname : "" ?><?php echo isset($event->lastname) ? $event->lastname : "";
                            } else {
                                echo "Not Assigned";
                            }
                            ?></span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="eRow">
        <div class="eTitle"><?php echo $title; ?></div>
        <div class="eDate"><?php echo $datetime; ?></div>
    </div>
</div>
<script type="text/javascript">

    $('[rel="popover"]').popover({
        placement: "top",
        trigger: "hover",
        container: 'body',
        html: true,
        content: function () {
            var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
            return clone;
        }
    }).click(function (e) {
        e.preventDefault();
    });
</script>