<?php

define('CLI_SCRIPT', true);

// =======================================================================
// Error reporting
// =======================================================================
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// =======================================================================
// Constants
// =======================================================================
define('KB', 1024);
define('MB', 1048576);

// =======================================================================
require(__DIR__ . '/../../../../config.php');

// =======================================================================
global $CFG;
require_once($CFG->libdir  . '/dmllib.php');
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/lib/datalib.php');

// =======================================================================
// =======================================================================
# $dst_dir = $CFG -> backup_auto_destination;

// =======================================================================
// =======================================================================
$options = getopt("qxcd:");
$config = get_config('backup');

if (array_key_exists('c', $options)) {
    if (array_key_exists('d', $options) && !empty($options['d'])) {
        $dst_dir = $options['d'];
    } else {
        $dst_dir = $config->backup_auto_destination;
    }
}

// =======================================================================
// =======================================================================
global $DB;

$sql = "SELECT file.id, file.contextid, file.itemid, filepath, filename, filesize,
	  file.timemodified AS mtime, cse.id AS course_id, cse.fullname
	      FROM {files} AS file
	 LEFT JOIN {context} ctx on (ctx.id = file.contextid and ctx.contextlevel = 50)
	 LEFT JOIN {course}  cse on (ctx.instanceid = cse.id)
	     WHERE component = 'backup' 
	       AND filearea = 'automated' 
	       AND filename LIKE '%.mbz' 
	  ORDER BY filesize DESC";

$sth = $DB->get_records_sql($sql);

$fs = get_file_storage();
if ($sth) {
    foreach ($sth as $id => $row) {

        if (!array_key_exists('q', $options)) {
            printf("| %6d | % 7.2f | %s | %s \n", $row->id, $row->filesize / MB,
                date('d/m/Y H:i:s', $row->mtime), $row->fullname);
        }

        $file = $fs->get_file($row->contextid, 'backup', 'automated', $row->itemid, $row->filepath, $row->filename);
        if ($file) { // file found

            if (array_key_exists('c', $options)) {
                // Copy file
                if ($file->copy_content_to($dst_dir . '/' . $file->get_filename())) {
                    echo "File sent to $dst_dir:  " . $file->get_filename() . "\n";
                } else {
                    echo 'Could not copy file:    ' . $file->get_filename() . "\n";
                }
            }

            if (array_key_exists('x', $options)) {
                // Delete file
                if ($file->delete()) {
                    echo 'File sent to trash:    ' . $file->get_filename() . "\n";
                } else {
                    echo 'Could not delete file: ' . $file->get_filename() . "\n";
                }
            }
        }
    }
} else {
    echo 'No automated backup file to purge was found.' . PHP_EOL;
}


// =======================================================================
// Set fileslastcleanup far in the past to force trash emptying
if (array_key_exists('x', $options)) {
    set_config('fileslastcleanup', time() - 60 * 60 * 24 * 5);
}

exit(0);