<?php
/**
* Added By : Vaidehi
* Dt : 10/14/2017
* Add New Lead Form
*/
init_head();
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('class'=>'lead-form','autocomplete'=>'off')); ?>
                <div class="col-md-6">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin">
                            	<?php echo _l('lead_profile'); ?>                      
                            </h4>
                            <hr class="hr-panel-heading" />
                            <?php $attrs = array('autofocus'=>true); ?>
                             <div class="form-group">
                                <label for="profile_image" class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
                                <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('profile_dimension'); ?>"></i>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                      <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                                      <input name="profile_image" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" type="file">
                                    </span>
                                    <span class="form-control"></span>
                                </div>
                                <!--<input type="file" name="profile_image" class="form-control" id="profile_image">-->
                             </div>
                            <?php echo render_input('name','lead_add_edit_event_name','','',$attrs); ?>
                                <?php echo render_select('eventtypeid',$eventtypes, array('eventtypeid','eventtypename'),'lead_add_edit_event_type'); ?>
                                 <div class="form-group" id="eventstartdate">
                                <?php echo render_datetime_input('eventstartdatetime','lead_add_edit_event_start_datetime', '',array('data-date-min-date'=>date('Y-m-d H:i')) ); ?></div>
                                <div class="form-group" id="eventenddate">
                                <?php echo render_datetime_input('eventenddatetime','lead_add_edit_event_end_datetime', '',array('data-date-min-date'=>date('Y-m-d H:i')) ); ?></div>
                                <div class="form-group">
                                	<label for="<?php echo _l('lead_add_edit_event_end_timezone'); ?>" class="control-label"><?php echo _l('lead_add_edit_event_end_timezone'); ?></label>
                                	<select name="eventtimezone" id="eventtimezone" class="form-control selectpicker" data-none-selected-text="<?php echo _l('lead_add_edit_event_end_timezone'); ?>" data-live-search="true">
    							        <?php //foreach(get_timezones_list() as $key => $timezones){ ?>
    							        <!--<optgroup label="<?php //echo $key; ?>">-->

    							            <?php foreach(get_timezones_list() as $key => $timezone){
                                             $timezone_name = str_replace("America/", "", $timezone);
                                             $timezone_name = str_replace("_", " ", $timezone_name);
                                             ?>
    							            <option value="<?php echo $key; ?>" <?php if(get_brand_option('default_timezone') == $key){echo 'selected';} ?>><?php echo $timezone_name; ?></option>
    							            <?php } ?>
    							        <!--</optgroup>-->
    							        <?php //} ?>
    							    </select>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="row">                                           
                                              <?php if(!isset($lead)){ ?>
                                              <div class="lead-select-date-contacted hide">
                                                 <?php echo render_datetime_input('custom_contact_date','lead_add_edit_datecontacted','',array('data-date-end-date'=>date('Y-m-d'))); ?>
                                              </div>
                                              <div class="checkbox checkbox-primary mtop25 hide">
                                                 <input type="checkbox" name="contacted_today" id="contacted_today" checked>
                                                 <label for="contacted_today"><?php echo _l('lead_add_edit_contacted_today'); ?></label>
                                              </div>
                                              <?php } else { ?>
                                              <?php echo render_datetime_input('lastcontact','leads_dt_last_contact',_dt($lead->lastcontact),array('data-date-end-date'=>date('Y-m-d'))); ?>
                                              <?php } ?>
                                           
                                        </div>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin">
                                <?php echo _l('lead_details'); ?>                      
                            </h4>
                            <hr class="hr-panel-heading" />
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <?php echo render_select('status',$statuses, array('id','name'),'lead_add_edit_status'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_select('assigned',$members, array('staffid','firstname', 'lastname'),'lead_add_edit_assigned'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <?php echo render_input('budget','lead_add_edit_budget','','number'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="<?php echo _l('lead_add_edit_booking_confidence'); ?>" class="control-label"><?php echo _l('lead_add_edit_booking_confidence'); ?></label>
                                            <select name="bookingconfidence" id="bookingconfidence" class="form-control selectpicker" data-none-selected-text="<?php echo _l('lead_add_edit_booking_confidence'); ?>" data-live-search="true">
                                                <option value=""></option>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <?php echo render_date_input('eventinquireon','lead_add_edit_event_inquireon', ''); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_date_input('eventdecideby','lead_add_edit_event_decideby', '',array('data-date-min-date'=>date('Y-m-d')) ); ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <?php echo render_select('source',$sources, array('id','name'),'lead_add_edit_source'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('sourcedetails','lead_add_edit_sourcedetails'); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                    <?php echo render_input('notes','lead_add_edit_notes'); ?>
                                </div></div>                                
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin">
                                <?php echo _l('lead_contact'); ?>                      
                            </h4>
                            <hr class="hr-panel-heading" />
                            <div class="form-group">
                                <div class="radio radio-primary radio-inline">
                                    <input id="contact_new" name="contact[]" value="new" checked="true" type="radio">
                                    <label for="<?php echo _l('new_contact'); ?>"><?php echo _l('new_contact'); ?></label>
                                </div>
                                <div class="radio radio-primary radio-inline">
                                    <input id="contact_existing" name="contact[]" value="existing" type="radio">
                                    <label for="<?php echo _l('choose_existing_client'); ?>"><?php echo _l('choose_existing_client'); ?></label>
                                </div>
                            </div>
                            <div id="new-address-book">
                                <div class="col-md-12">
                                    <div class="panel_s">
                                        <div class="panel-body">
                                            <h4 class="no-margin">
                                             Add New Contact
                                             <?php if(isset($addressbook)){ ?>
                                          <?php echo form_hidden('addressbookid',$addressbook->addressbookid); ?>
                                          <?php if (has_permission('addressbooks','','create')) { ?>
                                            <a href="<?php echo admin_url('addressbooks/addressbook'); ?>" class="btn btn-info pull-right mbot20 display-block" style="margin-bottom:0px">New Contact</a>
                                         
                                         <?php } ?>
                                         <?php } ?>
                                         </h4>
                                             <hr class="hr-panel-heading" />
                                         <?php if($profile_allow == 1){?>
                                         <div class="col-md-12">
                                            <?php if((isset($addressbook) && $addressbook->profile_image == NULL) || !isset($addressbook)){ ?>
                                              <div class="col-md-12">
                                                 <div class="form-group">
                                                    <label for="profile_image" class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
                                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('profile_dimension'); ?>"></i>
                                                    <div class="input-group">
                                                        <span class="input-group-btn">
                                                          <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                                                          <input name="profile_image" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" type="file">
                                                        </span>
                                                        <span class="form-control"></span>
                                                    </div>
                                                    <!--<input type="file" name="profile_image" class="form-control" id="profile_image">-->
                                                 </div>
                                              </div>
                                           <?php } ?>
                                           <?php if(isset($addressbook) && $addressbook->profile_image != NULL){ ?>
                                            <div class="form-group">
                                              <div class="col-md-9">
                                                    <?php echo addressbook_profile_image($addressbook->addressbookid,array('profile_image','img-responsive','addressbook-profile-image-thumb'),'thumb'); ?>
                                                 </div>
                                                 <div class="col-md-2 text-right">
                                                    <a href="<?php echo admin_url('addressbooks/remove_addressbook_profile_image/'.$addressbook->addressbookid); ?>"><i class="fa fa-remove"></i></a>
                                                 </div>
                                              </div>
                                           <?php } ?>
                                        </div>
                                          <?php }else{ ?>
                                            <input type="hidden" name="profile_image" value="">
                                          <?php } ?>
                                        <div class="col-md-12">
                                          <div class="col-md-12">
                                            <div class="form-group">
                                               <div class="checkbox checkbox-primary" title="Company">
                                                  <input value="1" type="checkbox" name="company" id="company" <?php if(isset($addressbook)){if($addressbook->company == 1){echo 'checked';}}; ?>>
                                                  <label for="company">Company</label>
                                               </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-12 companydetails">
                                          <div class="col-md-6">
                                              <?php $companyname=( isset($addressbook) ? $addressbook->companyname : ''); ?>
                                              <?php echo render_input('companyname','Company Name',$companyname,'text'); ?>
                                          </div>
                                          <div class="col-md-6">
                                              <?php $companytitle=( isset($addressbook) ? $addressbook->companytitle : ''); ?>
                                              <?php echo render_input('companytitle','Title',$companytitle,'text'); ?>
                                          </div>
                                        </div>
                                        <div class="col-md-12">
                                          <div class="col-md-6">
                                              <?php $firstname=( isset($addressbook) ? $addressbook->firstname : ''); ?>
                                              <?php echo render_input('firstname','First Name',$firstname,'text'); ?>
                                          </div>
                                          <div class="col-md-6">
                                            <?php $lastname=( isset($addressbook) ? $addressbook->lastname : ''); ?>
                                            <?php echo render_input('lastname','Last Name',$lastname,'text'); ?>
                                          </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                          <div class="col-md-6">
                                          <label for="gender" class="control-label">Gender</label>
                                            <select id="gender" class="selectpicker" name="gender" data-width="100%" data-none-selected-text="Select" data-live-search="false">
                                              <option value=""></option>
                                              <option value="male" <?php echo isset($addressbook) && $addressbook->gender == "male" ? "selected='selected'" : ""; ?>>Male</option>
                                              <option value="female" <?php echo (isset($addressbook) && $addressbook->gender == "female") ? "selected='selected'" : ""; ?>>Female</option>
                                              <option value="others" <?php echo isset($addressbook) && $addressbook->gender == "others" ? "selected='selected'" : ""; ?>>Other</option>
                                           </select>
                                            
                                          </div>
                                          <div class="col-md-6">
                                              <div class="form-group">
                                                <label for="tags" class="control-label"><small class="req text-danger">* </small>Tags</label>
                                                <select name="tags[]" id="tags[]" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" multiple>
                                                    <?php
                                                        foreach($tags as $tag){
                                                           $tselected = '';
                                                           if(in_array($tag['id'],$addressbook->tags_id)){
                                                              $tselected = "selected='selected'";
                                                           }
                                                           echo '<option value="'.$tag['id'].'" '.$tselected.'>'.$tag['name'].'</option>';
                                                        }
                                                     ?>
                                                </select>
                                              </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">

                                            <?php if($global_search_allow == 1){?>
                                            <div class="col-md-6">
                                              <div class="form-group">
                                                 <div class="checkbox checkbox-primary"  title="Allow Global Search?">
                                                    <input value="1" type="checkbox" name="ispublic" id="ispublic" <?php if(isset($addressbook)){if($addressbook->ispublic == 1){echo 'checked';}}; ?>>
                                                    <label for="ispublic">Allow Global Search?</label>
                                                 </div>
                                              </div>
                                            </div>
                                            <?php }else{ ?>
                                              <input type="hidden" name="ispublic" value="0">
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-12">
                                          <h4>Email</h4>
                                          <hr>
                                        </div>
                                          <div class="col-md-12" id="email-0">
                                              <div class="col-md-3">
                                                <label for="email[0][type]" class="control-label">Type</label>
                                                <select name="email[0][type]" id="email[0][type]" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                        echo '<option value="primary">Primary</option>';
                                                        
                                                     ?>
                                                </select>
                                              </div>
                                              <div class="col-md-9 multiemail">
                                                <?php $email=( isset($addressbook) ? $addressbook->email : ''); ?>
                                                  <?php echo render_input('email[0][email]','<small class="req text-danger">* </small>Email',$email,'email',array('autocomplete'=>'off')); ?>
                                              </div> 
                                          </div>
                                        <div class="col-md-12 text-right" style="margin-top:10px">
                                             <button id="email-add-more" name="email-add-more" class="btn btn-primary">Add More</button>
                                        </div>
                                        
                                        <div class="col-md-12">
                                          <h4>Phone</h4>
                                          <hr>
                                        </div>
                                          <div class="col-md-12" id="phone-0">
                                              <div class="col-md-3">
                                                <label for="phone[0][type]" class="control-label">Type</label>
                                                <select name="phone[0][type]" id="phone[0][type]" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                        echo '<option value="primary" selected="selected">Primary</option>';
                                                     ?>
                                                </select>
                                              </div>
                                              <div class="col-md-9 multiphone">
                                                <?php $phonenumber=( isset($addressbook) ? $addressbook->phonenumber : ''); ?>
                                                <?php echo render_input( 'phone[0][phone]', 'client_phonenumber',$phonenumber); ?>
                                              </div>        
                                          </div>
                                        <div class="col-md-12 text-right" style="margin-top:10px">
                                             <button id="phone-add-more" name="phone-add-more" class="btn btn-primary">Add More</button>
                                        </div>

                                        <div class="col-md-12">
                                          <h4>Social</h4>
                                          <hr>
                                        </div>
                                          <div class="col-md-12" id="website-0">
                                              <div class="col-md-3">
                                                <label for="website[0][type]" class="control-label">Type</label>
                                                <select name="website[0][type]" id="website[0][type]" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                        foreach($socialsettings as $social){
                                                           echo '<option value="'.$social['socialid'].'" >'.$social['name'].'</option>';
                                                        }
                                                     ?>
                                                </select>
                                              </div>
                                              <div class="col-md-9">
                                                <?php $website=( isset($addressbook) ? $addressbook->website : ''); ?>
                                                  <?php echo render_input( 'website[0][url]', 'Address',$website); ?>
                                              </div>
                                          </div>
                                        <div class="col-md-12 text-right" style="margin-top:10px">
                                             <button id="website-add-more" name="website-add-more" class="btn btn-primary">Add More</button>
                                        </div>
                                        <div class="col-md-12">
                                          <h4>Address</h4>
                                          <hr>
                                        </div>
                                          <div id="address-0">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                  <label for="address[0][type]" class="control-label">Type</label>
                                                  <select name="address[0][type]" id="address[0][type]" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                      <?php
                                                          echo '<option value="primary" selected="selected">Primary</option>';
                                                       ?>
                                                  </select>
                                                </div>
                                                <div class="col-md-5">
                                                  <div id="locationField" class="form-group">
                                                    <label class="control-label" for="address">Address</label>
                                                    <input id="autocomplete0" class="form-control searchmap"  data-addmap="0" placeholder="Search Google Maps..." onFocus="geolocate()" type="text">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group" style="margin-top:28px;">
                                                    <button type="button" class="btn btn-info custom_address customadd-0" style="display:block" data-addressid="0">Custom Address</button>
                                                    <button type="button" class="btn btn-default remove_address removeadd-0" style="display:none" data-addressid="0">Remove & Search Again</button>
                                                  </div>
                                                </div>
                                            </div>
                                             <?php
                                                if(isset($addressbook)){
                                                  if(!empty($addressbook->address) || !empty($addressbook->address2) || !empty($addressbook->city) || !empty($addressbook->state) || !empty($addressbook->zip)){
                                                        $style = 'style="display:block"';
                                                    }else{
                                                        $style = 'style="display:none"';
                                                    }
                                                }else{
                                                  $style = 'style="display:none"';
                                                }
                                             ?>
                                             <div class="addressdetails customaddress-0" <?php echo $style; ?> >
                                                 <div class="col-md-12"> 
                                                    <div class="col-md-6">
                                                        <?php $address=( isset($addressbook) ? $addressbook->address : ''); ?>
                                                        <?php echo render_input( 'address[0][street_number]', 'Street Address',$address); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php $address2=( isset($addressbook) ? $addressbook->address2 : ''); ?>
                                                        <?php echo render_input( 'address[0][route]', 'Address2',$address2); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="col-md-6">
                                                      <?php $city=( isset($addressbook) ? $addressbook->city : ''); ?>
                                                      <?php echo render_input( 'address[0][locality]', 'client_city',$city); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php $state=( isset($addressbook) ? $addressbook->state : ''); ?>
                                                        <?php echo render_input( 'address[0][administrative_area_level_1]', 'client_state',$state); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-12"> 
                                                    <div class="col-md-6">
                                                      <?php $zip=( isset($addressbook) ? $addressbook->zip : ''); ?>
                                                      <?php echo render_input( 'address[0][postal_code]', 'client_postal_code',$zip); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                      <div class="form-group">
                                                        <label for="address[0][country]" class="control-label">Country</label>
                                                        <select name="address[0][country]" id="address[0][country]" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                            <option value="US" selected>United States</option>
                                                        </select>
                                                      </div>
                                                    </div>
                                                </div>
                                            </div> 
                                          </div>
                                        <div class="col-md-12 text-right" style="margin-top:10px">
                                             <button id="address-add-more" name="address-add-more" class="btn btn-primary">Add More</button>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div id="existing-client-book">
                                <?php 
                                    if(isset($clients)) { 
                                        echo render_select('clients',$clients, array('addressbookid','firstname','lastname'),'lead_add_edit_client'); 
                                 } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
                    <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>admin/leads'"><?php echo _l( 'Cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            <?php echo form_close(); ?>
        </div>
	</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
  
    //$('#new-address-book').toggle();
    $('#existing-client-book').toggle();

    $('input:radio').change(function() {
        if($(this).val() == 'new') {
            $('#new-address-book').toggle();
            $('#existing-client-book').hide();
        }

        if($(this).val() == 'existing') {
            $('#new-address-book').hide();
            $('#existing-client-book').toggle();            
        }
    });


    jQuery.validator.addMethod("greaterThan", function(value, element, params) {
      console.log(params);
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val()) 
                || (Number(value) > Number($(params).val())); 
        },'Must be greater than Event Start Date.');

	_validate_form($('.lead-form'),{name : 'required', eventtypeid: 'required', eventstartdatetime: 'required', eventenddatetime: 'required', status: 'required', source: 'required', budget : 'required', bookingconfidence: 'required', eventinquireon: 'required', eventdecideby: 'required', companyname : 'required',companytitle : 'required', firstname : 'required', lastname : 'required', clients : 'required','tags[]': 'required'});
    

    // Code for multiple email validation
      var createEmailValidation = function() {

          $(".multiemail .form-control").each(function(index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
              email: true,
              required: true,
              remote:{
                 url: site_url + "admin/misc/addressbook_email_exists",
                 type:'post',
                 data: {
                  email:function(){
                     return $(value).val();
                  },
                  addressbookid:function(){
                   return $('input[name="addressbookid"]').val();
                  }
               }},
              messages: {
                email: "Please enter valid email.",
                required: "Please enter an email adress.",
                remote: "Email already exist."
              }
            });
          });  
      }

      // Code for multiple phone validation
      var createPhoneValidation = function() {
          $(".multiphone .form-control").each(function() {            
            $(this).mask("(999) 999-9999", {placeholder: "(___) ___-____"});          
        });
      }
    showcompany();

    function showcompany(){
        if($('#company').is(":checked"))   
            $(".companydetails").show();
        else
            $(".companydetails").hide();
    }

    $('#company').on('click', function(){
        showcompany();
    });  

   //Added By Avni on 10/18/2017     
    $('#eventstartdate #eventstartdatetime').change(function(e){  
        var selected = e.target.value;
        //var dt = new Date(selected);             
        //var ndt=new Date( dt.setHours(dt.getHours() + 1)); 
        //console.log(ndt);             
        $('#eventenddate #eventenddatetime').val(selected);

    });

    $("#eventenddatetime").rules('add', { greaterThan: "#eventstartdatetime" });

    $('.custom_address').on('click', function(){
      var addressid = $(this).data('addressid');
      $(".customaddress-"+addressid).show();
   });  
   $('.remove_address').on('click', function(){
      var addressid = $(this).data('addressid');
      $("#autocomplete"+addressid).val('');
      $("#address["+ addressid +"][street_number]").val('');
      $("#address["+ addressid +"][route]").val('');
      $("#address["+ addressid +"][locality]").val('');
      $("#address["+ addressid +"][administrative_area_level_1]").val('');
      $("#address["+ addressid +"][postal_code]").val('');
      $(".customaddress-"+addressid).hide();
      $(this).hide();
      $(".customadd-"+addressid).show();
   }); 
</script>
<script>
    // This example displays an address form, using the autocomplete feature
    // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      
      // Start code of Add more / Remove email
      
      var email_phone_type = <?php echo json_encode($email_phone_type); ?>;
      $("#email-add-more").click(function(e){
        e.preventDefault();
        var my_email_fields = $("div[id^='email-']");
        var highestemail = -Infinity;
         $.each(my_email_fields, function(mindex, mvalue) {
            var fieldEmailNum = mvalue.id.split("-");
            highestemail = Math.max(highestemail, parseFloat(fieldEmailNum[1]));
         });
        var emailnext = highestemail;
        var addtoEmail = "#email-" + emailnext;
        var addRemoveEmail = "#email-" + (emailnext);
        
        emailnext = emailnext + 1;
       
        var newemailIn = "";
         newemailIn += ' <div class="col-md-12" id="email-'+ emailnext +'" name="email'+ emailnext +'"><div class="col-md-3"><label class="control-label" for="email['+ emailnext +'][type]">Type</label><select id="email['+ emailnext +'][type]" name="email['+ emailnext +'][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
         $.each(email_phone_type, function(etindex, etvalue) {
            newemailIn += '<option value="'+etindex+'">'+etvalue+'</option>';
         });
            
         newemailIn += '</select></div>';
         newemailIn += '<div class="col-md-7 multiemail"><div class="form-group"><label class="control-label" for="email['+ emailnext +'][email]"><small class="req text-danger">* </small>Email</label><input id="email['+ emailnext +'][email]" class="form-control" name="email['+ emailnext +'][email]" autocomplete="off" value="" type="email"></div>';
         newemailIn += '</div>';
         newemailIn += '<div class="col-md-2 text-right"><label class="control-label" for="">&nbsp;</label><button id="emailremove-' + (emailnext) + '" class="form-control btn btn-danger email-remove-me" >Remove</button></div></div>';
        var newemailInput = $(newemailIn);
        
        //var removeEmailButton = $(removeEmailBtn);
        $(addtoEmail).after(newemailInput);
       // $(addRemoveEmail).after(removeEmailButton);
        $("#email-" + emailnext).attr('data-source',$(addtoEmail).attr('data-source'));
        $("#count").val(emailnext);  

         $('.email-remove-me').click(function(e){
             e.preventDefault();
             var fieldEmailNum = this.id.split("-");
             var fieldEmailID = "#email-" + fieldEmailNum[1];
             $(fieldEmailID).remove();
         });
          $('.selectpicker').selectpicker('render');
          createEmailValidation();
      });
      
      createEmailValidation();

      $('.email-remove-me').click(function(e){
           e.preventDefault();
           var fieldEmailNum = this.id.split("-");
           var fieldEmailID = "#email-" + fieldEmailNum[1];
           $(fieldEmailID).remove();
      });
      // End code of Add more / Remove email

      // Start code of Add more / Remove phone
      
      var email_phone_type = <?php echo json_encode($email_phone_type); ?>;
      $("#phone-add-more").click(function(e){

        e.preventDefault();
        var my_phone_fields = $("div[id^='phone-']");
        var highestphone = -Infinity;
         $.each(my_phone_fields, function(mindex, mvalue) {
            var fieldphoneNum = mvalue.id.split("-");
            highestphone = Math.max(highestphone, parseFloat(fieldphoneNum[1]));
         });
        var phonenext = highestphone;
        var addtophone = "#phone-" + phonenext;
        var addRemovephone = "#phone-" + (phonenext);
        
        phonenext = phonenext + 1;
        var newphoneIn = "";
         newphoneIn += ' <div class="col-md-12" id="phone-'+ phonenext +'" name="phone'+ phonenext +'"><div class="col-md-3"><label class="control-label" for="phone['+ phonenext +'][type]">Type</label><select id="phone['+ phonenext +'][type]" name="phone['+ phonenext +'][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
         $.each(email_phone_type, function(epindex, epvalue) {
            newphoneIn += '<option value="'+epindex+'">'+epvalue+'</option>';
         });
            
         newphoneIn += '</select></div>';
         newphoneIn += '<div class="col-md-7 multiphone"><div class="form-group"><label class="control-label" for="phone['+ phonenext +'][phone]">Phone</label><input id="phone['+ phonenext +'][phone]" class="form-control" name="phone['+ phonenext +'][phone]" autocomplete="off" value="" type="text"></div>';
         newphoneIn += '</div>';
         newphoneIn += '<div class="col-md-2 text-right"><label class="control-label" for="">&nbsp;</label><button id="phoneremove-' + (phonenext) + '" class="form-control btn btn-danger phone-remove-me" >Remove</button></div></div>';
        var newphoneInput = $(newphoneIn);
        
        //var removephoneButton = $(removephoneBtn);
        $(addtophone).after(newphoneInput);
       // $(addRemovephone).after(removephoneButton);
        $("#phone-" + phonenext).attr('data-source',$(addtophone).attr('data-source'));
        $("#count").val(phonenext);  

         $('.phone-remove-me').click(function(e){
             e.preventDefault();
             var fieldPhoneNum = this.id.split("-");
             var fieldphoneID = "#phone-" + fieldPhoneNum[1];
             //$(this).parent('div').remove();
             $(fieldphoneID).remove();
         });
         createPhoneValidation();
          $('.selectpicker').selectpicker('render');
      });
      createPhoneValidation();
      $('.phone-remove-me').click(function(e){
           e.preventDefault();
           var fieldPhoneNum = this.id.split("-");
           var fieldphoneID = "#phone-" + fieldPhoneNum[1];
           //$(this).parent('div').remove();
           $(fieldphoneID).remove();
      });
      // End code of Add more / Remove phone

      // Start code of Add more / Remove website
      
      var website_type = <?php echo json_encode($socialsettings); ?>;
      $("#website-add-more").click(function(e){
       
        e.preventDefault();
        var my_website_fields = $("div[id^='website-']");
        var highestwebsite = -Infinity;
          $.each(my_website_fields, function(mindex, mvalue) {
          var fieldwebsiteNum = mvalue.id.split("-");
          highestwebsite = Math.max(highestwebsite, parseFloat(fieldwebsiteNum[1]));
        });
        var websitenext = highestwebsite;
        var addtowebsite = "#website-" + websitenext;
        var addRemovewebsite = "#website-" + (websitenext);
        websitenext = websitenext + 1;
        
        var newwebsiteIn = "";
         newwebsiteIn += ' <div class="col-md-12" id="website-'+ websitenext +'" name="website'+ websitenext +'"><div class="col-md-3"><label class="control-label" for="website['+ websitenext +'][type]">Type</label><select id="website['+ websitenext +'][type]" name="website['+ websitenext +'][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
         $.each(website_type, function(windex, wvalue) {
            newwebsiteIn += '<option value="'+wvalue['socialid']+'">'+wvalue['name']+'</option>';
         });
            
         newwebsiteIn += '</select></div>';
         newwebsiteIn += '<div class="col-md-7"><div class="form-group"><label class="control-label" for="website['+ websitenext +'][url]">Web Address</label><input id="website['+ websitenext +'][url]" class="form-control" name="website['+ websitenext +'][url]" autocomplete="off" value="" type="text"></div>';
         newwebsiteIn += '</div>';
         newwebsiteIn += '<div class="col-md-2 text-right"><label class="control-label" for="">&nbsp;</label><button id="websiteremove-' + (websitenext) + '" class="form-control btn btn-danger website-remove-me" >Remove</button></div></div>';
         var newwebsiteInput = $(newwebsiteIn);
        $(addtowebsite).after(newwebsiteInput);
        $("#website-" + websitenext).attr('data-source',$(addtowebsite).attr('data-source'));
        $("#count").val(websitenext);  

         $('.website-remove-me').click(function(e){
             e.preventDefault();
             var fieldwebsiteNum = this.id.split("-");
             var fieldwebsiteID = "#website-" + fieldwebsiteNum[1];
             $(fieldwebsiteID).remove();
         });
          $('.selectpicker').selectpicker('render');
      });
      $('.website-remove-me').click(function(e){
           e.preventDefault();
           var fieldwebsiteNum = this.id.split("-");
           var fieldwebsiteID = "#website-" + fieldwebsiteNum[1];
           $(fieldwebsiteID).remove();
      });
      // End code of Add more / Remove website

      // Start code of Add more / Remove address
      
      var address_type = <?php echo json_encode($address_type); ?>;
      $("#address-add-more").click(function(e){

        e.preventDefault();
        var my_address_fields = $("div[id^='address-']");
        var highestaddress = -Infinity;
         $.each(my_address_fields, function(mindex, mvalue) {
            var fieldaddressNum = mvalue.id.split("-");
            highestaddress = Math.max(highestaddress, parseFloat(fieldaddressNum[1]));
         });
        var addressnext = highestaddress;
        var addtoaddress = "#address-" + addressnext;
        var addRemoveaddress = "#address-" + (addressnext);
        
        addressnext = addressnext + 1;
        var newaddressIn = "";
         newaddressIn += ' <div id="address-'+ addressnext +'"><div class="col-md-12"><div class="col-md-3"><label for="address['+ addressnext +'][type]" class="control-label">Type</label><select name="address['+ addressnext +'][type]" id="address['+ addressnext +'][type]" class="form-control selectpicker" data-none-selected-text="Select">';
           $.each(address_type, function(aindex, avalue) {
              newaddressIn += '<option value="'+aindex+'">'+avalue+'</option>';
           });
            
         newaddressIn += '</select></div><div class="col-md-5"><div id="locationField" class="form-group"><label class="control-label" for="address">Address</label><input id="autocomplete'+ addressnext +'" class="form-control searchmap" data-addmap="'+addressnext+'" placeholder="Search Google Maps..." onfocus="geolocate()" type="text"></div></div><div class="col-md-4"><div class="form-group" style="margin-top: 28px;"><button type="button" class="btn btn-info custom_address customadd-'+addressnext+'" data-addressid="'+ addressnext +'">Custom Address</button><button type="button" class="btn btn-default remove_address removeadd-'+addressnext+'" data-addressid="'+ addressnext +'">Remove &amp; Search Again</button><button id="addressremove-' + (addressnext) + '" class="btn btn-danger address-remove-me" style="margin-left:5px">Remove</button></div></div></div>';
         newaddressIn += ' <div class="addressdetails customaddress-'+ addressnext +'" style="display:none"><div class="col-md-12"><div class="col-md-6"><div class="form-group"><label for="address['+ addressnext +'][street_number]" class="control-label">Street Address</label><input id="address['+ addressnext +'][street_number]" name="address['+ addressnext +'][street_number]" class="form-control" value="" type="text"></div></div><div class="col-md-6"><div class="form-group"><label for="address['+ addressnext +'][route]" class="control-label">Address2</label><input id="address['+ addressnext +'][route]" name="address['+ addressnext +'][route]" class="form-control" value="" type="text"></div></div></div><div class="col-md-12"><div class="col-md-6"><div class="form-group"><label for="address['+ addressnext +'][locality]" class="control-label">City</label><input id="address['+ addressnext +'][locality]" name="address['+ addressnext +'][locality]" class="form-control" value="" type="text"></div></div><div class="col-md-6"><div class="form-group"><label for="address['+ addressnext +'][administrative_area_level_1]" class="control-label">State</label><input id="address['+ addressnext +'][administrative_area_level_1]" name="address['+ addressnext +'][administrative_area_level_1]" class="form-control" value="" type="text"></div></div></div><div class="col-md-12"><div class="col-md-6"><div class="form-group"><label for="address['+ addressnext +'][postal_code]" class="control-label">Zip Code</label><input id="address['+ addressnext +'][postal_code]" name="address['+ addressnext +'][postal_code]" class="form-control" value="" type="text"></div></div><div class="col-md-6"><div class="form-group"><label for="address['+ addressnext +'][country]" class="control-label">Country</label><select name="address['+ addressnext +'][country]" id="address['+ addressnext +'][country]" class="form-control selectpicker" data-none-selected-text="Select" ><option value="US" selected="">United States</option></select></div></div></div></div>';
         
        newaddressIn += '</div></div>';
        var newaddressInput = $(newaddressIn);
        
       // var removeaddressButton = $(removeaddressBtn);
        $(addtoaddress).after(newaddressInput);
        
        //$(addRemoveaddress).after(removeaddressButton);
        $("#address-" + addressnext).attr('data-source',$(addtoaddress).attr('data-source'));
        $("#count").val(addressnext);  
          $(".removeadd-"+addressnext).hide();
          $('.custom_address').on('click', function(){
            var addressid = $(this).data('addressid');
            $(".customaddress-"+addressid).show();
         });  
         $('.remove_address').on('click', function(){
            var addressid = $(this).data('addressid');
            $("#autocomplete"+addressid).val('');
            $("#address["+ addressid +"][street_number]").val('');
            $("#address["+ addressid +"][route]").val('');
            $("#address["+ addressid +"][locality]").val('');
            $("#address["+ addressid +"][administrative_area_level_1]").val('');
            $("#address["+ addressid +"][postal_code]").val('');
            $(".customaddress-"+addressid).hide();
            $(this).hide();
            $(".customadd-"+addressid).show();
         }); 
       
         $('.address-remove-me').click(function(e){
             e.preventDefault();
             var fieldaddressNum = this.id.split("-");
             var fieldaddressID = "#address-" + fieldaddressNum[1];
             $(fieldaddressID).remove();
         });
          $('.selectpicker').selectpicker('render');
          $(".searchmap").on("keyup, change, keypress, keydown, click",function(){
          // alert("here");
              var searchmapid = $(this).data('addmap');
              initAutocomplete(searchmapid);
          });
      });
      $('.address-remove-me').click(function(e){
           e.preventDefault();
           var fieldaddressNum = this.id.split("-");
           var fieldaddressID = "#address-" + fieldaddressNum[1];
           $(fieldaddressID).remove();
      });

      $(".searchmap").on("keyup, change, keypress, keydown, click",function(){
       // alert("here1");
          var searchmapid = $(this).data('addmap');
          initAutocomplete(searchmapid);
      });

      var placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'short_name',
        postal_code: 'short_name'
      };

      // function initAutocomplete() {
      //     // Create the autocomplete object, restricting the search to geographical
      //     // location types.
      //    autocomplete = new google.maps.places.Autocomplete(
      //         /** @type {!HTMLInputElement} */(document.getElementById('autocomplete0')),
      //         {types: ['geocode'],  componentRestrictions: {country: 'us'}});

      //     // When the user selects an address from the dropdown, populate the address
      //     // fields in the form.
      //     autocomplete.addListener('place_changed', fillInAddress);
      // }

      function initAutocomplete(addid) {
          // Create the autocomplete object, restricting the search to geographical
          // location types.
         // alert(addid);
          addid = addid;
          
          //alert(addid);
          
          autocomplete = new google.maps.places.Autocomplete(
              /** @type {!HTMLInputElement} */(document.getElementById('autocomplete'+addid)),
              {types: ['geocode'],  componentRestrictions: {country: 'us'}});

          // When the user selects an address from the dropdown, populate the address
          // fields in the form.
          autocomplete.addListener('place_changed', function () {
          //google.maps.event.addListener(autocomplete, 'place_changed', function () {
              var place = autocomplete.getPlace();

              for (var component in componentForm) {
                document.getElementById("address["+addid+"]["+component+"]").value = '';
                document.getElementById("address["+addid+"]["+component+"]").disabled = false;
              }

              // Get each component of the address from the place details
              // and fill the corresponding field on the form.
              for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                  var val = place.address_components[i][componentForm[addressType]];
                  document.getElementById("address["+addid+"]["+addressType+"]").value = val;
                }
              }
              $(".customaddress-"+addid).show();
              $(".customadd-"+addid).hide();
              $(".removeadd-"+addid).show();
          });

      }
      function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                
                autocomplete.setBounds(circle.getBounds());
            });
        }
      }
      
      // End code of Add more / Remove address
     
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-0SSogvGqWSro2pyjAlek2DP_lwfQMvE&libraries=places&callback=initAutocomplete"></script>
</body>
</html>