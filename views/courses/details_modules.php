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


$content .= '<div class="row">';

// var_dump(countCourseActivities($courseid));
if (countCourseActivities($courseid) == 0) {
    $content .= '<div class="col-12">';
    $content .= nothingtodisplay("Le contenu de formation n'est pas encore disponible");
    $content .= '</div>';
} else {
    $content .= '<div class="col-4"  id="fff-my-courses">';
    // $content .= '<div class="fff-my-courses-caroussel" >';
    // $content .= '<div class="fff-my-courses-caroussel-items"  id="fff-my-courses">';


    //les icones
    $icon1 = '<svg width="60" height="56" viewBox="0 0 79 71" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M59.8338 60.5041C59.8338 62.3912 59.8307 64.2015 59.8338 66.0087C59.8369 67.4255 59.4294 68.6826 58.3891 69.6845C57.392 70.6404 56.182 71 54.8052 71C45.7605 70.9846 36.719 70.9939 27.6743 70.9939C26.4673 70.9939 25.2604 71.0061 24.0534 70.9939C21.1208 70.957 19.2038 69.0483 19.1668 66.1162C19.1452 64.2814 19.1637 62.4434 19.1637 60.5102H18.2808C13.9653 60.5102 9.64984 60.5102 5.33125 60.5102C1.8986 60.5102 0.0279318 58.66 0.0279318 55.2638C0.0279318 49.2521 0.126713 43.2403 0.00323648 37.2348C-0.141848 30.2365 4.60891 24.8118 10.6377 23.5824C10.7364 23.5609 10.8352 23.5332 11.0204 23.484C10.5821 22.9431 10.153 22.4483 9.76405 21.9258C8.18355 19.7959 7.28835 17.4108 7.11548 14.7646C7.04757 13.7472 7.43961 13.1756 8.19282 13.1356C8.89972 13.0957 9.35967 13.612 9.4461 14.5771C9.78566 18.3605 11.4927 21.3572 14.7371 23.3734C18.1512 25.4941 21.7629 25.74 25.4919 24.1909C26.0537 23.9573 26.2142 23.5394 26.2729 22.9923C26.6309 19.6391 28.017 16.7746 30.4402 14.4142C30.6964 14.1652 30.9526 13.8978 31.2551 13.7134C32.1565 13.1602 32.3047 12.4226 32.0948 11.4176C30.9248 5.82997 25.5351 1.85903 19.8336 2.47373C15.339 2.95934 12.1811 5.29211 10.332 9.39213C10.2178 9.64723 10.1067 9.90847 9.97705 10.1574C9.65293 10.7721 9.13741 10.9719 8.49225 10.7537C7.89339 10.5539 7.62174 9.91155 7.85326 9.23231C8.54781 7.18537 9.67453 5.38739 11.2612 3.92134C15.0952 0.368396 19.6237 -0.848701 24.6491 0.59276C29.7642 2.06188 32.9684 5.54106 34.2957 10.6922C34.5396 11.6388 34.5211 11.6388 35.4657 11.3161C38.4847 10.2865 41.5099 10.2865 44.532 11.3069C45.4858 11.6296 45.4827 11.6358 45.7328 10.6338C47.588 3.16219 54.7898 -1.27591 62.3527 0.405278C62.9516 0.537438 63.5381 0.740287 64.1092 0.964651C64.7605 1.21975 65.0414 1.79756 64.8654 2.36923C64.671 2.99007 64.1122 3.30664 63.4053 3.14375C62.7324 2.99007 62.078 2.75342 61.4019 2.61204C55.1046 1.28737 48.7795 5.68859 47.8442 12.0292C47.7609 12.5947 47.9152 13.0096 48.3721 13.3661C51.5053 15.8003 53.3019 18.9937 53.7433 22.9216C53.8236 23.65 54.1508 24.0434 54.8361 24.3139C61.7044 27.0277 69.2118 22.8847 70.4435 15.5975C71.0609 11.9523 70.0021 8.71904 67.4955 5.97442C67.3226 5.78387 67.1282 5.61482 66.9615 5.42119C66.449 4.82801 66.4274 4.17336 66.8997 3.70619C67.3597 3.24825 68.1283 3.22366 68.613 3.79533C69.5483 4.90485 70.5793 5.98979 71.2554 7.25299C74.1632 12.6746 73.3205 18.6986 69.1532 23.2228C69.0513 23.3334 68.9525 23.4502 68.8136 23.607C69.5853 23.8928 70.3385 24.1079 71.0362 24.443C76.1358 26.8925 78.8584 30.9495 78.9479 36.5863C79.0467 42.9023 78.9757 49.2213 78.9696 55.5373C78.9665 58.537 77.0248 60.4856 74.0027 60.4979C69.61 60.5164 65.2174 60.5041 60.8216 60.5041C60.5222 60.5041 60.2196 60.5041 59.8369 60.5041H59.8338ZM19.1637 58.1068C19.1637 57.7902 19.1637 57.5167 19.1637 57.2431C19.1637 53.9699 19.1267 50.6966 19.1822 47.4234C19.2038 46.2063 19.3613 44.9769 19.6051 43.7813C19.9663 42.0325 20.7936 40.4681 21.8802 39.0512C22.3463 38.4426 23.0285 38.3259 23.5687 38.7131C24.0935 39.0912 24.1892 39.6905 23.8219 40.3451C23.7354 40.5019 23.6397 40.6525 23.5348 40.7969C22.0994 42.7793 21.5252 45.023 21.5252 47.4326C21.519 53.6226 21.5159 59.8095 21.5252 65.9995C21.5252 67.8097 22.4111 68.658 24.2417 68.658C34.4161 68.658 44.5875 68.658 54.762 68.658C56.5771 68.658 57.4661 67.7944 57.4692 65.9841C57.4785 59.7695 57.497 53.5549 57.4661 47.3404C57.4383 42.0478 54.2773 37.8648 49.3352 36.5371C48.3103 36.2635 47.2886 35.9869 46.1958 36.5217C42.0871 38.5379 37.9568 38.4888 33.808 36.5555C33.1937 36.2697 32.4282 36.1867 31.7398 36.2052C30.1284 36.2482 28.6189 36.7399 27.1928 37.4991C26.4056 37.9171 25.6987 37.7388 25.3869 37.1211C25.0566 36.4695 25.3437 35.7964 26.1833 35.3999C26.9242 35.0495 27.6836 34.7391 28.4461 34.4471C29.5882 34.0107 29.7487 33.5589 29.014 32.5662C27.9984 31.1954 27.2298 29.7078 26.7544 28.0697C26.3346 26.6313 26.1401 26.5268 24.6738 26.9509C21.4635 27.8822 18.2839 27.7254 15.2896 26.284C13.7678 25.5525 12.3355 25.5771 10.826 25.9428C5.69859 27.1876 2.38942 31.4382 2.37399 36.8045C2.35546 42.9668 2.36781 49.1322 2.3709 55.2945C2.3709 57.3138 3.19202 58.1344 5.22012 58.1375C9.5881 58.1406 13.9561 58.1375 18.321 58.1375C18.571 58.1375 18.8241 58.116 19.1637 58.1006V58.1068ZM59.8338 58.0729C60.0221 58.1068 60.1178 58.1375 60.2166 58.1375C64.8901 58.1375 69.5637 58.159 74.2373 58.1313C75.7499 58.1221 76.6173 57.1693 76.6204 55.6111C76.6297 49.2705 76.6667 42.9299 76.6111 36.5924C76.5648 31.3706 73.1631 27.1415 68.1808 25.9459C67.1004 25.6877 66.091 25.5986 64.9951 26.161C62.7386 27.3228 60.2845 27.7562 57.7532 27.3966C56.395 27.2029 55.0614 26.8464 53.6383 26.5452C53.1413 29.4189 51.7924 31.9638 49.6531 34.1244C49.8322 34.2074 49.897 34.2504 49.968 34.2719C56.219 36.1867 59.8307 41.0551 59.8338 47.5832C59.8338 50.8042 59.8338 54.0252 59.8338 57.2462C59.8338 57.5197 59.8338 57.7933 59.8338 58.0729ZM40.0528 12.9051C33.6321 12.9574 28.517 18.0225 28.5695 24.2739C28.622 30.7344 33.7555 35.7349 40.2658 35.6673C46.3316 35.6058 51.5053 30.384 51.4559 24.3692C51.4034 17.9794 46.3038 12.8498 40.0528 12.902V12.9051Z" fill="white" fill-opacity="0.2"/>
</svg>';
    $icon2 = '<svg width="60" height="56" viewBox="0 0 79 71" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M44.1306 51.8573C44.5418 53.5253 44.9204 55.0636 45.2991 56.6019C45.4529 57.2208 45.5742 57.8514 45.7872 58.4497C45.8582 58.6501 46.1629 58.8917 46.37 58.9005C47.4024 58.9477 48.4407 58.9064 49.4731 58.9271C51.1386 58.9595 52.0586 59.9055 52.0675 61.5675C52.0704 62.2542 52.0823 62.9438 52.0645 63.6304C52.032 64.9359 51.1031 65.8937 49.7985 65.988C49.5766 66.0027 49.3548 65.9998 49.1329 65.9998C40.063 65.9998 30.9932 65.9998 21.9203 65.9998C19.752 65.9998 18.9326 65.1805 18.9326 63.0174C18.9326 62.5017 18.9266 61.986 18.9326 61.4703C18.9503 59.9438 19.8673 58.9801 21.4027 58.93C22.4617 58.8946 23.5237 58.9389 24.5827 58.9035C24.7898 58.8976 25.127 58.7178 25.1685 58.5587C25.7512 56.3838 26.2896 54.1972 26.8694 51.9045C26.4849 51.8838 26.1802 51.8573 25.8755 51.8573C18.5539 51.8573 11.2353 51.8573 3.91373 51.8544C1.86369 51.8544 0.446706 50.7964 0.0710133 48.9988C-0.00294198 48.6422 1.62316e-05 48.2679 1.62316e-05 47.8995C1.62316e-05 33.2414 1.62316e-05 18.5832 1.62316e-05 3.92502C1.62316e-05 1.42305 1.42587 0.00261952 3.94627 0.00261952C24.1094 0.00261952 44.2696 0.00261952 64.4328 0.00261952C65.3439 0.00261952 66.258 -0.0032744 67.1691 0.00261952C69.5387 0.0173543 71 1.46431 71 3.81598C71 18.5478 71 33.2797 71 48.0145C71 50.4251 69.5741 51.8514 67.1307 51.8544C59.8357 51.8632 52.5378 51.8573 45.2429 51.8573C44.9234 51.8573 44.6069 51.8573 44.1306 51.8573ZM2.36954 42.3769H68.6275C68.6275 42.0705 68.6275 41.8259 68.6275 41.5813C68.6275 29.0803 68.6275 16.5822 68.6275 4.08121C68.6275 2.65783 68.3317 2.36608 66.8881 2.36608C45.9588 2.36608 25.0294 2.36608 4.10009 2.36608C2.6624 2.36608 2.36658 2.66078 2.36658 4.0871C2.36658 16.5881 2.36658 29.0862 2.36658 41.5872V42.3769H2.36954ZM68.6216 44.8612H2.3725C2.3725 46.0695 2.34883 47.2188 2.38138 48.3681C2.39912 49.0164 2.78369 49.4054 3.44929 49.4673C3.71849 49.4939 3.99064 49.4909 4.25984 49.4909C25.0827 49.4909 45.9055 49.4909 66.7254 49.4909C66.9975 49.4909 67.2697 49.4909 67.5389 49.4703C68.2075 49.4113 68.5891 49.0164 68.6098 48.374C68.6482 47.2247 68.6216 46.0724 68.6216 44.8612ZM43.3703 58.8829C43.3526 58.7031 43.3585 58.5557 43.326 58.4202C42.8231 56.3986 42.2965 54.3829 41.8202 52.3553C41.7019 51.8455 41.4002 51.8544 41.0126 51.8544C37.3415 51.8603 33.6704 51.8691 29.9992 51.8455C29.4845 51.8426 29.2685 51.9929 29.1502 52.4939C28.7035 54.4035 28.2095 56.3013 27.7391 58.2051C27.6889 58.4084 27.6681 58.6235 27.6238 58.8829H43.3703ZM49.6713 61.3436H21.3494V63.5833H49.6743V61.3436H49.6713Z" fill="white" fill-opacity="0.2"/>
</svg>';
    $icon3 = '<svg width="60" height="56" viewBox="0 0 79 71" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M2.33001e-05 35.4777C0.0222437 15.8861 15.9154 0 35.4999 0C55.0789 0 71.0665 16.0166 70.9998 35.5638C70.9331 55.1805 55.0205 71.0443 35.4582 70.9999C15.8515 70.9555 -0.0221971 55.0527 2.33001e-05 35.4777ZM36.6915 16.7361C36.6915 18.6528 36.7026 20.5694 36.6831 22.4889C36.6776 22.9222 36.8081 23.2083 37.1525 23.4833C40.4801 26.125 43.7965 28.7833 47.1073 31.4444C47.4239 31.6972 47.6878 31.7777 48.1017 31.6388C52.0541 30.3027 56.0149 28.9861 59.9757 27.6805C60.3645 27.5527 60.5673 27.3694 60.6645 26.9472C61.3895 23.7777 62.1533 20.6166 62.8755 17.4472C62.931 17.2 62.8393 16.8528 62.6894 16.6389C59.298 11.7833 54.9289 8.07499 49.5682 5.55277C49.3016 5.42777 48.8905 5.41666 48.6127 5.52499C44.813 7.01666 41.0272 8.54166 37.2303 10.0417C36.8248 10.2028 36.672 10.4111 36.6776 10.85C36.6998 12.8139 36.6887 14.7778 36.6887 16.7389L36.6915 16.7361ZM34.3111 16.7194C34.3111 14.8 34.2917 12.8833 34.325 10.9639C34.3333 10.4444 34.175 10.1944 33.6834 10.0028C30.1587 8.63054 26.6284 7.26666 23.137 5.80833C22.1871 5.4111 21.4566 5.48055 20.565 5.93888C15.5738 8.50832 11.4824 12.0889 8.27713 16.6833C8.14103 16.8778 8.07159 17.2055 8.12436 17.4361C8.85208 20.6083 9.61313 23.7694 10.3381 26.9416C10.4353 27.3666 10.638 27.55 11.0269 27.6777C14.9905 28.9833 18.954 30.3 22.9093 31.6388C23.3259 31.7805 23.587 31.6916 23.9009 31.4388C27.2145 28.775 30.5309 26.1166 33.8612 23.4722C34.2111 23.1944 34.325 22.9027 34.3194 22.4722C34.3 20.5555 34.3111 18.6361 34.3111 16.7166V16.7194ZM19.4484 55.4916C19.5484 55.411 19.7484 55.3055 19.8706 55.1444C21.8038 52.5833 23.7342 50.0194 25.6424 47.4388C25.7896 47.2388 25.8618 46.8833 25.8035 46.6388C24.8202 42.6083 23.8147 38.5805 22.7871 34.5611C22.7232 34.3083 22.4593 33.9972 22.2204 33.9138C18.2485 32.5666 14.2655 31.2444 10.277 29.9444C10.0853 29.8833 9.72979 29.9805 9.59924 30.1333C7.31887 32.8333 5.05794 35.5472 2.81091 38.2749C2.66092 38.4583 2.57204 38.7722 2.59703 39.0111C3.11088 44.0277 4.73297 48.6666 7.36609 52.9638C7.69106 53.4916 8.06326 53.7222 8.65765 53.8083C10.6214 54.086 12.5796 54.4194 14.5377 54.7277C16.132 54.9777 17.7291 55.2277 19.4484 55.4944V55.4916ZM51.5903 55.4805C51.7875 55.4583 51.8569 55.4555 51.9236 55.4444C55.5038 54.8777 59.0841 54.3194 62.6588 53.7221C62.9643 53.6721 63.3199 53.4083 63.4893 53.1416C66.2335 48.8249 67.8834 44.1166 68.4083 39.0249C68.4361 38.7694 68.325 38.4333 68.1611 38.2333C65.9419 35.5416 63.6976 32.8694 61.4756 30.1805C61.2339 29.8888 61.0395 29.8083 60.6617 29.9361C56.7065 31.2694 52.7402 32.575 48.7877 33.9138C48.56 33.9916 48.2961 34.2638 48.2378 34.4944C47.1962 38.5611 46.1824 42.6361 45.1769 46.711C45.1297 46.8999 45.1713 47.1805 45.2852 47.3333C47.2517 49.9583 49.2349 52.5722 51.2208 55.1805C51.332 55.3277 51.5153 55.4194 51.593 55.4805H51.5903ZM35.5027 48.5249C33.1223 48.5249 30.7447 48.511 28.3644 48.5416C28.0755 48.5444 27.6894 48.7138 27.5172 48.9388C25.6229 51.4138 23.7648 53.9166 21.8844 56.4027C21.6399 56.7249 21.6066 56.9527 21.8371 57.3138C24.0147 60.6888 26.1646 64.0805 28.3449 67.4527C28.506 67.7027 28.8449 67.9277 29.1393 67.986C33.7556 68.9277 38.3608 68.8221 42.9437 67.7582C43.2104 67.6971 43.4993 67.436 43.6409 67.1888C45.5213 63.9332 47.3712 60.6583 49.2516 57.3999C49.471 57.0221 49.4654 56.7833 49.196 56.4333C47.3101 53.9805 45.4408 51.5138 43.5909 49.0333C43.3132 48.661 43.0354 48.5083 42.5688 48.5138C40.2134 48.5388 37.8553 48.5249 35.4999 48.5249H35.5027ZM35.5277 25.2083C35.4138 25.2805 35.3333 25.3194 35.2638 25.375C31.9613 28.0111 28.6671 30.6583 25.3535 33.2805C24.9897 33.5666 25.0619 33.8472 25.1452 34.1861C25.7007 36.4 26.2507 38.6138 26.8062 40.8277C27.2478 42.5916 27.6978 44.3555 28.145 46.1305H42.7993C42.8438 46.0333 42.8993 45.9527 42.9215 45.8666C43.9131 41.9083 44.9103 37.9499 45.8741 33.9861C45.9241 33.7777 45.7546 33.4166 45.5713 33.2666C44.0214 31.9888 42.4466 30.7416 40.8772 29.4861C39.0968 28.0611 37.3136 26.6361 35.5249 25.2055L35.5277 25.2083ZM25.1674 4.04166C25.3341 4.13888 25.3896 4.18055 25.4535 4.20555C28.6894 5.48888 31.9252 6.78055 35.1694 8.04443C35.3916 8.13054 35.711 8.09721 35.9443 8.00832C37.0664 7.5861 38.1719 7.12221 39.2857 6.67777C41.4189 5.82499 43.552 4.97222 45.8546 4.05277C38.8774 1.84166 32.0919 1.84722 25.1674 4.04166ZM10.0409 56.4138C14.0239 61.4194 19.0262 64.8666 25.0535 66.9221C25.0369 66.7888 25.0452 66.7388 25.0258 66.7055C23.2176 63.8666 21.4094 61.0249 19.5818 58.1944C19.4651 58.0166 19.1985 57.8721 18.979 57.8333C17.5958 57.586 16.2043 57.3721 14.8155 57.1527C13.2239 56.9027 11.6324 56.6583 10.0409 56.4138ZM46.7907 66.4777C52.5069 64.4999 57.2315 61.1499 61.0867 56.4916C60.9617 56.4583 60.9145 56.4305 60.8756 56.436C57.912 56.8971 54.9483 57.3583 51.9874 57.8333C51.8347 57.8583 51.6514 57.9971 51.5736 58.1333C49.9709 60.911 48.3822 63.6944 46.7907 66.4777ZM64.7697 20.0222C64.6836 20.2639 64.6448 20.3416 64.6253 20.425C64.067 22.8027 63.5226 25.1833 62.9421 27.5583C62.8421 27.9666 62.9227 28.2361 63.1865 28.5472C64.8531 30.5222 66.5057 32.5111 68.1528 34.5027C68.2639 34.6388 68.3167 34.8222 68.3944 34.9861L68.6055 34.8C68.4833 29.6333 67.2473 24.7555 64.767 20.0222H64.7697ZM2.39149 34.8277C2.48871 34.8138 2.54982 34.8222 2.57204 34.7972C4.38022 32.6388 6.19119 30.4833 7.97993 28.3111C8.09381 28.1722 8.09936 27.8861 8.0577 27.6861C7.87994 26.8055 7.67162 25.9305 7.46608 25.0555C7.08278 23.4277 6.69392 21.8027 6.26062 19.9778C3.74694 24.7472 2.51926 29.6305 2.39149 34.825V34.8277Z" fill="white" fill-opacity="0.2"/>
</svg>';


    $countsvg = 0;
    $count = 1;
    foreach ($sections as $key => $section) {

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

        // foreach ($tableact as $v) {
        //     $content .= $v;
        // }

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

        if ($countsvg == 1) {
            $svgicon = $icon1;
        } else if ($countsvg == 2) {
            $svgicon = $icon2;
        } else if ($countsvg == 3) {
            $svgicon = $icon3;
            $countsvg = 0;
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



$content .= '<div class="col-8">

    <div class="col-sm-12 col-md-12 col-lg-12" style="background: transparent;border-radius: 20px;">
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
