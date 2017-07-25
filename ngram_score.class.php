<?php

/**
 * Class to score a specific piece of text using quadgrams.
 */
class NgramScore {
  /**
   * Array of quadgrams used to score
   * a piece of text.
   * @var array
   */
  private $aQuadgrams = [];

  /**
   * Total count from quadgram file.
   * @var integer
   */
  private $iTotal;

  /**
   * Length of quadgrams.
   * @var integer
   */
  private $iLength;

  /**
   * Floor of the quadgrams.
   * @var float
   */
  private $fFloor;

  /**
   * Generate our values used to compute scores.
   * @param string $sQuadgramFile
   * @return Object
   */
  public function __construct($sQuadgramFile = 'english_quadgrams.txt') {
    $rHandle = fopen($sQuadgramFile, 'r');
    if ($rHandle) {
      while (($sLine = fgets($rHandle)) !== false) {
        $aLineSplit = explode(' ', $sLine);
        $this->aQuadgrams[$aLineSplit[0]] = $aLineSplit[1];
        $this->iTotal += $aLineSplit[1];
        $this->iLength = strlen($aLineSplit[0]);
      }

      foreach ($this->aQuadgrams as $sKey => $iCount) {
        $this->aQuadgrams[$sKey] = log10($this->aQuadgrams[$sKey] / $this->iTotal);
      }

      $this->fFloor = log10(0.01 / $this->iTotal);
    }
  }

  /**
   * Generate a score given text, using our quadgrams.
   * @param string $sText
   * @return float $fScore
   */
  public function score($sText) {
    $fScore = 0.0;

    for ($i = 0; $i < (strlen($sText) - $this->iLength + 1); $i++) {
      $sSubString = substr($sText, $i, $this->iLength);
      if (isset($this->aQuadgrams[$sSubString])) {
        $fScore += $this->aQuadgrams[$sSubString];
      } else {
        $fScore += $this->fFloor;
      }
    }

    return $fScore;
  }
}
