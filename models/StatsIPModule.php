<?php
class StatsIPModule extends StudIPPlugin implements SystemPlugin {

    protected $db;

    public function __construct() {
        parent::__construct();
        $this->db = DBManager::get();
    }

    public static function onEnable($pluginId) {
        parent::onEnable($pluginId);
        $rc = new ReflectionClass(get_called_class());
        foreach ($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class == get_called_class()) {
                preg_match_all("/@([\w]+)\s([\w\säöüÄÖÜ,]*[\wäöüÄÖÜ])/", $method->getDocComment(), $matches, PREG_SET_ORDER);
                $attributes = array();
                foreach ($matches as $match) {
                    $attributes[$match[1]] = $match[2];
                }
                $stats = new StatsIPStatistic($method->class . '_' . $method->name);
                $stats->name = $attributes['name'] ? : "$method->class $method->name";
                $stats->type = $attributes['type'] ? : 'user';
                $stats->plugin = $method->class;
                $stats->call = $method->name;
                $stats->desc = $attributes['description'] ? : '';
                $stats->dummy = isset($attributes['dummy']) ?$attributes['dummy'] : '';
                $stats->store();
            }
        }
    }

    public static function onDisable($pluginId) {
        parent::onDisable($pluginId);
        StatsIPStatistic::deleteBySQL('plugin = ?', array(get_called_class()));
    }

    public static function inQuery($array) {
        return " IN (" . implode(',', array_fill(0, count($array), '?')) . ") ";
    }
}

?>
