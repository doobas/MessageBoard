
$(document).ready(function(){

    $("#message-form").on('submit', function(e){
        e.preventDefault();

        $(this).children("p").children("input, textarea").attr("disabled", "disabled");
        $("#loader").fadeIn();
        clearErrors();

        $.post(MESSAGE_FORM_POST,
            {
                fullname: $("#fullname").val(),
                birthdate: $("#birthdate").val(),
                email: $("#email").val(),
                message: $("#message").val()
            },
            function(data, status){
                if(status && status === 'success') {
                    if(data && data.errors) {
                        fillErrors(data.errors);
                        $("#message-form").children("p").children("input, textarea").removeAttr("disabled");
                        $("#loader").fadeOut();
                        return;
                    }
                    if(data && data.message) {
                        insertNewMessage(data.message);
                        clearFields();
                    }
                }

            }, 'json');

        $("#loader").fadeOut();
        $(this).children("p").children("input, textarea").removeAttr("disabled");

    })

    function fillErrors(errors) {
        $.each(errors, function( index, value ) {
            $("#"+index).parent("p").addClass("err")
            $.each(value, function ( key, error) {
                $("#"+index).parent("p").append("<span class='err-message'>"+error+"</span><br>");
            });
        });
    }

    function clearErrors() {
        $(".err-message").next("br").remove();
        $(".err-message").remove();
        $(".err").removeClass("err");
    }

    function insertNewMessage(message) {
        removeLastMessage();
        var html = "<li style='display:none; background-color: #d3d3d3;'><span>"+message.createdAt+"</span>";
        if (message.email) {
            html += "<a href='mailto:"+message.email+"'>" + message.firstName + " " + message.lastName +"</a>";
        } else {
            html += message.firstName + " " + message.lastName;
        }
        html += ", " + message.age + "m. <br>";
        html += message.message;
        html += "</li>";
        $("#messagesContainer").prepend(html);
        $("#messagesContainer").children("li").first().fadeIn(800).animate({backgroundColor: "#ffffff"}, 5000);
    }

    function removeLastMessage() {
        $("#messagesContainer").children("li").last().remove();
    }

    function clearFields() {
        $("#fullname, #birthdate, #email, #message").val("");
    }

});
