<?php
/**/

$signatures = array();
$removed_sections = array();
if (isset($proposal)) {
    $sections = json_decode($proposal->sections, true);
    $section = $sections['signatures'];
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
$membersids=array();
$clientsids=array();
if (isset($removed_sections)) {
    $class = in_array('signatures', $removed_sections) ? "removed_section" : "";
    $checked = in_array('signatures', $removed_sections) ? "checked" : "";
}
if (empty($signatures)) {
    if (isset($staff) && !empty($staff)) {

        $signer['signer_id'] = $staff->staffid;
        $signer['name'] = $staff->firstname . " " . $staff->lastname;
        $signer['designation'] = $staff->designation;
        $signer['counter_signer'] = 0;
        $signer['signer_type'] = "member";
        $signatures[] = $signer;
        array_push($membersids, $staff->staffid);
    }
    if (isset($eclients) && !empty($eclients)) {
        foreach ($eclients as $client) {
            $signer = array();
            $signer['signer_id'] = $client['id'];
            $signer['name'] = isset($client['name']) ? $client['name'] : "";
            $signer['designation'] = "";
            $signer['counter_signer'] = 0;
            $signer['signer_type'] = "client";
            array_push($signatures, $signer);
            array_push($clientsids, $client['id']);
        }
    }
}
foreach ($signatures as $signature){
    if($signature['signer_type']=='member'){
        $membersids[]=$signature['signer_id'];
    }else{
        $clientsids[]=$signature['signer_id'];
    }
}
?>
<div id="signatures" class="<?php echo $class ?>">
    <div class="row">
        <div class="signature_header gallery_header col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h4 id="signatures_page_name"><i
                                class="fa fa-pencil mright10"></i><b><span><?php echo isset($section) ? $section['name'] : "Signatures"; ?></span></b>
                    </h4>
                    <input type="hidden" name="sections[signatures][name]" class="signatures_page_name"
                           value="<?php echo isset($section) ? $section['name'] : "Signatures"; ?>">
                </div>
                <div class="col-sm-6 col-right">
                    <?php
                    if (!isset($_GET['preview'])) {
                        ?>
                        <a href="javascript:void(0)" class="btn btn-info inline-block" id="add_signer"><i
                                    class="fa fa-plus-square"></i> ADD SIGNER</a>

                        <div class="show-options">
                            <a class='show_act' href='javascript:void(0)'>
                                <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                            </a>
                        </div>
                        <div class='table_actions'>
                            <ul>
                                <li>
                                    <a href='javascript:void(0)' class="" id="edit_page" data-toggle="modal"
                                       data-target="#edit_signatures_popup">
                                        <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="checkbox ">
                            <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]" id="remove_signatures"
                                   data-pid="#signatures" value="signatures" <?php echo $checked ?>/>
                            <label for="remove_signatures"><?php echo "Remove"; ?></label></div>
                    <?php } ?>
                </div>
            </div>
            <div class="section_body">
                <div class="signer_form ">
                    <div class="tab-content">
                        <div id="member" class=" col-sm-6 tab-pane fade in active">
                            <div id="signer_list" class="form-group">
                                <select class="selectpicker memberpicker" name="signer">
                                    <option value="">--Select signer--</option>
                                    <?php if (isset($members) && !empty($members)) { ?>
                                        <optgroup label="Members">
                                            <?php foreach ($members as $member) { ?>
                                                <option value="<?php echo $member['staffid'] ?>"
                                                        data-id="member"
                                                        data-subtext="<?php echo $member['lastname'] ?>"
                                                        data-fname="<?php echo $member['firstname'] ?>"
                                                        data-designation="<?php echo isset($member['designation']) ? $member['designation'] : "" ?>" <?php echo in_array($member['staffid'],$membersids)?"disabled":"" ?> ><?php echo $member['firstname'] ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>

                                    <?php if (isset($clients) && !empty($clients)) { ?>
                                        <optgroup label="Clients">
                                            <?php foreach ($clients as $client) { ?>
                                                <option value="<?php echo $client['id'] ?>"
                                                        data-id="client"
                                                        data-subtext="<?php echo $client['lastname'] ?>"
                                                        data-fname="<?php echo $client['firstname'] ?>"
                                                        data-designation="<?php echo isset($client['designation']) ? $member['designation'] : "" ?>" <?php echo in_array($client['id'],$clientsids)?"disabled":"" ?> ><?php echo $client['firstname'] ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                    <?php if(isset($rel_type) && !empty($rel_type)){ ?>
                                        <option data-rtype="<?php echo $rel_type ?>"
                                                data-rid="<?php echo $rel_id ?>" value="new">Add new signer
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <input type="text" class="form-control signer_role"/>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="signer[csigner]" class="checkbox counter_signer"
                                       id="counter_signer" value="1"/>
                                <label for="counter_signer">Counter Signer</label>
                            </div>
                            <!--<a href="javascript:void(0)" class="btn btn-info" id="add_sign"
                               onclick="add_sign(this)">ADD SIGNER</a>-->
                            <a href="javascript:void(0)" class="btn btn-info" id="add_sign">ADD SIGNER</a>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="signatures_list row">
                    <div class="hrow"><span class="hTxt"><?php echo isset($signatures) ? count($signatures) : "0" ?>
                            Items</span> <a href="javascript:void(0)" class="fa fa-caret-up pull-right"></a></div>
                    <div class="row rowWrap sortable">
                        <?php if (isset($signatures) && count($signatures) > 0 && !empty($signatures)) {
                            $signatures = array_values($signatures);
                            foreach ($signatures as $key => $signer) {
                                $signer['id'] = $key;
                                ?>
                                <?php $this->load->view('admin/proposaltemplates/single_signature', $signer); ?>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_signatures_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                <input type="text" class="form-control page_name"
                                       value="<?php echo isset($section) ? strtoupper($section['name']) : "SIGNATURES"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="signatures_page_name"
                   data-id="#edit_signatures_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>
