var type;
load(function() {
    type = "Friends";
    $(".addfriend").click(function() {
        $(".friend-edit").toggleClass("open");
        $(".addfriend .add-icon").toggleClass("spin");
    });

    $(".friend-box").click(function() {
        $(".friend-box").not(this).toggleClass("open", false);
        $(this).toggleClass("open");
    });
    $(".friend-edit").toggleClass("open", false);
    $(".addfriend .add-icon").toggleClass("spin", false);
    $(".friend-box").toggleClass("open", false);

    $(".friend-viewer .search form").submit(function(e) {
        e.preventDefault();
        console.log(type);
        $.get("/api/usersearch.php", {
            search: $('.friend-viewer .search input').val(),
            type: type
        }, function(data, status) {
            console.log(data, status);
        });
    });

    $(".friend-type").click(function() {
        $(".friend-type").toggleClass("active", false);
        $(this).toggleClass("active");

        type = $(this).text().trim();
        loadFriendScreen();
    });
    loadFriendScreen();
}, true);

function loadFriendScreen() {
    console.log(personalData);
    if (type == "Friends") {
        $(".friend-wrapper").html("");
    } else if (type == "Add") {
        $(".friend-wrapper").html("");
    } else if (type == "Pending") {
        $(".friend-wrapper").html("");
        $(".friend-wrapper").html(createFriendItems(personalData.pending, "End Request"));
    } else if (type == "Requests") {
        $(".friend-wrapper").html("");
    } else if (type == "Blocked") {
        $(".friend-wrapper").html("");
    }
}

function createFriendItems(users, action) {
    let output = "";
    for (var i = 0; i < users.length; i++) {
        var user = users[i];
        output += createFriendItem(user.displayName, user.name, 0, false, action);
    }
    return output;
}

function createFriendItem(name, username, mutual, online, action) {
    return '<div class="friend-box' + (online ? ' online' : '') + '"><div class="friend-image"></div><div class="friend-name">' + name + '</div><div class="friend-username">@' + username + '</div><div class="friend-mutual">Mutual friends: ' + mutual + '</div><div class="friend-dropdown"><div class="button view-profile">View Profile</div><div class="button friend-action">' + action + '</div></div></div>';
}