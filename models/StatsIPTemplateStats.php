<?php

class StatsIPTemplateStats extends SimpleORMap {

    protected $db_table = "statsip_template_stats";

    /**
      WHY U NO WORK!?!?!?!?!?
      protected $has_one = array(
      'stats' => array(
      'class_name' => 'StatsIPStatistic',
      'foreign_key' => 'statistics_id',
      'assoc_foreign_key' => 'statistics_id',
      'on_delete' => 'delete',
      'on_store' => 'store')); */
    public function getData($input) {
        $stats = StatsIPStatistic::find($this->statistics_id);
        $plugin = PluginEngine::getPlugin($stats->plugin);

        // Get values
        $result = call_user_func_array(array($plugin, $stats->call), array($input));

        if ($stats->dummy != "") {

            // Fill Dummies
            $IDs = array_flip($input);
            foreach ($result as $data) {
                unset($IDs[$data['id']]);
            }

            foreach ($IDs as $key => $val) {
                $result[] = array('id' => $key, $stats->name => $stats->dummy);
            }
        }
        return $result;
    }

}

?>
