<?php
/*if (isset($venue['rel_type']) && $venue['rel_id'] > 0) {
    $event = get_event_name($venue['rel_type'], $venue['rel_id']);
    $venue['eventtypename'] = isset($event->name) ? $event->name : "";
}*/
/*if ($venue['status'] == $status['statusid']) {*/

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
$CI->db->where('pintype', 'Venues');
$CI->db->where('pintypeid', $venue['venueid']);
$result = $CI->db->get()->row();

$favorit = $CI->db->select('favoriteid')->from('tblfavorites')->where('favtype = "Venue" AND typeid=' . $venue['venueid'] . ' AND userid=' . $user_id)->get()->row();

$venueaddress = "";
if (!empty($venue['venuecity'])) {
    $venueaddress .= $venue['venuecity'];
}
if (!empty($venue['venuestate'])) {
    $venueaddress .= ", ";
    $venueaddress .= $venue['venuestate'];
}
?>
    <li data-venue-id="<?php echo $venue['venueid']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="panel-body card-body">
            <div class="row">
                <div class="cardHead">
                    <a href="javascript:void(0)"
                       class="venue-fav <?php echo isset($favorit->favoriteid) ? "favorite" : "" ?>"
                       title="<?php echo isset($favorit->favoriteid) ? "UnMark Favorite" : "Mark Favorite" ?>"
                       venue_id="<?php echo $venue['venueid'] ?>"><i
                                class="fa fa-star<?php echo isset($favorit->favoriteid) ? "" : "-o" ?>"></i></a>

                    <div class="right-links">
                        <div class="pin-block">
                            <i class="fa fa-fw fa-thumb-tack venue-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                               title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                               id="<?php echo $venue['venueid'] ?>"
                               venue_id="<?php echo $venue['venueid'] ?>">

                            </i>
                        </div>
                        <div class="show-act-block"><?php
                            $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                            $options .= '<li><a href=' . admin_url() . 'venues/view/' . $venue['venueid'] . ' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
                            if ((has_permission('addressbook', '', 'edit') && $venue['created_by'] == $user_id) || is_sido_admin()) {
                                $options .= '<li><a href=' . admin_url() . 'venues/venue/' . $venue['venueid'] . ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                            } else {
                                $options .= "";
                            }

                            if ((has_permission('addressbook', '', 'edit') && $venue['created_by'] == $user_id) || is_sido_admin()) {
                                $options .= '<li><a href=' . admin_url() . 'venues/delete/' . $venue['venueid'] . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                            }
                            $options .= "</ul></div>";
                            echo $options;
                            ?></div>
                        <!--<div class="checkbox"><input type="checkbox" value="<?php /*echo $venue['id'] */ ?>"><label></label>
                    </div>-->
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-xs-3">
                    <?php echo venue_logo_image($venue['venueid'], array(
                        'staff-profile-image-small',
                        'media-object img-circle pull-left mright10'
                    )); ?>
                </div>
                <div class="col-xs-9 card-name">
                    <span class="venueFrom">
                        <?php echo $venue['venuename']; ?>
                    </span>
                    <?php if (!empty($venue['venueslogan'])) { ?>
                        <span class="venueslogan"><?php echo $venue['venueslogan']; ?></span>
                    <?php } ?>
                    <span class="venueaddress"><?php echo $venueaddress; ?></span>
                    <span class="venuephone"><?php
                        if (is_serialized($venue['venuephone'])) {
                            $venue['venuephone'] = unserialize($venue['venuephone']);
                            echo $venue['venuephone'][0]['phone'];
                            if(!empty($venue['venuephone'][0]['ext']) && $venue['venuephone'][0]['ext']!=""){
                                echo "  x".$venue['venuephone'][0]['ext'];
                            }
                        }else{
                            echo $venue['venuephone'];
                        } ?></span>
                    <span class="venueemail">
                        <?php
                        if (is_serialized($venue['venueemail'])) {
                            $venue['venueemail'] = unserialize($venue['venueemail']);
                            echo $venue['venueemail'][0]['email'];
                        }else{
                            echo $venue['venueemail'];
                        } ?></span>
                    <div class="venue-body text-center"></div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">

                    <?php
                    $tags = $venue['venuetags'];
                    if (!empty($tags)) {
                        $tags = explode(',', $tags);
                        $tags = get_tags_in_addressbook($tags);
                        $counter = 1; ?>
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
                                        <?php
                                    } ?>
									</span>
									</div>
									</span>
                                    <?php
                                    break;
                                }
                                ?>
                            <span class="tag"><?php echo $tag; ?>

							</span>
                                <?php $counter++;
                            } ?>
                        </span>

                        <?php
                    }
                    ?>

                </div>
            </div>
    </li>
<?php //} ?>