load(function() {
  $(".profile-logout").click(function(){
    $.ajax({
      url: "/user/logout.php"
    }).done(function() {
      reload();
    });
  });
},true);
