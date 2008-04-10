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
        return $this->escape_tag_close('textarea', $input);
    }

    public function option($input)
    {
        return $this->escape_tag_close('option', $input);
    }

    public function escape_tag_close($tagname, $input)
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
