<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Base class for object management controller. Extend this to easily implement the regular view/edit/delete cycle
 *
 * @package midcom_core
 */
abstract class midcom_core_controllers_baseclasses_manage
{
    /**
     * The actual MgdSchema object to be managed by the controller.
     */
    protected $object = null;
    
    /**
     * Datamanager instance
     */
    protected $datamanager = null;

    /**
     * Method for loading the object to be managed. To be overridden in the actual controller.
     */
    abstract public function load_object($args);
    
    /**
     * Method for generating route to the object
     *
     * @return string Object URL
     */
    abstract public function get_object_url();
    
    public function load_datamanager(&$data, $schemadb)
    {
        // Load the article via Datamanager for configurability
        $_MIDCOM->componentloader->load('midcom_helper_datamanager');
        
        $this->datamanager = new midcom_helper_datamanager_datamanager($schemadb);
        $this->datamanager->autoset_storage($this->object);
        
        $data['datamanager'] =& $this->datamanager;
    }

    public function action_show($route_id, &$data, $args)
    {
        $this->load_object($args);
        $this->load_datamanager($data, $this->configuration->get('schemadb'));
        $data['object'] =& $this->object;
    }
    
    public function action_edit($route_id, &$data, $args)
    {
        $this->load_object($args);
        $this->load_datamanager($data, $this->configuration->get('schemadb'));
        $data['object'] =& $this->object;
        
        $_MIDCOM->authorization->require_do('midgard:update', $this->object);
        
        // Handle saves through the datamanager
        $data['datamanager_form'] =& $this->datamanager->get_form('simple');
        try
        {
            $data['datamanager_form']->process();
        }
        catch (midcom_helper_datamanager_exception_datamanager $e)
        {
            // TODO: add uimessage of $e->getMessage();
            header('Location: ' . $this->get_object_url());
            exit();
        }
    }
        
    public function action_delete($route_id, &$data, $args)
    {
        $this->load_object($args);
        $this->load_datamanager($data, $this->configuration->get('schemadb'));
        $data['object'] =& $this->object;
        
        $_MIDCOM->authorization->require_do('midgard:delete', $this->object);
        if(isset($_POST['delete']))
        {
            $this->object->delete();
            header("Location: {$_MIDGARD['prefix']}/"); // TODO: This needs a better redirect
            exit();     
        }
    }
}
?>