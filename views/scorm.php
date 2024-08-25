<?php

$querycategory = 'SELECT fullname from mdl_course
                WHERE id = ' . $scorm->course;

$course = $DB->get_records_sql($querycategory, null);
$name = reset($course)->fullname;

echo '<div class="row">
<div class="col-lg-12" >
  <nav class="breadcrumb_widgets ccn-clip-l" aria-label="breadcrumb mb30">
    <h4 class="title float-left">' . $name . ' - ' . $scorm->name . '</h4>
    <ol class="breadcrumb float-right" >
      <ol class="breadcrumb" >
<li class="breadcrumb-item">
<a href="/course/view.php?id=' . $scorm->course . '">Retour au menu du cours</a>
</li>
</ol>
    </ol>
  </nav>
</div>
</div>';

echo '<style>
    #ccn-main > .d-flex.flex-row-reverse.mb-2 {
        display: none !important;
    }
</style>';
