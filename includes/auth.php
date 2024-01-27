<pre>
<?php
require 'include.php';
session_start();

session_unset();

$login = $_POST['login'];
$password = $_POST['password'];

if (!empty($login) && !empty($password)) {
    $user = R::findOne('users', 'login = ?', [$login]);

    if ($user['password'] == $password) {
        $_SESSION['user']['id'] = $user->id;
        $_SESSION['user']['numzachetka'] = $user->numzachetka;
        $_SESSION['user']['username'] = $user->username;
        $_SESSION['user']['groupname'] = $user->groupname;
        $_SESSION['user']['subgroup'] = $user->subgroup;

        // print_r($_SESSION);
        header('Location: /');
        exit;
    } else {
        $_SESSION['errors'] = 'Неправильный логин или пароль';
    }
}
?>
</pre>