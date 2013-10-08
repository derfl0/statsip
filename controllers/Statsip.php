<?php

class StatsipController extends StudipController {

    public function before_filter(&$action, &$args) {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
    }

    public function index_action() {

        $this->prepareTemplateSelection();

        if (!$this->templates) {
            $this->redirect('statsip/create');
            return 0;
        }

        if (!$this->selected) {
            $this->selected = $this->templates[0];
        }
    }

    public function create_action() {

        $this->loadSelects();
        $this->updateFromRequest();
        $this->deleteFromRequest();
        $this->prepareTemplateSelection();
        $this->pushNewTemplate($this->templates);
        $this->loadElements();

        // Shitty code to detect sql fails. Since sql code wont be a valid option in retail this will be removed some time
        if ($this->selected) {
            try {
                $db = DBManager::get();
                $db->query($this->selected->sql);
            } catch (Exception $exc) {
                $this->sql = _('SQL PROBLEM! <pre>' . $exc . '</pre><br>');
            }
        }
    }

    /*
     * HELPER FUNCTIONS
     */

    // customized #url_for for plugins
    function url_for($to) {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map("urlencode", $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join("/", $args));
    }

    private function prepareTemplateSelection() {
        $this->templates = $this->getAllTemplates();
        $this->selected = $this->getSelectedTemplate($this->templates);
    }

    private function getAllTemplates() {
        return StatsIPTemplate::findBySQL("1=1");
    }

    private function pushNewTemplate(&$templates) {
        $new = new StatsIPTemplate();
        $new->name = _('Neue Statistik erstellen');
        $new = array($new);
        $templates = array_merge($new, $templates);
    }

    private function getSelectedTemplate() {
        foreach ($this->templates as $template) {
            if ($template->id == $this->newId || $template->id == Request::get('template')) {
                return $template;
            }
        }
    }

    private function updateFromRequest() {
        if (Request::submitted('save')) {
            if (Request::get('template') == '') {
                $edit = new StatsIPTemplate();
            } else {
                $edit = new StatsIPTemplate(Request::get('template'));
            }
            $this->setTemplateInfoByRequest($edit);
            $edit->store();
            if (!$edit->isNew()) {
                StatsIPTemplateStats::deleteBySQL('template_id = ?', array($edit->id));
            }
            foreach (Request::getArray('elements') as $element) {
                $new = new StatsIPTemplateStats(array($edit->id, $element));
                $new->store();
            }
            $this->selected = $edit;
        }
    }

    private function deleteFromRequest() {
        if (Request::submitted('delete')) {
            StatsIPTemplate::deleteBySQL('template_id = ?', array(Request::get('template')));
            StatsIPTemplateStats::deleteBySQL('template_id = ?', array(Request::get('template')));
        }
    }

    private function loadElements() {
        $this->elements = StatsIPStatistic::findByType($this->selected ? $this->selected->type : "user");
    }

    private function loadSelects() {
        $this->types = array(
            'user' => _('Benutzer'),
            'sem' => _('Veranstaltung'),
            'inst' => _('Einrichtungen')
        );
        $this->graphics = array('ColumnChart' => _('Balken'),
            'AreaChart' => _('Flächen'),
            'SteppedAreaChart' => _('Stufenflächen'),
            'BarChart' => _('Streifen'),
            'CandlestickChart' => _('Kerzen'),
            'ComboChart' => _('Combo'),
            'Gauge' => _('Messuhr'),
            'LineChart' => _('Linien'),
            'PieChart' => _('Kuchen'),
            'ScatterChart' => _('Streuung'),
            'BubbleChart' => _('Blasen'));
    }

    private function setTemplateInfoByRequest(&$template) {
        $template->name = Request::get('name') ? : _('Unbenannt');
        $template->user_id = $GLOBALS['user']->id;
        $template->sql = Request::get('sql') ? : "";
        $template->type = Request::get('type') ? : "user";
        $template->table = Request::get('table') ? 1 : 0;
        $template->graphic = Request::get('graphic') ? : "";
        $template->height = Request::get('height') ? : 300;
        $template->width = Request::get('width') ? : 0;
    }

}
