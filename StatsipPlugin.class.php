<?php

require 'bootstrap.php';

/**
 * StatsipPlugin.class.php
 *
 * ...
 *
 * @author  Florian Bieringer <florian.bieringer@uni-passau.de>
 * @version 0.1a
 */
class StatsipPlugin extends StudIPPlugin implements HomepagePlugin, StandardPlugin, SystemPlugin {

    public function __construct() {
        parent::__construct();
        $this->setupAutoload();

        $navigation = new AutoNavigation(_('Stats.IP'));
        $navigation->setURL(PluginEngine::GetURL($this, array(), 'statsip'));
        $navigation->setImage(Assets::image_path('icons/16/white/stat.png'));
        $navigation->setActiveImage(Assets::image_path('icons/16/black/stat.png'));

        if ($GLOBALS['perm']->have_perm('root')) {
            Navigation::addItem('tools/statsip', $navigation);
        } else if (Navigation::hasItem("/profile") &&
                $this->isActivated($GLOBALS['user']->id, 'user') && StatsIPShare::findBySQL("range_id = ? LIMIT 1", array($GLOBALS['user']->id))) {
            Navigation::addItem("/profile/statsip", $navigation);
        }
    }

    public function initialize() {

        if ($GLOBALS['perm']->have_perm('root')) {
            $loadedTemplate = Request::get('template');

            $subNavigation = new AutoNavigation(_('Anzeigen'));
            $subNavigation->setURL(PluginEngine::GetURL($this, array(), 'statsip'), array('template' => $loadedTemplate));
            Navigation::addItem('tools/statsip/show', $subNavigation);

            $subNavigation = new AutoNavigation(_('Erstellen / Bearbeiten'));
            $subNavigation->setURL(PluginEngine::GetURL($this, array(), 'statsip/create'), array('template' => $loadedTemplate));
            Navigation::addItem('tools/statsip/create', $subNavigation);

            PageLayout::addStylesheet($this->getPluginURL() . '/assets/style.css');
            PageLayout::addScript($this->getPluginURL() . '/assets/application.js');
        }
    }

    public function getHomepageTemplate($user_id) {
        // ...
    }

    public function getTabNavigation($course_id) {
        return array();
    }

    public function getNotificationObjects($course_id, $since, $user_id) {
        return array();
    }

    public function getIconNavigation($course_id, $last_visit, $user_id) {
        // ...
    }

    public function getInfoTemplate($course_id) {
        // ...
    }

    public function perform($unconsumed_path) {
        $this->setupAutoload();
        $dispatcher = new Trails_Dispatcher(
                $this->getPluginPath(), rtrim(PluginEngine::getLink($this, array(), null), '/'), 'statsip'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    private function setupAutoload() {
        if (class_exists("StudipAutoloader")) {
            StudipAutoloader::addAutoloadPath(__DIR__ . '/models');
        } else {
            spl_autoload_register(function ($class) {
                        include_once __DIR__ . $class . '.php';
                    });
        }
    }

}
