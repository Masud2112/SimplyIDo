<!--<div class="col-md-12">
    <h3><?php /*echo _l('leads_summary'); */?></h3>
</div>-->
<?php
$where_not_admin = '(addedfrom = ' . get_staff_user_id() . ' OR assigned=' . get_staff_user_id() . ' OR is_public = 1)';
$numStatuses = count($statuses);
$is_admin = is_admin();
$processwidth = 100 / $numStatuses;
foreach ($statuses as $status) { ?>
    <div class="process-step" style="width:<?php echo $processwidth . '%'; ?>">
        <?php
        $this->db->where('status', $status['id']);
        $this->db->where('deleted = ', 0);
        $this->db->where('converted = ', 0);
        $this->db->where('brandid = ', get_user_session());
        if (!$is_admin) {
            $this->db->where($where_not_admin);
        }
        $total = $this->db->count_all_results('tblleads');
        ?>
        <h3 class="bold" style="background-color:<?php echo $status['color']; ?>"><?php echo $total; ?></h3>
        <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span>
    </div>
<?php } ?>
<?php
if (!$is_admin) {
    $this->db->where($where_not_admin);
}
$total_leads = $this->db->count_all_results('tblleads');
?>
<?php if ($is_admin) { ?>
    <div class="col-md-2 col-xs-6">
        <?php
        $this->db->where('lost', 1);
        if (!$is_admin) {
            $this->db->where($where_not_admin);
        }
        $total_lost = $this->db->count_all_results('tblleads');
        $percent_lost = ($total_leads > 0 ? number_format(($total_lost * 100) / $total_leads, 2) : 0);
        ?>
        <h3 class="bold"><?php echo $percent_lost; ?>%</h3>
        <span class="text-danger"><?php echo _l('lost_leads'); ?></span>
    </div>
    <div class="col-md-2 col-xs-6">
        <?php
        $this->db->where('junk', 1);
        if (!$is_admin) {
            $this->db->where($where_not_admin);
        }
        $total_junk = $this->db->count_all_results('tblleads');
        $percent_junk = ($total_leads > 0 ? number_format(($total_junk * 100) / $total_leads, 2) : 0);
        ?>
        <h3 class="bold"><?php echo $percent_junk; ?>%</h3>
        <span class="text-danger"><?php echo _l('junk_leads'); ?></span>
    </div>
<?php } ?>