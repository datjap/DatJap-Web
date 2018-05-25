//load(function() { }, [run on reload]);

//todo: add script reload support
function load(callback, reloadRun = false) {
  $(window).on("load", function(e, reload = false) {
    if (reloadRun && reload || reload == false) {
      callback(e, reload);
    }
  });
}

function reload() {
  var parser = new DOMParser();
  var doc;
  $.ajax({
    type: "POST",
    url: "",
    success: function(html){
      doc = parser.parseFromString(html, "text/html");
      var reloads = doc.getElementsByClassName("reload");
      for (var i = 0; i < reloads.length; i++) {
        if (reloads[i].classList.length > 0) {
          for (var j = 0; j < reloads[i].classList.length; j++) {
            var className = reloads[i].classList[j];
            if (document.getElementsByClassName(className).length == 1) {
              var reloadSpot = document.getElementsByClassName(className)[0];
              if (reloadSpot != null) {
                //reloadSpot.innerHTML = reloads[i].innerHTML;
                $("."+className).html(reloads[i].innerHTML);
              }
              break;
            }
          }
        }
      }
      $(window).trigger("load", [true]);

      //loads personalData
      eval($(".reloadPersonalData").html());
      /*setTimeout(function() {
        $(window).trigger("load", [true]);
      }, 500);*/
    }
  });
}

/*
Usage:
Just add reload to the div or html element you want to refresh
Also add an class with a unique name so it can find what needs to be replaced

*/
