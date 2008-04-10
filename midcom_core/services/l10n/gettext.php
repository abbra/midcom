<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * HTTP Basic authentication service for MidCOM
 *
 * @package midcom_core
 */
class midcom_core_services_l10n_gettext implements midcom_core_services_l10n
{
    /**
     * Simple constructor.
     * 
     * @access public
     */
    public function __construct(&$configuration = array())
    {
    }
    
    /**
     * Get a translated string
     * 
     * @access public
     * @param String $string  Requested translation string
     * @return String         Translated string or the original request on failure
     */
    public function get($string)
    {
        return _($string);
    }
}
?>