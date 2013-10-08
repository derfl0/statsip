<?php

class StatsipController extends StudipController {

    public function before_filter(&$action, &$args) {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
    }

    public function index_action() {

        $this->templates = StatsIPTemplate::findBySQL("1=1");

        if (!$this->templates) {
            $this->redirect('statsip/create');
            return 0;
        }

        if (Request::get('template')) {
            foreach ($this->templates as $template) {
                if ($template->id == Request::get('template')) {
                    $this->selected = $template;
                }
            }
        }

        if (Request::get('template')) {
            $template = new StatsIPTemplate(Request::get('template'));
            if ($template) {
                $this->template = $template;
                $this->head = $template->getHead();
                $this->stats = $template->getEntities();
                $this->google = $template->googleAPIJavascriptDatatable();
            }
        }
    }

    public function create_action() {



        $this->types = array(
            'user' => _('Benutzer'),
            'sem' => _('Veranstaltung'),
            'inst' => _('Einrichtungen')
        );

        $this->templates = $this->getAllTemplates();
        $this->selected = $this->getSelectedTemplate($this->templates);
        $this->pushNewTemplate($this->templates);

        if (Request::submitted('save')) {
            if (Request::get('template') == '') {
                $edit = new StatsIPTemplate();
            } else {
                $edit = new StatsIPTemplate(Request::get('template'));
            }
            $edit->name = Request::get('name') ? : _('Unbenannt');
            $edit->user_id = $GLOBALS['user']->id;
            $edit->sql = Request::get('sql') ? : "";
            $edit->type = Request::get('type') ? : "user";
            $edit->table = Request::get('table') ? 1 : 0;
            $edit->graphic = Request::get('graphic') ? : "";
            $edit->height = Request::get('height') ? : 300;
            $edit->width = Request::get('width') ? : 0;

            $edit->store();
            if (!$edit->isNew()) {
                StatsIPTemplateStats::deleteBySQL('template_id = ?', array($edit->id));
            }
            foreach (Request::getArray('elements') as $element) {
                $new = new StatsIPTemplateStats(array($edit->id, $element));
                $new->store();
            }
        }
        if (Request::submitted('delete')) {
            StatsIPTemplate::deleteBySQL('template_id = ?', array(Request::get('template')));
            StatsIPTemplateStats::deleteBySQL('template_id = ?', array(Request::get('template')));
        }


        $this->elements = StatsIPStatistic::findByType($this->selected ? $this->selected->type : "user");
        if ($this->selected) {
            try {
                $db = DBManager::get();
                $db->query($this->selected->sql);
            } catch (Exception $exc) {
                $this->sql = _('SQL PROBLEM! <pre>' . $exc . '</pre><br>');
            }
        }

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
            if ($template->id == $edit->id || $template->id == Request::get('template')) {
                return $template;
            }
        }
    }

}
