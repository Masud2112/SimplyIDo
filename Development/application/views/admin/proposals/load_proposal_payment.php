<div class="table-responsive s_table">
  <table class="table invoice-items-table items table-main-invoice-edit no-mtop">
    <thead>
      <tr>
        <th width="8%"></th>
        <th width="20%" class="text-left"><?php echo _l('invoice_table_item_heading'); ?></th>
        <?php
          $qty_heading = _l('invoice_table_quantity_heading');
        ?>
        <th width="10%" class="text-left qty"><?php echo $qty_heading; ?></th>
        <th width="15%" class="text-left" style="text-align: left"><?php echo _l('invoice_table_rate_heading'); ?></th>
        <th width="20%" class="text-left" style="text-align: left"><?php echo _l('invoice_table_tax_heading'); ?></th>
        <th width="10%" class="text-left"><?php echo _l('invoice_table_amount_heading'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php 
        if (isset($invoice) || isset($add_items)) {
          $i               = 1;
          $items_indicator = 'newitems';
          
          if (isset($invoice)) {
            $add_items       = $invoice->items;
            $items_indicator = 'newitems';
          }

          foreach ($add_items as $item) {
            $manual    = false;
            $table_row = '<tr class="sortable item">';
            $table_row .= '<td>';

            if (!is_numeric($item['qty'])) {
              $item['qty'] = 1;
            }

            $invoice_item_taxes = get_proposaltemplate_item_taxes($item['id']);

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
            $table_row .= '<td class="bold description"><input type="text" name="' . $items_indicator . '[' . $i . '][description]" class="form-control" value="' . clear_textarea_breaks($item['description']) . '"></td>';
            
            $table_row .= '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
            
            $table_row .= '</td>';
            $table_row .= '<td class="rate"><input type="number" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
            $table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item['id'], true, $manual) . '</td>';
            $table_row .= '<td class="amount">' . $amount . '</td>';
            
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
        <td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>
        </td>
        <td class="subtotal">
        </td>
      </tr>
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
        <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
        </td>
        <td class="total">
        </td>
      </tr>
    </tbody>
 </table>
</div>