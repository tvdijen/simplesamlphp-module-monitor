<?php
$modules = $this->data['modules'];
$configuration = $this->data['configuration'];
$store = $this->data['store'];
$state = $this->data['overall'];
$authsources = $this->data['authsources'];
$metadata = $this->data['metadata'];
$healthInfo = $this->data['healthInfo'];

$this->includeAtTemplateBase('includes/header.php');

?>
<div class="enablebox">
  <table style='width: 100%;'>
    <tr><th colspan='4'>Required modules</th></tr>
    <tr><th style="width:10%;">State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php

foreach ($modules as $check) {
    $health = $check['state'];
    $type = $check['category'];
    $item = $check['subject'];
    $summary = $check['message'];
    list($healthState, $healthColor) = $healthInfo[$health];
    echo " <tr><td style='color:$healthColor;'>$healthState</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
}

?>
  </table>
  <br />
  <table style='width: 100%;'>
    <tr><th colspan='4'>Global configuration</th></tr>
    <tr><th style="width:10%;">State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php

foreach ($configuration as $check) {
    $health = $check['state'];
    $type = $check['category'];
    $item = $check['subject'];
    $summary = $check['message'];
    list($healthState, $healthColor) = $healthInfo[$health];
    echo " <tr><td style='color:$healthColor;'>$healthState</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
}

?>
  </table>
  <br />
  <table style='width: 100%;'>
    <tr><th colspan='4'>Session store</th></tr>
    <tr><th style="width:10%;">State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php

foreach ($store as $check) {
    $health = $check['state'];
    $type = $check['category'];
    $item = $check['subject'];
    $summary = $check['message'];

    list($healthState, $healthColor) = $healthInfo[$health];
    echo " <tr><td style='color:$healthColor;'>$healthState</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
}

?>
  </table>
  <br />
<?php

foreach ($authsources as $name => $authsource) {
?>
  <table style='width: 100%;'>
    <tr><th colspan='4'>Authsource '<?php echo $name;?>'</th></tr>
    <tr><th style="width:10%;">State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php
    foreach ($authsource as $check) {
        $health = $check['state'];
        $type = $check['category'];
        $item = $check['subject'];
        $summary = $check['message'];
        list($healthState, $healthColor) = $healthInfo[$health];
        echo "    <tr><td style='color:$healthColor;'>$healthState</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
    }
?>
  </table>
  <br />
<?php
}
foreach ($metadata as $entityId => $entity) {
?>
  <table style='width: 100%;'>
    <tr><th colspan='4'>Metadata Endpoint - <?php echo $entityId; ?></th></tr>
    <tr><th style="width:10%;">State</th><th style="width:20%;">Category</th><th style="width:40%;">Subject</th><th>Summary</th></tr>
<?php
    foreach ($entity as $check) {
        $health = $check['state'];
        $type = $check['category'];
        $item = $check['subject'];
        $summary = $check['message'];
        list($healthState, $healthColor) = $healthInfo[$health];
        echo "    <tr><td style='color:$healthColor;'>$healthState</td><td>$type</td><td>$item</td><td>$summary</td></tr>\n";
    }
?>
  </table>
  <br />
<?php
}
?>
</div>
<?php
list($healthState, $healthColor) = $healthInfo[$state];
echo " <span>Overall status: <span style='color: $healthColor;'>$healthState</span></span>\n";

$this->includeAtTemplateBase('includes/footer.php');
