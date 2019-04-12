<?php
/**/
$removed_sections = array();
if (isset($proposal)) {
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
$colclass="";
if (isset($removed_sections)) {
    $class = in_array('quote', $removed_sections) ? "removed_section" : "";
    $checked = in_array('quote', $removed_sections) ? "checked" : "";
}
/**/
?>
<div id="quote" class="quotes <?php echo $class ?>">
    <?php
    $this->load->view('proposaltemplates/psl_section_head', array('title' => "Quote"));
    ?>
    <div class="section_body">
        <div class="clearfix clear_with_groups"></div>
        <div class="quote_groups">
            <?php
            $data['packages'] = $items_groups;
            $data['items'] = $items;
            $selected_items = array();

            ?>
            <?php
            if (isset($quotes) && count($quotes) > 0) {
                foreach ($quotes as $gid => $quote) {
                    $quote_items = json_decode($quote['quote_items'], true);
                    if (!empty($quote_items)) {
                        foreach ($quote_items as $quote_item) {
                            $si[] = strtolower($quote_item['type']) . "_" . $quote_item['id'];
                        }
                    }
                    $quote['gid'] = $gid;
                    $data['quote'] = $quote;
                    $data['selected_items'] = isset($si) ? $si : array();
                    $this->load->view('proposaltemplates/quote_group', $data);
                }
            }

            ?>
            <div class="quote_footer">
                <div class="row">
                    <?php if ($proposal->markups == 0 && $proposal->discounts == 0){
                        $cols_class = "col-lg-8 col-md-7 col-sm-12";
                    } else {
                        $cols_class = "col-lg-7 col-md-6  col-sm-12";
                    } ?>
                    <div class="<?php echo $cols_class; ?>">
                        <div class="selected_item"></div>
                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-sm-1 col-xs-2 "><span class="proposal_orgttl"></span></div>

                    <?php if($proposal->markups==0 && $proposal->discounts==0){
                        $colclass="hide";
                    }  ?>
                        <div class="col-sm-1 col-xs-2 <?php echo $colclass; ?>  ">
                            <span class="proposal_discount"></span>
                        </div>

                    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-2 text-center"><span class="proposal_tax "></span></div>
                    <div class="col-lg-1 col-md-2 col-sm-12 col-xs-2 text-right"><span class="proposal_sbttl"></span></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="quote_note">
                    <h5><?php echo _l('notes') ?></h5>
                    <p><?php echo _l('proposal_notes_text') ?></p>
                </div>
                <div class="quote_terms_condition">
                    <h5><?php echo _l('terms_condition') ?></h5>
                    <p><?php echo _l('proposal_terms_condition') ?></p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="final_total text-right">
                    <div class="psubtotal">
                        <div class="row">
                            <div class="col-xs-7">
                                <h5><?php echo _l('subtotal') ?></h5>
                            </div>
                            <div class="col-xs-5 text-right">
                                <input class="proposal_sbttl form-control" type="hidden" name="proposal_subtotal"
                                       readonly
                                       value="<?php isset($proposal->feedback->proposal_subtotal) ? $proposal->feedback->proposal_subtotal : '' ?>">
                                <span class="proposal_sbttl"></span>
                            </div>
                        </div>
                    </div>
                    <div class="tax_discount">
                        <!--<div class="pdiscount">
                            <div class="row">
                                <div class="col-xs-7">
                                    <h5 class="mkpdiosc_label">
                                        <?php /*if ($proposal->markups == 1 && $proposal->discounts == 1) {
                                            echo _l('markup_discount');
                                        } else {
                                            if ($proposal->markups == 1) {
                                                echo _l('markup');
                                            } else {
                                                echo _l('discount');
                                            }
                                        } */ ?>
                                    </h5>
                                </div>
                                <div class="col-xs-5">
                                    <input class="proposal_discount form-control" type="hidden" name="" readonly value="<?php /*isset($proposal->feedback->proposal_tax)? $proposal->feedback->proposal_tax:'---'; */ ?>">
                                    <span class="final_discount"></span>
                                    <span class="final_markup"></span>
                                    <span class="final_mkpdisc"></span>
                                </div>
                            </div>
                        </div>-->
                        <!--<div class="ptax">
                            <div class="row">
                                <div class="col-xs-7">
                                    <h5><?php /*echo _l('tax') */ ?></h5>
                                </div>
                                <div class="col-xs-5">
                                    <input class="proposal_tax form-control" type="hidden" name="" readonly value="<?php /*isset($proposal->feedback->proposal_tax)? $proposal->feedback->proposal_tax:'---' */ ?>">
                                    <span class="proposal_tax"></span>
                                </div>
                            </div>
                        </div>-->
                        <?php
                        if (isset($proposal) && !empty($proposal->othrdisctype) && $proposal->othrdiscval < 0) {
                            $proposal->othrdiscval = str_replace('-', "", $proposal->othrdiscval)
                            ?>
                            <div class="potherdisc">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <h5><?php echo "Other discount(";
                                            echo "<span class='discpercent'>" . $proposal->othrdiscval . "</span>";
                                            echo "%";
                                            echo ")"; ?></h5>
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        <span class="proposal_othedisc"
                                              data-val="<?php echo $proposal->othrdiscval; ?>"
                                              data-valtype="<?php echo $proposal->othrdisctype; ?>">
                                            <?php
                                            echo "-$";
                                            echo "<span class='discamount'>" . $proposal->othrdiscval . "</span>";
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="discount_percent" id="discount_percent">
                            <input type="hidden" name="discount_amount" id="discount_amount">
                        <?php } ?>
                        <?php
                        if (isset($proposal) && !empty($proposal->other) && $proposal->otherval > 0) { ?>
                            <div class="pother">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <h5><?php echo $proposal->other; ?></h5>
                                    </div>
                                    <div class="col-xs-5 text-right">
                                        <!--<input class="proposal_tax form-control" type="hidden" name="" readonly value="<?php /*isset($proposal->feedback->proposal_tax)? $proposal->feedback->proposal_tax:'---' */ ?>">-->
                                        <span class="proposal_otherval psymbol"
                                              data-val="<?php echo $proposal->otherval; ?>"><?php echo $proposal->otherval; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="ptotal">
                        <div class="row">
                            <div class="col-xs-7">
                                <h5><?php echo _l('total') ?></h5>
                            </div>
                            <div class="col-xs-5 text-right">
                                <input class="proposal_ttl form-control" type="hidden" name="proposal_total" readonly
                                       value="<?php isset($proposal->proposal_total) ? $proposal->proposal_total : '' ?>">
                                <h5 class="proposal_ttl"></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>