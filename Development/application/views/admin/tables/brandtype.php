<?php
//Added By Avni on 11/14 
defined('BASEPATH') OR exit('No direct script access allowed');

$session_data = get_session_data();
$is_sido_admin = $session_data['is_sido_admin'];
$is_admin = $session_data['is_admin'];

$aColumns     = array(
    'name'    
    );
$sIndexColumn = "brandtypeid";
$sTable       = 'tblbrandtype';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
    'brandtypeid'
    ));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        //echo '<pre>'; print_r($_data);
         $is_referenced = (total_rows('tblbrand',array('brandtypeid'=>$aRow['brandtypeid'])) > 0 ? 1 : 0);

        if($aColumns[$i] == 'name'){
            if($is_sido_admin == 1 || $is_admin == 1){
                $_data = '<a href="#" data-toggle="modal" data-target="#service_modal" data-id="'.$aRow['brandtypeid'].'">'.$_data.'</a>';
            }else {
                $_data = $_data;
            }
        }
       
        $row[] = $_data;
    }

   
   if($is_sido_admin == 1 || $is_admin == 1){
       $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
        $options .= icon_url('#' . $aRow['brandtypeid'], 'pencil-square-o', '', array(
            'data-toggle' => 'modal',
            'data-target' => '#service_modal',
            'data-id' => $aRow['brandtypeid'],
            'data-is-referenced'=>$is_referenced
            ));
      $row[]   = $options .= icon_url('services/delete/' . $aRow['brandtypeid'], 'remove', 'modal-alert-warning _delete');
   }
   else{
     $row[]   = $options = "";
   }
    $options.="</ul></div>";
    $output['aaData'][] = $row;
}
