<?php

$disabled = "";
$bullets = array(
    array('name' => 'COVER PAGE', 'color' => '', 'icon' => 'fa-file-o', 'id' => 'introduction_step','char'=>'P'),
    array('name' => 'QUOTE', 'color' => '#00bcd4', 'icon' => 'fa-file-o', 'id' => 'quote_step','char'=>'Q'),
    array('name' => 'AGREEMENT', 'color' => '#9c27b0', 'icon' => 'fa-file-o', 'id' => 'agreement_step','char'=>'A'),
    array('name' => 'INVOICE', 'color' => '', 'icon' => 'fa-file-o', 'id' => 'message_step','char'=>'I'),
    array('name' => 'PAYMENT', 'color' => '#f44336', 'icon' => 'fa-file-o', 'id' => 'gallery_step','char'=>'$'),
);
$numStatuses = count($bullets);
$processwidth = 100 / $numStatuses;
?>
<div class="clearfix"></div>
<div class="">
    <div class="row leads-overview proposal_overview" style="display: block;">
        <?php foreach ($bullets as $status) {
            $class="";
            $id = $status['name'];
            if ($status['name'] == "COVER PAGE") {
                $id = "introduction";
                $class="active";
            } ?>
            <div id="<?php echo $status['id']; ?>" class="col-sm-2 proposal_step <?php echo $class; ?>"
                 style="width:<?php //echo $processwidth . '%' ; ?>">
                <a href="#<?php echo strtolower($id); ?>">
                    <i class="fa <?php echo $status['icon']; ?>"><span class="icon_char"><?php echo $status['char']; ?></span></i>
                    <span><?php echo $status['name']; ?></span>
                </a>
            </div>
        <?php } ?>
        <div id="<?php echo $status['id']; ?>" class="col-sm-2 proposal_status">
                <span class="label-success p7 inline-block pull-right text-center"><?php echo strtoupper($proposal->status); ?></span>
        </div>
    </div>
</div>