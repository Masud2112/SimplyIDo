<?php include_once(APPPATH.'views/admin/includes/helpers_bottom.php'); ?>
<?php do_action('before_js_scripts_render'); ?>
<script src="<?php echo base_url('assets/plugins/app-build/vendor.js?v='.get_app_version()); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery/jquery-migrate.'.(ENVIRONMENT === 'production' ? 'min.' : '').'js?v='.get_app_version()); ?>"></script>
<?php //echo app_script('assets/js','pace.js'); ?>
<script src="<?php echo base_url('assets/plugins/datatables/datatables.min.js?v='.get_app_version()); ?>"></script>
<script src="<?php echo base_url('assets/plugins/app-build/moment.min.js?v='.get_app_version()); ?>"></script>
<?php app_select_plugin_js($locale); ?>
<script src="<?php echo base_url('assets/plugins/tinymce/tinymce.min.js?v='.get_app_version()); ?>"></script>
<?php app_jquery_validation_plugin_js($locale); ?>
<?php if(get_option('dropbox_app_key') != ''){ ?>
<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js?v=<?php echo get_app_version(); ?>" id="dropboxjs" data-app-key="<?php echo get_option('dropbox_app_key'); ?>"></script>
<?php } ?>
<script src="<?php echo base_url('assets/plugins/lightbox/js/lightbox.min.js?v='.get_app_version()); ?>"></script>

<script src="https://cdn.jsdelivr.net/picturefill/2.3.1/picturefill.min.js"></script>
<script src="<?php echo base_url('assets/plugins/lightGallery/dist/js/lightgallery-all.min.js?v='.get_app_version()); ?>"></script>
<script src="<?php echo base_url('assets/plugins/lightGallery/lib/jquery.mousewheel.min.js?v='.get_app_version()); ?>"></script>

<?php if(isset($form_builder_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/form-builder/form-builder.js?v='.get_app_version()); ?>"></script>
<?php } ?>
<?php if(isset($files_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/elFinder/js/elfinder.min.js?v='.get_app_version()); ?>"></script>
<?php if(file_exists(FCPATH.'assets/plugins/elFinder/js/i18n/elfinder.'.get_media_locale($locale).'.js') && get_media_locale($locale) != 'en'){ ?>
<script src="<?php echo base_url('assets/plugins/elFinder/js/i18n/elfinder.'.get_media_locale($locale).'.js'); ?>"></script>
<?php } ?>
<?php } ?>
<?php if(isset($projects_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-comments/js/jquery-comments.min.js?v='.get_app_version()); ?>"></script>
<script src="<?php echo base_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js?v='.get_app_version()); ?>"></script>
<?php } ?>
<?php if(isset($circle_progress_asset)){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-circle-progress/circle-progress.min.js?v='.get_app_version()); ?>"></script>
<?php } ?>
<?php if(isset($accounting_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/accounting.js/accounting.min.js?v='.get_app_version()); ?>"></script>
<?php } ?>
<?php if(isset($calendar_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.js?v='.get_app_version()); ?>"></script>
<?php if(get_option('google_api_key') != ''){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/gcal.min.js?v='.get_app_version()); ?>"></script>
<?php } ?>
<?php if(file_exists(FCPATH.'assets/plugins/fullcalendar/locale/'.$locale.'.js') && $locale != 'en'){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/locale/'.$locale.'.js'); ?>"></script>
<?php } ?>
<?php } ?>
<?php //if(isset($accounting_assets)){ ?>
<!-- <script type="text/javascript">
var CKEDITOR_BASEPATH = '<?php echo $_SERVER["DOCUMENT_ROOT"]."/SimplyIDO/Development/assets/plugins/ckeditor/"?>';
//echo CKEDITOR_BASEPATH;exit;
</script> -->
<!-- <script src="https://cdn.ckeditor.com/4.4.5.1/full/ckeditor.js"></script> -->
<script src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.2/jquery.ui.touch-punch.min.js"></script>
<?php //} ?>
<?php echo app_script('assets/js','jquery.toast.js'); ?>
<?php echo app_script('assets/js','sweetalert2.js'); ?>
<?php echo app_script('assets/js','jquery.mask.js'); ?>
<?php echo app_script('assets/plugins/Croppie-master','croppie.js'); ?>
<?php echo app_script('assets/js','main.js'); ?>

<?php
/**
 * Global function for custom field of type hyperlink
 */
echo get_custom_fields_hyperlink_js_function(); ?>
<?php
/**
 * Outputs function global for ajax search
 */
app_admin_ajax_search_function();
?>
<?php
/**
 * Check for any alerts stored in session
 */
app_js_alerts();
?>
<?php
/**
 * Check pusher real time notifications
 */
if(get_option('pusher_realtime_notifications') == 1){ ?>
<script src="https://js.pusher.com/4.1.0/pusher.min.js?v=<?php echo get_app_version();?>"></script>
<script type="text/javascript">
   // Enable pusher logging - don't include this in production
   // Pusher.logToConsole = true;
   <?php $pusher_options = do_action('pusher_options',array());
   if(!isset($pusher_options['cluster']) && get_option('pusher_cluster') != ''){
     $pusher_options['cluster'] = get_option('pusher_cluster');
   } ?>
   var pusher_options = <?php echo json_encode($pusher_options); ?>;
   var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
   var channel = pusher.subscribe('notifications-channel-<?php echo get_staff_user_id(); ?>');
   channel.bind('notification', function(data) {
      fetch_notifications();
   });
</script>
<?php } ?>
<?php
  /**
  * Added By : Vaidehi
  * Dt: 10/23/2017
  * to display color picker on brand settings page
  */
?>
<script>
  var pickers = $('.colorpicker-component');
  $(function() {
    <?php
    /**
    * Added By : Vaidehi
    * Dt: 10/12/2017
    * to update brand id in session
    */
    ?>

    /*$(".brand").click(function(){
      
    });*/
    $.each(pickers, function() {
      $(this).colorpicker({
        format: "hex"
      });

      $(this).colorpicker().on('changeColor', function(e) {
        var color = e.color.toHex();
        var _class = 'custom_style_' + $(this).find('input').data('id');
        var val = $(this).find('input').val();
        if (val == '') {
          $('.' + _class).remove();
          return false;
        }
        var append_data = '';
        var additional = $(this).data('additional');
        additional = additional.split('+');
        if (additional.length > 0 && additional[0] != '') {
          $.each(additional, function(i, add) {
            add = add.split('|');
            append_data += add[0] + '{' + add[1] + ':' + color + ';}';
          });
        }

        append_data += $(this).data('target') + '{' + $(this).data('css') + ':' + color + ';}';
        if ($('head').find('.' + _class).length > 0) {
          $('head').find('.' + _class).html(append_data);
        } else {
          $("<style />", {
            class: _class,
            type: 'text/css',
            html: append_data
          }).appendTo("head");
        }
      });
    });
  });

  /**
  * Added By : Vaidehi
  * Dt : 10/24/2017
  * to load theme images for preview based on theme selection
  */
  $(".btn-preview").click(function(){
    setTimeout(function() {
      if($("#ModalCarousel").is(":visible")) {
        var theme = $('#clients_default_theme').val();
        var newSrc = "<?php echo base_url('uploads/theme_images/');?>"+theme+"<?php echo '_dashboard.png'; ?>";
        $("#img-dashboard").attr('src', newSrc);
        var title = jsUcfirst(theme) + " " + "<?php echo _l('btn_preview'); ?>";
        $(".modal-title").html(title);
      } else {
        $("#carousel-modal-demo").html("No Images found for theme");
      }
    }, 200);
  });

  function jsUcfirst(string) 
  {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  function changeBrand(d) {
    var brandid = d.getAttribute("data-id");
    if(brandid > 0){
      $.ajax({
        url: "<?php echo admin_url('home/updatebrand'); ?>",
        method: "post",
        data: "brandid="+brandid,
        success: function(data) {
          window.location.href = '<?php echo admin_url();?>';
        }
      });
    }
  }
</script>
<!-- livezilla.net PLACE SOMEWHERE IN BODY -->
<!--<script type="text/javascript" id="lzdefsc" src="//172.16.1.51/SimplyIDo/Development/livezilla/script.php?id=lzdefsc" defer></script>-->
<!-- livezilla.net PLACE SOMEWHERE IN BODY -->
<!--Start of Zendesk Chat Script-->
<!--<script type="text/javascript">
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
        d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
        $.src="https://v2.zopim.com/?6POBhrA9yegRWfvPtf1vsSbRRWK6CnBt";z.t=+new Date;$.
            type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>-->

<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5c8f34ab101df77a8be3134a/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>
<!--End of Tawk.to Script-->

<?php
/**
 * End users can inject any javascript/jquery code after all js is executed
 */
do_action('after_js_scripts_render');
?>
<footer class="footer text-center clearfix"><span>SiDO - <?php echo get_app_version(); ?></span><br><?php echo date('Y')?> © Simply I Do, Inc. Events Planning. Streamlined!</footer>