var speedmult = 1;
var dspeed = 4;
var speed = 4;
var speedinc = 1;

var pspeed = 5;

class Loc{
    constructor(x, y){
        this.x = x;
        this.y = y;
    }
}

class Vel{
    constructor(x, y){
        this.x = x;
        this.y = y;
    }

    updateLoc(loc){
        loc.x += this.x * speedmult;
        loc.y += this.y * speedmult;
    }
}

class Ball{
    constructor(loc, vel, size){
        this.loc = loc;
        this.vel = vel;
        this.size = size;
    }

    draw(){
        fill(255, 255, 255);
        rect(this.loc.x, this.loc.y, this.size, this.size);
    }

    updateLoc(){
        this.vel.updateLoc(this.loc);
        if (this.loc.y < 0){
            this.vel.y = this.vel.y * -1;
        }
        if (this.loc.y + this.size> getSize()){
            this.vel.y = this.vel.y * -1;
        }
        if (this.loc.x <= paddle1.loc.x + paddle1.width){
            //console.log("pass");

            if (this.loc.y + this.size >= paddle1.loc.y && this.loc.y /*+ this.size*/ <= paddle1.loc.y + paddle1.height && this.loc.x > paddle1.loc.x){
                //this.vel.x *= -1;
                var offSet = (this.loc.y - paddle1.loc.y) / paddle1.height
                speed += speedinc;
                newBallVelocity(ball, offSet, 1);
                this.loc.x = paddle1.loc.x + paddle1.width
            }
        }

        if (this.loc.x + this.size >= paddle2.loc.x){
            //console.log("pass");

            if (this.loc.y + this.size >= paddle2.loc.y && this.loc.y /*+ this.size*/ <= paddle2.loc.y + paddle2.height && this.loc.x + this.size < paddle2.loc.x + paddle2.width){
                //this.vel.x *= -1;

                var offSet2 = (this.loc.y - paddle2.loc.y) / paddle2.height
                speed += speedinc;
                newBallVelocity(ball, offSet2 , 2);
                this.loc.x = paddle2.loc.x - this.size;
            }
        }

        if (this.loc.x + this.size < 0){
            speed = dspeed;
            score2++;
            this.loc.x = size / 2 - this.size / 2
            this.loc.y = size / 2 - this.size / 2
            this.vel.x = speed;
            this.vel.y = 0;
        }
        if (this.loc.x > size){
            speed = dspeed;
            score1++;
            this.loc.x = size / 2 - this.size / 2
            this.loc.y = size / 2 - this.size / 2
            this.vel.x = -speed;
            this.vel.y = 0;
        }
    }
}


class Paddle{
    constructor(loc, width, height){
        this.loc = loc;
        this.width = width;
        this.height = height;
    }

    draw(){
        fill(255, 255, 255);
        rect(this.loc.x, this.loc.y, this.width, this.height);
    }
}
console.log("test");


var ball;
var pwidth = 10;
var pheight = getSize() / 5;
var poff = 10;
var paddle1 = new Paddle(new Loc(poff, (getSize()/2) - (pheight/2)), pwidth, pheight);
var paddle2 = new Paddle(new Loc(getSize() - (poff + pwidth), (getSize()/2) - (pheight/2)), pwidth, pheight);
var size = 0;
var score1 = 0;
var score2 = 0;
function getSize(){//done
  size = 0;
  if (window.innerWidth > window.innerHeight){
      size = window.innerHeight;
  } else {
      size = window.innerWidth;
  }
  return size;
}

function newBallVelocity(b, offSet, paddle){
    //console.log(offSet + " '")
    offSet = (offSet * 2) - 1
    if (paddle == 2){
        offSet *= 1
    }
    console.log(offSet)
    if (b.vel.x > 0){// --->
        b.vel = angleToVelocity(TAU / 2 + offSet / 3);
    } else {// <---
        b.vel = angleToVelocity(TAU / 1 + offSet / 3);
    }

}

function angleToVelocity(angle){//in radians
    y = sin(angle) * speed;
    x = cos(angle) * speed;
    return new Vel(x, y);
}

function setup(){
    size = getSize();
    createCanvas(size, size);

    var vel =  angleToVelocity(TAU / 6);
    ball = new Ball(new Loc(getSize() / 2, getSize()/2), vel, 20);
}

function draw(){
    //paddle2.loc.y = ball.loc.y
    pspeed = speed + 1;
    background(0, 0, 0);
    textSize(60);
    textAlign(CENTER)
    textFont("Helvetica");
    text(score1 + " : " + score2, size / 2, 60);
    ball.draw();
    paddle1.draw();
    paddle2.draw();
    //paddle1.loc.y = ball.loc.y;
    //paddle2.loc.y = ball.loc.y;

    if (!(keyIsDown(87) && keyIsDown(83))){
        if (keyIsDown(87)){//w
            paddle1.loc.y -= pspeed;
            if (paddle1.loc.y < 0){
                paddle1.loc.y = 0;
            }
        }
        if (keyIsDown(83)){//s
            paddle1.loc.y += pspeed;
            if (paddle1.loc.y + paddle1.height > size){
                paddle1.loc.y = size - paddle1.height;
            }
        }
    }
    if (!(keyIsDown(38) && keyIsDown(40))){
        if (keyIsDown(38)){//up
            paddle2.loc.y -= pspeed;
            if (paddle2.loc.y < 0){
                paddle2.loc.y = 0;
            }
        }
        if (keyIsDown(40)){//down
            paddle2.loc.y += pspeed;
            if (paddle2.loc.y + paddle2.height > size){
                paddle2.loc.y = size - paddle2.height;
            }
        }
    }


    ball.updateLoc();
}
