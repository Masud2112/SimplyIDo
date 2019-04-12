<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 16-03-2018
 * Time: 13:41
 */
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
$disabled = "";
if (isset($preview)) {
    $preview = "preview";
    $cancelurl = "";
    $cancel_text = "Exit";
    $disabled = "disabled";
} else {
    $cancelurl = admin_url('proposaltemplates') . $rel_link;
    $cancel_text = "Cancel";
}
if (isset($token) && is_staff_logged_in()) {

}
?>
<?php $this->load->view('proposaltemplates/includes/head'); ?>
<div class="note no-margin row">
    <div class="col-sm-6 col-xs-6">
        <strong>
            <?php
            if (isset($preview) && $preview == "preview") { ?>
                <a class=""
                   href="<?php echo $proposal->status == "decline" ? admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link : admin_url('proposaltemplates') . $rel_link; ?>"
                   onclick="self.close()">
                    <i class="fa fa-angle-left mright10"></i>
                    Preview mode
                </a>
            <?php } else {
                if (isset($token)) {
                    if (is_staff_logged_in()) {
                        $href = admin_url('projects/dashboard/' . $rel_id);
                    } else {
                        $href = "javascript:void(0)";
                    }
                } else {
                    $href = admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link;
                }
                ?>
                <a class="" href="<?php echo $href; ?>">
                    <i class="fa fa-angle-left mright10"></i>
                    <?php
                    if (isset($token) && is_staff_logged_in()) {
                        echo "Project Dashboard";
                    } else {
                        echo $proposal->name;
                    } ?>
                </a>
            <?php } ?>
        </strong>
    </div>
    <div class="col-sm-6 col-xs-6">
        <?php
        $download = "javascript:void(0)";
        $print = "javascript:void(0)";
        $email = "javascript:void(0)";
        if (is_staff_logged_in() && ((isset($_GET['pid']) && $_GET['pid'] > 0) || (isset($_GET['lid']) && $_GET['lid'] > 0))) {
            $email = site_url('proposal/createemail/' . $proposal->templateid . $rel_link);
            if (isset($token)) {
                $email = "javascript:void(0)";
            }
            $download = "javascript:void(0)";
            $print = "javascript:void(0)";

        }
        if ($proposal->isclosed || $proposal->isarchieve) {
            $email = "javascript:void(0)";
        }
        $download = site_url('proposal/proposalpdf/'.$proposal->templateid);
        $print = site_url('proposal/proposalpdf/'.$proposal->templateid).'?print=true';
        ?>
        <div class="headicons text-right">
            <ul class="topiconmenu">
                <li class="inline-block mleft10">
                    <a href="<?php echo $download ?>" target="_blank">
                        <i class="fa fa-download"></i>
                    </a>
                </li>
                <li class="inline-block mleft10">
                    <a href="<?php echo $print ?>" target="_blank">
                        <i class="fa fa-print"></i>
                    </a>
                </li>
                <li class="inline-block mleft10">
                    <a href="<?php echo $email ?>">
                        <i class="fa fa-envelope-o"></i>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>
<div id="wrapper">

    <div class="content viewproposal proposal-template">
        <div class="row">
            <!-- Tabs Vertical Left -->
            <div class="col-md-12 widget-holder">
                <h1 class="pageTitleH1"><i class="fa fa-file-text-o "></i><?php echo ucfirst($title); ?></h1>
                <?php $this->load->view('proposaltemplates/proposal_bullets'); ?>
                <div class="widget-bg">
                    <div class="widget-body clearfix">
                        <?php
                        echo form_open_multipart($action, array('id' => 'proposal-form', 'class' => '_transaction_form'));
                        ?>
                        <div class="proposalsections">
                            <div class="coverpage">
                                <div class="editPro-block">
                                    <div class="topButton">
                                        <input id="proposal_id" type="hidden" name="proposal_id"
                                               value="<?php echo isset($proposal) ? $proposal->templateid : ''; ?>">
                                    </div>
                                    <?php $attrs = (isset($proposal) ? array() : array('autofocus' => true)); ?>
                                    <?php $value = (isset($proposal) ? $proposal->name : ''); ?>
                                    <h1 class="proposal_tittle text-center">
                                        <?php echo $value ?>
                                            <div class="proposal_status pull-right">
                <span class="label <?php echo strtolower($proposal->status); ?> p7 inline-block pull-right text-center"><?php echo strtoupper($proposal->status); ?></span>
            </div>
                                        </h1>
                                    <input type="hidden" id="rel_type" name="rel_type" class="form-control"
                                           value="<?php echo $rel_type; ?>">
                                    <input type="hidden" id="rel_id" name="rel_id" class="form-control"
                                           value="<?php echo $rel_id; ?>">

                                    <?php $this->load->view('proposaltemplates/proposal_banner'); ?>
                                    <?php
                                    if (!in_array('introduction', $removed_sections)) {
                                        $this->load->view('proposaltemplates/introduction');
                                    } ?>
                                    <?php
                                    if (!in_array('gallery', $removed_sections)) {
                                        $this->load->view('proposaltemplates/gallery', $pgallery);
                                    }
                                    ?>
                                    <?php
                                    if (!in_array('files', $removed_sections)) {
                                        $this->load->view('proposaltemplates/files');
                                    }
                                    ?>
                                </div>
                                <div class="proposal_actions text-center mbot25">
                                    <div class="inline-block">
                                        <a class="btn btn-info"
                                           href="<?php echo $proposal->status == "draft" ? admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link : admin_url('proposaltemplates') . $rel_link; ?>"
                                           onclick="self.close()">
                                            <i class="fa fa-reply" aria-hidden="true"></i>
                                            <?php echo _l('exit_proposal'); ?>
                                        </a>
                                    </div>
                                    <div class="inline-block">
                                        <a class="btn proposal_step slickNext btn-primary" href="#quote"
                                           data-tab="quote">
                                            <?php echo _l('view_quote'); ?>
                                            <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="quotes">
                                <?php
                                if (!in_array('quote', $removed_sections)) {
                                    $this->load->view('proposaltemplates/proposal_quotes', $pquotes);
                                }
                                ?>
                                <?php
                                if (!in_array('payments', $removed_sections)) {
                                    $this->load->view('proposaltemplates/payment_schedule');
                                }
                                ?>
                                <div class="proposal_actions text-center mbot25">
                                    <?php if ($proposal->status != "decline") { ?>

                                        <div class="inline-block <?php echo isset($token) ? "" : "disabled" ?>">
                                            <a class="btn btn-decline <?php echo isset($token) ? "" : "disabled" ?>"
                                               href="<?php echo site_url('proposaltemplates/updatestatus/decline/' . $proposal->templateid) ?>">
                                                <i class="fa fa-remove" aria-hidden="true"></i>
                                                <?php echo _l('decline'); ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div class="inline-block">
                                        <a class="btn btn-info"
                                           href="<?php echo $proposal->status == "draft" ? admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link : admin_url('proposaltemplates') . $rel_link; ?>"
                                           onclick="self.close()">
                                            <i class="fa fa-reply" aria-hidden="true"></i>
                                            <?php echo _l('exit_proposal'); ?>
                                        </a>
                                    </div>
                                    <div class="inline-block">
                                        <a class="btn proposal_step slickNext btn-primary" href="#agreement"
                                           data-tab="introduction">
                                            <i class="fa fa-angle-left mleft10" aria-hidden="true"></i>
                                            <?php echo _l('cover_page'); ?>
                                        </a>
                                    </div>
                                    <div class="inline-block">
                                        <a class="btn proposal_step slickNext btn-primary" href="#agreement"
                                           data-tab="agreement">
                                            <?php echo _l('agreement'); ?>
                                            <i class="fa fa-angle-right mleft10" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="agreement">
                                <?php
                                if (!in_array('agreement', $removed_sections)) {
                                    $this->load->view('proposaltemplates/agreement' ,array('removed_sections'=>$removed_sections));
                                }
                                ?>
                                <?php
/*                                if (!in_array('message', $removed_sections)) {
                                    $this->load->view('proposaltemplates/clent_message');
                                }
                                */?>

                            </div>
                            <?php
                            if (isset($proposal->feedback) && $proposal->feedback->is_invoiced == 1) {
                                $this->load->view('proposaltemplates/invoice');
                            }
                            ?>
                            <?php
                            if ((isset($proposal->feedback) && $proposal->feedback->is_invoiced == 0) || !isset($proposal->feedback)) {
                            ?>
                        </div>
                    <?php echo form_close();
                    } ?>
                        <!-- /.tabs -->
                    </div>
                    <!-- /.widget-body -->
                </div>
                <!-- /.widget-bg -->
            </div>

        </div>
    </div>
</div>
<div id="top" class="hidden fixed"><i class="fa fa-angle-up"></i></div>

<!---- Decline Proposal start --->

<div class="modal fade" id="decline_proposal_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="decline_form"
                  action="<?php echo site_url('proposal/decline/' . $proposal->templateid) ?>"
                  method="post">
                <div class="modal-body">
                    <h2 class="text-center" id="myModalLabel">
                        Are you sure?
                    </h2>
                    <div class="confirm_comment mtop35">
                        <label>Please Let us know the reason to decline proposal</label>
                        <textarea name="resason_comment" id="reason" rows="10" style="width: 100%"></textarea>
                    </div>
                    <input name="declinedby[userid]" type="hidden" id="userid" value="<?php echo $authclient; ?>">
                    <input name="declinedby[usertype]" type="hidden" id="usertype" value="<?php echo $authtype; ?>">
                    <input name="declinedat" type="hidden" id="declinedat" value="<?php echo date('Y-m-d H:i:s'); ?>">
                    <?php if (isset($token)) { ?>
                        <input name="usertoken" type="hidden" id="usertoken" value="<?php echo $token; ?>">
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">Cancel</span>
                    </button>
                    <input type="submit" class="btn btn-info"
                           value="Submit" <?php echo isset($token) ? "" : "disabled" ?>/>
                </div>
            </form>
        </div>
    </div>
</div>
<!---- Decline Proposal end --->

<?php $this->load->view('proposaltemplates/includes/scripts'); ?>
<script>
    var validator = $("#proposal-form").validate({
        rules: {name: {required: true}},
    });
    $(function () {
        _validate_form($('.proposal-form'), {name: 'required'});
        _validate_form($('#decline_form'), {reason: 'required'});
        $('.proposalsections').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            infinite: false,
            draggable: false,
            adaptiveHeight: true,
            asNavFor: '.proposal_bullets',
            centerMode: false,
            /*swipe: false,
            touchMove: false*/
        });
        $('.proposal_bullets').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: '.proposalsections',
            dots: false,
            centerMode: false,
            focusOnSelect: true,
            useTransform: false
        });
    });
    var createamountValidation = function () {
        $(".multiphone .form-control").each(function () {
            $(this).mask("(999) 999-9999", {placeholder: "(___) ___-____"});
        });
    }
    function closeCurrentTab() {
        var conf = confirm("Are you sure, you want to Exit Proposal?");
        if (conf == true) {
            window.close();
        }
    }

    // On before slide change
    /*$('.proposalsections').on('afterChange', function(event, slick, currentSlide){
        if(currentSlide==4){
            $('.makepayment').toggleClass('hide');
        }
    });*/

</script>
</body>
</html>
