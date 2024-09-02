<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once(__DIR__ . '/../../../lib/phpspreadsheet/vendor/autoload.php');

// retourne le plus haut rôle de l'utilisateur connecté
function getMainRole($userid = null)
{
    global $DB, $USER;
    if (!$userid) {
        $userid = $USER->id;
    }
    $rolename = "";
    $assignments = $DB->get_records('role_assignments', ['userid' => $userid]);
    foreach ($assignments as $assignment) {
        $role = $DB->get_record('role', ['id' => $assignment->roleid]);
        if ($role->shortname == "super-admin") {
            $rolename = "super-admin";
        } else if ($role->shortname == "manager") {
            if ($rolename != "super-admin") {
                $rolename = "manager";
            }
        } else if ($role->shortname == "smalleditingteacher") {
            if ($rolename != "super-admin" && $rolename != "manager") {
                $rolename = "smalleditingteacher";
            }
        } else if ($role->shortname == "editingteacher") {
            if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "smalleditingteacher") {
                $rolename = "editingteacher";
            }
        } else if ($role->shortname == "teacher") {
            if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "smalleditingteacher" && $rolename != "editingteacher") {
                $rolename = "teacher";
            }
        } else if ($role->shortname == "noneditingteacher") {
            if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "teacher" && $rolename != "smalleditingteacher" && $rolename != "editingteacher") {
                $rolename = "noneditingteacher";
            }
        } else if ($role->shortname == "student") {
            if ($rolename != "super-admin" && $rolename != "manager" && $rolename != "teacher" && $rolename != "noneditingteacher" && $rolename != "smalleditingteacher" && $rolename != "editingteacher") {
                $rolename = "student";
            }
        }
        // if ($role->shortname == "manager") {
        //     $rolename = "manager";
        // } else if ($role->shortname == "teacher") {
        //     if ($rolename != "manager") {
        //         $rolename = "teacher";
        //     }
        // } else if ($role->shortname == "noneditingteacher") {
        //     if ($rolename != "manager" && $rolename != "teacher") {
        //         $rolename = "noneditingteacher";
        //     }
        // } else if ($role->shortname == "student") {
        //     if ($rolename != "manager" && $rolename != "teacher" && $rolename != "noneditingteacher") {
        //         $rolename = "student";
        //     }
        // }
    }
    return $rolename;
}

function isPortailRH(){
    $portail = getConfigPortail();
    
    if($portail == "portailrh"){
        return true;
    } else {
        redirect('/');
    }
}
function getConfigPortail(){
    global $DB;

    // $test = $DB->get_record_sql('SELECT *
    // FROM information_schema.COLUMNS
    // WHERE TABLE_NAME = "mdl_smartch_config"
    // AND COLUMN_NAME = "value"', null);
    
    // //si on doit mettre les nouveaux noms de colonne
    // // key renvoyait des bugs
    // if($test){
    //     //on change la keyvalue
    //     $DB->execute('ALTER TABLE `mdl_smartch_config` CHANGE `key` `config_key` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL', null);
    //     $DB->execute('ALTER TABLE `mdl_smartch_config` CHANGE `value` `config_value` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL', null);
    // }
    
    
    $portail = $DB->get_record_sql('SELECT * 
    FROM mdl_smartch_config sc
    WHERE sc.config_key = "portail"', null);

    if(empty($portail)){
        //On créer le portail
        $portail = new stdClass();
        $portail->config_key = "portail";
        $portail->config_value = "portailformation";
        $DB->insert_record('smartch_config', $portail);
    }

    return $portail->config_value;
}

function getConfigValueByKey($key){

    global $DB;
    
    $config = $DB->get_record_sql('SELECT * 
    FROM mdl_smartch_config sc
    WHERE sc.config_key = "'.$key.'"', null);

    if(empty($config)){
        return "";
    }

    return $config->config_value;
}

function smartchFormatEmail($contentemail, $user) {

    //On créer le header
    $contentmail = '';
    $contentmail .= '<div class="container">';
    $contentmail .= displayHeaderMail($user->firstname . ' ' . $user->lastname);

    $contentmail .= '<div style="color:#00315A;text-align:center;letter-spacing:1px;">';
    $contentmail .= $contentemail;
    $contentmail .= '</div>';

    $contentmail .= '<div>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_plateforme").'" style="background:#00315A;border-radius:20px;display:inline-block;text-align:center;margin:20px auto;color:white;padding:10px 20px;text-decoration:none;">Accéder à mon compte</a>';
    $contentmail .= '</div>';

    $contentmail .= '<div style="color:#00315A;text-align:center;letter-spacing:1px;margin:20px 0;">';
    $contentmail .= 'Pour toute question ou aide supplémentaire, n\'hésitez pas à contacter notre équipe d\'assistance via 
    <a style="margin:10px auto;display:block;text-decoration:none;font-weight:bold;color:#00315A;" href="mailto:'.getConfigValueByKey("contact_mail").'" style="color:#00315A;text-decoration:none;">'.getConfigValueByKey("contact_mail").'</a>
    <br>L\'équipe du service formation de la FFA';
    $contentmail .= '</div>';

    //on rajouter le footer un bandeau bleu avec des icones centré
    $contentmail .= displayFooterMail();

    $contentmail .= '</div>';

    return $contentmail;
}

function displayHeaderMail($title){
    $contentmail = '';
    $contentmail .= ' <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

        .container {
            font-family: "Poppins", sans-serif;
            max-width: 600px;
            margin:0 auto;
            text-align: center;
        }
    </style>';
    $contentmail .= '<div style="text-align:center;padding:20px;">';
    $contentmail .= '<svg style="width:300px;" width="961" height="296" viewBox="0 0 961 296" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M321.545 65.5828H184.481V102.924H234.442V205.048H271.821V102.924H321.545" fill="#0D2366"/>
<path d="M89.7097 65.5828L0 146.234V205.048H35.8085L111.108 182.204V205.048H149.443V65.5828H89.7097ZM111.108 145.117L40.4681 166.802V159.428L111.108 96.8406" fill="#0D2366"/>
<path d="M961 65.5828H818.003V205.048H944.105V167.728H892.248L961 102.877V65.5828ZM911.589 101.101L856.973 151.805V101.101" fill="#0D2366"/>
<path d="M921.56 34.3375L910.116 0L856.384 17.9366L867.824 52.2742" fill="#0D2366"/>
<path d="M699.074 167.673V65.587H661.06L661.067 205.052H788.579V167.673" fill="#0D2366"/>
<path d="M624.883 134.558L611.298 62.2746L573.627 69.1658L582.35 114.371H390.652L399.371 69.1658L361.692 62.2746L348.123 134.558L361.692 206.854L399.371 199.963L390.551 154.263H582.447L573.627 199.963L611.298 206.854" fill="#0D2366"/>
<path d="M83.3825 263.342H98.6861V267.64H88.275V275.487H97.7381V279.853H88.275V287.078H83.3825" fill="#0D2366"/>
<path d="M133.991 263.342H141.292C148.596 263.342 153.798 268.194 153.798 275.212C153.798 282.226 148.63 287.078 141.292 287.078H133.991V263.342ZM141.05 282.789C146.324 282.789 148.732 279.646 148.732 275.212C148.732 270.783 146.324 267.64 141.05 267.64H138.883V282.789" fill="#0D2366"/>
<path d="M206.632 287.078H200.974L197.063 281.075C196.611 281.143 196.12 281.181 195.663 281.181H193.991V287.078H189.094V263.342H195.663C202.303 263.342 206.277 266.515 206.277 272.314C206.277 276.012 204.639 278.351 201.706 279.675L206.632 287.078ZM195.523 276.921C199.505 276.921 201.249 275.661 201.249 272.315C201.249 268.96 199.438 267.632 195.523 267.632H193.991V276.921" fill="#0D2366"/>
<path d="M223.794 263.135H224.217L235.073 287.082H229.935L228.826 284.392H219.185L218.064 287.082H213.349L223.794 263.135ZM227.074 280.31L225.364 276.189C224.67 274.518 223.934 271.929 223.934 271.929C223.934 271.929 223.231 274.518 222.537 276.189L220.827 280.31" fill="#0D2366"/>
<path d="M245.598 267.636H238.823V263.338H257.262V267.636H250.491V287.078H245.598" fill="#0D2366"/>
<path d="M266.192 287.078H271.08V263.342H266.192V287.078Z" fill="#0D2366"/>
<path d="M280.531 275.208C280.531 268.194 285.978 263.029 292.83 263.029C299.673 263.029 305.116 268.194 305.116 275.208C305.116 282.226 299.673 287.391 292.83 287.391C285.978 287.391 280.531 282.226 280.531 275.208ZM300.092 275.208C300.092 270.775 297.155 267.39 292.83 267.39C288.492 267.39 285.563 270.775 285.563 275.208C285.563 279.65 288.492 283.03 292.83 283.03C297.155 283.03 300.092 279.65 300.092 275.208Z" fill="#0D2366"/>
<path d="M321.549 276.05C320.368 274.929 318.722 272.801 318.722 272.801C318.722 272.801 319.069 275.424 319.069 277.133V287.078H314.499V263.135H314.977L326.962 274.375C328.113 275.453 329.755 277.619 329.755 277.619C329.755 277.619 329.442 274.929 329.442 273.296V263.342H334.022V287.285H333.531" fill="#0D2366"/>
<path d="M357.142 263.342H372.441V267.64H362.03V275.487H371.502V279.853H362.03V287.078H357.142" fill="#0D2366"/>
<path d="M399.316 287.078H393.653L389.747 281.075C389.29 281.143 388.803 281.181 388.35 281.181H386.678V287.078H381.782V263.342H388.35C394.991 263.342 398.969 266.515 398.969 272.314C398.969 276.012 397.322 278.351 394.389 279.675L399.316 287.078ZM388.206 276.921C392.189 276.921 393.941 275.661 393.941 272.315C393.941 268.96 392.117 267.632 388.206 267.632H386.678V276.921" fill="#0D2366"/>
<path d="M416.973 263.135H417.392L428.247 287.082H423.109L421.996 284.392H412.359L411.242 287.082H406.528L416.973 263.135ZM420.244 280.31L418.543 276.189C417.84 274.518 417.108 271.929 417.108 271.929C417.108 271.929 416.41 274.518 415.716 276.189L413.997 280.31" fill="#0D2366"/>
<path d="M443.733 276.05C442.552 274.929 440.91 272.801 440.91 272.801C440.91 272.801 441.257 275.424 441.257 277.133V287.078H436.682V263.135H437.169L449.146 274.375C450.301 275.453 451.939 277.619 451.939 277.619C451.939 277.619 451.626 274.929 451.626 273.296V263.342H456.205V287.285H455.718" fill="#0D2366"/>
<path d="M503.123 263.135H503.534L514.402 287.082H509.256L508.147 284.392H498.51L497.389 287.082H492.674L503.123 263.135ZM506.395 280.31L504.693 276.189C503.991 274.518 503.254 271.929 503.254 271.929C503.254 271.929 502.56 274.518 501.862 276.189L500.148 280.31" fill="#0D2366"/>
<path d="M522.938 287.078H527.818V263.342H522.938V287.078Z" fill="#0D2366"/>
<path d="M536.02 282.404L540.002 280.098C541.052 281.807 542.411 283.068 544.827 283.068C546.846 283.068 548.137 282.053 548.137 280.657C548.137 278.982 546.816 278.385 544.573 277.412L543.35 276.892C539.825 275.386 537.488 273.499 537.488 269.522C537.488 265.855 540.282 263.063 544.645 263.063C547.752 263.063 549.995 264.146 551.599 266.976L547.79 269.417C546.956 267.915 546.046 267.323 544.645 267.323C543.21 267.323 542.301 268.224 542.301 269.417C542.301 270.88 543.21 271.477 545.31 272.382L546.529 272.911C550.693 274.688 553.025 276.502 553.025 280.58C553.025 284.988 549.576 287.396 544.924 287.396C540.388 287.396 537.455 285.23 536.02 282.404Z" fill="#0D2366"/>
<path d="M562.056 263.342H577.775V267.64H566.953V273.689H576.48V277.877H566.953V282.789H578.266V287.078H562.056" fill="#0D2366"/>
<path d="M600.976 263.342H608.272C615.572 263.342 620.778 268.194 620.778 275.212C620.778 282.226 615.61 287.078 608.272 287.078H600.976V263.342ZM608.031 282.789C613.3 282.789 615.704 279.646 615.704 275.212C615.704 270.783 613.3 267.64 608.031 267.64H605.864V282.789" fill="#0D2366"/>
<path d="M647.462 263.135H647.881L658.741 287.082H653.603L652.494 284.392H642.849L641.736 287.082H637.013L647.462 263.135ZM650.738 280.31L649.032 276.189C648.334 274.518 647.602 271.929 647.602 271.929C647.602 271.929 646.899 274.518 646.201 276.189L644.491 280.31" fill="#0D2366"/>
<path d="M669.275 267.636H662.495V263.338H680.943V267.636H674.163V287.078H669.275" fill="#0D2366"/>
<path d="M689.754 263.342H694.651V275.487H704.808V263.342H709.696V287.078H704.808V279.853H694.651V287.078H689.754" fill="#0D2366"/>
<path d="M719.875 263.342H724.763V282.789H735.521V287.078H719.875" fill="#0D2366"/>
<path d="M776.066 267.636H769.29V263.338H787.73V267.636H780.958V287.078H776.066" fill="#0D2366"/>
<path d="M796.647 287.078H801.544V263.342H796.647V287.078Z" fill="#0D2366"/>
<path d="M809.741 282.404L813.724 280.098C814.773 281.807 816.132 283.068 818.549 283.068C820.567 283.068 821.858 282.053 821.858 280.657C821.858 278.982 820.538 278.385 818.295 277.412L817.08 276.892C813.542 275.386 811.214 273.499 811.214 269.522C811.214 265.855 814.003 263.063 818.367 263.063C821.477 263.063 823.712 264.146 825.32 266.976L821.511 269.417C820.677 267.915 819.768 267.323 818.367 267.323C816.94 267.323 816.026 268.224 816.026 269.417C816.026 270.88 816.94 271.477 819.031 272.382L820.258 272.911C824.406 274.688 826.755 276.502 826.755 280.58C826.755 284.988 823.293 287.396 818.646 287.396C814.105 287.396 811.176 285.23 809.741 282.404Z" fill="#0D2366"/>
<path d="M835.782 263.097H836.163L847.861 275.948L859.389 263.097H859.808V287.078H855.085V278.178C855.085 276.57 855.267 274.028 855.267 274.028C855.267 274.028 853.836 276.117 852.719 277.374L848.004 282.645H847.547L842.833 277.374C841.707 276.117 840.281 274.028 840.281 274.028C840.281 274.028 840.458 276.57 840.458 278.178V287.078H835.782" fill="#0D2366"/>
<path d="M869.982 263.342H885.701V267.64H874.875V273.689H884.406V277.877H874.875V282.789H886.188V287.078H869.982" fill="#0D2366"/>
<path d="M108.022 263.342H123.741V267.64H112.915V273.689H122.446V277.877H112.915V282.789H124.227V287.078H108.022" fill="#0D2366"/>
<path d="M119.83 257.864L118.658 254.365L112.428 256.455L113.605 259.958" fill="#0D2366"/>
<path d="M163.113 263.342H178.827V267.64H168.005V273.689H177.536V277.877H168.005V282.789H179.318V287.078H163.113" fill="#0D2366"/>
<path d="M174.916 257.864L173.744 254.365L167.514 256.455L168.691 259.958" fill="#0D2366"/>
<path d="M744.684 263.342H760.402V267.64H749.572V273.689H759.107V277.877H749.572V282.789H760.889V287.078H744.684" fill="#0D2366"/>
<path d="M756.487 257.864L755.32 254.365L749.09 256.455L750.262 259.958" fill="#0D2366"/>
<path d="M465.067 275.229C465.067 268.203 470.497 263.063 477.307 263.063C481.607 263.063 484.692 264.603 486.829 267.919L483.088 270.576C481.962 268.761 480.041 267.467 477.307 267.467C473.011 267.467 470.108 270.792 470.108 275.229C470.108 279.671 473.011 283.06 477.307 283.06C480.388 283.06 482.102 281.592 483.435 279.565L487.244 282.188C485.183 285.399 481.89 287.43 477.307 287.43C470.497 287.43 465.067 282.256 465.067 275.229Z" fill="#0D2366"/>
<path d="M479.859 293.902L478.682 290.403L472.457 292.497L473.629 296" fill="#0D2366"/>
<path d="M634.706 266.845L633.534 263.342L627.3 265.44L628.472 268.939" fill="#0D2366"/>
</svg>';
    $contentmail .= '<h3 style="margin-top: 0px;text-transform:uppercase;font-size:2rem;font-weight:500;"><span class="FFABold FFF-Blue" style="letter-spacing:1px;color:#004687;">Bonjour, </span><span class="FFABold FFF-Blue" style="letter-spacing:1px;color:#3fb6ea;">'.$title.'</span></h3>';
    $contentmail .= '</div>';

    return $contentmail;
}

function displayFooterMail(){
    $contentmail = '';
    //on rajouter le footer un bandeau bleu avec des icones centré
    $contentmail .= '<div style="background:#00315A;padding:20px;text-align:center;margin-top:50px;display:flex;justify-content:space-around;align-items:center;">';
    $contentmail .= '<a href="'.getConfigValueByKey("url_facebook").'" target="_blank">
        <svg fill="white" style="width:30px;" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Facebook</title><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></svg>
    </a>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_instagram").'" target="_blank">
        <svg fill="white" style="width:30px;" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Instagram</title><path d="M7.0301.084c-1.2768.0602-2.1487.264-2.911.5634-.7888.3075-1.4575.72-2.1228 1.3877-.6652.6677-1.075 1.3368-1.3802 2.127-.2954.7638-.4956 1.6365-.552 2.914-.0564 1.2775-.0689 1.6882-.0626 4.947.0062 3.2586.0206 3.6671.0825 4.9473.061 1.2765.264 2.1482.5635 2.9107.308.7889.72 1.4573 1.388 2.1228.6679.6655 1.3365 1.0743 2.1285 1.38.7632.295 1.6361.4961 2.9134.552 1.2773.056 1.6884.069 4.9462.0627 3.2578-.0062 3.668-.0207 4.9478-.0814 1.28-.0607 2.147-.2652 2.9098-.5633.7889-.3086 1.4578-.72 2.1228-1.3881.665-.6682 1.0745-1.3378 1.3795-2.1284.2957-.7632.4966-1.636.552-2.9124.056-1.2809.0692-1.6898.063-4.948-.0063-3.2583-.021-3.6668-.0817-4.9465-.0607-1.2797-.264-2.1487-.5633-2.9117-.3084-.7889-.72-1.4568-1.3876-2.1228C21.2982 1.33 20.628.9208 19.8378.6165 19.074.321 18.2017.1197 16.9244.0645 15.6471.0093 15.236-.005 11.977.0014 8.718.0076 8.31.0215 7.0301.0839m.1402 21.6932c-1.17-.0509-1.8053-.2453-2.2287-.408-.5606-.216-.96-.4771-1.3819-.895-.422-.4178-.6811-.8186-.9-1.378-.1644-.4234-.3624-1.058-.4171-2.228-.0595-1.2645-.072-1.6442-.079-4.848-.007-3.2037.0053-3.583.0607-4.848.05-1.169.2456-1.805.408-2.2282.216-.5613.4762-.96.895-1.3816.4188-.4217.8184-.6814 1.3783-.9003.423-.1651 1.0575-.3614 2.227-.4171 1.2655-.06 1.6447-.072 4.848-.079 3.2033-.007 3.5835.005 4.8495.0608 1.169.0508 1.8053.2445 2.228.408.5608.216.96.4754 1.3816.895.4217.4194.6816.8176.9005 1.3787.1653.4217.3617 1.056.4169 2.2263.0602 1.2655.0739 1.645.0796 4.848.0058 3.203-.0055 3.5834-.061 4.848-.051 1.17-.245 1.8055-.408 2.2294-.216.5604-.4763.96-.8954 1.3814-.419.4215-.8181.6811-1.3783.9-.4224.1649-1.0577.3617-2.2262.4174-1.2656.0595-1.6448.072-4.8493.079-3.2045.007-3.5825-.006-4.848-.0608M16.953 5.5864A1.44 1.44 0 1 0 18.39 4.144a1.44 1.44 0 0 0-1.437 1.4424M5.8385 12.012c.0067 3.4032 2.7706 6.1557 6.173 6.1493 3.4026-.0065 6.157-2.7701 6.1506-6.1733-.0065-3.4032-2.771-6.1565-6.174-6.1498-3.403.0067-6.156 2.771-6.1496 6.1738M8 12.0077a4 4 0 1 1 4.008 3.9921A3.9996 3.9996 0 0 1 8 12.0077"/></svg>
    </a>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_linkedin").'" target="_blank">
        <svg fill="white" style="width:30px;" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>LinkedIn</title><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
    </a>';
    $contentmail .= '<a href="'.getConfigValueByKey("url_youtube").'" target="_blank">
        <svg fill="white" style="width:30px;" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>YouTube</title><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
    </a>';
    $contentmail .= '</div>';
    return $contentmail;
}

function createSendUserEmail(){
    $senduser = new stdClass();
    $senduser->email = getConfigValueByKey("contact_mail");
    $senduser->firstname = "Portail";
    $senduser->lastname = "Formation FFA";
    $senduser->maildisplay = true;
    $senduser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
    $senduser->id = -99; // Moodle User ID. If it is for someone who is not a Moodle user, use an invalid ID like -99.
    $senduser->firstnamephonetic = "Portail";
    $senduser->lastnamephonetic = "Formation FFA";
    $senduser->middlename = "";
    $senduser->alternatename = "";
    return $senduser;
}

function isAdmin()
{
    $rolename = getMainRole();
    if ($rolename == "super-admin") {
    } else {
        redirect('/');
    }
}

function isAdminFormation()
{
    $rolename = getMainRole();
    if ($rolename == "super-admin" || $rolename == "manager") {
    } else {
        redirect('/');
    }
}

function hasResponsablePedagogiqueRole(){
    $rolename = getMainRole();
    if ($rolename == "super-admin" || $rolename == "manager" || $rolename == "smalleditingteacher") {
        return true;
    }
    return false;
}

function formatGroupName($inputString)
{
    $parts = explode(" - ", $inputString, 2);
    if (count($parts) > 1) {
        return $parts[1];
    } else {
        return $inputString;
    }
}

function displayHeaderEditActivity($backurl, $coursetitle, $activityname, $isactivity)
{
    $rolename = getMainRole();
    // if ($rolename == "super-admin") {
    $position = "-190px";
    // } else {
    //     $position = "-120px";
    //     //on cache les paramètres
    //     echo '<style>
    //         .secondary-navigation{
    //             display:none !important;
    //         }
    //         #topofscroll{
    //             margin-top: 130px !important;
    //         }
    //         </style>';
    // }
    echo '<div style="top: ' . $position . ';position:relative;left:0;cursor:pointer;display: flex; align-items: center;" onclick="location.href=\'' . $backurl . '\'">
            <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
            </svg>
            <div class="ml-4 FFF-White FFARegular" style="font-size: 12px;">Retour au parcours</div>
        </div>';

    //le titre
    echo '<div class="row">
            <div style="width:100%" >';
    if ($isactivity) {
        echo '<div>
                    <h4 class="smartch_course_header_title">' . $coursetitle . '</h4>
                </div>';
    }
    echo '
                <div class="smartch_course_header">
                    <h4 class="smartch_title FFABold FFF-Blue">' . $activityname . '</h4>';
    // if ($isactivity) {
    //     echo '<div>
    //             <a class="btn btn-primary" href="' . $backurl . '">Quitter l\'activité</a>
    //         </div>';
    // }

    echo '</div>
                </div>
            </div>';

    echo '<style>
        .activity-header{
            display:none;
        }
        div[role=main] {
            margin-top: 0px !important;
        }
        </style>';
}

function displayHeaderActivity($backurl, $coursetitle, $activityname, $isactivity, $sectionid = null, $activityid = null, $edit = false, $hascompletion = true)
{

    global $DB, $USER;
    $completion = "";

    if ($isactivity) {

        if ($hascompletion) {
            //on va chercher le role pour la completion
            $rolename = getMainRole();
            if ($rolename == "student") {
                $completion = getActivityCompletionStatus($activityid, $USER->id);
            }
        }

        //on va chercher le type d'activity
        $type = $activityid;
        // $backurl = 'location.href="' . $backurl . '"';


        $section = $DB->get_record('course_sections', ['id' => $sectionid]);
        $matiere = $section->name;
        if ($matiere == "") {
            $matiere = "Généralités";
        }
        $textback = "Retour " . $coursetitle;
    } else {
        $textback = "Retour au parcours";
        // $backurl = 'javascript: history.go(-1)';
    }



    $content = "";
    $rolename = getMainRole();
    if ($rolename != "super-admin") {
        $position = "90px";
    } else if ($rolename != "manager") {
        $position = "90px";
    } else {
        $position = "90px";
        //on cache les paramètres
        $content .= '<style>
            .secondary-navigation{
                display:none !important;
            }
            #topofscroll{
                margin-top: 130px !important;
            }
            </style>';
    }
    if (!$edit) {
        $content .= '<a class="returnactivity" href="' . $backurl . '">';
        $content .= '<div style="display: flex; align-items: center;font-size:0.8rem;height:0;">
                        <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
                        </svg>
                        <div class="ml-4 FFF-White FFARegular">' . $textback . '</div>
                    </a>';
    }

    if ($isactivity) {
        $content .= '<div>
                            <h4 style="margin:20px 0;font-size: 1rem;" class="FFF-Blue FFABold">' . $matiere . '</h4>
                        </div>';
    }
    $content .= '</div>';

    //le titre
    $content .= '<div class="row">
            <div style="width:100%" >';

    $content .= '
                <div class="smartch_course_header">
                    <h4 style="margin-right:20px;letter-spacing:2px;display:inline;" class="smartch_title FFABold FFF-Blue">' . $activityname . ' </h4>' . $completion . '';
    // if ($isactivity) {
    //     echo '<div>
    //             <a class="btn btn-primary" href="' . $backurl . '">Quitter l\'activité</a>
    //         </div>';
    // }

    $content .= '</div>
                </div>
            </div>';

    $content .= '<style>
        .activity-header{
            display:none;
        }
        div[role=main] {
            margin-top: 0px !important;
        }
        </style>';

    echo $content;
}

function getCourseCustom($courseid, $shortname){
    global $DB;

    if(!$shortname || !$courseid){
        return "";
    }
    //on recupère les champs personnalisés
    $result = $DB->get_record_sql('
    SELECT cd.value 
    FROM mdl_customfield_data cd
    JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
    WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "'.$shortname.'"', null);
    if ($result) {
        return $result->value;
    }
    return "";
}

// function displayHeaderActivity2($backurl, $coursetitle, $activityname, $isactivity)
// {
//     $content = "";
//     $rolename = getMainRole();
//     if ($rolename == "super-admin") {
//         $position = "-200px";
//     } else {
//         $position = "-120px";
//         //on cache les paramètres
//         $content .= '<style>
//             .secondary-navigation{
//                 display:none !important;
//             }
//             #topofscroll{
//                 margin-top: 130px !important;
//             }
//             </style>';
//     }
//     $content .= '<div style="top: ' . $position . ';position:absolute;cursor:pointer;" onclick="location.href=\'' . $backurl . '\'" class="fff-course-box-info-details">
//             <svg width="8" height="15" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
//                 <path fill-rule="evenodd" clip-rule="evenodd" d="M5.70711 0.292893C6.09763 0.683417 6.09763 1.31658 5.70711 1.70711L2.41421 5L5.70711 8.29289C6.09763 8.68342 6.09763 9.31658 5.70711 9.70711C5.31658 10.0976 4.68342 10.0976 4.29289 9.70711L0.292893 5.70711C-0.0976311 5.31658 -0.0976311 4.68342 0.292893 4.29289L4.29289 0.292893C4.68342 -0.0976311 5.31658 -0.0976311 5.70711 0.292893Z" fill="white"/>
//             </svg>
//             <div class="ml-4 FFF-White FFARegular">Retour au parcours</div>
//         </div>';

//     //le titre
//     $content .= '<div class="row">
//             <div style="width:100%" >';
//     if ($isactivity) {
//         $content .= '<div>
//                     <h4 class="smartch_course_header_title">' . $coursetitle . '</h4>
//                 </div>';
//     }
//     $content .= '
//                 <div class="smartch_course_header">
//                     <h4 class="smartch_title FFABlack FFF-Blue">' . $activityname . '</h4>';
//     // if ($isactivity) {
//     //     echo '<div>
//     //             <a class="btn btn-primary" href="' . $backurl . '">Quitter l\'activité</a>
//     //         </div>';
//     // }

//     $content .= '</div>
//                 </div>
//             </div>';

//     $content .= '<style>
//         .activity-header{
//             display:none;
//         }
//         </style>';

//     return $content;
// }

function displayNotification($texte)
{
    echo '<div id="notification" style="z-index: 300;display: flex; position: fixed; justify-content: center; top: 100px; left: 0; width: 100vw;">
        <div style="font-weight:bold;position:relative;background: white; padding: 5px 100px; border-radius: 15px; color: #00315a; border: 1px solid #00315a;">
            '.$texte.'
            <svg style="position: absolute; left: 10px; top: 8px;" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18ZM13.7071 8.70711C14.0976 8.31658 14.0976 7.68342 13.7071 7.29289C13.3166 6.90237 12.6834 6.90237 12.2929 7.29289L9 10.5858L7.70711 9.29289C7.31658 8.90237 6.68342 8.90237 6.29289 9.29289C5.90237 9.68342 5.90237 10.3166 6.29289 10.7071L8.29289 12.7071C8.68342 13.0976 9.31658 13.0976 9.70711 12.7071L13.7071 8.70711Z" fill="#00315a"/>
            </svg>

        </div>  
        </div>';

    echo '
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
setTimeout(function() {
    const notif = $("#notification");
    notif.hide({
      duration: 300, 
      easing: "linear", // Fonction danimation (par exemple, "linear", "swing", "easeInOut")
      complete: function() {
        notif.remove();
      }
    });
  }, 2000);
  
        </script>';
}

function displayMessageSent()
{
    echo '<div id="notification" style="z-index: 300;display: flex; position: fixed; justify-content: center; top: 100px; left: 0; width: 100vw;">
        <div style="font-weight:bold;position:relative;background: white; padding: 5px 100px; border-radius: 15px; color: #00315a; border: 1px solid #00315a;">
            Message envoyé
            <svg style="position: absolute; left: 10px; top: 8px;" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18ZM13.7071 8.70711C14.0976 8.31658 14.0976 7.68342 13.7071 7.29289C13.3166 6.90237 12.6834 6.90237 12.2929 7.29289L9 10.5858L7.70711 9.29289C7.31658 8.90237 6.68342 8.90237 6.29289 9.29289C5.90237 9.68342 5.90237 10.3166 6.29289 10.7071L8.29289 12.7071C8.68342 13.0976 9.31658 13.0976 9.70711 12.7071L13.7071 8.70711Z" fill="#00315a"/>
            </svg>

        </div>  
        </div>';

    echo '
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
setTimeout(function() {
    const notif = $("#notification");
    notif.hide({
      duration: 300, 
      easing: "linear", // Fonction danimation (par exemple, "linear", "swing", "easeInOut")
      complete: function() {
        notif.remove();
      }
    });
  }, 2000);
  
        </script>';
}

function isFreeCourse($courseid)
{
    global $DB;
    $course = $DB->get_record('course', ['id' => $courseid]);
    $freecat = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);
    if ($course->category == $freecat->id) {
        return true;
    } else {
        return false;
    }
}

function getResponsablePedagogique($groupid, $courseid)
{
    global $DB;

    if (isFreeCourse($courseid)) {
        array('Formation Gratuite', null);
    } else {
        $queryresponsable = 'SELECT DISTINCT u.id, u.firstname, u.lastname 
        FROM mdl_groups g
        JOIN mdl_groups_members gm ON gm.groupid = g.id
        JOIN mdl_user u ON u.id = gm.userid
        JOIN mdl_role_assignments ra ON ra.userid = u.id
        JOIN mdl_role r ON r.id = ra.roleid
        WHERE g.id = ' . $groupid . ' 
        AND r.shortname = "smalleditingteacher"';
        // var_dump($queryresponsable);
        $findresponsable = $DB->get_records_sql($queryresponsable, null);
        // var_dump($findresponsable);
        $found = reset($findresponsable);
        if ($found) {
            $coach = $found->firstname . ' ' . $found->lastname;
        } else {
            $coach = "Aucun responsable pédagogique";
        }

        return array($coach, $found);
    }
}


function isStudent()
{
    $rolename = getMainRole();
    if ($rolename == "student") {
        //on redirige vers la bonne page de cours
        redirect('/');
    }
}

function nothingtodisplay($message)
{
    return '<div class="row">
                <div class="col-md-12">
                    <h3 class="nothing_to_display">' . $message . '</h3>
                </div>
            </div>';
}


function longTitles($chaine)
{
    if (strlen($chaine) > 50) {
        $chaine = substr($chaine, 0, 47) . '...';
    }
    return $chaine;
}

function longTitlesModules($chaine)
{
    if (strlen($chaine) > 53) {
        $chaine = substr($chaine, 0, 50) . '...';
    }
    return $chaine;
}

require_once("$CFG->dirroot/enrol/cohort/locallib.php");
require_once($CFG->dirroot.'/group/lib.php');

function deleteCohort($cohortid){
    global $DB;
    //on va chercher tous les cours synchro avec la cohorte
    $querycourses = 'SELECT c.id, c.fullname as name, ss.startdate, ss.enddate
    FROM mdl_enrol e
    JOIN mdl_cohort co ON e.customint1 = co.id
    JOIN mdl_course c ON c.id = e.courseid
    JOIN mdl_smartch_session ss ON ss.groupid = e.customint2
    WHERE co.id = ' . $cohortid;

    $courses = $DB->get_records_sql($querycourses, null);

    //on supprime la sync + le groupe + la session
    foreach($courses as $course){
        desyncCohortWithCourse($cohortid, $course->id);
    }

    //On supprime la cohorte
    $cohort = $DB->get_record('cohort', ['id' => $cohortid]);
    cohort_delete_cohort($cohort);
}

function syncCohortWithCourse($cohortid, $courseid, $startdate = null, $enddate = null){
    global $DB;

    //on va chercher la cohorte
    $cohort = $DB->get_record('cohort', ['id' => $cohortid]);

    //on créer le groupe dans le cours
    $groupdata = new stdClass();
    $groupdata->courseid = $courseid;
    $groupdata->name = $cohort->name;
    $groupdata->description = "Groupe lié via la cohorte " . $cohort->name;
    $groupdata->descriptionformat = FORMAT_HTML;  // Utilisez la constante appropriée pour le format de description

    $groupid = groups_create_group($groupdata);

    //on créer la methode d'inscription
    $enrol = $DB->get_record('enrol', array('enrol'=>'cohort', 'courseid'=>$courseid, 'customint1'=>$cohortid));
    if (!$enrol) {
        $enrolid = $DB->insert_record('enrol', array('enrol'=>'cohort', 'courseid'=>$courseid, 'status'=>0, 'customint1'=>$cohortid, 'customint2'=>$groupid, 'roleid'=>5));
    } else {
        $enrolid = $enrol->id;
    }

    //on synchronise le cours avec la cohorte
    $trace = new \null_progress_trace();
    enrol_cohort_sync($trace, $courseid);

    //on créer la session
    $session = new stdClass();
    $session->startdate = strtotime($startdate);
    $session->enddate = strtotime($enddate);
    $session->externalid = 0;
    // $session->courseid = $courseid;
    $session->groupid = $groupid;
    $DB->insert_record('smartch_session', $session);
}

function smartchModal($title = null, $url = null, $btntext = null)
{
    echo '<div class="smartch_modal_container">';
    echo '<div class="smartch_modal" style="text-align:center;">';

    echo '<svg style="width:50px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>';

    echo '<h5 id="modal_title" style="text-align:center;margin:30px 0;">' . $title . '</h5>';

    echo '<div style="display:flex;align-items:center;justify-content:center;">';
    echo '<a onclick="document.querySelector(\'.smartch_modal_container\').style.display=\'none\'" class="smartch_btn">Annuler</a>';
    echo '<a style="margin-left:20px;" id="modal_btn" href="' . $url . '" class="smartch_btn">' . $btntext . '</a>';
    echo '</div>';

    echo '</div>'; // smartch_modal_container
    echo '</div>'; // smartch_modal
}

function desyncCohortWithCourse($cohortid, $courseid){
    global $DB;

    //on va chercher la cohorte
    // $cohort = $DB->get_record('cohort', ['id' => $cohortid]);

    //on va chercher le groupe de la cohorte dans le cours
    $enrol = $DB->get_record_sql('SELECT * 
    FROM mdl_enrol
    WHERE customint1 = ' . $cohortid . '
    AND courseid = ' . $courseid, null);

    $cohortgroupid = $enrol->customint2;

    //on supprime le groupe
    groups_delete_group($cohortgroupid);

    //on supprime la methode d'inscription
    $enrol = $DB->get_record('enrol', array('enrol'=>'cohort', 'courseid'=>$courseid, 'customint1'=>$cohortid));
    if ($enrol) {
        $DB->delete_records('enrol', ['id'=>$enrol->id]);
    }

    //on supprime la session
    $DB->delete_records('smartch_session', ['groupid' => $cohortgroupid]);

}

function extraireNomEquipe($entree)
{
    // Utilisation d'une expression régulière pour extraire le texte entre crochets et le tiret
    if (preg_match('/\]\s+(.+)/', $entree, $matches)) {
        // $matches[1] contient le texte après les crochets
        return $matches[1];
    } else {
        // Si le format n'est pas conforme, retourne une chaîne vide ou un message d'erreur
        return $entree;
    }
}

function getUserRoleFromCourse($courseid, $userid = null){
    global $DB, $USER;
    if(!$userid){
        $userid = $USER->id;
    }
    $context = context_course::instance($courseid);
    // Récupérer le rôle de l'utilisateur dans le contexte du cours
    $roles = get_user_roles($context, $userid, false);

    // Filtrer pour ne récupérer que les rôles assignés dans ce contexte précis
    $course_roles = array_filter($roles, function($role) use ($context) {
        return $role->contextid == $context->id;
    });

    $role = reset($course_roles);
    // var_dump($role);
    // die();
    return $role;
}

function displayRole($userid){
    $role = getUserRole($userid);
    if($role->name){
        return $role->name;
    } else {
        return $role->shortname;
    } 
}

function getUserRole($userid = null)
{
    global $DB, $USER;
    if (!$userid) {
        $userid = $USER->id;
    }
    // $rolename = "";
    $assignments = $DB->get_records('role_assignments', ['userid' => $userid]);
    foreach ($assignments as $assignment) {
        $role = $DB->get_record('role', ['id' => $assignment->roleid]);
        // $rolename = $role->name;
        break;
    }
    return $role;
}

function smartchModalRole()
{
    global $DB;
    echo '<div class="smartch_modal_container">';
    echo '<div class="smartch_modal" style="text-align:center;">';

    echo '<svg style="width:50px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>';

    echo '<div id="modal_content" style="text-align:center;margin:30px 0;"></div>';

    echo '<form action="" method="post" >';
    //On va chercher les roles
    $rolesavailables = $DB->get_records_sql('SELECT *
              FROM mdl_role r 
              WHERE r.shortname = "teacher" 
              OR r.shortname = "student"
              OR r.shortname = "editingteacher"', null);
    echo '<select name="newroleid" id="newroleid" class="form-control my-5">';
    foreach($rolesavailables as $role){
        $rolename = $role->name;
        if(!$rolename){
            $rolename = $role->shortname;
        }
        echo '<option value="'.$role->id.'">' . $rolename . '</option>';
    }
    echo '</select>';
    echo '<input type="hidden" value="" name="newroleuserid" id="newroleuserid"/>';

    echo '<div style="display:flex;align-items:center;justify-content:center;">';
    echo '<a onclick="document.querySelector(\'.smartch_modal_container\').style.display=\'none\'" class="smartch_btn">Annuler</a>';
    echo '<button onclick="this.form.submit()" style="margin-left:20px;" id="modal_btn" class="smartch_btn">Modifier</button>';
    echo '</div>';

    echo '</form>';

    echo '</div>'; // smartch_modal_container
    echo '</div>'; // smartch_modal
}

function countCourseActivities($courseid)
{
    global $DB;
    $results = $DB->get_records_sql('SELECT COUNT(*) count
    FROM mdl_course_modules cm
    JOIN mdl_course c ON c.id = cm.course
    JOIN mdl_modules m ON m.id = cm.module
    WHERE c.id = ' . $courseid, null);
    return reset($results)->count;
}

function getCourseActivities($courseid)
{
    global $DB;
    $results = $DB->get_records_sql("SELECT cm.id as id, cm.deletioninprogress, activity.summary as summary,
    activity.activityname, c.id AS courseid, c.fullname AS coursename,
    cm.instance AS activityid, m.id as activitytypeid, m.name AS activitytype, cm.section as moduleid
    FROM mdl_course_modules cm
    JOIN mdl_course c ON c.id = cm.course
    JOIN mdl_modules m ON m.id = cm.module
    LEFT JOIN (
        SELECT a.id, a.name AS activityname, 'scorm' AS activitytype, a.intro AS summary
        FROM mdl_scorm a
        UNION
        SELECT a.id, a.name AS activityname, 'forum' AS activitytype, a.intro AS summary
        FROM mdl_forum a
        UNION
        SELECT a.id, a.name AS activityname, 'label' AS activitytype, a.intro AS summary
        FROM mdl_label a
        UNION
        SELECT a.id, a.name AS activityname, 'url' AS activitytype, a.intro AS summary
        FROM mdl_url a
        UNION
        SELECT a.id, a.name AS activityname, 'page' AS activitytype, a.intro AS summary
        FROM mdl_page a
        UNION
        SELECT a.id, a.name AS activityname, 'quiz' AS activitytype, a.intro AS summary
        FROM mdl_quiz a
        UNION
        SELECT a.id, a.name AS activityname, 'data' AS activitytype, a.intro AS summary
        FROM mdl_data a
        UNION
        SELECT a.id, a.name AS activityname, 'assign' AS activitytype, a.intro AS summary
        FROM mdl_assign a
        UNION
        SELECT a.id, a.name AS activityname, 'folder' AS activitytype, a.intro AS summary
        FROM mdl_folder a
        UNION
        SELECT a.id, a.name AS activityname, 'resource' AS activitytype, a.intro AS summary
        FROM mdl_resource a
        UNION
        SELECT a.id, a.name AS activityname, 'lesson' AS activitytype, a.intro AS summary
        FROM mdl_lesson a
        UNION
        SELECT a.id, a.name AS activityname, 'feedback' AS activitytype, a.intro AS summary
        FROM mdl_feedback a
        UNION
        SELECT a.id, a.name AS activityname, 'bigbluebuttonbn' AS activitytype, a.intro AS summary
        FROM mdl_bigbluebuttonbn a
        UNION
        SELECT a.id, a.name AS activityname, 'smartchfolder' AS activitytype, a.intro AS summary
        FROM mdl_smartchfolder a
        UNION
        SELECT a.id, a.name AS activityname, 'book' AS activitytype, a.intro AS summary
        FROM mdl_book a
        UNION
        SELECT a.id, a.name AS activityname, 'face2face' AS activitytype, a.intro AS summary
        FROM mdl_face2face a
    ) activity ON activity.id = cm.instance AND activity.activitytype = m.name
    WHERE cm.deletioninprogress = 0 AND c.id = " . $courseid, null);

    // $coursemodules = get_course_mods($courseid);
    // $results = array();
    // if ($coursemodules) {
    //     foreach ($coursemodules as $coursemodule) {
    //         $result = $DB->get_record($coursemodule->modname, array('id' => $coursemodule->instance));
    //         // $result[$course_mod->id] = $course_mod;
    //         array_push($results, $result);
    //     }
    // }
    return $results;
}

function getCourseActivitiesRapport($courseid)
{
    global $DB;
    $results = $DB->get_records_sql("SELECT DISTINCT cm.id as id, activity.summary as summary,
    activity.activityname, c.id AS courseid, c.fullname AS coursename,
    cm.instance AS activityid, m.id as activitytypeid, m.name AS activitytype, cm.section as moduleid
    FROM mdl_course_modules cm
    JOIN mdl_course c ON c.id = cm.course
    JOIN mdl_modules m ON m.id = cm.module
    LEFT JOIN (
        SELECT a.id, a.name AS activityname, 'scorm' AS activitytype, a.intro AS summary
        FROM mdl_scorm a
        UNION
        SELECT a.id, a.name AS activityname, 'forum' AS activitytype, a.intro AS summary
        FROM mdl_forum a
        UNION
        SELECT a.id, a.name AS activityname, 'label' AS activitytype, a.intro AS summary
        FROM mdl_label a
        UNION
        SELECT a.id, a.name AS activityname, 'url' AS activitytype, a.intro AS summary
        FROM mdl_url a
        UNION
        SELECT a.id, a.name AS activityname, 'page' AS activitytype, a.intro AS summary
        FROM mdl_page a
        UNION
        SELECT a.id, a.name AS activityname, 'quiz' AS activitytype, a.intro AS summary
        FROM mdl_quiz a
        UNION
        SELECT a.id, a.name AS activityname, 'data' AS activitytype, a.intro AS summary
        FROM mdl_data a
        UNION
        SELECT a.id, a.name AS activityname, 'assign' AS activitytype, a.intro AS summary
        FROM mdl_assign a
        UNION
        SELECT a.id, a.name AS activityname, 'folder' AS activitytype, a.intro AS summary
        FROM mdl_folder a
        UNION
        SELECT a.id, a.name AS activityname, 'resource' AS activitytype, a.intro AS summary
        FROM mdl_resource a
        UNION
        SELECT a.id, a.name AS activityname, 'lesson' AS activitytype, a.intro AS summary
        FROM mdl_lesson a
        UNION
        SELECT a.id, a.name AS activityname, 'feedback' AS activitytype, a.intro AS summary
        FROM mdl_feedback a
        UNION
        SELECT a.id, a.name AS activityname, 'bigbluebuttonbn' AS activitytype, a.intro AS summary
        FROM mdl_bigbluebuttonbn a
        UNION
        SELECT a.id, a.name AS activityname, 'book' AS activitytype, a.intro AS summary
        FROM mdl_book a
        UNION
        SELECT a.id, a.name AS activityname, 'face2face' AS activitytype, a.intro AS summary
        FROM mdl_face2face a
    ) activity ON activity.id = cm.instance AND activity.activitytype = m.name
    WHERE c.id = " . $courseid, null);

    // $coursemodules = get_course_mods($courseid);
    // $results = array();
    // if ($coursemodules) {
    //     foreach ($coursemodules as $coursemodule) {
    //         $result = $DB->get_record($coursemodule->modname, array('id' => $coursemodule->instance));
    //         // $result[$course_mod->id] = $course_mod;
    //         array_push($results, $result);
    //     }
    // }
    return $results;
}


//compte le nombre d'activité d'une section
function countSectionActivities($sectionid, $courseid)
{
    // var_dump($courseid);
    // global $DB;
    // $results = $DB->get_records_sql('SELECT COUNT(*) count
    // FROM mdl_course_modules cm
    // JOIN mdl_course c ON c.id = cm.course
    // JOIN mdl_modules m ON m.id = cm.module
    // WHERE c.id = ' . $courseid . ' AND cm.id = ' . $sectionid, null);

    // return reset($results)->count;
}

function getSectionActivity($activityid)
{
    global $DB;
    $results = $DB->get_records_sql("SELECT cm.id as id, activity.summary as summary,
    activity.activityname, c.id AS courseid, c.fullname AS coursename,
    cm.instance AS activityid, m.id as activitytypeid, m.name AS activitytype, cm.section as moduleid
    FROM mdl_course_modules cm
    JOIN mdl_course c ON c.id = cm.course
    JOIN mdl_modules m ON m.id = cm.module
    LEFT JOIN (
        SELECT a.id, a.name AS activityname, 'scorm' AS activitytype, a.intro AS summary
        FROM mdl_scorm a
        UNION
        SELECT a.id, a.name AS activityname, 'forum' AS activitytype, a.intro AS summary
        FROM mdl_forum a
        UNION
        SELECT a.id, a.name AS activityname, 'label' AS activitytype, a.intro AS summary
        FROM mdl_label a
        UNION
        SELECT a.id, a.name AS activityname, 'url' AS activitytype, a.intro AS summary
        FROM mdl_url a
        UNION
        SELECT a.id, a.name AS activityname, 'page' AS activitytype, a.intro AS summary
        FROM mdl_page a
        UNION
        SELECT a.id, a.name AS activityname, 'quiz' AS activitytype, a.intro AS summary
        FROM mdl_quiz a
        UNION
        SELECT a.id, a.name AS activityname, 'data' AS activitytype, a.intro AS summary
        FROM mdl_data a
        UNION
        SELECT a.id, a.name AS activityname, 'assign' AS activitytype, a.intro AS summary
        FROM mdl_assign a
        UNION
        SELECT a.id, a.name AS activityname, 'folder' AS activitytype, a.intro AS summary
        FROM mdl_folder a
        UNION
        SELECT a.id, a.name AS activityname, 'resource' AS activitytype, a.intro AS summary
        FROM mdl_resource a
        UNION
        SELECT a.id, a.name AS activityname, 'lesson' AS activitytype, a.intro AS summary
        FROM mdl_lesson a
        UNION
        SELECT a.id, a.name AS activityname, 'feedback' AS activitytype, a.intro AS summary
        FROM mdl_feedback a
        UNION
        SELECT a.id, a.name AS activityname, 'h5pactivity' AS activitytype, a.intro AS summary
        FROM mdl_h5pactivity a
        UNION
        SELECT a.id, a.name AS activityname, 'bigbluebuttonbn' AS activitytype, a.intro AS summary
        FROM mdl_bigbluebuttonbn a
        UNION
        SELECT a.id, a.name AS activityname, 'book' AS activitytype, a.intro AS summary
        FROM mdl_book a
        UNION
        SELECT a.id, a.name AS activityname, 'smartchfolder' AS activitytype, a.intro AS summary
        FROM mdl_smartchfolder a
        UNION
        SELECT a.id, a.name AS activityname, 'face2face' AS activitytype, a.intro AS summary
        FROM mdl_face2face a
    ) activity ON activity.id = cm.instance AND activity.activitytype = m.name
    WHERE cm.deletioninprogress = 0 AND cm.id = " . $activityid, null);

    // $coursemodules = get_course_mods($courseid);
    // $results = array();
    // if ($coursemodules) {
    //     foreach ($coursemodules as $coursemodule) {
    //         $result = $DB->get_record($coursemodule->modname, array('id' => $coursemodule->instance));
    //         // $result[$course_mod->id] = $course_mod;
    //         array_push($results, $result);
    //     }
    // }
    return reset($results);
}

function getSectionFromActivity($activityid)
{
    global $DB;
    $act = $DB->get_record('course_modules', ['id' => $activityid]);
    return $act->section;
}

function downloadCSVTeam($groupid)
{

    global $DB;

    //on va chercher le cours
    $querycourse = 'SELECT c.*, g.name as groupname
        FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        WHERE g.id = ' . $groupid;

    $courseresult = $DB->get_records_sql($querycourse, null);

    $course = reset($courseresult);

    $session = $DB->get_record('smartch_session', ['groupid' => $groupid]);

    $textsession = "";
    if ($session) {
        $textsession = 'Session du ' . userdate($session->startdate, get_string('strftimedate')) . ' au ' . userdate($session->enddate, get_string('strftimedate') . '');
    }

    $textdate = 'Extraction du rapport le ' . userdate(Time(), get_string('strftimedate')) . '';

    $data = [
        [$course->fullname, $textsession, $textdate]
    ];

    //on va chercher les membres du groupe
    $querygroupmembers = 'SELECT u.id, u.firstname, u.lastname, u.email, r.shortname, r.id as roleid 
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


    array_push($data, ""); //saut de ligne

    $headertable = ['Nom Prénom de l\'apprenant', 'Adresse courriel', 'N° individu', '% de progression totale', 'Temps total passé'];

    foreach ($sections as $section) {
        //on compte le nombre de matière
        $tableau = explode(',', $section->sequence);
        $nbmodule = 0;
        foreach ($tableau as $moduleid) {
            $activity = null;
            //on cherche dans le tableau des activités
            foreach ($activities as $activityy) {
                if ($activityy->id == $moduleid) {
                    $activity = $activityy;
                    break; // Sortir de la boucle dès que l'élément est trouvé
                }
            }
            if ($activity) {
                if ($activity->activityname && $activity->activitytype != "folder") {
                    $nbmodule++;
                }
            }
        }
        $sectionname = $section->name;
        if ($sectionname == "") {
            $sectionname = "Généralités";
        }
        $textmodule = $sectionname;
        array_push($headertable, $textmodule);
        $nbmodule--;
        for ($i = 0; $i < $nbmodule; $i++) {
            array_push($headertable, "");
        }
    }

    array_push($data, $headertable);


    $sectiontable = ['', '', '', '', ''];
    foreach ($sections as $section) {

        if ($session) {
            //on va chercher le nombre de planning dans la section disponible
            $sectionsplannings = getSectionPlannings($course->id, $session->id, $section->id);
            $totalsectionsplannings = count($sectionsplannings);
        }

        //on compte le nombre de matière
        $tableau = explode(',', $section->sequence);
        foreach ($tableau as $moduleid) {
            //on cherche dans le tableau des activités
            foreach ($activities as $activityy) {
                if ($activityy->id == $moduleid) {
                    $activity = $activityy;
                    break; // Sortir de la boucle dès que l'élément est trouvé
                }
            }
            if ($activity->activitytype == 'face2face') {
                //On va chercher le nombre de planning dans cette section
                if ($totalsectionsplannings > 0) {
                    $totalsectionsplannings--;
                    array_push($sectiontable, $activity->activityname);
                }
            } else if ($activity->activityname && $activity->activitytype != "folder") {
                array_push($sectiontable, $activity->activityname);
            }
        }
    }
    array_push($data, $sectiontable);



    foreach ($groupmembers as $groupmember) {

        $membertable = [];

        $progression = getCourseProgression($groupmember->id, $course->id) . '%';
        $timespent = getTimeSpentOnCourse($groupmember->id, $course->id);



        array_push($membertable, $groupmember->firstname . ' ' . $groupmember->lastname);
        array_push($membertable, $groupmember->email);
        array_push($membertable, $groupmember->id);
        array_push($membertable, $progression);
        array_push($membertable, $timespent);



        foreach ($sections as $section) {

            if ($session) {
                $sectionsplannings = getSectionPlannings($course->id, $session->id, $section->id);
                $totalsectionsplannings = count($sectionsplannings);
            }

            //on compte le nombre de matière
            $tableau = explode(',', $section->sequence);
            foreach ($tableau as $moduleid) {
                //on cherche dans le tableau des activités
                foreach ($activities as $activityy) {
                    if ($activityy->id == $moduleid) {
                        $activity = $activityy;
                        break; // Sortir de la boucle dès que l'élément est trouvé
                    }
                }
                if ($activity->activitytype == 'face2face') {
                    if ($totalsectionsplannings > 0) {
                        //on va chercher le planning correspondant
                        $completion = getPlanningCompletion($course->id, $session->id, $section->id);
                        array_push($membertable, $completion);
                        //si il reste des plannings dans cette section à mettre
                        $totalsectionsplannings--;
                    }
                } else if ($activity->activityname && $activity->activitytype != "folder") {
                    $completion = getActivityCompletionStatusRapport($moduleid, $groupmember->id);
                    array_push($membertable, $completion);
                }
            }
        }

        array_push($data, $membertable);
    }

    //on va chercher les logs du groupe
    $logs = $DB->get_records_sql('SELECT sa.id, sa.timespent FROM mdl_smartch_activity_log sa
JOIN mdl_groups_members gm ON gm.userid = sa.userid
WHERE sa.course = ' . $course->id . ' AND gm.groupid =  ' . $groupid, null);

    $timetotal = 0;
    foreach ($logs as $log) {
        $timetotal += $log->timespent;
    }

    $totaltimespent = convert_to_string_time($timetotal);


    $timegrouptable = ['PROGRESSION GÉNÉRALE', '', '', getTeamProgress($course->id, $groupid)[0], $totaltimespent];
    array_push($data, $timegrouptable);

    $legendtable = ['Terminé : X', 'Pas terminé : -'];
    array_push($data, $legendtable);



    // Définir les en-têtes pour le téléchargement
    header('Content-Type: text/csv');
    //nom_session - date d'extraction
    header('Content-Disposition: attachment; filename="' . $course->groupname . '-' . date("d-m-Y") . '.csv"');

    // Ouvrir le flux de sortie
    $output = fopen('php://output', 'w');

    // Parcourir les données et les écrire au format CSV
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // Fermer le flux de sortie
    fclose($output);
    exit();
}

function downloadXLSTeam($groupid)
{

    global $DB;

    //on va chercher le cours
    $querycourse = 'SELECT c.*, g.name as groupname
        FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        WHERE g.id = ' . $groupid;

    $courseresult = $DB->get_records_sql($querycourse, null);

    $course = reset($courseresult);

    $session = $DB->get_record('smartch_session', ['groupid' => $groupid]);

    $textsession = "";
    if ($session) {
        $textsession = 'Session du ' . userdate($session->startdate, get_string('strftimedate')) . ' au ' . userdate($session->enddate, get_string('strftimedate') . '');
    }

    $textdate = 'Extraction du rapport le ' . userdate(Time(), get_string('strftimedate')) . '';

    $data = [
        [$course->fullname, $textsession, $textdate]
    ];

    //on va chercher les membres du groupe
    $querygroupmembers = 'SELECT u.id, u.firstname, u.lastname, u.email, r.shortname, r.id as roleid 
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


    array_push($data, ""); //saut de ligne

    $headertable = ['Nom Prénom de l\'apprenant', 'Adresse courriel', 'N° individu', '% de progression totale', 'Temps total passé'];

    foreach ($sections as $section) {
        //on compte le nombre de matière
        $tableau = explode(',', $section->sequence);
        $nbmodule = 0;
        foreach ($tableau as $moduleid) {
            $activity = null;
            //on cherche dans le tableau des activités
            foreach ($activities as $activityy) {
                if ($activityy->id == $moduleid) {
                    $activity = $activityy;
                    break; // Sortir de la boucle dès que l'élément est trouvé
                }
            }
            if ($activity) {
                if ($activity->activityname && $activity->activitytype != "folder") {
                    $nbmodule++;
                }
            }
        }
        $sectionname = $section->name;
        if ($sectionname == "") {
            $sectionname = "Généralités";
        }
        $textmodule = $sectionname;
        array_push($headertable, $textmodule);
        $nbmodule--;
        for ($i = 0; $i < $nbmodule; $i++) {
            array_push($headertable, "");
        }
    }

    array_push($data, $headertable);


    $sectiontable = ['', '', '', '', ''];
    foreach ($sections as $section) {

        if ($session) {
            //on va chercher le nombre de planning dans la section disponible
            $sectionsplannings = getSectionPlannings($course->id, $session->id, $section->id);
            $totalsectionsplannings = count($sectionsplannings);
        }

        //on compte le nombre de matière
        $tableau = explode(',', $section->sequence);
        foreach ($tableau as $moduleid) {
            //on cherche dans le tableau des activités
            foreach ($activities as $activityy) {
                if ($activityy->id == $moduleid) {
                    $activity = $activityy;
                    break; // Sortir de la boucle dès que l'élément est trouvé
                }
            }
            if ($activity->activitytype == 'face2face') {
                //On va chercher le nombre de planning dans cette section
                if ($totalsectionsplannings > 0) {
                    $totalsectionsplannings--;
                    array_push($sectiontable, $activity->activityname);
                }
            } else if ($activity->activityname && $activity->activitytype != "folder") {
                array_push($sectiontable, $activity->activityname);
            }
        }
    }
    array_push($data, $sectiontable);



    foreach ($groupmembers as $groupmember) {

        $membertable = [];

        $progression = getCourseProgression($groupmember->id, $course->id) . '%';
        $timespent = getTimeSpentOnCourse($groupmember->id, $course->id);



        array_push($membertable, $groupmember->firstname . ' ' . $groupmember->lastname);
        array_push($membertable, $groupmember->email);
        array_push($membertable, $groupmember->id);
        array_push($membertable, $progression);
        array_push($membertable, $timespent);



        foreach ($sections as $section) {

            if ($session) {
                $sectionsplannings = getSectionPlannings($course->id, $session->id, $section->id);
                $totalsectionsplannings = count($sectionsplannings);
            }


            //on compte le nombre de matière
            $tableau = explode(',', $section->sequence);
            foreach ($tableau as $moduleid) {
                //on cherche dans le tableau des activités
                foreach ($activities as $activityy) {
                    if ($activityy->id == $moduleid) {
                        $activity = $activityy;
                        break; // Sortir de la boucle dès que l'élément est trouvé
                    }
                }
                if ($activity->activitytype == 'face2face') {
                    if ($totalsectionsplannings > 0) {
                        //on va chercher le planning correspondant
                        $completion = getPlanningCompletion($course->id, $session->id, $section->id);
                        array_push($membertable, $completion);
                        //si il reste des plannings dans cette section à mettre
                        $totalsectionsplannings--;
                    }
                } else if ($activity->activityname && $activity->activitytype != "folder") {
                    $completion = getActivityCompletionStatusRapport($moduleid, $groupmember->id);
                    array_push($membertable, $completion);
                }
            }
        }

        array_push($data, $membertable);
    }

    //on va chercher les logs du groupe
    $logs = $DB->get_records_sql('SELECT sa.id, sa.timespent FROM mdl_smartch_activity_log sa
JOIN mdl_groups_members gm ON gm.userid = sa.userid
WHERE sa.course = ' . $course->id . ' AND gm.groupid =  ' . $groupid, null);

    $timetotal = 0;
    foreach ($logs as $log) {
        $timetotal += $log->timespent;
    }

    $totaltimespent = convert_to_string_time($timetotal);


    $timegrouptable = ['PROGRESSION GÉNÉRALE', '', '', getTeamProgress($course->id, $groupid)[0], $totaltimespent];
    array_push($data, $timegrouptable);

    $legendtable = ['Terminé : X', 'Pas terminé : -'];
    array_push($data, $legendtable);

    // // Créer un nouvel objet Spreadsheet
    // $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    // $sheet = $spreadsheet->getActiveSheet();

    // // Remplir les données dans le Spreadsheet
    // $rowNumber = 1;
    // foreach ($data as $row) {
    //     $column = 'A';
    //     foreach ($row as $cell) {
    //         $sheet->setCellValue($column++ . $rowNumber, $cell);
    //     }
    //     $rowNumber++;
    // }

    // // Préparer le téléchargement
    // header('Content-Type: application/vnd.ms-excel');
    // header('Content-Disposition: attachment; filename="' . $course->groupname . '-' . date("d-m-Y") . '.xls"');

    // // Créer un écrivain et sauvegarder dans PHP output
    // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
    // $writer->save('php://output');
    // exit();

    // Créer un nouveau document
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Ajouter des données
    // $sheet->setCellValue('A1', 'Hello World !');

    // Remplir les données dans le Spreadsheet
    $rowNumber = 1;
    foreach ($data as $row) {
        $column = 'A';
        foreach ($row as $cell) {
            $sheet->setCellValue($column++ . $rowNumber, $cell);
        }
        $rowNumber++;
    }

    // Écrire dans un fichier .xlsx
    $writer = new Xlsx($spreadsheet);
    $fileName = $course->groupname . '-' . date("d-m-Y");

    // En-têtes pour le téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '.xlsx' . '"');

    // Envoyer le fichier au navigateur
    $writer->save('php://output');
    exit;
}

function getDataTeamGrades($course, $groupid){

    global $DB;
    $session = $DB->get_record('smartch_session', ['groupid' => $groupid]);

    $textsession = "";
    if ($session) {
        $textsession = 'Session du ' . userdate($session->startdate, get_string('strftimedate')) . ' au ' . userdate($session->enddate, get_string('strftimedate') . '');
    }

    $textdate = 'Extraction du carnet de note le ' . userdate(Time(), get_string('strftimedate')) . '';

    $data = [
        [$course->fullname, $textsession, $textdate]
    ];

    //on va chercher les membres du groupe
    $querygroupmembers = 'SELECT u.id, u.firstname, u.lastname, u.email, r.shortname, r.id as roleid 
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


    // array_push($data, ""); //saut de ligne

    $headertable = ['Nom Prénom de l\'apprenant', 'Adresse courriel', 'N° individu'];

    foreach ($sections as $section) {
        //on compte le nombre de matière
        $tableau = explode(',', $section->sequence);
        $nbmodule = 0;
        foreach ($tableau as $moduleid) {
            $activity = null;
            //on cherche dans le tableau des activités
            foreach ($activities as $activityy) {
                if ($activityy->id == $moduleid) {
                    $activity = $activityy;
                    break; // Sortir de la boucle dès que l'élément est trouvé
                }
            }
            if ($activity) {
                if ($activity->activityname && $activity->activitytype == "quiz") {
                    $nbmodule++;
                }
            }
        }
        // $sectionname = $section->name;
        // if ($sectionname == "") {
        //     $sectionname = "Généralités";
        // }
        // $textmodule = $sectionname;
        // array_push($headertable, $textmodule);
        // $nbmodule--;
        // for ($i = 0; $i < $nbmodule; $i++) {
        //     array_push($headertable, "");
        // }
    }

    array_push($data, $headertable);


    $sectiontable = ['', '', ''];
    foreach ($sections as $section) {


        //on compte le nombre de matière
        $tableau = explode(',', $section->sequence);
        foreach ($tableau as $moduleid) {
            //on cherche dans le tableau des activités
            foreach ($activities as $activityy) {
                if ($activityy->id == $moduleid) {
                    $activity = $activityy;
                    break; // Sortir de la boucle dès que l'élément est trouvé
                }
            }
            if ($activity->activityname && $activity->activitytype == "quiz") {
                array_push($sectiontable, $activity->activityname);
            }
        }
    }
    array_push($data, $sectiontable);

    foreach ($groupmembers as $groupmember) {

        $membertable = [];

        // $progression = getCourseProgression($groupmember->id, $course->id) . '%';
        // $timespent = getTimeSpentOnCourse($groupmember->id, $course->id);

        array_push($membertable, $groupmember->firstname . ' ' . $groupmember->lastname);
        array_push($membertable, $groupmember->email);
        array_push($membertable, $groupmember->id);
        // array_push($membertable, $progression);
        // array_push($membertable, $timespent);



        foreach ($sections as $section) {

            if ($session) {
                $sectionsplannings = getSectionPlannings($course->id, $session->id, $section->id);
                $totalsectionsplannings = count($sectionsplannings);
            }

            //on compte le nombre de matière
            $tableau = explode(',', $section->sequence);
            foreach ($tableau as $moduleid) {
                //on cherche dans le tableau des activités
                foreach ($activities as $activityy) {
                    if ($activityy->id == $moduleid) {
                        $activity = $activityy;
                        break; // Sortir de la boucle dès que l'élément est trouvé
                    }
                }
                if ($activity->activityname && $activity->activitytype == "quiz") {
                    $grade = getModuleGrade($groupmember->id, $activity->id);
                    array_push($membertable, $grade);
                }
            }
        }

        array_push($data, $membertable);
    }
    return $data;
}
function downloadCSVTeamGrade($groupid)
{

    global $DB;

    //on va chercher le cours
    $querycourse = 'SELECT c.*, g.name as groupname
        FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        WHERE g.id = ' . $groupid;

    $courseresult = $DB->get_records_sql($querycourse, null);

    $course = reset($courseresult);

    //on va chercher la data en tableau
    $data = getDataTeamGrades($course, $groupid);


    // Définir les en-têtes pour le téléchargement
    header('Content-Type: text/csv');
    //nom_session - date d'extraction
    header('Content-Disposition: attachment; filename="' . $course->groupname . '-' . date("d-m-Y") . '.csv"');

    // Ouvrir le flux de sortie
    $output = fopen('php://output', 'w');

    // Parcourir les données et les écrire au format CSV
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // Fermer le flux de sortie
    fclose($output);
    exit();
}

function downloadXLSTeamGrade($groupid)
{

    global $DB;

    //on va chercher le cours
    $querycourse = 'SELECT c.*, g.name as groupname
        FROM mdl_groups g
        JOIN mdl_course c ON c.id = g.courseid
        WHERE g.id = ' . $groupid;

    $courseresult = $DB->get_records_sql($querycourse, null);

    $course = reset($courseresult);

    //on va chercher la data en tableau
    $data = getDataTeamGrades($course, $groupid);

    // Créer un nouveau document
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Remplir les données dans le Spreadsheet
    $rowNumber = 1;
    foreach ($data as $row) {
        $column = 'A';
        foreach ($row as $cell) {
            $sheet->setCellValue($column++ . $rowNumber, $cell);
        }
        $rowNumber++;
    }

    // Écrire dans un fichier .xlsx
    $writer = new Xlsx($spreadsheet);
    $fileName = $course->groupname . '-' . date("d-m-Y");

    // En-têtes pour le téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '.xlsx' . '"');

    // Envoyer le fichier au navigateur
    $writer->save('php://output');
    exit;
}

function getCourseType($courseid){
    global $DB;
    $coursetype = "";
    $coursetypeobject = $DB->get_record_sql('
    SELECT cd.value 
    FROM mdl_customfield_data cd
    JOIN mdl_customfield_field cf ON cf.id = cd.fieldid
    WHERE cd.instanceid = ' . $courseid . ' AND cf.shortname = "diplome"', null);
    if ($coursetypeobject) {
        $coursetype = $coursetypeobject->value;
    }
    return $coursetype;
}

function getActualUserSessions($courseid, $userid = null){
    global $DB, $USER;
    if(!$userid){
        $userid = $USER->id;
    }
    $actualdate = time();
    $allsessions = $DB->get_records_sql('SELECT DISTINCT ss.id, ss.startdate, ss.enddate
    FROM mdl_groups g
    JOIN mdl_groups_members gm ON gm.groupid = g.id
    JOIN mdl_smartch_session ss ON ss.groupid = g.id
    WHERE gm.userid = ' . $userid . ' 
    AND g.courseid = ' . $courseid . '
    AND ss.startdate < ' . $actualdate . '
    AND ss.enddate > ' . $actualdate, null);

    return $allsessions;
}
function getUserSessions($courseid, $userid = null){
    global $DB, $USER;
    if(!$userid){
        $userid = $USER->id;
    }
    $actualdate = time();
    $allsessions = $DB->get_records_sql('SELECT DISTINCT ss.id, ss.startdate, ss.enddate
    FROM mdl_groups g
    JOIN mdl_groups_members gm ON gm.groupid = g.id
    JOIN mdl_smartch_session ss ON ss.groupid = g.id
    WHERE gm.userid = ' . $userid . ' 
    AND g.courseid = ' . $courseid, null);

    return $allsessions;
}

function getUserQuizAttempts($moduleid, $userid = null){
    global $DB, $USER;
    if(!$userid){
        $userid = $USER->id;
    }
    // $query = 'SELECT gi.courseid, g.timemodified, g.rawgrade, g.rawgrademax, cm.id AS moduleid, gi.itemname AS modulename, gi.itemmodule
    // FROM mdl_grade_items gi
    // JOIN mdl_grade_grades g ON gi.id = g.itemid
    // JOIN mdl_course_modules cm ON cm.course = gi.courseid AND cm.instance = gi.iteminstance
    // JOIN mdl_modules md ON cm.module = md.id AND md.name = gi.itemmodule
    // WHERE gi.itemtype = "mod" AND g.userid = ' . $userid . ' AND cm.id = ' . $moduleid . '
    // ORDER BY g.timemodified';
    $query = 'SELECT 
    qa.id AS attemptid, 
    gi.courseid, 
    qa.timefinish AS timemodified, 
    qa.sumgrades AS rawgrade, 
    q.grade AS rawgrademax, 
    cm.id AS moduleid, 
    gi.itemname AS modulename, 
    gi.itemmodule, 
    qa.attempt
FROM 
    mdl_quiz_attempts qa
JOIN 
    mdl_quiz q ON qa.quiz = q.id
JOIN 
    mdl_course_modules cm ON cm.instance = q.id
JOIN 
    mdl_modules md ON cm.module = md.id AND md.name = "quiz"
JOIN 
    mdl_grade_items gi ON gi.iteminstance = q.id AND gi.itemmodule = "quiz"
WHERE 
    qa.userid = ' . $userid . ' 
    AND cm.id = ' . $moduleid . '
ORDER BY 
    qa.timefinish';

    $attempts = $DB->get_records_sql($query, null);
    return $attempts;
}

function checkUserCanPassAttempt($moduleid, $courseid, $userid){
    $coursetype = getCourseType($courseid);
    if($coursetype == "Certifications Fédérales"){
        
        $useractualsessions = [];
        $useractualsessions = [];
        $userattempts = [];
        //on regarde le nombre de session de l'apprenant
        $usertotalsessions = getUserSessions($courseid, $userid);
        // var_dump($usertotalsessions);
        //on regarde le nombre de session actuelle de l'apprenant
        $useractualsessions = getActualUserSessions($courseid, $userid);
        // var_dump($useractualsessions);
        //on regarde le nombre de tentative de l'apprenant
        $userattempts = getUserQuizAttempts($moduleid, $userid);
        // var_dump($userattempts);

        //si il y a plus ou autant de tentative que de session actuelle
        if(count($usertotalsessions) > count($userattempts)){
            //si il n'a pas de session en cours
            if(count($useractualsessions) == 0){
                return false;
            } 
            return true;
        } 
        return false;
    }
}

function generateGUID()
{
    if (function_exists('com_create_guid')) {
        // Utilise com_create_guid() sur les systèmes Windows
        return trim(com_create_guid(), '{}');
    } else {
        // Génère un GUID de manière aléatoire sur les autres systèmes
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

function convert_to_string_time($time)
{
    $stringtime = "";

    $h = floor($time / 3600);
    $rh = $time % 3600;
    $m = floor($rh / 60);
    $s = $rh % 60;

    if ($h != 0) {
        $stringtime .= $h . "h";
    }
    if ($m != 0 && $h < 0) {
        $stringtime .= $m . "m";
    }
    if ($s != 0 && $m < 0 && $h < 0) {
        if ($s != 0) {
            $stringtime .= $s . "s";
        }
    }

    if ($stringtime == "") {
        $stringtime = "0h";
    }
    return $stringtime;
}

function getTimeSpentOnCourse($userid, $courseid)
{
    global $DB;
    //on va chercher les logs de l'utilisateur
    $logs = $DB->get_records_sql('SELECT * FROM mdl_smartch_activity_log WHERE course = ' . $courseid . ' AND userid = ' . $userid, null);

    $timetotal = 0;
    foreach ($logs as $log) {
        $timetotal += $log->timespent;
    }

    return convert_to_string_time($timetotal);
}

function getActivityCompletionStatus($activityid, $userid = null, $type = null)
{
    global $DB, $USER;

    if ($userid) {
        $user = $DB->get_record('user', ['id' => $userid]);
    } else {
        $user = $USER;
    }

    if ($user && $activityid) {
        //on va chercher si il y a un log
        $logs = $DB->get_records_sql('SELECT * FROM mdl_smartch_activity_log WHERE activity = ' . $activityid . ' AND userid = ' . $user->id, null);

        //on va chercher si il y a un score
        $grade = getModuleGrade($user->id, $activityid);

        $query = 'SELECT cmc.id, cmc.completionstate
        FROM mdl_course_modules_completion cmc
        WHERE cmc.userid = ' . $user->id . ' AND cmc.coursemoduleid = ' . $activityid;
        $arrobject = $DB->get_record_sql($query, null);
        if ($arrobject) {
            if ($arrobject->completionstate >= 1) {
                // L'activité est complétée
                return '<div style="background:#3CAFE3" class="smartch_pastille">Terminé</div>';
            } else {
                return '<div style="background:#C1C1C1;" class="smartch_pastille">Pas terminé</div>';
            }
        } else {
            return '<div style="background:#C1C1C1;" class="smartch_pastille">Pas terminé</div>';
        }

        //OLD COMPLETION
        // if ($arrobject) {
        //     if ($arrobject->completionstate >= 1) {
        //         // L'activité est complétée
        //         return '<div style="background:#3CAFE3" class="smartch_pastille">Terminé</div>';
        //     } else if ($grade) {
        //         //il y a un score
        //         // return '<div style="background:#00315a;" class="smartch_pastille">En cours (' . $grade . '%)</div>';
        //         return '<div style="background:#00315a;" class="smartch_pastille">En cours</div>';
        //     } else {
        //         if (count($logs) > 0) {
        //             return '<div style="background:#00315a;" class="smartch_pastille">En cours</div>';
        //             // return '<div style="background:#00315a;" class="smartch_pastille">En cours (' . count($logs) . ')</div>';
        //         } else {
        //             return '<div style="background:#C1C1C1;" class="smartch_pastille">Non démarré</div>';
        //         }
        //     }
        // } else {
        //     if (count($logs) > 0 && $type = "resource") {
        //         return '<div style="background:#3CAFE3;" class="smartch_pastille">Terminé</div>';
        //     } else if (count($logs) > 0) {
        //         return '<div style="background:#00315a;" class="smartch_pastille">En cours</div>';
        //     } else {
        //         return '<div style="background:#C1C1C1;" class="smartch_pastille">Non démarré</div>';
        //     }
        // }
    } else {
        return '';
    }
}

function getActivityCompletionStatusRapport($activityid, $userid = null)
{
    global $DB, $USER;

    if ($userid) {
        $user = $DB->get_record('user', ['id' => $userid]);
    } else {
        $user = $USER;
    }

    if ($user && $activityid) {
        //on va chercher si il y a un log
        // $logs = $DB->get_records_sql('SELECT * FROM mdl_smartch_activity_log WHERE activity = ' . $activityid . ' AND userid = ' . $user->id, null);

        //on va chercher si il y a un score
        // $grade = getModuleGrade($user->id, $activityid);

        $query = 'SELECT cmc.id, cmc.completionstate
        FROM mdl_course_modules_completion cmc
        WHERE cmc.userid = ' . $user->id . ' AND cmc.coursemoduleid = ' . $activityid;
        $arr = $DB->get_records_sql($query, null);
        $arrobject = reset($arr);
        if ($arrobject) {
            if ($arrobject->completionstate >= 1) {
                // L'activité est complétée
                return 'X';
            } else {
                return '-';
            }
        } else {
            return '-';
        }
        // if ($arrobject) {
        //     if ($arrobject->completionstate == 1) {
        //         // L'activité est complétée
        //         // return '<input type="checkbox" checked/>';
        //         return 'X';
        //     } else if ($grade) {
        //         //il y a un score
        //         return 'En cours (' . $grade . '%)</div>';
        //     } else {
        //         if (count($logs) > 0) {
        //             // return '<input style="border:1px solid dashed" type="checkbox" checked/>';
        //             return '/';
        //         } else {
        //             // return '<input type="checkbox" />';
        //             return '-';
        //         }
        //     }
        // } else {
        //     if (count($logs) > 0) {
        //         // return '<input style="border:1px solid dashed" type="checkbox" checked/>';
        //         return '/';
        //     } else {
        //         // return '<input type="checkbox" />';
        //         return '-';
        //     }
        // }
    } else {
        return '';
    }
}
function getModuleGrade($userid, $activityid)
{
    global $DB;

    $query = 'SELECT gi.courseid, g.rawgrade, g.rawgrademax, cm.id AS moduleid, gi.itemname AS modulename, gi.itemmodule
    FROM mdl_grade_items gi
    JOIN mdl_grade_grades g ON gi.id = g.itemid
    JOIN mdl_course_modules cm ON cm.course = gi.courseid AND cm.instance = gi.iteminstance
    JOIN mdl_modules md ON cm.module = md.id AND md.name = gi.itemmodule
    WHERE gi.itemtype = "mod" AND g.userid = ' . $userid . ' AND cm.id = ' . $activityid;

    $result = $DB->get_record_sql($query, null);

    if($result){
        //le score
        $grade = number_format($result->rawgrade, 2, '.', '');

        //le score max
        $rawgrademax = $result->rawgrademax;

        if(!empty($rawgrademax)){
            $score = $grade . '/' . number_format($rawgrademax, 2, '.', '');
        } else {
            $score = $grade;
        }
        
        return $score;
    } else{
        return "";
    }
}

function get_module_grade_by_user_scorm_V2($user_id, $activity_id)
{

    global $DB;

    $query = 'SELECT gi.courseid, g.rawgrade, cm.id AS moduleid, gi.itemname AS modulename, gi.itemmodule
        FROM mdl_grade_items gi
        INNER JOIN mdl_grade_grades g ON gi.id = g.itemid
        INNER JOIN mdl_course_modules cm ON cm.course = gi.courseid AND cm.instance = gi.iteminstance
        INNER JOIN mdl_modules md ON cm.module = md.id AND md.name = gi.itemmodule
        WHERE gi.itemtype = "mod" AND g.userid = ' . $user_id . ' AND cm.id = ' . $activity_id;

    $result = $DB->get_record_sql($query, null);

    if($result){
        //le score
        $grade = number_format($result->rawgrade, 2, '.', '');

        //le score max
        $rawgrademax = $result->rawgrademax;

        if(!empty($rawgrademax)){
            $score = $grade . '/' . number_format($rawgrademax, 2, '.', '');
        } else {
            $score = $grade;
        }
        
        return $score;
    } else{
        return null;
    }
    
}



function getCourseActivitiesStats($courseid)
{
    global $DB;
    $results = $DB->get_records_sql("SELECT cm.id as id, activity.summary as summary,
    activity.activityname, c.id AS courseid, c.fullname AS coursename,
    cm.instance AS activityid, m.id as activitytypeid, m.name AS activitytype, cm.section as moduleid
    FROM mdl_course_modules cm
    JOIN mdl_course c ON c.id = cm.course
    JOIN mdl_modules m ON m.id = cm.module
    LEFT JOIN (
        SELECT a.id, a.name AS activityname, 'scorm' AS activitytype, a.intro AS summary
        FROM mdl_scorm a
        UNION
        SELECT a.id, a.name AS activityname, 'forum' AS activitytype, a.intro AS summary
        FROM mdl_forum a
        UNION
        SELECT a.id, a.name AS activityname, 'label' AS activitytype, a.intro AS summary
        FROM mdl_label a
        UNION
        SELECT a.id, a.name AS activityname, 'url' AS activitytype, a.intro AS summary
        FROM mdl_url a
        UNION
        SELECT a.id, a.name AS activityname, 'page' AS activitytype, a.intro AS summary
        FROM mdl_page a
        UNION
        SELECT a.id, a.name AS activityname, 'quiz' AS activitytype, a.intro AS summary
        FROM mdl_quiz a
        UNION
        SELECT a.id, a.name AS activityname, 'data' AS activitytype, a.intro AS summary
        FROM mdl_data a
        UNION
        SELECT a.id, a.name AS activityname, 'assign' AS activitytype, a.intro AS summary
        FROM mdl_assign a
        UNION
        SELECT a.id, a.name AS activityname, 'folder' AS activitytype, a.intro AS summary
        FROM mdl_folder a
        UNION
        SELECT a.id, a.name AS activityname, 'resource' AS activitytype, a.intro AS summary
        FROM mdl_resource a
        UNION
        SELECT a.id, a.name AS activityname, 'lesson' AS activitytype, a.intro AS summary
        FROM mdl_lesson a
        UNION
        SELECT a.id, a.name AS activityname, 'feedback' AS activitytype, a.intro AS summary
        FROM mdl_feedback a
        UNION
        SELECT a.id, a.name AS activityname, 'bigbluebuttonbn' AS activitytype, a.intro AS summary
        FROM mdl_bigbluebuttonbn a
        UNION
        SELECT a.id, a.name AS activityname, 'book' AS activitytype, a.intro AS summary
        FROM mdl_book a
        UNION
        SELECT a.id, a.name AS activityname, 'face2face' AS activitytype, a.intro AS summary
        FROM mdl_face2face a
        
    ) activity ON activity.id = cm.instance AND activity.activitytype = m.name
    WHERE activity.activitytype != 'folder'
    AND activity.activitytype != 'face2face'
    AND activity.activitytype != 'forum'
    -- AND activity.activitytype != 'resource'
    -- AND activity.activitytype != 'page'
    -- AND activity.activitytype != 'quiz'
    -- AND activity.activitytype != 'feedback'
    -- AND activity.activitytype != 'scorm'
    -- AND activity.activitytype != 'label'
    -- AND activity.activitytype != 'book'
    -- AND activity.activitytype != 'assign'
    -- AND activity.activitytype != 'url'
    -- AND activity.activitytype != 'bigbluebuttonbn'
    -- AND activity.activitytype != 'lesson'
    AND c.id = " . $courseid, null);

    return $results;
}

function getCourseActivitiesPlanningStats($courseid)
{
    global $DB;
    $results = $DB->get_records_sql("SELECT cm.id as id, activity.summary as summary,
    activity.activityname, c.id AS courseid, c.fullname AS coursename,
    cm.instance AS activityid, m.id as activitytypeid, m.name AS activitytype, cm.section as moduleid
    FROM mdl_course_modules cm
    JOIN mdl_course c ON c.id = cm.course
    JOIN mdl_modules m ON m.id = cm.module
    LEFT JOIN (
        SELECT a.id, a.name AS activityname, 'scorm' AS activitytype, a.intro AS summary
        FROM mdl_scorm a
        UNION
        SELECT a.id, a.name AS activityname, 'forum' AS activitytype, a.intro AS summary
        FROM mdl_forum a
        UNION
        SELECT a.id, a.name AS activityname, 'label' AS activitytype, a.intro AS summary
        FROM mdl_label a
        UNION
        SELECT a.id, a.name AS activityname, 'url' AS activitytype, a.intro AS summary
        FROM mdl_url a
        UNION
        SELECT a.id, a.name AS activityname, 'page' AS activitytype, a.intro AS summary
        FROM mdl_page a
        UNION
        SELECT a.id, a.name AS activityname, 'quiz' AS activitytype, a.intro AS summary
        FROM mdl_quiz a
        UNION
        SELECT a.id, a.name AS activityname, 'data' AS activitytype, a.intro AS summary
        FROM mdl_data a
        UNION
        SELECT a.id, a.name AS activityname, 'assign' AS activitytype, a.intro AS summary
        FROM mdl_assign a
        UNION
        SELECT a.id, a.name AS activityname, 'folder' AS activitytype, a.intro AS summary
        FROM mdl_folder a
        UNION
        SELECT a.id, a.name AS activityname, 'resource' AS activitytype, a.intro AS summary
        FROM mdl_resource a
        UNION
        SELECT a.id, a.name AS activityname, 'lesson' AS activitytype, a.intro AS summary
        FROM mdl_lesson a
        UNION
        SELECT a.id, a.name AS activityname, 'feedback' AS activitytype, a.intro AS summary
        FROM mdl_feedback a
        UNION
        SELECT a.id, a.name AS activityname, 'bigbluebuttonbn' AS activitytype, a.intro AS summary
        FROM mdl_bigbluebuttonbn a
        UNION
        SELECT a.id, a.name AS activityname, 'book' AS activitytype, a.intro AS summary
        FROM mdl_book a
        UNION
        SELECT a.id, a.name AS activityname, 'face2face' AS activitytype, a.intro AS summary
        FROM mdl_face2face a
        
    ) activity ON activity.id = cm.instance AND activity.activitytype = m.name
    WHERE activity.activitytype = 'face2face'
    AND c.id = " . $courseid, null);

    return $results;
}
 
function getModulesStatus($courseid, $sessionid = null, $userid = null)
{
    global $DB, $USER;
    if (!$userid) {
        $userid = $USER->id;
    }

    //les activités 
    $activities = getCourseActivitiesStats($courseid);
    
    $complete = 0;
    foreach ($activities as $activity) {
        if ($activity->id) {
            $query = 'SELECT cmc.id, cmc.completionstate
            FROM mdl_course_modules_completion cmc
            WHERE cmc.userid = ' . $userid . ' AND cmc.coursemoduleid = ' . $activity->id;
            $arr = $DB->get_records_sql($query, null);
            $arrobject = reset($arr);
            if ($arrobject) {
                if ($arrobject->completionstate >= 1) {
                    // L'activité est complétée
                    $complete++;
                }
            }
        }
    }

    $totalactivities = count($activities);
    
    if ($sessionid) {
        //on va chercher les plannings
        global $DB;
        $plannings = $DB->get_records_sql('SELECT DISTINCT sp.id, sp.sectionid, sp.startdate, sp.enddate, sp.geforplanningid
            FROM mdl_smartch_planning sp
            JOIN mdl_smartch_session ss ON ss.id = sp.sessionid
            JOIN mdl_groups g ON g.id = ss.groupid
            JOIN mdl_course c ON c.id = g.courseid
            WHERE c.id = ' . $courseid . ' AND sp.sessionid = ' . $sessionid . '
            ORDER BY sp.startdate ASC', null);

        

        $planningactivities = 0;
        $planningcomplete = 0;
        foreach ($plannings as $planning) {
            
            $planningactivities++;
            if ($planning->startdate < time()) {
                $planningcomplete++;
            }
        }

        //on va chercher le nombre d'activités de type planning dans le ruban
        $activitiesplanning = getCourseActivitiesPlanningStats($courseid);
        //on compte le nombre maximum d'activité planning qu'on peut rajouter
        if($planningactivities > count($activitiesplanning)){
            $planningactivities = count($activitiesplanning);
        }
        //si le nombre de planning est le meme que que le ruban
        if(count($plannings) == count($activitiesplanning)){
            $complete += $planningcomplete;
        }
        $totalactivities += $planningactivities;
    }

    $modulesfinished = $complete;
    $modulestocome = $totalactivities - $modulesfinished;
    if($modulestocome < 0){
        $modulestocome = 0;
    }

    return array($modulesfinished, $modulestocome);
}

function getCompletionRatio($courseid)
{
    global $DB, $USER;
    //les activités 
    $activities = getCourseActivitiesStats($courseid);
    $complete = 0;
    foreach ($activities as $activity) {
        if($activity->id){
            $query = 'SELECT cmc.id, cmc.completionstate
            FROM mdl_course_modules_completion cmc
            WHERE cmc.userid = ' . $USER->id . ' AND cmc.coursemoduleid = ' . $activity->id;
            $arr = $DB->get_records_sql($query, null);
            if (reset($arr)->completionstate >= 1) {
                // L'activité est complétée
                $complete++;
            }
        }
    }
    return $complete;
}

function getCompletionPourcent($courseid, $userid = null)
{
    global $DB, $USER;
    if (!$userid) {
        $userid = $USER->id;
    }

    $modulesstatus = getModulesStatus($courseid, null, $userid);
    $pourcent = $modulesstatus[0]/($modulesstatus[0]+$modulesstatus[1])*100;
    return number_format($pourcent, 2);;
    



    // //les activités 
    // $activities = getCourseActivitiesStats($courseid);
    // $complete = 0;
    // foreach ($activities as $activity) {
    //     if($activity->id){
    //         $query = 'SELECT DISTINCT cmc.id, cmc.completionstate
    //         FROM mdl_course_modules_completion cmc
    //         WHERE cmc.userid = ' . $userid . ' AND cmc.coursemoduleid = ' . $activity->id;
    //         $arr = $DB->get_records_sql($query, null);
    //         $arrobject = reset($arr);
    //         if ($arrobject) {
    //             if ($arrobject->completionstate >= 1) {
    //                 // L'activité est complétée
    //                 $complete++;
    //             }
    //         }
    //     }
        
    // }

    // $totalactivities = count($activities);

    // //on va chercher la session du cours
    // $groups = $DB->get_records_sql('SELECT DISTINCT g.id, g.name FROM mdl_groups g
    // JOIN mdl_groups_members gm ON gm.groupid = g.id
    // WHERE gm.userid = ' . $userid . ' AND g.courseid = ' . $courseid, null);

    // if ($totalactivities > 0) {

    //     //si l'utilisateur à un groupe
    //     if (count($groups) > 0) {
    //         $group = reset($groups);
    //         $groupid = $group->id;
    //         //on va chercher les informations de session 
    //         $sessions = $DB->get_records_sql('SELECT * FROM mdl_smartch_session WHERE groupid = ' . $group->id, null);
    //         $session = reset($sessions);

    //         if ($session) {
    //             //les sessions
    //             global $DB;
    //             $plannings = $DB->get_records_sql('SELECT DISTINCT sp.id, sp.sectionid, sp.startdate, sp.enddate, sp.geforplanningid
    //             FROM mdl_smartch_planning sp
    //             JOIN mdl_smartch_session ss ON ss.id = sp.sessionid
    //             JOIN mdl_groups g ON g.id = ss.groupid
    //             JOIN mdl_course c ON c.id = g.courseid
    //             WHERE c.id = ' . $courseid . ' AND sp.sessionid = ' . $session->id . '
    //             ORDER BY sp.startdate ASC', null);

    //             $planningcomplete = 0;
    //             $planningactivities = 0;
    //             foreach ($plannings as $planning) {
    //                 if ($planning->startdate < time()) {
    //                     $planningcomplete++;
    //                 }
    //                 $totalactivities++;
    //             }
    //             //on va chercher le nombre d'activités de type planning dans le ruban
    //             $activitiesplanning = getCourseActivitiesPlanningStats($courseid);
    //             //on compte le nombre maximum d'activité planning qu'on peut rajouter
    //             if($planningactivities > count($activitiesplanning)){
    //                 $planningactivities = count($activitiesplanning);
    //             }  
    //             //si le nombre de planning est le meme que que le ruban
    //             if(count($plannings) == count($activitiesplanning)){
    //                 $complete += $planningcomplete;
    //             }
    //             $totalactivities += $planningactivities;
    //         }
    //     }
    // }

    // if ($totalactivities == 0) {
    //     $pourcent = 0;
    //     // $pourcent = "N/A";
    // } else {
    //     $pourcent = ceil($complete / $totalactivities * 100);
    // }

    // return $pourcent;
}

function getTeamProgress($courseid, $groupid)
{
    global $DB, $USER;

    //on va chercher les membres du groupe
    $queryusers = '
    SELECT DISTINCT u.id, u.firstname, u.lastname, r.shortname, r.id as roleid
    FROM mdl_role_assignments AS ra 
    LEFT JOIN mdl_user_enrolments AS ue ON ra.userid = ue.userid 
    LEFT JOIN mdl_role AS r ON ra.roleid = r.id 
    LEFT JOIN mdl_context AS c ON c.id = ra.contextid 
    LEFT JOIN mdl_enrol AS e ON e.courseid = c.instanceid AND ue.enrolid = e.id 
    LEFT JOIN mdl_user u ON u.id = ue.userid
    LEFT JOIN mdl_groups_members gm ON u.id = gm.userid
    WHERE gm.groupid = ' . $groupid . '
    AND e.courseid = ' . $courseid . ' 
    AND r.shortname = "student"
    ORDER BY u.lastname ASC';

    $teamates = $DB->get_records_sql($queryusers, null);
    $allprog = 0;
    $min = 0;
    $max = 0;
    $prog = 0;
    //si il n'y a pas de membres, on s'arête
    if (count($teamates) == 0) {
        $all = "N/A";
        $min = "N/A";
        $max = "N/A";
    } else {
        // var_dump($teamates);
        foreach ($teamates as $mate) {
            $prog = getCompletionPourcent($courseid, $mate->id);


            if (is_numeric($prog)) {
                if ($prog < $min) {
                    $min = floor($prog);
                } else if ($prog > $max) {
                    $max = floor($prog);
                }
                $allprog += $prog;
            }
        }
        $all = floor($allprog / count($teamates)) . '%';
        $max = $max . '%';
        $min = $min . '%';
    }


    return array($all, $max, $min);
}

function countSectionPlannings($sectionid, $sessionid)
{
    global $DB;
    $results = $DB->get_records_sql('SELECT COUNT(*) count
    FROM mdl_smartch_planning sp
    WHERE sp.sessionid = ' . $sessionid . ' AND sp.sectionid = ' . $sectionid, null);

    return reset($results)->count;
}

function getSectionPlannings($courseid, $sessionid, $sectionid)
{
    // global $DB;
    // $results = $DB->get_records_sql('SELECT sp.id, sp.sectionid, sp.startdate, sp.enddate, sp.geforplanningid
    // FROM mdl_smartch_planning sp
    // JOIN mdl_smartch_session ss ON ss.id = sp.sessionid
    // JOIN mdl_groups g ON g.id = ss.groupid
    // JOIN mdl_course c ON c.id = g.courseid
    // WHERE c.id = ' . $courseid . ' AND sp.sessionid = ' . $sessionid . '
    // ORDER BY sp.startdate ASC', null);

    global $DB;
    $results = $DB->get_records_sql('SELECT DISTINCT sp.id, sp.sectionid, sp.startdate, sp.enddate, sp.geforplanningid
    FROM mdl_smartch_planning sp
    JOIN mdl_smartch_session ss ON ss.id = sp.sessionid
    JOIN mdl_groups g ON g.id = ss.groupid
    JOIN mdl_course c ON c.id = g.courseid
    WHERE c.id = ' . $courseid . ' AND sp.sessionid = ' . $sessionid . '
    AND sp.sectionid = ' . $sectionid . '
    ORDER BY sp.startdate ASC', null);
    return $results;
}

function getSectionActivityPlannings($courseid, $sessionid, $sectionid)
{
    global $DB;
    $results = $DB->get_records_sql("SELECT cm.id as id, activity.summary as summary,
    activity.activityname, c.id AS courseid, c.fullname AS coursename,
    cm.instance AS activityid, m.id as activitytypeid, m.name AS activitytype, cm.section as moduleid
    FROM mdl_course_modules cm
    JOIN mdl_course c ON c.id = cm.course
    JOIN mdl_modules m ON m.id = cm.module
    LEFT JOIN (
        SELECT a.id, a.name AS activityname, 'face2face' AS activitytype, a.intro AS summary
        FROM mdl_face2face a
    ) activity ON activity.id = cm.instance AND activity.activitytype = m.name
    WHERE c.id = " . $courseid . "
    AND activity.activitytype = 'face2face'
    AND cm.section = " . $sectionid, null);

    return $results;
}



function getPlanningFormateurs($planningid)
{
    global $DB;
    $results = $DB->get_records_sql('SELECT DISTINCT u.id, u.firstname, u.lastname
    FROM mdl_smartch_planning_formateur spf
    JOIN mdl_user u ON u.id = spf.userid
    WHERE u.deleted = 0
    AND spf.planningid = ' . $planningid, null);

    return $results;
}

function getCourseSections($courseid)
{
    global $DB;
    $sections = $DB->get_records_sql('SELECT cs.id, cs.sequence, cs.name
    FROM mdl_course_sections cs
    WHERE cs.course = ' . $courseid . ' AND cs.visible = 1', null);
    return $sections;
}


function getCourseAverageProgression($courseid)
{
    global $DB;
    $totalprog = 0;

    //on va chercher tous les membres du cours
    $members = $DB->get_records_sql('SELECT DISTINCT u.id
    FROM mdl_user u
    JOIN mdl_role_assignments ra ON u.id = ra.userid
    WHERE ra.contextid IN (SELECT id FROM mdl_context WHERE instanceid = ' . $courseid . ' AND contextlevel = 50)
    AND ra.roleid = (SELECT id FROM mdl_role WHERE shortname = "student")
    LIMIT 50', null);

    foreach ($members as $member) {
        $progmember = getCourseProgression($member->id, $courseid);
        $totalprog += $progmember;
    }

    if (count($members) > 0) {
        $averageprog = floor($totalprog / count($members));
    } else {
        $averageprog = 0;
    }
    // return $totalprog;
    return floor($averageprog);
}


function getCourseProgression($userid, $courseid)
{
    global $DB;
    $coursepercentage = new \core_completion\progress();
    $course = $DB->get_record('course', ['id' => $courseid]);
    $completion = new \completion_info($course);
    $progresspercentvalue = $coursepercentage->get_course_progress_percentage($course, $userid);


    // if ($completion->is_enabled()) {
    //     $modules = $completion->get_activities();
    //     //on increment le total d'activités
    //     $totalactivities = $totalactivities + count($modules);

    //     foreach ($modules as $module) {
    //         $moduledata = $completion->get_data($module, false, $USER->id);
    //         if ($moduledata->completionstate == COMPLETION_INCOMPLETE) {
    //             $activitiesdue++;
    //         } else {
    //             $activitiescomplete++;
    //         }
    //     }
    //     if ($progresspercentvalue == "100") {
    //         $coursescompleted++;
    //     }
    // }

    return floor($progresspercentvalue);


    // $modules = getCourseSections($course_id);
    // $completed = 0;
    // $total = 0;
    // foreach ($modules as $module) {
    //     $query = 'SELECT cmc.completionstate
    //     FROM mdl_course_modules_completion cmc
    //     WHERE cmc.userid = ' . $user_id . ' AND cmc.coursemoduleid = ' . $module->id;
    //     global $DB;
    //     $params = null;

    //     $arr = $DB->get_records_sql($query, $params);

    //     if (reset($arr)->completionstate == 1) {
    //         $completed++;
    //     }
    //     $total++;
    // }
    // if ($completed == 0) {
    //     return 0;
    // } else {
    //     return floor(100 * $completed / $total);
    // }

}

function getCourseCompletionRatio($userid, $courseid)
{

    global $DB;
    $course = $DB->get_record('course', ['id' => $courseid]);

    $activitiescomplete = 0;
    $activitiesdue = 0;
    $totalactivities = 0;
    $coursepercentage = new \core_completion\progress();
    $completion = new \completion_info($course);
    $progresspercentvalue = $coursepercentage->get_course_progress_percentage($course, $userid);
    if ($completion->is_enabled()) {
        $modules = $completion->get_activities();
        //on increment le total d'activités
        $totalactivities = $totalactivities + count($modules);

        foreach ($modules as $module) {

            if($module->id){
                $query = 'SELECT cmc.completionstate
                FROM mdl_course_modules_completion cmc
                WHERE cmc.userid = ' . $userid . ' AND cmc.coursemoduleid = ' . $module->id;
    
                $arr = $DB->get_records_sql($query, null);
    
                if (reset($arr)->completionstate >= 1) {
                    $activitiescomplete++;
                } else {
                    $activitiesdue++;
                }
    
    
                //old
                // $moduledata = $completion->get_data($module, false, $userid);
                // if ($moduledata->completionstate == COMPLETION_INCOMPLETE) {
                //     $activitiesdue++;
                // } else {
                //     $activitiescomplete++;
                // }
            }
            
        }
    }

    return $activitiescomplete . "/" . $totalactivities;

    // $modules = getCourseSections($courseid);
    // $completed = 0;
    // $total = 0;
    // foreach ($modules as $module) {
    //     $query = 'SELECT cmc.id, cmc.completionstate
    //     FROM mdl_course_modules_completion cmc
    //     WHERE cmc.userid = ' . $userid . ' AND cmc.coursemoduleid = ' . $module->id;

    //     global $DB;
    //     $params = null;

    //     $arr = $DB->get_records_sql($query, $params);

    //     if (reset($arr)->completionstate >= 1) {
    //         $completed++;
    //     }
    //     $total++;
    // }
    //return $completed . "/" . $total;
}

function GUIDv4($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }

    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((float)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace .
        substr($charid,  0,  8) . $hyphen .
        substr($charid,  8,  4) . $hyphen .
        substr($charid, 12,  4) . $hyphen .
        substr($charid, 16,  4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
    return $guidv4;
}



function getPlanningCompletion($courseid, $sessionid, $sectionid)
{
    //on va chercher les plannings
    $plannings = getSectionPlannings($courseid, $sessionid, $sectionid);

    $planningTrouve = null;
    $countplanning = 1;
    $allsmartchplanning = count($plannings);
    //on compte le nombre de planning de la section dans le ruban
    $sectionplannings = getSectionActivityPlannings($courseid, $sessionid, $sectionid);
    //le nombre d'activité planning de la section
    $countactivityplanning = count($sectionplannings);

    if ($plannings) {
        //on parcoure les smartch plannings de la section
        foreach ($plannings as $planning) {
            if ($planning->sectionid == $sectionid) {

                $planningTrouve = $planning;
                //on supprime l'objet du tableau
                unset($plannings[$planning->id]);
                break; // Sortir de la boucle une fois que le planning est trouvé
            }
        }

        // var_dump($planning);
        if ($planningTrouve  && $countplanning <= $countactivityplanning) {

            // $content .= '->' . $countplanning . '->' . $allsmartchplanning;
            $countplanning++;

            if ($planningTrouve->startdate > time()) {
                $completion = 'Planifiée';
            }
            // else if ($planningTrouve->startdate < time() && $planningTrouve->enddate > time()) {
            //     $completion = '<div>En cours</div>';
            // } 
            else {
                $completion = 'Passée';
            }

            return $completion;
        }
    }
}

function exportCSV($title, $data)
{
    // Définir les en-têtes pour le téléchargement
    header('Content-Type: text/csv');
    //nom_session - date d'extraction
    header('Content-Disposition: attachment; filename="' . $title . '.csv"');

    // Ouvrir le flux de sortie
    $output = fopen('php://output', 'w');

    // Parcourir les données et les écrire au format CSV
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // Fermer le flux de sortie
    fclose($output);
    exit();
}

function exportXLS($title, $data)
{
    // Créer un nouveau document
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Ajouter des données
    // $sheet->setCellValue('A1', 'Hello World !');

    // Remplir les données dans le Spreadsheet
    $rowNumber = 1;
    foreach ($data as $row) {
        $column = 'A';
        foreach ($row as $cell) {
            $sheet->setCellValue($column++ . $rowNumber, $cell);
        }
        $rowNumber++;
    }

    // Écrire dans un fichier .xlsx
    $writer = new Xlsx($spreadsheet);

    // En-têtes pour le téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $title . '.xlsx' . '"');

    // Envoyer le fichier au navigateur
    $writer->save('php://output');
    exit;
}


function smartchDropdownDownload($urlpdf = null,  $urlxls = null, $urlcsv = null)
{
    $content = "";
    $content .= '<style>
/* Style du menu */

    .dropbtn{
        display: block !important;
    }
    .dropdown {
        position: relative;
        width: auto;
    }

    /* Style des éléments du menu déroulant */
    .dropdown-content {
        display: none;
        position: absolute;
        background: white;
        width: 100%;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 30;
    }

    /* Style des liens à l\'intérieur du menu */
    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    /* Afficher le menu déroulant */
    .dropdown-content.show {
        display: block !important;
    }

    /* Changement de couleur au survol */
    .dropdown-content a:hover {
        background: #00315a;
        color: white !important;
    }

</style>';

    $content .=  '
<div>
<div class="dropdown">
<div onclick="toggleDropdown()" class="dropbtn smartch_btn">
    Télécharger le rapport
    <svg style="width: 25px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" />
    </svg>
</div>
<div id="myDropdown" class="dropdown-content">';
    if ($urlpdf) {
        $content .=  '<a target="_blank" href="' . $urlpdf . '" style="cursor:pointer;display:flex;color:#00315a;">
        Télécharger en pdf
    </a>';
    }
    if ($urlxls) {
        $content .=  '<a href="' . $urlxls . '" style="cursor:pointer;color:#00315a;">
        Télécharger en xlsx
    </a>';
    }
    if ($urlcsv) {
        $content .= '<a href="' . $urlcsv . '" style="cursor:pointer;color:#00315a;">
        Télécharger en csv
    </a>';
    }

    $content .=  '</div>
</div>
</div>';

    $content .=  '<script>

// Fonction pour afficher ou masquer le menu
    function toggleDropdown() {
        // alert(\'ouiii\')
        document.getElementById("myDropdown").classList.toggle("show");
    }

    // Fermer le dropdown si l\'utilisateur clique en dehors de celui-ci
    window.onclick = function(event) {
      if (!event.target.matches(".dropbtn")) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains("show")) {
            openDropdown.classList.remove("show");
          }
        }
      }
    }
</script>';
    return $content;
}


function createFreeCategory()
{
    global $DB;
    // On regarde si la category existe déjà
    $catexists = $DB->get_record_sql('SELECT * from mdl_course_categories WHERE name = "Formation gratuite"', null);

    if (!$catexists) {
        // On créer la nouvelle catégorie
        $newcat = new stdClass();
        $newcat->name = "Formation gratuite"; // Le nom de la nouvelle catégorie
        $newcat->parent = 0; // ID de la catégorie parente (0 pour une catégorie de premier niveau)
        $newcat->visible = 1; // 1 pour visible, 0 pour masquer la catégorie
        core_course_category::create($newcat);
    }
}

function stringToAlphabetPositionSum($string) {
    $string = strtoupper($string); // Convertir la chaîne en majuscules pour uniformité
    $sum = 0;
    $chain = "";

    // Parcourir chaque caractère de la chaîne
    for ($i = 0; $i < strlen($string); $i++) {
        $char = $string[$i];

        // Vérifier si le caractère est une lettre de l'alphabet
        if (ctype_alpha($char)) {
            // Ajouter la position de la lettre dans l'alphabet a la somme
            $chain .= ord($char);
            // Calculer la position de la lettre dans l'alphabet (A=1, B=2, ..., Z=26)
            // $position = ord($char) - ord('A') + 1;
            // $sum += $position;
        } else {
            $chain .= ord($char);
        }
    }
    return intval($chain);
}

function addUserToGroupFreeCourse($courseid, $userid)
{
    global $DB;

    //on regarde la date du jour
    setlocale(LC_TIME, "fr_FR");
    $groupname = date('F Y');

    // echo $groupname;

    //on regarde si le groupe existe
    $groupexists = $DB->get_record_sql('SELECT * 
    FROM mdl_groups 
    WHERE courseid = ' . $courseid . ' 
    AND name = "' . $groupname . '"', null);

    if (!$groupexists) {
        //on créer le groupe
        $newgroup = new stdClass();
        $newgroup->name = $groupname;
        $newgroup->courseid = $courseid;
        $newgroup->timecreated = time();
        $newgroup->description = 'Groupe pour utilisateur inscrit en ' . $groupname;
        $newgroup->id = $DB->insert_record('groups', $newgroup);
        $groupexists = $newgroup;

        //on créer une session dans la foulée
        $newsession = new stdClass();
        $newsession->groupid = $groupexists->id;
        $newsession->location = '';
        $newsession->adress1 = '';
        $newsession->adress2 = '';
        $newsession->zip = '';
        $newsession->city = '';
        $newsession->startdate = mktime(0, 0, 0, date('n'), 1, date('Y'));
        $newsession->enddate = $timestamp = mktime(23, 59, 59, date('n'), date('t'), date('Y'));

        $newsessionid = $DB->insert_record('smartch_session', $newsession);
    }

    //on regarde si il n'est pas déjà dans le groupe
    $isuseringroup = $DB->get_record_sql('SELECT * 
        FROM mdl_groups_members 
        WHERE groupid = ' . $groupexists->id . ' 
        AND userid = ' . $userid, null);

    if (!$isuseringroup) {
        //on l'ajoute au groupe
        $newgrouplink = new stdClass();
        $newgrouplink->groupid = $groupexists->id;
        $newgrouplink->userid = $userid;
        $DB->insert_record('groups_members', $newgrouplink);
    }
}


function checkIfUserIsEnrolled($courseid, $userid)
{
    global $DB;
    $query = 'SELECT * 
        FROM mdl_user_enrolments ue
        JOIN mdl_enrol e ON e.id = ue.enrolid
        WHERE ue.userid = ' . $userid . ' AND e.courseid = ' . $courseid;
    $existenroll = $DB->get_records_sql($query, null);

    if (count($existenroll) > 0) {
        return true;
    } else {
        return false;
    }
}


function displayChartBar($dataName, $jsonDays, $jsonData)
{
    global $DB;
    $guid = GUIDv4();
    $content = "";
    $content .= '<div class="col-md-6" style="margin-bottom:50px;">';
    $content .=  '<h4 style="margin:20px 0;text-align:center;">Utilisateurs connectés</h4>';
    $content .=  '<div id="barchart' . $guid . '"></div>';
    $content .=  '</div>';



    $content .=  "<script>

var optionsbarchart" . $guid . " = {
    chart: {
      type: 'bar'
    },
    labels: {
        style: {
            colors: [],
        }
    },
    series: [{
        name: '" . $dataName . "',
        data: " . $jsonData . ",
        
            style: {
                colors: [],
            }
        
    }],
    xaxis: {
        categories: " . $jsonDays . ",
        labels: {
            style: {
                colors: [],
            }
        },
    },
    colors: ['#F44336', '#E91E63', '#9C27B0']
  }

  var barchart" . $guid . " = new ApexCharts(document.querySelector('#barchart" . $guid . "'), optionsbarchart" . $guid . ");

  barchart" . $guid . ".render();

</script>";

    return $content;
}
function displayChartLine($titlechart, $jsonDays, $jsonData)
{
    $guid = GUIDv4();
    $content = "";
    $content .= '<div class="col-md-6" style="margin-bottom:50px;">';
    $content .=  '<h4 style="margin:20px 0;text-align:center;">' . $titlechart . '</h4>';
    $content .=  '<div id="chart' . $guid . '"></div>';
    $content .=  '</div>';


    $content .=  "<script>

    function convertToStringTime(time) {
        let stringTime = \"\";
    
        const h = Math.floor(time / 3600);
        const rh = time % 3600;
        const m = Math.floor(rh / 60);
        const s = rh % 60;
    
        if (h !== 0) {
            stringTime += h + \"h\";
        }
        if (m !== 0 || h !== 0) {
            stringTime += m + \"m\";
        }
        // if (s !== 0 || m !== 0 || h !== 0) {
        //     stringTime += s + \"s\";
        // }
    
        if (stringTime === \"\") {
            stringTime = \"0h\";
        }
        return stringTime;
    }

    
// Votre code ici
var optionschart" . $guid . " = {
  series: " . $jsonData . ",
  chart: {
  height: 350,
  type: 'area'
},
dataLabels: {
  enabled: false
},
stroke: {
  curve: 'smooth'
},
xaxis: {
    type: 'datetime',
    //categories: ['2018-09-19T00:00:00.000Z', '2018-09-19T01:30:00.000Z', '2018-09-19T02:30:00.000Z', '2018-09-19T03:30:00.000Z', '2018-09-19T04:30:00.000Z', '2018-09-19T05:30:00.000Z', '2018-09-19T06:30:00.000Z']
    categories: " . $jsonDays . ",
    labels: {
        style: {
            colors: [],
        }
    },
    title: {
        text: '',
        offsetX: 0,
        offsetY: 0,
        style: {
            
        },
    },
},
tooltip: {
  x: {
    format: 'dd/MM/yyyy'
  },
},
fill: {
    colors: ['#3CAFE3', '#E91E63', '#9C27B0']
},
yaxis: {
    labels: {
        style: {
            colors: [],
        },
        formatter: function (time) {
            //on formate en h
            return convertToStringTime(time)
          }
    },
},
markers: {
    colors: ['#F44336', '#E91E63', '#9C27B0']
},
colors: ['#3CAFE3', '#00315a', '#DCDCDC']
};

var chart" . $guid . " = new ApexCharts(document.querySelector('#chart" . $guid . "'), optionschart" . $guid . ");
chart" . $guid . ".render();


</script>";


    return $content;
}

function displayChartBarMultiple($titlechart, $jsonDays, $jsonData)
{
    $guid = GUIDv4();
    $content = "";
    $content .= '<div class="col-md-6" style="margin-bottom:50px;">';
    $content .=  '<h4 style="margin:20px 0;text-align:center;">' . $titlechart . '</h4>';
    $content .=  '<div id="chart' . $guid . '"></div>';
    $content .=  '</div>';


    $content .= "
    <script>

    var optionschart" . $guid . " = {

    series: " . $jsonData . ",
    // series: [{
    //     name: 'PRODUCT A',
    //     data: [44, 55, 41, 67, 22, 43]
    //   }, {
    //     name: 'PRODUCT B',
    //     data: [13, 23, 20, 8, 13, 27]
    //   }, {
    //     name: 'PRODUCT C',
    //     data: [11, 17, 15, 15, 21, 14]
    //   }, {
    //     name: 'PRODUCT D',
    //     data: [21, 7, 25, 13, 22, 8]
    //   }],
        chart: {
        type: 'bar',
        height: 350,
        stacked: true,
        toolbar: {
          show: true
        },
        zoom: {
          enabled: true
        }
      },
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 0
          }
        }
      }],
      plotOptions: {
        bar: {
          horizontal: false,
          borderRadius: 2,
          dataLabels: {
            total: {
              enabled: true,
              style: {
                fontSize: '13px',
                fontWeight: 900
              }
            }
          }
        },
      },
      xaxis: {
        type: 'datetime',
        categories: " . $jsonDays . "
        // categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT',
        //   '01/05/2011 GMT', '01/06/2011 GMT'
        // ],
      },
      legend: {
        position: 'right',
        offsetY: 40
      },
      fill: {
        opacity: 1
      },
      colors: ['#3CAFE3', '#00315a', '#DCDCDC']
    };

    var chart" . $guid . " = new ApexCharts(document.querySelector('#chart" . $guid . "'), optionschart" . $guid . ");
    chart" . $guid . ".render();
    
    </script>
    ";

    return $content;
}

function displayChartLineHorizontal($titlechart, $jsonX, $jsonY, $size = 350)
{
    $guid = GUIDv4();
    $content = "";
    $content .= '<div class="col-md-6" style="margin-bottom:50px;">';
    $content .=  '<h4 style="margin:20px 0;text-align:center;">' . $titlechart . '</h4>';
    $content .=  '<div id="chart' . $guid . '"></div>';
    $content .=  '</div>';


    $content .=  "<script>

    var optionschart" . $guid . " = {
        series: " . $jsonX . ",
    // series: [{
    //     name: 'Nombre d\'utilisateurs',
    //     data: [44, 55, 41, 37, 22, 43, 21]
    //   }, {
    //     name: 'Nombre de sessions',
    //     data: [53, 32, 33, 52, 13, 43, 32]
    //   }],
        chart: {
        type: 'bar',
        height:  " . $size . "
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: " . $jsonY . ",
      },
      colors: ['#3CAFE3', '#E60028', '#00315a']
      };

var chart" . $guid . " = new ApexCharts(document.querySelector('#chart" . $guid . "'), optionschart" . $guid . ");
chart" . $guid . ".render();


</script>";


    return $content;
}

function displayChartLineHorizontalMultiple($titlechart, $jsonX, $jsonY)
{
    $guid = GUIDv4();
    $content = "";
    $content .= '<div class="col-md-6" style="margin-bottom:50px;">';
    $content .=  '<h4 style="margin:20px 0;text-align:center;">' . $titlechart . '</h4>';
    $content .=  '<div id="chart' . $guid . '"></div>';
    $content .=  '</div>';


    $content .= "<script>
    
    var optionschart" . $guid . " = {

        // series: [{
        //     data: " . $jsonX . "
        //   }],
        series: [{
            name: 'Sessions publiés',
            data: [44, 55, 41, 37, 22, 43, 21]
          }, {
            name: 'Nombre de sessions',
            data: [53, 32, 33, 52, 13, 43, 32]
          }],
            chart: {
            type: 'bar',
            height: 350,
            stacked: true,
          },
          plotOptions: {
            bar: {
              horizontal: true,
              dataLabels: {
                total: {
                  enabled: true,
                  offsetX: 0,
                  style: {
                    fontSize: '13px',
                    fontWeight: 900
                  }
                }
              }
            },
          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },
        //   title: {
        //     text: 'Fiction Books Sales'
        //   },
          xaxis: {
            categories: " . $jsonY . ",
            // categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
            labels: {
              formatter: function (val) {
                return val + \"K\"
              }
            }
          },
          yaxis: {
            title: {
              text: undefined
            },
          },
          tooltip: {
            y: {
              formatter: function (val) {
                return val + \"K\"
              }
            }
          },
          fill: {
            opacity: 1
          },
          legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
          }
          };

        var chart" . $guid . " = new ApexCharts(document.querySelector('#chart" . $guid . "'), optionschart" . $guid . ");
        chart" . $guid . ".render();
    
    </script>";



    return $content;
}
function displayChartLineHorizontalMultipleInside($titlechart, $jsonX, $jsonY, $size = null)
{
    $guid = GUIDv4();
    $content = "";
    $content .= '<div class="col-md-6" style="margin-bottom:50px;">';
    $content .=  '<h4 style="margin:20px 0;text-align:center;">' . $titlechart . '</h4>';
    $content .=  '<div id="chart' . $guid . '"></div>';
    $content .=  '</div>';


    $content .= "<script>

    function convertToStringTime(time) {
        let stringTime = \"\";
    
        const h = Math.floor(time / 3600);
        const rh = time % 3600;
        const m = Math.floor(rh / 60);
        const s = rh % 60;
    
        if (h !== 0) {
            stringTime += h + \"h\";
        }
        if (m !== 0 || h !== 0) {
            stringTime += m + \"m\";
        }
        // if (s !== 0 || m !== 0 || h !== 0) {
        //     stringTime += s + \"s\";
        // }
    
        if (stringTime === \"\") {
            stringTime = \"0h\";
        }
        return stringTime;
    }
    
    var optionschart" . $guid . " = {
        series: [{
            data: " . $jsonX . "
          }],
            chart: {
            type: 'bar',
            height: " . $size . "
          },
          plotOptions: {
            bar: {
              barHeight: '100%',
              distributed: true,
              horizontal: true,
              dataLabels: {
                position: 'bottom'
              },
            }
          },
          colors: ['#33b2df', '#546E7A', '#d4526e', '#13d8aa', '#A5978B', '#2b908f', '#f9a3a4', '#90ee7e',
            '#f48024', '#69d2e7'
          ],
          dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
              colors: ['#333']
            },
            formatter: function (time, opt) {
              return opt.w.globals.labels[opt.dataPointIndex] + \" |  \" + convertToStringTime(time) + ' '
            },
            offsetX: 0,
            dropShadow: {
              enabled: false
            }
          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },
          xaxis: {
            categories: " . $jsonY . ",
            labels: {
                show: false
              }
          },
          yaxis: {
            labels: {
              show: false
            }
          },
        //   title: {
        //       text: 'Custom DataLabels',
        //       align: 'center',
        //       floating: true
        //   },
        //   subtitle: {
        //       text: 'Category Names as DataLabels inside bars',
        //       align: 'center',
        //   },
          tooltip: {
            theme: 'light',
            x: {
              show: false
            },
            y: {
              title: {
                formatter: function (time) {
                    if(time != ''){
                        return convertToStringTime(time) + ' '
                    } else{
                        return '';
                    }
                }
              }
            }
          }
        };
    
      
      var chart" . $guid . " = new ApexCharts(document.querySelector('#chart" . $guid . "'), optionschart" . $guid . ");
        chart" . $guid . ".render();
    
    
    </script>";

    return $content;
}

function checkIfUsernameIsINNO($chaine) {
    // Vérifier si la chaîne n'est pas un email
    if (!filter_var($chaine, FILTER_VALIDATE_EMAIL)) {
        // Vérifier si la chaîne est un chiffre
        if (ctype_digit($chaine)) {
            return true; // La chaîne est un chiffre et n'est pas un email
        } else {
            return false; // La chaîne n'est pas un email mais n'est pas un chiffre non plus
        }
    } else {
        return false; // La chaîne est un email
    }
}