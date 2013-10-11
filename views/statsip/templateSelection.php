<? if(count($templates) > 1): ?>
<form class='studip_form'>
    <label><strong><?= _('Statistikauswahl') ?></strong>
        <select name="template" onChange="this.form.submit()">
            <? foreach ($templates as $template): ?>
                <option value="<?= $template->id ?>" <?= $selected->id == $template->id ? "SELECTED" : ''; ?>><?= htmlReady($template->name) ?></option>
            <? endforeach; ?>
        </select>
    </label><br>
    <? \Studip\Button::create(_('Laden')) ?>
</form>
<? endif; ?>