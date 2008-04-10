<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Page management controller
 *
 * @package midcom_core
 */
class midcom_core_controllers_page
{
    public function __construct($instance)
    {
        $this->configuration = $_MIDCOM->configuration;
    }
    
    public function action_show($route_id, &$data, $args)
    {
    }
    
    public function action_edit($route_id, &$data, $args)
    {
        if (!isset($_MIDGARD['page']))
        {
            throw new midcom_exception_notfound("No Midgard page found");
        }
        
        $data['page'] = new midgard_page();
        $data['page']->get_by_id($_MIDGARD['page']);
        
        $_MIDCOM->authorization->require_do('midgard:update', $data['page']);
        
        // Load the page via Datamanager for configurability
        $_MIDCOM->componentloader->load('midcom_helper_datamanager');
        $dm = new midcom_helper_datamanager_datamanager($this->configuration->get('schemadb_default'));
        $dm->autoset_storage($data['page']);
        $data['page_dm'] =& $dm;

        // Handle saves through the datamanager
        $data['page_dm_form'] =& $data['page_dm']->get_form('simple');
        try
        {
            $data['page_dm_form']->process();
        }
        catch (midcom_helper_datamanager_exception_datamanager $e)
        {
            $uimessages = $_MIDCOM->serviceloader->load('uimessages');
            $uimessages->add(array(
                'title' => 'Error while editing page', //TODO: Localization
                'message' => $e->getMessage(),
                'type' => 'error'
            ));
            
            header("Location: {$_MIDCOM->context->prefix}");
            exit();
        }
    }
    
    public function action_create($route_id, &$data, $args)
    {
        if (!isset($_MIDGARD['page']))
        {
            throw new midcom_exception_notfound("No Midgard page found");
        }
        $data['parent'] = new midgard_page();
        $data['parent']->get_by_id($_MIDGARD['page']);
        
        $data['page'] = new midgard_page();
        $data['page']->up = $data['parent']->id;
        
        $_MIDCOM->authorization->require_do('midgard:create', $data['parent']);
        
        if (isset($_POST['save']))
        {
            $data['page']->name = $_POST['name'];
            $data['page']->title = $_POST['title'];
            $data['page']->content = $_POST['content'];
            $data['page']->info = 'active';
            $data['page']->create();
            
            header("Location: {$_MIDCOM->context->prefix}{$data['page']->name}/");
            exit();
        }
    }
    
    public function action_delete($route_id, &$data, $args)
    {
        if (!isset($_MIDGARD['page']))
        {
            throw new midcom_exception_notfound("No Midgard page found");
        }
        $data['page'] = new midgard_page();
        $data['page']->get_by_id($_MIDGARD['page']);
        
        if (!$data['page']->up)
        {
            // Root page, disable deletion?
            $uimessages = $_MIDCOM->serviceloader->load('uimessages');
            $uimessages->add
            (
                array
                (
                    'type' => 'warning',
                    'title' => 'MidCOM',
                    'message' => 'Disallowing deletion of the website root page',
                )
            );
            header("Location: {$_MIDGARD['prefix']}/");
            exit();
        }
        
        $data['parent'] = new midgard_page();
        $data['parent']->get_by_id($data['page']->up);
        
        $_MIDCOM->authorization->require_do('midgard:delete', $data['page']);
        if(isset($_POST['delete']))
        {
            $data['page']->delete();
            header("Location: {$_MIDGARD['prefix']}/"); // TODO: This needs a better redirect
            exit();     
        }
    
    }
}
?>