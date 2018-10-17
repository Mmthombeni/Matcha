(function(){
   setInterval(loadNotification, 10000);
})();


 //loadNotification();
// (function(){
//     setInterval(loadNotification, 2000);
// })();


function loadNotification(view= ''){
    $.ajax({
        url: "loadNotification.php",
        method: "POST",
        data:{view:view},
        dataType: "json",
        success:function(data)
        {
            $('.dropdown-menu').html(data.notification);
            if (data.unseen_notification > 0)
            {
                $('.count').html(data.unseen_notification);
            }
        }
    })
}
$('.dropdown-toggle').on('click', function(){
                $(".count").html('');
                loadNotification('yes');
            });