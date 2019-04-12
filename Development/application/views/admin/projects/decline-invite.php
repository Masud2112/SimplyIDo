<div class="modal fade" id="decline" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <input type="hidden" name="projectid" id="projectid">
    <input type="hidden" name="inviteid" id="inviteid">
    <input type="hidden" name="contacttype" id="contacttype">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="add-title">Enter Comments</span>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div id="additional"></div>
            <?php echo render_textarea('comments',_l('comments')); ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
        <button type="button" id="btnsubmit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->