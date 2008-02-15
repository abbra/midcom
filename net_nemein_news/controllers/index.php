<?php
/**
 * @package net_nemein_news
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * News listing controller
 *
 * @package net_nemein_news
 */
class net_nemein_news_controllers_index
{
    public function __construct($instance)
    {
        $this->configuration = $instance->configuration;
    }
    
    public function action_latest($route_id, &$data)
    {
        $topic = new midgard_topic($this->configuration->get('news_topic'));

        $qb = midgard_article::new_query_builder();
        $qb->add_constraint('topic', '=', $topic->id);
        $qb->add_order('metadata.published', 'DESC');
        $qb->set_limit((int) $this->configuration->get('index_show_articles'));
        $data['news'] = $qb->execute();
    }
}
?>