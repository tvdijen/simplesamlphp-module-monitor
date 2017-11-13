<?php
$modules = $this->data['modules'];
$configuration = $this->data['configuration'];
$store = $this->data['store'];
$state = $this->data['overall'];
$authsources = $this->data['authsources'];
$metadata = $this->data['metadata'];
$health_info = $this->data['health_info'];

$this->includeAtTemplateBase('includes/header.php');

?>
<div class="enablebox">
  <table style='width: 98%;'>
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
  <br />
  <table style='width: 98%;'>
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
  <br />
  <table style='width: 98%;'>
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
  <br />
<?php

foreach ($authsources as $name => $authsource) {
?>
  <table style='width: 98%;'>
    <tr><th colspan='4'>Authsource '<?php echo $name;?>'</th></tr>
    <tr><th>State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php
    foreach ($authsource as $check) {
        list($health, $type, $item, $summary) = $check;
        list($health_state, $health_color) = $health_info[$health];
        echo "    <tr><td style='color:$health_color;'>$health_state</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
    }
?>
  </table>
  <br />
<?php
}

foreach ($metadata as $entityId => $entity) {
?>
  <table style='width: 98%;'>
    <tr><th colspan='4'>Metadata Endpoint - <?php echo $entityId; ?></th></tr>
    <tr><th>State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php
    foreach ($entity as $entityId => $check) {
        list($health, $type, $item, $summary) = $check;
        list($health_state, $health_color) = $health_info[$health];
        echo "    <tr><td style='color:$health_color;'>$health_state</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
    }
?>
  </table>
  <br />
<?php
}
?>
</div>
<?php
list($health_state, $health_color) = $health_info[$state];
echo " <span>Overall status: <span style='color: $health_color;'>$health_state</span></span>\n";

$this->includeAtTemplateBase('includes/footer.php');
