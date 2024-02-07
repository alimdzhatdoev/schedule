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

    <main>
        <?php require 'includes/getData.php'; ?>
        <?php
        $thisDay = '<b>' . $russianDay[date('l')] . ' (' . date('d.m.Y') . ')' . '</b>';
        $thisWeek = $_SESSION['week'];
        $nextDay = '';
        ?>

        <?php
        if ($_POST['day'] == 'tomorrow') {
            $current_date = date('Y-m-d');
            $current_day_of_week = date('w', strtotime($current_date));
            $tomorrow_date = date('Y-m-d', strtotime('+1 day', strtotime($current_date)));
            $tomorrow_day_of_week = date('w', strtotime($tomorrow_date));

            $nextDay = '<b>' . $russianDay[date('l', strtotime('tomorrow'))] . ' (' . date('d.m.Y', strtotime('tomorrow')) . ')' . '</b>';

            if ($current_day_of_week == 0 && $tomorrow_day_of_week != 0) {
                if ($_SESSION['week'] == 'числитель') {
                    $_SESSION['week'] = 'знаменатель';
                    $separation = 'знаменатель';
                    $thisWeek = $_SESSION['week'];
                } else
                    if ($_SESSION['week'] == 'знаменатель') {
                        $_SESSION['week'] = 'числитель';
                        $separation = 'числитель';
                        $thisWeek = $_SESSION['week'];
                    }
            }
        }
        ?>

        <?php
        if ($_SESSION['user']) { ?>
            <div class="tabSchedule tabShow">
                <div class="tabSchedule__thisWeek">
                    <div class="tabSchedule__thisWeek___block">
                        Сегодня:
                        <? echo $thisDay; ?>
                    </div>
                    <?php if ($nextDay != '') { ?>
                        <div class="tabSchedule__thisWeek___block">
                            Выбранный день:
                            <? echo $nextDay; ?>
                        </div>
                    <?php } ?>
                    <div class="tabSchedule__thisWeek___block">
                        Текущая неделя:
                        <? echo ('<b>' . $thisWeek . '</b>'); ?>
                    </div>
                </div>

                <form method="post" action="" class="selectDay">
                    <select name="day" id="day">
                        <option value="today" <?php echo ($_POST['day'] == 'today') ? 'selected' : ''; ?>>Сегодня</option>
                        <option value="tomorrow" <?php echo ($_POST['day'] == 'tomorrow') ? 'selected' : ''; ?>>Завтра
                        </option>
                        <option value="all" <?php echo ($_POST['day'] == 'all') ? 'selected' : ''; ?>>Вся неделя</option>
                    </select>

                    <button type="submit">Показать расписание</button>
                </form>

                <?php if ($_SESSION['user']['type'] == 'студент') { ?>
                    <div class="lessons">
                        <?php
                        $found_lessons = false; ?>

                        <?php foreach ($lessonsByDay as $groupTitle => $groups):
                            if ($selectedGroup != '' && $selectedGroup != $groupTitle) {
                                continue;
                            }

                            foreach ($groups as $weekday => $dayLessons):
                                if (
                                    ($selectedDay == 'all')
                                    || ($selectedDay == 'today' && $weekday == $russianDay[date('l')])
                                    || ($selectedDay == 'tomorrow' && $weekday == $russianDay[date('l', strtotime('tomorrow'))])
                                ): ?>
                                    <div class="lessons_day">
                                        <div class="lessons_day__title">
                                            <?php echo ($weekday); ?>
                                        </div>

                                        <?php foreach ($dayLessons as $lesson):
                                            if (
                                                ($lesson['separation'] == $separation || $lesson['separation'] == '-')
                                                &&
                                                ($lesson['subgroup'] == $subgroup || $lesson['subgroup'] == '-')
                                            ): ?>
                                                <?php
                                                $found_lessons = true;
                                                ?>
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
                        <?php endforeach;
                        if (!$found_lessons) {
                            echo "<div class='no_lessons_message'>Занятия не найдены</div>";
                        } ?>
                    </div>
                <?php } else if ($_SESSION['user']['type'] == 'преподаватель') { ?>
                        <div class="lessons">
                            <?php
                            $found_lessons_show = false;

                            foreach ($lessonsByDayTeachers as $weekday => $dayLessons):
                                $found_lessons_show = false;

                                foreach ($dayLessons as $lesson) {
                                    if ($lesson['teacher'] == $_SESSION['user']['username']) {
                                        $found_lessons_show = true;
                                        break;
                                    }
                                }

                                if (!$found_lessons_show) {
                                    continue;
                                }
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
                                            ($lesson['teacher'] == $_SESSION['user']['username'])
                                        ): ?>
                                                <?php
                                                $found_lessons = true;
                                                ?>
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
                                                        <?php echo ('Группа: ' . '<span>' . $lesson['grouptitle'] . '</span>'); ?>
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
                        <?php endforeach;
                            if (!$found_lessons) {
                                echo "<div class='no_lessons_message'>Занятия не найдены</div>";
                            } ?>
                        </div>
                <?php } ?>
            </div>

            <div class="tabProfile">
                <?php
                if ($_SESSION['user']) {
                    if ($_SESSION['user']['type'] == 'студент') { ?>
                        <div class="tabProfile_userData">
                            <div class="tabProfile_userData__photo">
                                <img src="refs/no-photo.webp" alt="">
                            </div>
                            <div class="tabProfile_userData__text">
                                <div class="tabProfile_userData__text___title">ФИО:</div>
                                <div class="tabProfile_userData__text___data">
                                    <?php echo ($_SESSION['user']['username']); ?>
                                </div>
                            </div>
                            <div class="tabProfile_userData__text">
                                <div class="tabProfile_userData__text___title">Номер зачетки:</div>
                                <div class="tabProfile_userData__text___data">
                                    <?php echo ($_SESSION['user']['numzachetka']); ?>
                                </div>
                            </div>
                            <div class="tabProfile_userData__text">
                                <div class="tabProfile_userData__text___title">Группа:</div>
                                <div class="tabProfile_userData__text___data">
                                    <?php echo ($_SESSION['user']['groupname']); ?>
                                </div>
                            </div>
                            <div class="tabProfile_userData__text">
                                <div class="tabProfile_userData__text___title">Подгруппа:</div>
                                <div class="tabProfile_userData__text___data">
                                    <?php echo ($_SESSION['user']['subgroup']); ?>
                                </div>
                            </div>
                        </div>
                        <a href="includes/logout.php" class="tabProfile_exit">
                            <img src="refs/exit.png" alt="">
                        </a>
                        <!-- <a href="includes/logout.php">Выход</a> -->
                    <?php } else if ($_SESSION['user']['type'] == 'преподаватель') { ?>
                            <div class="tabProfile_userData">
                                <div class="tabProfile_userData__photo">
                                    <img src="refs/no-photo.webp" alt="">
                                </div>
                                <div class="tabProfile_userData__text">
                                    <div class="tabProfile_userData__text___title">ФИО:</div>
                                    <div class="tabProfile_userData__text___data">
                                    <?php echo ($_SESSION['user']['username']); ?>
                                    </div>
                                </div>
                                <div class="tabProfile_userData__text">
                                    <div class="tabProfile_userData__text___title">Должность:</div>
                                    <div class="tabProfile_userData__text___data">
                                    <?php echo ($_SESSION['user']['position']); ?>
                                    </div>
                                </div>
                            </div>
                            <a href="includes/logout.php" class="tabProfile_exit">
                                <img src="refs/exit.png" alt="">
                            </a>
                    <?php }
                } else { ?>
                    <div class="tabProfile_auth">
                        <img src="refs/logo.png" alt="">
                        <h1>Авторизация</h1>
                        <form action="includes/auth.php" method="post">
                            <label for="">Логин</label>
                            <input type="text" name="login" placeholder="Введите логин">
                            <label for="">Пароль</label>
                            <input type="password" name="password" placeholder="Введите пароль">
                            <button type="submit">Вход</button>
                        </form>
                    </div>
                <?php } ?>

            </div>

            <div class="bottomMenu">
                <div class="bottomMenu_elem activeBottomElem roundedTopRight" onclick="changeTab(this, 'tabSchedule')">
                    Расписание</div>
                <div class="bottomMenu_elem" onclick="changeTab(this, 'tabProfile')">Профиль</div>
            </div>
        <?php } else { ?>
            <div class="tabProfile tabShow">
                <?php
                if ($_SESSION['user']) {
                    print_r('Текущая неделя: ' . $_SESSION['user']['week'] . '<br/>');
                    print_r('ФИО: ' . $_SESSION['user']['username'] . '<br/>');
                    print_r('Номер зачетки: ' . $_SESSION['user']['numzachetka'] . '<br/>');
                    print_r('Группа: ' . $_SESSION['user']['groupname'] . '<br/>');
                    print_r('Подгруппа: ' . $_SESSION['user']['subgroup'] . '<br/>');
                    ?>

                    <a href="includes/logout.php">Выход</a>
                <?php } else { ?>
                    <div class="tabProfile_auth">
                        <img src="refs/logo.png" alt="">
                        <h1>Авторизация</h1>
                        <form action="includes/auth.php" method="post">
                            <label for="">Логин</label>
                            <input type="text" name="login" placeholder="Введите логин">
                            <label for="">Пароль</label>
                            <input type="password" name="password" placeholder="Введите пароль">
                            <button type="submit">Вход</button>
                        </form>
                    </div>
                <?php } ?>

            </div>
        <?php } ?>

    </main>


</body>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/main.js"></script>

</html>