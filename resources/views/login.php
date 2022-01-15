<?php declare(strict_types=1);
/**
 * @var $errors
 * @var $success
 */
$errors = $errors ??= null;
$success = $success ??= null;
?>

<div class="row fadeInDown justify-content-md-center">
    <?php if($success) {?>
        <?php foreach($success as $ok): ?>
            <div class="alert alert-success" role="alert">
                <?= $ok ?>
            </div>
        <?php endforeach;?>

    <?php } ?>
    <?php if($errors) {?>
            <?php foreach($errors as $error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php endforeach;?>

    <?php } ?>


        <!--  SignUp  -->
        <div class="oe_user_login_container col ">
            <div class="container-fluid">
                <h2>SignUp</h2>
                <!-- Icon -->
                <div class="fadeIn first logo-user-box">
                    <i class='bx bxs-edit login--user' ></i>
                </div>
                <form method="POST" action="/signup?submit">
                    <input type="email" id="email" class="fadeIn second oe_user_login_input" name="email" placeholder="Email..." required>
                    <input type="password" id="password" class="fadeIn third oe_user_login_input" name="password" placeholder="Password.." required>
                    <input type="submit" class="fadeIn fourth oe_user_login_sumbit" value="SignUp">
                </form>
            </div>
        </div>
        <!--  Login  -->
        <div class="oe_user_login_container col ">
            <div class="container-fluid">
                <h2>Login</h2>
                <!-- Icon -->
                <div class="fadeIn first logo-user-box">
                    <i class='bx bx-log-in login--user'></i>
                </div>
                <form method="POST" action="/login?submit">
                    <input type="email" id="email" class="fadeIn second oe_user_login_input" name="email" placeholder="Email..." required>
                    <input type="password" id="password" class="fadeIn third oe_user_login_input" name="password" placeholder="Password.." required>
                    <input type="submit" class="fadeIn fourth oe_user_login_sumbit" value="Log In">
                </form>
            </div>
        </div>

</div>