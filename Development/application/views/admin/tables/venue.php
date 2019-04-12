<?php
/**
* Added By: Vaidehi
* Dt: 02/13/2018
* Venue Module
*/
defined('BASEPATH') OR exit('No direct script access allowed');

$brandid        = get_user_session();
$session_data   = get_session_data();
$is_sido_admin  = $session_data['is_sido_admin'];
$is_admin       = $session_data['is_admin'];
$user_id        = $session_data['staff_user_id'];
$lid = $this->_instance->input->get('lid');
$pid = $this->_instance->input->get('pid');
$eid = $this->_instance->input->get('eid');
$aColumns     = array(
    'favoriteid',
    '30',
    'venuename',
    'venuephone',
    'venueemail',
);

$sIndexColumn = "venueid";
$sTable       = 'tblvenue';

$join    = array();
$where   = array();

array_push($where, ' AND tblvenue.deleted = 0');
array_push($where, ' AND (tblvenue.ispublic = 1 OR tblvenue.created_by='.get_staff_user_id().')');

if($brandid > 0){
    array_push($where, 'AND tblbrandvenue.brandid = ' . $brandid);
    array_push($where, 'AND tblbrandvenue.deleted = 0');
}  

if($brandid > 0){
    array_push($join, 'INNER JOIN tblbrandvenue ON tblvenue.venueid = tblbrandvenue.venueid AND tblbrandvenue.deleted = 0 AND tblbrandvenue.brandid = ' . $brandid); 
}

//for lead
if(isset($lid)) {
    //array_push($join, 'INNER JOIN tblleadvenue ON tblvenue.venueid = tblleadvenue.venueid AND tblleadvenue.brandid = ' . $brandid . ' AND tblleadvenue.leadid = ' . $lid);
    array_push($join, 'INNER JOIN tblleads ON tblvenue.venueid = tblleads.venueid AND tblleads.brandid = ' . $brandid . ' AND tblleads.id = ' . $lid);
}

//for project and/or sub-project
if(isset($pid)) {

    array_push($join, 'INNER JOIN tblprojects ON tblvenue.venueid = tblprojects.venueid AND tblprojects.brandid = ' . $brandid . ' AND tblprojects.id = ' . $pid);

    /*$this->_instance->db->select('id');
    $this->_instance->db->where('(parent = '.$pid.' OR id = '.$pid.')');
    $this->_instance->db->where('deleted', 0);
    $related_project_ids = $this->_instance->db->get('tblprojects')->result_array();
    $related_project_ids = array_column($related_project_ids, 'id');
    if(!empty($related_project_ids)) {
        $related_project_ids = implode(",", $related_project_ids);
        array_push($join, 'INNER JOIN tblprojectvenue ON tblvenue.venueid = tblprojectvenue.venueid AND tblprojectvenue.brandid = ' . $brandid . ' AND (tblprojectvenue.projectid IN (' . $related_project_ids. ') OR tblprojectvenue.eventid IN ('.$related_project_ids.'))');
    } else {
        array_push($join, 'INNER JOIN tblprojectvenue ON tblvenue.venueid = tblprojectvenue.venueid AND tblprojectvenue.brandid = ' . $brandid . ' AND tblprojectvenue.projectid = ' . $pid);
    }*/
    
}
if(isset($eid)) {
    array_push($join, 'INNER JOIN tblprojectvenue ON tblvenue.venueid = tblprojectvenue.venueid AND tblprojectvenue.brandid = ' . $brandid . ' AND tblprojectvenue.eventid = ' . $eid);
}

array_push($join, 'LEFT JOIN tblfavorites ON tblfavorites.userid = ' . $user_id . ' and tblfavorites.favtype = "Venue" and tblfavorites.typeid = tblvenue.venueid');

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblvenue.venueid', 'venueaddress','venueaddress2', 'venuecity', 'venuestate', 'venuecity', 'venuezip', 'venuecountry', '(SELECT pinid FROM tblpins WHERE tblpins.userid = ' . $user_id . ' and tblpins.pintype = "Venues" and tblpins.pintypeid = tblvenue.venueid) as pinned','venuetags'));
$output  = $result['output'];
$rResult = $result['rResult'];

/*echo "<pre>";
print_r($rResult);
die();*/
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        //$_data = $aRow[$aColumns[$i]];

        if($aRow['favoriteid'] > 0){
            $_data =  '<a href="javascript:void(0)" class="venue-fav favorite" title="UnMark Favorite" venue_id="'.$aRow['venueid'].'"><i class="fa fa-star"></i></a>';
        } else {
            $_data =  '<a href="javascript:void(0)" class="venue-fav" title="Mark Favorite" venue_id="'.$aRow['venueid'].'"><i class="fa fa-star-o"></i></a>';
        }

        if($aColumns[$i] == '30') {
            if($aRow['pinned'] > 0){
                $_data =  '<i class="fa fa-fw fa-thumb-tack venue-pin pinned" title="Unpin from dashboard" id="'.$aRow['venueid'].'" venue_id="'.$aRow['venueid'].'"></i>';
            } else {
                $_data =  '<i class="fa fa-fw fa-thumb-tack venue-pin" title="Pin to dashboard" id="'.$aRow['venueid'].'" venue_id="'.$aRow['venueid'].'"></i>';
            }
        }

        if ($aColumns[$i] == 'venuename') {
            $_data = "<div class='addressbook_Name_blk'><div class='venue_profile_image_img mright5'> <div class='profImgDiv'>" . venue_logo_image($aRow['venueid'], array('venue-logo-image-small ')) ."</div></div>";
            $_data .= "<div class='addressbook_cont'> <strong>". $aRow['venuename'] . "</strong>";
            if(!empty($aRow['venuecity'])) {
                $_data .=  "<p>".$aRow['venuecity'];
            }
           
            if(!empty($aRow['venuestate'])) {
                $_data .=  ', ' . $aRow['venuestate'] . "</p></div></div>";
            }
        }

        if ($aColumns[$i] == 'venueaddress') {
            $address = '';

            if(!empty($aRow['venueaddress'])) {
                $address .= $aRow['venueaddress'];
            }

            if(!empty($aRow['venueaddress2'])) {
                $address .=  ', ' . $aRow['venueaddress2'];
            }

            if(!empty($aRow['venuecity'])) {
                $address .=  /*', ' . */$aRow['venuecity'];
            }

            if(!empty($aRow['venuestate'])) {
                $address .= ', ' . $aRow['venuestate'];
            }

            if(!empty($aRow['venuecountry']) && $aRow['venuecountry'] == 236) {
                $address .= ', United States';
            }

            if(!empty($aRow['venuezip'])) {
                $address .= ' - ' . $aRow['venuezip'];
            }

            $_data = "";
        }

        if ($aColumns[$i] == 'venueemail') {
            if(is_serialized($aRow['venueemail'])){
                $venueemail=unserialize($aRow['venueemail']);
                $aRow['venueemail']=$venueemail[0]['email'];
            }
            $_data = $aRow['venueemail'];
        }

        if ($aColumns[$i] == 'venuephone') {
            if(is_serialized($aRow['venuephone'])){
                $venuephone=unserialize($aRow['venuephone']);
                $aRow['venuephone']=$venuephone[0]['phone'];
                if(!empty($venuephone[0]['ext']) && $venuephone[0]['ext']!=""){
                    $aRow['venuephone'].= "  x".$venuephone[0]['ext'];
                }
            }
            $_data = $aRow['venuephone'];
        }    
                
        $row[] = $_data;
    }
    $row[] = render_venue_tags($aRow['venuetags']);
    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

    if (has_permission('addressbook','','view')) {
        if(isset($lid)) {
            $options .= icon_url('venues/view/' . $aRow['venueid'] . "?lid=" . $lid, 'eye', '', array('title'=>'View Venue Details'));
        } else if(isset($pid)) {
            $options .= icon_url('venues/view/' . $aRow['venueid'] . "?pid=" . $pid, 'eye', '', array('title'=>'View Venue Details'));
        } else if(isset($eid)) {
            $options .= icon_url('venues/view/' . $aRow['venueid'] . "?eid=" . $eid, 'eye', '', array('title'=>'View Venue Details'));
        } else {
            $options .= icon_url('venues/view/' . $aRow['venueid'], 'eye', '', array('title'=>'View Venue Details'));
        }
    } else {
        $options .= "";
    }

    if (has_permission('addressbook','','edit') && $is_sido_admin > 0 ) {
        if(isset($lid)) {
            $options .= icon_url('venues/venue/' . $aRow['venueid'] . "?lid=" . $lid, 'pencil-square-o');
        } else if(isset($pid)) {
            $options .= icon_url('venues/venue/' . $aRow['venueid'] . "?pid=" . $pid, 'pencil-square-o');
        } else if(isset($eid)) {
            $options .= icon_url('venues/venue/' . $aRow['venueid'] . "?eid=" . $eid, 'pencil-square-o');
        } else {
            $options .= icon_url('venues/venue/' . $aRow['venueid'], 'pencil-square-o');
        }
    } else {
        $options .= "";
    }

    if (has_permission('addressbook','','delete')) {
        if(isset($lid)) {
            $row[]   = $options .= icon_url('venues/delete/' . $aRow['venueid']. "?lid=" . $lid, 'remove', ' _delete');
        } else if(isset($pid)) {
            $row[]   = $options .= icon_url('venues/delete/' . $aRow['venueid']. "?pid=" . $pid, 'remove', ' _delete');
        } else if(isset($eid)) {
            $row[]   = $options .= icon_url('venues/delete/' . $aRow['venueid']. "?eid=" . $eid, 'remove', ' _delete');
        } else {
            $row[]   = $options .= icon_url('venues/delete/' . $aRow['venueid'], 'remove', ' _delete');
        }
    } else {
        $row[]   = $options .= "";
    }
    $options.="</ul></div>";
    $output['aaData'][] = $row;
}
