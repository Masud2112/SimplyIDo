<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 16-03-2018
 * Time: 13:41
 */
?>
<?php
$pquotes['quotes'] = isset($quotes) ? $quotes : [];
$pgallery['pid'] = isset($proposal) ? $proposal->templateid : "";
$pgallery ['gallery'] = isset($gallery) ? $gallery : "";
$removed_sections = array();
if (isset($proposal) && $proposal->removed_sections != "null" && !empty($proposal->removed_sections)) {
    $removed_sections = json_decode($proposal->removed_sections);
}
$bullets['sections'] = $removed_sections;
$action = $this->uri->uri_string();
$rel_id = $proposal->rel_id;
$rel_type = $proposal->rel_type;
if (isset($_GET['pid']) && $_GET['pid'] > 0) {
    $rel_link = "?pid=" . $_GET['pid'];
} elseif (isset($_GET['lid']) && $_GET['lid'] > 0) {
    $rel_link = "?lid=" . $_GET['lid'];
} else {
    $rel_link = "";
}
$preview = "";
$disabled = "";
if (isset($_GET['preview'])) {
    $preview = "preview";
    $cancelurl = "";
    $cancel_text = "Exit";
    $disabled = "disabled";
} else {
    $cancelurl = admin_url('proposaltemplates') . $rel_link;
    $cancel_text = "Cancel";
}
?>
<?php init_head(); ?>
<?php /*if(isset($_GET['preview'])){ */ ?>
<p class="note no-margin"><strong><a class="" href="javascript:history.go(-1)"><i class="fa fa-angle-left mright10"></i>Preview
            mode</a></strong></p>
<?php /*} */ ?>
<div class=""
<div id="wrapper">

    <div class="content viewproposal proposal-template">
        <div class="row">
            <!-- Tabs Vertical Left -->
            <?php echo form_open_multipart($action, array('id' => 'proposal-form', 'class' => '_transaction_form')); ?>
            <div class="col-md-12 widget-holder">
                <h1 class="pageTitleH1"><i class="fa fa-file-text-o "></i><?php echo ucfirst($title); ?></h1>
                <?php $this->load->view('admin/proposaltemplates/viewproposal/proposal_bullets'); ?>
                <div class="widget-bg">
                    <div class="widget-body clearfix">
                        <div class="editPro-block mbot25">
                            <div class="topButton">
                                <input type="hidden" name="proposal_id"
                                       value="<?php echo isset($proposal) ? $proposal->templateid : ''; ?>">
                                <!--<a class="btn btn-default" type="button"
                                        href='<?php /*echo $cancelurl */ ?>';><?php /*echo $cancel_text; */ ?>
                                </a>-->
                                <!--<button <?php /*echo $disabled */ ?> type="submit"
                                        class="btn btn-info proposal-form-submit"><?php /*echo _l('send'); */ ?>
                                </button>-->
                            </div>
                            <?php $attrs = (isset($proposal) ? array() : array('autofocus' => true)); ?>
                            <?php $value = (isset($proposal) ? $proposal->name : ''); ?>
                            <h1 class="proposal_tittle text-center"><?php echo $value ?></h1>
                            <input type="hidden" id="rel_type" name="rel_type" class="form-control"
                                   value="<?php echo $rel_type; ?>">
                            <input type="hidden" id="rel_id" name="rel_id" class="form-control"
                                   value="<?php echo $rel_id; ?>">
                            <?php $this->load->view('admin/proposaltemplates/viewproposal/proposal_banner'); ?>
                            <?php
                            if (!in_array('introduction', $removed_sections)) {
                                $this->load->view('admin/proposaltemplates/viewproposal/introduction');
                            } ?>
                        </div>

                        <?php
                        if (!in_array('gallery', $removed_sections)) {
                            $this->load->view('admin/proposaltemplates/viewproposal/gallery', $pgallery);
                        }
                        ?>
                        <?php
                        if (!in_array('files', $removed_sections)) {
                            $this->load->view('admin/proposaltemplates/viewproposal/files');
                        }
                        ?>
                        <div class="proposal_actions text-center mbot25">
                            <div class="inline-block">
                                <a class="btn btn-info"
                                   href="<?php echo admin_url('proposaltemplates/proposal/' . $proposal->templateid) ?>">
                                    <i class="fa fa-reply" aria-hidden="true"></i>
                                    <?php echo _l('exit_proposal'); ?>
                                </a>
                            </div>
                            <div class="inline-block">
                                <a class="btn proposal_step btn-primary" href="#quote">
                                    <?php echo _l('view_quote'); ?>
                                    <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                        <?php
                        if (!in_array('quote', $removed_sections)) {
                            $this->load->view('admin/proposaltemplates/viewproposal/proposal_quotes', $pquotes);
                        }
                        ?>
                        <?php
                        if (!in_array('payments', $removed_sections)) {
                            $this->load->view('admin/proposaltemplates/viewproposal/payment_schedule');
                        }
                        ?>
                        <div class="proposal_actions text-center mbot25">
                            <div class="inline-block">
                                <a class="btn btn-info btn-decline"
                                   href="<?php echo admin_url('proposaltemplates/updatestatus/decline/' . $proposal->templateid) ?>">
                                    <i class="fa fa-remove" aria-hidden="true"></i>
                                    <?php echo _l('decline'); ?>
                                </a>
                            </div>
                            <div class="inline-block">
                                <a class="btn btn-info"
                                   href="<?php echo admin_url('proposaltemplates/proposal/' . $proposal->templateid) ?>">
                                    <i class="fa fa-reply" aria-hidden="true"></i>
                                    <?php echo _l('exit_proposal'); ?>
                                </a>
                            </div>
                            <div class="inline-block">
                                <a class="btn proposal_step btn-primary" href="#agreement">
                                    <?php echo _l('view_agreement'); ?>
                                    <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                        <?php
                        if (!in_array('agreement', $removed_sections)) {
                            $this->load->view('admin/proposaltemplates/viewproposal/agreement');
                        }
                        ?>
                        <?php
                        if (!in_array('message', $removed_sections)) {
                            $this->load->view('admin/proposaltemplates/viewproposal/clent_message');
                        }
                        ?>
                        <?php
                        if (!in_array('signatures', $removed_sections)) {
                            $this->load->view('admin/proposaltemplates/viewproposal/signatures');
                        }
                        ?>
                        <div class="proposal_actions text-center mbot25">
                            <div class="inline-block">
                                <a class="btn btn-info btn-decline"
                                   href="<?php echo admin_url('proposaltemplates/updatestatus/decline/' . $proposal->templateid) ?>">
                                    <i class="fa fa-remove" aria-hidden="true"></i>
                                    <?php echo _l('decline'); ?>
                                </a>
                            </div>
                            <div class="inline-block">
                                <a class="btn btn-info"
                                   href="<?php echo admin_url('proposaltemplates/proposal/' . $proposal->templateid) ?>">
                                    <i class="fa fa-reply" aria-hidden="true"></i>
                                    <?php echo _l('exit_proposal'); ?>
                                </a>
                            </div>
                            <!--<div class="inline-block">
                                <?php /*if ($proposal->status == "accepted") { */?>
                                    <a class="btn proposal_step btn-primary" href="#invoice">
                                        <?php /*echo _l('view_invoice'); */?>
                                        <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                                    </a>
                                <?php /*} else { */?>
                                    <button class="btn proposal_step btn-primary">
                                        <?php /*echo _l('accept_view_invoice'); */?>
                                        <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                                    </button>
                                <?php /*} */?>
                            </div>-->
                            <button class="btn proposal_step btn-primary">
                                <?php echo _l('accept_view_invoice'); ?>
                                <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                            </button>
                        </div>
                        <?php
                        if ($proposal->status == "accepted") {
                            $this->load->view('admin/proposaltemplates/viewproposal/invoice');
                        }
                        ?>
                        <!-- /.tabs -->
                    </div>
                    <!-- /.widget-body -->
                </div>
                <!-- /.widget-bg -->
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div id="top" class="hidden fixed"><i class="fa fa-angle-up"></i></div>
<?php init_tail(); ?>
<script>
    var validator = $("#proposal-form").validate({
        rules: {name: {required: true}},
    });
    $(function () {
        _validate_form($('.proposal-form'), {name: 'required'});
    });
</script>
</body>
</html>
