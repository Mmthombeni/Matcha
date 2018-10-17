//INSERT INTO `chats` (`id`, `user_id_from`, `user_id_to`, `message`, `date_updated`) VALUES (NULL, '1', '2', 'hello, wassup', CURRENT_TIMESTAMP), (NULL, '2', '1', 'hey yourself', CURRENT_TIMESTAMP);
//SELECT * FROM `chats` WHERE (user_id_from = 1 AND user_id_to = 2) OR (user_id_from = 2 AND user_id_to = 1) ORDER BY date_updated
//INSERT INTO `chats` (`id`, `user_id_from`, `user_id_to`, `message`, `date_updated`) VALUES (NULL, '1', '2', 'Sorry I don\'t know what you talking about.', CURRENT_TIMESTAMP), (NULL, '1', '2', 'Please explain yourself?', CURRENT_TIMESTAMP);

(function(){
    setInterval(getChats, 2000);

    document.getElementById("message-to-send").addEventListener("keyup", function(e){
        if (e.keyCode === 13){
            let userid = $("#userid").val();
            
            sendMessage(userid);
        }
    });

    $("#sendMssgBtn").on("click", function(){
        let userid = $("#userid").val();

        sendMessage(userid);
    });
})();

function getChats(){
    let userid = $("#userid").val();
    var me = $("#me").val();
    var friend = $("#friend").val();

    $.get(`getChats.php?userid=${userid}`, function(data, status){
        if (status === 'success'){
            try {
                let response = JSON.parse(data);

                $("#chat_container").html('');
                response.forEach(function(value, key) {
                    if (value.user_id_from !== userid){
                        $("#chat_container").append(`
                            <li class="clearfix">
                                <div class="message-data align-right">
                                    <span class="message-data-time" >${value.date_updated}</span>
                                    <span class="message-data-name" ><a href="home.php">${me}</a></span> <i class="fa fa-circle me"></i>
                                </div>
                                <div class="message other-message float-right">
                                    ${value.message}
                                </div>
                            </li>
                        `);
                    }
                    else{
                        $("#chat_container").append(`
                            <li>
                                <div class="message-data">
                                    <span class="message-data-name"><a href="home.php?user=${userid}">${friend}</a><i class="fa fa-circle online"></i></span>
                                    <span class="message-data-time">${value.date_updated}</span>
                                </div>
                                <div class="message my-message">
                                    ${value.message}
                                </div>
                            </li> 
                        `);
                    }
                });
            } catch (error) {
              //  console.error(error);
            }
        }
        else{

        }
    });
}

function sendMessage(userid){
    var message = $("#message-to-send").val();

    if (!message || !message.trim())
        return;
    $.post(`sendMssg.php?userid=${userid}`, { message: message }, function(data, status){
        if(status === "success"){
            try {
                data = JSON.parse(data);

                if (data.success && data.success === "Message sent"){
                    $("#message-to-send").val(""); 
                }
            } catch (error) {
                
            }
        }
        else{

        }
    });
}