<?php
/**
 * This tests the corectness of rules!
 */
// Comment when needed.
exit('');

ini_set('display_errors', '1');
error_reporting(-1);

define('MW_PATH', true);
require_once __DIR__ . '/BounceHandler.php';

$rules  = require __DIR__ . '/rules.php';
$string = "";
$string = BounceHandler::stripSpecialChars($string);

$matched= [];

foreach ($rules[BounceHandler::COMMON_RULES] as $info) {
    foreach ($info['regex'] as $regex) {
        echo strtoupper((string)$info['bounceType']) . " bounce testing for: {$regex}";
        if (preg_match($regex, $string, $matches)) {
            echo " >>> Matched";
            $matched[] = [$regex => $info['bounceType']];
        } else {
            echo " >>> Not matched";
        }
        echo PHP_EOL;
    }
}
echo "Matched rules:\n";
print_r($matched);