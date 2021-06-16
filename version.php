<?php

/**
 * Autobackups deletion/extraction tool
 * See: https://moodle.org/mod/forum/discuss.php?d=219826
 *
 * @package    local
 * @subpackage tool
 * @author     Jean Pierre Ducassou (ducassou@ort.edu.uy)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2021061600;
$plugin->requires = 2010112400;
$plugin->component = 'tool_autobackups';       // Full name of the plugin (used for diagnostics)
$plugin->release = '1.0.0';
$plugin->maturity = MATURITY_STABLE;

