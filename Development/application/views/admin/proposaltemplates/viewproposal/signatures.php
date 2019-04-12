<?php
/**/
$removed_sections = array();
if(isset($proposal)){
    $signatures = json_decode($proposal->signatures,true);
    $removed_sections = json_decode($proposal->removed_sections,true);
}
$class = "";
$checked = "";
if(isset($removed_sections)){
    $class = in_array('signatures',$removed_sections)?"removed_section":"";
    $checked = in_array('signatures',$removed_sections)?"checked":"";
}
$signed=0;
if(isset($proposal->feedback)){
    $signed=$proposal->feedback->total_signed;
}
$total_signed=0;
/**/
?>
<div id="signatures" class="<?php echo $class?>">
    <div class="row">
        <div class="signature_header gallery_header col-sm-12">
            <!--<div class="row mbot10">
                <div class="col-sm-6"><h4><i class="fa fa-pencil mright10"></i><b>Signatures</b></h4></div>
                <div class="col-sm-6 col-right">
                </div>
            </div>-->
            <p><?php echo _l('signature_note'); ?></p>
            <div class="section_body">
                <div class="clearfix"></div>
                <div class="signatures_list row">
                    <!--<div class="hrow"><span class="hTxt"><?php /*echo isset($signatures)? count($signatures):"0" */?> Items</span> <i class="fa fa-caret-up"></i></div>-->
                    <div class="row rowWrap">
                        <?php if (isset($signatures) && count($signatures) > 0 && !empty($signatures)){
                            $total_signer = count($signatures);
                            foreach ($signatures as $key => $signer) {
                                if(!empty($signer['image'])){
                                    $total_signed++;
                                }
                                $signer['disabled']="active";
                                if($signer['counter_signer']==1){
                                    $signer['disabled']="disabled";
                                    if(($total_signer-1)==$signed){
                                        $signer['disabled']="active";
                                    }
                                }
                                $signer['id']=$key;
                            ?>
                                <?php $this->load->view('admin/proposaltemplates/viewproposal/single_signature',$signer); ?>
                        <?php } } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="total_signed" value="<?php echo $signed ;?>" class="total_signed">