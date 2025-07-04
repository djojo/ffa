<?php

namespace Dompdf;

require_once(__DIR__ . '/../../../config.php');

include $CFG->dirroot . '/theme/remui/views/utils.php';

global $USER, $DB, $CFG;

$content =  '';

$groupid = $_GET['groupid'];

//on va chercher le cours
$querycourse = 'SELECT c.*, g.name as groupname
FROM mdl_groups g
JOIN mdl_course c ON c.id = g.courseid
WHERE g.id = ' . $groupid;

$courseresult = $DB->get_records_sql($querycourse, null);

$course = reset($courseresult);

$content .= '<h1>Carnet de note du groupe : ' . $course->groupname . '</h1>';
$content .= '<h3>Formation : ' . $course->fullname . '</h3>';

$session = $DB->get_record('smartch_session', ['groupid' => $groupid]);

if ($session) {
  $content .= '<div>Session du ' . userdate($session->startdate, get_string('strftimedate')) . ' au ' . userdate($session->enddate, get_string('strftimedate') . '</div>');
}

$content .= '<div style="margin:10px 0;">Extraction du carnet de note le ' . userdate(Time(), get_string('strftimedate')) . '</div>';


//on va chercher les membres du groupe
$querygroupmembers = 'SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, r.shortname, r.id as roleid 
FROM mdl_role_assignments AS ra 
LEFT JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
LEFT JOIN mdl_role AS r ON ra.roleid = r.id 
LEFT JOIN mdl_context AS c ON c.id = ra.contextid 
LEFT JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
LEFT JOIN mdl_user u ON u.id = ue.userid
LEFT JOIN mdl_groups_members gm ON u.id = gm.userid
WHERE gm.groupid = ' . $groupid . '
AND r.shortname = "student"
ORDER BY u.lastname ASC';

$groupmembers = $DB->get_records_sql($querygroupmembers, null);

//on va chercher les sections
$sections = getCourseSections($course->id);

//on va chercher toutes les activités
$activities = getCourseActivitiesRapport($course->id);
// var_dump($activities);
// die();

$content .= '<table>';
$content .= '<tbody>';
$content .= '<tr>';
$content .= '<td  rowspan="2">Nom Prénom de l\'apprenant</td>';
$content .= '<td  rowspan="2">Adresse courriel</td>';
$content .= '<td  rowspan="2">N° individu</td>';
// $content .= '<td  rowspan="2">% de progression totale</td>';
// $content .= '<td  rowspan="2">Temps total passé</td>';

foreach ($sections as $section) {

  $tableau = [];
  ///////////////
  if ($session) {
    //on va chercher le nombre de planning dans la section disponible
    $sectionsplannings = getSectionPlannings($course->id, $session->id, $section->id);
    $totalsectionsplannings = count($sectionsplannings);
  }
  //////////

  //on compte le nombre de matière
  $tableau = explode(',', $section->sequence);
  
  $nbmodule = 0;
  foreach ($tableau as $moduleid) {
    $activity = null; // Réinitialisation avant chaque recherche
    //on cherche dans le tableau des activités
    foreach ($activities as $activityy) {
      if ($activityy->id == $moduleid) {
        $activity = $activityy;
        break; // Sortir de la boucle dès que l'élément est trouvé
      }
    }
    if($activity){
      if ($activity->activitytype == 'face2face') {
        //On va chercher le nombre de planning dans cette section
        if ($totalsectionsplannings > 0) {
          //si il reste des plannings dans cette section à mettre
          $totalsectionsplannings--;
          $nbmodule++;
        }
      } else if ($activity->activityname && $activity->activitytype != "folder") {
        $nbmodule++;
      }
    }
    
  }
  $sectionname = $section->name;
  if ($sectionname == "") {
    $sectionname = "Généralités";
  }
  // N'afficher la section que si elle contient des activités
  if($nbmodule > 0){
    $content .= '<td  colspan="' . $nbmodule . '">' . $sectionname . '</td>';
  }
  
}

$content .= '</tr>';
$content .= '<tr>';

foreach ($sections as $section) {

  //on compte le nombre de matière
  $tableau = explode(',', $section->sequence);
  foreach ($tableau as $moduleid) {
    $activity = null; // Réinitialisation avant chaque recherche
    //on cherche dans le tableau des activités
    foreach ($activities as $activityy) {
      if ($activityy->id == $moduleid) {
        $activity = $activityy;
        break; // Sortir de la boucle dès que l'élément est trouvé
      }
    }
    if($activity){
      if ($activity->activityname) {
        $content .= '<td>' . $activity->activityname . '</td>';
      }
    }
    
  }
}

$content .= '</tr>';

foreach ($groupmembers as $groupmember) {

  $content .= '<tr>';

  $content .= '<td>' . $groupmember->firstname . ' ' . $groupmember->lastname . '</td>';
  $content .= '<td>' . $groupmember->email . '</td>';
  $content .= '<td>' . $groupmember->id . '</td>';

  foreach ($sections as $section) {
    //on compte le nombre de matière
    $tableau = explode(',', $section->sequence);
    foreach ($tableau as $moduleid) {
      $activity = null; // Réinitialisation avant chaque recherche
      //on cherche dans le tableau des activités
      foreach ($activities as $activityy) {
        if ($activityy->id == $moduleid) {
          
          $activity = $activityy;
          break; // Sortir de la boucle dès que l'élément est trouvé
        }
      }
      if($activity){
        // $content .= '<td>' . $activityy->activityname . '</td>';
        if ($activity->activityname) {
          $grade = getModuleGrade($groupmember->id, $activity->id);
          $content .= '<td>' . $grade . '</td>';
        }
      }
    }
  }

  $content .= '</tr>';
}

$content .= '</tbody>';
$content .= '</table>';

$content .= '<div>';

$content .= '</div>';


require_once '../dompdf/autoload.inc.php';

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$css = '<link type="text/css" href="' . $CFG->dirroot . '/local/concorde_plugin/exports/export.css" rel="stylesheet" />';

$html = '
<html>
<head>
    <meta http-equiv="Content-Type" content="charset=utf-8" />
    <style type="text/css">
    @page {
        margin: 50px;
      }
      
      
      table {
        border-radius: 5px;
        width: 100%;
      }
      
      thead {
        background: white;
        color: #00315a;
        display: table-header-group;
        vertical-align: middle;
      }
      
      tr:nth-child(even) {
        background-color: #f2f2f2;
      }
      
     
      
      th,
      td {
        text-align: center;
      }
      th,
      tr,
      td {
        
      }
      h1,
      h2 {
        color: #00315a !important;
      }
      h2 {
        margin-top: 50px;
        text-decoration: underline !important;
      }
      @font-face {
        font-family: "Open Sans";
        font-style: normal;
        font-weight: normal;
        src: url(http://themes.googleusercontent.com/static/fonts/opensans/v8/cJZKeOuBrn4kERxqtaUH3aCWcynf_cDxXwCLxiixG1c.ttf)
          format("truetype");
      }
      body {
        font-family: "source_sans_proregular", Calibri, Candara, Segoe, Segoe UI,
          Optima, Arial, sans-serif;
        font-size: 0.3rem;
      }
      
    </style>
</head>
<body>
' . $content . '
</body>
</html>
';
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('Rapport-' . $course->fullname . '-' . date("d-m-Y") . '.pdf', array("Attachment" => false));
exit(0);
