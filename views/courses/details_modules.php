<?php

if (!$session) {
    $session = false;
}
if (!$group) {
    $group = false;
}

$userselected = $DB->get_record('user', ['id' => $userid]);

// if ($userid) {
//     $titlecontenu = "CONTENU PÉDAGOGIQUE POUR " . $userselected->firstname . ' ' . $userselected->lastname;
// } else {
    $titlecontenu = "CONTENU PÉDAGOGIQUE";
// }


//les sections du cours

use core\progress\display;

$sections = getCourseSections($courseid);
// $coursemodules = array();

//on va chercher toutes les activités
$activities = getCourseActivities($courseid);

// var_dump($activities);

//la session
if ($group) {
    // var_dump($group->id);
    $session = $DB->get_record('smartch_session', ['groupid' => $group->id]);
    $sessionadress = "";
    if ($session) {
        $sessionsite = $session->site;
        if ($session->adress1 != "") {
            $sessionadress .= $session->adress1;
        }
        if ($session->adress2 != "") {
            $sessionadress .= ', ' . $session->adress2;
        }
        if ($session->zip != "") {
            $sessionadress .= ', ' . $session->zip;
        }
        if ($session->city != "") {
            $sessionadress .= ', ' . $session->city;
        }
    }
}


// $content .= '

// <h3 class="FFF-title1" style="display: flex;align-items: center;margin-top:50px;" id="modulesformation">
//     <span class="FFABlack FFF-Blue" style="margin-right:10px;letter-spacing:1px;">Détails du </span><span class="FFABlack FFF-Gold" style="letter-spacing:1px;">Parcours</span> 
// </h3>';


$content .= '<div class="row" style="padding: 0 40px;">';

// var_dump(countCourseActivities($courseid));
if (countCourseActivities($courseid) == 0) {
    $content .= '<div class="col-12">';
    $content .= nothingtodisplay("Le contenu de formation n'est pas encore disponible");
    $content .= '</div>';
} else {
    $content .= '<div class="col-xs-12 col-md-12 col-lg-4"  id="fff-my-courses" >';
    // $content .= '<div class="fff-my-courses-caroussel" >';
    // $content .= '<div class="fff-my-courses-caroussel-items"  id="fff-my-courses">';

    //pochette intervenant
    $icon0 = '<svg style="margin:10px;" width="40" height="46" viewBox="0 0 40 46" fill="none" xmlns="http://www.w3.org/2000/svg">
<g opacity="0.2">
<path d="M21.9494 7.66667H18.0506C17.2287 7.66667 16.5625 8.35316 16.5625 9.2C16.5625 10.0468 17.2287 10.7333 18.0506 10.7333H21.9494C22.7713 10.7333 23.4375 10.0468 23.4375 9.2C23.4375 8.35316 22.7713 7.66667 21.9494 7.66667Z" fill="white"/>
<path d="M35.3869 46H5.90774C4.68942 45.996 3.52213 45.4955 2.66064 44.6078C1.79916 43.7201 1.31345 42.5173 1.30952 41.262V4.738C1.31345 3.48265 1.79916 2.27987 2.66064 1.39219C3.52213 0.504522 4.68942 0.00404303 5.90774 0H35.3869C36.6052 0.00404303 37.7725 0.504522 38.634 1.39219C39.4955 2.27987 39.9812 3.48265 39.9851 4.738V41.262C39.9812 42.5173 39.4955 43.7201 38.634 44.6078C37.7725 45.4955 36.6052 45.996 35.3869 46ZM5.90774 3.06667C5.47755 3.06667 5.06498 3.24275 4.76079 3.55619C4.45661 3.86962 4.28571 4.29473 4.28571 4.738V41.262C4.28571 41.7053 4.45661 42.1304 4.76079 42.4438C5.06498 42.7572 5.47755 42.9333 5.90774 42.9333H35.3869C35.8171 42.9333 36.2297 42.7572 36.5338 42.4438C36.838 42.1304 37.0089 41.7053 37.0089 41.262V4.738C37.0089 4.29473 36.838 3.86962 36.5338 3.55619C36.2297 3.24275 35.8171 3.06667 35.3869 3.06667H5.90774Z" fill="white"/>
<path d="M4.46429 6.13333H1.4881C0.666243 6.13333 0 6.81983 0 7.66667C0 8.5135 0.666243 9.2 1.4881 9.2H4.46429C5.28614 9.2 5.95238 8.5135 5.95238 7.66667C5.95238 6.81983 5.28614 6.13333 4.46429 6.13333Z" fill="white"/>
<path d="M4.46429 21.4667H1.4881C0.666243 21.4667 0 22.1532 0 23C0 23.8468 0.666243 24.5333 1.4881 24.5333H4.46429C5.28614 24.5333 5.95238 23.8468 5.95238 23C5.95238 22.1532 5.28614 21.4667 4.46429 21.4667Z" fill="white"/>
<path d="M4.46429 36.8H1.4881C0.666243 36.8 0 37.4865 0 38.3333C0 39.1802 0.666243 39.8667 1.4881 39.8667H4.46429C5.28614 39.8667 5.95238 39.1802 5.95238 38.3333C5.95238 37.4865 5.28614 36.8 4.46429 36.8Z" fill="white"/>
<path d="M40 27.6H28.8244C28.3378 27.6 27.856 27.5012 27.4064 27.3094C26.9569 27.1175 26.5484 26.8363 26.2043 26.4817C25.8602 26.1272 25.5873 25.7063 25.4011 25.2431C25.2149 24.7799 25.119 24.2834 25.119 23.782V22.2487C25.1151 21.7447 25.2081 21.2449 25.3925 20.7782C25.577 20.3114 25.8493 19.8869 26.1937 19.5291C26.5382 19.1713 26.948 18.8873 27.3995 18.6935C27.851 18.4997 28.3353 18.4 28.8244 18.4H40V27.6ZM28.8244 21.4667C28.631 21.4667 28.4455 21.5458 28.3088 21.6867C28.1721 21.8276 28.0952 22.0187 28.0952 22.218V23.7513C28.0952 23.9506 28.1721 24.1417 28.3088 24.2826C28.4455 24.4235 28.631 24.5027 28.8244 24.5027H37.0238V21.4667H28.8244Z" fill="white"/>
</g>
</svg>
';

    //les icones
    $icon1 = '<svg style="margin:10px;" width="43" height="38" viewBox="0 0 43 38" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M32.4963 32.3825C32.4953 32.3825 32.4946 32.3832 32.4946 32.3841C32.4946 33.3935 32.4929 34.3619 32.4946 35.3286C32.4963 36.0869 32.2766 36.7597 31.7158 37.296C31.1783 37.8075 30.526 38 29.7838 38C24.9082 37.9918 20.0342 37.9967 15.1585 37.9967C14.5079 37.9967 13.8572 38.0033 13.2066 37.9967C11.6257 37.977 10.5924 36.9554 10.5724 35.3862C10.5625 34.5558 10.5682 33.7242 10.5701 32.8617C10.5707 32.5988 10.3577 32.3857 10.0948 32.3857C7.76848 32.3857 5.44213 32.3857 3.11413 32.3857C1.26371 32.3857 0.255291 31.3955 0.255291 29.5778C0.255291 26.3603 0.308541 23.1427 0.241979 19.9285C0.163769 16.1829 2.72474 13.2795 5.97463 12.6216C6.0721 12.6005 6.10922 12.4813 6.04551 12.4046C5.85664 12.1771 5.67311 11.9609 5.5037 11.7349C4.65171 10.595 4.16914 9.31847 4.07595 7.90216C4.03934 7.35768 4.25067 7.05172 4.6567 7.03033C5.03777 7.00895 5.28571 7.2853 5.33231 7.80182C5.51535 9.82677 6.43557 11.4306 8.18449 12.5097C10.0249 13.6447 11.9719 13.7763 13.982 12.9473C14.2849 12.8222 14.3714 12.5985 14.403 12.3057C14.5961 10.5111 15.3432 8.97797 16.6495 7.71464C16.7876 7.5814 16.9257 7.43828 17.0888 7.33959C17.5747 7.04349 17.6546 6.6487 17.5414 6.1108C16.9108 3.12026 14.0053 0.994976 10.9318 1.32397C8.50898 1.58387 6.80665 2.8324 5.80989 5.02677C5.74832 5.1633 5.68841 5.30313 5.61852 5.43637C5.4438 5.76536 5.1659 5.87228 4.81812 5.75549C4.49529 5.64857 4.34885 5.30477 4.47366 4.94124C4.84807 3.84569 5.45545 2.88339 6.31077 2.09874C8.37752 0.19717 10.8187 -0.454234 13.5278 0.317252C16.2851 1.10354 18.0124 2.96564 18.7279 5.72259C18.8594 6.22924 18.8494 6.22924 19.3586 6.05652C20.986 5.50546 22.6168 5.50546 24.2459 6.05158C24.7601 6.2243 24.7584 6.22759 24.8932 5.69134C25.8933 1.69244 29.7755 -0.682884 33.8525 0.21691C34.1753 0.287643 34.4914 0.39621 34.7993 0.516292C35.1504 0.652824 35.3018 0.962077 35.207 1.26804C35.1022 1.60032 34.801 1.76975 34.4199 1.68257C34.0571 1.60032 33.7043 1.47366 33.3399 1.39799C29.9453 0.689013 26.5356 3.0446 26.0314 6.43815C25.9865 6.74082 26.0697 6.96289 26.316 7.15371C28.005 8.45652 28.9735 10.1656 29.2114 12.2679C29.2547 12.6577 29.4311 12.8683 29.8005 13.0131C33.503 14.4656 37.55 12.2481 38.2139 8.34795C38.5467 6.39703 37.976 4.66653 36.6248 3.19758C36.5316 3.09559 36.4267 3.00512 36.3369 2.90148C36.0606 2.58401 36.049 2.23363 36.3036 1.9836C36.5515 1.7385 36.9659 1.72534 37.2271 2.0313C37.7313 2.62513 38.2871 3.2058 38.6516 3.88188C40.2191 6.78359 39.7648 10.0077 37.5184 12.4291C37.4285 12.526 37.4699 12.6836 37.5948 12.7267C37.9198 12.8387 38.236 12.9403 38.5334 13.0821C41.2824 14.3932 42.7501 16.5645 42.7984 19.5814C42.8516 22.9618 42.8134 26.3438 42.81 29.7242C42.8084 31.3297 41.7617 32.3726 40.1326 32.3792C37.7646 32.389 35.3967 32.3825 33.0271 32.3825C32.8657 32.3825 32.7026 32.3825 32.4963 32.3825ZM10.5707 31.0994C10.5707 30.93 10.5707 30.7836 10.5707 30.6372C10.5707 28.8853 10.5508 27.1334 10.5807 25.3815C10.5924 24.7301 10.6772 24.0721 10.8087 23.4322C11.0034 22.4963 11.4494 21.659 12.0351 20.9006C12.2864 20.5749 12.6541 20.5124 12.9453 20.7197C13.2282 20.922 13.2798 21.2428 13.0818 21.5932C13.0352 21.6771 12.9836 21.7577 12.927 21.835C12.1532 22.896 11.8437 24.0968 11.8437 25.3864C11.8404 28.6994 11.8387 32.0107 11.8437 35.3237C11.8437 36.2925 12.3213 36.7465 13.3081 36.7465C18.7928 36.7465 24.2758 36.7465 29.7606 36.7465C30.739 36.7465 31.2183 36.2843 31.2199 35.3154C31.2249 31.9893 31.2349 28.6632 31.2183 25.3371C31.2033 22.5045 29.4993 20.2657 26.8352 19.5551C26.2827 19.4087 25.7319 19.2606 25.1428 19.5468C22.928 20.6259 20.7015 20.5996 18.465 19.5649C18.1338 19.412 17.7212 19.3675 17.3501 19.3774C16.4814 19.4004 15.6677 19.6636 14.8989 20.0699C14.4746 20.2936 14.0935 20.1982 13.9255 19.8676C13.7474 19.5189 13.9022 19.1586 14.3548 18.9464C14.7542 18.7589 15.1635 18.5928 15.5745 18.4365C16.1902 18.2029 16.2768 17.9611 15.8807 17.4298C15.3332 16.6961 14.9189 15.9 14.6626 15.0232C14.4363 14.2534 14.3315 14.1974 13.5411 14.4244C11.8105 14.9229 10.0965 14.839 8.48235 14.0675C7.66198 13.676 6.88986 13.6891 6.07614 13.8849C3.31215 14.5511 1.52829 16.8261 1.51997 19.6982C1.50998 22.9963 1.51664 26.2961 1.5183 29.5943C1.5183 30.675 1.96094 31.1142 3.05422 31.1158C5.40885 31.1175 7.76348 31.1158 10.1164 31.1158C10.2504 31.1158 10.386 31.1045 10.5674 31.0963C10.5692 31.0962 10.5707 31.0976 10.5707 31.0994ZM32.4946 30.8718C32.4946 30.993 32.5797 31.1158 32.7009 31.1158C35.2203 31.1158 37.7397 31.1274 40.259 31.1126C41.0744 31.1076 41.542 30.5977 41.5437 29.7637C41.5487 26.3701 41.5686 22.9766 41.5387 19.5847C41.5137 16.7899 39.68 14.5264 36.9942 13.8865C36.4118 13.7484 35.8676 13.7006 35.2769 14.0017C34.0605 14.6235 32.7375 14.8554 31.373 14.663C31.271 14.6485 31.1693 14.6324 31.0677 14.6149C30.0141 14.4335 29.0241 15.1 28.5686 16.0672C28.2184 16.8107 27.7396 17.4997 27.1375 18.1297C27.0747 18.1954 27.0892 18.3165 27.1763 18.3427C30.546 19.3675 32.4929 21.9732 32.4946 25.467C32.4946 27.191 32.4946 28.9149 32.4946 30.6388C32.4946 30.7163 32.4946 30.7938 32.4946 30.8718ZM21.8314 6.90614C21.8314 6.9066 21.831 6.90697 21.8305 6.90697C18.3697 6.93536 15.6128 9.64609 15.6411 12.9917C15.6694 16.4494 18.4367 19.1257 21.9462 19.0895C25.216 19.0566 28.005 16.2619 27.9784 13.0427C27.9501 9.62307 25.2015 6.8778 21.8322 6.90531C21.8317 6.90531 21.8314 6.90568 21.8314 6.90614Z" fill="white" fill-opacity="0.2"/>
</svg>
';
    $icon2 = '<svg style="margin:10px;" width="48" height="35" viewBox="0 0 48 35" fill="none" xmlns="http://www.w3.org/2000/svg">
<path opacity="0.2" d="M19.4167 20.3167H3.83333C3.3471 20.3167 2.88079 20.1238 2.53697 19.7803C2.19315 19.4368 2 18.9709 2 18.4851V3.83167C2 3.34588 2.19315 2.87999 2.53697 2.53649C2.88079 2.19298 3.3471 2 3.83333 2H17.5833C18.0696 2 18.5359 2.19298 18.8797 2.53649C19.2235 2.87999 19.4167 3.34588 19.4167 3.83167V23.9801C19.4167 26.409 18.4509 28.7385 16.7318 30.456C15.0127 32.1736 12.6812 33.1385 10.25 33.1385M46 20.3167H30.4167C29.9304 20.3167 29.4641 20.1238 29.1203 19.7803C28.7765 19.4368 28.5833 18.9709 28.5833 18.4851V3.83167C28.5833 3.34588 28.7765 2.87999 29.1203 2.53649C29.4641 2.19298 29.9304 2 30.4167 2H44.1667C44.6529 2 45.1192 2.19298 45.463 2.53649C45.8068 2.87999 46 3.34588 46 3.83167V23.9801C46 26.409 45.0342 28.7385 43.3151 30.456C41.5961 32.1736 39.2645 33.1385 36.8333 33.1385" stroke="white" stroke-width="2.70769" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';

    $icon3 = '<svg style="margin:10px;" width="35" height="51" viewBox="0 0 35 51" fill="none" xmlns="http://www.w3.org/2000/svg">
<g opacity="0.2">
<path d="M15.7402 20.5193C15.3649 19.7166 14.7691 19.0375 14.0224 18.5611C13.2756 18.0847 12.4087 17.8309 11.5231 17.8292C9.72793 17.8292 8.16726 18.8567 7.39501 20.3534C6.6268 19.7871 5.67665 19.4473 4.64968 19.4473C2.08629 19.4473 0 21.5346 0 24.0993V31.7852C0 34.3499 2.08629 36.4372 4.64968 36.4372C6.21844 36.4372 7.60525 35.6524 8.45028 34.4591C8.96377 34.9162 9.57833 35.2641 10.2697 35.4583C10.5796 35.5442 10.9109 35.5039 11.1912 35.3463C11.4715 35.1886 11.678 34.9264 11.7657 34.6169C11.9476 33.9696 11.5676 33.3022 10.9247 33.1201C10.4574 32.9885 10.0459 32.7079 9.7525 32.321C9.45914 31.934 9.30003 31.4618 9.29935 30.9762V22.4812C9.29935 21.2555 10.298 20.2563 11.5231 20.2563C12.3843 20.2563 13.1768 20.762 13.5407 21.5427C13.8237 22.1495 14.5434 22.4124 15.1539 22.1293C15.7604 21.8501 16.0232 21.1301 15.7402 20.5193ZM6.87344 31.7852C6.87344 33.0109 5.87476 34.0101 4.64968 34.0101C3.42459 34.0101 2.42592 33.0109 2.42592 31.7852V24.0993C2.42592 22.8736 3.42459 21.8744 4.64968 21.8744C5.87476 21.8744 6.87344 22.8736 6.87344 24.0993V31.7852Z" fill="white"/>
<path d="M15.3642 51C8.55546 51 2.48662 46.4329 0.602491 39.8999C0.513488 39.5909 0.550813 39.2592 0.706253 38.9778C0.861694 38.6963 1.12252 38.4882 1.43135 38.3991C1.74018 38.3101 2.07171 38.3474 2.35302 38.5029C2.63433 38.6585 2.84237 38.9194 2.93137 39.2284C4.52035 44.7299 9.63095 48.5729 15.3642 48.5729C22.5004 48.5729 28.3024 42.768 28.3024 35.6282V34.8555C28.3071 34.4593 28.2067 34.069 28.0113 33.7243C27.816 33.3796 27.5328 33.093 27.1906 32.8936L17.3858 27.2303C17.1333 27.0839 16.8543 26.9889 16.565 26.9507C16.2756 26.9126 15.9816 26.9319 15.6998 27.0078C15.4174 27.0823 15.1525 27.2122 14.9207 27.39C14.6889 27.5678 14.4947 27.7899 14.3494 28.0434C13.7348 29.1073 14.1027 30.4705 15.162 31.0813L21.7808 34.9041C22.363 35.2398 22.5611 35.9801 22.2255 36.5626C22.0644 36.8413 21.7994 37.0446 21.4886 37.128C21.1778 37.2115 20.8467 37.1681 20.5678 37.0076L13.9491 33.1889C11.7294 31.9065 10.9652 29.0547 12.2469 26.8338C12.8696 25.7578 13.8723 24.9892 15.0691 24.6656C16.2699 24.346 17.5233 24.5078 18.5988 25.1308L28.4035 30.7941C29.1161 31.2071 29.7067 31.8017 30.115 32.5172C30.5234 33.2327 30.735 34.0437 30.7284 34.8676V35.6282C30.7284 44.1029 23.8347 51 15.3642 51Z" fill="white"/>
<path d="M4.64968 36.4372C2.08629 36.4372 0 34.3499 0 31.7852C0 31.1137 0.541788 30.5716 1.21296 30.5716C1.88413 30.5716 2.42592 31.1137 2.42592 31.7852C2.42592 33.0109 3.42459 34.0101 4.64968 34.0101C5.32085 34.0101 5.86264 34.5521 5.86264 35.2237C5.86264 35.8952 5.32085 36.4372 4.64968 36.4372ZM34.068 2.64747C33.7376 2.13332 33.3089 1.68958 32.8066 1.34177C32.3042 0.993966 31.7381 0.748949 31.1407 0.620814C29.9277 0.357875 28.6824 0.584407 27.6393 1.25996C27.1254 1.59049 26.6819 2.01939 26.3343 2.52201C25.9866 3.02464 25.7417 3.59106 25.6137 4.1887L23.0664 16.0088L21.4451 4.02689C21.1014 1.48649 18.7523 -0.301497 16.2132 0.0423474C13.6741 0.386191 11.887 2.73647 12.2307 5.27687L13.6013 15.4021C13.6228 15.5601 13.6752 15.7124 13.7555 15.8501C13.8359 15.9879 13.9425 16.1085 14.0694 16.205C14.1963 16.3016 14.341 16.3722 14.4951 16.4128C14.6493 16.4534 14.81 16.4632 14.9679 16.4417C15.1259 16.4202 15.2781 16.3677 15.4158 16.2874C15.5535 16.207 15.674 16.1003 15.7705 15.9734C15.867 15.8464 15.9375 15.7017 15.9781 15.5474C16.0187 15.3932 16.0285 15.2324 16.007 15.0744L14.6364 4.9492C14.4706 3.73159 15.3278 2.61106 16.5407 2.44521C17.7537 2.27936 18.8777 3.13694 19.0435 4.35051L21.5947 23.1891C21.6158 23.3473 21.668 23.4998 21.7482 23.6378C21.8284 23.7757 21.9351 23.8965 22.0621 23.9931C22.189 24.0897 22.3339 24.1602 22.4882 24.2007C22.6425 24.2411 22.8033 24.2507 22.9613 24.2287C23.2081 24.1956 23.4386 24.0868 23.621 23.9173C23.8035 23.7478 23.9289 23.5259 23.9802 23.2822H23.9842L27.9911 4.6984C28.0513 4.41265 28.1678 4.14175 28.3337 3.90146C28.4996 3.66117 28.7116 3.45629 28.9574 3.29875C29.4547 2.97513 30.049 2.86591 30.6313 2.99536C31.8281 3.25425 32.5963 4.43951 32.3375 5.64094L28.3347 24.2247C28.3105 24.3096 28.3024 24.3946 28.3024 24.4795V27.6025C28.3024 28.274 28.8442 28.816 29.5153 28.816C30.1865 28.816 30.7283 28.274 30.7283 27.6025V24.609L34.7028 6.15063C34.9656 4.93707 34.7392 3.69114 34.068 2.64747Z" fill="white"/>
</g>
</svg>
';
    
$icon4 = '<svg style="margin:10px;" width="41" height="43" viewBox="0 0 41 43" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M6.54815 15.268C6.54815 15.268 13.0941 9.2 22.9131 16.908C33.2434 24.985 39.278 21.828 39.278 21.828M22.729 16.744C21.8289 20.926 15.8762 36.875 1.63867 35.44M17.7172 27.486C21.3789 28.265 31.0955 31.34 31.0955 42M31.0955 5.92C31.0955 8.63724 28.8975 10.84 26.1861 10.84C23.4746 10.84 21.2766 8.63724 21.2766 5.92C21.2766 3.20276 23.4746 1 26.1861 1C28.8975 1 31.0955 3.20276 31.0955 5.92Z" stroke="white" stroke-opacity="0.2" stroke-width="2.01639" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';


    $countsvg = 0;
    $count = 1;
    foreach ($sections as $key => $section) {

        var_dump($section->sequence);

        $hiddensection = false;
        //si la section est caché pour les étudiants
        if($section->visible == 0){
            //on ne la montre que pour les smalleditingteacher
            if($rolename == "smalleditingteacher" || $rolename == "super-admin" || $rolename == "manager" || $rolename == "editingteacher"){
                $hiddensection = true;
                // var_dump($rolename);
                // die();
            } else {
                continue;
            }
        }

        //on affiche uniquement la section si elle contient des activités
        // $nbactivities = countSectionActivities($section->id, $course->id);
        // $nbactivities = $section->sequence;
        $tableact = explode(',', $section->sequence);
        $tableact = array_map('intval', $tableact);

        $nbactivities = reset($tableact);

        // var_dump($tableact);

        //on regarde si la section à un planning sur la session
        if ($session) {
            $nbplannings = countSectionPlannings($section->id, $session->id);
        } else {
            $nbplannings = 0;
        }

        $tot = $nbactivities + $nbplannings;

        if ($tot == 0) {
            continue;
        }

        $countsvg++;
        if ($section->name) {
            $sectionname = longTitlesModules($section->name);
        } else {
            if($key == 1){
                $sectionname = "Généralités ";
            } else {
                $sectionname = "Section " . intval($key - 1);
            }
        }

        //on va chercher les activités de la section

        $tableau = explode(',', $section->sequence);
        $tableau = array_map('intval', $tableau);

        if($hiddensection){
            $svgicon = $icon0;
        } else {
            if ($countsvg == 1) {
                $svgicon = $icon1;
            } else if ($countsvg == 2) {
                $svgicon = $icon2;
            } else if ($countsvg == 3) {
                $svgicon = $icon3;
            } else if ($countsvg == 4) {
                $svgicon = $icon4;
                $countsvg = 0;
            }
        }

        $content .= '<div id="module-block-' . $section->id . '" onclick="changeModuleInfo(' . $section->id . ', false)" class="fff-module-thumbnail-box">
        <div style="margin:0 10px;display: flex; justify-content: space-between;width: calc(100% - 40px);">
            <h1 class="fff-my-courses-caroussel-item-title">' . $count . '</h1>';
        $content .= $svgicon;
        $content .= '</div>
        <div>
            <h5 style="margin-bottom:0;">' . $sectionname . '</h5>
        </div>
    </div>';
        $count++;
    }



$content .= '</div>'; //col-4



$content .= '<div class="col-xs-12 col-md-12 col-lg-8">

    <div style="background: transparent;border-radius: 20px;">
        <div id="activitiesall" style="padding:0 20px;">
            <div style="justify-content: center;display: flex;margin-bottom:20px;" id="course_module_info_titles">
                <h2 class="course_module_info_title_highlight" >' . $titlecontenu . '</h2>';
    $content .= '</div>

            <div class="sub-box-module" id="presentiel">';


    //les activités
    require_once('./details_activities.php');

    $content .= '
            </div>


            <div class="sub-box-module" id="deliver" style="display:none;">
                <div>';

    $content .= '</div>
            </div>

            <div class="sub-box-module" id="result" style="display:none;">
                <div>
                    
                </div>
            </div>


        </div>
    </div>

</div>

';


}

$content .= '</div>'; //row




$content .= '<script>

function fading(element){
    var increment = 0.045;
    var opacity = 0;
    var instance = window.setInterval(function() {
        element.style.opacity = opacity
        opacity = opacity + increment;
        if(opacity > 1){
            window.clearInterval(instance);
        }
    },30)
}

//change le module
function changeModuleInfo(moduleid, first){
    // alert(moduleid);
    //on change la couleur des blocks 
    let oldblock = document.getElementsByClassName(\'fff-module-thumbnail-box-selected\')[0]
    if(oldblock) {
        oldblock.classList.remove(\'fff-module-thumbnail-box-selected\')
    }
    document.getElementById(\'module-block-\'+moduleid).classList.add("fff-module-thumbnail-box-selected")

    //on affiche les activités du module sélectionné
    let allactivities = document.getElementsByClassName(\'activity-element\')
    allactivities.forEach(el=>el.style.display = \'none\')
    let activities = document.getElementsByClassName(\'module-\'+moduleid)
    activities.forEach(el=>el.style.display = \'block\')
    

    // if(!first){
    //     //on se deplace sur la page sur la section modules du cours
    //     //const element_to_scroll_to = document.getElementById(\'modulesformation\');
    //     //element_to_scroll_to.scrollIntoView({ behavior: "smooth" });
    // }
    
}

//met a jour les onglets presentiel/a delivrer/resultats
function changeinfomodule(me, boxid) {
    let old = document.getElementsByClassName("course_module_info_title_highlight")[0];
    old.classList.remove("course_module_info_title_highlight");
    old.classList.add("course_module_info_title");
    me.classList.add("course_module_info_title_highlight");
    let boxes = document.getElementsByClassName("sub-box-module");
    boxes.forEach(box=>{
        box.style.display = "none";
    })
    document.getElementById(boxid).style.display = "block";
}

// const firstel = document.getElementById(\'fff-my-courses\').childNodes[0]
//     firstel.click();

</script>';


//on check si il y a l'id de la section 

$sectionid = optional_param('sectionid', null, PARAM_INT);
if ($sectionid) {
    echo '<script>

    document.addEventListener("DOMContentLoaded", () => {
        changeModuleInfo(' . $sectionid . ', false)
    });

    
 </script>';
} else {
    //on clique sur la première section
    echo '<script>
    document.addEventListener("DOMContentLoaded", () => {
        const firstel = document.getElementById(\'fff-my-courses\').childNodes[0]
        firstel.click();
    });
    
 </script>';
}

// <div class="fff-course-box-info-details">
//     <svg class="mr-2" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
//         <circle cx="16" cy="15.9995" r="15.6667" fill="#E2E8F0" stroke="white" stroke-width="0.666667"/>
//         <path d="M18.6663 12.6662C18.6663 14.1389 17.4724 15.3328 15.9997 15.3328C14.5269 15.3328 13.333 14.1389 13.333 12.6662C13.333 11.1934 14.5269 9.99951 15.9997 9.99951C17.4724 9.99951 18.6663 11.1934 18.6663 12.6662Z" stroke="#00315a" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
//         <path d="M15.9997 17.3328C13.4223 17.3328 11.333 19.4222 11.333 21.9995H20.6663C20.6663 19.4222 18.577 17.3328 15.9997 17.3328Z" stroke="#00315a" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
//     </svg>

//     <span>Josselin CHUBILLEAU, Erick SENECHAL</span>
// </div>

echo '<script>

var positionNextIcon = 0;
var numbersections = ' . count($sections) . ';



function moveIconCourse(move){

    //on regarde la largeur de l\'ecran
    let largeur = window.innerWidth;
    let largeurInterval = Math.floor((largeur - 180)/240);
    let rest = numbersections-largeurInterval;
    let nbencarts = largeurInterval - numbersections;
    //on calcule le nombre delement max
    maxelements = nbencarts;
    rest--
    
    if(maxelements < 0){
        //console.log(positionNextIcon +" ok "+rest);

        if(move == "next" && positionNextIcon < rest){
            //alert("next")
            positionNextIcon++;
            document.getElementById(\'fff-my-courses\').scrollBy({top: 0, left: 30, behavior: \'smooth\'});
        } else if(move == "prev" && positionNextIcon > 0){
            //alert("prev")
            document.getElementById(\'fff-my-courses\').scrollBy({top: 0, left: -30, behavior: \'smooth\'});
            positionNextIcon--;
        }
        
        //si on est à la position 0
        if(positionNextIcon == 0){
            document.getElementById(\'leftcourseicon\').style.opacity=0.3; 
        } else {
            document.getElementById(\'leftcourseicon\').style.opacity=1; 
        }

        //si on est à la position max
        if(positionNextIcon == rest){
            document.getElementById(\'rightcourseicon\').style.opacity=0.3; 
        } else {
            document.getElementById(\'rightcourseicon\').style.opacity=1; 
        }
    }
    else {
        document.getElementById(\'rightcourseicon\').style.opacity=0.3; 
    }
}

</script>';

echo '<script>
var firstDigit = "";
window.onload = function(){


    //on regarde la largeur de l\'ecran
    let largeur = window.innerWidth;
    let largeurInterval = Math.floor((largeur - 180)/220);
    let rest = numbersections-largeurInterval;
    //on désactive la fleche de droite
    //console.log(rest)
    if(rest == 0){
        document.getElementById(\'rightcourseicon\').style.opacity=0.3; 
    }
';

if ($group) {

    //on affiche seulement le dossier de la région
    echo '
    
    
    var inputString = "' . $group->name . '";

    //console.log("' . $group->name . '");

    // Utiliser une expression régulière pour extraire le premier nombre
    var match2 = inputString.match(/\d+/);

    if (match2) {
        firstDigit = match2[0];
        console.log("Premier chiffre :", firstDigit);
    } else {
        console.log("Aucun chiffre trouvé.");
    }

    // Sélectionnez tous les éléments avec la classe "fff-name-activity"
    //var elements = document.querySelectorAll(".fff-name-activity");

    // Parcourir les éléments sélectionnés
    //elements.forEach(function(element) {
        //if (element.textContent.includes(firstDigit)) {
            //console.log("Élément contenant le texte :" + element);
            //element.parentNode.parentNode.parentNode.parentNode.style.display = "block";
        //}
    //});
    ';
}

echo '
    



    

    //On cache tout les dossiers
    var elements = document.querySelectorAll(".fff-name-activity");
    // Expression régulière pour repérer du texte entre crochets
    var regex = /\[([^\]]+)\]/g;
    
    // Parcourir tous les éléments
    elements.forEach(function(element) {
        
        // Vérifier si l\'élément contient du texte
        if (element.textContent) {
            //console.log(element.textContent)
            var matches = element.textContent.match(regex);
            if (matches) {
                // Si des correspondances sont trouvées, on check 
                matches.forEach(function(match) {

                    //dossier de ligue new
                    element.parentNode.parentNode.parentNode.parentNode.remove();

                    if(firstDigit){
                        if (!element.textContent.includes(firstDigit)){
                            //on supprime
                            element.parentNode.parentNode.parentNode.parentNode.remove();
                            //console.log("Élément supprimé:", element.parentNode.parentNode.parentNode.parentNode);
                            // console.log("Texte entre crochets :", match);
                        }
                    }  else {
                        //on supprime pas
                        //element.parentNode.parentNode.parentNode.parentNode.remove();
                    }
                    
                });
            }
        }
    });

    ';


echo '

  };
    
    </script>';

if ($group) {
    //on affiche seulement le dossier de la région
    echo '<script>
    
// window.onload = function(){
    

// };
</script>';
}
