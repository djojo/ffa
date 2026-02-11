<?php
namespace theme_remui\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Tâche planifiée : Envoi quotidien des résultats vers l'API FFA
 */
class ffa_daily_export extends \core\task\scheduled_task {

    public function get_name() {
        return 'FFA API - Envoi automatique quotidien des resultats';
    }

    public function execute() {
        global $CFG;

        mtrace('=== FFA API EXPORT START ===');

        $_GET['token'] = 'ffa_export_secret_2025';
        ob_start();
        require($CFG->dirroot . '/theme/remui/views/exports/auto_export_email.php');
        $output = ob_get_clean();

        mtrace($output);
        mtrace('=== FFA API EXPORT END ===');
    }
}