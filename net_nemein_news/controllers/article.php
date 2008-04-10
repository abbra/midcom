<?php
/**
 * @package midcom_core
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Page management controller
 *
 * @package midcom_core
 */
class net_nemein_news_controllers_article extends midcom_core_controllers_baseclasses_manage
{
    public function __construct($instance)
    {
        $this->configuration = $instance->configuration;
    }
    
    public function load_object($args)
    {
        $topic_guid = $this->configuration->get('news_topic');
        if (!$topic_guid)
        {
            throw new midcom_exception_notfound("No news topic defined");
        }
        $data['topic'] = new midgard_topic($topic_guid);
        
        $qb = midgard_article::new_query_builder();
        $qb->add_constraint('topic', '=', $data['topic']->id);
        $qb->add_constraint('name', '=', $args['name']);        
        $articles = $qb->execute();        
        if (count($articles) == 0)
        {
            throw new midcom_exception_notfound("Article {$args['name']} not found.");
        }
        $this->object = $articles[0];
    }
    
    public function get_object_url()
    {
        return $_MIDCOM->dispatcher->generate_url('show', array('name' => $this->object->name));
    }
    
    public function populate_toolbar()
    {
        $_MIDCOM->toolbar->add_item
        (
            'article', 
            'edit', 
            array
            (
                'label' => 'edit article',            
                'route_id' => 'edit',
                'route_arguments' => array
                (
                    'name' => $this->object->name,
                ),
                'icon' => 'edit',
                'enabled' => $_MIDCOM->authorization->can_do('midgard:update', $this->object),
            )
        );
    }
}
?>