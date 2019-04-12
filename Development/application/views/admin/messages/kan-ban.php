<?php
/*$is_admin = is_admin();
$i = 0;
foreach ($statuses as $status) {*/
    /*$messages = $this->messages_model->do_message_kanban_query($status['statusid'], $this->input->get('search'), 1, array('sort_by' => $this->input->get('sort_by'), 'sort' => $this->input->get('sort')));*/
    ?>
    <ul class="kan-ban-col" data-col-status-id="<?php //echo $status['statusid']; ?>"
        data-total-pages="<?php // echo $total_pages; ?>">
        <li class="kan-ban-col-wrapper-messages">
            <div class="panel_s">
                <!--<div class="panel-heading-bg primary-bg" data-status-id="<?php /*//echo $status['statusid']; */?>">
                    <div class="kan-ban-step-indicator-full"></div>
                    <span class="heading pointer"><?php /*echo $status['name']; */?></span>
                    <?php /*if (count($messages) > 3) { */?>
                        <span class="pull-right">
                        <a href="javascript:void(0)" class="kan-ban-exp-clps"
                           data-pid="#status_<?php /*//echo $status['statusid']; */?>">
                            <i class="fa fa-caret-down"></i>
                        </a>
                    </span>
                    <?php /*} */?>
                </div>-->
                <div class="kan-ban-content-wrapper" id="status_<?php //echo $status['statusid']; ?>">
                    <div class="kan-ban-content">
                        <ul class="messages-status"
                            data-message-status-id="<?php //echo $status['statusid']; ?>">
                            <?php
                             $total_messages = count($totalmessages);
                             /*if(isset($search) && !empty($search)){
                                 $total_messages = count($messages);
                             }*/
                            $count = 1;
                            foreach ($messages as $message) {
                                $this->load->view('admin/messages/_kan_ban_card', array('message' => $message,'count' => $count));
                                $count++;
                            }
                            ?>
                            <?php if ($total_messages > 0) { ?>
                                <li class="text-center not-sortable kanban-load-more"
                                    data-load-status="<?php //echo $status['statusid']; ?>">
                                    </a>
                                </li>
                            <?php } ?>
                            <li class="text-center not-sortable mtop30 kanban-empty<?php if ($total_messages > 0) {
                                echo ' hide';
                            } ?>">
                                <h4 class="text-muted">
                                    <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br/><br/>
                                    <?php echo _l('no_messages_found'); ?>
                                </h4>
                            </li>
                        </ul>
                    </div>
                </div>
        </li>
    </ul>
    <?php
/*    $i++;
}
*/?>
<div class="col-sm-6 data-footer">
    <div class="dataTables_length" id="DataTables_Table_0_length">
            <select name="page_itmes_no" class="form-control input-sm page_itmes_no">
                <option <?php echo $limit==9?"selected":""?> value="9">9</option>
                <option <?php echo $limit==27?"selected":""?> value="27">27</option>
                <option <?php echo $limit==45?"selected":""?> value="45">45</option>
                <option <?php echo $limit==90?"selected":""?> value="90">90</option>
                <option <?php echo $limit==-1?"selected":""?> value="-1">All</option>
            </select>
    </div>
    <?php
        $end=$page*$limit;
        if($limit > count($messages)){
            $end = $total_messages;
        }
        if($limit==-1){
            $end = $total_messages;
        }
    ?>
    <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing <?php echo $start= ($page-1)*$limit+1; ?> to <?php echo $end ?>
        of <?php echo $total_messages ?> entries
    </div>
</div>
<?php
$total_pages = $total_messages/$limit;
$total_pages = ceil($total_pages);
$stages = 2;	
if($total_pages > 1){
?>
<div class="col-sm-6">
    <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
        <ul class="pagination pull-right m0">
            <li class="paginate_button previous <?php echo $page==1?"disabled":"" ?>" id="DataTables_Table_0_previous">
                <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="previous" tabindex="0">Previous</a>
            </li>
            <?php for($i=1;$i<=$total_pages;$i++){ ?>
                
				<?php if(is_mobile()) {
					if(($page-$stages) < $i && ($page+$stages) > $i){ ?>
                    <li class="paginate_button <?php echo $page==$i?"active":"" ?>">
                        <a href="#" aria-controls="DataTables_Table_<?php echo $i ?>" data-dt-idx="<?php echo $i ?>" tabindex="0"><?php echo $i ?></a>
                    </li>
                <?php } ?>	
					<?php }else{ ?>
                    <li class="paginate_button <?php echo $page==$i?"active":"" ?>">
                        <a href="#" aria-controls="DataTables_Table_<?php echo $i ?>" data-dt-idx="<?php echo $i ?>" tabindex="0"><?php echo $i ?></a>
                    </li>
                <?php }  ?>
				
			
			
            <?php } ?>
            <li class="paginate_button next <?php echo $page==$total_pages?"disabled":"" ?>" id="DataTables_Table_0_next">
                <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="next" tabindex="0">Next</a>
            </li>
        </ul>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
    jQuery('.page_itmes_no').change(function () {
        messages_kanban('limitchanged');
    });

    jQuery('.paginate_button').on('click', function (e) {
        e.preventDefault();
        if(!$(this).hasClass('disabled')){
            jQuery('.paginate_button.active').addClass('lastactive');
            jQuery('.paginate_button').removeClass('active');
            jQuery(this).addClass('active');
            messages_kanban();
        }

    });
</script>
