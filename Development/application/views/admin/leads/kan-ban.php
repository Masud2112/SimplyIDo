<?php
$is_admin = is_admin();
$i = 0;
foreach ($statuses as $status) {
    $total_pages = ceil($this->leads_model->do_kanban_query($status['id'],$this->input->get('search'),1,array(),true)/get_option('leads_kanban_limit'));

    $settings = '';
    foreach(get_system_favourite_colors() as $color){
        $color_selected_class = 'cpicker-small';
        if($color == $status['color']){
            $color_selected_class = 'cpicker-big';
        }
        $settings .= "<div class='kanban-cpicker cpicker ".$color_selected_class."' data-color='".$color."' style='background:".$color.";border:1px solid ".$color."'></div>";
    }
    $leads = $this->leads_model->do_kanban_query($status['id'],$this->input->get('search'),1,array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort')));
    ?>
    <ul class="kan-ban-col" data-col-status-id="<?php echo $status['id']; ?>" data-total-pages="<?php echo $total_pages; ?>">
        <li class="kan-ban-col-wrapper">
            <div class="border-right panel_s">
                <?php
                $status_color = '';
                if(!empty($status["color"])){
                    $status_color = 'style="border-left:5px solid '.$status['color'].'"';
                }
                ?>
                <div class="panel-heading-bg primary-bg" <?php echo $status_color; ?> data-status-id="<?php echo $status['id']; ?>">
                    <div class="kan-ban-step-indicator-full"></div>
                    <!-- <i class="fa fa-reorder pointer"></i> -->
                    <div class="checkbox inline-block"><input class="chk_status" type="checkbox" value="<?php echo $status['id'] ?>"><label></label></div>
                    <span class="heading pointer" <?php if($is_admin){ ?> data-order="<?php echo $status['statusorder']; ?>" data-color="<?php echo $status['color']; ?>" data-name="<?php echo $status['name']; ?>" onclick="edit_status(this,<?php echo $status['id']; ?>); return false;" <?php } ?>><?php echo $status['name']; ?>
					</span>
                    <?php if(count($leads) > 3 ){ ?>
                    <span class="pull-right">
                        <a href="javascript:void(0)" class="kan-ban-exp-clps" data-pid="#status_<?php echo $status['id']; ?>">
                            <i class="fa fa-caret-down"></i>
                        </a>
                    </span>
                    <?php } ?>
                </div>
                <div class="kan-ban-content-wrapper" id="status_<?php echo $status['id']; ?>">
                    <div class="kan-ban-content">
                        <ul class="status row leads-status sortable" data-lead-status-id="<?php echo $status['id']; ?>">
                            <?php
                            $total_leads = count($leads);
                            $count=1;
                            foreach ($leads as $lead) {
                                $this->load->view('admin/leads/_kan_ban_card',array('lead'=>$lead,'status'=>$status,'count'=>$count));
                                $count++;
                            } ?>
                            <?php if($total_leads > 0 ){ ?>
                                <li class="text-center col-sm-4 not-sortable kanban-load-more" data-load-status="<?php echo $status['id']; ?>">
                                    <?php
                                    // <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1){echo ' disabled';} " data-page="1" onclick="kanban_load_more(<?php echo $status['id']; ,this,'leads/leads_kanban_load_more',290,360); return false;";>
                                    // <?php echo _l('load_more'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <li class="text-center not-sortable kanban-empty<?php if($total_leads > 0){echo ' hide';} ?>">
                                <h4 class="text-muted">
                                    <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                                    <?php echo _l('no_leads_found'); ?></h4>
                            </li>
                        </ul>
                    </div>
                </div>
        </li>
    </ul>
    <?php $i++; } ?>
