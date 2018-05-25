var cards = document.getElementsByClassName("overlay");
for (var i = 0; i < cards.length; i++) {
  cards[i].addEventListener("mouseleave", function(event) {
    scrollUp(event);
  });
  cards[i].addEventListener("scroll", function(event) {
    scrollEvent(event)
  })
}

function scrollUp(event) {
  setTimeout(function() {
    event.target.scrollTop = 0;
  }, 350);
}


function scrollEvent(event) {
  if (event.target.getElementsByClassName("cardArrow").length > 0) {
    if (event.target.scrollTop > 30) {
      event.target.getElementsByClassName("cardArrow")[0].classList.remove("pulse");
    } else {
      event.target.getElementsByClassName("cardArrow")[0].classList.add("pulse");
    }
  }
}
