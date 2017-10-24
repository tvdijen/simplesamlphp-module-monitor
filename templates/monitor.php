<?php
$modules = $this->data['modules'];
$configuration = $this->data['configuration'];
$store = $this->data['store'];
$state = $this->data['overall'];
$authsources = $this->data['authsources'];
$health_info = $this->data['health_info'];

$this->includeAtTemplateBase('includes/header.php');

?>
  <table>
    <tr><th colspan='4'>Required modules</th></tr>
    <tr><th>State</th><th>Category</th><th style="width:35%;">Subject</th><th>Summary</th></tr>
<?php

foreach ($modules as $check) {
    list($health, $type, $item, $summary) = $check;
    list($health_state, $health_color) = $health_info[$health];
    echo " <tr><td style='color:$health_color;'>$health_state</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
}

?>
  </table>
  <table>
    <tr><th colspan='4'>Global configuration</th></tr>
    <tr><th>State</th><th>Category</th><th style="width:35%;">Subject</th><th>Summary</th></tr>
<?php

foreach ($configuration as $check) {
    list($health, $type, $item, $summary) = $check;
    list($health_state, $health_color) = $health_info[$health];
    echo " <tr><td style='color:$health_color;'>$health_state</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
}

?>
  </table>
  <table>
    <tr><th colspan='4'>Session store</th></tr>
    <tr><th>State</th><th style="width:35%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php

foreach ($store as $check) {
    list($health, $type, $item, $summary) = $check;
    list($health_state, $health_color) = $health_info[$health];
    echo " <tr><td style='color:$health_color;'>$health_state</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
}

?>
  </table>
<?php

foreach ($authsources as $name => $authsource) {
?>
  <table>
    <tr><th colspan='4'>Authsource '<?php echo $name;?>'</th></tr>
    <tr><th>State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php
    foreach ($authsource as $check) {
        list($health, $type, $item, $summary) = $check;
        list($health_state, $health_color) = $health_info[$health];
        echo " <tr><td style='color:$health_color;'>$health_state</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
    }
echo "</table>";
}
?>
<?php
list($health_state, $health_color) = $health_info[$state];
echo " <span>Overall status: <span style='color: $health_color;'>$health_state</span></span>\n";

$this->includeAtTemplateBase('includes/footer.php');
