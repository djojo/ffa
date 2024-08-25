<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language file.
 *
 * @package   theme_remui
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedsettings'] = 'Paramètres avancés';
$string['backgroundimage'] = 'Image de fond';
$string['backgroundimage_desc'] = 'L\'image à afficher en tant que fond du site. L\'image de fond que vous téléchargez ici remplacera l\'image de fond dans les fichiers de thème prédéfinis.';
$string['brandcolor'] = 'Couleur de la marque';
$string['brandcolor_desc'] = 'La couleur de la marque.';
$string['bootswatch'] = 'Bootswatch';
$string['bootswatch_desc'] = 'Un bootswatch est un ensemble de variables Bootstrap et de CSS pour styler Bootstrap.';
$string['choosereadme'] = 'Smartch FFA est un thème créé pour la FFA.';
$string['currentinparentheses'] = '(actuel)';
$string['configtitle'] = 'Smartch FFA';
$string['generalsettings'] = 'Général';
$string['loginbackgroundimage'] = 'Image de fond de la page de connexion';
$string['loginbackgroundimage_desc'] = 'L\'image à afficher en tant que fond de la page de connexion.';
$string['nobootswatch'] = 'Aucun';
$string['pluginname'] = 'Smartch FFA';
$string['presetfiles'] = 'Fichiers prédéfinis supplémentaires du thème';
$string['presetfiles_desc'] = 'Les fichiers prédéfinis peuvent être utilisés pour modifier radicalement l\'apparence du thème. Consultez la <a href="https://docs.moodle.org/dev/remui_Presets">documentation sur les prédéfinis remui</a> pour obtenir des informations sur la création et le partage de vos propres fichiers prédéfinis, et consultez le <a href="https://archive.moodle.net/remui">dépôt de prédéfinis</a> pour trouver des prédéfinis partagés par d\'autres.';
$string['preset'] = 'Prédéfini du thème';
$string['preset_desc'] = 'Sélectionnez un prédéfini pour changer largement l\'apparence du thème.';
$string['privacy:metadata'] = 'Le thème remui ne stocke aucune donnée personnelle sur un utilisateur.';
$string['rawscss'] = 'SCSS brut';
$string['rawscss_desc'] = 'Utilisez ce champ pour fournir du code SCSS ou CSS qui sera injecté à la fin de la feuille de style.';
$string['rawscsspre'] = 'SCSS initial brut';
$string['rawscsspre_desc'] = 'Dans ce champ, vous pouvez fournir du code SCSS d\'initialisation, qui sera injecté avant tout le reste. La plupart du temps, vous utiliserez ce paramètre pour définir des variables.';
$string['region-side-pre'] = 'Droite';
$string['region-side-top'] = 'Haut';
$string['region-side-bottom'] = 'Bas';
$string['showfooter'] = 'Afficher le pied de page';
$string['unaddableblocks'] = 'Blocs inutiles';

$string['unaddableblocks_desc'] = 'Les blocs spécifiés ne sont pas nécessaires lors de l\'utilisation de ce thème et ne seront pas répertoriés dans le menu "Ajouter un bloc".';
$string['privacy:metadata:preference:draweropenblock'] = 'La préférence de l\'utilisateur pour masquer ou afficher le tiroir avec les blocs.';
$string['privacy:metadata:preference:draweropenindex'] = 'La préférence de l\'utilisateur pour masquer ou afficher le tiroir avec l\'index des cours.';
$string['privacy:metadata:preference:draweropennav'] = 'La préférence de l\'utilisateur pour masquer ou afficher la navigation du menu du tiroir.';
$string['privacy:drawerindexclosed'] = 'La préférence actuelle pour le tiroir d\'index est fermée.';
$string['privacy:drawerindexopen'] = 'La préférence actuelle pour le tiroir d\'index est ouverte.';
$string['privacy:drawerblockclosed'] = 'La préférence actuelle pour le tiroir de bloc est fermée.';
$string['privacy:drawerblockopen'] = 'La préférence actuelle pour le tiroir de bloc est ouverte.';
$string['privacy:drawernavclosed'] = 'La préférence actuelle pour le tiroir de navigation est fermée.';
$string['privacy:drawernavopen'] = 'La préférence actuelle pour le tiroir de navigation est ouverte.';

// Deprecated since Moodle 4.0.
$string['totop'] = 'Retour en haut';

// Edwiser RemUI Settings Page Strings.

// Settings Tabs strings.
$string['homepagesettings'] = 'Page d\'accueil';
$string['coursesettings'] = "Page du cours";
$string['footersettings'] = 'Pied de page';
$string["formsettings"] = "Formulaires";
$string["iconsettings"] = "Icônes";
$string['loginsettings'] = 'Page de connexion';

$string['versionforheading'] = '<span class="small remuiversion"> Version {$a}</span>';
$string['themeversionforinfo'] = '<span>Version actuellement installée : Edwiser RemUI v{$a}</span>';

// General Settings.
$string['mergemessagingsidebar'] = 'Fusionner le panneau de messagerie';
$string['mergemessagingsidebardesc'] = 'Fusionner le panneau de messagerie dans la barre latérale droite';
$string['logoorsitename'] = 'Choisir le format du logo du site';
$string['logoorsitenamedesc'] = 'Logo uniquement - Grand logo de marque <br /> Logo mini - Mini logo de marque <br /> Icône uniquement - Une icône en tant que marque <br/> Icône et nom du site - Icône avec nom du site';
$string['onlylogo'] = 'Logo uniquement';
$string['logo'] = 'Logo';
$string['logomini'] = 'Logo mini';
$string['icononly'] = 'Icône uniquement';
$string['iconsitename'] = 'Icône et nom du site';

$string['logodesc'] = 'Vous pouvez ajouter le logo à afficher dans l\'en-tête. Notez que la hauteur préférée est de 50px. Si vous souhaitez personnaliser la hauteur, vous pouvez le faire dans la boîte CSS personnalisée.';
$string['logominidesc'] = 'Vous pouvez ajouter le logo mini à afficher dans l\'en-tête lorsque la barre latérale est réduite. Notez que la hauteur préférée est de 50px. Si vous souhaitez personnaliser la hauteur, vous pouvez le faire dans la boîte CSS personnalisée.';
$string['siteicon'] = 'Icône du site';
$string['siteicondesc'] = 'Vous n\'avez pas de logo ? Vous pouvez en choisir un dans cette <a href="https://fontawesome.com/v4.7.0/cheatsheet/" target="_new" ><b style="color:#17a2b8!important">liste</b></a>.<br /> Entrez simplement le texte après "fa-".';
$string['navlogin_popup'] = 'Activer la connexion en pop-up';
$string['navlogin_popupdesc'] = 'Activer la connexion en pop-up pour se connecter rapidement sans être redirigé vers la page de connexion.';
$string['coursecategories'] = 'Catégories';
$string['enablecoursecategorymenu'] = "Menu déroulant des catégories dans l'en-tête";
$string['enablecoursecategorymenudesc'] = "Gardez cette option activée si vous souhaitez afficher le menu déroulant des catégories dans l'en-tête";
$string['coursepagesettings'] = "Page de cours";
$string['coursepagesettingsdesc'] = "Paramètres liés aux cours.";
$string['coursecategoriestext'] = "Renommer le menu déroulant des catégories dans l'en-tête";
$string['coursecategoriestextdesc'] = "Vous pouvez ajouter un nom personnalisé pour le menu déroulant des catégories dans l'en-tête.";
$string['enablerecentcourses'] = 'Activer les cours récents';
$string['enablerecentcoursesdesc'] = 'Si cette option est activée, le menu déroulant des cours récents sera affiché dans l\'en-tête.';
$string['recent'] = 'Récent';
$string['recentcoursesmenu'] = 'Menu des cours récents';
$string['searchcatplaceholdertext'] = 'Rechercher des catégories';
$string['viewallnotifications'] = 'Voir toutes les notifications';
$string['forgotpassword'] = 'Mot de passe oublié ?';
$string['enableannouncement'] = "Activer l'annonce du site entier";
$string['enableannouncementdesc'] = "Activer l'annonce du site entier pour tous les utilisateurs.";
$string['enabledismissannouncement'] = "Activer l'annonce du site entier avec possibilité de la fermer";
$string['enabledismissannouncementdesc'] = "Si cette option est activée, les utilisateurs peuvent fermer l'annonce.";
$string['brandlogo'] = 'Logo de marque';
$string['brandname'] = 'Nom de la marque';

$string['announcementtext'] = "Annonce";
$string['announcementtextdesc'] = "Message d'annonce à afficher sur l'ensemble du site.";
$string['announcementtype'] = "Type d'annonce";
$string['announcementtypedesc'] = "Sélectionnez le type d'annonce pour afficher une couleur de fond différente pour l'annonce.";
$string['typeinfo'] = "Information";
$string['typedanger'] = "Urgent";
$string['typewarning'] = "Avertissement";
$string['typesuccess'] = "Succès";






// Google Analytics.
$string['googleanalytics'] = 'Google Analytics Tracking ID';
$string['googleanalyticsdesc'] = 'Please enter your Google Analytics Tracking ID to enable analytics on your website. The  tracking ID format shold be like [UA-XXXXX-Y].<br/>Please be aware that by including this setting, you will be sending data to Google Analytics and you should make sure that your users are warned about this. Our product does not store any of the data being sent to Google Analytics.';
$string['favicon'] = 'Favicon';
$string['favicosize'] = 'Expected size is 16x16 pixels';
$string['favicondesc'] = 'Your site’s “favourite icon”. It is a visual reminder of the Web site identity and is displayed in the address bar or in the browser\'s tabs';
$string['fontselect'] = 'Font type selector';
$string['fontselectdesc'] = 'Choose from either Standard fonts or <a href="https://fonts.google.com/" target="_new">Google web fonts</a> types. Please save to show the options for your choice. Note: If Customizer font is set to Standard then Google web font will be applied.';
$string['fontname'] = 'Site Font';
$string['fontnamedesc'] = 'Enter the exact name of the font to use for Moodle.';
$string['fonttypestandard'] = 'Standard font';
$string['fonttypegoogle'] = 'Google web font';

$string['sendfeedback'] = "Send Feedback to Edwiser";
$string['enableedwfeedback'] = "Edwiser Feedback & Support";
$string['enableedwfeedbackdesc'] = "Enable Edwiser Feedback & Support, visible to Admins only.";
$string["checkfaq"] = "Edwiser RemUI - Check FAQ";
$string['poweredbyedwiser'] = 'Powered by Edwiser';
$string['poweredbyedwiserdesc'] = 'Uncheck to remove  \'Powered by Edwiser\' from your site.';
$string['enabledictionary'] = 'Enable Dictionary';
$string['enabledictionarydesc'] = 'If enabled, Dictionary feature will be activated and which will show the meaning of selected text in popup.';
$string['customcss'] = 'Custom CSS';
$string['customcssdesc'] = 'You may customise the CSS from the text box above. The changes will be reflected on all the pages of your site.';
// Footer Content.
$string['followus'] = 'Follow Us';
$string['poweredby'] = 'Powered by';

// One click report  bug/feedback.
$string['sendfeedback'] = "Send Feedback to Edwiser";
$string['descriptionmodal_text1'] = "<p>Feedback lets you send us suggestions about our products. We welcome problem reports, feature ideas and general comments.</p><p>Start by writing a brief description:</p>";
$string['descriptionmodal_text2'] = "<p>Next we\'ll let you identify areas of the page related to your description.</p>";
$string['emptydescription_error'] = "Please enter a description.";
$string['incorrectemail_error'] = "Please enter proper email ID.";

$string['highlightmodal_text1'] = "Click and drag on the page to help us better understand your feedback. You can move this dialog if it\'s in the way.";
$string['highlight_button'] = "Highlight area";
$string['blackout_button'] = "Hide info";
$string['highlight_button_tooltip'] = "Highlight areas relevant to your feedback.";
$string['blackout_button_tooltip'] = "Hide any personal information.";

$string['feedbackmodal_next'] = 'Take Screenshot and Continue';
$string['feedbackmodal_skipnext'] = 'Skip and Continue';
$string['feedbackmodal_previous'] = 'Back';
$string['feedbackmodal_submit'] = 'Submit';
$string['feedbackmodal_ok'] = 'Okay';

$string['description_heading'] = 'Description';
$string['feedback_email_heading'] = 'Email';
$string['additional_info'] = 'Additional info';
$string['additional_info_none'] = 'None';
$string['additional_info_browser'] = 'Browser Info';
$string['additional_info_page'] = 'Page Info';
$string['additional_info_pagestructure'] = 'Page Structure';
$string['feedback_screenshot'] = 'Screenshot';
$string['feebdack_datacollected_desc'] = 'An overview of the data collected is available <strong><a href="https://forums.edwiser.org/topic/67/anonymously-tracking-the-usage-of-edwiser-products" target="_blank">here</a></strong>.';

$string['submit_loading'] = 'Loading...';
$string['submit_success'] = 'Thank you for your feedback. We value every piece of feedback we receive.';
$string['submit_error'] = 'Sadly an error occured while sending your feedback. Please try again.';
$string['send_feedback_license_error'] = "Please activate the license to get product support.";
$string['disabled'] = 'Disabled';

$string['nocoursefound'] = 'No Course Found';

$string['pagewidth'] = 'Theme layout';
$string['pagewidthdesc'] = 'Here you can choose layout size for pages.';
$string['defaultpermoodle'] = 'Narrow width (Moodle default)';
$string['fullwidthlayout'] = 'Full width';

// Footer Page Settings.
$string['footersettings'] = 'Footer';
$string['socialmedia'] = 'Social Media';
$string['socialmediadesc'] = 'Enter the social media links for your site.';
$string['facebooksetting'] = 'Facebook';
$string['facebooksettingdesc'] = 'Enter your site\'s facebook page link. For eg. https://www.facebook.com/pagename';
$string['twittersetting'] = 'Twitter';
$string['twittersettingdesc'] = 'Enter your site\'s twitter page link. For eg. https://www.twitter.com/pagename';
$string['linkedinsetting'] = 'Linkedin';
$string['linkedinsettingdesc'] = 'Enter your site\'s linkedin page link. For eg. https://www.linkedin.com/in/pagename';
$string['gplussetting'] = 'Google Plus';
$string['gplussettingdesc'] = 'Enter your site\'s Google Plus page link. For eg. https://plus.google.com/pagename';
$string['youtubesetting'] = 'YouTube';
$string['youtubesettingdesc'] = 'Enter your site\'s YouTube page link. For eg. https://www.youtube.com/channel/UCU1u6QtAAPJrV0v0_c2EISA';
$string['instagramsetting'] = 'Instagram';
$string['instagramsettingdesc'] = 'Enter your site\'s Instagram page link. For eg. https://www.instagram.com/name';
$string['pinterestsetting'] = 'Pinterest';
$string['pinterestsettingdesc'] = 'Enter your site\'s Pinterest page link. For eg. https://www.pinterest.com/name';
$string['quorasetting'] = 'Quora';
$string['quorasettingdesc'] = 'Enter your site\'s Quora page link. For eg. https://www.quora.com/name';
$string['footerbottomtext'] = 'Footer Bottom-Left Text';
$string['footerbottomlink'] = 'Footer Bottom-Left Link';
$string['footerbottomlinkdesc'] = 'Enter the Link for the bottom-left section of Footer. For eg. http://www.yourcompany.com';
$string['footercolumn1heading'] = 'Footer Content for 1st Column (Left)';
$string['footercolumn1headingdesc'] = 'This section relates to the bottom portion (Column 1) of your frontpage.';
$string['footercolumn1title'] = '1st Footer Column title';
$string['footercolumn1titledesc'] = 'Add title to this column.';
$string['footercolumncustomhtml'] = 'Content';
$string['footercolumn1customhtmldesc'] = 'You can customize HTML of this column using above given textbox.';
$string['footercolumn2heading'] = 'Footer Content for 2nd Column (Middle)';
$string['footercolumn2headingdesc'] = 'This section relates to the bottom portion (Column 2) of your frontpage.';
$string['footercolumn2title'] = '2nd Footer Column Title';
$string['footercolumn2titledesc'] = 'Add title to this column.';
$string['footercolumn2customhtml'] = 'Custom HTML';
$string['footercolumn2customhtmldesc'] = 'You can customize HTML of this column using above given textbox.';
$string['footercolumn3heading'] = 'Footer Content for 3rd Column (Middle)';
$string['footercolumn3headingdesc'] = 'This section relates to the bottom portion (Column 3) of your frontpage.';
$string['footercolumn3title'] = '3rd Footer Column Title';
$string['footercolumn3titledesc'] = 'Add title to this column.';
$string['footercolumn3customhtml'] = 'Custom HTML';
$string['footercolumn3customhtmldesc'] = 'You can customize HTML of this column using above given textbox.';
$string['footercolumn4heading'] = 'Footer Content for 4th Column (Right)';
$string['footercolumn4headingdesc'] = 'This section relates to the bottom portion (Column 4) of your frontpage.';
$string['footercolumn4title'] = '4th Footer Column title';
$string['footercolumn4titledesc'] = 'Add title to this column.';
$string['footercolumn4customhtml'] = 'Custom HTML';
$string['footercolumn4customhtmldesc'] = 'You can customize HTML of this column using above given textbox.';
$string['footerbottomheading'] = 'Bottom Footer Setting';
$string['footerbottomdesc'] = 'Here you can specify your own link you want to enter at the bottom section of Footer';
$string['footerbottomtextdesc'] = 'Add text to Bottom Footer Setting.';
$string['footercopyrightsshow'] = 'show';
$string['footercopyright'] = 'Show Copyrights Content';
$string['footercopyrights'] = '[site] © [year]. All rights reserved.';
$string['footercopyrightsdesc'] = 'Add Copyrights content in the bottom of page.';
$string['footercopyrightstags'] = 'Tags:<br>[site]  -  Site name<br>[year]  -  Current year';
$string['footerbottomlink'] = 'Footer Bottom-Left Link';
$string['footerbottomlinkdesc'] = 'Enter the Link for the bottom-left section of Footer. For eg. http://www.yourcompany.com';
$string['footerbottomtext'] = 'Footer Bottom-Left Text';
$string['footerbottomlink'] = 'Footer Bottom-Left Link';
$string['copyrighttextarea'] = 'Copyrights Content';
$string['footercolumnsize'] = 'No of widget';
$string['one'] = 'One';
$string['two'] = 'Two';
$string['three'] = 'Three';
$string['four'] = 'Four';
$string['showsocialmediaicon'] = "Show social media icons";
$string['footercolumntype'] = 'Type';
$string['footercolumncustommenudesc'] = 'Add Your menu items in this formate for eg.<br><pre>[
    {
        "text": "Add your Text here",
        "address": "http://XYZ.abc"
    },
    {
        "text": "Add your Text here",
        "address": "http://XYZ.abc"
    }, ...
]</pre>
<b style="color:red;">Note:</b> This setting is just temporarily being placed here as we are developing our customizer. In future, these settings will be moved to our customizer along with user-friendly UI to add the menu items';
$string['gotop'] = 'Haut de page';

$string['menu'] = 'Menu';
$string['content'] = 'Contenu';
$string['footercolumntypedesc'] = 'Vous pouvez choisir le type de widget pour le pied de page';
$string['socialmediaicondesc'] = 'Cela affichera les icônes des réseaux sociaux dans cette section';
$string['footercolumncustommmenu'] = 'Ajouter des éléments de menu';
$string['follometext'] = 'Suivez-moi sur {$a}';
$string['footercolumndesc'] = 'Sélectionnez le nombre de widgets dans le pied de page';
$string['footershowlogo'] = 'Afficher le logo dans le pied de page';
$string['footershowlogodesc'] = 'Affichez le logo dans le pied de page secondaire.';

$string['footertermsandconditionsshow'] = 'Afficher les termes et conditions';
$string['footertermsandconditions'] = 'Lien des termes et conditions';
$string['footertermsandconditionsdesc'] = 'Vous pouvez ajouter un lien vers la page des termes et conditions.';
$string['footertermsandconditionsshowdesc'] = 'Termes et conditions du pied de page';
$string['footerprivacypolicyshowdesc'] = 'Lien vers la politique de confidentialité';

$string['footerprivacypolicyshow'] = 'Afficher la politique de confidentialité';
$string['footerprivacypolicy'] = 'Lien vers la politique de confidentialité';
$string['footerprivacypolicydesc'] = 'Vous pouvez ajouter un lien vers la page de la politique de confidentialité.';
$string['termsandconditions'] = 'Termes et conditions';
$string['privacypolicy'] = 'Politique de confidentialité';
$string['typeamessage'] = "Tapez votre message ici";
$string['allcontacts'] = "Tous les contacts";

// Profile Page.
$string['administrator'] = 'Administrateur';
$string['contacts'] = 'Contacts';
$string['blogentries'] = 'Articles de blog';
$string['discussions'] = 'Discussions';
$string['aboutme'] = 'À propos de moi';
$string['courses'] = 'Cours';
$string['interests'] = 'Centres d\'intérêt';
$string['institution'] = 'Département et établissement';
$string['location'] = 'Emplacement';
$string['description'] = 'Description';
$string['editprofile'] = 'Modifier le profil';
$string['start_date'] = 'Date de début';
$string['complete'] = 'Compléter';
$string['surname'] = 'Nom de famille';
$string['actioncouldnotbeperformed'] = 'L\'action n\'a pas pu être effectuée!';
$string['enterfirstname'] = 'Veuillez entrer votre prénom.';
$string['enterlastname'] = 'Veuillez entrer votre nom de famille.';
$string['entervalidphoneno'] = 'Entrez un numéro de téléphone valide';
$string['enteremailid'] = 'Veuillez entrer votre identifiant de messagerie.';
$string['enterproperemailid'] = 'Veuillez entrer un identifiant de messagerie correct.';
$string['detailssavedsuccessfully'] = 'Détails enregistrés avec succès!';
$string['fullname'] = 'Nom complet';
$string['viewcourselow'] = "Voir le cours";

$string['focusmodesettings'] = 'Paramètres du mode de focus';
$string['focusmode'] = 'Mode de focus';
$string['enablefocusmode'] = 'Activer le mode de focus';
$string['enablefocusmodedesc'] = 'Si activé, un bouton pour passer en mode d\'apprentissage sans distraction apparaîtra sur la page de cours.';
$string['focusmodeenabled'] = 'Mode de focus activé';
$string['focusmodedisabled'] = 'Mode de focus désactivé';
$string['coursedata'] = 'Données du cours';
$string['prev'] = 'Précédent';
$string['next'] = 'Suivant';
$string['enablecoursestats'] = 'Activer les statistiques de cours';
$string['enablecoursestatsdesc'] = 'Si activé, l\'administrateur, les gestionnaires et les enseignants verront les statistiques des utilisateurs relatives au cours inscrit sur la page de cours unique.';

// Statistiques de cours.
$string['notenrolledanycourse'] = 'Aucun cours inscrit.';
$string['enrolledusers'] = 'Étudiants inscrits';
$string['studentcompleted'] = 'Étudiants ayant terminé';
$string['inprogress'] = 'En cours';
$string['yettostart'] = 'Pas encore commencé';
$string['completepercent'] = '{$a}% de cours terminé';
$string['seeallmycourses'] = "<span class='d-none d-lg-block'>Voir tous mes </span> <span>cours en cours</span>";
$string['noactivity'] = 'Aucune activité dans le cours';
$string['activitydata'] = '{$a->complete} activités sur {$a->total} complétées';

// Login Page Strings.
$string['loginsettingpic'] = 'Télécharger une image de fond';
$string['loginsettingpicdesc'] = 'Téléchargez une image en tant que fond pour le formulaire de connexion.';
$string['loginpagelayout'] = 'Position du formulaire de connexion';
$string['loginpagelayoutdesc'] = 'Choisissez le design de la page de connexion.';
$string['logincenter'] = 'Centré';
$string['loginleft'] = 'Côté gauche';
$string['loginright'] = 'Côté droit';
$string['brandlogopos'] = "Afficher le logo sur la page de connexion";
$string['brandlogoposdesc'] = "Si activé, le logo de la marque sera affiché sur la page de connexion.";
$string['hiddenlogo'] = "Désactiver";
$string['sidebarregionlogo'] = 'Sur la carte de connexion';
$string['maincontentregionlogo'] = 'Sur la région centrale';
$string['loginpanellogo'] = 'Logo de l\'en-tête (page de connexion)';
$string['loginpanellogodesc'] = 'Dépend de <strong>Choisir le format du logo du site</strong>';
$string['signuptextcolor'] = 'Couleur de la description du site';
$string['signuptextcolordesc'] = 'Sélectionnez la couleur du texte pour la description du site.';
$string['brandlogotext'] = "Description du site";
$string['loginpagesitedescription'] = 'Description du site sur la page de connexion';
$string['brandlogotextdesc'] = "Ajoutez du texte pour la description du site qui s'affichera sur la page de connexion et d'inscription. Laissez ceci vide si vous ne voulez pas mettre de description.";
$string['createnewaccount'] = 'Créer un nouveau compte';
$string['welcometobrand'] = 'Bonjour, bienvenue sur {$a}';
$string['entertologin'] = "Entrez vos informations pour vous connecter à votre compte";
$string['forgotaccount'] = 'Mot de passe oublié?';
$string['potentialidps'] = 'Ou connectez-vous en utilisant votre compte';
$string['firsttime'] = 'Première utilisation de ce site';
// Signup Page.
$string['createnewaccount'] = 'Créer un nouveau compte';
// Course Page Settings.
$string['coursesettings'] = "Page de cours";
$string['enrolpagesettings'] = "Paramètres de la page d'inscription";
$string['enrolpagesettingsdesc'] = "Gérer le contenu de la page d'inscription ici.";
$string['coursearchivepagesettings'] = 'Paramètres de la page d\'archive de cours';
$string['coursearchivepagesettingsdesc'] = 'Gérer la mise en page et le contenu de la page d\'archive de cours.';
$string['courseperpage'] = 'Cours par page';
$string['courseperpagedesc'] = 'Nombre de cours à afficher par page sur la page d\'archive de cours.';
$string['none'] = 'Aucun';
$string['fade'] = 'Fade';
$string['slide-top'] = 'Glissement vers le haut';
$string['slide-bottom'] = 'Glissement vers le bas';
$string['slide-right'] = 'Glissement vers la droite';
$string['scale-up'] = 'Agrandir';
$string['scale-down'] = 'Réduire';
$string['courseanimation'] = 'Animation de la carte de cours';
$string['courseanimationdesc'] = 'Sélectionnez l\'animation de la carte de cours à afficher sur la page d\'archive de cours';

$string['currency'] = 'EURO';
$string['currency_symbol'] = '€';
$string['enrolment_payment'] = 'Paiement du cours';
$string['enrolment_payment_desc'] = 'Paramètres pour les préférences d\'inscription aux cours. Tous les cours nécessitent-ils un paiement ou certains sont-ils gratuits ? Ce paramètre dicte comment l\'inscription au cours fonctionnera et sera affichée.';
$string['allrequirepayment'] = 'Tous les cours nécessitent un paiement';
$string['somearefree'] = 'Certains cours sont gratuits';
$string['allarefree'] = 'Tous les cours sont gratuits';

$string['showcoursepricing'] = 'Afficher les prix des cours';
$string['showcoursepricingdesc'] = 'Activer cette option pour afficher la section de prix sur la page d\'inscription.';
$string['fullwidthcourseheader'] = 'Entête du cours en pleine largeur';
$string['fullwidthcourseheaderdesc'] = 'Activer cette option pour rendre l\'entête du cours en pleine largeur.';

$string['price'] = 'Prix';
$string['course_free'] = 'GRATUIT';
$string['enrolnow'] = 'S\'inscrire maintenant';
$string['buyand'] = 'Acheter et ';
$string['notags'] = 'Aucune étiquette.';
$string['tags'] = 'Étiquettes';

$string['enrolment_layout'] = 'Disposition de la page d\'inscription';
$string['enrolment_layout_desc'] = 'Activer la disposition Edwiser pour une nouvelle conception améliorée de la page d\'inscription.';
$string['disable'] = 'Désactiver';
$string['defaultlayout'] = 'Disposition par défaut de Moodle';
$string['enable_layout1'] = 'Disposition Edwiser';

$string['webpage'] = "Page web";
$string['categorypagelayout'] = 'Disposition de la page d\'archive des cours';
$string['categorypagelayoutdesc'] = 'Choisissez entre les dispositions de la page d\'archive des cours.';
$string['edwiserlayout'] = 'Disposition Edwiser';
$string['categoryfilter'] = 'Filtre de catégorie';

$string['skill1'] = 'Débutant';
$string['skill2'] = 'Intermédiaire';
$string['skill3'] = 'Avancé';

$string['lastupdatedon'] = 'Dernière mise à jour le ';

$string['courseoverview'] = "Aperçu du cours";
$string['coursecontent'] = "Contenu du cours";
$string['instructors'] = "Instructeurs";
$string['reviews'] = "Commentaires";
$string['curatedby'] = 'Sélectionné par';
$string["studentsenrolled"] = 'Étudiants inscrits';
$string['lesson'] = 'Leçon';
$string['category'] = 'Catégorie';
$string['review'] = 'Commentaire';
$string['length'] = 'Durée';
$string['lecture'] = 'Cours magistral';
$string['startdate'] = 'Date de début';
$string['skilllevel'] = 'Niveau de compétence';
$string['language'] = 'Langue';
$string['certificate'] = 'Certificat';
$string['students'] = 'Étudiants';
$string['courses'] = 'Cours';

// Course archive.
$string['cachedef_courses'] = 'Cache pour les cours';
$string['cachedef_guestcourses'] = 'Cache pour les cours invités';
$string['cachedef_updates'] = 'Cache pour les mises à jour';
$string['mycourses'] = "Mes cours";
$string['allcategories'] = 'Toutes les catégories';
$string['categorysort'] = 'Trier les catégories';
$string['sortdefault'] = 'Trier (aucun)';
$string['sortascending'] = 'Trier de A à Z';
$string['sortdescending'] = 'Trier de Z à A';

// Frontpage Old String.
// Home Page Settings.
$string['homepagesettings'] = 'Home Page';
$string['frontpagedesign'] = 'Frontpage Design';
$string['frontpagedesigndesc'] = 'Enable Legacy Builder or Edwiser RemUI Homepage builder';
$string['frontpagechooser'] = 'Choose frontpage design';
$string['frontpagechooserdesc'] = 'Choose your frontpage design.';
$string['frontpagedesignold'] = 'Legacy Homepage Builder';
$string['frontpagedesignolddesc'] = 'Default dashboard like previous.';
$string['frontpagedesignnew'] = 'New design';
$string['frontpagedesignnewdesc'] = 'Fresh new design with multiple sections. You can configure sections individualy on frontpage.';
$string['newhomepagedescription'] = 'Click on \'Site Home\' from the Navigation bar to go to \'Homepage Builder\' and create your own Homepage';
$string['frontpageloader'] = 'Upload loader image for frontpage';
$string['frontpageloaderdesc'] = 'This replace the default loader with your image';
$string['frontpageimagecontent'] = 'Header content';
$string['frontpageimagecontentdesc'] = ' This section relates to the top portion of your frontpage.';
$string['frontpageimagecontentstyle'] = 'Style';
$string['frontpageimagecontentstyledesc'] = 'You can choose between Static & Slider.';
$string['staticcontent'] = 'Static';
$string['slidercontent'] = 'Slider';
$string['addtext'] = 'Add Text';
$string['defaultaddtext'] = 'Education is a time-tested path to progress.';
$string['addtextdesc'] = 'Here you may add the text to be displayed on the front page, preferably in HTML.';
$string['uploadimage'] = 'Upload Image';
$string['uploadimagedesc'] = 'You may upload image as content for slide';
$string['video'] = 'iframe Embedded code';
$string['videodesc'] = ' Here, you may insert the iframe Embedded code of the video that is to be embedded.';
$string['contenttype'] = 'Select Content type';
$string['contentdesc'] = 'You can choose between image or give video url.';
$string['imageorvideo'] = 'Image/ Video';
$string['image'] = 'Image';
$string['videourl'] = 'Video URL';
$string['slideinterval'] = 'Slide interval';
$string['slideintervalplaceholder'] = 'Positive integer number in milliseconds.';
$string['slideintervaldesc'] = 'You may set the transition time between the slides. In case if there is one slide, this option will have no effect. If interval is invalid(empty|0|less than 0) then default interval is 5000 milliseconds.';
$string['slidercount'] = 'No of slides';
$string['slidercountdesc'] = '';
$string['one'] = '1';
$string['two'] = '2';
$string['three'] = '3';
$string['four'] = '4';
$string['five'] = '5';
$string['six'] = '6';
$string['eight'] = '8';
$string['nine'] = '9';
$string['twelve'] = '12';
$string['slideimage'] = 'Upload images for Slider';
$string['slideimagedesc'] = 'You may upload an image as content for this slide.';
$string['sliderurl'] = 'Add link to Slider button';
$string['slidertext'] = 'Add Slider text';
$string['defaultslidertext'] = '';
$string['slidertextdesc'] = 'You may insert the text content for this slide. Preferably in HTML.';
$string['sliderbuttontext'] = 'Add Text button on slide';
$string['sliderbuttontextdesc'] = 'You may add text to the button on this slide.';
$string['sliderurldesc'] = 'You may insert the link of the page where the user will be redirected once they click on the button.';
$string['sliderautoplay'] = 'Set Slider Autoplay';
$string['sliderautoplaydesc'] = 'Select ‘yes’ if you want automatic transition in your slideshow.';
$string['true'] = 'Yes';
$string['false'] = 'No';
$string['frontpageblocks'] = 'Body Content';
$string['frontpageblocksdesc'] = 'You may insert a heading for your site’s body';
$string['frontpageblockdisplay'] = 'About Us Section';
$string['frontpageblockdisplaydesc'] = 'You can show or hide the "About Us" section, also you can show "About Us" section in grid format';
$string['donotshowaboutus'] = 'Do Not Show';
$string['showaboutusinrow'] = 'Show Section in a Row';
$string['showaboutusingridblock'] = 'Show Section in Grid Block';

// About Us.
$string['frontpageaboutus'] = 'Frontpage About us';
$string['frontpageaboutusdesc'] = 'This section is for front page About us';
$string['frontpageaboutustitledesc'] = 'Add title to About Us Section';
$string['frontpageaboutusbody'] = 'Body Description for About Us Section';
$string['frontpageaboutusbodydesc'] = 'A brief description about this Section';
$string['enablesectionbutton'] = 'Enable buttons on Sections';
$string['enablesectionbuttondesc'] = 'Enable the buttons on body sections.';
$string['sectionbuttontextdesc'] = 'Enter the text for button in this Section.';
$string['sectionbuttonlinkdesc'] = 'Enter the URL link for this Section.';
$string['frontpageblocksectiondesc'] = 'Add title to this Section.';

// Block section 1.
$string['frontpageblocksection1'] = 'Body title for 1st Section';
$string['frontpageblockdescriptionsection1'] = 'Body description for 1st Section';
$string['frontpageblockiconsection1'] = 'Font-Awesome icon for 1st Section';
$string['sectionbuttontext1'] = 'Button text for 1st Section';
$string['sectionbuttonlink1'] = 'URL link for 1st Section';

// Block section 2.
$string['frontpageblocksection2'] = 'Body title for 2nd Section';
$string['frontpageblockdescriptionsection2'] = 'Body description for 2nd Section';
$string['frontpageblockiconsection2'] = 'Font-Awesome icon for 2nd Section';
$string['sectionbuttontext2'] = 'Button text for 2nd Section';
$string['sectionbuttonlink2'] = 'URL link for 2nd Section';

// Block section 3.
$string['frontpageblocksection3'] = 'Body title for 3rd Section';
$string['frontpageblockdescriptionsection3'] = 'Body description for 3rd Section';
$string['frontpageblockiconsection3'] = 'Font-Awesome icon for 3rd Section';
$string['sectionbuttontext3'] = 'Button text for 3rd Section';
$string['sectionbuttonlink3'] = 'URL link for 3rd Section';

// Block section 4.
$string['frontpageblocksection4'] = 'Body title for 4th Section';
$string['frontpageblockdescriptionsection4'] = 'Body description for 4th Section';
$string['frontpageblockiconsection4'] = 'Font-Awesome icon for 4th Section';
$string['sectionbuttontext4'] = 'Button text for 4th Section';
$string['sectionbuttonlink4'] = 'URL link for 4th Section';
$string['defaultdescriptionsection'] = 'Holisticly harness just in time technologies via corporate scenarios.';
$string['frontpagetestimonial'] = 'Frontpage Testimonial';
$string['frontpagetestimonialdesc'] = 'Frontpage Testimonial Section';
$string['enablefrontpageaboutus'] = 'Enable Testimonial section';
$string['enablefrontpageaboutusdesc'] = 'Enable the Testimonial section in front page.';
$string['frontpageaboutusheading'] = 'Testimonial Heading';
$string['frontpageaboutusheadingdesc'] = 'Heading for the default heading text for section';
$string['frontpageaboutustext'] = 'Testimonial text';
$string['frontpageaboutustextdesc'] = 'Enter testimonial text for frontpage.';
$string['frontpageaboutusdefault'] = '<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
              Ut enim ad minim veniam.</p>';
$string['testimonialcount'] = 'Testimonial Count';
$string['testimonialcountdesc'] = 'Number of testimonials to show.';
$string['testimonialimage'] = 'Testimonial Image';
$string['testimonialimagedesc'] = 'Person image to display with testimonial';
$string['testimonialname'] = 'Person Name';
$string['testimonialnamedesc'] = 'Name of person';
$string['testimonialdesignation'] = 'Person Designation';
$string['testimonialdesignationdesc'] = 'Person\'s designation.';
$string['testimonialtext'] = 'Person\'s Testimonial';
$string['testimonialtextdesc'] = 'What person says';
$string['frontpageblockimage'] = 'Upload image';
$string['frontpageblockimagedesc'] = 'You may upload an image as content for this.';
$string['frontpageblockiconsectiondesc'] = 'You can choose any icon from this <a href="https://fontawesome.com/v4.7.0/cheatsheet/" target="_new">list</a>. Just enter the text after "fa-". ';
$string['frontpageblockdescriptionsectiondesc'] = 'A brief description about the title.';

// Course.
$string['graderreport'] = 'Rapport de notation';
$string['enroluser'] = 'Inscrire des utilisateurs';
$string['activityeport'] = 'Rapport d\'activité';
$string['editcourse'] = 'Modifier le cours';
$string['imageforcourse'] = 'Image du cours';
// Next Previous Activity.
$string['activityprev'] = 'Précédent';
$string['activitynext'] = 'Suivant';
// $string['activityprev'] = 'Activité précédente';
// $string['activitynext'] = 'Activité suivante';
$string['activitynextpreviousbutton'] = 'Activer le bouton Activité suivante et Activité précédente';
$string['activitynextpreviousbuttondesc'] = 'Lorsque activé, le bouton Activité suivante et Activité précédente apparaîtra sur la page de la seule activité pour naviguer entre les activités';
$string['disablenextprevious'] = 'Désactiver';
$string['enablenextprevious'] = 'Activer';
$string['enablenextpreviouswithname'] = 'Activer avec le nom de l\'activité';

// Importer.
$string['importer'] = 'Importer';
$string['importer-missing'] = 'Edwiser Site Importer plugin is missing. Please visit <a href="https://edwiser.org">Edwiser</a> site to download this plugin.';

// Information center.
$string['informationcenter'] = 'Information Center';
$string['licensenotactive'] = '<strong>Alert!</strong> License is not activated , please <strong>activate</strong> the license in RemUI settings.';
$string['licensenotactiveadmin'] = '<strong>Alert!</strong> License is not activated , please <strong>activate</strong> the license <a class="text-primary" href="' . $CFG->wwwroot . '/admin/settings.php?section=themesettingremui#informationcenter" >here</a>.';
$string['activatelicense'] = 'Activate License';
$string['deactivatelicense'] = 'Deactivate License';
$string['renewlicense'] = 'Renew License';
$string['deactivated'] = 'Deactivated';
$string['active'] = 'Active';
$string['notactive'] = 'Not Active';
$string['expired'] = 'Expired';
$string['licensekey'] = 'License key';
$string['licensestatus'] = 'License Status';
$string['no_activations_left'] = 'Limit exceeded';
$string['activationfailed'] = 'License Key activation failed. Please try again later.';
$string['noresponsereceived'] = 'No response received from the server. Please try again later.';
$string['licensekeydeactivated'] = 'License Key is deactivated.';
$string['siteinactive'] = 'Site inactive (Press Activate license to activate plugin).';
$string['entervalidlicensekey'] = 'Please enter valid license key.';
$string['nolicenselimitleft'] = 'Maximum activation limit reached, No activations left.';
$string['licensekeyisdisabled'] = 'Your license key is Disabled.';
$string['licensekeyhasexpired'] = "Your license key has Expired. Please, Renew it.";
$string['licensekeyactivated'] = "Your license key is activated.";
$string['entervalidlicensekey'] = "Please enter correct license key.";
$string['edwiserremuilicenseactivation'] = 'Edwiser RemUI License Activation';
$string['enterlicensekey'] = "Enter license key...";
$string['invalid'] = "Invalid";

$string['courseheaderdesign'] = 'Course page header design';
$string['courseheaderdesigndesc'] = 'Choose course page header\'s design';
$string['default'] = 'Default';
$string['headerdesign'] = 'Header design {$a}';
$string['sidebarcoursemenuheading'] = "Course Menu";

// Notification.
$string['inproductnotification'] = "Update user preferences (In-product Notification) - RemUI";

$string["noti_enrolandcompletion"] = 'The modern, professional-looking Edwiser RemUI layouts have helped brilliantly in increasing your overall learner engagement with <b>{$a->enrolment} new course enrollments and {$a->completion} course completions</b> this month';

$string["noti_completion"] = 'Edwiser RemUI has improved your student engagement levels: You have a total of <b>{$a->completion} course completions</b> this month';

$string["noti_enrol"] = 'Your LMS design looks great with Edwiser RemUI: You have <b>{$a->enrolment} new course enrollments</b> in your portal this month';

$string["coolthankx"] = "Cool, merci!";

$string['gridview'] = 'Vue en grille';
$string['listview'] = 'Vue en liste';
$string['summaryview'] = 'Vue résumée';

$string['side-top'] = "Haut latéral";
$string['content'] = "Contenu";
$string['side-bottom'] = "Bas latéral";
$string['side-pre'] = "Latéral gauche";

$string['sitenamecolor'] = "Couleur du nom ou de l'icône du site.";
$string['sitenamecolordesc'] = "Couleur du texte du nom et de l'icône du site, qui sera également appliquée sur la page de connexion.";

$string['coursesenrolled'] = "Cours inscrits";
$string['coursescompleted'] = "Cours terminés";
$string['activitiescompleted'] = "Activités terminées";
$string['activitiesdue'] = "Activités dues";

// Customizer Strings
$string['customizer-migrate-notice'] = 'Color settings are migrated to Customizer. Please click below button to open customizer.';
$string['customizer-close-heading'] = 'Close customizer';
$string['customizer-close-description'] = 'Unsaved changes will be discarded. Would you like to continue?';
$string['reset'] = 'Reset';
$string['resetall'] = 'Reset All';
$string['reset-settings'] = 'Reset all customizer settings';
$string['reset-settings-description'] = '
<div>Customizer settings will be restored to default. Do you want to continue?</div>
<div class="mt-3"><strong>Reset All:</strong> Reset all settings.</div>
<div class="mt-3"><strong>Reset:</strong> Settings except the follwing settings will be reset to default.</div>
';
$string['link'] = 'Link';
$string['customizer'] = 'Customizer';
$string['error'] = 'Error';
$string['resetdesc'] = 'Reset setting to last save or default when nothing saved';
$string['noaccessright'] = 'Sorry! You don\'t have rights to use this page';

$string['font-family'] = 'Font family';
$string['font-family_help'] = 'Set font family of {$a}';

$string['button-font-family'] = 'Font family';
$string['button-font-family_help'] = 'Set font family of button text';

$string['font-size'] = 'Font size';
$string['font-size_help'] = 'Set font size of {$a}';
$string['font-weight'] = 'Font weight';
$string['font-weight_help'] = 'Set a font weight of {$a}. The font-weight property sets how thick or thin characters in text should be displayed.';
$string['line-height'] = 'Line height';
$string['line-height_help'] = 'Set line height of {$a}';
$string['global'] = 'Global';
$string['global_help'] = 'You can manage global settings like color, font, heading, buttons etc.';
$string['site'] = 'Site';
$string['text-color'] = 'Text color';
$string['welcome-text-color'] = 'Welcome text color';
$string['text-hover-color'] = 'Text Hover color';
$string['text-color_help'] = 'Set text color of {$a}';
$string['content-color'] = 'Content color';
$string['icon-color'] = 'Icon color';
$string['icon-hover-color'] = 'Icon Hover color';
$string['icon-color_help'] = 'Set icon color of {$a}';
$string['link-color'] = 'Link color';
$string['link-color_help'] = 'Set link color of {$a}';
$string['link-hover-color'] = 'Link hover color';
$string['link-hover-color_help'] = 'Set link hover color of {$a}';
$string['typography'] = 'Typography';
$string['inherit'] = 'Inherit';
$string["weight-100"] = 'Thin 100';
$string["weight-200"] = 'Extra-Light 200';
$string["weight-300"] = 'Light 300';
$string["weight-400"] = 'Normal 400';
$string["weight-500"] = 'Medium 500';
$string["weight-600"] = 'Semi-Bold 600';
$string["weight-700"] = 'Bold 700';
$string["weight-800"] = 'Extra-Bold 800';
$string["weight-900"] = 'Ultra-Bold 900';
$string['text-transform'] = 'Text transform';
$string['text-transform_help'] = 'The text-transform property controls the capitalization of text. Set text transform of {$a}.';

$string['button-text-transform'] = 'Text transform';
$string['button-text-transform_help'] = 'The text-transform property controls the capitalization of text. Set text transform for button text';

$string["default"] = 'Default';
$string["none"] = 'None';
$string["capitalize"] = 'Capitalize';
$string["uppercase"] = 'Uppercase';
$string["lowercase"] = 'Lowercase';
$string['background-color'] = 'Background color';
$string['background-hover-color'] = 'Background Hover color';
$string['background-color_help'] = 'Set background color of {$a}';
$string['background-hover-color'] = 'Background hover color';
$string['background-hover-color_help'] = 'Set background hover color of {$a}';
$string['color'] = 'Color';
$string['customizing'] = 'Customizing';
$string['savesuccess'] = 'Saved successfully.';
$string['mobile'] = 'Mobile';
$string['tablet'] = 'Tablet';
$string['hide-customizer'] = 'Hide customizer';
$string['customcss_help'] = 'You can add custom CSS. This will be applied on all the pages of your site.';

// Customizer Global body.
$string['body'] = 'Body';
$string['body-font-family_desc'] = 'Set font family for entire site. Note if set to Standard then font from RemUI setting will be applied.';
$string['body-font-size_desc'] = 'Set base font size for entire site.';
$string['body-fontweight_desc'] = 'Set font weight for entire site.';
$string['body-text-transform_desc'] = 'Set text transform for entire site.';
$string['body-lineheight_desc'] = 'Set line height for entire site.';
$string['faviconurl_help'] = 'Favicon url';

// Customizer Global heading.
$string['heading'] = 'Heading';
$string['use-custom-color'] = 'Use custom color';
$string['use-custom-color_help'] = 'Use custom color for {$a}';
$string['typography-heading-all-heading'] = 'Headings (H1 - H6)';
$string['typography-heading-h1-heading'] = 'Heading 1';
$string['typography-heading-h2-heading'] = 'Heading 2';
$string['typography-heading-h3-heading'] = 'Heading 3';
$string['typography-heading-h4-heading'] = 'Heading 4';
$string['typography-heading-h5-heading'] = 'Heading 5';
$string['typography-heading-h6-heading'] = 'Heading 6';

// Customizer Colors.
$string['primary-color'] = 'Primary color';
$string['primary-color_help'] = 'Apply brand primary color to entire site. This color will be applied to the button, text links, On hover and for active header menu items, On hover and for active icons
    <br><b>Note:</b> Changing primary color won\'t change the button colors if you have changed the button colors via their individuals settings (<b>Global > Buttons> Button Color Settings</b>). Reset the button colors from their individual settings to change the button change by globally chaning the primary color from here ';

$string['secondary-color'] = 'Ascent color';
$string['secondary-color_help'] = 'Apply ascent color to entire site. This color will be applied to Icons on the Stats block on the Dashboard page, tags on course cards, course header banners';

$string['page-background'] = 'Page background';
$string['page-background_help'] = 'Set custom page background to page content area. You can choose color, gradient or image. Gradient color angle is 100deg.';

$string['page-background-color'] = 'Page background color';
$string['page-background-color_help'] = 'Set background color to page content area.';

$string['page-background-image'] = 'Page background image';
$string['page-background-image_help'] = 'Set image as background for page content area.';

$string['gradient'] = 'Gradient';
$string['gradient-color1'] = 'Gradient color 1';
$string['gradient-color1_help'] = 'Set first color of gradient';
$string['gradient-color2'] = 'Gradient color 2';
$string['gradient-color2_help'] = 'Set second color of gradient';
$string['gradient-color-angle'] = 'Gradient Angle';
$string['gradient-color-angle_help'] = 'Set angle for gradient colors';

$string['page-background-imageattachment'] = 'Background image attachment';
$string['page-background-imageattachment_help'] = 'The background-attachment property sets whether a background image scrolls with the rest of the page, or is fixed.';

$string['image'] = 'Image';
$string['additional-css'] = 'Additional css';
$string['left-sidebar'] = 'Left sidebar';
$string['main-sidebar'] = 'Main sidebar';
$string['sidebar-links'] = 'Sidebar links';
$string['secondary-sidebar'] = 'Secondary sidebar';
$string['header'] = 'Header';
$string['headertypography'] = 'Header typography';
$string['headercolors'] = 'Header colors';
$string['menu'] = 'Menu';
$string['site-identity'] = 'Site Identity';
$string['primary-header'] = 'Primary header';
$string['color'] = 'Color';

// Customizer Buttons.
$string['buttons'] = 'Buttons';
$string['border'] = 'Border';
$string['border-width'] = 'Border width';
$string['border-width_help'] = 'Set border width of {$a}';
$string['border-color'] = 'Border color';
$string['border-color_help'] = 'Set border color of {$a}';
$string['border-hover-color'] = 'Border hover color';
$string['border-hover-color_help'] = 'Set border hover color of {$a}';
$string['border-radius'] = 'Border radius';
$string['border-radius_help'] = 'Set border radius of {$a}';
$string['letter-spacing'] = 'Letter spacing';
$string['letter-spacing_help'] = 'Set letter spacing of {$a}';
$string['text'] = 'Text';
$string['padding'] = 'Padding';
$string['padding-top'] = 'Padding top';
$string['padding-top_help'] = 'Set padding top of {$a}';
$string['padding-right'] = 'Padding right';
$string['padding-right_help'] = 'Set padding right of {$a}';
$string['padding-bottom'] = 'Padding bottom';
$string['padding-bottom_help'] = 'Set padding bottom of {$a}';
$string['padding-left'] = 'Padding left';
$string['padding-left_help'] = 'Set padding left of {$a}';
$string['secondary'] = 'Secondary';
$string['colors'] = 'Colors';
$string['commonbuttonsettings'] = 'Common Settings';
$string['buttonsizesettings'] = 'Button Sizes';
$string['buttonsizesettingshead'] = '{$a}';
$string['commonfontsettings'] = 'Font';
$string['buttoncolorsettings'] = 'Button Color Settings';
// Customizer Header.
$string['header-background-color_help'] = 'Set background color of header. This will not be applied if <strong>Set Header Background color same as logo background color</strong> is enabled.';
$string['site-logo'] = 'Site logo';
$string['header-menu'] = 'Header menu';
$string['box-shadow-size'] = 'Box shadow size';
$string['box-shadow-size_help'] = 'Set box shadow size for site header';
$string['box-shadow-blur'] = 'Box shadow blur';
$string['box-shadow-blur_help'] = 'Set box shadow blur for site header';
$string['box-shadow-color'] = 'Box shadow color';
$string['box-shadow-color_help'] = 'Set box shadow color for site header';
$string['layout-desktop'] = 'Layout desktop';
$string['layout-desktop_help'] = 'Set header\'s layout for desktop';
$string['layout-mobile'] = 'Layout mobile';
$string['layout-mobile_help'] = 'Set header\'s layout for mobile';
$string['header-left'] = 'Left icon right menu';
$string['header-right'] = 'Right icon left menu';
$string['header-top'] = 'Top icon bottom menu';
$string['hover'] = 'Hover';
$string['logo'] = 'Logo';
$string['applynavbarcolor'] = 'Set Header Background color same as logo background color';
$string['applynavbarcolor_help'] = 'Logo background color will be applied to entire header. Changing logo background color will change background color of header. You can still apply custom text color and hover color to header menus.';
$string['header-background-color-warning'] = 'Will not be used if <strong>Set site color of navbar</strong> is enabled.';
$string['logosize'] = 'Expected aspect ratio is 130:33 for left view, 140:33 for right view.';
$string['logominisize'] = 'Expected aspect ratio is 40:33.';
$string['sitenamewithlogo'] = 'Site name with logo(Top view only)';

// Customizer Sidebar.
$string['link-text'] = 'Link text';
$string['link-text_help'] = 'Set link text color of {$a}';
$string['link-icon'] = 'Link icon';
$string['link-icon_help'] = 'Set link icon color of {$a}';
$string['active-link-color'] = 'Active link color';
$string['active-link-color_help'] = 'Set custom color to active link of {$a}';
$string['active-link-background'] = 'Active link background';
$string['active-link-background_help'] = 'Set custom color to active link background of {$a}';
$string['link-hover-background'] = 'Link hover background';
$string['link-hover-background_help'] = 'Set link hover background to {$a}';
$string['link-hover-text'] = 'Link hover text';
$string['link-hover-text_help'] = 'Set link hover text color of {$a}';

// Customizer Footer.
$string['footer'] = 'Footer';
$string['basic'] = 'Footer design';
$string['socialall'] = 'Social media links';
$string['advance'] = 'Main footer area';
$string['footercolumn'] = 'Widget';
$string['footercolumnwidgetno'] = 'Select number of widgets';
$string['footercolumndesc'] = 'Number of widgets to show in footer.';
$string['footercolumntype'] = 'Select type';
$string['footercolumnsettings'] = 'Footer Column Settings';
$string['footercolumntypedesc'] = 'You can choose footer widget type';
$string['footercolumnsocial'] = 'Social media links';
$string['footercolumnsocialdesc'] = 'Select the links to the displayed. Press and hold "ctrl" on the keyboard to select multiple links';
$string['footercolumntitle'] = 'Add title';
$string['footercolumntitledesc'] = 'Add title to this widget.';
$string['footercolumncustomhtml'] = 'Content';
$string['footercolumncustomhtmldesc'] = 'You can customize content of this widgest using below given editor.';
$string['both'] = 'Both';
$string['footercolumnsize'] = 'Adjust widget width';
$string['footercolumnsizenote'] = 'Drag vertical line to adjust widget size.';
$string['footercolumnsizedesc'] = 'You can set individual widget size.';
$string['footercolumnmenu'] = 'Menu';
$string['footercolumnmenureset'] = 'Footer Column Menus';
$string['footercolumnmenudesc'] = 'Link menu';
$string['footermenu'] = 'Menu';
$string['footermenudesc'] = 'Add menu in footer widget.';
$string['customizermenuadd'] = 'Add menu item';
$string['customizermenuedit'] = 'Edit menu item';
$string['customizermenumoveup'] = 'Move menu item up';
$string['customizermenuemovedown'] = 'Move menu item down';
$string['customizermenuedelete'] = 'Delete menu item';
$string['menutext'] = 'Text';
$string['menuaddress'] = 'Address';
$string['menuorientation'] = 'Menu orientation';
$string['menuorientationdesc'] = 'Set orientation of menu. Orientation can be either vertical or horizontal.';
$string['menuorientationvertical'] = 'Vertical';
$string['menuorientationhorizontal'] = 'Horizontal';
$string['footerfacebook'] = 'Facebook';
$string['footertwitter'] = 'Twitter';
$string['footerlinkedin'] = 'Linkedin';
$string['footergplus'] = 'Google Plus';
$string['footeryoutube'] = 'Youtube';
$string['footerinstagram'] = 'Instagram';
$string['footerpinterest'] = 'Pinterest';
$string['footerquora'] = 'Quora';
$string['footershowlogo'] = 'Show Logo';
$string['footershowlogodesc'] = 'Show logo in the secondary footer.';
$string['footersecondarysocial'] = 'Show social media links';
$string['footersecondarysocialdesc'] = 'Show social media links in the secondary footer.';
$string['footertermsandconditionsshow'] = 'Show Terms & Conditions';
$string['footertermsandconditions'] = 'Terms & Conditions Link';
$string['footertermsandconditionsdesc'] = 'You can add link for Terms & Conditions page.';
$string['footerprivacypolicyshow'] = 'Show Privacy Policy';
$string['footerprivacypolicy'] = 'Privacy Policy Link';
$string['footerprivacypolicydesc'] = 'You can add link for Privacy Policy page.';
$string['footercopyrightsshow'] = 'Show Copyrights Content';
$string['footercopyrights'] = 'Copyrights Content';
$string['footercopyrightsdesc'] = 'Add Copyrights content in the bottom of page.';
$string['footercopyrightstags'] = 'Tags:<br>[site]  -  Site name<br>[year]  -  Current year';
$string['termsandconditions'] = 'Terms & Conditions';
$string['privacypolicy'] = 'Privacy Policy';
$string['footerfont'] = 'Font';
$string['footerbasiccolumntitle'] = 'Column title';
$string['divider-color'] = 'Divider color';
$string['divider-color_help'] = 'Set divider color of {$a}';
$string['text-hover-color'] = 'Text hover color';
$string['text-hover-color_help'] = 'Set text hover color of {$a}';
$string['link-color'] = 'Link color';
$string['link-color_help'] = 'Set link color of {$a}';
$string['link-hover-color'] = 'Link hover color';
$string['link-hover-color_help'] = 'Set link hover color of {$a}';
$string['icon-default-color'] = 'Icon color';
$string['icon-default-color_help'] = 'Icon color of {$a}';
$string['icon-hover-color'] = 'Icon hover color';
$string['icon-hover-color_help'] = 'Icon hover color of {$a}';
$string['footerfontsize_help'] = 'Set font size';
$string['footer-color-heading1'] = 'Footer colors';
$string['footer-color-heading2'] = 'Footer links';
$string['footer-color-heading3'] = 'Footer icons';

$string['footerfontfamily'] = 'Font family';
$string['footerfontfamily_help'] = 'Font family';
$string['footerfontsize'] = 'Font size';
$string['footerfontsize_help'] = 'Footer font size';
$string['footerfontweight'] = 'Font weight';
$string['footerfontweight_help'] = 'Footer font weight';
$string['footerfonttext-transform'] = 'Text case';
$string['footerfonttext-transform_help'] = 'Text case';
$string['footerfontlineheight'] = 'Line spacing';
$string['footerfontlineheight_help'] = 'Line spacing';
$string['footerfontltrspace'] = 'Letter spacing';
$string['footerfontltrspace_help'] = 'Set letter spacing of {$a}';

$string['footer-columntitle-fontfamily'] = 'Font family';
$string['footer-columntitle-fontfamily_help'] = 'Font family';
$string['footer-columntitle-fontsize'] = 'Font size';
$string['footer-columntitle-fontsize_help'] = 'Footer column title font size';
$string['footer-columntitle-fontweight'] = 'Font weight';
$string['footer-columntitle-fontweight_help'] = 'Footer column title font weight';
$string['footer-columntitle-textransform'] = 'Text case';
$string['footer-columntitle-textransform_help'] = 'Text case';
$string['footer-columntitle-lineheight'] = 'Line spacing';
$string['footer-columntitle-lineheight_help'] = 'Line spacing';
$string['footer-columntitle-ltrspace'] = 'Letter spacing';
$string['footer-columntitle-ltrspace_help'] = 'Letter spacing';
$string['footer-columntitle-color'] = 'Color';
$string['footer-columntitle-color_help'] = 'Color';

$string['openinnewtab'] = 'Open in a new tab';
$string['useheaderlogo'] = 'Use the same logo from header';
$string['secondaryfooterlogo'] = 'Add a new logo';
$string['logosettings'] = 'Logo settings';
$string['loginformsettings'] = 'Login form settings';
$string['loginpagesettings'] = 'Login page settings';
$string['footersecondary'] = 'Footer bottom area';
$string['footer-columns'] = 'Footer columns';
$string['footer-columntitle-color_help'] = 'Set text color of {$a}';
$string['footer-logo-color'] = 'Select Icon or Text color';
$string['footer-logo-color_help'] = 'Select Icon or Text color';
// Customizer login.
$string['login'] = 'Login';
$string['panel'] = 'Panel';
$string['page'] = 'Page';
$string['loginbackgroundopacity'] = 'Background overlay opacity';
$string['loginbackgroundopacity_help'] = 'Apply  overlay to login page background image.';
$string['loginpanelbackgroundcolor_help'] = 'Apply background color to login panel.';
$string['loginpaneltextcolor_help'] = 'Apply text color to login panel.';
$string['loginpanelcontentcolor_help'] = 'Apply text color to login panel content.';
$string['loginpanellinkcolor_help'] = 'Apply link color to login panel.';
$string['loginpanellinkhovercolor_help'] = 'Apply link hover color to login panel.';
$string['login-panel-position'] = 'Login panel position';
$string['login-panel-position_help'] = 'Set position for login and registration panel';
$string['login-page-info'] = '<p><b>Note: </b>The login page cannot be previewed in customizer because logged-out users can only view it. You can test the setting by saving and opening the login page in incognito mode.</p>';
$string['login-page-setting'] = 'Page background style';
$string['login-page-backgroundgradient1'] = 'Select Color 1';
$string['login-page-backgroundgradient2'] = 'Select Color 2';
$string['loginpanelbackgroundcolor'] = 'Page background Color';
$string['loginpagebackgroundcolor'] = 'Select background Color';
$string['loginpagebackgroundcolor_help'] = 'Set Login page background. You can choose color, gradient or image.';
$string['login-page-background_help'] = 'Apply background color to login panel';

/*Customizer Strings*/
$string['primary'] = 'Primary';

$string['dashboardsettingdesc'] = 'Dashboard related settings';
$string['dashboardsetting'] = 'Dashboard';
$string['dashboardpage'] = 'Dashboard page';
$string['enabledashboardcoursestats'] = 'Enable Dashboard Course Stats';
$string['enabledashboardcoursestatsdesc'] = 'If enabled, will show course stats on dashboard page';

$string['customizecontrolsclose'] = "Customizer close button";

// Quick setup customizer.
$string['quicksetup'] = 'Quick setup';
$string['pallet'] = 'Pallete';
$string['colorpallet'] = 'Color palettes';
$string['currentpallet'] = 'Current Pallete';
$string['currentfont'] = 'Current font';
$string['colorpalletdesc'] = 'Color palettes description';
$string['preset1'] = 'Preset 1';
$string['preset2'] = 'Preset 2';
$string['sitefavicon'] = 'Site favicon';

$string['themecolors'] = 'Theme colors';
$string['brandcolors-heading'] = 'Brand colors';
$string['border-color'] = 'Border color';
$string['border-hover-color'] = 'Border Hover color';
$string['smart-colors-heading'] = "Apply global colors";
$string['smart-colors-info'] = "<p>The global colors and its shades/ tints will be applied to the site to create a visually stunning color combination</p><p><b>Note: </b>You have the flexibility to personalize the colors of individual elements at any time by simply visiting their specific settings.</p>";
$string['apply'] = "Apply";
$string['backgroundsettings'] = 'Background settings';

$string['ascent-background-color'] = 'Ascent background color';
$string['ascent-background-color_help'] = 'Set the Ascent background color. This color will be applied to background of the tags on the site except for the tags on the course cards and course header banner';
$string['element-background-color'] = 'Element background color';
$string['element-background-color_help'] = 'Set the Element background color. This color is applied to the backgound for small text, background on hover for dropdown texts, background of section headers , tooltips etc';

$string['light-border-color'] = 'Light border color';
$string['themecolors-lightbordercolor_help'] = 'Set the Light border color. This color is applied as Border to elements with White backgrounds like Notification dropdown on header, Course Cards, search for course dropdown and on divider lines on the block elements etc';

$string['medium-border-color'] = 'Medium border color';
$string['themecolors-mediumbordercolor_help'] = 'Set the  Medium border color. This color is applied as the Border color and divider color. It is spefically applied as border color for Dropdowns and search box and also to elements background for whom the element background color is applied (You can find the Element background color setting under <b>Theme Colors > Background settings</b>)  for examples like background for small text, background of section headers , tooltips etc';
$string['borderssettings'] = 'Borders settings';

// Quick Menu settings.
$string['enablequickmenu'] = 'Enable Quick menu';
$string['enablequickmenudesc'] = 'Quick links floating menu for easier access to pages.';

// Left Navigation Drawer.
$string['createarchivepage'] = 'Course Archive Page';
$string['createanewcourse'] = 'Create A New Course';
$string['remuisettings'] = 'RemUI Settings';

$string['bodysettingslinking'] = 'Link Advance settings';
$string['bodysettingslinking_help'] = 'When enabled, settings from Small Paragraph and Small Info Text will be linked with body settings.';
$string['bodysettingslinked'] = 'Linked with body settings';
$string['normal-para-font'] = "Normal paragraph";
$string['smallpara-font'] = "Small paragraph";
$string['smallinfo-font'] = "Small info text";

$string['interactiveicons'] = 'Interactive icons';
$string['noninteractiveicons'] = 'Non-interactive icons';
$string['singlecolorsicon'] = "Single colors icon";
$string['scicon-color'] = 'Color';
$string['scicon-color_help'] = 'Single-color-icon rest state color';
$string['scicon-hover'] = 'Hover';
$string['scicon-hover_help'] = 'Single-color-icon hover state color';
$string['scicon-active'] = 'Active';
$string['scicon-active_help'] = 'Single-color-icon active state color';

$string['dualcolorsicon'] = "Dual colors icon";
$string['dcicon-color'] = 'Color';
$string['dcicon-color_help'] = 'Dual-color-icon rest state color';
$string['dcicon-hover'] = 'Hover';
$string['dcicon-hover_help'] = 'Dual-color-icon hover state color';
$string['dcicon-active'] = 'Active';
$string['dcicon-active_help'] = 'Dual-color-icon active state color';

$string['non-interactive-color'] = 'Color';
$string['non-interactive-color_help'] = 'Non interactive icon color';
$string['textlink'] = 'Text link';

$string['header-logo-setting'] = 'Header logo settings';
$string['logo-bg-color'] = 'Logo background color';
$string['logo-bg-color_help'] = 'Set background color to header brand logo.';
$string['header-design-settings'] = 'Header design settings';
$string['hide-show-menu-item'] = 'Hide/Show menu item';
$string['hide-dashboard'] = 'Hide Dashboard';
$string['hide-dashboard_help'] = 'By enabling this, Dashboard item from header will be hidden';
$string['hide-home'] = 'Hide Home';
$string['hide-home_help'] = 'By enabling this, Home item from header will be hidden';
$string['hide-my-courses'] = 'Hide My Courses';
$string['hide-my-courses_help'] = 'By enabling this, My courses and nested course items from header will be hidden';
$string['hide-site-admin'] = 'Hide Site Administration';
$string['hide-site-admin_help'] = 'By enabling this, Site Administration item from header will be hidden';
$string['hide-recent-courses'] = 'Hide Recent Courses';
$string['hide-recent-courses_help'] = 'By enabling this, Recent Courses dropdown from header will be hidden';
$string['header-menu-element-bg-color'] = 'Element background color';
$string['header-menu-element-bg-color_help'] = 'Element background color';
$string['header-menu-divider-bg-color'] = 'Element divider color';
$string['header-menu-divider-bg-color_help'] = 'Element divider color';
$string['hds-iconcolor'] = 'Header icon color';
$string['hds-boxshadow'] = 'Header box shadow';

$string['hds-menuitems'] = 'Header menu items';
$string['hds-menu-fontsize_desc'] = 'Set font size for header menu items';
$string['hds-menu-color'] = 'Menu item color';
$string['hds-menu-color_desc'] = 'Set header menu item color';
$string['hds-menu-hover-color'] = 'Menu item hover color';
$string['hds-menu-hover-color_desc'] = 'Set header menu item hover color';
$string['hds-menu-active-color'] = 'Menu item active color';
$string['hds-menu-active-color_desc'] = 'Set header menu item active color';

$string['hds-icon-color'] = 'Icons color';
$string['hds-icon-color_help'] = 'Header menu icons color';
$string['hds-icon-hover-color'] = 'Icons hover color';
$string['hds-icon-hover-color_help'] = 'Header menu icons hover color';
$string['hds-icon-active-color'] = 'Icons active color';
$string['hds-icon-active-color_help'] = 'Header menu icons color active state color';

$string['preset1'] = "Preset 1";
$string['preset2'] = "Preset 2";
$string['preset3'] = "Preset 3";
$string['fonts'] = "Fonts";
$string['show'] = "Show";
$string['hide'] = "Hide";

$string['other-bg-color'] = 'Other background colors';
$string['text-link-panel'] = 'Text link';
$string['colorpalletes'] = 'Color palettes';
$string['selectpallete'] = 'Select palette';
$string['selectfont'] = 'Select font';

$string['socialiconspanel'] = "Social icons panel";
$string['social-icons-info'] = "<p>To display the social media icons at the bottom on any column with content, go to <b>Footer > Footer Main Area > Widget > Select type = Content </b> and turn on the show social media icons setting.</p>";
$string['social-icons-heading'] = "Social media icons";
$string["custommenulinktext"] = 'Custom menu items';
$string["custommenulink"] = '<h6>Custom menu items</h6><p> To Add / Edit / Delete custom menu items go to Site Administration > Appearance > Theme Settings > <a href="{$a}/admin/settings.php?section=themesettings#admin-custommenuitems" target ="_blank" class="text-decoration-none">Custom menu items</a> <p>';
$string['note'] = 'Note';
$string['social-media-selection-note'] = "<p>Press Ctrl to select/deselect the media</p>";

$string['editmodeswitch'] = "Switcher en mode Édition";
$string['continue'] = 'Continuer';
$string['viewcourse'] = 'Voir le cours';
$string['hiddencourse'] = 'Cours caché';
$string['openquickmenu'] = 'Ouvrir le menu rapide';
$string['closequickmenu'] = 'Fermer le menu rapide';
$string['start'] = 'Commencer';

$string['readmore'] = 'Lire la suite';
$string['readless'] = 'Lire moins';
$string['setting'] = 'Paramètres';
$string['lastaccess'] = 'Dernier accès ';
$string['certificate'] = 'Certificats';
$string['badge'] = 'Insignes';
$string['firstname'] = 'Prénom';
$string['lastname'] = 'Nom de famille';
$string['badgefrom'] = 'Insignes de {$a}';
$string['timelinenoevenettext'] = 'Aucune activité à venir';
$string['description'] = 'Description';
$string['instructorcounttitle'] = "Enseignants supplémentaires disponibles dans le cours";

$string['searchtotalcount'] = "résultats trouvés";
