<h1>Релизы</h1>

<div class="element" id="plot">
    <form method="post" action="release.php" enctype="multipart/form-data">
        <p>Версия: <input type="text" name="version" /></p>
        <p>Сборка: <input type="text" name="build" /></p>
        <p>Ветка: <input type="text" name="branch" /></p>
        <p>Проверять по сборке: <input type="checkbox" name="by_build" value="1" /></p>
        <p>Сообщение: <textarea name="message" cols="20" rows="5"></textarea></p>
        <p>APK: <input type="file" name="apk" /></p>
        <input type="submit" value="Отправить">
    </form>
</div>

<div class="element" id="plot">
    <table border="1px" width="100%">
        <tr>
            <td>branch</td>
            <td>version</td>
            <td>build</td>
            <td>message</td>
            <td>downloads</td>
        </tr>

        <?php
            include __DIR__ . '/../../config.php';
            include __DIR__ . '/../../lib/branches.php';

            foreach (array_keys($branches) as $branch) {
                echo "<tr><td><a href=\"" . $branches[$branch]['url'] . 
                    "\">" . $branch . "</a></td>" .
                    "<td>" . $branches[$branch]['version'] . "</td>" .
                    "<td>" . $branches[$branch]['build'] . "</td>" .
                    "<td>" . nl2br($branches[$branch]['message']) . "</td>" .
                    "<td>" . $branches[$branch]['downloads'] . "</td></tr>";
            }
        ?>

        <form method="get" action="recache.php">
            <input type="submit" value="Перезаполнить кэш">
        </form>
    </table>
</div>
