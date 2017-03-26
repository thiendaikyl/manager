<?php

    define('LOADED', 1);
    require_once('global.php');

    if ($appUser->isLogin())
        $appAlert->info(lng('login.alert.login_already'), ALERT_INDEX, env('app.http.host'));

    $title = lng('login.title_page');
    $themes = [ env('resource.theme.login') ];
    $appAlert->setID(ALERT_LOGIN);
    require_once('header.php');

    $username = null;
    $password = null;

    if (isset($_POST['submit'])) {
        $user     = null;
        $username = addslashes($_POST['username']);
        $password = addslashes($_POST['password']);

        if (empty($username) || empty($password)) {
            $appAlert->danger(lng('login.alert.not_input_username_or_password'));
        } else if (($user = $appUser->isUser($username, $password, true)) == false) {
            $appAlert->danger(lng('login.alert.username_or_password_wrong'));
        } else if ($user == null) {
            $appAlert->danger(lng('login.alert.user_not_exists'));
        } else {
            $appUser->createSessionUser($user[Librarys\App\AppUser::KEY_USERNAME]);
            $appAlert->success(lng('login.alert.login_success'), ALERT_INDEX, env('app.http.host'));
        }
    }

    $appAlert->display();

?>

    <div id="login">
        <form action="login.php" method="post" id="login-form">
            <input type="hidden" name="<?php echo $boot->getCFSRToken()->getName(); ?>" value="<?php echo $boot->getCFSRToken()->getToken(); ?>"/>
            <input type="text" name="username" value="<?php echo stripslashes($username); ?>" placeholder="<?php echo lng('login.form.input_username_placeholder'); ?>"/>
            <input type="password" name="password" value="<?php echo stripslashes($password); ?>" placeholder="<?php echo lng('login.form.input_password_placeholder'); ?>"/>
            <div id="login-form-action">
                <a href="forgot_password.php" id="forgot-password">
                    <span><?php echo lng('login.form.forgot_password'); ?></span>
                </a>
                <button type="submit" name="submit">
                    <span><?php echo lng('login.form.button_login'); ?></span>
                </button>
            </div>
        </form>
    </div>

<?php require_once('footer.php'); ?>
