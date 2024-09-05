<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     smartch
 * @author      Geoffroy Rouaix
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once('./utils.php');

// defined('MOODLE_INTERNAL') || die();

require_login();

global $USER, $DB, $CFG;

echo '<div id="page" style="text-align: center;">

<div style="padding:50px 0;display:flex;align-items:center;justify-content:center;">
    
</div>

<h1 class="FFABold" style="text-transform:uppercase; color:#004685;letter-spacing:2px;padding:0 20px;">La plateforme est actuellement en maintenance</h1>
<h3 class="FFALight" style="color:#004685;">Elle sera de nouveau accessible prochainement.</h3>
</div>';
