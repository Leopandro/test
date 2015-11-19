<?php
/* @var $this yii\web\View */
?>
<h4>Всего входов: <?= count($connections); ?></h4>
<table>
    <tr><td>Ip адрес:</td><td>Время:</td></tr>
<?
    if ($connections)
    {
        foreach($connections as $connection)
        {
            ?>
            <tr>
                <td><?=$connection->ip ?></td>
                <td><?=date('d-M-Y H:i:s', strtotime($connection->time)) ?></td>
            </tr>
            <?
        }
    }
?>
</table>