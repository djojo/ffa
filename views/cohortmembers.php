<?php

use tool_brickfield\local\areas\mod_choice\option;

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');
require_once($CFG->dirroot . '/cohort/lib.php');
// require_once($CFG->dirroot.'/enrol/cohort/lib.php');
// require_once($CFG->dirroot.'/group/lib.php');

require_login();
isPortailRH();

global $USER, $DB, $CFG;

$cohortid = required_param('cohortid', PARAM_INT);
$cohort = $DB->get_record('cohort', ['id' => $cohortid]);
// $courseid = optional_param('courseid', null, PARAM_INT);
$userid = optional_param('userid', null, PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);
$messagesent = optional_param('messagesent', null, PARAM_TEXT);

if($userid){
    $user = $DB->get_record('user', ['id'=>$userid]);
}
$messagenotif = null;
if($userid && $action == "sync"){
    $exist = $DB->get_record('cohort_members', ['userid' => $userid, 'cohortid' => $cohortid]);
    if (!$exist){
        //on ajoute au groupe
        cohort_add_member($cohortid, $userid);
        $messagenotif = $user->firstname . ' ' . $user->lastname . ' a été ajouté au groupe ' . $cohort->name . '.';
        
    }
} else if($userid && $action == "desync"){
    $exist = $DB->get_record('cohort_members', ['userid' => $userid, 'cohortid' => $cohortid]);
    if ($exist) {
        //on supprime du groupe
        cohort_remove_member($cohortid, $userid);
        $textnotification = "Utilisateur ajouté";
        $messagenotif = $user->firstname . ' ' . $user->lastname . ' a été supprimé du groupe ' . $cohort->name . '.';
    }
} else if(!empty($messagesent)){
    $messagenotif = 'Message envoyé';
}

$content = '';
$paginationtitle = '';
$prevurl = '';
$nexturl = '';

//On va chercher le rôle le plus haut de l'utilisateur
$rolename = getMainRole();

isStudent();

$context = context_system::instance();
$PAGE->set_url(new moodle_url('/theme/remui/views/cohortmembers.php'), ['cohortid' => $cohortid]);
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Membres du groupe");

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

// echo html_writer::start_div('container');

$search = optional_param('search', '', PARAM_TEXT);
$pageno = optional_param('pageno', 1, PARAM_TEXT);

//le tableau des parametres pour la recherche et pagination
$params = array();
$filter = '';

// $param0['paramname'] = "courseid";
// $param0['paramvalue'] = $courseid;
// array_push($params, $param0);
// $filter = '&courseid=' . $courseid;


$param0['paramname'] = "cohortid";
$param0['paramvalue'] = $cohortid;
array_push($params, $param0);
$filter = '&cohortid=' . $cohortid;

if ($search != '') {
    $param1['paramname'] = "search";
    $param1['paramvalue'] = $search;
    array_push($params, $param1);
    $filter = '&search=' . $search;
}

$no_of_records_per_page = 5;
$offset = ($pageno - 1) * $no_of_records_per_page;

$filtersql = "";
if (!empty($search)) {
    // $filtersql = ' AND (u.email LIKE "%' . $search . '%" )';
}

$queryusers = 'SELECT u.id, u.firstname, u.lastname, u.email
        FROM mdl_cohort co
        JOIN mdl_cohort_members cm ON cm.cohortid = co.id
        JOIN mdl_user u ON u.id = cm.userid
        WHERE co.id = ' . $cohortid . '
        AND u.deleted = 0 AND u.suspended = 0
        '.$filtersql.'
        LIMIT ' . $offset . ', ' . $no_of_records_per_page . '
        ';
$total_pages_sql = 'SELECT COUNT(*) count 
        FROM mdl_cohort co
        JOIN mdl_cohort_members cm ON cm.cohortid = co.id
        JOIN mdl_user u ON u.id = cm.userid
        WHERE co.id = ' . $cohortid . '
        AND u.deleted = 0 AND u.suspended = 0
        '.$filtersql.'';


$users = $DB->get_records_sql($queryusers, null);

// $allcourses = $DB->get_records('course', null);

$result = $DB->get_records_sql($total_pages_sql, null);
$total_rows = reset($result)->count;
$total_pages = ceil($total_rows / $no_of_records_per_page);

//le header avec bouton de retour au panneau admin
$templatecontextheader = (object)[
    'url' => new moodle_url('/theme/remui/views/cohorts.php'),
    'textcontent' => 'Retour aux groupes'
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_back', $templatecontextheader);

$content .= '<div class="row" style="margin:50px 0;"></div>';


require_once('./cohortmenu.php');


//barre de recherche des parcours
$templatecontext = (object)[
    'formurl' => new moodle_url('/theme/remui/views/cohortmembers.php'),
    'textcontent' => 'Membres du groupe : '.$cohort->name,
    'lang_search' => "Rechercher",
    'params' => $params,
    'search' => $search
];
$content .= $OUTPUT->render_from_template('theme_remui/smartch_header_search', $templatecontext);


//La pagination
if (count($users) == 0) {
    $paginationtitle .= 'Aucun résultat';
} else if (count($users) == 1) {
    $paginationtitle .= '1 résultat';
} else {
    $paginationtitle .= $total_rows . ' résultats - page ' . $pageno . ' sur ' . $total_pages . '';
}
$paginationarray = range(1, $total_pages); // array(1, 2, 3)


if ($pageno == 1) {
    $previous = false;
} else {
    $previous = true;
    $newpage = $pageno - 1;
    $prevurl = new moodle_url('/theme/remui/views/cohortmembers.php?pageno=' . $newpage) . $filter;
}

if ($pageno == $total_pages) {
    $next = false;
} else {
    $next = true;
    $newpage = $pageno + 1;
    $nexturl = new moodle_url('/theme/remui/views/cohortmembers.php?pageno=' . $newpage) . $filter;
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
    'formurl' => new moodle_url('/theme/remui/views/cohortmembers.php')
];


if($cohort->name == "Employés FFF"){
    $content .= '<div class="row">
        <div class="col-12">
            <p style="margin:10px 0;">Ce groupe contient tous les membres de la plateforme. Au moment de l\'inscription d\'un nouvel utilisateur, il est automatiquement ajouté à ce groupe.</p>
        </div>
    </div>
    ';
}

if (count($users) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
} else {
    $content .= nothingtodisplay("Aucun membre pour l'instant...");
}


//affichage de la table de tous les utilisateurs
$content .= '<div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <table class="smartch_table">
                <thead style="display:none;">
                    <tr>
                        <th>Membre</th>
                        <th>Email</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>';
foreach ($users as $user) {


    $content .= '<tr>
                    <td style="text-transform:capitalize;text-align:left;">
                        <a href="' . new moodle_url('/theme/remui/views/adminuser.php') . '?userid=' . $user->id . '">' . $user->firstname . ' ' . $user->lastname . '</a>
                    </td>
                    <td>
                        ' . $user->email . '
                    </td>
                    <td>';
                    $content .= '<a class="smartch_table_btn mr-2" href="' . new moodle_url('/theme/remui/views/usermessage.php') . '?userid=' . $user->id . '&returnurl='.$PAGE->url.'">
                        <svg style="width:20px;margin-right:5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        Envoyer un message
                    </a>';
                    if($cohort->name != "Employés FFF"){
                        $content .= '<a class="smartch_table_btn" onclick="deleteFromGroup(\'' . new moodle_url('/theme/remui/views/cohortmembers.php') . '?cohortid='.$cohortid.'&userid=' . $user->id . '&action=desync\', \'group\')">
                            <svg style="width:20px;margin-right:5px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"  class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Supprimer le membre du groupe
                        </a>';
                    }
            $content .= '</td>
                </tr>';
}

$content .= '</tbody>
            </table>
        </div>
    </div>';

//la pagination en bas
if (count($users) > 0) {
    $content .= $OUTPUT->render_from_template('theme_remui/smartch_header_pagination', $templatecontextpagination);
}

if($cohort->name != "Employés FFF"){
    // ajout d'un membre
    $content .= '<div class="row" id="addmember">';
    $content .= '<div class="col-md-12">';
    $content .= '<h4 style="letter-spacing:1px;max-width:70%;cursor:pointer;" class="FFABold FFF-Blue mt-5">Ajouter un membre au groupe : '.$cohort->name . '</h4>';
    // $content .= '<form class="mt-5" action="" method="post">';
    $content .= '<div class="mt-5">';
    $content .= '<label class="mr-2" for="startdate">Chercher un membre</label>';
    $content .= '<input type="hidden" name="cohortid" value="'.$cohortid.'"/>';
    $content .= '<input type="hidden" name="action" value="sync"/>';
    $content .= '<input id="searchuser" onkeyup="checkEnter(event)" class="smartch_input" type="text" name="search"/>';

    $content .= '<a onclick="getUsers();" class="smartch_btn ml-5">Chercher</a>';

    $content .= '<table class="smartch_table mt-5">';
    $content .= '<tbody id="boxresultsearch"></tbody>';
    $content .= '</table>';

    $content .= '</div>';

    // $content .= '</form>';

    $content .= '</div>'; //md12
    $content .= '</div>'; //row
}





// $content .= html_writer::end_div(); //container

if($messagenotif){
    displayNotification($messagenotif);
}


echo $content;

echo $OUTPUT->footer();

$urlsearchuser = new moodle_url('/theme/remui/views/searchuser.php');
$urlsyncuser = new moodle_url('/theme/remui/views/cohortmembers.php?cohortid='.$cohortid);


echo '<script>

function checkEnter(event) {

    // Vérifie si la touche entrée est pressé
    if (event.keyCode === 13) {
      getUsers();
    } else {
        setTimeout(()=>{
            getUsers();
        }, 300);
    }
  }

function getUsers(){

    const search = document.getElementById("searchuser").value;

    // Créer une instance de l\'objet XMLHttpRequest
    var xhr = new XMLHttpRequest();

    // Définir l\'action à effectuer lorsque la requête est terminée (réussie ou échouée)
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // La requête est terminée avec succès (status 200)
                // Le résultat de la requête est contenu dans xhr.responseText
                var data = JSON.parse(xhr.responseText);
                let htmlmessagebox = "";

                // console.log(data);

                //On regarde si on peut reply au thread
                
                let html = "";

                if(data.users){
                    // console.log(data.users)
                    
                    // alert(data.users.length)

                    Array.from(data.users).forEach(user=>{
                        console.log(user)
                        html += "<tr>";
                        html += "<td>" + user.firstname + " " + user.lastname + "</td>";
                        html += "<td>" + user.email + "</td>";
                        html += "<td><a class=\'smartch_btn\' href=\''.$urlsyncuser.'&userid="+user.id+"&action=sync#addmember\'>Ajouter au groupe</a></td>";
                        html += "</tr>";
                    })

                    
                }
                else{
                    html += "<div style=\'height: 100%; display: flex; align-items: center; justify-content: center; font-size: 1.2em;\'>Aucun utilisateur trouvé...</div>";
                }

                document.querySelector("#boxresultsearch").innerHTML = html;

            } else {
                // La requête a échoué (le statut n\'est pas 200)
                console.error("La requête a échoué avec le statut : " + xhr.status);
            }
        }
    };

  // Définir la méthode de requête et l\'URL de l\'API à appeler
  var apiURL = "' . $urlsearchuser . '?cohortid='.$cohortid.'&search="+search; // Remplacez par l\'URL de l\'API réelle
 
  xhr.open("GET", apiURL, true);

  // Facultatif : si vous envoyez des en-têtes, définissez-les ici (par exemple pour une authentification)

  // Envoyer la requête
  xhr.send();
}

</script>';

//pour la pagination
echo '<script>

    var els = document.getElementsByClassName("page' . $pageno . '");
    Array.from(els).forEach((el) => {
        el.setAttribute("selected", "selected");
    });

</script>';


echo '<script>
function deleteFromGroup(url, name){
    let text = "Voulez vous vraiment supprimer le membre du groupe ?";
    let btntext = "Supprimer"
    document.querySelector("#modal_title").innerHTML = text;
    document.querySelector("#modal_btn").innerHTML = btntext;
    document.querySelector("#modal_btn").href = url;
    document.querySelector(".smartch_modal_container").style.display = "flex";
}
</script>';
