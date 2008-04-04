<?php
/**
 * @package midcom_helper_datamanager
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Datamanager type proxy, loaded as $dm->types
 *
 * @package midcom_helper_datamanager
 */
class midcom_helper_datamanager_typeproxy
{
    private $schema = false;
    private $storage = false;
    private $types = array();

    public function __construct(&$schema, &$storage)
    {
        if (! is_a($schema, 'midcom_helper_datamanager_schema'))
        {
            throw new midcom_helper_datamanager_exception_type('given schema is not instance of midcom_helper_datamanager_schema');
        }
        $this->schema = $schema
        if (! is_a($storage, 'midcom_helper_datamanager_storage'))
        {
            throw new midcom_helper_datamanager_exception_type('given storage is not instance of midcom_helper_datamanager_storage');
        }
        $this->storage = $storage;
    }

    public function __get($name)
    {
        $this->prepare_type($name);
        // PONDER: how does this work when the original call is something like $dm->types->fieldname->value
        return $this->types[$name];
    }

    public function __set($name, $value)
    {
        $this->prepare_type($name);
        // PONDER: how does this work when the original call is something like $dm->types->fieldname->value = x
        $this->types[$name] = $value;
    }

    public function __isset($name)
    {
        return $this->field_exists($name);
    }

    /**
     * Tries to load type and throws exception if cannot
     */
    private function prepare_type($name)
    {
        if (isset($this->types[$name]))
        {
            return;
        }
        if (!$this->load_type($name))
        {
            //TODO: use dm exception
            throw new midcom_helper_datamanager_exception_type("The datatype for field {$name} could not be loaded");
        }
    }

    private function field_exists($name)
    {
        return isset($this->schema->fields[$name]);
    }

    /**
     * Loads and initialized datatype for the given schema field, if config is not given schema is used
     *
     * @param string $name name of the schema field
     * @param array $config type configuration, if left as default the valu is read from schema
     * @return bool indicating success/failure
     */
    public function load_type($name, $config = null)
    {
        if (! $this->field_exists($name))
        {
            throw new midcom_helper_datamanager_exception_type("The field {$name} is not defined in schema");
        }

        if (is_null($config))
        {
            $config = $this->schema->fields[$name];
        }

        // TODO: Move to schema class internal sanity checks
        if (! isset($config['type']) )
        {
            throw new midcom_helper_datamanager_exception_type("The field {$name} is missing type");
        }

        $type_class = $config['type'];
        
        if (strpos($type_class, '_') === false)
        {
            $type_class = "midcom_helper_datamanager_type_{$type_class}";
        }

        $this->types[$name] = new $type_class();
        if (! $this->types[$name]->initialize($name, $config['type_config'], $this->storage))
        {
            return false;
        }

        return true;
    }
}

?>