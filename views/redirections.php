<?php

// $redirections = optional_param('redirect', 'adminmenu', PARAM_TEXT);

if ($return == "teams") {
    redirect($CFG->wwwroot . '/theme/remui/views/adminteams.php');
} else if ($return == "team") {
    redirect($CFG->wwwroot . '/theme/remui/views/adminteam.php?teamid=' . $teamid);
} else if ($return == "course") {
    redirect($CFG->wwwroot . '/theme/remui/views/formation.php?id=' . $courseid);
} else if ($return == "dashboard") {
    redirect($CFG->wwwroot . '/my');
} else if ($return == "adminmenu") {
    redirect($CFG->wwwroot . '/theme/remui/views/adminmenu.php');
} else if ($return == "user") {
    redirect($CFG->wwwroot . '/theme/remui/views/adminuser.php?userid=' . $userid);
}
