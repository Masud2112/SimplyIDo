<?php init_head(); ?>

<div id="wrapper">
    <div class="content dashboard-config">
        <div class="row">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-margin">Dashboard widget configuration</h4>
                    <div class="topButton">
                        <button class="btn btn-default" type="button"
                                onclick='location.href="<?php echo admin_url(); ?>"'><?php echo _l('Back'); ?></button>
                    </div>
                    <hr class="hr-panel-heading"/>
                    <?php
                    /*foreach ($widget_data as $data) {*/
                    if (!empty($widget_data) || $widget_data != ""){
                    $widget_data = (array)$widget_data;
                    $all_data = $widget_data['widget_type'];
                    $quick_link_all_data = $widget_data['quick_link_type'];
                    /*}*/
                    $rel_id = (isset($all_data) ? $all_data : "");
                    $exp_val = explode(',', $rel_id);

                    $link_data = (isset($quick_link_all_data) ? $quick_link_all_data : "");
                    $quick_link_val = explode(',', $link_data);

                    $all_data = json_decode($widget_data['order'], true);
                    ?>
                    <input type="hidden" name="tagid" value="<?php echo $this->session->userdata['staff_user_id']; ?>">
                    <div class="row sortable_config_item">
                        <?php foreach ($all_data as $json_order) { ?>
                            <div class="
               <?php if ($json_order['widget_name'] == 'getting_started') { ?>
               col-md-12 
               <?php } elseif ($json_order['widget_name'] == 'lead_pipeline') { ?>
                col-md-12 
               <?php } elseif ($json_order['widget_name'] == 'calendar') { ?>
               col-md-6
               <?php } elseif ($json_order['widget_name'] == 'pinned_item') { ?>
               col-md-6
               <?php } elseif ($json_order['widget_name'] == 'quick_link') { ?>
               col-md-6
               <?php } elseif ($json_order['widget_name'] == 'upcoming_project') { ?>
               col-md-6
               <?php } elseif ($json_order['widget_name'] == 'contacts') { ?>
               col-md-6
               <?php } elseif ($json_order['widget_name'] == 'messages') { ?>
               col-md-6
               <?php } elseif ($json_order['widget_name'] == 'task_list') { ?>
               col-md-6
               <?php } ?>
                panel_s option" data-class='option' data-id="<?php echo $json_order['widget_name'] ?>"
                                 data-order="<?php echo $json_order['order'] ?>">
                                <div class="panel-body config_block">
                                    <h5 class="pull-left"><?php echo _l($json_order['widget_name']); ?></h5>
                                    <?php if (in_array($json_order['widget_name'], $exp_val)) { ?>
                                        <a href="#" class="btn btn-danger pull-right update_setting"
                                           data-index="<?php echo $json_order['widget_name'] ?>">Hide</a>
                                    <?php } else { ?>
                                        <a href="#" class="btn btn-info pull-right update_setting"
                                           data-index="<?php echo $json_order['widget_name'] ?>">show</a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php }else{

                        echo "<div class='text-center'>No widget(s) found</div>";
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script type="text/javascript">
    $('.update_setting').click(function () {
        var current_val = $(this).attr("data-index");
        var curr_id = $('input[name="tagid"]').val();

        $.ajax({
            type: "POST",
            url: admin_url + "home/check_dashboard_setting_ajax",
            async: true,
            data: {tagid: curr_id, currentval: current_val}
        }).done(function () {
            location.reload();
        });
    });

    $(function () {
        $(".sortable_config_item").sortable({
            stop: function (event, ui) {
                var clas = ui.item.attr("data-class");

                order = 0;
                count = 0;
                var option = [];
                $("." + clas).each(function () {
                    var id = $(this).attr('data-id');
                    order = $(this).attr('data-order');
                    var option_val = {
                        'widget_name': id,
                        'order': count,
                    };

                    $(this).attr('data-order', count);
                    option.push(option_val);
                    count++;
                });
                option = JSON.stringify(option);

                var url = "<?php echo admin_url('home/ajax_widget_order_update'); ?>";

                $.ajax({
                    method: "POST",
                    url: url,
                    data: "options=" + option,
                }).done(function () {
                });
            }
        });
    });
</script>