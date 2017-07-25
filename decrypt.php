<?php

require 'decipher.class.php';

$aOptions = getopt('e:');

$sEncryptedFile = $aOptions['e'];

if (!is_file($sEncryptedFile)) {
  print $sEncryptedFile . " is not a file.\n";
  exit;
}

$rHandle = fopen($sEncryptedFile, "r");
$sText = '';
if ($rHandle) {
  while (($sLine = fgets($rHandle)) !== false) {
    $sText .= strtoupper($sLine);
  }
  // Use a random key first to decipher.
  $aKey = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
    'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
    'Y', 'Z'
  ];

  shuffle($aKey);

  $oDecipher = new Decipher($sText, $aKey);

  $iIterations = 1000;
  $fBestScore = -99e9;
  $iStartTime = microtime(true);
  while ($iIterations > 0) {
    $fScore = $oDecipher->decrypt();

    // If we got a better score, store it.
    if ($fScore > $fBestScore) {
      $fBestScore = $fScore;
    // If we haven't changed scores, we have the best score available.
    } else if ($fScore == $fBestScore) {
      break;
    }
    $iIterations--;
  }

  // Decrypt the file using our found key.
  $sOutfilename = 'decrypted_text/' . basename($sEncryptedFile, '.txt') . '.decrypted.txt';
  file_put_contents($sOutfilename, $oDecipher->decodeOriginal());
  $iTotalTime = microtime(true) - $iStartTime;

  print "Cipher Key: " . implode("", $oDecipher->aKey) . "\n";
  print "Took " . number_format($iTotalTime, 2) . " seconds.\n";
  print "Generated " . $oDecipher->iNumKeysGenerated . " keys to find cipher.\n";
} else {
  print "Unable to open file: " . $sEncryptedFile . "\n";
  exit;
}
