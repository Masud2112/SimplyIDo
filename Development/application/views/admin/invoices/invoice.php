<?php init_head(); ?>
<div id="wrapper">
    <div class="content invoice-page">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form'));
            if(isset($invoice)){
                echo form_hidden('isedit','true');
            }
            ?>
            <div class="col-md-12">
                <?php $this->load->view('admin/invoices/invoice_template'); ?>
            </div>
            <?php echo form_close(); ?>
            <?php //$this->load->view('admin/invoice_items/item'); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        validate_invoice_form();
        // Init accountacy currency symbol
        init_currency_symbol();
        // Project ajax search
        init_ajax_project_search_by_customer_id();
        // Maybe items ajax search
        init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
    });

    /*
    ** Added By Sanjay on 02/08/2018
    ** For start-date and end-date
    */
    $(function(){

        $(".input-group-addon").css({"padding": "0px"});
        $(".fa.fa-calendar.calendar-icon").css({"padding": "6px 12px"});

        $('.input-group-addon').find('.fa-calendar').on('click', function(){
            $(this).parent().siblings('#date').trigger('focus');
            $(this).parent().siblings('#duedate').trigger('focus');
        });

        url = window.location.href;
        //    var date=url.split('?')[1].split('=')[1];
        //    if(date)
        //   	{
        //     var spl_txt = date.split('-');
        //     var time = new Date();
        //     date = spl_txt[1]+"/"+spl_txt[2]+"/"+spl_txt[0]+" "+time.getHours() + ":" + time.getMinutes();
        //     $('#date').val(date);
        // }
    });

</script>
</body>
</html>
