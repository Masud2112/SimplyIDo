<?php init_head(); ?>
<div id="wrapper">
    <div class="content new-team-page">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(),array('class'=>'team-form')); ?>

            <div class="col-md-12">
                <h1 class="pageTitleH1"><i class="fa fa-group"></i><?php echo $title; ?></h1>
                <div class="pull-right">
                    <div class="breadcrumb mb0">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('teams'); ?>">Teams</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <span><?php echo $value = (isset($team) ? $team->name : 'New Team'); ?></span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">

                        <?php $attrs = (isset($team) ? array() : array('autofocus'=>true)); ?>
                        <?php $value = (isset($team) ? $team->name : ''); ?>
                        <?php echo render_input('name','team_add_edit_name',$value,'text',$attrs); ?>
                        <?php
                        foreach($roles as $role){
                            if(isset($team)){
                                if($team->role == $role['roleid']){
                                    $selected = $role['roleid'];
                                }
                            } else {
                                $selected = "";
                            }
                        }

                        ?>
                        <!-- <?php echo render_select('role',$roles,array('roleid','name'),'team_add_edit_role',$selected); ?>
                    <div class="table-responsive">
                        <table class="table table-bordered roles no-margin">
                           <thead>
                              <tr>
                                 <th class="bold"><?php echo _l('permission'); ?></th>
                                 <th class="text-center bold"><?php echo _l('permission_view'); ?> (<?php echo _l('permission_global'); ?>)</th>
                                 <th class="text-center bold"><?php echo _l('permission_view_own'); ?></th>
                                 <th class="text-center bold"><?php echo _l('permission_create'); ?></th>
                                 <th class="text-center bold"><?php echo _l('permission_edit'); ?></th>
                                 <th class="text-center text-danger bold"><?php echo _l('permission_delete'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                        if(isset($team)){
                            $is_admin = is_admin($team->teamid);
                        }
                        $conditions = get_permission_conditions();
                        foreach($permissions as $permission){
                            $permission_condition = $conditions[$permission['shortname']];
                            ?>
                              <tr data-id="<?php echo $permission['permissionid']; ?>">
                                 <td>
                                    <?php
                            ?>
                                    <?php echo $permission['name']; ?>
                                 </td>
                                 <td class="text-center">
                                    <?php if($permission_condition['view'] == true){
                                $statement = '';
                                if(isset($is_admin) && $is_admin || isset($team) && team_has_permission($permission['shortname'],$team->teamid,'view_own')){
                                    $statement = 'disabled';
                                } else if(isset($team) && team_has_permission($permission['shortname'],$team->teamid,'view')){
                                    $statement = 'checked';
                                }
                                ?>
                                    <?php
                                if(isset($permission_condition['help'])){
                                    echo '<i class="fa fa-question-circle text-danger" data-toggle="tooltip" data-title="'.$permission_condition['help'].'"></i>';
                                }
                                ?>
                                    <div class="checkbox">
                                       <input type="checkbox" data-can-view <?php echo $statement; ?> name="view[]" value="<?php echo $permission['permissionid']; ?>">
                                       <label></label>
                                    </div>
                                    <?php } ?>
                                 </td>
                                 <td class="text-center">
                                    <?php if($permission_condition['view_own'] == true){
                                $statement = '';
                                if(isset($is_admin) && $is_admin || isset($team) && team_has_permission($permission['shortname'],$team->teamid,'view')){
                                    $statement = 'disabled';
                                } else if(isset($team) && team_has_permission($permission['shortname'],$team->teamid,'view_own')){
                                    $statement = 'checked';
                                }
                                ?>
                                    <div class="checkbox">
                                       <input type="checkbox" <?php echo $statement; ?> data-shortname="<?php echo $permission['shortname']; ?>" data-can-view-own name="view_own[]" value="<?php echo $permission['permissionid']; ?>">
                                       <label></label>
                                    </div>
                                    <?php } else if($permission['shortname'] == 'customers'){
                                echo '<i class="fa fa-question-circle mtop15" data-toggle="tooltip" data-title="'._l('permission_customers_based_on_admins').'"></i>';
                            } else if($permission['shortname'] == 'projects'){
                                echo '<i class="fa fa-question-circle mtop25" data-toggle="tooltip" data-title="'._l('permission_projects_based_on_assignee').'"></i>';
                            } else if($permission['shortname'] == 'tasks'){
                                echo '<i class="fa fa-question-circle mtop25" data-toggle="tooltip" data-title="'._l('permission_tasks_based_on_assignee').'"></i>';
                            } else if($permission['shortname'] == 'payments'){
                                echo '<i class="fa fa-question-circle mtop15" data-toggle="tooltip" data-title="'._l('permission_payments_based_on_invoices').'"></i>';
                            } ?>
                                 </td>
                                 <td  class="text-center">
                                    <?php if($permission_condition['create'] == true){
                                $statement = '';
                                if(isset($is_admin) && $is_admin){
                                    $statement = 'disabled';
                                } else if(isset($team) && team_has_permission($permission['shortname'],$team->teamid,'create')){
                                    $statement = 'checked';
                                }
                                ?>
                                    <div class="checkbox">
                                       <input type="checkbox" data-shortname="<?php echo $permission['shortname']; ?>" data-can-create <?php echo $statement; ?> name="create[]" value="<?php echo $permission['permissionid']; ?>">
                                       <label></label>
                                    </div>
                                    <?php } ?>
                                 </td>
                                 <td  class="text-center">
                                    <?php if($permission_condition['edit'] == true){
                                $statement = '';
                                if(isset($is_admin) && $is_admin){
                                    $statement = 'disabled';
                                } else if(isset($team) && team_has_permission($permission['shortname'],$team->teamid,'edit')){
                                    $statement = 'checked';
                                }
                                ?>
                                    <div class="checkbox">
                                       <input type="checkbox" data-shortname="<?php echo $permission['shortname']; ?>" data-can-edit <?php echo $statement; ?> name="edit[]" value="<?php echo $permission['permissionid']; ?>">
                                       <label></label>
                                    </div>
                                    <?php } ?>
                                 </td>
                                 <td  class="text-center">
                                    <?php if($permission_condition['delete'] == true){
                                $statement = '';
                                if(isset($is_admin) && $is_admin){
                                    $statement = 'disabled';
                                } else if(isset($team) && team_has_permission($permission['shortname'],$team->teamid,'delete')){
                                    $statement = 'checked';
                                }
                                ?>
                                    <div class="checkbox checkbox-danger">
                                       <input type="checkbox" data-shortname="<?php echo $permission['shortname']; ?>" data-can-delete <?php echo $statement; ?> name="delete[]" value="<?php echo $permission['permissionid']; ?>">
                                       <label></label>
                                    </div>
                                    <?php } ?>
                                 </td>
                              </tr>
                              <?php } ?>
                           </tbody>
                        </table>
                        </div> -->
                        <div class="topButton">
                            <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>admin/teams'"><?php echo _l( 'Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                            <?php if(isset($team)){ ?>
                                <?php if (has_permission('teams','','create')) { ?>
                                    <a href="<?php echo admin_url('teams/team'); ?>" class="btn btn-info pull-right mleft4 mbot20 display-block"><?php echo _l('new_team'); ?></a>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>

        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        //alert("here");
        // $('select[name="role"]').on('change', function() {
        //   var roleid = $(this).val();
        //   init_roles_permissions(roleid, true);
        // });
        //init_roles_permissions();
    });
</script>
<script>
    _validate_form($('.team-form'),{name:'required'});
</script>
</body>
</html>