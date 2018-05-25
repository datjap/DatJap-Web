<?php
function pageInit(){
  ?>
  <div id="toast">

  </div>


  <ul id="nav-bar" class="reload reloadNav">
    <div class="nav-bar-item" id="sidebar-btn">
      <div class="hamburger-x">
        <div class="top bun"></div><div class="mid bun"></div><div class="bot bun"></div>
      </div>
    </div>
    <div class="nav-bar-item color-transform">
      <span class="nav-bar-text">Dat Jap</span>
    </div>
    <?php
    if (isset($_SESSION['user'])) {
    ?>

        <div class="nav-bar-item color-transform right">
            <span class="nav-bar-text">Welcome <?php echo $_SESSION['user']->displayName; ?></span>
        </div>

    <?php
    } else {
    ?>
      <div class="nav-bar-item color-transform right" id="login">
        <span class="nav-bar-text">Login</span>
      </div>
    <?php
    }
    ?>

  </ul>

  <div id="clickout">

  </div>
  <div id="loginShield">
    <div class="loginSelect" style="float: left;" id="signin">
      <h2 class="loginHeader">Sign In</h2>
      <div class="line">

      </div>
      <div class="login-form">
        <form id="loginForm" method="post">
          <span>Username</span> <br/><input id="loginInput" class="login-input" type="text" placeholder="Username, Email, or Phone Number" /><br/> <span>Password</span> <br/><input id="loginPassword" class="login-input" type="password" placeholder="Password" /><br/>
          <button id="loginBtn" class="login-btn">Sign In</button>
        </form>
      </div>
    </div>
    <div class="loginSelect" style="float: right;" id="signup">
      <h2 class="loginHeader">Register</h2>
      <div class="line">

      </div>
      <div class="login-form">
        <form id="registerForm" method="post">
          <span>Name</span> <br/><input id="registerName" class="login-input" type="text" placeholder="Name | Ex. Jim Bob" /><br/> <span>Username</span> <br/><input id="registerUsername" class="login-input" type="text" placeholder="Username" /><br/> <span>Email</span> <br/><input id="registerEmail"
            class="login-input" type="email" placeholder="Email | Ex. jimbobby432@gmail.com" /><br/> <span>Password</span> <br/><input id="registerPassword" class="login-input" type="password" placeholder="Password" /><br/>
          <button id="registerBtn" class="login-btn">Sign Up</button>
        </form>
      </div>
    </div>
  </div>

  <div id="sidebar" class="reload reloadSidebar">
    <div id="sidebar-content">
      <div id="sidebar-account">
        <?php if (isset($_SESSION['user'])) {?>
          <div class="profile-pic">

          </div>
          <div class="profile-name">
            <?php echo $_SESSION['user']->displayName ?>
          </div>
          <div class="profile-username">
            @<?php echo $_SESSION['user']->username ?>
          </div>
          <div class="profile-actions">
            <div class="action profile-view">
              <span>Profile</span>
            </div>
            <div class="action profile-logout">
              <span>Sign Out</span>
            </div>
          </div>
      <?php } ?>
      </div>
      <?php
      db();
      $result = dbPrepare("SELECT * FROM sidebar WHERE ?", "i", 1);

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          createElement($row['icon'], $row['name'], $row['link']);
        }
      }
      ?>
      <div class="line">

      </div>
      <div class="positionfix">

      </div>
      <a class="clean">
        <div class="friend online sidebar-item"><div class="friend-img"></div><span class="friend-name">Justin Fernald</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Jason Aldean</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Tim McGraw</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Jason Aldean</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Tim McGraw</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Jason Aldean</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Tim McGraw</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Jason Aldean</span></div>
      </a>
      <a class="clean">
        <div class="friend sidebar-item"><div class="friend-img"></div><span class="friend-name">Tim McGraw</span></div>
      </a>
      <div class="positionfix2">

      </div>
    </div>
    <div class="noti-bar">
      <?php
        if (isset($_SESSION['user'])) {
          ?>
         <div class="addfriend" title="Add Friend / View Requests">
           <i class="material-icons add-icon icon">add_circle</i><div class="dot active"></div>
         </div>
          <?php
        }
       ?>
      <div class="bell-icon">
        <i class="material-icons icon">notifications</i><div class="dot"></div>
      </div>
    </div>
  </div>
  <div class="friend-edit reload reload-friends">
    <div class="friend-types">
      <div class="friend-type active">
        <span>Friends</span>
      </div>
      <div class="friend-type">
        <span>Add</span>
      </div>
      <div class="friend-type">
        <span>Pending</span>
      </div>
      <div class="friend-type">
        <span>Requests</span>
      </div>
      <div class="friend-type">
        <span>Blocked</span>
      </div>
    </div>
    <div class="friend-viewer">
      <div class="search">
        <form>
          <input type="text" placeholder="Search Users">
        </form>
      </div>
      <div class="friend-content">
        <div class="friend-wrapper">
          HELLO
          <?php
            echo "test";
            if (isset($_SESSION['user']) && count($_SESSION['user']->getFriends()) > 0) {
              echo $_SESSION['user']->getFriends();
            } else {
              ?>
                <span class="nofriends">You have no friends *dab*</span>
              <?php
            }
           ?>
        </div>
      </div>
    </div>
  </div>
<?php
}

function createElement($icon, $name, $link){
  echo "<a class=\"clean\" href=\"$link\"><div class=\"sidebar-item\"><i class=\"material-icons icon\">$icon</i><span class=\"sidebar-text\">$name</span></div></a>";
}
?>
