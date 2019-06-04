<?php $content = (isset($agreement) ? $agreement->content : ''); ?>
<div class="form-group">
	<label for="content" class="control-label"> <small class="req text-danger">* </small>Content</label>
	<div id="agreement_wrapper">
   		<textarea id="content" name="content"><?php echo $content; ?></textarea>
	</div>
</div>
<script type="text/javascript">
	CKEDITOR.replace( 'content', {
      	toolbar: [
        	[ 'Font', 'FontSize', 'TextColor' ,'BGColor', 'Bold', 'Italic',  'Underline','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'BulletedList', 'NumberedList' ,'Link', 'TextField']
      	]
    });
</script>