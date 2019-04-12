<?php init_head(); ?>
<div id="wrapper">
    <div class="content invoice-page">
        <div class="row manage-invoice-page">
            <?php
            //include_once(APPPATH.'views/admin/invoices/filter_params.php');
            $this->load->view('admin/invoices/list_template');
            ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    //initDataTable('.table-invoices');
</script>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>var hidden_columns = [2,6,7];</script>

<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script>

    $(function(){

        var path= window.location.pathname.substring(window.location.pathname.lastIndexOf('/') + 1);
        if(path.indexOf("list_invoices") >= 0){
            $("#dvList").addClass("hide");
        } else {
            $("#dvList").removeClass("hide");
        }

        init_invoices_total(true);
        slideToggle('#stats-top');
        init_invoice();
        $('input[name="view_invoicedate"]').daterangepicker({
            clearBtn: true
        });

        invoices_kanban();
    });

    /**
     * Added By : Avni
     * Dt : 11/22/2017
     * to clear view invoicedate filter on cancel button
     */
    $('#view_invoicedate').on('cancel.daterangepicker', function(ev, picker) {
        //do something, like clearing an input
        $('#view_invoicedate').val('');
        $('.table-invoice').DataTable().ajax.reload();
    });
</script>
</body>
</html>
