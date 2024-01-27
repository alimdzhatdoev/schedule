<?php
// print_r($_SESSION);

$current_week_number = (int)date('W');

if ($current_week_number % 2 === 0) {
    $_SESSION['week'] = 'числитель';
} else {
    $_SESSION['week'] = 'знаменатель';
}

$separation = $_SESSION['week'];

$subgroup = $_SESSION['user']['subgroup'];
$selectedGroup = $_SESSION['user']['groupname'];

$selectedDay = isset($_POST['day']) ? $_POST['day'] : 'today';

$tableName = 'lessons';

$lessons = R::findAll($tableName);

$data = [];
$count = 1;

foreach ($lessons as $lesson) {
    $data[] = [
        'institute' => $lesson['institute'],
        'direction' => $lesson['direction'],
        'grouptitle' => $lesson['grouptitle'],
        'weekday' => $lesson['weekday'],
        'number' => $lesson['number'],
        'title' => $lesson['title'],
        'teacher' => $lesson['teacher'],
        'auditorium' => $lesson['auditorium'],
        'type' => $lesson['type'],
        'subgroup' => $lesson['subgroup'],
        'separation' => $lesson['separation'],
    ];
    $count++;
}

$groupTitles = array_unique(array_column($data, 'grouptitle'));

$lessonsByDay = array();

foreach ($data as $lesson) {
    $weekday = $lesson['weekday'];
    $grouptitle = $lesson['grouptitle'];
    if (!isset($lessonsByDay[$grouptitle])) {
        $lessonsByDay[$grouptitle] = array();
    }
    if (!isset($lessonsByDay[$grouptitle][$weekday])) {
        $lessonsByDay[$grouptitle][$weekday] = array();
    }
    $lessonsByDay[$grouptitle][$weekday][] = $lesson;
}

$russianDay = [
    'Monday' => 'Понедельник',
    'Tuesday' => 'Вторник',
    'Wednesday' => 'Среда',
    'Thursday' => 'Четверг',
    'Friday' => 'Пятница',
    'Saturday' => 'Суббота',
    'Sunday' => 'Воскресенье',
];
?>