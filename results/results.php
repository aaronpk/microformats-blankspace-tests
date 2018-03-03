<?php
chdir(__DIR__);
$data = json_decode(file_get_contents('output.json'), true);

function explicit_whitespace($v) {
  $v = str_replace("\n", '\n', $v);
  $v = str_replace("\r", '\r', $v);
  return htmlspecialchars($v);
}

ob_start();
?>
<style>
body {
  font-family: sans-serif;
}
table {
  width: 100%;
  border-collapse: collapse;
}
tr.top {
  border-top: 2px #aaa solid;
}
.bottom {
  border-bottom: 2px #aaa solid;
}
tr.even {
  background-color: #f6f6f6;
}
td {
  border: 1px #ccc solid;
  padding: 4px;
}
td.html {
  font-size: 0.8em;
}
td.success {
  background-color: #d5ead9;
}
tr.even .success {
  background-color: #c9dccd;
}
td.fail {
  background-color: #f6e0e0;
}
tr.even .fail {
  background-color: #ead5d5;
}
td.testnum {
  text-align: center;
  font-size: 1.4em;
}
td.testnum a {
  text-decoration: none;
}
</style>
<table>
  <thead>
    <tr>
      <th>Test</th>
      <th>HTML</th>
      <th>Property</th>
      <th>Expected</th>
      <th>PHP</th>
      <th>Ruby</th>
      <th>Python</th>
      <th>Go</th>
      <th>Node</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($data as $i=>$test): ?>
      <?php foreach(['name', 'content.value', 'content.html'] as $j=>$prop): ?>
        <tr class="<?= $j == 0 ? 'top' : '' ?> <?= $i == count($data)-1 && $j == 2 ? 'bottom' : '' ?> <?= $i % 2 == 0 ? 'even' : 'odd' ?>">
          <?php if($j == 0): ?>
            <td rowspan="3" class="testnum <?= $i == count($data)-1 ? 'bottom' : '' ?>"><a href="https://github.com/aaronpk/microformats-whitespace-tests/blob/master/tests/<?= $test['test'] ?>.html"><?= $test['test'] ?></td>
            <td rowspan="3" class="html <?= $i == count($data)-1 ? 'bottom' : '' ?>"><pre><?= htmlspecialchars(file_get_contents('../tests/'.$test['test'].'.html')) ?></pre></td>
          <?php endif ?>
          <td><?= $prop ?></td>
          <td><pre><?= explicit_whitespace($test['expected'][$prop]) ?></pre></td>
          <?php foreach(['php','ruby','python','go','node'] as $parser): ?>
            <td class="<?= $test['expected'][$prop] == $test[$parser][$prop] ? 'success' : 'fail' ?>"><pre><?= explicit_whitespace($test[$parser][$prop]) ?></pre></td>
          <?php endforeach ?>
        </tr>
      <?php endforeach ?>
    <?php endforeach ?>
  </tbody>
</table>
<?php
$html = ob_get_clean();
file_put_contents('results.html', $html);
