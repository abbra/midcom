<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Midgard2 dispatcher for MidCOM 3
 *
 * Dispatches Midgard HTTP requests to components.
 *
 * @package midcom_core
 */
class midcom_core_services_dispatcher_midgard2 extends midcom_core_services_dispatcher_midgard implements midcom_core_services_dispatcher
{
    public function __construct()
    {
        if (!extension_loaded('midgard2'))
        {
            throw new Exception('Midgard 2.x is required for this MidCOM setup.');
        }
        
        if (isset($_GET))
        {
            $this->get = $_GET;
        }

        $this->argv = $_MIDGARD_CONNECTION->request_config->argv;
    }
    
    /**
     * Pull data from currently loaded page into the context.
     */
    public function populate_environment_data()
    {
        $page_data = array();

        $prefix = "{$_MIDGARD_CONNECTION->request_config->host->prefix}/";
        foreach ($_MIDGARD_CONNECTION->request_config->pages as $page)
        {
            if ($page->id != $_MIDGARD_CONNECTION->request_config->host->root)
            {
                $prefix = "{$prefix}{$page->name}/";
            }
            $current_page = $page;
        }
        
        $page_data['guid'] = $current_page->guid;
        $page_data['title'] = $current_page->title;
        $page_data['content'] = $current_page->content;

        $_MIDCOM->context->component = $current_page->component;
        
        $_MIDCOM->context->page = $page_data;
        $_MIDCOM->context->prefix = $prefix;
        
        // Append styles from context
        $_MIDCOM->templating->append_style($_MIDGARD_CONNECTION->request_config->style->id);
        $_MIDCOM->templating->append_page($current_page->id);
        
        // Populate page to toolbar
        $this->populate_node_toolbar();
    }
}
?>