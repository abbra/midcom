<?php
/**
 * @package midcom_helper_datamanager
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

require_once('tests/testcase.php');

/**
 * Test to...
 */
class midcom_helper_datamanager_tests_types extends midcom_tests_testcase
{
    
    public function testLoad()
    {
        if (MIDCOM_TESTS_ENABLE_OUTPUT)
        {
            echo __FUNCTION__ . "\n";
            echo "Loading types\n\n";
        }

        if (MIDCOM_TESTS_ENABLE_OUTPUT)
        {
            echo "\n";
        }
 
        $this->assertTrue(true);
    }

}
?>