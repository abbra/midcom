<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

 /**
  * Localization interface
  *
  * @package midcom_core
  */
interface midcom_core_services_l10n
{
    /**
     * @param &$configuration  Configuration for the current localization type
     */
    public function __construct(&$configuration = array());
    
    /**
     * Get translation for the requested string
     * 
     * @return String
     */
    public function get($string);
}
?>