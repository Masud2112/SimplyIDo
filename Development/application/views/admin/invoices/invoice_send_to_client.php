<div class="modal fade email-template" data-editor-id=".<?php echo 'tinymce-'.$invoice->id; ?>" id="invoice_send_to_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open('admin/invoices/send_to_email/'.$invoice->id); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('invoice_send_to_client_modal_heading'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                                if($template_disabled){
                                    echo '<div class="alert alert-danger">';
                                    echo 'The email template <b><a href="'.admin_url('emails/email_template/'.$template_id).'" target="_blank">'.$template_system_name.'</a></b> is disabled. Click <a href="'.admin_url('emails/email_template/'.$template_id).'" target="_blank">here</a> to enable the email template in order to be sent successfully.';
                                    echo '</div>';
                                }
                                $counter =0;
                                $contacts_final = array();
                                $selected = array();
                                foreach ($invoice->clients as $client){
                                    $contacts_final[$counter]['id'] = isset($client->addressbookid)?$client->addressbookid:"";
                                    $contacts_final[$counter]['email'] = isset($client->email->email)?$client->email->email:"";
                                    $contacts_final[$counter]['firstname'] = isset($client->firstname)?$client->firstname:"";
                                    $contacts_final[$counter]['lastname'] = isset($client->lastname)?$client->lastname:"";
                                    $counter++;
                                }
                                /*$contacts = $this->addressbooks_model->get_contacts($invoice->clientid);
                                $contacts_final = array();
                                $contacts_final[0]['id'] = isset($contacts->addressbookid)?$contacts->addressbookid:"";
                                $contacts_final[0]['email'] = isset($contacts->email->email)?$contacts->email->email:"";
                                $contacts_final[0]['firstname'] = isset($contacts->firstname)?$contacts->firstname:"";
                                $contacts_final[0]['lastname'] = isset($contacts->lastname)?$contacts->lastname:"";*/
                                
                                // foreach($contacts_final as $contact_final){
                                //     if(has_contact_permission('invoices',$contact_final['id'])){
                                //         array_push($selected,$contact_final['id']);
                                //     }
                                // }
                                // if(count($selected) == 0){
                                //     echo '<p class="text-danger">' . _l('sending_email_contact_permissions_warning',_l('customer_permission_invoice')) . '</p><hr />';
                                // }
                                echo render_select('sent_to[]',$contacts_final,array('id','email','firstname,lastname'),'invoice_estimate_sent_to_email',$selected,array(),array(),'','',false);

                                ?>
                        </div>
                        <?php echo render_input('cc','CC'); ?>
                        <hr />
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="attach_pdf" id="attach_pdf" checked>
                            <label for="attach_pdf"><?php echo _l('invoice_send_to_client_attach_pdf'); ?></label>
                        </div>
                        <h5 class="bold"><?php echo _l('invoice_send_to_client_preview_template'); ?></h5>
                        <hr />
                        <?php echo render_textarea('email_template_custom','',$template->message,array(),array(),'','tinymce-'.$invoice->id); ?>
                        <?php echo form_hidden('template_name',$template_name); ?>
                    </div>
                </div>
                <?php if(count($invoice->attachments) > 0){ ?>
                <hr />
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="bold no-margin"><?php echo _l('include_attachments_to_email'); ?></h5>
                        <hr />
                        <?php foreach($invoice->attachments as $attachment) {
                            ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" <?php if(!empty($attachment['external'])){echo 'disabled';}; ?> value="<?php echo $attachment['id']; ?>" name="email_attachments[]">
                            <label for=""><a href="<?php echo site_url('download/file/sales_attachment/'.$attachment['attachment_key']); ?>"><?php echo $attachment['file_name']; ?></a></label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('send'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
