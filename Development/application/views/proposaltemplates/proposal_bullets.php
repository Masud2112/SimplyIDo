<?php

$disabled = "";
$bullets = array(
    array('name' => 'COVER PAGE', 'color' => '', 'icon' => 'fa-file-o', 'id' => 'introduction_step', 'char' => 'P'),
    array('name' => 'QUOTE', 'color' => '#00bcd4', 'icon' => 'fa-file-o', 'id' => 'quote_step', 'char' => 'Q'),
    array('name' => 'AGREEMENT', 'color' => '#9c27b0', 'icon' => 'fa-file-o', 'id' => 'agreement_step', 'char' => 'A'),
);
$invoice = array('name' => 'INVOICE', 'color' => '', 'icon' => 'fa-file-o', 'id' => 'invoice_step', 'char' => 'I');
$payment = array('name' => 'PAYMENT', 'color' => '#f44336', 'icon' => 'fa-file-o', 'id' => 'payment_step', 'char' => '$');
if (isset($proposal->feedback) && $proposal->feedback->is_invoiced == 1) {
    array_push($bullets,$invoice);
    if($proposal->isclosed==0 && $proposal->status!="complete"){
        array_push($bullets,$payment);
    }
}
$numStatuses = count($bullets);
$processwidth = 100 / $numStatuses;

?>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12">
        <div class="proposal_bullets">
            <?php foreach ($bullets as $status) {
                $class = "";
                $id = $status['name'];
                if ($status['name'] == "COVER PAGE") {
                    $id = "introduction";
                    $class = "active";
                }
                ?>
                <div id="<?php echo $status['id']; ?>" class="col-sm-2 proposal_step <?php echo $class; ?>"
                     style="width:<?php //echo $processwidth . '%' ; ?>">
                    <a href="javascript:void(0)<?php //echo strtolower($id); ?>" data-tab="<?php echo $status['id']; ?>">
                        <i class="fa <?php echo $status['icon']; ?>"><span
                                    class="icon_char"><?php echo $status['char']; ?></span></i>
                        <span><?php echo $status['name']; ?></span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-md-2">
        <!-- <div id="<?php echo $status['id']; ?>" class="proposal_status">
            <span class="label-success p7 inline-block pull-right text-center"><?php echo strtoupper($proposal->status); ?></span>
        </div> -->
    </div>
</div>
<div class="clearfix"></div>