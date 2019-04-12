<?php init_head(); ?>
<?php echo app_stylesheet('assets/css','tabs.css'); ?>
<div id="wrapper">
   <div class="content proposal-template">
      <div class="row">
        <div class="col-md-12">
          <div class="panel_s">
            <div class="panel-body">
              <div class="row">
                <h4 class="page-title"><?php echo $title; ?></h4>
                <?php if (isset($lid)) { ?>        
                  <ol class="breadcrumb pull-right">
                      <li class="breadcrumb-item"><a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo ($lname); ?></a>
                      </li>
                      <li class="breadcrumb-item active"><?php echo _l('proposal'); ?></li>
                  </ol>                                             
                <?php } ?>
              </div>
              <hr class="hr-panel-heading" />
              <div class="row leads-overview" style="display: block;">
                <div class="process-step" style="width: 14%">
                  <a href="#proposal-tab">
                    <h3 class="bold">1</h3>
                    <span style="color:#00bcd4"><?php echo _l('proposal'); ?></span>
                  </a>
                </div>
                <div class="process-step" style="width: 14%">
                  <a href="#payment-schedule-tab">
                    <h3 class="bold">2</h3>
                    <span><?php echo _l('payment_schedule'); ?></span>
                  </a>
                </div>
                <div class="process-step" style="width: 14%">
                  <a href="#agreement-tab">
                    <h3 class="bold">3</h3>
                    <span><?php echo _l('agreement'); ?></span>
                  </a>
                </div>
                <div class="process-step" style="width: 14%">
                  <a href="#signatures-tab">
                    <h3 class="bold">4</h3>
                    <span><?php echo _l('signatures'); ?></span>
                  </a>
                </div>
                <div class="process-step" style="width: 14%">
                  <a href="#payment-tab">
                    <h3 class="bold">5</h3>
                    <span><?php echo _l('payment'); ?></span>
                  </a>
                </div>
                <div class="process-step" style="width: 14%">
                  <a href="#receipt-tab">
                    <h3 class="bold">6</h3>
                    <span><?php echo _l('receipt'); ?></span>
                  </a>
                </div>
              </div>            
              <div class="row">
                <div class="tab-content">
                  <div class="tab-pane active" id="proposal-tab">
                    <?php echo form_open($this->uri->uri_string(),array('id'=>'proposal-form','class'=>'_transaction_form')); ?>
                      <?php $attrs = (isset($proposal) ? array() : array('autofocus'=>true)); ?>
                      <?php $value = (isset($proposal) ? $proposal->name : ''); ?>
                      <?php echo render_input('name','proposal_name',$value,'text',$attrs); ?>
                      <?php $proposal_date = isset($proposal->proposal_date) ? _dt($proposal->proposal_date, true) : ""; ?>
                      <?php echo render_date_input('proposal_date','proposal_date', $proposal_date,array('data-date-min-date'=>date('Y-m-d')) ); ?>
                      <?php $proposal_duedate = isset($proposal->proposal_duedate) ? _dt($proposal->proposal_duedate, true) : ""; ?>
                      <?php echo render_date_input('proposal_duedate','proposal_duedate', $proposal_duedate,array('data-date-min-date'=>date('Y-m-d')) ); ?>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="proposal_template" class="control-label"><?php echo _l('add_proposal_template'); ?></label>
                          <select name="proposal_templateid" id="proposal_templateid" class="selectpicker no-margin<?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%" id="item_group_select" data-none-selected-text="<?php echo _l('add_proposal_template'); ?>" data-live-search="true">
                            <option value=""></option>
                            <?php foreach($proposal_templates as $_items){  ?>
                              <option value="<?php echo $_items['templateid']; ?>"><?php echo $_items['name']; ?></option>
                            <?php } ?>                  
                          </select>
                        </div>
                      </div>
                      <div id="load-proposal-payment"></div>
                      <a class="btn btn-primary btnNext pull-right" href="javascript:loadTab('payment-schedule-tab');" id="btnNextPayment">Next</a>
                    <?php echo form_close(); ?>
                  </div>
                  <div class="tab-pane" id="payment-schedule-tab">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="payment_template" class="control-label"><?php echo _l('add_payment_template'); ?></label>
                        <select name="payment_templateid" id="payment_templateid" class="selectpicker no-margin<?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%" id="item_group_select" data-none-selected-text="<?php echo _l('add_payment_template'); ?>" data-live-search="true">
                          <option value=""></option>
                          <?php foreach($payment_templates as $_items){  ?>
                            <option value="<?php echo $_items['templateid']; ?>"><?php echo $_items['name']; ?></option>
                          <?php } ?>                  
                        </select>
                      </div>
                    </div>
                    <div id="load-payment-schedule"></div>
                    <a class="btn btn-primary btnPrevious pull-right" href="javascript:loadTab('proposal-tab');"id="btnPrevProposal">Previous</a>
                    <a class="btn btn-primary btnNexts" href="javascript:loadTab('agreement-tab');" id="btnNextAgreement">Next</a>
                  </div>
                  <div class="tab-pane" id="agreement-tab">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="agreement_template" class="control-label"><?php echo _l('add_agreement_template'); ?></label>
                        <select name="agreement_templateid" id="agreement_templateid" class="selectpicker no-margin<?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%" id="item_group_select" data-none-selected-text="<?php echo _l('add_agreement_template'); ?>" data-live-search="true">
                          <option value=""></option>
                          <?php foreach($agreement_templates as $_items){  ?>
                            <option value="<?php echo $_items['templateid']; ?>"><?php echo $_items['name']; ?></option>
                          <?php } ?>                  
                        </select>
                      </div>
                    </div>
                    <div id="load-agreement-template"></div>
                    <a class="btn btn-primary btnPrevious pull-right" href="javascript:loadTab('payment-schedule-tab');"id="btnPrevSchedule">Previous</a>
                    <a class="btn btn-primary btnNext" href="javascript:loadTab('signatures-tab');" id="btnNextSignature">Next</a>
                  </div>
                  <div class="tab-pane" id="signatures-tab">
                    <div class="row">
                      <div class="col-md-6">
                        <div id="clientfield-0">
                          <?php $client_signature = isset($proposal->client_signature) ? $proposal->client_signature : ""; ?>
                          <textarea id="client-signature-0" name="client_signature[0]" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="col-md-12 text-right" style="margin-top:10px">
                          <button id="add-client-more" name="add-client-more" class="btn btn-primary">Add More</button>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div id="stafffield-0">
                          <?php $staff_signature = isset($proposal->staff_signature) ? $proposal->staff_signature : ""; ?>
                          <textarea id="staff-signature-0" name="staff_signature[0]" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="col-md-12 text-right" style="margin-top:10px">
                          <button id="add-staff-more" name="add-staff-more" class="btn btn-primary">Add More</button>
                        </div>
                      </div>
                    </div>
                    <a class="btn btn-primary btnPrevious pull-right" href="javascript:loadTab('agreement-tab');" id="btnPrevSchedule">Previous</a>
                    <a class="btn btn-primary btnNext" href="javascript:loadTab('payment-tab');" id="btnNextPayment">Next</a>
                  </div>
                  <div class="tab-pane" id="payment-tab">
                    <a class="btn btn-primary btnPrevious pull-right" href="javascript:loadTab('signatures-tab');" id="btnPrevSignature">Previous</a>
                    <a class="btn btn-primary btnNext" href="javascript:loadTab('receipt-tab');" id="btnNextReceipt">Next</a>
                  </div>
                  <div class="tab-pane" id="receipt-tab">
                    <a class="btn btn-primary btnPrevious pull-right" href="javascript:loadTab('payment-tab');" id="btnPrevPayment">Previous</a>
                    <a class="btn btn-primary" id="btnSave">Save</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
  $(function() {
    /**
    * Added By : Vaidehi
    * Dt : 12/15/2017
    * load proposal template
    */
    $('#proposal_templateid').change(function(){
      var proposaltemplateid = $(this).val();

      $.ajax({
        method: 'post',
        async: false,
        url: '<?php echo admin_url(); ?>proposals/getproposalitems',
        data: 'proposaltemplateid='+proposaltemplateid,
        dataType: "html",
        success: function(data) {
          $("#load-proposal-payment").html(data);
        }
      })
    });

    /**
    * Added By : Vaidehi
    * Dt : 12/15/2017
    * load payment schedule template
    */
    $('#payment_templateid').change(function(){
      var paymenttemplateid = $(this).val();

      $.ajax({
        method: 'post',
        async: false,
        url: '<?php echo admin_url(); ?>proposals/getpaymentscheduleitems',
        data: 'paymenttemplateid='+paymenttemplateid,
        dataType: "html",
        success: function(data) {
          $("#load-payment-schedule").html(data);
        }
      })
    });

    /**
    * Added By : Vaidehi
    * Dt : 12/15/2017
    * load agreement template
    */
    $('#agreement_templateid').change(function(){
      var agreementtemplateid = $(this).val();

      $.ajax({
        method: 'post',
        async: false,
        url: '<?php echo admin_url(); ?>proposals/getagreementitems',
        data: 'agreementtemplateid='+agreementtemplateid,
        dataType: "html",
        success: function(data) {
          $("#load-agreement-template").html(data);
        }
      })
    });

    //add more staff signature
    var staff_my_fields = $("div[id^='stafffield-']");
    
    var highest = -Infinity;
    $.each(staff_my_fields, function(mindex, mvalue) {
      var fieldNum = mvalue.id.split("-");
      highest = Math.max(highest, parseFloat(fieldNum[1]));
    });

    var staff_next = highest;
    $("#add-staff-more").click(function(e){
      e.preventDefault();
      
      var addto = "#stafffield-" + staff_next;
      var addRemove = "#stafffield-" + (staff_next);
     
      staff_next = staff_next + 1;
      var newIn = "";
      newIn += ' <div id="stafffield-'+ staff_next +'" name="stafffield'+ staff_next +'"><textarea id="staff-signature-'+ staff_next +'" name="staff_signature['+ staff_next +']" class="form-control" rows="5"></textarea></div>';

      var newInput = $(newIn);
      
      var removeBtn = '<div class="col-md-12 text-right" style="margin-top:10px"><button id="remove' + (staff_next - 1) + '" class="btn btn-danger remove-me" >Remove</button></div>';
      var removeButton = $(removeBtn);
      $(addto).after(newInput);
      $(addRemove).after(removeButton);
      $("#stafffield-" + staff_next).attr('data-source',$(addto).attr('data-source'));
      $("#staff_count").val(staff_next);  

      $('.remove-me').click(function(e){
         e.preventDefault();
         var fieldNum = this.id.charAt(this.id.length-1);
         var fieldID = "#stafffield-" + fieldNum;
         $(this).remove();
         $(fieldID).remove();
      });
    });

    //add more client signature
    var client_my_fields = $("div[id^='clientfield-']");
    
    var client_highest = -Infinity;
    $.each(client_my_fields, function(mindex, mvalue) {
      var fieldNum = mvalue.id.split("-");
      client_highest = Math.max(client_highest, parseFloat(fieldNum[1]));
    });

    var client_next = client_highest;
    $("#add-client-more").click(function(e){
      e.preventDefault();
      
      var addto = "#clientfield-" + client_next;
      var addRemove = "#clientfield-" + (client_next);
     
      client_next = client_next + 1;
      var newIn = "";
      newIn += ' <div id="clientfield-'+ client_next +'" name="clientfield'+ client_next +'"><textarea id="client-signature-0" name="client_signature['+ client_next +']" class="form-control" rows="5"></textarea></div>';

      var newInput = $(newIn);
      
      var removeBtn = '<div class="col-md-12 text-right" style="margin-top:10px"><button id="remove' + (client_next - 1) + '" class="btn btn-danger remove-me" >Remove</button></div>';
      var removeButton = $(removeBtn);
      $(addto).after(newInput);
      $(addRemove).after(removeButton);
      $("#clientfield-" + client_next).attr('data-source',$(addto).attr('data-source'));
      $("#client_count").val(client_next);  

      $('.remove-me').click(function(e){
         e.preventDefault();
         var fieldNum = this.id.charAt(this.id.length-1);
         var fieldID = "#clientfield-" + fieldNum;
         $(this).remove();
         $(fieldID).remove();
      });
    });
  });

  //load tab
  function loadTab(selected) {
    $('.tab-pane').each(function(index) {
      if ($(this).attr("id") == selected) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }
</script>
</body>
</html>