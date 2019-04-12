<?php
/**
* Added By: Vaidehi
* Dt: 10/02/2017
* Package Module
*/
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name',
    'name',
    'packagetypeid',
    'price',
    'trial_period'
);

$sIndexColumn = "packageid";
$sTable       = 'tblpackages';

$where = array();
array_push($where, ' AND deleted = 0');

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),$where,array('packageid'));

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $package_permissions = $this->_instance->packages_model->get_package_permissions($aRow['packageid']);
            $_data            = '<span class="package">' .$_data. '</span>';
            $_data .= '<span class="customers ">' . total_rows('tblclients', array(
                'packageid' => $aRow['packageid']
                )) . '</span>';
        }/*else if ($aColumns[$i] == 'customers') {
            $_data = '<span class="mtop10 display-block">' . total_rows('tblclients', array(
                    'packageid' => $aRow['packageid']
                )) . '</span>';
        }*/else if ($aColumns[$i] == 'packagetypeid') {
            $package_permissions = $this->_instance->packages_model->get_package_permissions($aRow['packageid']);
            $packagetype_name = $this->_instance->packages_model->get_packagetype_byid($_data);
            $_data            = '' . $packagetype_name[0]['name'] . '';
        } else if ($aColumns[$i] == 'price') {
            $_data            = '$' . $_data . '';
        } else if ($aColumns[$i] == 'trial_period') {
            $trial_period = ($_data == '' ? '--' : $_data);
            $_data            = '' . $trial_period . '';
        }

        $row[] = $_data;
    }
    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
    if (has_permission('packages','','edit')) {
        $options .= icon_url('packages/package/' . $aRow['packageid'], 'pencil-square-o');
    }else{
        $options = "";
    }
    if (has_permission('packages','','delete')) {
        $row[]   = $options .= icon_url('packages/delete/' . $aRow['packageid'], 'remove', ' _delete');
    }else{
        $row[]   = $options .= "";
    }
    $options.="</ul></div>";
    $output['aaData'][] = $row;
}
