<?php
$pquotes['packages'] = $items_groups;
$pquotes['items'] = $items;
$pquotes['quotes'] = isset($quotes) ? $quotes : [];
$pquotes['taxes'] = $taxes;
$pgallery['pid'] = isset($proposal) ? $proposal->templateid : "";
$pgallery ['gallery'] = isset($gallery) ? $gallery : "";
$removed_sections = array();
if (isset($proposal) && $proposal->removed_sections != "null" && !empty($proposal->removed_sections)) {
    $removed_sections = json_decode($proposal->removed_sections);
}
$bullets['sections'] = $removed_sections;
$action = $this->uri->uri_string();
if (isset($_GET['lid'])) {
    $rel_id = $_GET['lid'];
    $rel_type = 'lead';
    $action = $action . "?lid=" . $rel_id . '&preview=true';
    $rel_link = '?lid=' . $rel_id;
    $plttl = "Leads";
    $pllink = 'leads';

} elseif (isset($_GET['pid'])) {
    $rel_id = $_GET['pid'];
    $rel_type = 'project';
    $action = $action . "?pid=" . $rel_id . '&preview=true';
    $rel_link = '?pid=' . $rel_id;
    $plttl = "Projects";
    $pllink = 'projects';
} else {
    $rel_id = "";
    $rel_type = '';
    $action = $action . '?preview=true';
    $rel_link = '';
    $plttl = "";
}
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content proposal-template">
        <div class="row">
            <!-- Tabs Vertical Left -->
            <?php echo form_open_multipart($action, array('id' => 'proposal-form', 'class' => '_transaction_form')); ?>
            <input type="hidden" name="pg" value="<?php echo(isset($pg) ? $pg : ''); ?>">
            <?php if (isset($pg) && $pg != '') { ?>
                <input type="hidden" name="relation_type" value="<?php echo $proposal->rel_type; ?>">
                <input type="hidden" name="relation_id" value="<?php echo $proposal->rel_id; ?>">
            <?php } ?>
            <div class="col-md-12 widget-holder">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($rel_id) && $rel_id != "") { ?>
                        <a href="<?php echo admin_url($pllink); ?>"><?php echo $plttl; ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url($pllink . '/dashboard/' . $rel_id); ?>"><?php echo(isset($rel_content) ? $rel_content->name : ""); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php if (isset($parent_id) && $parent_id > 0) { ?>
                            <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>

                    <?php } else { ?>
                        <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } ?>
                    <a href="<?php echo admin_url('proposaltemplates' . $rel_link); ?>"><?php echo _l('proposals'); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($proposal)) { ?>
                        <span><?php echo $proposal->name; ?></span>
                    <?php } else { ?>
                        <span>New Proposal</span>
                    <?php } ?>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-file-text-o "></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="col-sm-12 mbot5"><?php $this->load->view('admin/proposaltemplates/proposal_bullets', $bullets); ?></div>
                <div class="clearfix"></div>
                <div class="widget-bg ">
                    <div class="widget-body clearfix">
                        <?php
                        if (!isset($_GET['preview'])) {
                            ?>

                            <?php if (!isset($proposal)) { ?>
                                <div class="topButton pull-left">
                                    <select class="proposal_template_picker selectpicker"
                                            onchange="use_proposal_template(this,'<?php echo $rel_link ?>')">
                                        <option value="">USE TEMPLATE</option>
                                        <?php foreach ($proposal_templates as $proposal_template) { ?>
                                            <option value="<?php echo $proposal_template['templateid'] ?>"><?php echo $proposal_template['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } ?>

                            <div class="topButton">

                                <input id="templateid" type="hidden" name="proposaltemplateid"
                                       value="<?php echo isset($proposal) ? $proposal->templateid : ''; ?>">
                                <?php if (isset($proposal) && $proposal->templateid > 0) { ?>
                                    <a href="<?php echo site_url() ?>proposal/view/<?php echo $proposal->templateid . $rel_link ?>"
                                       name="preview" class="preview btn btn-info"><i
                                                class="fa fa-eye"></i> <?php echo _l('preview'); ?></a>
                                <?php } ?>
                                <?php if (isset($pg) && $pg != '') { ?>
                                    <button class="btn btn-default" type="button"
                                            onclick="location.href='<?php echo admin_url('calendar'); ?>';return false;"><?php echo _l('Cancel'); ?></button>
                                <?php } else { ?>
                                    <button class="btn btn-default" type="button"
                                            onclick="location.href='<?php echo base_url(); ?>admin/proposaltemplates<?php echo $rel_link; ?>';return false;"><?php echo _l('Cancel'); ?></button>
                                <?php } ?>
                                <!--<button type="submit"
                                        class="proposal_save btn btn-info proposal-form-submit"><?php /*echo _l('submit'); */ ?></button>-->
                                <?php if (isset($_GET['lid']) || isset($_GET['pid'])) { ?>
                                    <?php if (!isset($proposal) || (isset($proposal) && $proposal->is_template == 0)) { ?>
                                        <input type="submit" name="save_as_template"
                                               value="<?php echo _l('save_as_temp'); ?>"
                                               class="proposal_save btn btn-info"><?php } ?>
                                <?php } ?>
                                <input type="submit" name="save_and_preview"
                                       value="<?php echo _l('save_and_preview'); ?>"
                                       class="proposal_save btn btn-info">
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="editPro-block panel_s btmbrd">
                            <div class="clearfix"></div>
                            <?php $attrs = (isset($proposal) ? array() : array('autofocus' => true)); ?>
                            <?php $value = (isset($proposal) ? $proposal->name : 'Proposal'); ?>

                            <a href="javascript:void(0)" class="pull-right" id="add_group" data-toggle="modal"
                               data-target="#add_name_popup"><i class="fa fa-cogs"></i></a>
                            <h2 class="proposal_tittle text-center"><?php echo ucfirst($value) ?></h2>
                            <input type="hidden" id="proposal_name" name="name" class="form-control"
                                   value="<?php echo $value ?>">
                            <input type="hidden" id="rel_type" name="rel_type" class="form-control"
                                   value="<?php echo $rel_type; ?>">
                            <input type="hidden" id="rel_id" name="rel_id" class="form-control"
                                   value="<?php echo $rel_id; ?>">
                            <?php $this->load->view('admin/proposaltemplates/proposal_banner'); ?>
                            <?php $this->load->view('admin/proposaltemplates/introduction'); ?>
                        </div>
                        <?php $this->load->view('admin/proposaltemplates/proposal_quotes', $pquotes); ?>
                        <?php
                        $this->load->view('admin/proposaltemplates/payment_schedule');
                        ?>
                        <?php
                        $this->load->view('admin/proposaltemplates/agreement');
                        ?>
                        <?php
                        $this->load->view('admin/proposaltemplates/clent_message');
                        ?>
                        <?php
                        $this->load->view('admin/proposaltemplates/gallery', $pgallery);
                        ?>
                        <?php
                        $this->load->view('admin/proposaltemplates/files');
                        ?>
                        <?php
                        $this->load->view('admin/proposaltemplates/signatures');
                        ?>
                        <!-- /.tabs -->
                        <div class="topButton">

                            <input type="hidden" name="proposaltemplateid"
                                   value="<?php echo isset($proposal) ? $proposal->templateid : ''; ?>">
                            <?php if (isset($proposal) && $proposal->templateid > 0) { ?>
                                <a href="<?php echo site_url() ?>proposal/view/<?php echo $proposal->templateid . $rel_link ?>"
                                   name="preview" class="preview btn btn-info"><i
                                            class="fa fa-eye"></i> <?php echo _l('preview'); ?></a>
                            <?php } ?>
                            <?php if (isset($pg) && $pg != '') { ?>
                                <button class="btn btn-default" type="button"
                                        onclick="location.href='<?php echo admin_url('calendar'); ?>';return false;"><?php echo _l('Cancel'); ?></button>
                            <?php } else { ?>
                                <button class="btn btn-default" type="button"
                                        onclick="location.href='<?php echo base_url(); ?>admin/proposaltemplates<?php echo $rel_link; ?>';return false;"><?php echo _l('Cancel'); ?></button>
                            <?php } ?>
                            <?php if (isset($_GET['lid']) || isset($_GET['pid'])) { ?>
                                <?php if (!isset($proposal) || (isset($proposal) && $proposal->is_template == 0)) { ?>
                                    <input type="submit" name="save_as_template"
                                           value="<?php echo _l('save_as_temp'); ?>"
                                           class="proposal_save btn btn-info" /><?php } ?>
                            <?php } ?>
                            <input type="submit" name="save_and_preview" value="<?php echo _l('save_and_preview'); ?>"
                                   class="proposal_save btn btn-info"/>
                        </div>
                    </div>
                    <!-- /.widget-body -->
                </div>
                <!-- /.widget-bg -->
            </div>
            <input type="hidden" name="save_and_preview" value="<?php echo _l('save_and_preview'); ?>" id="savetype">
            <?php echo form_close(); ?>
            <?php
            $rec_payment = isset($rec_payment) ? $rec_payment : array();
            $this->load->view('admin/proposaltemplates/recurring_payment', $rec_payment); ?>
        </div>
    </div>
</div>

<!--
  * Added by: Masud
  * Date: 02-08-2018
  * Popup to display column setting option
  -->
<div class="modal fade" id="add_name_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('Proposal Name'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="group_popup">
                        <div class="group_name">
                            <div class="form-group">
                                <label class="control-label">Proposal Name
                                    <small class="req text-danger">*</small>
                                </label>
                                <input id="proposalname" type="text" class="form-control proposal_name"
                                       value="<?php echo $value = (isset($proposal) ? $proposal->name : 'Proposal'); ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info" id="save_name"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add_group_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('add_group'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="group_popup">
                        <div class="group_name">
                            <div class="form-group">
                                <label class="control-label">Group Name
                                    <small class="req text-danger">*</small>
                                </label>
                                <input type="text" name="group_name" class="form-control gname"/>
                            </div>
                        </div>
                        <div class="group_type">
                            <div class="form-group">
                                <label class="control-label">Group Type
                                    <small class="req text-danger">*</small>
                                </label>
                                <select class="form-control gtype selectpicker" name="group_type">
                                    <option value="">Select group type</option>
                                    <option value="0">Pre-Selected</option>
                                    <option value="1">Select One</option>
                                    <option value="2">Select Any</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info" id="group_save"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>

<!----- New signer form-->

<div class="modal fade" id="add_new_signer_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('add_new_signer'); ?>
                </h4>
            </div>
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#addnew">Add Contact</a></li>
                <li><a data-toggle="tab" href="#existing">Choose Existing</a></li>
            </ul>
            <div class="tab-content">
                <div id="addnew" class="tab-pane fade in active">
                    <form id="new_signer_form" method="post">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?php $attrs = (isset($addressbook) ? array() : array('autofocus' => true)); ?>
                                    <?php echo render_input('firstname', '<small class="req text-danger">* </small>First Name', '', 'text', $attrs); ?>
                                </div>
                                <div class="col-sm-6">
                                    <?php echo render_input('lastname', '<small class="req text-danger">* </small>Last Name', '', 'text'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <?php echo render_input('email', '<small class="req text-danger">* </small>Email', '', 'email', array('autocomplete' => 'off')); ?>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="tags" class="control-label">Tags
                                            <small class="req text-danger">*</small>
                                        </label>
                                        <select name="tags[]" id="tags[]" class="form-control selectpicker"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                data-live-search="true" multiple>
                                            <?php
                                            foreach ($tags as $tag) {
                                                $tselected = '';
                                                if (in_array($tag['id'], $addressbook->tags_id)) {
                                                    $tselected = "selected='selected'";
                                                }
                                                echo '<option value="' . $tag['id'] . '" ' . $tselected . '>' . $tag['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <?php
                                        if (isset($proposal) && $proposal->rel_type != "") {
                                            $rel_type = $proposal->rel_type;
                                            $rel_id = $proposal->rel_id;
                                        } elseif ((isset($_GET['pid']) && $_GET['pid'] > 0) || (isset($_GET['lid']) && $_GET['lid'] > 0)) {
                                            if (isset($_GET['pid'])) {
                                                $rel_type = 'project';
                                                $rel_id = $_GET['pid'];
                                            } else {
                                                $rel_type = 'lead';
                                                $rel_id = $_GET['lid'];
                                            }
                                        } else {
                                            $rel_type = '';
                                            $rel_id = '';
                                        }
                                        if (!empty($rel_type) && $rel_id > 0) {
                                            ?>
                                            <input type="hidden" name="rel_type" value="<?php echo $rel_type ?>">
                                            <input type="hidden" name="<?php echo $rel_type ?>"
                                                   value="<?php echo $rel_id ?>">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-info save_signer"><?php echo _l('submit'); ?></button>
                        </div>
                    </form>
                </div>
                <div id="existing" class="tab-pane fade in">
                    <form id="existing_signer_form" method="post">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="tags" class="control-label">Choose from existing
                                            <small class="req text-danger">*</small>
                                        </label>
                                        <select name="contactid" id="contactid"
                                                class="form-control selectpicker"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                data-live-search="true">
                                            <?php
                                            echo "<pre>";
                                            print_r($addressbooks);
                                            foreach ($addressbooks as $addressbook) {
                                                echo '<option value="' . $addressbook['addressbookid'] . '" data-subtext="' . $addressbook['email'] . '">' . $addressbook['firstname']." ".$addressbook['lastname'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <?php
                                        if (isset($proposal) && $proposal->rel_type != "") {
                                            $rel_type = $proposal->rel_type;
                                            $rel_id = $proposal->rel_id;
                                        } elseif ((isset($_GET['pid']) && $_GET['pid'] > 0) || (isset($_GET['lid']) && $_GET['lid'] > 0)) {
                                            if (isset($_GET['pid'])) {
                                                $rel_type = 'projectid';
                                                $rel_id = $_GET['pid'];
                                            } else {
                                                $rel_type = 'leadid';
                                                $rel_id = $_GET['lid'];
                                            }
                                        } else {
                                            $rel_type = '';
                                            $rel_id = '';
                                        }
                                        if (!empty($rel_type) && $rel_id > 0) {
                                            ?>
                                            <!--<input type="hidden" name="rel_type" value="<?php /*echo $rel_type */ ?>">-->
                                            <input type="hidden" name="<?php echo $rel_type ?>"
                                                   value="<?php echo $rel_id ?>">
                                            <input type="hidden" name="brandid" value="<?php echo get_user_session(); ?>">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-info save_signer"><?php echo _l('submit'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--- End------->

<div id="top" class="hidden fixed"><i class="fa fa-angle-up"></i></div>
<?php init_tail(); ?>
<script>
    var validator = $("#proposal-form").validate({
        rules: {
            name: {required: true},
            pmt_sdl_name: {
                required: true,
                remote: {
                    url: admin_url + "paymentschedules/check_paymentschedule_name_exists",
                    type: 'post',
                    data: {
                        tagid: <?php echo isset($proposal) ? $proposal->ps_template : 0; ?>
                    },
                }
            }
        },

    });
    $("#new_signer_form").validate({
        rules: {
            firstname: {required: true},
            lastname: {required: true},
            'tags[]': {required: true},
            email: {
                required: true,
                remote: {
                    url: admin_url + "misc/addressbook_email_exists",
                    type: 'post',
                    data: {
                        email: function () {
                            return $('#email').val();
                        }
                    },
                },
            }
        },
        messages: {
            email: {
                remote: 'Email already exist.',
            }
        }
    });
    $("#existing_signer_form").validate({
        rules: {
            addressbookid: {required: true},
        },
    });
    $(function () {
        _validate_form($('.proposal-form'), {name: 'required'});

        /*_validate_form($('#new_signer_form'), {
            firstname: 'required',
            lastname: 'required',
            email: 'required',
            'tags[]': 'required'
        });*/
    });
</script>


<!---- Manual Item start --->

<div class="modal fade" id="add_manual_item_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('add product & service'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php $this->load->view('admin/proposaltemplates/manualitem'); ?>
            </div>
            <div class="modal-footer">
                <!--<a href="javascript:void(0)" class="btn btn-info group_save" id="edit_group_save"
                   data-gid="<?php /*echo $gid; */ ?>">
                    <?php /*echo _l('submit'); */ ?>
                </a>-->
            </div>
        </div>
    </div>
</div>

<!---- Manual Item end --->
</body>
</html>