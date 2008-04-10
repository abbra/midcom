<?php
/**
 * @package midcom.helper.datamanager
 * @author The Midgard Project, http://www.midgard-project.org
 * @version $Id: tinymce.php 12007 2007-09-04 13:47:39Z w_i $
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Datamanager FCKeditor driven textarea widget
 *
 * This widget implements currently very simple support for fckeditor. 
 * It simply replaces textarea to an editor. 
 * 
 * If browser does not support javascript or fckeditor this widget deprecates
 * to a siple textarea
 * 
 * @package midcom.helper.datamanager
 */
class midcom_helper_datamanager_widget_fckeditor extends midcom_helper_datamanager_widget_textarea
{
    /**
     * Width of the editor.
     *
     * @var int
     * @access public
     */
    public $width = null; // FIXME: As soon as configuration works, switch to null
    
    /**
     * Height of the editor.
     *
     * @var int
     * @access public
     */
    public $height = null; // FIXME: As soon as configuration works, switch to null
    
    /**
     * The FCK configuration snippet to use. Argument must be applicable to use with
     * midcom_get_snippet_content.
     *
     * @var string
     */
    public $fck_config_snippet = null;
    
    
    public $_static_toolbar_sets = array(
        'Default', 'Basic'
    );
    public $_default_toolbar_set_contents = array(
        
    );

    /**
     * Configuration array
     * @var array
     * @access public
     */
    public $configuration = array(
        
    );
    
    public $fckeditor_path = null;
    
    /**
     * This is called during intialization.
     * @return boolean always true
     */
    function on_initialize()
    {
        // FIXME: Lots of hardcodings
        $this->width = 400;
        $this->height = 300;
        $this->configuration['basepath'] = '/midcom-static/datamanager/fckeditor/';
        
        // FIXME: As soon as language support is up, remove hardcoding
        $language = 'en';
        
        //$language = $_MIDCOM->i18n->get_current_language();
        // fix to use the correct langcode for norwegian.
        if ($language == 'no')
        {
            $language = 'nb';
        }
        
        return true;
    }
    
  public function render_html()
    {
        $output = "<script src=\"{$this->configuration['basepath']}/fckeditor.js\"></script>";
        $output .=  "<label for=\"{$this->namespace}_{$this->main_input_name}\"><span>{$this->field['title']}</span>\n";
        $output .= "    <textarea style=\"width: {$this->width}; height: {$this->height};\" id=\"{$this->namespace}_{$this->main_input_name}\" name=\"{$this->namespace}_{$this->main_input_name}\"";
        if ($this->frozen)
        {
            $output .= ' disabled="disabled"';
        }
        // TODO: Escape to be safe
        $output .= ">{$this->type->value}";
        $output .= "</textarea>";
        $output .= ("
        <script>
           jQuery(document).ready(function(){
              var oFCKeditor{$this->namespace}_{$this->main_input_name} = new FCKeditor(\"{$this->namespace}_{$this->main_input_name}\");
              oFCKeditor{$this->namespace}_{$this->main_input_name}.BasePath = \"".$this->configuration['basepath']."\";
              oFCKeditor{$this->namespace}_{$this->main_input_name}.width=\"{$this->width}\";
              oFCKeditor{$this->namespace}_{$this->main_input_name}.width=\"{$this->height}\";
              oFCKeditor{$this->namespace}_{$this->main_input_name}.ReplaceTextarea();
           });
        </script>
              ");
        $output .= "</label>\n";
        return $output;
    }
    

}

?>