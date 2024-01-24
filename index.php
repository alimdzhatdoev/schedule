<?php require 'includes/include.php'; ?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    $tableName = 'lessons';
    $separation = isset($_POST['week']) ? $_POST['week'] : 'числитель';
    $subgroup = isset($_POST['subgroup']) ? $_POST['subgroup'] : '1';
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


    <form method="post" action="" class="selectDay">
        <select name="group" id="group">
            <option value="" disabled selected>Выберите группу</option>
            <?php foreach ($groupTitles as $title): ?>
                <option value="<?php echo $title; ?>" <?php echo ($_POST['group'] == $title) ? 'selected' : ''; ?>>
                    <?php echo $title; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="week" id="week">
            <option value="числитель" <?php echo ($_POST['week'] == 'числитель') ? 'selected' : ''; ?>>Числитель</option>
            <option value="знаменатель" <?php echo ($_POST['week'] == 'знаменатель') ? 'selected' : ''; ?>>Знаменатель
            </option>
        </select>
        <select name="subgroup" id="subgroup">
            <option value="1" <?php echo ($_POST['subgroup'] == '1') ? 'selected' : ''; ?>>Первая подгруппа</option>
            <option value="2" <?php echo ($_POST['subgroup'] == '2') ? 'selected' : ''; ?>>Вторая подгруппа</option>
        </select>

        <select name="day" id="day">
            <option value="today" <?php echo ($_POST['day'] == 'today') ? 'selected' : ''; ?>>Сегодня</option>
            <option value="tomorrow" <?php echo ($_POST['day'] == 'tomorrow') ? 'selected' : ''; ?>>Завтра</option>
            <option value="all" <?php echo ($_POST['day'] == 'all') ? 'selected' : ''; ?>>Вся неделя</option>
        </select>
        <button type="submit">Показать расписание</button>
    </form>

    <div class="lessons">
        <?php
        $selectedDay = isset($_POST['day']) ? $_POST['day'] : 'today';
        $selectedGroup = isset($_POST['group']) ? $_POST['group'] : '';

        foreach ($lessonsByDay as $groupTitle => $groups):
            if ($selectedGroup != '' && $selectedGroup != $groupTitle) {
                continue; // Skip if the group is not the selected group
            }

            foreach ($groups as $weekday => $dayLessons):
                if (
                    ($selectedDay == 'all')
                    || ($selectedDay == 'today' && $weekday == $russianDay[date('l')])
                    || ($selectedDay == 'tomorrow' && $weekday == $russianDay[date('l', strtotime('tomorrow'))])
                ):
                    ?>
                    <div class="lessons_day">
                        <div class="lessons_day__title">
                            <?php echo ($weekday); ?>
                        </div>

                        <?php foreach ($dayLessons as $lesson):
                            if (
                                ($lesson['separation'] == $separation || $lesson['separation'] == '0')
                                &&
                                ($lesson['subgroup'] == $subgroup || $lesson['subgroup'] == '0')
                            ): ?>
                                <div class="lessons_day__lesson">
                                    <div class="lessons_day__lesson___num">
                                        <?php echo ($lesson['number'] . '.'); ?>
                                    </div>
                                    <div class="lessons_day__lesson___data">
                                        <div class="lessons_day__lesson___data____elem">
                                            <?php echo ($lesson['title']); ?>
                                        </div>
                                        <div class="lessons_day__lesson___data____elem">
                                            <?php echo ('Преподаватель: ' . '<span>' . $lesson['teacher'] . '</span>'); ?>
                                        </div>
                                        <div class="lessons_day__lesson___data____elem">
                                            <?php echo ('Аудитория: ' . '<span>' . $lesson['auditorium'] . '</span>'); ?>
                                        </div>
                                        <div class="lessons_day__lesson___data____elem">
                                            <?php echo ('Тип занятия: ' . '<span>' . $lesson['type'] . '</span>'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</body>

</html>