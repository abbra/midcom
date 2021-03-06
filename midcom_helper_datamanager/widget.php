<?php
/**
 * @package midcom_helper_datamanager
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * FIXME: Since we're not using quickform anymore due to it's many annoyances and limitations
 * we need to make bunch of small helpers for escaping values to safe for html/tal, probably
 * handiest way to use them is the have them as methods in the baseclass
 */

/**
 * Datamanager Data Type interface.
 *
 *
 * @package midcom_helper_datamanager
 */
interface midcom_helper_datamanager_widget_interface
{
    /**
     * Initializes and configures the widget.
     *
     * @see midcom_helper_datamanager_widget_baseclass::__construct
     */
    function initialize($name, $config, &$schema, &$type, $namespace);

    /**
     * Set the form reference.
     *
     * @see midcom_helper_datamanager_widget_baseclass::set_form
     */
    function set_form(&$form);

    /**
     * This event handler is called if and only if the Formmanager detects an actual
     * form submission (this is tracked using a hidden form member). 
     * 
     * No Form validation has been done at this point. The event is triggered on all 
     * submissions with the exception of the cancel and previous form events.
     *
     * You should be careful when using this event for data
     * processing therefore. Its main application is the processing of additional buttons
     * placed into the form by the widget.
     *
     * The implementation of this handler is optional.
     *
     * @param mixed $result data we got from form, relevant to widget as indicated by main_input_name, or null
     *      if we do not have relevant data.
     *
     * @see midcom_helper_datamanager_widget_baseclass::on_submit
     */
    function on_submit($result);

    /**
     * This function is invoked if the widget should extract the corresponding data
     * from the form result passed in $result. 
     * 
     * @param mixed $result data we got from form, relevant to widget as indicated by main_input_name
     *
     * @see midcom_helper_datamanager_widget_baseclass::sync_type_with_widget
     */
    function sync_widget2type($result);

    /**
     * When called, this method should display the current data without any
     * editing widget or surrounding braces in plain and simple HTML.
     *
     * The default implementation calls the type's convert_to_html method.
     *
     * @see midcom_helper_datamanager_widget_baseclass::render_content
     */
    // TODO: rethink, good idea in principle but needs to be compatible with the $form->widgets->fieldname->as_html approach
    function render_content();
    
    /**
     * Freezes all form elements associated with the widget. 
     * 
     * The default implementation works on the default field name, you don't need to override 
     * this function unless you have multiple widgets in the form.
     *
     * @see midcom_helper_datamanager_widget_baseclass::freeze
     */
    function freeze();

    /**
     * Unfreezes all form elements associated with the widget. 
     * 
     * The default implementation works on the default field name, you don't need to override 
     * this function unless you have multiple widgets in the form.
     *
     * @see midcom_helper_datamanager_widget_baseclass::unfreeze
     */
    function unfreeze();

    /**
     * Checks if the widget is frozen. 
     * 
     * The default implementation works on the default field name, usually you don't need to 
     * override this function unless you have some strange form element logic.
     *
     * @see midcom_helper_datamanager_widget_baseclass::is_frozen
     */
    function is_frozen();
}

/**
 * Datamanager Widget base class.
 *
 * As with all subclasses, the actual initialization is done in the initialize() function,
 * not in the constructor, to allow for error handling.
 *
 * Quick glance at the changes
 *
 * - No more form prefixes, use the field name as a form field name
 * - Now uses class members, which should use initializers (var $name = 'default_value';)
 *   for configuration defaults.
 * - The schema configuration ('widget_config') is merged using the semantics
 *   $widget->$key = $value;
 *
 * @package midcom_helper_datamanager
 */
class midcom_helper_datamanager_widget implements midcom_helper_datamanager_widget_interface
{
    protected $frozen = false;

    /**
     * This is a reference to the type we're based on.
     *
     * @var midcom_helper_datamanager_type
     */
    protected $type = null;
    
    /**
     * The name field holds the name of the field the widget is encapsulating. 
     * 
     * This maps to the schema's field name. You should never have to change them.
     *
     * @var string
     */
    public $name = '';

    /**
     * The schema (not the schema <i>database!</i>) to use for operation. 
     * 
     * This variable will always contain a parsed representation of the schema, so that 
     * one can swiftly switch between individual schemas of the Database.
     *
     * This member is initialized by-reference.
     *
     * @var Array
     */
    protected $schema = null;

    /**
     * A reference to the schema field we should draw. 
     * 
     * Description texts etc. are taken from here.
     *
     * @var Array
     */
    protected $field = null;

    /**
     * This is the Namespace to use for all HTML/CSS/JS elements. 
     * 
     * It is deduced by the formmanager and tries to be as smart as possible to work safely with 
     * more then one form on a page.
     *
     * You have to prefix all elements which must be unique using this string (it includes a 
     * trailing underscore).
     *
     * @var string
     */
    public $namespace = null;

    /**
     * Name  (combined with the namespace above) of the input that the widget wants to see in POST
     */
    public $main_input_name = 'value';

    /**
     * The form we are using.
     */
    protected $form = null;
    
    /**
     * Initializes and configures the widget.
     *
     * @param string $name The name of the field to which this widget is bound.
     * @param Array $config The configuration data which should be used to customize the widget.
     * @param midcom_helper_datamanager_schema &$schema A reference to the full schema object.
     * @param midcom_helper_datamanager_type $type A reference to the type to which we are bound.
     * @param string $namespace The namespace to use including the trailing underscore.
     * @param boolean $initialize_dependencies Whether to load JS and other dependencies on initialize
     * @return boolean Indicating success. If this is false, the type will be unusable.
     */
    public function initialize($name, $config, &$schema, &$type, $namespace)
    {
        $this->name = $name;
        $this->schema =& $schema;
        $this->field =& $schema->fields[$this->name];
        $this->type =& $type;
        $this->namespace = $namespace;

        // Call the event handler for configuration in case we have some defaults that cannot
        // be covered by the class initializers.
        $this->on_configuring();

        // Assign the configuration values.
        foreach ($config as $key => $value)
        {
            $this->$key = $value;
        }

        if (! $this->on_initialize())
        {
            return false;
        }
        return true;        
    }

    /**
     * Set the form reference.
     *
     */
    public function set_form(&$form)
    {
        $this->form =& $form;
    }

    /**
     * This function is called  before the configuration keys are merged into the types
     * configuration.
     */
    protected function on_configuring() {}

    /**
     * This event handler is called during construction, so passing references to $this to the
     * outside is unsafe at this point.
     *
     * @return boolean Indicating success, false will abort the type construction sequence.
     */
    protected function on_initialize()
    {
        return true;
    }

    /**
     * This event handler is called if and only if the Formmanager detects an actual
     * form submission (this is tracked using a hidden form member). 
     * 
     * No Form validation has been done at this point. The event is triggered on all 
     * submissions with the exception of the cancel and previous form events.
     *
     * You should be careful when using this event for data
     * processing therefore. Its main application is the processing of additional buttons
     * placed into the form by the widget.
     *
     * The implementation of this handler is optional.
     *
     * @param Array $result The complete form result, you need to extract all values
     *     relevant for your type yourself.
     */
    public function on_submit($result) {}

    /**
     * This function is invoked if the widget should extract the corresponding data
     * from the form result passed in $result. 
     * 
     * Form validation has already been done before, this function will only be called 
     * if and only if the form validation succeeds.
     *
     * @param Array $result The complete form result, you need to extract all values
     *     relevant for your type yourself.
     */
    public function sync_widget2type($result)
    {
        throw new midcom_helper_datamanager_exception_widget('Method ' . __FUNCTION__ . ' must be overridden.');
    }

    /**
     * This is a shortcut to the translate_schema_string function.
     *
     * @param string $string The string to be translated.
     * @return string The translated string.
     * @see midcom_helper_datamanager_schema::translate_schema_string()
     */
    protected function translate($string)
    {
        return $this->schema->translate_schema_string($string);
    }

    /**
     * When called, this method should display the current data without any
     * editing widget or surrounding braces in plain and simple HTML.
     *
     * The default implementation calls the type's convert_to_html method.
     */
    public function render_content()
    {
        return $this->type->convert_to_html();
    }
    
////// ABOVE this line stuff jerry copied from DM2

    /**
     * Freezes all form elements associated with the widget. 
     * 
     * The default implementation works on the default field name, you don't need to override 
     * this function unless you have multiple widgets in the form.
     */
    public function freeze()
    {
        $this->frozen = true;
    }

    /**
     * Unfreezes all form elements associated with the widget. 
     * 
     * The default implementation works on the default field name, you don't need to override 
     * this function unless you have multiple widgets in the form.
     */
    public function unfreeze()
    {
        $this->frozen = false;
    }

    /**
     * Checks if the widget is frozen. 
     * 
     * The default implementation works on the default field name, usually you don't need to 
     * override this function unless you have some strange form element logic.
     *
     * @return boolean True if the element is frozen, false otherwise.
     */
    public function is_frozen()
    {
        return $this->frozen;
    }

    /**
     * Magic getters for the contents of the widget in a given format
     */
    public function __get($key)
    {
        switch ($key)
        {
            case 'as_html':
                return $this->render_html();
            case 'as_tal':
                return $this->render_tal();
        }
    }

    /**
     * Renders the form controls (if not frozen) or read-only view (if frozen)
     * of the widget as html
     */
    public function render_html()
    {
        throw new midcom_helper_datamanager_exception_widget('Method ' . __FUNCTION__ . ' must be overridden.');
    }

    /**
     * Renders the form controls (if not frozen) or read-only view (if frozen)
     * of the widget as TAL
     */
    public function render_tal()
    {
        throw new midcom_helper_datamanager_exception_widget('Method ' . __FUNCTION__ . ' must be overridden.');
    }

}

?>