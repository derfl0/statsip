<?php

/**
 * StatsipdefaultPlugin.class.php
 *
 * This isn't even my final form! There will be more. Please send me an email
 * with suggestions
 *
 * @author  Florian Bieringer <florian.bieringer@uni-passau.de>
 * @version 0.1a
 */
require_once dirname(__FILE__).'/models/StatsIPModule.php';
require_once dirname(__FILE__).'/models/StatsIPStatistic.php';

class StatsipdefaultPlugin extends StatsIPModule {
    /**
     * USER FUNCTIONS
     */

    /**
     * @dummy 0
     * @name Besuchte Seminare
     * @type user
     * @description Zählt die Seminare, an denen ein Benutzer teilgenommen hat
     */
    public function count_seminars($array) {
        $stmt = $this->db->prepare("SELECT user_id as id, COUNT(*) as 'Besuchte Seminare' FROM seminar_user WHERE user_id" . self::inQuery($array) . "GROUP BY  user_id ");
        $stmt->execute($array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @dummy 0
     * @name Geschriebene Foreneinträge
     * @type user
     * @description Zählt geschriebenen Foreneinträge
     */
    public function forum_entries($array) {
        $stmt = $this->db->prepare("SELECT user_id as id, COUNT(*) as 'Geschriebene Foreneinträge' FROM forum_entries WHERE user_id" . self::inQuery($array) . "GROUP BY  user_id ");
        $stmt->execute($array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * SEMINAR FUNCTIONS
     */

    /**
     * @dummy 0
     * @name Teilnehmer
     * @type sem
     * @description Anzahl der Teilnehmer
     */
    public function seminar_user($array) {
        $stmt = $this->db->prepare("SELECT Seminar_id as id, COUNT(*) as 'Teilnehmer' FROM seminar_user WHERE Seminar_id" . self::inQuery($array) . "GROUP BY Seminar_id ");
        $stmt->execute($array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @dummy 0
     * @name Forum Einträge
     * @type sem
     * @description Zählt geschriebenen Foreneinträge
     */
    public function sem_forum_entries($array) {
        $stmt = $this->db->prepare("SELECT Seminar_id as id, COUNT(*) as 'Forum Einträge' FROM forum_entries WHERE Seminar_id" . self::inQuery($array) . "GROUP BY  Seminar_id ");
        $stmt->execute($array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * INSTITUTE FUNCTIONS
     */

    /**
     * @dummy 0
     * @name Veranstaltungen
     * @type inst
     * @description Anzahl der eingetragenen Veranstaltungen
     */
    public function seminars($array) {
        $stmt = $this->db->prepare("SELECT institut_id as id, COUNT(*) as 'Veranstaltungen' FROM seminare WHERE Institut_id" . self::inQuery($array) . "GROUP BY Institut_id ");
        $stmt->execute($array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @dummy 0
     * @name Forum Einträge
     * @type inst
     * @description Zählt geschriebenen Foreneinträge
     */
    public function inst_forum_entries($array) {
        $stmt = $this->db->prepare("SELECT institut_id as id, COUNT(*) as 'Forum Einträge' FROM forum_entries JOIN seminare USING (seminar_id) WHERE institut_id" . self::inQuery($array) . "GROUP BY  institut_id ");
        $stmt->execute($array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
