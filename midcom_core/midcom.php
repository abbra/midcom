<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * MidCOM core class
 *
 * @package midcom_core
 */
class midcom_core_midcom
{
    // Services
    public $authentication;
    public $authorization;
    public $configuration;
    public $componentloader;
    public $dispatcher;
    public $templating;

    // Helpers
    public $navigation;
    public $context;
    public $timer = false;
    
    
    public function __construct($dispatcher = 'midgard')
    {
        // Register autoloader so we get all MidCOM classes loaded automatically
        spl_autoload_register(array($this, 'autoload'));

        // Load the request dispatcher
        $dispatcher_implementation = "midcom_core_services_dispatcher_{$dispatcher}";
        $this->dispatcher = new $dispatcher_implementation();
        
        $this->load_base_services();
        $this->context->create();
        
        date_default_timezone_set($this->configuration->get('default_timezone'));
    }
    
    /**
     * Load all basic services needed for MidCOM usage. This includes configuration, authorization and the component loader.
     */
    public function load_base_services()
    {   
        // Load the configuration loader and load core config
        $this->configuration = new midcom_core_services_configuration_yaml('midcom_core');
        
        $use_timer = $this->configuration->get('enable_benchmark');
        if ($use_timer)
        {
            // Note: PEAR is not E_STRICT compatible
            error_reporting(E_ALL);
            require_once 'Benchmark/Timer.php';
            $this->timer = new Benchmark_Timer(true);
        }

        // Load the preferred authentication implementation
        $services_authentication_implementation = $this->configuration->get('services_authentication');
        $this->authentication = new $services_authentication_implementation();
      
        // Load the preferred authorization implementation
        $services_authorization_implementation = $this->configuration->get('services_authorization');
        $this->authorization = new $services_authorization_implementation();
        
        // Load the preferred templating implementation
        $services_templating_implementation = $this->configuration->get('services_templating');
        $this->templating = new $services_templating_implementation();
        
        // Load the component loader
        $this->componentloader = new midcom_core_component_loader();
        
        // Load the context helper
        $this->context = new midcom_core_helpers_context();
        
        //Load the service loader
        $this->serviceloader = new midcom_core_services_loader();
        
        // Load the navigation helper
        //$this->navigation = new midcom_core_helpers_navigation();
        
        // Load the head helper
        $this->head = new midcom_core_helpers_head
        (
            $this->configuration->get('enable_jquery_framework'),
            $this->configuration->get('enable_js_midcom'),
            $this->configuration->get('js_midcom_config')
        );
    }
    
    /**
     * Automatically load missing class files
     *
     * @param string $class_name Name of a missing PHP class
     */
    public function autoload($class_name)
    {
        if (class_exists($class_name))
        {
            return;
        }
        
        $path = str_replace('_', '/', $class_name) . '.php';
                
        // FIXME: Do not check against component names (ie make phing build script to build correct file tree from source)
        $path = MIDCOM_ROOT . '/' . str_replace('midcom/core', 'midcom_core', $path);
        if (   isset($_MIDCOM)
            && isset($_MIDCOM->componentloader))
        {
            $components = array_keys($_MIDCOM->componentloader->manifests);
            foreach ($components as $component)
            {
                $component_path = str_replace('_', '/', $component);
                $path = str_replace($component_path, $component, $path);
            }
        }

        if (file_exists($path))
        {
            require($path);
        }
    }
    
    /**
     * Process the current request, loading the page's component and dispatching the request to it
     */
    public function process()
    {
        if ($this->timer)
        {
            $this->timer->setMarker('MidCOM::process');
        }

        $toolbar = new midcom_core_services_toolbars($this->configuration->get('toolbar_configuration'));
        $this->toolbar = $toolbar->get_implementation();

        $_MIDCOM->templating->append_directory(MIDCOM_ROOT . '/midcom_core/templates');        
        $this->dispatcher->populate_environment_data();
        try
        {
            $component = $this->context->get_item('component');
        }
        catch (Exception $e)
        {
            return;
        }
        
        if (!$component)
        {
            $component = 'midcom_core';
            //if (!empty($this->dispatcher->argv))
            //{
                // FIXME: Process these also in the dispatcher as we will have some "core" routes
            //    throw new midcom_exception_notfound("Page not found.");
            //}
            //return;
        }
        
        $this->dispatcher->initialize($component);
        
        try
        {
            $this->dispatcher->dispatch();
        }
        catch (midcom_exception_unauthorized $exception)
        {
            // Pass the exception to authentication handler
            $_MIDCOM->authentication->handle_exception($exception);
        }

        header('Content-Type: ' . $this->context->get_item('mimetype'));
        if ($this->timer)
        {
            $this->timer->setMarker('MidCOM::process ended');
        }
    }
}
?>