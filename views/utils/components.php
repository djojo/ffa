<?php

function displayCourseEdit($course){

    global $CFG;

    $content = "";

    $content .= '<style>
        .secondary-navigation {
            display:none;
        }
        #page{
            background:transparent !important;
        }

        #page.drawers .main-inner {
            background: transparent !important;
            margin-top: 0px;
        }

        @media screen and (max-width: 830px) {
            #page.drawers .main-inner{
                margin-top:40px;
            }
        }
    </style>';

    $urlback = new moodle_url('/theme/remui/views/courses/index.php');

    //le retour
    $content .= '<a href="'.$urlback.'" style="font-size:1rem;cursor: pointer; display: flex; align-items: center; position: relative; top: 30px;" >
        <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
        </svg>
        <div class="ml-4 FFF-White FFARegular">Retour</div>
    </a>';

    $content .= '<div style="display:flex;justify-content: space-between;align-items:center;">';

    //le titre
    $content .= '<h3 class="FFF-title1" style="margin-top: 80px;transform:translate(0,0) !important;">
        <span class="FFABold FFF-White" style="letter-spacing:1px;">' . $course->fullname . '</span>
    </h3>';

    $editurl = new moodle_url('/course/edit.php?id='.$course->id);
    $content .= '<a href="'.$editurl.'" class="smartch_btn" style="background:#00315a; color:white !important;">
        <svg style="width:20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
    Paramètres</a>';

    $content .= '</div>';


    return $content;
}
function displayCourseEditParams($course){

    global $CFG;

    $content = "";

    $content .= '<style>
        .secondary-navigation {
            display:none;
        }
        #page{
            background:transparent !important;
        }

        #page.drawers .main-inner {
            background: transparent !important;
            margin-top: 0px;
        }

        @media screen and (max-width: 830px) {
            #page.drawers .main-inner{
                margin-top:40px;
            }
        }
    </style>';

    $urlback = new moodle_url('/course/view.php?id='.$course->id);

    //le retour
    $content .= '<a href="'.$urlback.'" style="font-size:1rem;cursor: pointer; display: flex; align-items: center; position: relative; top: 30px;" >
        <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
        </svg>
        <div class="ml-4 FFF-White FFARegular">Retour</div>
    </a>';

    $content .= '<div style="display:flex;justify-content: space-between;align-items:center;">';

    //le titre
    $content .= '<h3 class="FFF-title1" style="margin-top: 80px;transform:translate(0,0) !important;">
        <span class="FFABold FFF-White" style="letter-spacing:1px;">' . $course->fullname . '</span>
    </h3>';

    $content .= '</div>';


    return $content;
}

function displayActivityModeEdit($course){

    $content = '';
    $content .= '<style>

    .secondary-navigation.edw-tabs-navigation {
        display: none;
    }

    h2 {
    background: white;
        padding: 20px;
    }

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



    $urlback = new moodle_url('/theme/remui/views/courses/details.php?id=' . $course->id);

    $content .= '<a href="'.$urlback.'" style="font-size:1rem;cursor: pointer; display: flex; align-items: center; position: relative; top: 30px;" >
        <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
        </svg>
        <div class="ml-4 FFF-White FFARegular">Retour</div>
    </a>';
    
    //le titre
    $content .= '<h3 class="FFF-title1" style="margin-top: 80px;">
        <span class="FFABold FFF-White" style="letter-spacing:1px;">'.$course->fullname.'</span>
    </h3>';

    return $content;
}


function displayHeaderNotifications(){

    global $CFG;

    $content = "";

    $content .= '<style>
        .secondary-navigation {
            display:none;
        }

        #page{
            background:transparent !important;
        }

        #page.drawers .main-inner {
            background: transparent !important;
            margin-top: 0px;
        }

        @media screen and (max-width: 830px) {
            #page.drawers .main-inner{
                margin-top:40px;
            }
        }
    </style>';

    $urlback = new moodle_url('/');

    //le retour
    $content .= '<a href="'.$urlback.'" style="font-size:1rem;cursor: pointer; display: flex; align-items: center; position: relative; top: 30px;" >
        <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
        </svg>
        <div class="ml-4 FFF-White FFARegular">Retour</div>
    </a>';

    //le titre
    $content .= '<h3 class="FFF-title1" style="margin-top: 80px;transform:translate(0,0) !important;">
        <span class="FFABold FFF-White" style="letter-spacing:1px;">Notifications</span>
    </h3>';


    return $content;
}