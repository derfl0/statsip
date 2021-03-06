<?= $this->render_partial('statsip/templateSelection') ?>

<? if ($selected): ?>

    <div id="chart_div" width="100%"></div>

    <? if ($selected->table): ?>
 <table class="default zebra nohover datatable">
                <caption><?= htmlReady($selected->name) ?></caption>
                <thead>
                    <tr>
                            <? foreach ($selected->getHead() as $entry): ?>
                            <th nowrap>
                                <?= $entry ?>
                            </th>
                        <? endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($selected->getEntities() as $stat): ?>
                        <tr >            
                            <? foreach ($selected->getHead() as $key): ?>
                                <td nowrap><?= $stat[$key] ?></td>
                            <? endforeach; ?>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
    <? endif; ?>

    <? if ($selected->graphic): ?>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">

            // Load the Visualization API
            google.load('visualization', '1.0', {'packages': ['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table
            function drawChart() {

                // Create the data table.       
                var data = google.visualization.arrayToDataTable([
        <?= $selected->googleAPIJavascriptDatatable() ?>
                ]);


                // Set chart options
                var options = {'title': '<?= htmlReady($selected->name) ?>',
                    'height': <?= htmlReady($selected->height) ?>
        <?= $selected->width ? "'width': " . htmlReady($selected->width) . "," : "" ?>
                };

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.<?= htmlReady($selected->graphic) ?>(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>
    <? endif; ?>
<? endif; ?>
