<?php init_head(); ?>
<div id="wrapper">
    <div class="content roles-page">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(),array('class'=>'roles-form')); ?>

            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('roles'); ?>">Roles</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span><?php echo isset($role) ? $role->name :"New Role"?></span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-tasks"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">

                        <div class="clearfix"></div>
                        <?php if(isset($role)){ ?>
                            <?php if(total_rows('tblstaff',array('role'=>$role->roleid)) > 0){ ?>
                                <div class="alert alert-warning bold">
                                    <?php echo _l('change_role_permission_warning'); ?>
                                    <div class="checkbox">
                                        <input type="checkbox" name="update_staff_permissions" id="update_staff_permissions">
                                        <label for="update_staff_permissions"><?php echo _l('role_update_staff_permissions'); ?></label>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php $attrs = (isset($role) ? array() : array('autofocus'=>true)); ?>
                        <?php $value = (isset($role) ? $role->name : ''); ?>
                        <?php echo render_input('name','role_add_edit_name',$value,'text',$attrs); ?>
                        <div class="table-responsive">
                            <table class="table sdtheme table-bordered">
                                <thead>
                                <tr>
                                    <th class="bold"><?php echo _l('permission'); ?></th>
                                    <th class="text-center bold"><?php echo _l('select_all'); ?></th>
                                    <th class="text-center bold"><?php echo _l('permission_view'); ?></th>
                                    <th class="text-center bold hidden"><?php echo _l('permission_view_own'); ?></th>
                                    <th class="text-center bold"><?php echo _l('permission_create'); ?></th>
                                    <th class="text-center bold"><?php echo _l('permission_edit'); ?></th>
                                    <th class="text-center text-danger bold"><?php echo _l('permission_delete'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $conditions = get_permission_conditions();
                                foreach($permissions as $permission){
                                    $permission_condition = $conditions[$permission['shortname']];
                                    ?>
                                    <tr id="<?php echo $permission['permissionid']; ?>">
                                        <td>
                                            <?php echo $permission['name']; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="checkbox">
                                                <?php
                                                $cnt = 0;
                                                if($permission_condition['delete'] == true){
                                                    $statement = '';
                                                    if(isset($role)){
                                                        if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_delete'=>1)) > 0){
                                                            $cnt++;
                                                        }
                                                    }
                                                }

                                                if($permission_condition['view'] == true){
                                                    $statement = '';
                                                    if(isset($role)){
                                                        if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view'=>1)) > 0){
                                                            $cnt++;
                                                        }
                                                    }
                                                }

                                                if($permission_condition['view_own'] == true){
                                                    $statement = '';
                                                    if(isset($role)){
                                                        if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view_own'=>1)) > 0){
                                                            $cnt++;
                                                        }
                                                    }
                                                }

                                                if($permission_condition['create'] == true){
                                                    $statement = '';
                                                    if(isset($role)){
                                                        if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_create'=>1)) > 0){
                                                            $cnt++;
                                                        }
                                                    }
                                                }

                                                if($permission_condition['edit'] == true){
                                                    $statement = '';
                                                    if(isset($role)){
                                                        if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_edit'=>1)) > 0){
                                                            $cnt++;
                                                        }
                                                    }
                                                }

                                                if($cnt >= 4) {
                                                    $statement = 'checked';
                                                }
                                                ?>
                                                <input type="checkbox" class="selectAll" id="all_<?php echo $permission['permissionid']; ?>" <?php echo $statement; ?>>
                                                <label></label>
                                                <input type="hidden" name="checkedsizes[]" id="checkedsizes_<?php echo $permission['permissionid']; ?>" value="<?php echo $cnt; ?>">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission_condition['view'] == true){
                                                $statement = '';
                                                if(isset($role)){
                                                    if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view'=>1)) > 0){
                                                        $statement = 'checked';
                                                    }

                                                    // if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view_own'=>1)) > 0){
                                                    //     $statement = 'disabled';
                                                    // }
                                                }
                                                ?>
                                                <?php if(isset($permission_condition['help'])){
                                                    echo '<i class="fa fa-question-circle text-danger" data-toggle="tooltip" data-title="'.$permission_condition['help'].'"></i>';
                                                }
                                                ?>
                                                <div class="checkbox">
                                                    <input type="checkbox" class="viewAll" data-can-view <?php echo $statement; ?> name="view[]" id="view_<?php echo $permission['permissionid']; ?>" value="<?php echo $permission['permissionid']; ?>">
                                                    <label></label>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center hidden">
                                            <?php if($permission_condition['view_own'] == true){
                                                $statement = '';
                                                if(isset($role)){
                                                    if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view_own'=>1)) > 0){
                                                        $statement = 'checked';
                                                    }

                                                    //  if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view'=>1)) > 0){
                                                    //     $statement = 'disabled';
                                                    // }


                                                }
                                                ?>
                                                <div class="checkbox">
                                                    <input type="checkbox" class="viewOwnAll" data-shortname="<?php echo $permission['shortname']; ?>" <?php echo $statement; ?> name="view_own[]" id="view_own_<?php echo $permission['permissionid']; ?>" value="<?php echo $permission['permissionid']; ?>" data-can-view-own>
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

                                        <td class="text-center">
                                            <?php if($permission_condition['create'] == true){
                                                $statement = '';
                                                if(isset($role)){
                                                    if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_create'=>1)) > 0){
                                                        $statement = 'checked';
                                                    }
                                                }
                                                ?>
                                                <div class="checkbox">
                                                    <input type="checkbox" class="createAll" data-shortname="<?php echo $permission['shortname']; ?>" data-can-create <?php echo $statement; ?> name="create[]" id="create_<?php echo $permission['permissionid']; ?>" value="<?php echo $permission['permissionid']; ?>">
                                                    <label></label>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission_condition['edit'] == true){
                                                $statement = '';
                                                if(isset($role)){
                                                    if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_edit'=>1)) > 0){
                                                        $statement = 'checked';
                                                    }
                                                }
                                                ?>
                                                <div class="checkbox">
                                                    <input type="checkbox" class="editAll" data-shortname="<?php echo $permission['shortname']; ?>" data-can-edit <?php echo $statement; ?> name="edit[]" id="edit_<?php echo $permission['permissionid']; ?>" value="<?php echo $permission['permissionid']; ?>">
                                                    <label></label>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($permission_condition['delete'] == true){
                                                $statement = '';
                                                if(isset($role)){
                                                    if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_delete'=>1)) > 0){
                                                        $statement = 'checked';
                                                    }
                                                }
                                                ?>
                                                <div class="checkbox checkbox-danger">
                                                    <input type="checkbox" class="deleteAll" data-shortname="<?php echo $permission['shortname']; ?>" data-can-delete <?php echo $statement; ?> name="delete[]" id="delete_<?php echo $permission['permissionid']; ?>" value="<?php echo $permission['permissionid']; ?>">
                                                    <label></label>
                                                </div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="topButton">
                            <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>admin/roles'"><?php echo _l( 'Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                            <?php if(isset($role)){ ?>
                                <?php if (has_permission('account_setup', '', 'create')){ ?>
                                    <a href="<?php echo admin_url('roles/role'); ?>" class="btn btn-info pull-right mleft4 mbot20 display-block"><?php echo _l('new_role'); ?></a>
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
    _validate_form($('.roles-form'),{name:'required'});
    $('.selectAll').click(function(e){
        var tr = $(e.target).closest('tr');
        $('td input:checkbox',tr).prop('checked',this.checked);

        var tr_id = $(tr).attr('id');
        if($('#all_'+tr_id).is(':checked')) {
            $("#checkedsizes_"+tr_id).val(4);
        } else {
            $("#checkedsizes_"+tr_id).val(0);
        }
    });

    /*$('.table-bordered tr').each(function(){
      var count = 0;
      var hdn = $(this).find('input[name^="checkedsizes"]');
      count = $(this).find(':checkbox:checked').length;
      hdn.val(count);
    });*/

    $('.viewAll').click(function(e){
        var tr = $(e.target).closest('tr');
        var tr_id = $(tr).attr('id');

        fncheck(tr_id, 'view');
    });

    $('.viewOwnAll').click(function(e){
        var tr = $(e.target).closest('tr');
        var tr_id = $(tr).attr('id');

        fncheck(tr_id, 'view_own');
    });

    $('.createAll').click(function(e){
        var tr = $(e.target).closest('tr');
        var tr_id = $(tr).attr('id');

        fncheck(tr_id, 'create');
    });

    $('.editAll').click(function(e){
        var tr = $(e.target).closest('tr');
        var tr_id = $(tr).attr('id');

        fncheck(tr_id, 'edit');
    });

    $('.deleteAll').click(function(e){
        var tr = $(e.target).closest('tr');
        var tr_id = $(tr).attr('id');

        fncheck(tr_id, 'delete');
    });

    function fncheck(tr_id, type) {
        var hiddenval = $("#checkedsizes_"+tr_id).val();

        if($('#'+type+'_'+tr_id).is(':checked')) {
            var newval = parseInt(hiddenval) + 1;
            $("#checkedsizes_"+tr_id).val(newval);

            if(newval >= 4) {
                $("#all_"+tr_id).prop('checked', true);
            }
        } else {
            var newval = parseInt(hiddenval) - 1;
            $("#checkedsizes_"+tr_id).val(newval);

            if(newval < 4) {
                $("#all_"+tr_id).prop('checked', false);
            }
        }
    }
</script>

</body>
</html>