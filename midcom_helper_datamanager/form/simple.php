<?php
/**
 * @package midcom_helper_datamanager
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Datamanager Form Manager simple form renderer.
 *
 * @package midcom_helper_datamanager
 */
class midcom_helper_datamanager_form_simple extends midcom_helper_datamanager_form_simple
{

    /**
     * Checks our POST data and acts accordingly
     */
    public function process()
    {
        $results = $this->get_submit_values();
        $operation = $this->compute_form_result();
        switch ($operation)
        {
            case 'cancel':
                throw new midcom_helper_datamanager_exception_datamanager_cancel();
            
            case 'previous':
                // What ?
                break;

            case 'next':
                $this->pass_results_to_method('on_submit', $results);
                $this->pass_results_to_method('sync_widget2type', $results);
                // and then what ?
                break;

            case 'save':
                $this->pass_results_to_method('on_submit', $results);
                $this->pass_results_to_method('sync_widget2type', $results);
                $this->datamanager->save();
                throw new midcom_helper_datamanager_exception_datamanager_save();
                // and then what ?
                break;

            default:
                throw new midcom_helper_datamanager_exception_datamanager("Don't know how to handle operation {$operation}");
        }
        return $operation;
    }

}

?>