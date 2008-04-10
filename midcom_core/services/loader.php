<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

class midcom_core_services_loader
{
    private $services = array();
    
    public function __construct()
    {
        
    }
    
    public function &load($name, &$configuration=null)
    {
        if (isset($this->services[$name]))
        {
            return $this->services[$name];
        }
        
        $interface_file = MIDCOM_ROOT . "/midcom_core/services/{$name}.php";
        if (!file_exists($interface_file))
        {
            throw new Exception("Service {$name} not installed");
        }
        
        if (!class_exists("midcom_core_services_{$name}"))
        {
            //echo "midcom_core_services_{$name}\n<br />";
            //include($interface_file);
        }
        
        $services_implementation = $_MIDCOM->configuration->get("services_{$name}");
        if (!$services_implementation)
        {
            throw new Exception("No implementation defined for service {$name}");
        }
        
        if (! is_null($configuration))
        {
            $this->services[$name] = new $services_implementation(&$configuration);
        }
        else
        {
            $this->services[$name] = new $services_implementation();
        }
        
        return $this->services[$name];
    }
}

?>