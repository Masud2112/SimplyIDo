<?php
/*if (isset($contact['rel_type']) && $contact['rel_id'] > 0) {
    $event = get_event_name($contact['rel_type'], $contact['rel_id']);
    $contact['eventtypename'] = isset($event->name) ? $event->name : "";
}*/
/*if ($contact['status'] == $status['statusid']) {*/

$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];

$class = "";
if ($count <= 3) {
    $class = "first_row";
}
$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];
$CI =& get_instance();
$CI->db->select('pinid as pinned');
$CI->db->from('tblpins');
$CI->db->where('userid', $user_id);
$CI->db->where('pintype', 'Addressbook');
$CI->db->where('pintypeid', $contact['addressbookid']);
$result = $CI->db->get()->row();

$favorit = $CI->db->select('favoriteid')->from('tblfavorites')->where('favtype = "Addressbook" AND typeid=' . $contact['addressbookid'] . ' AND userid=' . $user_id)->get()->row();

//echo '<pre>-->'; print_r($contact); die;

if (isset($_GET['lid'])) {
    $rel_id = $_GET['lid'];
    $rel_type = 'lead';
    $rel_link = '?lid=' . $rel_id;

} elseif (isset($_GET['pid'])) {
    $rel_id = $_GET['pid'];
    $rel_type = 'project';
    $rel_link = '?pid=' . $rel_id;

} else {
    $rel_id = "";
    $rel_type = '';
    $rel_link = "";
}

?>
    <li data-contact-id="<?php echo $contact['addressbookid']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="panel-body card-body">
            <div class="row">
                <div class="cardHead"><a href="javascript:void(0)"
                                         class="contact-fav <?php echo isset($favorit->favoriteid) ? "favorite" : "" ?>"
                                         title="<?php echo isset($favorit->favoriteid) ? "UnMark Favorite" : "Mark Favorite" ?>"
                                         contact_id="<?php echo $contact['addressbookid'] ?>"><i
                                class="fa fa-star<?php echo isset($favorit->favoriteid) ? "" : "-o" ?>"></i></a>
                    <div class="right-links">
                        <div class="show-act-block"><?php
                            $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                            $options .= '<li><a href=' . admin_url() . 'addressbooks/view/' . $contact['addressbookid'] .$rel_link. ' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
                            if ((has_permission('addressbook', '', 'edit') && $contact['created_by'] == $user_id) || is_sido_admin()) {
                                $options .= '<li><a href=' . admin_url() . 'addressbooks/addressbook/' . $contact['addressbookid'] .$rel_link. ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                            } else {
                                $options .= "";
                            }

                            if ((has_permission('addressbook', '', 'edit') && $contact['created_by'] == $user_id) || is_sido_admin()) {
                                $options .= '<li><a href=' . admin_url() . 'addressbooks/delete/' . $contact['addressbookid'] .$rel_link. ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                            }
                            $options .= "</ul></div>";
                            echo $options;
                            ?>
                        </div>
                        
                        <div class="pin-block">
                            <i class="fa fa-fw fa-thumb-tack contact-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                               title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                               id="<?php echo $contact['addressbookid'] ?>"
                               contact_id="<?php echo $contact['addressbookid'] ?>">
                            </i>
                        </div>
                        <!--<div class="checkbox"><input type="checkbox" value="<?php /*echo $contact['id'] */ ?>"><label></label>
						</div>-->

                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-xs-3">
                    <?php echo addressbook_profile_image($contact['addressbookid'], array(
                        'staff-profile-image-small',
                        'media-object img-circle pull-left mright10'
                    )); ?>
                </div>
                <div class="col-xs-9 card-name">
                    <span class="contactId">
                        <?php echo get_addressbook_full_name($contact['addressbookid']); ?>
                    </span>
                    <span class="conatctCompany"><?php echo $contact['companyname']; ?></span>
                    <span class="conatctPhone"><?php echo get_addressbook_phone($contact['addressbookid']); ?></span>
                    <span class="conatctEmail"><?php echo get_addressbook_email($contact['addressbookid']); ?></span>
                    <div class="contact-body text-center ">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">

                    <?php
                    $tags = get_addressbook_tags($contact['addressbookid'], $user_id);
                    if (!empty($tags)) {
                        $tags = explode(',', $tags);
                        $counter = 1;

                        ?>
                        <span class="conatctTags">
                    <?php
                    foreach ($tags as $tag) {
                        if ($counter > 3) { ?>
                            <span class="tag moretag">+<?php echo count($tags) - 3; ?>
                                <div class="moreTagsPopup">
							<span class="wrap">
                    <?php
                    foreach ($tags as $tag) { ?>
                        <span class="tag"><?php echo $tag; ?></span>
                        <?php $counter++;
                    } ?>
                    </span></div>
							</span>
                            <?php
                            break;
                        }
                        ?>
                        <span class="tag"><?php echo $tag; ?></span>
                        <?php $counter++;
                    } ?>
                    </span>

                    <?php } ?>
                </div>

            </div>
    </li>
<?php //} ?>