<?php
$class = "";
if ($count <= 3) {
    $class = "first_row";
}

if($form['updatedby']=="" && $form['updateddate']==""){
    $created=_l("created");
    $user = staff_profile_image($form['createdby']);
    $date = strtoupper(date('D, M j, Y',strtotime($form['createddate'])));
}else{
    $created=_l("updated");
    $user = staff_profile_image($form['updatedby']);
    $date = strtoupper(date('D, M j, Y',strtotime($form['updateddate'])));
}

?>
    <li data-form-id="<?php echo $form['id']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card form_card_blk <?php echo $class ?>">
        <div class="panel-body card-body">
            <div class="row">

                <div class="col-xs-11 card-name">
                    <div class="carddate-block">
                        <i class="fa fa-list-ul fa-2x"></i>
                    </div>
                    <div class="forminnerdetails">
                    <div class="formTitle display-block">
                        <a href="<?php echo admin_url('leadcaptureforms/form/' . $form['id']); ?>">
                            <?php echo ucwords($form['name']); ?>
                        </a>    
                    </div>
                    <?php echo "<div class='ceratedupdated'><div style='vertical-align: top' class='user inline-block mright10'>".$user."</div><div class='inline-block'><b>".strtoupper($created)."</b><br />".$date."</div></div>" ?>
                    </div>
                </div>
                <div class="col-xs-1 text-muted">
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                        if (has_permission('leads', '', 'edit')) {
                            $options .= '<li><a href=' . admin_url() . 'leadcaptureforms/form/' . $form['id'] . ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                        } else {
                            $options .= "";
                        }

                        if (has_permission('leads', '', 'delete')) {
                            $options .= '<li><a href=' . admin_url() . 'leadcaptureforms/delete/' . $form['id'] . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                        }
                        $options .= "</ul></div>";
                        echo $options;
                        ?></div>
                    <div class="checkbox"><input type="checkbox" value="<?php echo $form['id'] ?>"><label></label></div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">
                    <?php echo "<div class='fomdisplaymethods'><select id='formmethods' class='methods selectpicker'>
                            <option value=''>" . _l('choosemethods') . "</option>
                            <option value=''>" . _l('linktoform') . "</option>
                            <option value=''>" . _l('linktodialogwindow') . "</option>
                            <option value=''>" . _l('insertonwebpage') . "</option>
                            <option value=''>" . _l('facebooklink') . "</option>
                        </select></div>"; ?>
                </div>
            </div>
        </div>
    </li>
<?php // } ?>