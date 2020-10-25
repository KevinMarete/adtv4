
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $title; ?></title>
        <link rel="SHORTCUT ICON" href="<?php echo base_url() . '/public/Images/favicon.ico'; ?>">
            <?php
            echo view('\Modules\ADT\Views\sections\\head');
            ?>
    </head>
    <body style="margin:0;">
        <header>
            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span9" style="text-align:center">
                        <img src='<?php echo base_url(); ?>/public/assets/images/nascop.jpg'>
                    </div>
                    <div class="span3" style="text-align:center">
                        <a class="btn btn-warning pull-right" href="<?= base_url() ?>/public/recover">ADT Tools</a>
                    </div>
                </div>
            </div>
        </header>
        <script>
            $(document).ready(function () {
                $(".error").css("display", "block");
                setTimeout(function () {
                    $(".message").fadeOut("20000");
                }, 60000);
                $('#username').focus();
            })
        </script>
        <?php
        $session = session();
        /* echo validation_errors('<span class="message error">', '</span>'); */;
        if ($session->get("changed_password")) {
            $message = $session->get("changed_password");
            echo "<p class='message success'>" . $message . "</p>";
            $session->set("changed_password", "");
        }
        if (isset($invalid)) {
            echo "<p class='message error'>Invalid Credentials. Please try again " . @$login_attempt . "</p>";
        } else if (isset($inactive)) {
            echo "<p class='message error'>The Account is not active. Seek help from the Administrator</p>";
        } else if (isset($unactivated)) {
            echo "<p class='message error'>Your Account Has Not Been Activated.<br/>Please Check your Email to Activate Account</p>";
        } else if (isset($expired)) {
            echo "<p class='message error'>" . @$login_attempt . "</p>";
        } else if (isset($reset)) {
            echo "<p class='message success'>" . $message . "</p>";
        }
        ?>
        <?php $validation = \Config\Services::validation(); ?>
        <div class="row-fluid">
            <div class="span12">
                <div id="signup_form">
                    <div class="short_title" >
                        Login 
                        
                    </div>
                    <form class="login-form" action="<?php echo base_url() . '/public/user_management/authenticate' ?>" method="post" style="margin:0 auto " >
                        <br>
                            <p style="color:white;"><?php echo $session->getFlashdata('wrong_loggedin');?></p>   
                        <label> <strong >Username</strong>
                            <br>
                                <input type="text" name="username" class="input-xlarge" id="username" value="" placeholder="username">
                                    <?php if ($validation->getError('username')) { ?>
                                        <div class='alert alert-danger mt-2'>
                                            <?= $error = $validation->getError('username'); ?>
                                        </div>
                                    <?php } ?>
                                    </label>
                                    <label> <strong >Password</strong>
                                        <br>
                                            <input type="password" name="password" class="input-xlarge" id="password" placeholder="password">
                                                <?php if ($validation->getError('password')) { ?>
                                                    <div class='alert alert-danger mt-2'>
                                                        <?= $error = $validation->getError('password'); ?>
                                                    </div>
                                                <?php } ?>

                                                </label>

                                                <select class="input-xlarge" name="ccc_store" style="width: 100%;">
                                                    <option> -- Select Dispensing Point --</option>
                                                    <?php foreach ($stores as $value) { ?>
                                                        <option value="<?= $value->id; ?>"><?= $value->name; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <br />
                                                <br />
                                                <input type="submit" class="btn" name="register" id="register" value="Login" >

                                                    <div style="margin:auto;width:auto" class="anchor">
<!--                                                        <strong><a href="<?php echo base_url() . 'user_management/resetPassword' ?>" >Forgot Password?</a></strong>-->
                                                    </div>
                                                    </form>
                                                    </div>
                                                    </div>
                                                    </div>
                                                    <div class="row-fluid">
                                                        <footer id="bottom_ribbon2">
                                                            <div class="container-fluid">
                                                                <div class="row-fluid">
                                                                    <div id="footer_text2" class="span12" style="text-align:center">
                                                                        Government of Kenya &copy; <?php echo date('Y'); ?>.
                                                                        All Rights Reserved
                                                                        <br/><br/>
                                                                        <strong>Web-ADT version 3.5.0</strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </footer>
                                                    </div>
                                                    </body>
                                                    </html>
