load(function() {
  $("#sidebar-btn").click(function() {
    $("#sidebar").toggleClass("open");
    $('.bun').toggleClass('open');
    $(".friend-edit").toggleClass("open", false);
    $(".addfriend .add-icon").toggleClass("spin", false);
  });
  $("#sidebar").toggleClass("open", false);
}, true);

var toast = document.getElementById("toast");

var high = 20;

function createToast(text, time) {
    if (time === undefined || time === null) {
        time = 3;
    }
    setTimeout(function(){
      toast.innerHTML = text;
      toast.style.top = -toast.offsetHeight + "px";
      toast.style.top = high + "px";

      setTimeout(function(){
        toast.style.top = -toast.offsetHeight + "px";
      }, time * 1000);
    }, 1);
}
