$(function(){

    $('input[name="send_questionnaire_to[clients]"]').on('change',function(){
        $('.customer-groups').slideToggle();
    });
   
    // Init questions sortable
    var questions_sortable = $("#questionnaire_questions").sortable({
        placeholder: "ui-state-highlight-survey",
        update: function() {
            // Update question order
            update_questions_order();
        }
    });
    $("body").on('click', '.question_body_toggle', function(){
        $('i',this).toggleClass('fa-caret-up fa-caret-down');
        $(this).parent().siblings('.question_body').slideToggle();
    });
    $("body").on('change', '.que_file_upload input[type=file]', function(){
        if (this.files[0].name!="") {
            var file_name = this.files[0].name;
            $(this).siblings('.drag_drop_file').children('span.file_name').text(file_name);
        }
    });
    $('a.clicktoaddimage').click(function () {
        $(this).parent().siblings().children('input[type=file]').trigger('click');
    });
});

// New question
function add_question(type, id) {
    qdata = {'type':type,'id':id};
    $.ajax({
        type:'POST',
        url:admin_url + 'questionnaire/add_question',
        data:qdata,
        success:function(result){
            $('#questionnaire_questions').append(result);
            $("#questionnaire_questions").sortable('refresh');
            $('html,body').animate({
                    scrollTop: $(document).height()},
                'slow');
            $(".selectpicker").selectpicker('refresh');
            update_questions_order();
        }
    });
}
/*function add_question(type, id) {
    $.post(admin_url + 'questionnaire/add_question', {
        type: type,
        id: id
    }).done(function(response) {
        response = JSON.parse(response);
        question_area = '<li>';
        question_area += '<div class="form-group question">';
        question_area += '<div class="checkbox checkbox-primary required">';
        question_area += '<input type="checkbox" data-question_required="' + response.data.questionid + '" name="required[]" onchange="update_question(this,\'' + type + '\',' + response.data.questionid + ')">';
        question_area += '<label>' + response.question_required + '</label>';
        question_area += '</div>';
        question_area += hidden_input('order[]', '');
        // used only to identify input key no saved in database
        question_area += '<label for="' + response.data.questionid + '" class="control-label display-block">'+response.question_string+' <a href="#" onclick="update_question(this,\'' + type + '\',' + response.data.questionid + '); return false;" class="pull-right update-question-button"><i class="fa fa-refresh text-success question_update"></i></a><a href="#" class="pull-right"><i class="fa fa-remove text-danger" onclick="remove_question_from_database(this,' + response.data.questionid + '); return false;"></i></a></label>';
        question_area += '<input type="text" onblur="update_question(this,\'' + type + '\',' + response.data.questionid + ');" data-questionid="' + response.data.questionid + '" class="form-control questionid">';
        // if (type == 'textarea') {
        //     question_area += '<textarea class="form-control mtop20" disabled="disabled" rows="6">' + response.survey_question_only_for_preview + '</textarea>';
        // } else 
        if (type == 'checkbox' || type == 'radio' || type=='select') {
            question_area += '<div class="row">';
            box_description_icon_class = 'fa-plus';
            box_description_function = 'add_box_description_to_database(this,' + response.data.questionid + ',' + response.data.boxid + '); return false;';
            question_area += '<div class="box_area">';
            question_area += '<div class="col-md-12">';
            question_area += '<a href="#" class="add_remove_action survey_add_more_box" onclick="' + box_description_function + '"><i class="fa ' + box_description_icon_class + '"></i></a>';
            question_area += '<div class="' + type + ' ' + type + '-primary">';
            question_area += '<input type="' + type + '" disabled="disabled"/>';
            question_area += '<label><input onblur="update_question(this,\'' + type + '\',' + response.data.questionid + ');" type="text" data-box-descriptionid="' + response.data[0].questionboxdescriptionid + '" class="survey_input_box_description"></label>';
            question_area += '</div>';
            question_area += '</div>';
            question_area += '</div>';
            // end box row
            question_area += '</div>';
        } else if (type == 'heading') {
            question_area += '<select name="heading" class="selectpicker" onchange="update_question(this,\'<?php echo $question[\'boxtype\']?>\',<?php echo $question[\'questionid\'] ?>);">\n' +
                '                <option value="">Select heading tag</option>\n' +
                '                <option value="h1">H1</option>\n' +
                '                <option value="h2">H2</option>\n' +
                '                <option value="h3">H3</option>\n' +
                '                <option value="h4">H4</option>\n' +
                '                <option value="h5">H5</option>\n' +
                '                <option value="h6">H6</option>\n' +
                '            </select>';
        }else {
            //question_area += '<input type="text" onchange="update_question(this,\'' + type + '\',' + response.data.questionid + ');" class="form-control mtop20" disabled="disabled" value="' + response.survey_question_only_for_preview + '">';
        }
        question_area += '</div>';
        question_area += '</li>';
        $('#questionnaire_questions').append(question_area);
        $("#questionnaire_questions").sortable('refresh');
        $('html,body').animate({
            scrollTop: $("#questionnaire_questions li:last-child").offset().top},
            'slow');
        update_questions_order();
    });
}*/
// Update question when user click on reload button
function update_question(question, type, questionid) {
    $(question).parents('li').find('i.question_update').addClass('spinning');
    var data = {};
    var _question = $(question).parents('.question').find('input[data-questionid="' + questionid + '"]').val();
    var _required="";
    if (type !='heading') {
        _required = $(question).parents('.question').find('input[data-question_required="' + questionid + '"]').prop('checked');
    }

    data.question = {
        value: _question,
        required: _required
    };

    data.questionid = questionid;
    if (type == 'checkbox' || type == 'radio' || type=='select' || type=='heading') {
        var tempData = [];
        var boxes_area = $(question).parents('.question').find('.box_area');

        $.each(boxes_area, function() {
            var boxdescriptionid = $(this).find('input.input_box_description').data('box-descriptionid');
            var boxdescription = $(this).find('input.input_box_description').val();
            if(type=='heading'){
                boxdescriptionid = $(this).find('select.input_box_description').data('box-descriptionid');
                boxdescription = $(this).find('select.input_box_description').val();
            }
            var _temp_data = [boxdescriptionid, boxdescription];
            tempData.push(_temp_data);
        });

        data.boxes_description = tempData;
    }

    setTimeout(function() {
        $.post(admin_url + 'questionnaire/update_question', data).done(function(response) {
            $(question).parents('li').find('i.question_update').removeClass('spinning');
        });
    }, 10);
}

// Add more boxes to already added question // checkbox // radio box
function add_more_boxes(question, boxdescriptionid) {
    var box = $(question).parents('.box_area').clone();
    $(question).parents('.question').find('.box_area').last().after(box);
    $(box).find('i').removeClass('fa-plus').addClass('fa-minus').addClass('text-danger');
    $(box).find('input.input_box_description').val('');
    $(box).find('input.input_box_description').attr('data-box-descriptionid', boxdescriptionid);
    $(box).find('input.input_box_description').focus();
    $(box).find('.add_remove_action').attr('onclick', 'remove_box_description_from_database(this,' + boxdescriptionid + '); return false;');
    update_questions_order();

}
// Remove question from database
function remove_question_from_database(question, questionid) {
    $.get(admin_url + 'questionnaire/remove_question/' + questionid, function(response) {
        if (response.success == false) {
            alert_float('danger', response.message);
        } else {
            $(question).parents('.question').remove();
            update_questions_order();
        }
    }, 'json');
}
// Remove question box description  // checkbox // radio box
function remove_box_description_from_database(question, questionboxdescriptionid) {
    $.get(admin_url + 'questionnaire/remove_box_description/' + questionboxdescriptionid, function(response) {
        if (response.success == true) {
            $(question).parents('.box_area').remove();
        } else {
            alert_float('danger', response.message);
        }
    }, 'json');
}
// Add question box description  // checkbox // radio box
function add_box_description_to_database(question, questionid, boxid) {
    $.get(admin_url + 'questionnaire/add_box_description/' + questionid + '/' + boxid, function(response) {
        if (response.boxdescriptionid !== false) {
            add_more_boxes(question, response.boxdescriptionid);
        } else {
            alert_float('danger', response.message);
        }
    }, 'json');
}
// Updating question order // called when drop event called
function update_questions_order() {
    var questions = $('#questionnaire_questions').find('.question');
    var i = 1;
    $.each(questions, function() {
        $(this).find('input[name="order[]"]').val(i);
        i++;
    });
    var update = [];
    $.each(questions, function() {
        var questionid = $(this).find('input.questionid').data('questionid');
        var order = $(this).find('input[name="order[]"]').val();
        update.push([questionid, order])
    });
    data = {};
    data.data = update;
    $.post(admin_url + 'questionnaire/update_questions_orders', data);
}

function upload_image(input,type,id,desc_id,image) {
    var extension = input.files[0].name.substr((input.files[0].name.lastIndexOf('.') +1));
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            if(extension=='jpg' || extension=='jpeg' || extension=='png' || extension=='gif'){
                $(input).parent().siblings('.imageview').children('img').attr('src', e.target.result);
                $(input).parent('.clicktoaddimage').addClass('hidden');
                $(input).parent().siblings('.imageview').removeClass('hidden');
            }
        };
        var form_data = new FormData();
        form_data.append('file', input.files[0]);
        form_data.append('type', type);
        form_data.append('questionid', id);
        form_data.append('desc_id', desc_id);
        form_data.append('image', image);
        $.ajax({
            url: admin_url + 'questionnaire/upload_image', // point to server-side PHP script
            dataType: 'text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(response){
                response = response.split('#');
                if(response[0]=='success'){
                    $(input).attr('onchange',"upload_image(this,'image',"+id+","+desc_id+",'"+response[1]+"')");
                    alert_float('success',"Image Uploaded");
                }else if(response[0]=='ext'){
                    alert_float('warning',"Extension is invalid");
                }else {
                    alert_float('warning',"Something went wrong");
                }
            }
        });
        reader.readAsDataURL(input.files[0]);
    }
}

function duplicate_question(id,index){
    qdata = {'id':id};
    $.ajax({
        type:'POST',
        url:admin_url + 'questionnaire/copy_question',
        data:qdata,
        success:function(result){
            $('#question_'+index).after(result);
            $("#questionnaire_questions").sortable('refresh');
            $('html,body').animate({
                    scrollTop: $(document).height()},
                'slow');
            $(".selectpicker").selectpicker('refresh');
            update_questions_order();
        }
    });
}