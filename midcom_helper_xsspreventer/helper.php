<?php

class midcom_helper_xsspreventer_helper
{
    static public function value($input)
    {
        $output = str_replace('"', '&quot;', $input);
        return '"' . $output . '"';
    }

    static public function textarea($input)
    {
        return midcom_helper_xsspreventer_helper::escape_tag_close('textarea', $input);
    }

    static public function option($input)
    {
        return midcom_helper_xsspreventer_helper::escape_tag_close('option', $input);
    }

    static public function escape_tag_close($tagname, $input)
    {
        return preg_replace_callback
        (
        	"%(<\s*)+(/\s*)+{$tagname}%i", 
            create_function(
            	'$matches',
            	'return htmlentities($matches[0]);'
            ),
            $input
        );
    }
}

?>
