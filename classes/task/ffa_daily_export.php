<?php
namespace theme_remui\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Tâche planifiée : Export CSV quotidien + email automatique
 */
class ffa_daily_export extends \core\task\scheduled_task {
    
    public function get_name() {
        return 'FFA CSV Export - Envoi automatique quotidien';
    }
    
    public function execute() {
        global $CFG;
        
        mtrace('=== FFA CSV EXPORT START ===');
        
        $_GET['token'] = 'ffa_export_secret_2025';
        ob_start();
        require($CFG->dirroot . '/theme/remui/views/exports/auto_export_email.php');
        $output = ob_get_clean();
        
        mtrace($output);
        mtrace('=== FFA CSV EXPORT END ===');
    }
}