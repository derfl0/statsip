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

        $this->checkViewRights();
    }

    public function create_action() {
        $GLOBALS['perm']->check('root');

        $this->loadSelects();
        $this->updateFromRequest();
        $this->deleteFromRequest();
        $this->prepareTemplateSelection();
        $this->pushNewTemplate($this->templates);
        $this->addFromRequest();
        $this->removeFromRequest();
        $this->loadElements();
        $this->createShareQuicksearch();

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

    private function checkViewRights() {
        if ($GLOBALS['perm']->have_perm('root')) {
            return true;
        }
        if (Navigation::hasItem("/profile") && StatsIPShare::findBySQL('template_id = ? AND range_id = ?', array($this->selected->id, $GLOBALS['user']->id))) {
            return true;
        }
        throw new AccessDeniedException(_('Statistik nicht freigegeben'));
    }

    private function prepareTemplateSelection() {
        $this->templates = $this->getAllTemplates();
        $this->selected = $this->getSelectedTemplate($this->templates);
    }

    private function getAllTemplates() {
        if ($GLOBALS['perm']->have_perm('root')) {
            return StatsIPTemplate::findBySQL("1=1");
        }
        if (Navigation::hasItem("/profile")) {
            $shares = StatsIPShare::findByRange_id($GLOBALS['user']->id);
            foreach ($shares as $share) {
                $pks[] = $share->template_id;
            }
            return StatsIPTemplate::findMany($pks);
        }
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
        if (Request::submitted('save') || Request::submitted('add')) {
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

    private function addFromRequest() {
        if (Request::submitted('add') && Request::get('range_id')) {
            $new = new StatsIPShare();
            $new->name = Request::get('range_id_parameter');
            $new->range_id = Request::get('range_id');
            $new->template_id = $this->selected->id;
            $new->store();
        }
    }

    private function removeFromRequest() {
        if (Request::submitted('remove')) {
            $del = new StatsIPShare(Request::get('remove'));
            $del->delete();
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

    private function createShareQuicksearch() {
        $suche = new SQLSearch("SELECT institut_id as range_id, CONCAT('" . _('Einrichtung') . ": ', Name)
            FROM institute
            WHERE name LIKE :input
            UNION
            SELECT user_id as range_id, CONCAT('" . _('Benutzer') . ": ', Vorname, ' ', Nachname )
            FROM auth_user_md5
            WHERE Nachname LIKE :input OR Vorname LIKE :input OR CONCAT(Vorname,' ', Nachname) LIKE :input OR CONCAT(Nachname,' ',Vorname) LIKE :input 
            UNION
            SELECT seminar_id as range_id, CONCAT('" . _('Veranstaltung') . ": ', Name)
            FROM seminare
            WHERE name LIKE :input", _("Freigabe"), "range_id");
        $this->shareQS = QuickSearch::get("range_id", $suche)
                ->setInputStyle("width: 240px")
                ->render();
    }

}
