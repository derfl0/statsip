<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatsIPTemplate
 *
 * @author intelec
 */
class StatsIPTemplate extends SimpleORMap {

    protected $db_table = "statsip_templates";

    /**
      WHY U NO WORK!??!?!?!?!?!
      protected $has_many = array(
      'stats' => array(
      'class_name' => 'StatsIPTemplateStats',
      'func' => 'findByTemplate_id',
      'on_delete' => 'delete',
      'on_store' => 'store')); */
    private $entities;
    private $columns = array();
    private $stats;

    public function __construct($id = null) {
        parent::__construct($id);
    }

    public function activated($id) {
        foreach (StatsIPTemplateStats::findBySQL("template_id = ?", array($this->id)) as $stat) {
            if ($stat->statistics_id == $id) {
                return true;
            }
        }
        return false;
    }
    
    private function getStats() {
        if (!$this->stats) {
            $this->stats = StatsIPTemplateStats::findBySQL("template_id = ?", array($this->id));
        }
        return $this->stats;
    }

    private function loadEntries() {
        $db = DBManager::get();
        $stmt = $db->query($this->sql);
        $this->accumulateEntities($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function googleAPIJavascriptDatatable() {
        foreach (array_merge(array($this->getHead()), $this->getEntities()) as $data) {
            $result = "[";
            $result .= join(array_map(function($input) {
                                return is_numeric($input) ? $input : "'$input'";
                            }, $data), ", ");
            $result .= "]";
            $tmp[] = $result;
        }
        return join($tmp, ", ");
    }

    public function addEntity($id, $name, $value) {
        if (!in_array($name, $this->columns)) {
            $this->columns[] = $name;
        }
        $this->entities[$id][$name] = $value;
    }

    public function accumulateEntities($array) {
        foreach ($array as $new) {
            foreach ($new as $key => $value) {
                if ($key != 'id') {
                    $this->addEntity($new['id'], $key, $value);
                }
            }
        }
    }

    public function getHead() {
        if (!$this->columns) {
            $this->collectStats();
        }
        return $this->columns;
    }

    private function getIDs() {
        return array_keys($this->entities);
    }

    public function getEntities() {
        if (!$this->entities) {
            $this->collectStats();
        }
        return $this->entities;
    }

    private function collectStats() {
        $this->loadEntries();
        if ($this->getStats()) {
            foreach ($this->getStats() as $statistic) {
                try {
                    $newEntries = $statistic->getData($this->getIDs());
                    $this->accumulateEntities($newEntries);
                } catch (Exception $exc) {
                    
                }
            }
        }
    }

}

?>
