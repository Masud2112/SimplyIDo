<?php
/**/

$removed_sections = array();
if (isset($proposal)) {
    $sections = json_decode($proposal->sections, true);
    $section = $sections['quote'];
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('quote', $removed_sections) ? "removed_section" : "";
    $checked = in_array('quote', $removed_sections) ? "checked" : "";
}
/**/
?>
<div id="quote" class="quotes <?php echo $class ?> editQuotesProposal_blk">
    <div class="row">
        <div class="col-sm-6">
            <h4 id="quote_page_name"><i
                        class="fa fa-list-ul"></i><span><?php echo isset($section) ? $section['name'] : "Quote"; ?></span>
            </h4>
            <input type="hidden" name="sections[quote][name]" class="quote_page_name"
                   value="<?php echo isset($section) ? $section['name'] : "Quote"; ?>">
        </div>

        <div class="col-sm-6 col-right">
            <?php if (!isset($quotes) || count($quotes) == 0) { ?>
                <a href="#" class="btn btn-info inline-block add_group_top" id="add_group" data-toggle="modal"
                   data-target="#add_group_popup"><i class="fa fa-plus-square"></i> ADD GROUP</a>
            <?php } ?>
            <div class="show-options">
                <a class='show_act' href='javascript:void(0)'>
                    <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                </a>
            </div>
            <div class='table_actions'>
                <ul>
                    <li>
                        <a href='javascript:void(0)' class="" id="edit_page" data-toggle="modal"
                           data-target="#edit_quote_popup">
                            <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="checkbox">
                <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]" id="remove_quote"
                       data-pid="#quote" value="quote" <?php echo $checked ?>/>
                <label for="remove_quote"><?php echo "Remove"; ?></label></div>

        </div>
    </div>
    <div class="section_body">
        <div class="clearfix clear_with_groups"></div>
        <div class="quote_groups sortable">
            <?php
            $data['packages'] = $items_groups;
            $data['items'] = $items;
            $selected_items = array();
            if (isset($quotes) && count($quotes) > 0) {
                $si = array();
                foreach ($quotes as $gid => $quote) {
                    $quote_items = json_decode($quote['quote_items'], true);
                    if (!empty($quote_items)) {
                        foreach ($quote_items as $quote_item) {
                            $si[] = strtolower($quote_item['type']) . "_" . $quote_item['id'];
                        }
                    }
                }
                foreach ($quotes as $gid => $quote) {
                    $quote_items = json_decode($quote['quote_items'], true);
                    //array_push($data['selected_items'],$quote_items);
                    $quote['gid'] = $gid;
                    $data['quote'] = $quote;
                    $data['selected_items'] = $si;
                    $this->load->view('admin/proposaltemplates/quote_group', $data);
                }
            }
            ?>
        </div>
        <?php
        $gbclass="hide";
        if (isset($quotes) && count($quotes) > 0) {
            $gbclass = "";
        } ?>
        <div class="text-right mbot10 <?php echo $gbclass; ?> add_group_bottom"><a href="#" class="btn btn-info inline-block"
                                                                id="add_group" data-toggle="modal"
                                                                data-target="#add_group_popup"><i
                        class="fa fa-plus-square"></i> ADD GROUP</a></div>
        <div class="show_mark_disc">
            <div class="checkbox">
                <input name="discounts" type="checkbox" id="disc_client" class="checkbox form-control"
                       value="1" <?php echo isset($proposal) && $proposal->discounts == 1 ? "checked" : "" ?> >
                <label for="disc_client"><?php echo _l('show_disc_client') ?></label>
            </div>
            <div class="checkbox">
                <input name='markups' type="checkbox" id="markup_client" class="checkbox form-control"
                       value="1" <?php echo isset($proposal) && $proposal->markups == 1 ? "checked" : "" ?> >
                <label for="markup_client"><?php echo _l('show_markup_client') ?></label>
            </div>
        </div>
        <div class="pull-right">
            <div class="standard_tax">
                <label>Tax*</label>
                <select id="proposal_custom_tax" name="proposal_custom_tax" class="selectpicker">
                    <?php foreach ($taxes as $tax) { ?>
                        <option data-rate="<?php echo $tax['taxrate'] ?> "
                                value="<?php echo $tax['id'] ?>" <?php echo isset($proposal) && $proposal->proposal_custom_tax == $tax['id'] ? "selected" : "" ?>><?php echo $tax['name'] ?>
                            (<?php echo $tax['taxrate'] ?> % )
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="clearfix"></div>
            <div class="othersection mtop15 display-block">
                <div class="form-group inline-block">
                    <select name="othrdisctype" class="othrdisctype selectpicker">
                        <option value="">Other Discount</option>
                        <option value="percentage" <?php echo isset($proposal->othrdisctype) && $proposal->othrdisctype == "percentage" ? "selected" : '' ?>>
                            Percentage
                        </option>
                        <option value="amount" <?php echo isset($proposal->othrdisctype) && $proposal->othrdisctype == "amount" ? "selected" : '' ?>>
                            Fixed Amount
                        </option>
                    </select>
                </div>
                <div class="form-group inline-block">
                    <div class="input-group">
                        <span class="input-group-addon othrdisc_prefix" id="basic-addon2">$</span>
                        <input name="othrdiscval" class="othrdiscval danger form-control"
                               value="<?php echo isset($proposal) ? $proposal->othrdiscval : '' ?>">
                        <span class="input-group-addon othrdisc_suffix" id="basic-addon1" style="display: none">%</span>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="othersection mtop15 display-block">
                <div class="form-group inline-block">
                    <input name="other" class="other text-left form-control"
                           value="<?php echo isset($proposal) ? $proposal->other : '' ?>" placeholder="Other (specify)">
                </div>
                <div class="form-group inline-block">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">$</span>
                        <input type="text" class="form-control" name="otherval"
                               value="<?php echo isset($proposal) ? $proposal->otherval : '' ?>">
                    </div>
                </div>
            </div>
            <div class="checkbox">
                <input name='gratuity' type="checkbox" id="gratuity" class="checkbox form-control"
                       value="1" <?php echo isset($proposal) && $proposal->gratuity == 1 ? "checked" : "" ?> >
                <label for="gratuity"><?php echo _l('allow_gratuity_client') ?></label>
            </div>
        </div>

    </div>
</div>
</div>
<!--
  * Added by: Masud
  * Date: 02-08-2018
  * Popup to display column setting option
  -->

<div class="modal fade" id="edit_quote_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                       value="<?php echo isset($section) ? strtoupper($section['name']) : "QUOTE"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="quote_page_name"
                   data-id="#edit_quote_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>