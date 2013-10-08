<?= $this->render_partial('statsip/templateSelection') ?>

<? if ($selected): ?>

    <div id="chart_div" width="100%"></div>

    <? if ($selected->table): ?>
        <table class="default">
            <caption><?= htmlReady($template->name) ?></caption>
            <thead
                <tr>
                        <? foreach ($head as $entry): ?>
                        <th>
                            <?= $entry ?>
                        </th>
                    <? endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <? foreach ($stats as $stat): ?>
                    <tr>            
                        <? foreach ($head as $key): ?>
                            <td><?= $stat[$key] ?></td>
                        <? endforeach; ?>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    <? endif; ?>

    <? if ($selected->graphic): ?>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">

        // Load the Visualization API and the piechart package.
        google.load('visualization', '1.0', {'packages': ['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {

            // Create the data table.       
            var data = google.visualization.arrayToDataTable([
        <?= $google ?>
            ]);


            // Set chart options
            var options = {'title': '<?= htmlReady($selected->name) ?>',
        <?= $selected->width ? "'width': " . htmlReady($selected->width) . "," : "" ?>
                'height': <?= htmlReady($selected->height) ?>,
            };

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.<?= htmlReady($selected->graphic) ?>(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
        </script>
    <? endif; ?>
<? endif; ?>
