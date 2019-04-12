<?php init_head(); ?>
<div id="wrapper">
    <div class="content calendar-page">
        <div class="row">
            <div class="col-md-12">


                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Calendar</span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-calendar"></i> Calendar</h1>
                <div class="clearfix"></div>
                <div class="panel_s">
                    <div class="panel-body" style="overflow-x: auto;">
                        <div class="dt-loader hide"></div>
                        <?php $this->load->view('admin/calendar/calendar_filters'); ?>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('admin/calendar/calendar_template'); ?>
<script>
</script>
<?php init_tail(); ?>
<script>
    $(function(){
        $('.fc-calendarFilter-button').html('<i class="fa fa-filter"></i>');
        $('#calender_list_form').submit(function(e) {
            var calender_list_type = $('input[name=calender_list]:checked').val();
            var curr_date = $('#current_date').val();

            if(calender_list_type == 'task'){
                window.location = admin_url+"tasks/task?due_dt="+curr_date+"&pg=calendar";
                return false;
            } else if(calender_list_type == 'meeting'){
                window.location = admin_url+"meetings/meeting?from_dt="+curr_date+"&pg=calendar";
                return false;
            } else if(calender_list_type == 'lead'){
                window.location = admin_url+"leads/lead?start_dt="+curr_date+"&pg=calendar";
                return false;
            } else if(calender_list_type == 'invoice'){
                window.location = admin_url+"invoices/invoice?date="+curr_date+"&pg=calendar";
                return false;
            } else{
                window.location = admin_url+"projects/project?start_dt="+curr_date+"&pg=calendar";
                return false;
            }
        });

        $("#calender_list_form").trigger( "reset" );
    });
</script>
</body>
</html>