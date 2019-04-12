<?php
if (isset($file['rel_type']) && $file['rel_id'] > 0) {
    $event = get_event_name($file['rel_type'], $file['rel_id']);
    $file['eventtypename'] = isset($event->name) ? $event->name : "";
}
/*if ($file['status'] == $status['statusid']) {*/
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
$CI->db->where('pintype', 'File');
$CI->db->where('pintypeid', $file['id']);
$result = $CI->db->get()->row();
$extension = get_file_extension($file['file_name']);
$filename = str_replace("_", " ", $file['file_name']);
$filename = str_replace("." . $extension, "", $filename);
$filename = str_replace("-", " ", $filename);
$filename = ucfirst($filename);
//echo '<pre>-->'; print_r($file); die;
if (isset($lid)) {
    $FilePath = 'uploads/leads/' . $lid . '/' . $file['file_name'];
    $path = LEAD_ATTACHMENTS_FOLDER;
    $delete = admin_url('leads/remove_file/' . $lid . '/' . $file['id']);
} elseif (isset($pid)) {
    $FilePath = 'uploads/projects/' . $pid . '/' . $file['file_name'];
    $path = PROJECT_ATTACHMENTS_FOLDER;
    $delete = admin_url('projects/remove_custom_file/' . $pid . '/' . $file['id']);
}
?>
    <li data-file-id="<?php echo $file['id']; ?>"
        class="col-sm-6 col-lg-3 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="panel-body card-body">
            <div class="row">
                <div class="col-xs-12 card-name text-center">
                    <div class="filetypeImg">
                        <?php if (is_image($path . $file['rel_id'] . '/' . $file['file_name'])) { ?>
                            <a href="<?php echo base_url($FilePath); ?>" data-lightbox="lead-attachment"><img src="<?php echo base_url($FilePath); ?>"
                                 class="img img-responsive "
                                            alt="<?php echo $file['file_name']; ?>"></a>
                        <?php } elseif (in_array($extension, array('doc', 'docx'))) { ?>
                            <i class="fa fa-file-word-o fa-5x"></i>
                        <?php } elseif (in_array($extension, array('pdf'))) { ?>
                            <i class="fa fa-file-pdf-o fa-5x"></i>
                        <?php } elseif (in_array($extension, array('xls', 'xlsx'))) { ?>
                            <i class="fa fa-file-excel-o fa-5x"></i>
                        <?php } ?>
                    </div>
                    <a href="javascript:void(0)" class="file_name"><?php echo $filename; ?>
                        <div class="file_info_popup">
                            <span class="file_orig_name display-block"><?php echo $file['file_name']; ?></span>
                            <span class="file_size display-block"><?php
                                if (isset($lid)) {
                                    echo format_size(filesize(LEAD_ATTACHMENTS_FOLDER . $lid . '/' . $file['file_name']));
                                } elseif (isset($pid)) {
                                    echo format_size(filesize(PROJECT_ATTACHMENTS_FOLDER . $file['rel_id'] . '/' . $file['file_name']));
                                } ?>
								</span>
                            <span class="file_upload_date display-block"><?php echo isset($file) ? date('D, M d, Y | g:i A', strtotime($file['dateadded'])) : ""; ?></span>
                        </div>
                    </a>

                    <div class="file-body text-center">
                        <span class="meetingRelType display-block">
                        <?php if ($file['rel_type'] == "project" || $file['rel_type'] == "event") { ?>
                            <i class="fa fa-book"></i>
                        <?php } elseif ($file['rel_type'] == "lead") { ?>
                            <i class="fa fa-tty"></i>
                        <?php } ?>
                            <?php echo isset($file['eventtypename']) ? $file['eventtypename'] : ""; ?>
						</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="right-links">
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                        /*$options .= '<li><a href=' . admin_url() . 'files/file/' . $file['id'] . ' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';*/

                        if (has_permission('leads', '', 'delete')) {
                            $options .= '<li><a href=' . $delete . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                        }

                        $options .= '<li><a href=' . site_url($FilePath) . ' download class="btn-icon" title="Edit"><i class="fa fa-download"></i><span>Download</span></a></li>';

                        $options .= "</ul></div>";
                        echo $options;
                        ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">
                    <span class="fileFrom display-block">
                        <?php echo staff_profile_image($file['staffid'], array(
                            'staff-profile-image-small',
                            'media-object img-circle pull-left mright10'
                        )); ?>
                    </span>
                </div>
            </div>
    </li>
<?php //} ?>