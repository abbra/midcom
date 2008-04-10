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
class midcom_core_controllers_page extends midcom_core_controllers_baseclasses_manage
{
    public function __construct($instance)
    {
        $this->configuration = $_MIDCOM->configuration;
    }
    
    public function load_object($args)
    {
        if (!isset($_MIDGARD['page']))
        {
            throw new midcom_exception_notfound("No Midgard page found");
        }
        
        $this->object = new midgard_page();
        $this->object->get_by_id($_MIDGARD['page']);
    }
    
    public function get_object_url()
    {
        return $_MIDGARD['self'];
    }
    
    public function populate_toolbar()
    {
    $_MIDCOM->toolbar->add_item
        (
            'node', 
            'edit', 
            array
            (
                'label' => 'edit page',            
                'route_id' => 'page_edit',
                'icon' => 'edit',
                'enabled' => $_MIDCOM->authorization->can_do('midgard:update', $this->object),
            )
        );
        
        $_MIDCOM->toolbar->add_item
        (
            'node', 
            'create', 
            array
            (
                'label' => 'create subpage',
                'route_id' => 'page_create',
                'icon' => 'new-html',
                'enabled' => $_MIDCOM->authorization->can_do('midgard:create', $this->object),
            )
        );
        
        $_MIDCOM->toolbar->add_item
        (
            'node', 
            'delete', 
            array
            (
                'label' => 'delete page',
                'route_id' => 'page_delete',
                'icon' => 'trash',
                'enabled' => $_MIDCOM->authorization->can_do('midgard:delete', $this->object),
            )
        );
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
}
?>