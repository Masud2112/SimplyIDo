<?php init_head(); ?>
<div id="wrapper" class="paymentschedule-page">
    <div class="content">


        <div class="breadcrumb">
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('setup'); ?>">Settings</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('paymentschedules'); ?>">Payment Schedule</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php if (isset($paymentschedule)) { ?>
                <span><?php echo $paymentschedule->name; ?></span>
            <?php } else { ?>
                <span>New Payment Schedule</span>
            <?php } ?>

        </div>

        <h1 class="pageTitleH1"><i class="fa fa-calendar "></i><?php echo $title; ?></h1>
        <div class="clearfix"></div>
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), array('id' => 'paymentschedule_template')); ?>
            <div class="col-md-12">
                <div class="panel_s btmbrd">
                    <div class="panel-body payment-grid">

                        <div class="clearfix"></div>
                        <?php $value = (isset($paymentschedule) ? $paymentschedule->name : ''); ?>
                        <div class="form-group">
                            <label class="control-label" for="name">
                                <small class="req text-danger">*</small>
                                Name</label>
                            <input id="name" class="form-control" name="name" autofocus="1"
                                   value="<?php echo $value; ?>" type="text">
                        </div>
                        <div class="table-responsive1">
                            <div class="payment-header">
                                <ul class="payment-head">
                                    <li>Status</li>
                                    <li>Invoice #</li>
                                    <li>Due Date</li>
                                    <li>Amount</li>
                                    <li></li>
                                </ul>
                            </div>
                            <div class="payment-body">
                                <?php if (!empty($paymentschedule->schedules)) {
                                    $pe = 0;
                                    foreach ($paymentschedule->schedules as $pk => $pv) {
                                        if ($pv['duedate_type'] == "today") {
                                            $pv['duedate_type'] = "upon_signing";
                                        }
                                        ?>
                                        <div class='payment-wrapper' id='paymentwrapper-<?php echo $pe; ?>'>
                                            <ul class='payment-wrapper-ul'>
                                                <li><span class='pull-left'>Unpaid</span></li>
                                                <li>--</li>
                                                <li>
                                                    <a href='javascript:void(0)' class='payment-way'>
                                                        <span><?php echo $duedate_types[$pv['duedate_type']]; ?></span>
                                                        <i class="fa fa-caret-down"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href='javascript:void(0)' class='payment-wrapper-price'>
                                                        <span>
                                                            <?php if ($pv['price_type'] == "divide_equally") {
                                                                echo "Divide Equally";
                                                            } elseif ($pv['price_type'] == "fixed_amount") {
                                                                echo !empty($pv['price_amount']) ? format_money($pv['price_amount']) : "$0.00";
                                                            } else {
                                                                echo !empty($pv['price_percentage']) ? $pv['price_percentage'] . "%" : "0%";
                                                            } ?>
                                                        </span>
                                                        <i class="fa fa-caret-down"></i>
                                                    </a>
                                                </li>
                                                <li><a href='javascript:void(0)'
                                                       class='btn btn-danger btn-xs remove-payment'><i
                                                                class='fa fa-times'></i></a></li>
                                            </ul>
                                            <div class='sub-payment-wrapper' style="display: none;">
                                                <div class='col-md-3'>
                                                    <select class='selectpicker payment-data'
                                                            name='payment_schedule[<?php echo $pe; ?>][duedate_type]'
                                                            data-width='100%' data-live-search='false'>
                                                        <?php foreach ($duedate_types as $dtkey => $dtvalue) { ?>
                                                            <option value='<?php echo($dtkey); ?>' <?php echo ($dtkey == $pv['duedate_type']) ? "selected" : "" ?>><?php echo($dtvalue); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <?php
                                                if ($pv['duedate_type'] == "custom") {
                                                    $payment_class = "";
                                                } else {
                                                    $payment_class = "hide";
                                                }
                                                ?>
                                                <div class='col-md-9 custom-payment-wrapper <?php echo($payment_class) ?>'>
                                                    <div class='col-md-2'>
                                                        <input type='number' class='form-control' min='1'
                                                               value='<?php echo $pv['duedate_number'] ?>'
                                                               name='payment_schedule[<?php echo $pe; ?>][duedate_number]'>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <select class='selectpicker custom-range-duration'
                                                                name='payment_schedule[<?php echo $pe; ?>][custom_range_duration]'
                                                                data-width='100%'' data-live-search='false'>
                                                        <?php foreach ($duedate_duration as $ddkey => $ddvalue) { ?>
                                                            <option value='<?php echo($ddkey); ?>' <?php echo ($ddkey == $pv['custom_range_duration']) ? "selected" : "" ?>><?php echo($ddvalue); ?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class='col-md-3'>
                                                        <select class='selectpicker custom-range-at'
                                                                name='payment_schedule[<?php echo $pe; ?>][duedate_criteria]'
                                                                data-width='100%'' data-live-search='false'>
                                                        <?php foreach ($duedate_criteria as $dckey => $dcvalue) { ?>
                                                            <option value='<?php echo($dckey); ?>' <?php echo ($dckey == $pv['duedate_criteria']) ? "selected" : "" ?>><?php echo($dcvalue); ?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class='sub-payment-price-wrapper' style='display:none'>
                                                <div class='col-md-3'>
                                                    <select class='selectpicker amount-data'
                                                            name='payment_schedule[<?php echo $pe; ?>][price_type]'
                                                            data-width='100%'' data-live-search='false'>
                                                    <?php foreach ($amount_types as $atkey => $atvalue) { ?>
                                                        <option value='<?php echo($atkey); ?>' <?php echo ($atkey == $pv['price_type']) ? "selected" : "" ?>><?php echo($atvalue); ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                                <?php
                                                if ($pv['price_type'] == "fixed_amount") {
                                                    $amttype_class = "";
                                                } else {
                                                    $amttype_class = "hide";
                                                }
                                                ?>
                                                <div class='col-md-2 custom-payment-price-wrapper <?php echo($amttype_class) ?>'>

                                                    <div class="input-group">
                                                        <span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span>
                                                        <input type='number' class='form-control price_amount' min='0' 
                                                               name='payment_schedule[<?php echo $pe; ?>][price_amount]' 
                                                               value="<?php echo($pv['price_amount']) ?>">
                                                    </div>
                                                </div>

                                                <?php
                                                if ($pv['price_type'] == "percentage") {
                                                    $ptttype_class = "";
                                                } else {
                                                    $ptttype_class = "hide";
                                                }
                                                ?>
                                                <div class='col-md-2 custom-payment-percentage-wrapper <?php echo($ptttype_class); ?>'>
                                                    <div class="input-group">
                                                        <input type='number' class='form-control price_percentage' min='0'
                                                           name='payment_schedule[<?php echo $pe; ?>][price_percentage]'
                                                           value="<?php echo $pv['price_percentage']; ?>">
                                                        <span class="input-group-addon" ><i class="fa fa-percent" aria-hidden="true"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $pe++;
                                    } ?>
                                <?php } else { ?>
                                    <div class='payment-wrapper' id='paymentwrapper-0'>
                                        <ul class='payment-wrapper-ul'>
                                            <li>
                                                <span class='pull-left'>Unpaid</span></li>
                                            <li>--</li>
                                            <li><a href='javascript:void(0)' class='payment-way'><span>Midway</span> <i
                                                            class="fa fa-caret-down"></i></a></li>
                                            <li><a href='javascript:void(0)'
                                                   class='payment-wrapper-price'>$<span>0</span> <i
                                                            class="fa fa-caret-down"></i></a></li>
                                            <li><a href='javascript:void(0)'
                                                   class='btn btn-danger remove-payment btn-xs'><i
                                                            class='fa fa-times'></i></a></li>
                                        </ul>
                                        <div class='sub-payment-wrapper' style='display:none'>
                                            <div class='col-md-3'>
                                                <select class='selectpicker payment-data'
                                                        name='payment_schedule[0][duedate_type]' data-width='100%''
                                                data-live-search='false'>
                                                <?php foreach ($duedate_types as $dtkey => $dtvalue) { ?>
                                                    <option value='<?php echo($dtkey); ?>' <?php echo ($dtkey == "midway") ? "selected" : "" ?>><?php echo($dtvalue); ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class='col-md-9 custom-payment-wrapper hide'>
                                                <div class='col-md-2'>
                                                    <input type='number' class='form-control' min='1' value='1'
                                                           name='payment_schedule[0][duedate_number]'>
                                                </div>
                                                <div class='col-md-3'>
                                                    <select class='selectpicker custom-range-duration'
                                                            name='payment_schedule[0][custom_range_duration]'
                                                            data-width='100%'' data-live-search='false'>
                                                    <?php foreach ($duedate_duration as $ddkey => $ddvalue) { ?>
                                                        <option value='<?php echo($ddkey); ?>'><?php echo($ddvalue); ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                                <div class='col-md-3'>
                                                    <select class='selectpicker custom-range-at'
                                                            name='payment_schedule[0][duedate_criteria]'
                                                            data-width='100%'' data-live-search='false'>
                                                    <?php foreach ($duedate_criteria as $dckey => $dcvalue) { ?>
                                                        <option value='<?php echo($dckey); ?>'><?php echo($dcvalue); ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='sub-payment-price-wrapper' style='display:none'>
                                            <div class='col-md-3'>
                                                <select class='selectpicker amount-data'
                                                        name='payment_schedule[0][price_type]' data-width='100%''
                                                data-live-search='false'>
                                                <?php foreach ($amount_types as $atkey => $atvalue) { ?>
                                                    <option value='<?php echo($atkey); ?>'><?php echo($atvalue); ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                            <div class='col-md-2 custom-payment-price-wrapper hide'>
                                                    <div class="input-group">
                                                        <span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span>
                                           
                                                <input type='number' class='form-control price_amount' min='0'
                                                       n    ame='payment_schedule[0][price_amount]'>
                                            </div>
                                            </div>
                                            <div class='col-md-2 custom-payment-percentage-wrapper hide'>
                                                    <div class="input-group">
                                                <input type='number' class='form-control' min='0'
                                                       name='payment_schedule[0][price_percentage]'>
                                                        <span class="input-group-addon" ><i class="fa fa-percent" aria-hidden="true"></i></span>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="payment-footer">
                                <div class="col-md-12 ">
                                    <div class="add-payment-btn pull-right">
                                        <a href="javascript:void(0)" class="btn btn-primary" id="payment-add-more">Add
                                            Payment</a>
                                    </div>
                                </div>
                                <!-- <div class="col-md-7 text-right">
                                      <div class="total-wrapper">
                                          <span class="mright20">Total</span>
                                          $<span class="total">0</span>
                                      </div>

                                </div> -->
                            </div>
                        </div>
                        <div class="topButton">
                            <button class="btn btn-default" type="button"
                                    onclick="location.href='<?php echo base_url(); ?>admin/paymentschedules'"><?php echo _l('Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (isset($paymentschedule->templateid)) { ?>
                <input type="hidden" name="paymentscheduleid" value="<?php echo $paymentschedule->templateid ?>">
            <?php } ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $("#paymentschedule_template").validate(
        {
            ignore: [],
            rules: {
                name: {
                    required: true,
                    remote: {
                        url: site_url + "admin/misc/payment_title_exists",
                        type: 'post',
                        data: {
                            name: function () {
                                return $('input[name="name"]').val();
                            },
                            paymentscheduleid: function () {
                                return $('input[name="paymentscheduleid"]').val();
                            }
                        }
                    }
                }
            }
        });
    $(document).ready(function () {
        var duedate_types = <?php echo json_encode($duedate_types); ?>;
        var duedate_criteria = <?php echo json_encode($duedate_criteria); ?>;
        var amount_types = <?php echo json_encode($amount_types); ?>;
        var duedate_duration = <?php echo json_encode($duedate_duration); ?>;

        $("#payment-add-more").on("click", function () {
            var paymentschedule_wrapper = $("div[id^='paymentwrapper-']");
            var highestschedule = -Infinity;
            $.each(paymentschedule_wrapper, function (mindex, mvalue) {
                var fieldpaymentNum = mvalue.id.split("-");
                highestschedule = Math.max(highestschedule, parseFloat(fieldpaymentNum[1]));
            });
            highestschedule = highestschedule + 1;
            var final_html = "";
            final_html += "<div class='payment-wrapper' id='paymentwrapper-" + highestschedule + "'><ul class='payment-wrapper-ul'><li><span>Unpaid</span></li><li>--</li><li><a href='javascript:void(0)' class='payment-way'><span>Midway</span> <i class='fa fa-caret-down'></i></a></li><li><a href='javascript:void(0)' class='payment-wrapper-price'>$<span>0</span> <i class='fa fa-caret-down'></i></a></li></li><li><a href='javascript:void(0)' class='btn btn-danger remove-payment btn-xs'><i class='fa fa-times'></i></a></li></ul><div class='sub-payment-wrapper' style='display:none'><div class='col-md-3'><select class='selectpicker payment-data' name='payment_schedule[" + highestschedule + "][duedate_type]' data-width='100%' data-live-search='false'>";
            $.each(duedate_types, function (dtindex, dtvalue) {
                var dtselected = "";
                if (dtindex == 'midway') {
                    dtselected = "selected";
                }
                final_html += "<option value='" + dtindex + "' " + dtselected + ">" + dtvalue + "</option>";
            });
            final_html += "</select></div><div class='col-md-9 custom-payment-wrapper hide'><div class='col-md-2'><input type='number' class='form-control' min='1' value='1' name='payment_schedule[" + highestschedule + "][duedate_number]'></div><div class='col-md-3'><select class='selectpicker custom-range-duration' name='payment_schedule[" + highestschedule + "][custom_range_duration]' data-width='100%'' data-live-search='false'>";
            $.each(duedate_duration, function (ddindex, ddvalue) {
                final_html += "<option value='" + ddindex + "'>" + ddvalue + "</option>";
            });
            final_html += "</select></div><div class='col-md-3'><select class='selectpicker custom-range-at' name='payment_schedule[" + highestschedule + "][duedate_criteria]' data-width='100%'' data-live-search='false'>";
            $.each(duedate_criteria, function (dcindex, dcvalue) {
                final_html += "<option value='" + dcindex + "'>" + dcvalue + "</option>";
            });
            final_html += "</select></div></div></div><div class='sub-payment-price-wrapper' style='display:none'><div class='col-md-3'><select class='selectpicker amount-data' name='payment_schedule[" + highestschedule + "][price_type]' data-width='100%'' data-live-search='false'>";
            $.each(amount_types, function (atindex, atvalue) {
                final_html += "<option value='" + atindex + "'>" + atvalue + "</option>";
            });
            final_html += "</select></div><div class='col-md-2 custom-payment-price-wrapper hide'><div class='input-group'><span class='input-group-addon' ><i class='fa fa-usd'></i></span><input type='number' class='form-control price_amount' min='0' name='payment_schedule[" + highestschedule + "][price_amount]'></div></div><div class='col-md-2 custom-payment-percentage-wrapper hide'><div class='input-group'><span class='input-group-addon' ><i class='fa fa-usd'></i></span><input type='number' class='form-control' min='0' name='payment_schedule[" + highestschedule + "][price_percentage]'><span>%</span></div></div></div></div>";
            $(".payment-body").append(final_html);
            $(".selectpicker").selectpicker('refresh');
        });
        $("body").on('click', '.remove-payment', function () {
            $(this).parents(".payment-wrapper").remove();
        });

        /*$("body").on('click', '.payment-way', function() {
            $(this).parents(".payment-wrapper").find( ".sub-payment-price-wrapper" ).hide();
            $(this).parents(".payment-wrapper").find( ".sub-payment-wrapper" ).toggle();
            $(this).parents(".payment-wrapper").find(".payment-price").removeClass("active");
            $(this).toggleClass("active");
        });
        $("body").on('click', '.payment-price', function() {
            $(this).parents(".payment-wrapper").find( ".sub-payment-wrapper" ).hide();
            $(this).parents(".payment-wrapper").find( ".sub-payment-price-wrapper" ).toggle();
            $(this).parents(".payment-wrapper").find(".payment-way").removeClass("active");
            $(this).toggleClass("active");
        });
        $("body").on('change', '.payment-data', function() {
            var payment_way = $(this).find("option:selected").text();
            $(this).parents(".payment-wrapper").find( ".payment-way span" ).html(payment_way);
            if($(this).val() == 'custom'){
               $(this).parents(".payment-wrapper").find(".custom-payment-wrapper").removeClass("hide");
            }else{
               $(this).parents(".payment-wrapper").find(".custom-payment-wrapper").addClass("hide");
            }
            return false;
        });
        $("body").on('change', '.amount-data', function() {
            if($(this).val() == 'fixed_amount'){
               $(this).parents(".payment-wrapper").find(".custom-payment-price-wrapper").removeClass("hide");
               $(this).parents(".payment-wrapper").find(".custom-payment-percentage-wrapper").addClass("hide");
            }else if($(this).val() == 'percentage'){
               $(this).parents(".payment-wrapper").find(".custom-payment-price-wrapper").addClass("hide");
               $(this).parents(".payment-wrapper").find(".custom-payment-percentage-wrapper").removeClass("hide");
               $(this).parents(".payment-wrapper").find( ".payment-price" ).html("$0");
            }else{
               $(this).parents(".payment-wrapper").find(".custom-payment-price-wrapper").addClass("hide");
              $(this).parents(".payment-wrapper").find(".custom-payment-percentage-wrapper").addClass("hide");
               $(this).parents(".payment-wrapper").find( ".payment-price" ).html("$0");
            }
            return false;
        });

        $("body").on('keyup', '.price_amount', function() {
            var payment_way = $(this).val();
            if(payment_way.length > 0){
                $(this).parents(".payment-wrapper").find( ".payment-price > span" ).html(number_format(payment_way));
            }else{
               $(this).parents(".payment-wrapper").find( ".payment-price > span" ).html("0");
            }
            // var total = $(".total").html();
            // var final_total = parseInt(payment_way) + parseInt(total);
            // $(".total").html(final_total);

        });*/
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

</script>
</body>
</html>