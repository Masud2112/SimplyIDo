<?php
/**
 * Added By: Avni
 * Dt: 10/11/2017
 * Address Book Module
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$CI->db->query('SET sql_mode=""');
$brandid = get_user_session();
$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];
$is_sido_admin = $session_data['is_sido_admin'];
$lid = $this->_instance->input->get('lid');
$pid = $this->_instance->input->get('pid');
$eid = $this->_instance->input->get('eid');
$aColumns = array(
    'favoriteid', '30', 'firstname', '18', '17', '27'
);

$sIndexColumn = "addressbookid";
$sTable = 'tbladdressbook';
$join = array();

if ($brandid > 0) {
    $brandid = $brandid;
} else if ($is_sido_admin > 0) {
    $brandid = 0;
} else {
    $brandid = 0;
}

if ($brandid > 0) {
    array_push($join, 'INNER JOIN tbladdressbook_client as abclient ON tbladdressbook.addressbookid = abclient.addressbookid AND abclient.deleted=0 AND abclient.brandid=' . $brandid);
}

//Added on 10/31 by Avni 
if (isset($lid)) {
    array_push($join, 'INNER JOIN tblleadcontact ON tbladdressbook.addressbookid = tblleadcontact.contactid AND tblleadcontact.brandid=' . $brandid . ' AND tblleadcontact.leadid=' . $lid);
}

/* Added by Purvi on 12-20-2017 for get contacts by projects */
if (isset($pid)) {
    $this->_instance->db->select('id');
    $this->_instance->db->where('(parent = ' . $pid . ' OR id = ' . $pid . ')');
    $this->_instance->db->where('deleted', 0);
    $related_project_ids = $this->_instance->db->get('tblprojects')->result_array();
    $related_project_ids = array_column($related_project_ids, 'id');
    if (!empty($related_project_ids)) {
        $related_project_ids = implode(",", $related_project_ids);
        array_push($join, 'INNER JOIN tblprojectcontact ON tbladdressbook.addressbookid = tblprojectcontact.contactid AND tblprojectcontact.brandid=' . $brandid . ' AND (tblprojectcontact.projectid in(' . $related_project_ids . ') OR tblprojectcontact.eventid in(' . $related_project_ids . '))');
    } else {
        array_push($join, 'INNER JOIN tblprojectcontact ON tbladdressbook.addressbookid = tblprojectcontact.contactid AND tblprojectcontact.brandid=' . $brandid . ' AND tblprojectcontact.projectid =' . $pid);
    }

}

if (isset($eid)) {
    array_push($join, 'INNER JOIN tblprojectcontact ON tbladdressbook.addressbookid = tblprojectcontact.contactid AND tblprojectcontact.brandid=' . $brandid . ' AND tblprojectcontact.eventid =' . $eid);
}

array_push($join, 'LEFT JOIN tblfavorites ON tblfavorites.userid=' . $user_id . ' and tblfavorites.favtype = "Addressbook" and tblfavorites.typeid = tbladdressbook.addressbookid');

// array_push($join, 'JOIN tbladdressbooktags as abtags ON tbladdressbook.addressbookid = abtags.addressbookid AND abtags.brandid=' . $brandid .' INNER JOIN tbltags ON tbltags.id=abtags.tagid and abtags.brandid=tbltags.brandid and tbltags.deleted=0');

$where = array();
array_push($where, ' AND tbladdressbook.deleted = 0 AND (tbladdressbook.ispublic=1 OR tbladdressbook.brandid=' . get_user_session() . ')');
//array_push($where, ' and brandid=' . $brandid);
//echo '<pre>' . print_r($where); 

$groupby = ' Group By tbladdressbook.addressbookid';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'profile_image',
    'lastname',
    'firstname',
    'tbladdressbook.addressbookid',
    'created_by',
    'ispublic',
    'companytitle',
    '(SELECT pinid FROM tblpins WHERE tblpins.userid = ' . $user_id . ' and tblpins.pintype = "Addressbook" and tblpins.pintypeid = tbladdressbook.addressbookid) as pinned',
    '(SELECT tbladdressbookemail.email FROM tbladdressbookemail WHERE tbladdressbookemail.type = "primary" and tbladdressbookemail.addressbookid = tbladdressbook.addressbookid) as email',
    '(SELECT tbladdressbookphone.phone FROM tbladdressbookphone WHERE tbladdressbookphone.type = "primary" and tbladdressbookphone.addressbookid = tbladdressbook.addressbookid) as phone',
    '(SELECT tbladdressbookphone.ext FROM tbladdressbookphone WHERE tbladdressbookphone.type = "primary" and tbladdressbookphone.addressbookid = tbladdressbook.addressbookid) as ext',
    '(SELECT GROUP_CONCAT(tbltags.name) FROM tbltags INNER JOIN tbladdressbooktags ON  tbladdressbooktags.brandid=' . $brandid . ' Where tbladdressbook.addressbookid = tbladdressbooktags.addressbookid and tbltags.id=tbladdressbooktags.tagid and tbladdressbooktags.brandid=tbltags.brandid and tbltags.deleted=0) as tags'), $groupby);

$output = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];

        if ($aRow['favoriteid'] > 0) {
            $_data = '<a href="javascript:void(0)" class="contact-fav favorite" title="UnMark Favorite" contact_id="' . $aRow['addressbookid'] . '"><i class="fa fa-star"></i></a>';
        } else {
            $_data = '<a href="javascript:void(0)" class="contact-fav" title="Mark Favorite" contact_id="' . $aRow['addressbookid'] . '"><i class="fa fa-star-o"></i></a>';
        }

        if ($aColumns[$i] == '30') {
            if ($aRow['pinned'] > 0) {
                $_data = '<i class="fa fa-fw fa-thumb-tack contact-pin pinned" title="Unpin from dashboard" id="' . $aRow['addressbookid'] . '" contact_id="' . $aRow['addressbookid'] . '"></i>';
            } else {
                $_data = '<i class="fa fa-fw fa-thumb-tack contact-pin" title="Pin to dashboard" id="' . $aRow['addressbookid'] . '" contact_id="' . $aRow['addressbookid'] . '"></i>';
            }
        }

        if ($aColumns[$i] == 'firstname') {
            $_data = "<div class='addressbook_Name_blk'><div class='addressbook_profile_image_img'> <div class='profImgDiv'>".addressbook_profile_image($aRow['addressbookid'], array('addressbook-profile-image-small'))."</div></div>";
            $_data .= "<div class='addressbook_cont'> <strong>". $aRow['firstname'] . ' ' . $aRow['lastname'] . "</strong>";
            if(!empty($aRow['companytitle'])){
                $_data .="<p>".$aRow['companytitle']."</p></div></div>";
            }
        } elseif ($aColumns[$i] == '17') {
            $_data = $aRow['email'];
        } elseif ($aColumns[$i] == '18') {
            $_data = $aRow['phone'];
            if (!empty($aRow['ext'])) {
                $_data .= "  x" . $aRow['ext'];
            }
        }
        // elseif ($aColumns[$i] == 'tags') {
        //     $tags = explode(',', $_data);   
        //     $tagdata='';         
        //     foreach ($tags as $tag)
        //     {
        //         $tagdata .= $tag . ", ";
        //         //$tagdata .= '<i class="fa fa-bookmark-o">bookmark</i> ' . $tag . '</a>';
        //     }
        //     $_data=rtrim($tagdata,', ');
        // }
        elseif ($aColumns[$i] == '27') {
            $_data = render_tags($aRow['tags']);
        }

        $row[] = $_data;
    }
    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
    if (isset($lid)) {
        $options .= icon_url('addressbooks/view/' . $aRow['addressbookid'] . "?lid=" . $lid, 'eye', '', array('title' => 'View Contact Details'));
    } else if (isset($pid)) {
        $options .= icon_url('addressbooks/view/' . $aRow['addressbookid'] . "?pid=" . $pid, 'eye', '', array('title' => 'View Contact Details'));
    } else if (isset($eid)) {
        $options .= icon_url('addressbooks/view/' . $aRow['addressbookid'] . "?eid=" . $eid, 'eye', '', array('title' => 'View Contact Details'));
    } else {
        $options .= icon_url('addressbooks/view/' . $aRow['addressbookid'], 'eye', '', array('title' => 'View Contact Details'));
    }

    if (has_permission('addressbook', '', 'edit') && ($is_sido_admin > 0 || $aRow['created_by'] == $user_id)) {
        if (isset($lid)) {
            $options .= icon_url('addressbooks/addressbook/' . $aRow['addressbookid'] . "?lid=" . $lid, 'pencil-square-o');
        } else if (isset($pid)) {
            $options .= icon_url('addressbooks/addressbook/' . $aRow['addressbookid'] . "?pid=" . $pid, 'pencil-square-o');
        } else if (isset($eid)) {
            $options .= icon_url('addressbooks/addressbook/' . $aRow['addressbookid'] . "?eid=" . $eid, 'pencil-square-o');
        } else {
            $options .= icon_url('addressbooks/addressbook/' . $aRow['addressbookid'], 'pencil-square-o');
        }
    } else {
        $options .= "";
    }
    if (has_permission('addressbook', '', 'delete')) {
        if (isset($lid)) {
            $row[] = $options .= icon_url('addressbooks/delete/' . $aRow['addressbookid'] . "?lid=" . $lid, 'remove', '_delete');
        } else if (isset($pid)) {
            $row[] = $options .= icon_url('addressbooks/delete/' . $aRow['addressbookid'] . "?pid=" . $pid, 'remove', '_delete');
        } else if (isset($eid)) {
            $row[] = $options .= icon_url('addressbooks/delete/' . $aRow['addressbookid'] . "?eid=" . $eid, 'remove', '_delete');
        } else {
            $row[] = $options .= icon_url('addressbooks/delete/' . $aRow['addressbookid'], 'remove', '_delete');
        }
    } else {
        $row[] = $options .= "";
    }
    $options .= "</ul></div>";
    $output['aaData'][] = $row;
}