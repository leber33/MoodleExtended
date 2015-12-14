<?php
require_once($CFG->libdir . "/externallib.php");

class local_cohortenrol_external extends external_api {

    
    public static function enrol_cohortsync($data){
    global $CFG, $DB;
    $params = self::validate_parameters(self::enrol_cohortsync_parameters(), array('data'=>$data));
    
    $insertids = array();
    foreach ($params['data'] as $d) {
    $d = (object)$d;
    $record = new stdClass();
    
    $record->enrol = "cohort";
    $record->status = 0;
    $record->courseid = $d->courseid;
    $record->sortorder = 0;
    $record->name = "CohortSyncViaService";
    $record->customint1 = $d->cohortid;
    $record->roleid = $d->roleid;

    $d->id = $DB->insert_record('enrol', $record, true, false);
    unset($record);
    $insertids[] = (array)$d;
    }
    $cmd = "php " .  $CFG ->dirroot . "/enrol/cohort/cli/sync.php";
    shell_exec($cmd);

    return $insertids;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    
    
    public static function enrol_cohortsync_parameters() {
        return new external_function_parameters(
            array(
                "data" => new external_multiple_structure(
                    new external_single_structure(
                        array( 
                            'courseid' => new external_value(PARAM_INT, 'ID of course'), 
                            'roleid' => new external_value(PARAM_INT, 'Role in Course'), 
                            'cohortid' => new external_value(PARAM_INT, 'CohortID')
                        ) 
                    )
                )
            )               
        );
    }
    
    
    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function enrol_cohortsync_returns() {
            return new external_multiple_structure(
                new external_single_structure(
                    array(
                       'id' => new external_value(PARAM_INT, 'IDs of new Enrolements'),
                    )
                )
            );
    }
}
