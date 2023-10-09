$(document).ready(function(){

    $("#fixed").hide();
    $("#MCQ").css("display", "none");
    $("#text").css("display", "none");
        $('#fixed_answer').on('click', function () {
            $("#fixed").show();
            $("#MCQ").find("input").val("").end().hide();
            $("#text").find("input").val("").end().hide();
        });
        $('#multiple_answers').on('click', function () {
            $("#MCQ").show();
            $("#fixed").find("input").val("").end().hide();
            $("#text").find("input").val("").end().hide();
        });
        $('#text_answer').on('click', function () {
            $("#fixed").find("input").val("").end().hide();
            $("#MCQ").find("input").val("").end().hide();
        });
    
    
    //add new answer field  for multiple answers  
    $('#add_answer_check_box').on('click', function () {
        $('#add_answer_MCQ').clone().insertAfter("#add_answer_MCQ");
    });

    //add new answer field  for single answers  
    $('#add_answer_fixed_button').on('click', function () {
        $('#add_answer_fixed').clone().insertAfter("#add_answer_fixed");
    });

   //validate numeric
    $("#myTextBox").bind('keypress', function (e) {
        var keyCode = e.which ? e.which : e.keyCode
        if (!(keyCode >= 48 && keyCode <= 57)) {
            $("#emcheck").show();
            return false;
        }else if((keyCode >= 48 && keyCode <= 57)){
            $("#emcheck").hide();
        }
    });

    //Delete dynamic fields
    $(document).on('click', '#removeRow', function () {
        $(this).closest('#add_answer_fixed').detach();
        $(this).closest('#add_answer_MCQ').detach();
    });

    /*show delete button
    $('#delButton').hide();
    $('#imgD').hide();    
    var imgValShow = $.trim($('#imgShow').val()); 
    if(imgValShow !==''){
        $('#delButton').show();
        $('#imgD').show();
    }*/

});

//change radio button values
$(document).on("click", ".radio_reset_cls", function () {
    var index = 0;
    $(".radio_reset_cls").each(function() {
        $(this).val(index);
        index++;
     });
})

$(document).on("click", ".checkbox_reset_cls",function () {
    var index = 0;
    $(".checkbox_reset_cls").each(function() {
        $(this).val(index);
        index++;
     });


});