<?php

if (!$content) {
    $content = "";
}

$return = optional_param('return', 'adminmenu', PARAM_TEXT);

if ($return == "teams") {
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/adminteams.php'),
        'textcontent' => 'Retour aux groupes'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "team") {
    $teamid = optional_param('teamid', 1, PARAM_INT);
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/adminteam.php?teamid=' . $teamid),
        'textcontent' => 'Retour au groupe'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "group") {
    $teamid = optional_param('groupid', 1, PARAM_INT);
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/groups/details.php?teamid=' . $teamid),
        'textcontent' => 'Retour au groupe'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "groups") {
    $teamid = optional_param('groupid', 1, PARAM_INT);
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/groups/index.php?teamid=' . $teamid),
        'textcontent' => 'Retour aux groupes'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "course") {
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/formation.php?id=' . $courseid),
        'textcontent' => 'Retour au parcours'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "users") {
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/users/index.php'),
        'textcontent' => 'Retour aux utilisateurs'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "dashboard") {
    $templatecontextheader = (object)[
        'url' => new moodle_url('/my'),
        'textcontent' => 'Retour au tableau de bord'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "adminmenu") {
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/adminmenu.php'),
        'textcontent' => 'Retour au panneau de configuration'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
} else if ($return == "adminformations") {
    $templatecontextheader = (object)[
        'url' => new moodle_url('/theme/remui/views/adminformations.php'),
        'textcontent' => 'Retour aux formations'
    ];
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);
}
