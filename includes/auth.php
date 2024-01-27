<pre>
<?php
require 'include.php';
session_start();

session_unset();

$login = $_POST['login'];
$password = $_POST['password'];

if (!empty($login) && !empty($password)) {    
    $student = R::findOne('students', 'login = ?', [$login]);
    $teacher = R::findOne('teachers', 'login = ?', [$login]);

    if ($student && !$teacher && $student['password'] == $password) {
        $_SESSION['user']['id'] = $student->id;
        $_SESSION['user']['numzachetka'] = $student->numzachetka;
        $_SESSION['user']['type'] = $student->type;
        $_SESSION['user']['username'] = $student->username;
        $_SESSION['user']['groupname'] = $student->groupname;
        $_SESSION['user']['subgroup'] = $student->subgroup;

        // print_r($_SESSION);
        header('Location: /');
        exit;
    } else 
    if (!$student && $teacher && $teacher['password'] == $password) {
        $_SESSION['user']['id'] = $teacher->id;
        $_SESSION['user']['type'] = $teacher->type;
        $_SESSION['user']['username'] = $teacher->username;
        $_SESSION['user']['position'] = $teacher->position;

        // print_r($_SESSION);
        header('Location: /');
        exit;
    } else {
        $_SESSION['errors'] = 'Неправильный логин или пароль';
    }
}
?>
</pre>