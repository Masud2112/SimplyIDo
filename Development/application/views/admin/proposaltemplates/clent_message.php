<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 28-02-2018
 * Time: 16:38
 */

$removed_sections = array();
if (isset($proposal)) {
    $sections = json_decode($proposal->sections, true);
    $section = $sections['message'];
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('message', $removed_sections) ? "removed_section" : "";
    $checked = in_array('message', $removed_sections) ? "checked" : "";
}

$note = get_brand_option('predefined_clientnote_invoice');
$terms = get_brand_option('predefined_terms_invoice');
$invoive_message="";
if(!empty($note)){
    $invoive_message="<b>"._l('note').":</b><br />";
    $invoive_message.="<p>".$note."</p> <br /><br />";
}
if(!empty($terms)){
    $invoive_message.="<b>"._l('terms_and_conditions').":</b><br />";
    $invoive_message.="<p>".$terms."</p> <br /><br />";
}

?>
<div id="message" class="<?php echo $class ?>">
    <div class="row">
        <div class="files_header col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h4 id="message_page_name"><i
                                class="fa fa-user mright10"></i><b><span><?php echo isset($section) ? $section['name'] : _l('invoice_message'); ?></span></b>
                    </h4>
                    <input type="hidden" name="sections[message][name]" class="message_page_name"
                           value="<?php echo isset($section) ? $section['name'] : _l('invoice_message'); ?>">
                </div>
                <div class="col-sm-6 col-right">

                    <!--<a href="#" class="btn btn-default inline-block" id="add_agreement" data-toggle="modal" data-target="#add_media_popup"><i class="fa fa-plus-square"></i> USE TEMPLATE</a>-->
                    <div class="show-options">
                        <a class='show_act' href='javascript:void(0)'>
                            <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                        </a>
                    </div>
                    <div class='table_actions'>
                        <ul>
                            <li>
                                <a href='javascript:void(0)' class="" id="edit_page" data-toggle="modal"
                                   data-target="#edit_message_popup">
                                    <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]"
                               id="remove_message" data-pid="#message" value="message" <?php echo $checked ?>/>
                        <label for="remove_message"><?php echo "Remove"; ?></label>
                    </div>
                </div>
            </div>
            <div class="section_body">
                <div class="message_form">
                    <?php /*if (isset($agreements) && count($agreements) > 0 ){*/ ?><!--
                        <select id = "agreement_picker" class="agreement_load selectpicker">
                            <?php /*foreach ($agreements as $agreement){ */ ?>
                                <option value="<?php /*echo $agreement['templateid'] */ ?>"><?php /*echo $agreement['name'] */ ?></option>
                            <?php /*} */ ?>
                        </select>
                    --><?php /*} */ ?>
                    <?php if (isset($_GET['preview']) && $_GET['preview'] == true) { ?>
                        <div class="message_txt">
                            <?php echo isset($proposal) ? $proposal->client_message : "" ?>
                        </div>
                    <?php } else { ?>
                        <?php echo render_textarea('client_message', '', isset($proposal) && !empty($proposal->client_message) ? $proposal->client_message : $invoive_message, array(), array(), ''); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="edit_message_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('edit page'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="group_popup">
                        <div class="group_name">
                            <div class="form-group">
                                <label class="control-label">Page Name
                                    <small class="req text-danger">*</small>
                                </label>
                                <input type="text" name="page_name[quote]" class="form-control page_name"
                                       value="<?php echo isset($section) ? strtoupper($section['name']) : "CLIENT MESSAGE"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="message_page_name"
                   data-id="#edit_message_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>