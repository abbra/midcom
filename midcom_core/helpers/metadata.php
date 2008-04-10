<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Metadata helper for MidCOM 3
 *
 *
 * @package midcom_core
 */
class midcom_core_helpers_metadata
{
    public static function is_approved(&$object)
    {
        if ($object->metadata->approved >= $object->metadata->revised)
        {
            return true;
        }
        
        return false;
    }
    
    public static function approve(&$object)
    {
        $_MIDCOM->authorization->require_do('midcom:approve', $object);        
        
        $object->metadata->approved = gmstrftime('%Y-%m-%d %T', time());

        if ($_MIDCOM->authentication->is_user())
        {
            $person = $_MIDCOM->authentication->get_person();
            $object->metadata->approver = $person->guid;
        }
        
        $object->update();
    }
    
    public static function unapprove(&$object)
    {
        $_MIDCOM->authorization->require_do('midcom:approve', $object);        
        
        $object->metadata->approved = '';
        $object->metadata->approver = '';
        
        $object->update();
    }
    
    public static function is_locked(&$object)
    {
        if (empty($object->metadata->locked))
        {
            return false;
        }
        
        $lock_time = strtotime($object->metadata->locked);
        $lock_timeout = $lock_time + ($_MIDCOM->configuration->get('metadata_lock_timeout') * 60);
        
        if (time() > $lock_timeout)
        {
            // Stale lock
            // TODO: Should we clear the stale lock here?
            return false;
        }
        
        if ($_MIDCOM->authentication->is_user())
        {
            $person = $_MIDCOM->authentication->get_person();
            
            if ($object->metadata->locker == $person->guid)
            {
                // If you locked it yourself, you can also edit it
                return false;
            }
        }
        
        return true;
    }
    
    public static function lock(&$object)
    {
        $_MIDCOM->authorization->require_do('midgard:update', $object);
        
        $object->metadata->locked = gmstrftime('%Y-%m-%d %T', time());

        if ($_MIDCOM->authentication->is_user())
        {
            $person = $_MIDCOM->authentication->get_person();
            $object->metadata->locker = $person->guid;
        }

        $approved = midcom_core_helpers_metadata::is_approved(&$object);

        $object->update();

        if ($approved)
        {
            // TODO: This should be a SUDO operation
            midcom_core_helpers_metadata::approve(&$object);
        }
    }
    
    public static function unlock(&$object)
    {
        $_MIDCOM->authorization->require_do('midgard:update', $object);
        
        $allowed = false;
        if ($_MIDCOM->authentication->is_user())
        {
            $person = $_MIDCOM->authentication->get_person();
            if ($object->metadata->locker == $person->guid)
            {
                // The person who locked an object can always unlock it
                $allowed = true;
            }
        }
        
        if (!$allowed)
        {
            // If user didn't lock it herself require the unlock privilege
            $_MIDCOM->authorization->require_do('midcom:unlock', $object);
        }
        
        $object->metadata->locked = '';
        $object->metadata->locker = '';

        $approved = midcom_core_helpers_metadata::is_approved(&$object);
        
        $object->update();
        
        if ($approved)
        {
            // TODO: This should be a SUDO operation
            midcom_core_helpers_metadata::approve(&$object);
        }
    }
}
?>