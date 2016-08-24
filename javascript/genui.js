/* HOW THIS WORKS */
/*
changing or clicking the update button triggers the reset_1 or reset_2 functions which start a timeout with 450ms delay. if they're called again before that delay is up they reset it. this prevents each new character from creating a request to the image.php script
*/
var update_timer;

function reset_timer(callback){
  if(update_timer){
    //reset the timer so we dont query again
    clearTimeout(update_timer);
    console.log('interupted timer');
  }
    update_timer = setTimeout(callback, 450);
}

function replace_image(form) {
  var template = $(form).find("[name=template]").val();
  var fields = $(form).find("[name=gen_text]");
  if (fields.length === 1) {
    message = $(form).find("[name=gen_text]").val();
    $(form).find("img").attr("src", "/image.php?t="+template+"&message="+message);
  } else {
    fields = $(form).find("[name='gen_text[]']");
    var src = "/image.php?t="+template+"&";
    fields.each(function(i, o) {
      src += "m" + (i+1) + "=" + $(o).val() + "&";
    });
    $(form).find("img").attr("src", src);
  }
}

function reset(form){
    reset_timer(function() { replace_image(form); });
}

$(document).ready(function(){
    $("[name=gen_text], [name='gen_text[]'").change(function() { reset($(this).closest("form")); });
    $("[name=gen_text], [name='gen_text[]'").keyup(function() { reset($(this).closest("form")); });
    $('.gen_button').click(function() { reset($(this).closest("form")); });

  $('#more_info').click(function(){
    $('#more_info_form_wrapper').show();
  });
  $('#more_info_cancel').click(function(){
    $('#more_info_form_wrapper').hide();
  });
});
