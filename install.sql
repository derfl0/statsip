
--
-- Tabellenstruktur für Tabelle `statsip_statistics`
--

CREATE TABLE `statsip_statistics` (
  `id` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `type` varchar(32) COLLATE latin1_german1_ci NOT NULL,
  `plugin` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `call` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `desc` text COLLATE latin1_german1_ci NOT NULL,
  `dummy` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `statsip_templates`
--

CREATE TABLE `statsip_templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `user_id` varchar(32) COLLATE latin1_german1_ci NOT NULL,
  `type` varchar(32) COLLATE latin1_german1_ci NOT NULL,
  `mkdate` int(20) NOT NULL,
  `chdate` int(20) NOT NULL,
  `sql` text COLLATE latin1_german1_ci NOT NULL,
  `table` tinyint(1) NOT NULL,
  `graphic` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `width` int(5) NOT NULL,
  `height` int(5) NOT NULL,
  `options` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`template_id`)
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `statsip_template_stats`
--

CREATE TABLE `statsip_template_stats` (
  `template_id` int(11) NOT NULL,
  `statistics_id` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  PRIMARY KEY (`template_id`,`statistics_id`)
);
