<?php

namespace Ballen\Piplex\Commands;

use Ballen\Clip\Traits\RecievesArgumentsTrait;
use Ballen\Clip\Interfaces\CommandInterface;
use Ballen\Clip\Utilities\ArgumentsParser;
use PiPHP\GPIO\GPIO;
use PiPHP\GPIO\Pin\PinInterface;
use PiPHP\GPIO\Pin\InputPinInterface;
use PiPHP\GPIO\Pin\OutputPinInterface;

/**
 * Class IoCommand
 *
 * @package Ballen\Piplex\Commands
 */
class IoCommand extends PiplexBaseCommand implements CommandInterface
{

    use RecievesArgumentsTrait;

    /**
     * The GPIO Driver
     *
     * @var GPIO
     */
    private $gpio;

    /**
     * Disables GPIO functionality (great for dev/testing purposes.)
     *
     * @var bool
     */
    private $disableGPIO = false;

    /**
     * DaemonCommand constructor.
     *
     * @param ArgumentsParser $argv
     */
    public function __construct(ArgumentsParser $argv)
    {
        parent::__construct($argv);

        // Disable GPIO.
        if ($argv->options()->has('disable-gpio')) {
            $this->disableGPIO = true;
        } else {
            $this->gpio = new GPIO();
        }

    }

    /**
     * Handle the command.
     */
    public function handle()
    {

        if ($this->disableGPIO) {
            $this->writeln('GPIO is disabled, exiting...');
            $this->exitWithError();
        }

        // Output some debug information...
        $this->writeln('Starting test IO runner...');

        // Set pin types...
        $pin = $this->gpio->getOutputPin(18);
        // Set the value of the pin high (turn it on)
        $pin->setValue(PinInterface::VALUE_LOW);

        // Turn LED's on and off 10 mins...
        for ($i = 0; $i < 10; $i++) {
            $this->writeln('Running: ' . $i);
            // Turn on...
            sleep(2);
            $pin->setValue(PinInterface::VALUE_HIGH);
            // Turn off...
            sleep(2);
            $pin->setValue(PinInterface::VALUE_LOW);
        }

        // Output that we've finished the loop...
        $this->writeln('Completed the test IO runner!');
        $this->exitWithSuccess();
    }

}