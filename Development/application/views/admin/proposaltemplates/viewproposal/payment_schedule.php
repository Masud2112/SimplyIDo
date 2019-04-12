<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 06-03-2018
 * Time: 16:04
 */


$removed_sections = array();
if(isset($proposal)){
    $signatures = json_decode($proposal->signatures,true);
    $removed_sections = json_decode($proposal->removed_sections,true);
}
$class = "";
$checked = "";
if(isset($removed_sections)){
    $class = in_array('payments',$removed_sections)?"removed_section":"";
    $checked = in_array('payments',$removed_sections)?"checked":"";
}

/**/
?>
<div id="payments" class="<?php echo $class?>">
    <div class="row">
        <div class="files_header col-sm-12">
            <div class="row mbot10">
                <div class="col-sm-6"><h4><!--<i class="fa fa-calendar-o mright10"></i>--><b>PAYMENT SCHEDULE</b></h4></div>
                <div class="col-sm-6  col-right">
                    <!--<a href="#" class="btn btn-default inline-block" id="add_file" data-toggle="modal"
                       data-target="#add_media_popup"><i class="fa fa-plus-square"></i> ADD PAYMENT</a>-->
                </div>
            </div>
        </div>
        <div class="section_body">
            <div id="paymentschedule" class="paymentschedule-page">
                <?php if(isset($proposal) && $proposal->ps_template > 0) {
                    $pmt_sdl_template = $proposal->pmt_sdl_template;
                    $this->load->view('admin/proposaltemplates/viewproposal/paymentschedule_temp',$pmt_sdl_template);
                }else {
                    if (isset($rec_payment) && !empty($rec_payment)) {
                        $this->load->view('admin/proposaltemplates/viewproposal/rec_payment_temp', $rec_payment);
                    }
                }?>
                </div>
        </div>
    </div>
</div>
