<?php

require 'ngram_score.class.php';

/**
 * Class used to decipher a string using substitution
 */
class Decipher {
  /**
   * The alphabet.
   * @var array
   */
  public $aAlphabet = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
    'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
    'Y', 'Z'
  ];

  /**
   * Our encrypted text, stripped of everything but alpha characters.
   * @var string
   */
  public $sEncrypted = '';

  /**
   * Our original encrypted text.
   * @var string
   */
  public $sOrigEncrypted = '';

  /**
   * Our decrypted text.
   * @var string
   */
  public $sDecrypted = '';

  /**
   * Our key.
   * @var array
   */
  public $aKey = [];

  /**
   * The best possible ngram score.
   * @var float
   */
  private $fBestScore = -99e9;

  /**
   * The best possible key.
   * @var array
   */
  private $aBestKey = [];

  /**
   * Our instance to the NgramScore class.
   * @var Object
   */
  private $oNgramScore;

  /**
   * Store the number of keys generated.
   * @var int
   */
  public $iNumKeysGenerated = 0;

  /**
   * Hold some variables to decipher.
   * @param string $sText
   * @param array $aKey
   * @return Object
   */
  public function __construct($sText, $aKey) {
    $this->sOrigEncrypted = $sText;
    $this->sEncrypted = preg_replace('/[^a-z]+/i', '', $sText);
    $this->aKey = $aKey;
    $this->aBestKey = $aKey;

    $this->oNgramScore = new NgramScore();
  }

  /**
   * Decode text using a specified key
   * @param void
   * @return string
   */
  private function runCipher() {
    $sDecipheredText = implode('', array_map(function($cChar) {
      return str_replace($cChar, $this->aKey[array_search($cChar, $this->aAlphabet)], $cChar);
    }, str_split($this->sEncrypted)));

    return $sDecipheredText;
  }

  /**
   * Obtain our score from the first line of text.
   * @param void
   * @return float
   */
  private function obtainScore($sDecipheredText) {
    return $this->oNgramScore->score($sDecipheredText);
  }

  /**
   * Decode the original text. This should be called
   * when the best key is found.
   * @param void
   * @return string
   */
  public function decodeOriginal() {
    $sDecipheredText = implode('', array_map(function($cChar) {
      if (preg_match('/[a-z]/i', $cChar)) {
        return str_replace($cChar, $this->aKey[array_search($cChar, $this->aAlphabet)], $cChar);
      }
      return $cChar;
    }, str_split($this->sOrigEncrypted)));

    return $sDecipheredText;
  }

  /**
   * Attempt to decrypt our string.
   * @param void
   * @return string
   */
  public function decrypt() {
    $fScore = 0;

    do {
      $bBetterKey = false;
      for ($i = 0; $i < 25; $i++) {
        for ($j = $i + 1; $j < 26; $j++) {
          $iChar1 = $this->aKey[$i];
          $iChar2 = $this->aKey[$j];
          $this->aKey[$i] = $iChar2;
          $this->aKey[$j] = $iChar1;
          $this->iNumKeysGenerated++;

          $sDecipheredText = $this->runCipher();

          $fScore = $this->obtainScore($sDecipheredText);

          if ($fScore > $this->fBestScore) {
            // Found a better score/key, store it.
            $this->fBestScore = $fScore;
            $this->aBestKey = $this->aKey;
            $bBetterKey = true;

            $sDecipheredText = $this->runCipher();
          } else {
            // Did not get a better score with swapped
            // key. Swap back.
            $this->aKey[$i] = $iChar1;
            $this->aKey[$j] = $iChar2;
          }
        }
      }
    } while ($bBetterKey);

    return $this->fBestScore;
  }
}
