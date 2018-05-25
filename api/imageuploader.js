//'use strict';

class Resizer {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.selected = false;
    }

    draw() {
        rectMode(CENTER);
        fill("#fff");
        rect(this.x + xOff, this.y + yOff, resizerSize, resizerSize);
        fill("#000");
        rect(this.x + xOff, this.y + yOff, resizerSize - 5, resizerSize - 5);
    }

    resize(x, y, id) {

        if (id === 0) { //tl
            if (x > 0 && sizers[2].y - (sizers[1].x - x) > 0 && x < img.width * multiplier && sizers[2].y - (sizers[1].x - x) < img.height * multiplier) {
                this.x = x;
                this.y = sizers[2].y - (sizers[1].x - this.x);
                sizers[1].y = this.y;
                sizers[2].x = this.x;
            }
        } else if (id === 1) { //tr
            if (x > 0 && sizers[3].y - (x - sizers[0].x) > 0 && x < img.width * multiplier && sizers[3].y - (x - sizers[0].x) < img.height * multiplier) {
                this.x = x;
                this.y = sizers[3].y - (this.x - sizers[0].x);
                sizers[0].y = this.y;
                sizers[3].x = this.x;
            }
        } else if (id === 2) { //bl
            if (x > 0 && sizers[0].y + (sizers[3].x - x) > 0 && x < img.width * multiplier && sizers[0].y + (sizers[3].x - x) < img.height * multiplier) {
                this.x = x;
                this.y = sizers[0].y + (sizers[3].x - this.x);
                sizers[0].x = this.x;
                sizers[3].y = this.y;
            }
        } else if (id === 3) { //br
            if (x > 0 && sizers[1].y + (x - sizers[2].x) > 0 && x < img.width * multiplier && sizers[1].y + (x - sizers[2].x) < img.height * multiplier) {
                this.x = x;
                this.y = sizers[1].y + (this.x - sizers[2].x);
                sizers[1].x = this.x;
                sizers[2].y = this.y;
            }
        }
    }
}

var forcedRatio = true;
var ratio = 1; //ratio goes by: width / height

var xSide;
var ySide;
var maxSize;

var xOff;
var yOff;

var img;
var resizerSize = 30;
var border = 10;
var loaded = false;
var multiplier = 1;

var fileSave;

var sizers = [];
var resizerSelected = false;


function setup() {
    var uploadBtn = createFileInput(imageUpload);
    createCanvas(1200, 600);
}

function draw() {
    rectMode(CORNER);
    fill("#ccc");
    noStroke();
    rect(0, 0, width, height);
    if (loaded) {
        xOff = (width - img.width * multiplier) / 2;
        yOff = (height - img.height * multiplier) / 2;
        var imgWidth = img.width * multiplier;
        var imgHeight = img.height * multiplier;
        image(img, xOff, yOff, imgWidth, imgHeight);
        drawShade();
        for (var i = 0; i < sizers.length; i++) {
            sizers[i].draw();
        }
    }
}

function drawShade() {
    rectMode(CORNERS);
    fill("rgba(144, 143, 144, 0.5)");
    translate(xOff, yOff);
    rect(0, 0, sizers[0].x, img.height * multiplier);
    rect(sizers[0].x, 0, img.width * multiplier, sizers[0].y);
    rect(sizers[1].x, sizers[1].y, img.width * multiplier, img.height * multiplier);
    rect(sizers[2].x, sizers[2].y, sizers[3].x, img.height * multiplier);
    translate(-xOff, -yOff);
    //rect()
}

function imageSize(img) {
    multiplier = 1;
    var side = sideBigger(img).side;
    var size = sideBigger(img).size;
    if (side === "width") {
        multiplier = (width - border * 2) / size;
    } else {
        multiplier = (height - border * 2) / size;
    }
    return multiplier;
}

function sideBigger(img) {
    if (img.width > img.height) {
        return {
            size: img.width,
            side: "width"
        };
    } else {
        return {
            size: img.height,
            side: "height"
        };
    }
}

function sideSmaller(img) {
    if (img.width < img.height) {
        return img.width;
    } else {
        return img.height;
    }
}

function mouseDragged() {
    if (resizerSelected) {
        var resizerWidth = sizers[1].x - sizers[0].x;
        var halfWidth = resizerWidth / 2;
        if (mouseX - halfWidth - xOff > 0 && mouseX + halfWidth - xOff < img.width * multiplier) {
            sizers[0].x = mouseX - halfWidth - xOff;
            sizers[1].x = mouseX + halfWidth - xOff;
            sizers[2].x = mouseX - halfWidth - xOff;
            sizers[3].x = mouseX + halfWidth - xOff;
        }
        if (mouseY - halfWidth - yOff > 0 && mouseY + halfWidth - yOff < img.height * multiplier) {
            sizers[0].y = mouseY - halfWidth - yOff;
            sizers[1].y = mouseY - halfWidth - yOff;
            sizers[2].y = mouseY + halfWidth - yOff;
            sizers[3].y = mouseY + halfWidth - yOff;
        }
    } else {
        for (var i = 0; i < sizers.length; i++) {
            if (sizers[i].selected)
                sizers[i].resize(mouseX - xOff, mouseY - yOff, i);
        }
    }
}

function mouseReleased() {
    resizerSelected = false;
    for (var i = 0; i < sizers.length; i++) {
        sizers[i].selected = false;
    }
}

function mousePressed() {
    if (loaded) {
        if (mouseX < sizers[0].x + resizerSize / 2 + xOff && mouseX > sizers[0].x - resizerSize / 2 + xOff && mouseY < sizers[0].y + resizerSize / 2 + yOff && mouseY > sizers[0].y - resizerSize / 2 + yOff) {
            console.log("top left");
            sizers[0].selected = true;
        } else if (mouseX < sizers[1].x + resizerSize / 2 + xOff && mouseX > sizers[1].x - resizerSize / 2 + xOff && mouseY < sizers[1].y + resizerSize / 2 + yOff && mouseY > sizers[1].y - resizerSize / 2 + yOff) {
            console.log("top right");
            sizers[1].selected = true;
        } else if (mouseX < sizers[2].x + resizerSize / 2 + xOff && mouseX > sizers[0].x - resizerSize / 2 + xOff && mouseY < sizers[2].y + resizerSize / 2 + yOff && mouseY > sizers[2].y - resizerSize / 2 + yOff) {
            console.log("bot left");
            sizers[2].selected = true;
        } else if (mouseX < sizers[3].x + resizerSize / 2 + xOff && mouseX > sizers[3].x - resizerSize / 2 + xOff && mouseY < sizers[3].y + resizerSize / 2 + yOff && mouseY > sizers[3].y - resizerSize / 2 + yOff) {
            console.log("bot right");
            sizers[3].selected = true;
        } else if (mouseX > sizers[0].x + xOff && mouseX < sizers[1].x + xOff && mouseY > sizers[0].y + yOff && mouseY < sizers[2].y + yOff) {
            resizerSelected = true;
            //console.log("dont be gay will in the image, and fuck i shouldve looked for whats inside the selection not the whole fucking image, im a dumbass, fuck my life, dammit, what even i guess ill fix this now BUT FUCKING SHIT, ugh why am i so dumb, you know what, what ever")
        }
    }
}

function imageUpload(file) {
    fileSave = file.data;
    img = loadImage(file.data, function() {
        $("img").attr("src", file.data);
        loaded = true;
        multiplier = imageSize(img);
        var xOffset = (width - img.width * multiplier) / 2;
        var yOffset = (height - img.height * multiplier) / 2;
        maxSize = sideSmaller(img) * multiplier;
        sizers = [new Resizer(0, 0),
            new Resizer(maxSize, 0),
            new Resizer(0, maxSize),
            new Resizer(maxSize, maxSize)
        ];
    });
}

function btnClick() {
  console.log((img.width * multiplier)/ (sizers[1].x - sizers[0].x));
  console.log(100 - (sizers[0].x / (img.width * multiplier)) * 100);
  console.log(100 - (sizers[0].y / (img.height * multiplier)) * 100);

    if (fileSave !== undefined) {
      var zoom = (img.width * multiplier)/ (sizers[1].x - sizers[0].x);
        $.ajax({
                method: "POST",
                url: "/api/set_image.php",
                data: {
                    group: "will",
                    image: fileSave,
                    zoom: zoom,
                    x: 100 - (((img.width * multiplier) / zoom) / img.width * multiplier) * 100,
                    y: 100 - (((img.height * multiplier) / zoom) / img.height * multiplier) * 100
                }
            })
            .done(function() {
              //window.location.href = "/chat/";
            })
            .fail(function() {
              alert("BOY SOMETHING WENT WRONG!!!!!!!");
            });
    }
}
