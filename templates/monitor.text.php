<?php

$GLOBALS['http_response_code'] = $responseCode;
if ($responseCode === 200) {
  header($protocol . ' 200 OK');
  echo 'OK';
} else if ($responseCode === 417) {
  header($protocol . ' 417 Expectation failed');
  echo 'WARN';
} else {
  header($protocol . ' 500 Internal Server Error');
  echo 'FAIL';
}
