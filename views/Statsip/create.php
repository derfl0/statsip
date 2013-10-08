<?= $this->render_partial('statsip/templateSelection') ?>

<form class='studip_form' method="POST">

    <input  type="hidden" name="template" value="<?= htmlReady($selected->id) ?>">

    <fieldset>
        <legend><?= _('Template') ?></legend>
        <label><?= _('Name') ?>
            <input type="text" name="name" placeholder="Unbenannt" size="80" value="<?= htmlReady($selected->name) ?>">
        </label>

        <label><?= _('ID Inputs. Aktuell nur mittels SQL geregelt, da hier später die Filter der Anmeldesets eingesetzt werden sollen. SQL wie "drop database studip" ist zu vermeiden. Aktuell muss immer ein id Feld und ein weiteres Feld (Name) etc erzeugt werden.') ?><br>
            <input type="text" name="sql" size='200' placeholder="SELECT user_id as id, username as Name from auth_user_md5 ORDER BY rand() LIMIT 5" value="<?= htmlReady($selected->sql) ?>">
        </label>
        <?= $sql ?>
    </fieldset>

    <fieldset>
        <legend><?= _('Inhalte') ?></legend>
        <label><?= _('Typ') ?>
            <select name="type">
                <? foreach ($types as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $selected->type == $key ? "SELECTED" : "" ?>><?= $value ?></option>
                <? endforeach; ?>
            </select>
        </label>

        <? foreach ($elements as $element): ?>
            <label>
                <input type="checkbox" name="elements[]" value="<?= $element->id ?>" <?= $selected && $selected->activated($element->id) ? "CHECKED" : "" ?>>
                <?= htmlReady($element->name) ?>
            </label> 
        <? endforeach; ?>
    </fieldset>

    <fieldset>
        <legend><?= _('Freigabe') ?></legend>
        <small><?= _('Mögliche Freigaben: Einrichtungen (Mitarbeiter), Veranstaltungen (Dozenten und Tutoren), Benutzer') ?></small>
        <?= $shareQS ?><?= Studip\Button::create(_('Hinzufügen'), 'add') ?>
        <? if ($selected): ?>
            <br>
            <? foreach ($selected->shares as $share): ?>
                <?= htmlReady($share->name) ?><a href="<?= URLHelper::getLink('', array('template' => $selected->id, 'remove' => $share->id)); ?>"><?= Assets::img('icons/16/blue/trash.png') ?></a><br>
            <? endforeach; ?>
        <? endif; ?>
    </fieldset>

    <fieldset>
        <legend><?= _('Visualisierung') ?></legend>
        <label>
            <input type="checkbox" name="table" value="1" <?= $selected->table || !$selected ? "CHECKED" : "" ?>>
            <?= _('Tabelle') ?>
        </label>
        <label>
            <?= _('Diagramm') ?>
            <select name="graphic">
                <option value=""><?= _('Ohne') ?></option>
                <? foreach ($graphics as $key => $graphic): ?>
                    <option value="<?= $key ?>" <?= $selected->graphic == $key ? "SELECTED" : ''; ?>><?= htmlReady($graphic) ?></option>
                <? endforeach; ?>
            </select>
        </label>
        <label><?= _('Breite') ?> <small>(<?= _('0 streckt die Grafik auf maximale Breite') ?>)</small>
            <input  type="text" name="width" placeholder="<?= _('Gestreckt') ?>" value="<?= htmlReady($selected->width) ?>">
        </label>
        <label><?= _('Höhe') ?>
            <input  type="text" name="height" placeholder="300" value="<?= htmlReady($selected->height) ?>">
        </label>
    </fieldset>

    <?= \Studip\Button::create(_('Speichern'), 'save'); ?>
    <?= \Studip\Button::create(_('Löschen'), 'delete'); ?>
</form>