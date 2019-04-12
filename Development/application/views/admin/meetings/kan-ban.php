<?php
$is_admin = is_admin();
//$i = 0;
/*foreach ($statuses as $status) {*/
$search = isset($search)?$search:"";
    $meetings = $this->meetings_model->do_meeting_kanban_query("", array('sort_by' => $this->input->get('sort_by'), 'sort' => $this->input->get('sort')),"",$limit,$page,$kanban,$search);
$allmeetings = count($this->meetings_model->do_meeting_kanban_query("", array('sort_by' => $this->input->get('sort_by'), 'sort' => $this->input->get('sort')),"","","","",$search));
    ?>
    <!--<ul class="kan-ban-col" data-col-status-id="<?php /*echo $status['statusid']; */?>"
        data-total-pages="<?php /* echo $total_pages; */?>">-->
        <!--<li class="kan-ban-col-wrapper">
            <div class="border-right panel_s">-->
                <!--<div class="panel-heading-bg primary-bg" data-status-id="<?php /*echo $status['statusid']; */?>">
                    <div class="kan-ban-step-indicator-full"></div>
                    <span class="heading pointer"><?php /*echo $status['name']; */?></span>
                    <?php /*if (count($meetings) > 3) { */?>
                        <span class="pull-right">
                        <a href="javascript:void(0)" class="kan-ban-exp-clps"
                           data-pid="#status_<?php /*echo $status['statusid']; */?>">
                            <i class="fa fa-caret-down"></i>
                        </a>
                    </span>
                    <?php /*} */?>
                </div>-->
                <div class="kan-ban-content-wrapper-meetings" id="status_<?php //echo $status['statusid']; ?>">
                    <div class="kan-ban-content">
                        <ul class="meetings-status"
                            data-meeting-status-id="<?php //echo $status['statusid']; ?>">
                            <?php
                            $total_meetings = count($meetings);
                            $count = 1;
                            foreach ($meetings as $meeting) {
                                $this->load->view('admin/meetings/_kan_ban_card', array('meeting' => $meeting, 'count' => $count));
                                $count++;
                            }
                            ?>
                            <?php if ($total_meetings > 0) { ?>
                                <li class="text-center not-sortable kanban-load-more"
                                    data-load-status="<?php //echo $status['statusid']; ?>">
                                    </a>
                                </li>
                            <?php } ?>
                            <li class="text-center not-sortable mtop30 kanban-empty<?php if ($total_meetings > 0) {
                                echo ' hide';
                            } ?>">
                                <h4 class="text-muted">
                                    <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br/><br/>
                                    <?php echo _l('no_meetings_found'); ?>
                                </h4>
                            </li>
                        </ul>
                    </div>
                </div>
        <!--</li>-->
    <!--</ul>-->
    <?php
    /*$i++;
}*/
?>
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
    if($limit > count($meetings)){
        $end = $allmeetings;
    }
    ?>
    <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing <?php echo $start= ($page-1)*$limit+1; ?> to <?php echo $end ?>
        of <?php echo $allmeetings ?> entries
    </div>
</div>
<?php
$total_pages = $allmeetings/$limit;
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
    jQuery('#kan-ban .page_itmes_no').change(function () {
        meetings_kanban('limitchanged');
    });

    jQuery('#kan-ban .paginate_button').on('click', function (e) {
        e.preventDefault();
        if(!$(this).hasClass('disabled')){
            jQuery('.paginate_button.active').addClass('lastactive');
            jQuery('.paginate_button').removeClass('active');
            jQuery(this).addClass('active');
            meetings_kanban();
        }

    });
</script>

