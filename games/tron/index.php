<?php
session_start();
include_once '../../api/api.php';

$userrank = "default";
$currentuser = "";
if (isset($_SESSION['active']) && isset($_SESSION['id'])){
  $userrank = idToRank($_SESSION['id']);
  $currentuser = idToUsername($_SESSION['id']);
}
 ?>
<!DOCTYPE HTML>

<html>

<head>
    <title>Dat Jap - Tron</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.3/socket.io.min.js"></script>
    <script src="https://github.com/processing/p5.js/releases/download/0.5.7/p5.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        canvas {
            margin: 0;
            padding: 0;
            position: absolute;
            left: 0;
            /*300px*/
            right: 0;
            top: 0;
            bottom: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            text-align: center;
        }

        #chatbox {}

        #refresh {
            overflow-y: auto;
            overflow-x: hidden;
            position: absolute;
            width: 0;/*300px*/
            bottom: 100px;
            top: 0;
            background-color: #cccccc;
        }

        .default {
            display: inline-block;
            height: 23px;
            padding-top: 3px;
            padding-left: 5px;
            width: 100%;
            background-color: #585858;
            color: #ffffff;
        }

        .mod {
            display: inline-block;
            height: 23px;
            padding-top: 3px;
            padding-left: 5px;
            width: 100%;
            background-color: #5555ff;
            color: #ffffff;
        }

        .admin {
            display: inline-block;
            height: 23px;
            padding-top: 3px;
            padding-left: 5px;
            width: 100%;
            background-color: #ff0000;
            color: #ffffff;
        }

        .messagesent {
            background-color: #cccccc;
            padding-left: 5px;
            display: block;
            border-bottom: solid 1px #000;
        }

        .input {
            position: absolute;
            bottom: 0;
            margin-left: 5px;
            margin-bottom: 5px;
            width: 0;/*295*/
            display: block;
        }

        .input textarea {
            resize: none;
            width: 0;/*285px*/
            height: 50px;
        }

        .input input {
            width: 0;/*290px*/
        }
    </style>

    <script>
        var socket = io('ctf.datjap.com:3000');

        var bodySize = 20;
        var MAXPOWER = 100;

        var players = Array();

        var playerId = null;
        var selfPlayer = null;

        var playerPower = 100;
        var playerAlive = true;

        var WIDTH = 5000;
        var HEIGHT = 5000;

        var focused = true;

        var pulse = true;

        var img;

        function preload() {
          img = loadImage("../../images/games/tron-settings.png");
        }

        socket.on('connect', function() {
            console.log("Connected");
            //var xhttp = new XMLHttpRequest();
            //xhttp.onreadystatechange = function() {
                //if (this.readyState == 4 && this.status == 200) {
                    //var name = this.responseText;
                    var name = "guest";
                    //if (name === "guest") {
                        name = name + round(random(1000));
                    //}
                    console.log(name);
                    socket.emit('name', name);
                //}
            //};
            //xhttp.open("GET", "http://www.datjap.com/api/name.php");
            //xhttp.send();
        });

        socket.on('add_player', function(data) {
            var p = new Player(data.tail[0].x, data.tail[0].y, data.color, data.id, data.boost, data.name);
            p.tail = data.tail;
            players.push(p);
            if (data.id === playerId) {
                selfPlayer = p;
            }
            console.log(data);
        });

        socket.on('remove_player', function(data) {
            for (var i in players) {
                var player = players[i];
                if (player.id === data) {
                    delete players[i];
                    break;
                }
            }
        });

        socket.on('pos', function(data) {
            for (var i in data) {
                for (var j in players) {
                    if (data[i].id === players[j].id) {
                        var player = players[j];
                        if (data[i].tail !== null) {
                            player.tail.unshift(new Pos(player.pos.x, player.pos.y))
                            player.pos = data[i].tail;
                        }
                        player.color = data[i].color;
                        player.boost = data[i].boost;
                        player.name = data[i].name;
                    }
                }
            }
        });

        socket.on('personal', function(data) {
            playerPower = data.power;
            if (!data.alive && playerAlive) {
                filter(BLUR, 4);
                fill(360);
                stroke(0);
                strokeWeight(5);
                textSize(60);
                text("You died!\nPress R to restart", width / 2 - 250, height / 2 - 50);
            }
            playerAlive = data.alive;
        });

        socket.on('id', function(data) {
            playerId = data;
            console.log("playerId: " + data);
        });

        function setup() {
            createCanvas(windowWidth /*- 300*/, windowHeight);
            colorMode(HSB, 360);
            background(72);
            frameRate(30);
            var refresh = $("#refresh");
            refresh.animate({
                scrollTop: refresh[0].scrollHeight
            }, 'slow');
            $("#message").focus(function() {
                focused = false;
            });
            $("#message").blur(function() {
                focused = true;
            });
        }

        function draw() {
            if (selfPlayer !== null && playerAlive) {
                push();
                noStroke();
                background(360);
                center(true);
                fill(72);
                rect(0, 0, WIDTH, HEIGHT);
                center(false);

                image(img, 10, 10, 40, 40);

                colorMode(RGB, 1);
                fill(0, 0, 0, 0.3);
                rect(width - 210, 0, 200, 200);
                colorMode(HSB, 360);
                rect(width - 210, 210, 200, 100);
                var topList = Array();
                for (var i in players) {
                    var player = players[i];
                    var set = false;
                    for (var j in topList) {
                        if (topList[j].tail.length < player.tail.length) {
                            topList.splice(j, 0, player);
                            set = true;
                            break;
                        }
                    }
                    if (!set) {
                        append(topList, player);
                    }
                }
                textSize(10);
                stroke(0);
                strokeWeight(1);
                var playerInList = false;
                for (var i = 0; i < 5 && i < topList.length; i++) {
                    var player = topList[i];
                    if (player.id == playerId) {
                        playerInList = true;
                    }
                    fill(player.color, 360, 360);
                    text((i + 1) + ". " + player.name + ": " + player.tail.length, width - 195, 225 + (i * 15));
                }
                if (!playerInList) {
                    fill(selfPlayer.color, 360, 360);
                    text(topList.indexOf(player) + 1 + ". " + selfPlayer.name + ": " + selfPlayer.tail.length, width - 195, 225 + (75));
                }
                center(true);
                for (var i in players) {
                    var player = players[i];
                    player.draw(player.id == playerId);
                }
                pop();
                strokeWeight(5);
                stroke(0);
                fill(0);
                rect(0, height - 20, width, height);
                noStroke();
                fill(0, 360, 360);
                var barWidth = round(map(playerPower, 0, MAXPOWER, 0, width));
                rect(0, height - 20, barWidth, height);
            }
        }

        class Pos {
            constructor(x, y) {
                this.x = x;
                this.y = y;
            }
        }

        class Player {
            constructor(x, y, color, id, boost, name) {
                this.pos = new Pos(x, y);
                this.color = color;
                this.tail = [];
                this.id = id;
                this.boost = boost;
                this.name = name;
            }

            draw(self) {
                stroke(0);
                strokeWeight(1);
                fill(this.color, 360, 360);
                ellipse(this.pos.x, this.pos.y, bodySize, bodySize);
                var lastTail = this.pos;
                strokeWeight(5);

                var tailColor = color(this.color, 360, (this.boost ? 360 : 200));



                for (var i in this.tail) {
                    if (pulse) {
                        if (!this.boost) {
                            //normal
                            var sat = Math.cos(i / 3) * 90 + 270;
                            stroke(this.color, sat, 360);
                        } else {
                            //rainbow
                            var hue = (i * 10);
                            while (hue >= 360) {
                                hue -= 360;
                            }
                            stroke(hue, 360, 360);
                        }
                    } else {
                        stroke(tailColor);
                    }
                    var tail = this.tail[i];
                    line(lastTail.x, lastTail.y, tail.x, tail.y);
                    push();
                    center(false);
                    strokeWeight(1);
                    if (pulse) {
                        if (!this.boost) {
                            var sat = Math.cos(i / 3) * 90 + 270;
                            stroke(this.color, sat, 360);
                        } else {
                            var hue = (i * 10);
                            while (hue >= 360) {
                                hue -= 360;
                            }
                            stroke(hue, 360, 360);
                        }
                    } else {
                        stroke(tailColor);
                    }
                    var miniTail = posToMini(tail);
                    var miniLast = posToMini(lastTail);
                    line(miniLast.x, miniLast.y, miniTail.x, miniTail.y);
                    center(true);
                    pop();
                    lastTail = tail;
                }
                if (self) {
                    stroke(0);
                    strokeWeight(2);
                    fill(255);
                    ellipse(this.pos.x, this.pos.y, bodySize / 2, bodySize / 2);
                }
            }
        }

        function center(cent) {
            if (cent) {
                translate(-(selfPlayer.pos.x - width / 2), -(selfPlayer.pos.y - height / 2));
            } else {
                translate(+(selfPlayer.pos.x - width / 2), +(selfPlayer.pos.y - height / 2));
            }
        }

        function posToMini(pos) {
            return new Pos(round(map(pos.x, 0, WIDTH, width - 210, width - 10)), round(map(pos.y, 0, HEIGHT, 0, 200)));
        }

        function mouseClicked() {
          if (mouseX > 10 && mouseX < 50 && mouseY > 10 && mouseY < 50){
            if (pulse){
              pulse = false;
            } else {
              pulse = true;
            }
          }
        }

        function keyPressed() {
            if (focused)
                sendKeys();
        }

        function keyReleased() {
            if (focused)
                sendKeys();
        }

        function sendKeys() {
            var keys = Array();
            for (var i = 0; i < 222; i++) {
                if (keyIsDown(i)) {
                    append(keys, i);
                }
            }
            socket.emit('key', {
                key: keys
            });
        }

        function windowResized() {
            resizeCanvas(windowWidth /*- 300*/, windowHeight);
        }
    </script>

</head>

<body>

    <div id="chatbox" style="position: absolute; left:0; top:0; bottom: 0; width: 300px; background-color: #cccccc;">
        <div id="refresh">
            <?php
    $_SESSION['chatgroup'] = $chatid;
    require '../connLogin.php';
    $stmt = $conn->prepare("SELECT userid, message, time, id FROM chat  WHERE chatgroup='tron' ORDER BY time ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
    // output data of each row
      while($row = $result->fetch_assoc()) {
        $realname = idToUsername($row["userid"]);
        $username = idToName($row["userid"],false);
        $rank = idToRank($row["userid"]);
        $time = timeAgo($row['time']);
        echo "<div class=\"message\">" . "<span class=\"$rank\">"
        . $username . ":</span>"
        . "<span class=\"messagesent\">{$row["message"]}</span></div>";
      }
    }
    ?>
        </div>
    </div>
    <div class="input">
        <textarea name="message" id="message"></textarea>
        <input type="submit" name="submit" value="Send" class="button" onclick="sendCurrentChatMessage();" />
    </div>

    <script>
        function sendCurrentChatMessage() {
            $.ajax({
                method: "POST",
                url: "../sendmessage.php",
                data: {
                    message: $("#message").val(),
                    chatgroup: "tron"
                }
            }).done(function() {
                console.log("sentChatMessage");
                $("#message").val("");
                reloadChat();
            });
        }

        function reloadChat() {
            var $chatbox = $("#chatbox");
            var r = $("#refresh");
            var scrollToBottom = (r.scrollTop() > r.height() - 100);
            var scrollPos = $("#refresh").scrollTop();
            $chatbox.load(document.URL + " #refresh", function() {
                var refresh = $("#refresh");
                refresh.scrollTop(scrollPos);
                if (scrollToBottom) {
                    refresh.animate({
                        scrollTop: refresh[0].scrollHeight
                    }, 'slow');
                }
            });
        }

        //setInterval(reloadChat, 5000);
    </script>

</body>

</html>
