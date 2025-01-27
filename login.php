<?php
session_start();

// Memeriksa apakah pengguna sudah login, jika iya, arahkan ke halaman utama
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}

// Variabel untuk menyimpan pesan error
$error_message = '';

// Memproses data saat form login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('config/connection.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Melakukan query untuk memeriksa username di database
    $sql = "SELECT id_user, username, password, nama_user, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Memeriksa apakah password yang dimasukkan sesuai dengan password di database
        if ($password == $row['password']) {
            // Memulai sesi
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama_user'] = $row['nama_user'];
            $_SESSION['role'] = $row['role'];
            echo '<script>window.location.href = "index.php";</script>';
            exit();
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Design by foolishdeveloper.com -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        html,
        body * {
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background:
                linear-gradient(rgba(246, 247, 249, 0.8),
                    rgba(246, 247, 249, 0.8)),
                url(https://res.cloudinary.com/dxfq3iotg/image/upload/v1564049481/bg-clouds.jpg) no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            width: 100%;
            padding-top: 60px;
            padding-bottom: 100px;
        }

        .frame {
            height: 575px;
            width: 430px;
            background:
                linear-gradient(rgba(35, 43, 85, 0.75),
                    rgba(35, 43, 85, 0.95)),
                url(https://res.cloudinary.com/dxfq3iotg/image/upload/v1564049481/bg-clouds.jpg) no-repeat center center;
            background-size: cover;
            margin-left: auto;
            margin-right: auto;
            border-top: solid 1px rgba(255, 255, 255, .5);
            border-radius: 5px;
            box-shadow: 0px 2px 7px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all .5s ease;
        }

        .frame-long {
            height: 615px;
        }

        .frame-short {
            height: 400px;
            margin-top: 50px;
            box-shadow: 0px 2px 7px rgba(0, 0, 0, 0.1);
        }

        .nav {
            width: 100%;
            height: 100px;
            padding-top: 40px;
            opacity: 1;
            transition: all .5s ease;
        }

        .nav-up {
            transform: translateY(-100px);
            opacity: 0;
        }

        li {
            padding-left: 10px;
            font-size: 18px;
            display: inline;
            text-align: left;
            text-transform: uppercase;
            padding-right: 10px;
            color: #ffffff;
        }

        .signin-active a {
            padding-bottom: 10px;
            color: #ffffff;
            text-decoration: none;
            border-bottom: solid 2px #1059FF;
            transition: all .25s ease;
            cursor: pointer;
        }

        .signin-inactive a {
            padding-bottom: 0;
            color: rgba(255, 255, 255, .3);
            text-decoration: none;
            border-bottom: none;
            cursor: pointer;
        }

        .form-signin {
            width: 430px;
            height: 375px;
            font-size: 16px;
            font-weight: 300;
            padding-left: 37px;
            padding-right: 37px;
            padding-top: 55px;
            transition: opacity .5s ease, transform .5s ease;
        }

        .form-signin-left {
            transform: translateX(-400px);
            opacity: .0;
        }

        .success {
            width: 80%;
            height: 150px;
            text-align: center;
            position: relative;
            top: -890px;
            left: 450px;
            opacity: .0;
            transition: all .8s .4s ease;
        }

        .success-left {
            transform: translateX(-406px);
            opacity: 1;
        }

        .successtext {
            color: #ffffff;
            font-size: 16px;
            font-weight: 300;
            margin-top: -35px;
            padding-left: 37px;
            padding-right: 37px;
        }

        #check path {
            stroke: #ffffff;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: .85px;
            stroke-dasharray: 60px 300px;
            stroke-dashoffset: -166px;
            fill: rgba(255, 255, 255, .0);
            transition: stroke-dashoffset 2s ease .5s, fill 1.5s ease 1.0s;
        }

        #check.checked path {
            stroke-dashoffset: 33px;
            fill: rgba(255, 255, 255, .03);
        }

        .form-signin input {
            color: #ffffff;
            font-size: 13px;
        }

        .form-styling {
            width: 100%;
            height: 35px;
            padding-left: 15px;
            border: none;
            border-radius: 20px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, .2);
        }

        label {
            font-weight: 400;
            text-transform: uppercase;
            font-size: 13px;
            padding-left: 15px;
            padding-bottom: 10px;
            color: rgba(255, 255, 255, .7);
            display: block;
        }

        :focus {
            outline: none;
        }

        .form-signin input:focus,
        textarea:focus {
            background: rgba(255, 255, 255, .3);
            border: none;
            padding-right: 40px;
            transition: background .5s ease;
        }

        [type="checkbox"]:not(:checked),
        [type="checkbox"]:checked {
            position: absolute;
            display: none;
        }

        [type="checkbox"]:not(:checked)+label,
        [type="checkbox"]:checked+label {
            position: relative;
            padding-left: 85px;
            padding-top: 2px;
            cursor: pointer;
            margin-top: 8px;
        }

        [type="checkbox"]:not(:checked)+label:before,
        [type="checkbox"]:checked+label:before,
        [type="checkbox"]:not(:checked)+label:after,
        [type="checkbox"]:checked+label:after {
            content: '';
            position: absolute;
        }

        [type="checkbox"]:not(:checked)+label:before,
        [type="checkbox"]:checked+label:before {
            width: 65px;
            height: 30px;
            background: rgba(255, 255, 255, .2);
            border-radius: 15px;
            left: 0;
            top: -3px;
            transition: all .2s ease;
        }

        [type="checkbox"]:not(:checked)+label:after,
        [type="checkbox"]:checked+label:after {
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, .7);
            border-radius: 50%;
            top: 7px;
            left: 10px;
            transition: all .2s ease;
        }

        /* on checked */
        [type="checkbox"]:checked+label:before {
            background: #0F4FE6;
        }

        [type="checkbox"]:checked+label:after {
            background: #ffffff;
            top: 7px;
            left: 45px;
        }

        [type="checkbox"]:checked+label .ui,
        [type="checkbox"]:not(:checked)+label .ui:before,
        [type="checkbox"]:checked+label .ui:after {
            position: absolute;
            left: 6px;
            width: 65px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            line-height: 22px;
            transition: all .2s ease;
        }

        [type="checkbox"]:not(:checked)+label .ui:before {
            content: "no";
            left: 32px;
            color: rgba(255, 255, 255, .7);
        }

        [type="checkbox"]:checked+label .ui:after {
            content: "yes";
            color: #ffffff;
        }

        [type="checkbox"]:focus+label:before {
            box-sizing: border-box;
            margin-top: -1px;
        }

        .btn-signin {
            float: left;
            padding-top: 8px;
            width: 100%;
            height: 35px;
            border: none;
            border-radius: 20px;
            margin-top: -8px;
        }

        .btn-animate {
            float: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            text-align: center;
            color: rgba(255, 255, 255, 1);
            padding-top: 8px;
            width: 100%;
            height: 35px;
            border: none;
            border-radius: 20px;
            margin-top: 23px;
            background-color: rgba(16, 89, 255, 1);
            left: 0px;
            top: 0px;
            transition: all .5s ease, top .5s ease .5s, height .5s ease .5s, background-color .5s ease .75s;
        }

        .btn-animate-grow {
            width: 130%;
            height: 625px;
            position: relative;
            left: -55px;
            top: -420px;
            color: rgba(255, 255, 255, 0);
            background-color: rgba(255, 255, 255, 1);
        }


        .forgot {
            height: 100px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            padding-top: 24px;
            margin-top: -535px;
            border-top: solid 1px rgba(255, 255, 255, .3);
            transition: all 0.5s ease;
        }

        .forgot-left {
            transform: translateX(-400px);
            opacity: 0;
        }

        .forgot-fade {
            opacity: 0;
        }

        .forgot a {
            color: rgba(255, 255, 255, .3);
            font-weight: 400;
            font-size: 13px;
            text-decoration: none;
        }

        .welcome {
            width: 100%;
            height: 50px;
            position: relative;
            color: rgba(35, 43, 85, 0.75);
            opacity: 0;
            transition: transform 1.5s ease .25s, opacity .1s ease 1s;
        }

        .welcome-left {
            transform: translateY(-780px);
            opacity: 1;
        }

        .cover-photo {
            height: 150px;
            position: relative;
            left: 0px;
            top: -900px;
            background:
                linear-gradient(rgba(35, 43, 85, 0.75),
                    rgba(35, 43, 85, 0.95)),
                url(https://img.icons8.com/bubbles/100/000000/user.png);
            background-size: cover;
            opacity: 0;
            transition: all 1.5s ease 0.55s;
        }

        .cover-photo-down {
            top: -575px;
            opacity: 1;
        }

        .profile-photo {
            height: 125px;
            width: 125px;
            position: relative;
            border-radius: 70px;
            left: 155px;
            top: -1000px;
            background: url(https://img.icons8.com/bubbles/100/000000/user.png);
            background-size: 100% 135%;
            background-position: 100% 100%;
            opacity: 0;
            transition: top 1.5s ease 0.35s, opacity .75s ease .5s;
            border: solid 3px #ffffff;
        }

        .profile-photo-down {
            top: -636px;
            opacity: 1;
        }

        h1 {
            color: #ffffff;
            font-size: 35px;
            font-weight: 300;
            text-align: center;
        }

        .btn-goback {
            position: relative;
            margin-right: auto;
            top: -400px;
            float: left;
            padding: 8px;
            width: 83%;
            margin-left: 37px;
            margin-right: 37px;
            height: 35px;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            text-align: center;
            color: #1059FF;
            margin-top: -8px;
            border: solid 1px #1059FF;
            opacity: 0;
            transition: top 1.5s ease 0.35s, opacity .75s ease .5s;
        }

        .btn-goback-up {
            top: -1080px;
            opacity: 1;
        }

        a.btn-goback:hover {
            cursor: pointer;
            background-color: #0F4FE6;
            transition: all .5s;
            color: #ffffff;
        }

        /* refresh button styling */

        #refresh {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ffffff;
            width: 50px;
            height: 50px;
            border-radius: 25px;
            box-shadow: 0px 2px 7px rgba(0, 0, 0, 0.1);
            padding: 13px 0 0 13px;
        }

        .refreshicon {
            fill: #d3d3d3;
            transform: rotate(0deg);
            transition: fill .25s ease, transform .25s ease;
        }

        .refreshicon:hover {
            cursor: pointer;
            fill: #1059FF;
            transform: rotate(180deg);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="frame">
            <div class="nav">
                <ul class="links">
                    <li class="signin-active"><a class="btn">Sign in</a></li>

                </ul>
            </div>
            <div class="form-signin">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label for="username">Username</label>
                    <input class="form-styling" type="text" name="username" placeholder="Username" required>
                    <label for="password">Password</label>
                    <input class="form-styling" type="password" name="password" placeholder="Password" required>
                    <input type="checkbox" id="checkbox" />
                    <label for="checkbox"><span class="ui"></span>Keep me signed in</label>
                    <div class="btn-animate">
                        <button class="btn-signin" type="submit">Sign in</button>
                    </div>
                </form>
                <?php if ($error_message) : ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </div>
            <div class="forgot">
                <a href="#">Forgot your password?</a>
            </div>
        </div>
    </div>
    <a id="refresh" value="Refresh" onClick="history.go()">
        <svg class="refreshicon" version="1.1" id="Capa_1" x="0px" y="0px"
            width="25px" height="25px" viewBox="0 0 322.447 322.447">
            <path d="M321.832,230.327c-2.133-6.565-9.184-10.154-15.75-8.025l-16.254,5.281C299.785,206.991,305,184.347,305,161.224
                c0-84.089-68.41-152.5-152.5-152.5C68.411,8.724,0,77.135,0,161.224s68.411,152.5,152.5,152.5c6.903,0,12.5-5.597,12.5-12.5
                c0-6.902-5.597-12.5-12.5-12.5c-70.304,0-127.5-57.195-127.5-127.5c0-70.304,57.196-127.5,127.5-127.5
                c70.305,0,127.5,57.196,127.5,127.5c0,19.372-4.371,38.337-12.723,55.568l-5.553-17.096c-2.133-6.564-9.186-10.156-15.75-8.025
                c-6.566,2.134-10.16,9.186-8.027,15.751l14.74,45.368c1.715,5.283,6.615,8.642,11.885,8.642c1.279,0,2.582-0.198,3.865-0.614
                l45.369-14.738C320.371,243.946,323.965,236.895,321.832,230.327z" />
        </svg>
    </a>
</body>

</html>