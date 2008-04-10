<?php

class midcom_helper_xsspreventer_helper
{
    public function value($input)
    {
        $output = str_replace('"', '&quot;', $input);
        return '"' . $output . '"';
    }

    public function textarea($input)
    {
        return preg_replace_callback
        (
        	'%(<\s*)+(/\s*)+textarea%i', 
            create_function(
            	'$matches',
            	'return htmlentities($matches[0]);'
            ),
            $input
        );
    }
}

?>
