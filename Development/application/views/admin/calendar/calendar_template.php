<div class="modal fade _event" id="newEventModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo _l('create_new'); ?></h4>
      </div>
      <form action="" novalidate="1" id="calender_list_form" method="post" accept-charset="utf-8">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <span id="date-select"></span>
            </div>
            <div class="col-md-12">
              <div class="radio radio-primary radio-inline">
                <input type="radio" id="calender_list" name="calender_list" class="task" value="task">
                <label for="tasks">Tasks</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="radio radio-primary radio-inline">
                <input type="radio" id="calender_list" name="calender_list" class="meetings" value="meeting">
                <label for="meeting">Meeting</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="radio radio-primary radio-inline">
                <input type="radio" id="calender_list" name="calender_list" class="leads" value="lead">
                <label for="leads">Leads</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="radio radio-primary radio-inline">
                <input type="radio" id="calender_list" name="calender_list" class="invoices" value="invoice">
                <label for="invoices">Invoices</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="radio radio-primary radio-inline">
                <input type="radio" id="calender_list" name="calender_list" class="projects" value="project">
                <label for="projects">Projects</label>
              </div>
            </div>
            <input type="hidden" name="current_date" id="current_date">
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
  </div>
  <?php echo form_close(); ?>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
