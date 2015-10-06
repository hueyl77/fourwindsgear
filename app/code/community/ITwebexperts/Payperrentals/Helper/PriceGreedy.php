<?php
class ITwebexperts_Payperrentals_Helper_Pricegreedy extends Mage_Core_Helper_Abstract
{
    private $seconds = null;
    private $amount = null;
    private $combination = null;

    public function setSeconds($seconds) {
        $this->seconds = $seconds;
    }


    /**
     * Gives the seconds to make amount
     * @param int $amount Number to transform
     * @return Array of coins. The method returns -1 if the amount isn't transformable. If an error occurs the method returns FALSE.
     */
    public function getCombination($amount) {
        $this->combination = [];

        if (!isset($amount)) {
            $this->amount = 0;
        }
        $this->amount = $amount;
        $this->combination = $this->makeCombination($this->amount);

        return $this->combination;
    }

    /**
     * Get seconds with each amounts
     * @param array $seconds Array of seconds
     * @return Returns an associative array with key => Seconds type, value => number of seconds
     */
    public function groupBy($seconds) {
        $groups = [];
        if (!isset($seconds) || $seconds < 0) {
            return FALSE;
        }
        for ($k = 0; $k < count($seconds); $k++) {
            $second = strval($seconds[$k]);
            if (!array_key_exists($second, $groups) && !isset($groups[$second])) {
                $groups[$second] = 1;
            }
            else {
                $counter = $groups[$second] + 1;
                $groups[$second] = $counter;
            }
        }

        return $groups;
    }

    /**
     * Greedy Algorithm approach
     * @param int $amount Number to exchange
     * @return Array of coins
     */
    private function makeCombination($amount) {
        $maxSecond = 0;
        $sum = 0;

        while ($sum < $amount) {
            $maxSecond = $this->getLocalMax($amount, $sum);
            if (!isset($maxSecond)) {
                return -1;
            }

            array_push($this->combination, $maxSecond);
            $sum = $sum + $maxSecond;
        }

        return $this->combination;
    }

    /**
     * Retrieves the first biggest coin available
     * @param array $seconds
     * @param int $amount Number to be exchanged
     * @param int $sum Current amount exchanged
     * @return Returns the first bigger coin available
     */
    private function getLocalMax($amount, $sum) {
        $max = null;
        $seconds = $this->seconds;
        for ($k = 0; $k < count($seconds); $k++) {
            if (($seconds[$k] + $sum <= $amount + $seconds[count($seconds) - 1])) {
                $max = $seconds[$k];
                break;
            }
        }
        return $max;
    }
}