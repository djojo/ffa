<?php

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');

require_login();

global $USER, $DB, $CFG;

$filterparcours = '';
$content = '';
$paginationtitle = '';
$prevurl = '';
$nexturl = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

isStudent();

//on va chercher la config
$configportail = getConfigPortail();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/adminformations.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Formations");

echo '<style>

#page{
    background:transparent !important;
}

#topofscroll {
    background: transparent !important;
    margin-top: 0px !important;
}

@media screen and (max-width: 830px) {
    #topofscroll{
        margin-top:40px !important;
    }
}

</style>';

echo $OUTPUT->header();

smartchModal();


$togglevisible = optional_param('togglevisible', '', PARAM_INT);
$visible = optional_param('visible', 1, PARAM_INT);
$categoryid = optional_param('categoryid', '', PARAM_TEXT);
$courseid = optional_param('courseid', '', PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);

$allparams = "";

//si le filtre visible est actif
$filter = '';
if (!empty($visible)) {
    if ($visible == 1) {
        $filter = ' AND c.visible = 1 ';
        $allparams .= "&visible=1";
    } else if ($visible == 2) {
        $filter = ' AND c.visible = 0 ';
        $allparams .= "&visible=2";
    } else if ($visible == 3) {
        $allparams .= "&visible=3";
    }
}

//si il y a une category de choisi
if (!empty($categoryid)) {
    if ($categoryid != "all") {
        $filter .= ' AND cc.id = ' . $categoryid;
        $allparams .= "&categoryid=" . $categoryid;
    }
}

//on filtre seulement ses cours
if ($rolename == "teacher" || $rolename == "smalleditingteacher" || $rolename == "editingteacher") {
    $filterparcours = 'JOIN mdl_role_assignments ra ON ra.userid = ' . $USER->id . '
    JOIN mdl_context ct ON ct.id = ra.contextid AND c.id = ct.instanceid
    JOIN mdl_role r ON r.id = ra.roleid';
}

//on toggle la visibilité du cours 
if (!empty($togglevisible)) {
    // var_dump("toggle course");
    $course = $DB->get_record('course', ['id' => $courseid]);
    if ($course->visible == 1) {
        $course->visible = 0;
    } else {
        $course->visible = 1;
    }
    $DB->update_record('course', $course);
}

$no_of_records_per_page = 12;
$offset = ($pageno - 1) * $no_of_records_per_page;

if ($search != "") {
    $querycourses = 'SELECT c.id, c.fullname, c.visible, cc.name as category 
            FROM mdl_course c
            JOIN mdl_course_categories cc ON cc.id = c.category
            ' . $filterparcours . '
            WHERE c.format != "site"
            ' . $filter . '
            AND (lower(c.fullname) LIKE "%' . $search . '%")
            ORDER BY c.fullname ASC
            LIMIT ' . $offset . ', ' . $no_of_records_per_page;
    $total_pages_sql = 'SELECT COUNT(*) count 
        FROM mdl_course c
        JOIN mdl_course_categories cc ON cc.id = c.category
        ' . $filterparcours . '
        WHERE c.format != "site"
        ' . $filter . '
        AND (lower(c.fullname) LIKE "%' . $search . '%")';
} else {
    $querycourses = 'SELECT c.id, c.fullname,  c.visible, cc.name as category 
        FROM mdl_course c
        JOIN mdl_course_categories cc ON cc.id = c.category
        ' . $filterparcours . '
        WHERE c.format != "site"
        ' . $filter . '
        ORDER BY c.fullname ASC
        LIMIT ' . $offset . ', ' . $no_of_records_per_page . '
        ';
    $total_pages_sql = 'SELECT COUNT(*) count FROM mdl_course c
        JOIN mdl_course_categories cc ON cc.id = c.category
        ' . $filterparcours . '
        WHERE c.format != "site"
        ' . $filter . '
        ';
}
$courses = $DB->get_records_sql($querycourses, null);

$allcourses = $DB->get_records('course', null);

$result = $DB->get_records_sql($total_pages_sql, null);
$total_rows = reset($result)->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/adminmenu.php'),
    'textcontent' => 'Retour au panneau d\'administration'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

$content .= '<div class="row" style="margin:30px 0;"></div>';

$params = array();

//le tableau des parametres pour la pagination
$paramsvisible = array();
$param['paramname'] = "visible";
$param['paramvalue'] = $visible;
array_push($paramsvisible, $param);
array_push($params, $param);

$paramscategory = array();
$param['paramname'] = "categoryid";
$param['paramvalue'] = $categoryid;
array_push($paramscategory, $param);
array_push($params, $param);

//on va chercher toutes les catégories
$allcategories = $DB->get_records('course_categories', null);
//le tableau des parametres pour le filtre
$categories = array();
foreach ($allcategories as $categorie) {
    $cat['categoryname'] = $categorie->name;
    $cat['categoryid'] = $categorie->id;
    array_push($categories, $cat);
}

//barre de recherche des parcours
$templatecontext = (object)[
    'formurl' => new moodle_url('/theme/remui/views/adminformations.php'),
    'textcontent' => "Tous les parcours",
    'lang_search' => "Rechercher un parcours",
    'search' => $search,
    'visible' => $visible,
    // 'select' => $select,
    'select' => true,
    'category' => true,
    'cat' => $categories,
    'paramscategory' => $paramscategory,
    'paramsvisible' => $paramsvisible

];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_search', $templatecontext);

//La pagination
if (count($courses) == 0) {
    $paginationtitle .= 'Aucun résultat';
} else if (count($courses) == 1) {
    $paginationtitle .= '1 résultat';
} else {
    $paginationtitle .= $total_rows . ' résultats - page ' . $pageno . ' sur ' . $total_pages . '';
}

//si il n'y a qu'une page
if ($total_pages == 0) {
    $total_pages = 1;
    $paginationarray = range(1, 1); // array(1, 2, 3)
} else {
    $paginationarray = range(1, $total_pages); // array(1, 2, 3)
}





if ($pageno == 1) {
    $previous = false;
} else {
    $previous = true;
    $newpage = $pageno - 1;
    $prevurl = new moodle_url('/theme/remui/views/adminformations.php?pageno=' . $newpage) . $allparams;
}

if ($pageno == $total_pages) {
    $next = false;
} else {
    $next = true;
    $newpage = $pageno + 1;
    $nexturl = new moodle_url('/theme/remui/views/adminformations.php?pageno=' . $newpage) . $allparams;
}



//la pagination en haut
$templatecontextpagination = (object)[
    'paginationtitle' => $paginationtitle,
    'search' => $search,
    'params' => $params,
    'total_rows' => $total_rows,
    'total_pages' => $total_pages,
    'pageno' => $pageno,
    'previous' => $previous,
    'next' => $next,
    'prevurl' => $prevurl,
    'nexturl' => $nexturl,
    'paginationarray' => array_values($paginationarray),
    'formurl' => new moodle_url('/theme/remui/views/adminformations.php')
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);


//tous les parcours

if (count($courses) == 0) {
    $content .= nothingtodisplay("Aucun résultat...");
}

$content .= '<div class="row">';
foreach ($courses as $course) {

    $courseid = $course->id;
    $seeurl = $CFG->wwwroot . "/theme/remui/views/formation.php?id=" . $course->id . "&return=adminformations";
    $editurl = $CFG->wwwroot . "/course/view.php?id=" . $course->id;
    $togglevisibleurl = $CFG->wwwroot . "/theme/remui/views/adminformations.php?courseid=" . $course->id . "&togglevisible=1";

    $imgcourse = "";
    //On va chercher l'image du cours
    $course2 = new core_course_list_element($course);
    foreach ($course2->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            $imagepath = '/' . $file->get_contextid() .
                '/' . $file->get_component() .
                '/' . $file->get_filearea() .
                $file->get_filepath() .
                $file->get_filename();
            $imageurl = file_encode_url(
                $CFG->wwwroot . '/pluginfile.php',
                $imagepath,
                false
            );

            $imgcourse = $imageurl;
            // Use the first image found.
            break;
        }
    }
    // echo "<p>la chaine recherchée => " . $imgcourse . "</p>";
    // <div onmouseover="this.childNodes[2].style.display=\'block\'" class="fff-course-thumbnail-box-admin">

    $content .= '<div class="col-sm-12 col-md-6 col-lg-3 ">
    <div onmouseover="document.getElementById(\'options-' . $course->id . '\').style.display=\'block\'" onmouseout="document.getElementById(\'options-' . $course->id . '\').style.display=\'none\'" class="fff-course-thumbnail-box-admin">';
    $content .= '<div class="smartch_layer_thumbnail"></div>';
    if ($imgcourse == "") {
        $imgcourse = $CFG->wwwroot . '/theme/remui/pix/background.jpeg';
    }
    $content .= '<img src="' . $imgcourse . '" />';

    $content .= '<div class="fff-course-thumbnail-box-category">' . $course->category . '</div>';

    $content .= '<div class="fff-course-thumbnail-box-title">
            <h5 class="fff-course-thumbnail-title">' . longTitles($course->fullname) . '</h5>
        </div>

        <div id="options-' . $course->id . '" class="fff-course-thumbnail-box-options" style="justify-content: space-evenly;" id="thumbnail-{{id}}">
            <a data-toggle="tooltip" data-placement="top" title="Voir le parcours" href="' . $seeurl . '">
                <svg class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                </svg>
            </a>';


    //si le role permet de modifier un cours + le rendre visible
    if ($rolename == "super-admin" || $rolename == "manager" || $rolename == "editingteacher") {
        $content .= '
            <a data-toggle="tooltip" data-placement="top" title="Modifier le parcours" href="' . $editurl . '">
                <svg class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                </svg>
            </a>';

        if($configportail == "portailrh"){
            $usersurl = $CFG->wwwroot . "/theme/remui/views/courseusers.php?courseid=" . $course->id;
            $content .= '
            <a data-toggle="tooltip" data-placement="top" title="Voir les apprenants" href="' . $usersurl . '">
                <svg class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </a>';
            $cohortsurl = $CFG->wwwroot . "/theme/remui/views/cohorts.php?courseid=" . $course->id;
            $content .= '
            <a data-toggle="tooltip" data-placement="top" title="Voir les groupes" href="' . $cohortsurl . '">
                <svg class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </a>';
            
            //on regarde si l'utilisateur est admin
            if ($rolename == "super-admin" || $rolename == "manager") {
                //on check si il y a des apprenants dans la formations
                $countenroll = $DB->get_record_sql('SELECT COUNT(u.id) count 
                FROM mdl_user u
                JOIN mdl_user_enrolments ue ON ue.userid = u.id
                JOIN mdl_enrol e ON e.id = ue.enrolid
                WHERE e.courseid = ' . $course->id . '
                AND u.deleted = 0', null);
                if($countenroll->count > 1){
                    $possible = false;
                } else {
                    $possible = true;
                }
                // var_dump($countenroll);
                $content .= '
                    <a data-toggle="tooltip" data-placement="top" title="Supprimer la formation" onclick="deleteCourse(\'' . new moodle_url('/theme/remui/views/deletecourse.php') . '?id=' . $course->id . '\', \'group\', \''.$possible .'\')">
                        <svg class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </a>';
            }
        } else {
            //on check si c'est une formation gratuite
            if($course->category == "Formation gratuite"){
                //on regarde si l'utilisateur est admin
                if ($rolename == "super-admin" || $rolename == "manager") {
                    $content .= '
                    <a data-toggle="tooltip" data-placement="top" title="Supprimer la formation" href="' . new moodle_url('/course/delete.php') . '?id=' . $course->id . '">
                        <svg class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
    
                    </a>';
                }
            }
            
        }

        //si le cours est visible
        $content .= '<a  href="' . $togglevisibleurl . '">';
        if ($course->visible == 1) {
            $content .= '<svg data-toggle="tooltip" data-placement="top" title="Cacher le parcours" class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
          ';
        } else {
            $content .= '<svg data-toggle="tooltip" data-placement="top" title="Publier le parcours" class="iconsvg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          ';
        }
        $content .= '</a>';
    }




    $content .= ' </div>
    </div>
</div>';
}

$content .= '</div>';

//la pagination en bas
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);

echo $content;

echo $OUTPUT->footer();

//pour la pagination
echo '<script>

window.onload = function(){
    let els = document.getElementsByClassName("page' . $pageno . '");
    Array.from(els).forEach((el) => {
        el.setAttribute("selected", "selected");
    });
}
</script>';

if ($visible != 1) {
    echo '<script>document.getElementById("select' . $visible . '").setAttribute("selected", "selected")</script>';
}
if ($categoryid != "all") {
    echo '<script>document.getElementById("cat' . $categoryid . '").setAttribute("selected", "selected")</script>';
}



echo '<script>
function deleteCourse(url, name, possible){
    if(possible){
        let text = "Voulez vous vraiment supprimer la formation ?";
        let btntext = "Supprimer"
        document.querySelector("#modal_title").innerHTML = text;
        document.querySelector("#modal_btn").innerHTML = btntext;
        document.querySelector("#modal_btn").style.display = "block";
        document.querySelector("#modal_btn").href = url;
        document.querySelector(".smartch_modal_container").style.display = "flex";
    } else {
        let text = "<p>Vous ne pouvez pas supprimer la formation car il y a des apprenants inscrits ...</p>";
        text += "<p>Désinscrivez les apprenants puis réessayez</p>";
        document.querySelector("#modal_title").innerHTML = text;
        document.querySelector("#modal_btn").style.display = "none";
        document.querySelector(".smartch_modal_container").style.display = "flex";
    }
    

}
</script>';