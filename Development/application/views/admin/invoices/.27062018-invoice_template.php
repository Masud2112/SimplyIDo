<h1 class="pageTitleH1">
    <?php if(isset($invoice)){ echo _l('edit_invoice_tooltip'); } else { echo _l('create_new_invoice');  }?>
</h1>
<div class="pull-right">
    <div class="breadcrumb">
        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
        <i class="fa fa-angle-right breadcrumb-arrow"></i>
        <?php if (isset($lid)) { ?>
            <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo ($lname); ?></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('invoices?lid=' . $lid); ?>">Invoices</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
        <?php } elseif (isset($pid)) { ?>
            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo ($lname); ?></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('invoices?pid=' . $pid); ?>">Invoices</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
        <?php }else{ ?>
            <a href="<?php echo admin_url('invoices'); ?>">Invoices</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
        <?php } ?>
        <span><?php echo isset($invoice)?$invoice->number:"New Invoice"?></span>
    </div>
</div>
<div class="clearfix"></div>
<div class="btmbrd panel_s<?php if(!isset($invoice) || (isset($invoice) && count($invoices_to_merge) == 0 && (isset($invoice) && !isset($invoice_from_project) && count($expenses_to_bill) == 0))){echo ' hide';} ?> hidden" id="invoice_top_info">
   <div class="panel-body">
      <div class="row">
         <div id="merge" class="col-md-6">
            <?php if(isset($invoice)){
               $this->load->view('admin/invoices/merge_invoice',array('invoices_to_merge'=>$invoices_to_merge));
               } ?>
         </div>
         <?php
            // When invoicing from project area the expenses are not visible here because you can select to bill expenses while trying to invoice project
            if(!isset($invoice_from_project)){ ?>
         <div id="expenses_to_bill" class="col-md-6">
            <?php if(isset($invoice)){
               $this->load->view('admin/invoices/bill_expenses',array('expenses_to_bill'=>$expenses_to_bill));
               } ?>
         </div>
         <?php } ?>
      </div>
   </div>
</div>
<div class="panel_s btmbrd invoice accounting-template">
   <div class="additional"></div>
   <div class="panel-body">
      <?php if(isset($invoice)){ ?>
      <?php  echo format_invoice_status($invoice->status); ?>
      <hr class="hr-panel-heading" />
      <?php } ?>
      <?php do_action('before_render_invoice_template'); ?>
      <input type="hidden" name="pg" value="<?php echo isset($pg) ? $pg : '';?>">
      <?php if(isset($invoice)){
         echo form_hidden('merge_current_invoice',$invoice->id);
         }
         ?>
      <div class="row">
         <div class="col-md-6">                    
            <div class="f_client_id">
              <div class="form-group">
                <!-- <label for="clientid"><?php //echo _l('invoice_select_customer'); ?></label> -->
               <!--  <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search hidden" data-none-selected-text="<?php //echo _l('dropdown_non_selected_tex'); ?>">
               <?php /*$selected = (isset($invoice) ? $invoice->clientid : '');
                 if($selected == ''){
                   $selected = (isset($customer_id) ? $customer_id: '');
                 }
                 if($selected != ''){
                    $rel_data = get_relation_data('customer',$selected);
                    $rel_val = get_relation_values($rel_data,'customer');
                    echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                 }*/ ?>
                </select> -->
                <?php echo render_select('clientid', $contacts, array('addressbookid',array('firstname','lastname')), 'Contacts', (isset($invoice) ? $invoice->clientid : '')); ?>
                 
              </div>
            </div>
            <?php /*
            if(!isset($invoice_from_project)){ ?>
            <div class="form-group projects-wrapper<?php if((!isset($invoice)) || (isset($invoice) && !customer_has_projects($invoice->clientid))){ echo ' hide';} ?>">
               <label for="project_id"><?php echo _l('project'); ?></label>
              <div id="project_ajax_search_wrapper">
                   <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                   <?php
                     if(isset($invoice) && $invoice->project_id != 0){
                        echo '<option value="'.$invoice->project_id.'" selected>'.get_project_name_by_id($invoice->project_id).'</option>';
                     }
                   ?>
               </select>
               </div>
            </div>
            <?php } */ ?>            
            <?php
               $next_invoice_number = get_brand_option('next_invoice_number');
               $format = get_brand_option('invoice_number_format');
               if(isset($invoice)){$format = $invoice->number_format;}
               $prefix = get_brand_option('invoice_prefix');
               if ($format == 1) {
               // Number based
                 $__number = $next_invoice_number;
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = '<span id="prefix">' . $invoice->prefix . '</span>';
                 }
               } else if ($format == 2){
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = $invoice->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_year">' .date('Y',strtotime($invoice->date)).'</span>/';
                 } else {
                  $__number = $next_invoice_number;
                  $prefix = $prefix.'<span id="prefix_year">'.date('Y').'</span>-';
                }
               } else if ($format == 3) {
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = $invoice->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_month">' .date('Ymd',strtotime($invoice->date)).'</span>/';
                 } else {
                  $__number = $next_invoice_number;
                  $prefix = $prefix.'<span id="prefix_month">'.date('Ymd').'</span>-';
                }
               }else if ($format == 4) {
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = $invoice->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_month">' .date('Ymd',strtotime($invoice->leaddate)).'</span>/';
                 } else {
                  if(isset($lid)){
                    $__number = $next_invoice_number;
                    $lead_event_data = $this->leads_model->get($lid);
                    $lead_event_date = date('Ymd',strtotime($lead_event_data->eventstartdatetime));
                    //echo "<pre>";print_r($lead_event_data);exit;
                    $prefix = $prefix.'<span id="prefix_month">'.$lead_event_date.'</span>-';
                  }elseif(isset($pid)){
                    $__number = $next_invoice_number;
                    $project_event_data = $this->projects_model->get($pid);
                    $project_event_date = date('Ymd',strtotime($project_event_data->eventstartdatetime));
                    //echo "<pre>";print_r($lead_event_data);exit;
                    $prefix = $prefix.'<span id="prefix_month">'.$project_event_date.'</span>-';
                  }elseif(isset($eid)){
                    $__number = $next_invoice_number;
                    $project_event_data = $this->projects_model->get($eid);
                    $project_event_date = date('Ymd',strtotime($project_event_data->eventstartdatetime));
                    //echo "<pre>";print_r($lead_event_data);exit;
                    $prefix = $prefix.'<span id="prefix_month">'.$project_event_date.'</span>-';
                  }
                  
                }
               }else{
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = $invoice->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_month">' .date('Ymd',strtotime($invoice->date)).'</span>/';
                 } else {
                  $__number = $next_invoice_number;
                  $prefix = $prefix.'<span id="prefix_month">'.date('Ymd').'</span>-';
                }
               }
               if ($format == 4) {
                 if(isset($lid) && $lid != "" && !isset($invoice)){
                    $get_next_invoice = $this->invoices_model->get_next_invoice($lid,date('Y-m-d',strtotime($lead_event_data->eventstartdatetime)));
                    $_invoice_number = $get_next_invoice['event_no'].$get_next_invoice['event_invoice_no'];
                 }else if(isset($pid) && $pid != "" && !isset($invoice)){
                    $get_next_invoice = $this->invoices_model->get_next_invoice_project($pid,date('Y-m-d',strtotime($project_event_data->eventstartdatetime)));
                    $_invoice_number = $get_next_invoice['event_no'].$get_next_invoice['event_invoice_no'];
                 }else if(isset($eid) && $eid != "" && !isset($invoice)){
                    $get_next_invoice = $this->invoices_model->get_next_invoice_event($eid,date('Y-m-d',strtotime($project_event_data->eventstartdatetime)));
                    $_invoice_number = $get_next_invoice['event_no'].$get_next_invoice['event_invoice_no'];
                 }else{
                     if(isset($invoice)){
                         $_invoice_number = str_pad($invoice->eventno.$invoice->eventinvoiceno, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                     }else{
                         $_invoice_number = str_pad($next_invoice_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                     }
                 }
               }else{
                  $_invoice_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
               }

               if(isset($invoice)){
               $isedit = 'true';
               $data_original_number = $invoice->number;
               } else {
               $isedit = 'false';
               $data_original_number = 'false';
               }
               ?>
            <div class="form-group">
               <label for="number" class="control-label"><?php echo _l('invoice_add_edit_number'); ?></label>
               <div class="input-group">
                  <span class="input-group-addon">
                  <?php if(isset($invoice)){ ?>
                  <a href="#" onclick="return false;" data-toggle="popover" data-container='._transaction_form' data-html="true" data-content="<label class='control-label'><?php echo _l('settings_sales_invoice_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo $invoice->prefix; ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('invoices/update_number_settings/'.$invoice->id); ?>' class='btn btn-info btn-block mtop15'><?php echo _l('submit'); ?></button>"><!-- <i class="fa fa-cog"></i> --></a>
                  <?php } ?>
                  <?php echo $prefix; ?></span>
                  <input type="text" name="number" class="form-control" value="<?php echo $_invoice_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" data-number-format="<?php echo $format; ?>">
               </div>
            </div>
            
            <?php $rel_id = (isset($invoice) ? $invoice->id : false); ?>
            <?php echo render_custom_fields('invoice',$rel_id); ?>

            <?php
            if(isset($_GET['date'])){
                $from_dt=date_create($_GET['date']);
                $value=date_format($from_dt,'m/d/Y');
            }else{
                $value = (isset($invoice) ? _dt($invoice->date, false) : _dt(date('Y-m-d'),false));
            }
            ?>
            <?php echo render_date_input('date','invoice_add_edit_date',$value); ?>              
         </div>
         <div class="col-md-6">
            <div class="panel_s">
               
               <!-- <h4 class="no-margin"><?php //echo _l('invoice_add_edit_advanced_options'); ?></h4 >-->
              <!--  <hr class="hr-panel-heading" /> -->
               <!-- <?php //if(get_option('cron_send_invoice_overdue_reminder') == 1){ ?>
                <div class="form-group">
                  <div class="checkbox checkbox-danger">
                     <input type="checkbox" <?php //if(isset($invoice) && $invoice->cancel_overdue_reminders == 1){echo 'checked';} ?> id="cancel_overdue_reminders" name="cancel_overdue_reminders">
                     <label for="cancel_overdue_reminders"><?php //echo _l('cancel_overdue_reminders_invoice') ?></label>
                  </div>
               </div>
               <?php //} ?>-->
                <div class="form-group mbot15">
                  <label for="allowed_payment_modes" class="control-label"><?php echo _l('invoice_add_edit_allowed_payment_modes'); ?></label>
                  <br />
                  <?php if(count($payment_modes) > 0){ ?>
                  <select class="selectpicker" name="allowed_payment_modes[]" multiple="true" data-width="100%" data-title="<?php echo _l('dropdown_non_selected_tex'); ?>">
                  <?php foreach($payment_modes as $mode){
                     $selected = '';
                     if(isset($invoice)){
                       if($invoice->allowed_payment_modes){
                        $inv_modes = unserialize($invoice->allowed_payment_modes);
                        if(is_array($inv_modes)) {
                         foreach($inv_modes as $_allowed_payment_mode){
                           if($_allowed_payment_mode == $mode['id']){
                             $selected = ' selected';
                           }
                         }
                       }
                     }
                     } else {
                     if($mode['selected_by_default'] == 1){
                        $selected = ' selected';
                     }
                     }
                     ?>
                     <option value="<?php echo $mode['id']; ?>"<?php echo $selected; ?>><?php echo $mode['name']; ?></option>
                  <?php } ?>
                  </select>
                  <?php } else { ?>
                  <p><?php echo _l('invoice_add_edit_no_payment_modes_found'); ?></p>
                  <a class="btn btn-info" href="<?php echo admin_url('paymentmodes'); ?>">
                  <?php echo _l('new_payment_mode'); ?>
                  </a>
                  <?php } ?>
               </div>

               <div class="form-group">                  
                <!--   <div class="col-md-6"> -->
                     <?php
                        $i = 0;
                        $selected = '';
                        foreach($staff as $member){
                         if(isset($invoice)){
                           if($invoice->sale_agent == $member['staffid']) {
                             $selected = $member['staffid'];
                           }
                         }
                         $i++;
                        }
                        echo render_select('sale_agent',$staff,array('staffid',array('firstname','lastname')),'sale_agent_string',$selected);
                        ?>
                  <!-- </div> -->                 
               </div>               
                    <?php
                    $value = '';
                    if(isset($invoice)){
                      $value = _dt($invoice->duedate,false);
                    } else {
                      if(get_option('invoice_due_after') != 0){
                          if (isset($_GET['date'])) {
                              $from_dt = date_create($_GET['date']);
                              $value = date_format($from_dt, 'm/d/Y');
                              $value =date('m/d/Y',strtotime('+'.get_option('invoice_due_after').' DAY',strtotime($value)));
                          }else{
                              $value = _dt(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))),false);
                          }
                      }
                    }
                     ?>
                    <?php echo render_date_input('duedate','invoice_add_edit_duedate',$value); ?>
                 <?php //$value = (isset($invoice) ? $invoice->adminnote : ''); ?>
               <?php //echo render_textarea('adminnote','invoice_add_edit_admin_note',$value); ?>
            </div>
         </div>
      </div>
   <!-- </div>
   <div class="panel-body mtop10"> -->
    <h4>Payments</h4>
    <hr class="hr-panel-heading">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group mbot25 items-wrapper">
          <select name="item_group_select" class="selectpicker no-margin<?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%" id="item_group_select" data-none-selected-text="<?php echo _l('add_item_group'); ?>" data-live-search="true">
            <option value=""></option>
            <?php foreach($groups as $group){ ?>
              <option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
            <?php } ?>                  
          </select>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group mbot25 items-wrapper">
          <select name="item_select" class="selectpicker no-margin<?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%" id="item_select" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
            <option value=""></option>
            <?php foreach($items as $group_id=>$_items){  ?>
              <optgroup data-group-id="<?php echo $group_id; ?>" label="<?php echo $_items[0]['name']; ?>">
                <?php foreach($_items as $item){ ?>
                  <option value="<?php echo $item['id']; ?>">($<?php echo _format_number($item['rate']); ; ?>) <?php echo $item['description']; ?></option>
                <?php } ?>
              </optgroup>
            <?php } ?>                  
          </select>
        </div>
      </div>         
      <?php if(isset($invoice_from_project)){ echo '<hr class="no-mtop" />'; } ?>
      <div class="table-responsive s_table">
         <table class="table invoice-items-table items table-main-invoice-edit no-mtop">
            <thead>
               <tr>
                  <th width="8%"></th>
                  <th width="20%" class="text-left"><!-- <i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php //echo _l('item_description_new_lines_notice'); ?>"></i> --> <?php echo _l('invoice_table_item_heading'); ?></th>
                  <!-- <th width="25%" class="text-left"><?php //echo _l('invoice_table_item_description'); ?></th> -->
                  <?php
                     $qty_heading = _l('invoice_table_quantity_heading');
                     if(isset($invoice) && $invoice->show_quantity_as == 2 || isset($hours_quantity)){
                      $qty_heading = _l('invoice_table_hours_heading');
                     } else if(isset($invoice) && $invoice->show_quantity_as == 3){
                      $qty_heading = _l('invoice_table_quantity_heading') .'/'._l('invoice_table_hours_heading');
                     }
                     ?>
                  <th width="10%" class="text-left qty"><?php echo $qty_heading; ?></th>
                  <th width="15%" class="text-left" style="text-align: left"><?php echo _l('invoice_table_rate_heading'); ?></th>
                  <th width="20%" class="text-left" style="text-align: left"><?php echo _l('invoice_table_tax_heading'); ?></th>
                  <th width="10%" class="text-left"><?php echo _l('invoice_table_amount_heading'); ?></th>
                  <th></th>
               </tr>
            </thead>
            <tbody>
               <tr class="main hide">
                  <td class="item-img" style="padding: 10px 0"><img class="item-profile-image-small" name="profile-image"></td>
                  <td>
                    <input type="text" name="description" class="form-control" placeholder="<?php echo _l('item_description_placeholder'); ?>">
                     <!-- <textarea name="description" class="form-control" rows="4" placeholder="<?php //echo _l('item_description_placeholder'); ?>"></textarea> -->
                  </td>
                 <!--  <td>
                     <textarea name="long_description" rows="4" class="form-control" placeholder="<?php //echo _l('item_long_description_placeholder'); ?>"></textarea>
                  </td> -->
                  <td>
                      <input type="hidden" name="item_id" value="">
                     <input type="number" name="quantity" min="0" value="1" class="form-control" placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
                     <!-- <input type="text" placeholder="<?php //echo _l('unit'); ?>" name="unit" class="form-control input-transparent text-right"> -->
                  </td>
                  <td>
                     <input type="number" name="rate" class="form-control" placeholder="<?php echo _l('item_rate_placeholder'); ?>">
                  </td>
                  <td>
                     <?php
                        $default_tax = unserialize(get_option('default_tax'));
                        $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="'._l('no_tax').'">';
                      //  $select .= '<option value=""'.(count($default_tax) == 0 ? ' selected' : '').'>'._l('no_tax').'</option>';
                        foreach($taxes as $tax){
                        $selected = '';
                         if(is_array($default_tax)){
                             if(in_array($tax['name'] . '|' . $tax['taxrate'],$default_tax)){
                                  $selected = ' selected ';
                             }
                        }                        
                        $select .= '<option value="'.$tax['name'].'|'.$tax['taxrate'].'"'.$selected.'data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';
                        }
                        $select .= '</select>';
                        echo $select;
                        ?>
                  </td>
                  <td></td>
                  <td>
                     <?php
                        $new_item = 'undefined';
                        if(isset($invoice)){
                         $new_item = true;
                        }
                        ?>
                     <button id="btnAddItem" type="button" onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;" class="btn pull-right btn-info"><i class="fa fa-plus"></i></button>
                  </td>
               </tr>
               <?php if (isset($invoice) || isset($add_items)) {
                  $i               = 1;
                  $items_indicator = 'newitems';
                  if (isset($invoice)) {
                    $add_items       = $invoice->items;
                    $items_indicator = 'items';
                  }
                  foreach ($add_items as $item) {
                    $manual    = false;
                    $table_row = '<tr class="sortable item">';
                    $table_row .= '<td>';
                    if (!is_numeric($item['qty'])) {
                      $item['qty'] = 1;
                    }
                    $invoice_item_taxes = get_invoice_item_taxes($item['id']);
                    // passed like string
                    if ($item['id'] == 0) {
                        $invoice_item_taxes = $item['taxname'];
                        $manual             = true;
                    }
                    $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
                    $amount = $item['rate'] * $item['qty'];
                    $amount = _format_number($amount);
                    // order input
                    $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
                    $table_row .= '</td>';
                    $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea></td>';
                    // $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';
                    $table_row .= '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
                    // $unit_placeholder = '';
                    // // if(!$item['unit']){
                    // //   $unit_placeholder = _l('unit');
                    // //   $item['unit'] = '';
                    // // }
                    // $table_row .= '<input type="text" placeholder="'.$unit_placeholder.'" name="'.$items_indicator.'['.$i.'][unit]" class="form-control input-transparent text-right" value="'.$item['unit'].'">';
                    $table_row .= '</td>';
                    $table_row .= '<td class="rate"><input type="number" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
                    $table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item['id'], true, $manual) . '</td>';
                    $table_row .= '<td class="amount">' . $amount . '</td>';
                    $table_row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                    if (isset($item['task_id'])) {
                      if (!is_array($item['task_id'])) {
                        $table_row .= form_hidden('billed_tasks['.$i.'][]', $item['task_id']);
                      } else {
                        foreach ($item['task_id'] as $task_id) {
                          $table_row .= form_hidden('billed_tasks['.$i.'][]', $task_id);
                        }
                      }
                    } else if (isset($item['expense_id'])) {
                      $table_row .= form_hidden('billed_expenses['.$i.'][]', $item['expense_id']);
                    }
                    $table_row .= '</tr>';
                    echo $table_row;
                    $i++;
                  }
                  }
                  ?>
            </tbody>
         </table>
      </div>
      <div class="col-md-8 col-md-offset-4">
        <table class="table text-right">
          <tbody>
            <tr id="subtotal">
              <td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span></td>
              <td class="subtotal"></td>
            </tr>
            <!-- Added By: Vaidehi
              -- Dt: 03/29/2018
              -- for transaction charge
            -->
            <!-- <tr id="transaction">
              <td><span class="bold"><?php //echo _l('transaction_charge'); ?> :</span></td>
              <td class="transaction"></td>
            </tr> -->
            <tr id="discount_percent" class="hidden">
              <td>
                <div class="row">
                  <div class="col-md-7">
                    <span class="bold"><?php echo _l('invoice_discount'); ?> (%)</span>
                  </div>
                  <div class="col-md-5">
                    <?php
                      $discount_percent = 0;
                      if(isset($invoice)){
                        if($invoice->discount_percent != 0){
                          $discount_percent =  $invoice->discount_percent;
                        }
                      }
                    ?>
                    <input type="number" value="<?php echo $discount_percent; ?>" class="form-control pull-left" min="0" max="100" name="discount_percent">
                  </div>
                </div>
              </td>
              <td class="discount_percent"></td>
            </tr>
            <tr class="hidden">
              <td>
                <div class="row">
                  <div class="col-md-7">
                    <span class="bold"><?php echo _l('invoice_adjustment'); ?></span>
                  </div>
                  <div class="col-md-5">
                    <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if(isset($invoice)){echo $invoice->adjustment; } else { echo 0; } ?>" class="form-control pull-left" name="adjustment">
                  </div>
                </div>
              </td>
              <td class="adjustment"></td>
            </tr>
            <tr>
              <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span></td>
              <td class="total"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div id="removed-items"></div>
      <div id="billed-tasks"></div>
      <div id="billed-expenses"></div>
      <?php echo form_hidden('task_id'); ?>
      <?php echo form_hidden('expense_id'); ?>
   </div>
   </div>
   <div class="row">
      <div class="col-md-12 mtop15">
         <div class="panel-body bottom-transaction">
            <?php $value = (isset($invoice) ? clear_textarea_breaks($invoice->clientnote) : get_option('predefined_clientnote_invoice')); ?>
            <?php echo render_textarea('clientnote','invoice_add_edit_client_note',$value,array(),array(),'mtop15'); ?>
            <?php $value = (isset($invoice) ? clear_textarea_breaks($invoice->terms) : get_option('predefined_terms_invoice')); ?>
            <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15'); ?>
            <div class="btn-bottom-toolbar text-right">
                <input type="hidden" name="leadid" value="<?php echo isset($lid) ? $lid : '';?>">
                <input type="hidden" name="project_id" value="<?php echo isset($pid) ? $pid : '';?>">
                <input type="hidden" name="eventid" value="<?php echo isset($eid) ? $eid : '';?>">
                <button class="btn btn-default" type="button" onclick="fncancel();"><?php echo _l( 'Cancel'); ?></button>
                <?php if(!isset($invoice)){ ?>
                <button class="btn-tr btn btn-default mleft10 text-right invoice-form-submit save-as-draft">
                <?php echo _l('save_as_draft'); ?>
                </button>
                <?php } ?>                 
                <button class="btn-tr btn btn-info mleft10 text-right invoice-form-submit save-and-send">
                  <?php echo _l('save_and_send'); ?>
                </button>
                   <button class="btn-tr btn btn-info mleft10 text-right invoice-form-submit">
                <?php echo _l('submit'); ?>
                </button>
             </div>
         </div>
        <div class="btn-bottom-pusher"></div>
      </div>
   </div>
</div>

<script>

function fncancel(){    
    var id=<?php if(isset($lid)) { echo $lid;} else { echo '0';}  ?>;
    var pid=<?php if(isset($pid)) { echo $pid;} else { echo '0';}  ?>;
    var eid=<?php if(isset($eid)) { echo $eid;} else { echo '0';}  ?>;
    if( id > '0') {
      location.href='<?php echo base_url(); ?>admin/invoices?lid=' + id;
    }else if( pid > '0') {
      location.href='<?php echo base_url(); ?>admin/invoices?pid=' + pid;
    }else if( eid > '0') {
      location.href='<?php echo base_url(); ?>admin/invoices?eid=' + eid;
    }  else {
        window.history.go(-1);
    }
  }
</script>
