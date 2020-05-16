<?php

namespace App\Services;

class SystemResourceService
{

    const DATE_OUTPUT_FORMAT = 'H:i jS M Y';

    private $ramTotal = 0;

    private $ramFree = 0;

    private $ramAvailable = 0;

    public function toArray()
    {

        $tempDegreesC = $this->getTemperature();
        $tempDegreesF = (($tempDegreesC / 5) * 9) + 32;

        $ramUsage = $this->getRamUsage();

        return [

            // Times
            'booted' => date(self::DATE_OUTPUT_FORMAT, 4322),
            'uptime_time' => $this->getUptime(),
            'system_time' => date(self::DATE_OUTPUT_FORMAT),

            // Usage Percentages
            'cpu_percent' => $this->getCpuUsage(),
            'ram_percent' => $ramUsage,
            'ram_total' => $this->ramTotal,
            'disk_percent' => '100', // @todo Add this later
            'disk_total' => '600', // @todo Add this laster

            // Temperature
            'temp_c' => $tempDegreesC,
            'temp_f' => $tempDegreesF,

            // GPS Data
            'gps_lat' => '0.0',
            'gps_lng' => '0.0.',
            'gps_alt' => '0',
            'gps_spd' => '0',
        ];
    }

    public function getTemperature(): string
    {
        $data = shell_exec('vcgencmd measure_temp | cut -d \'=\' -f2');
        if (!$data) {
            return '**not detected**';
        }
        return rtrim(trim($data), '\'C');
    }

    public function getCpuUsage(): int
    {
        $cpuUsage = trim(shell_exec("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage \"%\"}'"));
        return rtrim($cpuUsage, "%");
    }

    function getRamUsage(): int
    {
        $this->ramTotal = trim($this->removeKbSuffix(shell_exec("grep 'MemTotal' /proc/meminfo | cut -d : -f2")));
        $this->ramFree = trim($this->removeKbSuffix(shell_exec("grep 'MemFree' /proc/meminfo | cut -d : -f2")));
        $this->ramAvailable = trim($this->removeKbSuffix(shell_exec("grep 'MemAvailable' /proc/meminfo | cut -d : -f2")));
        return $this->ramTotal - $this->ramAvailable;
    }

    public function getUptime(): string
    {
        $str = @file_get_contents('/proc/uptime');
        $num = floatval($str);
        $num = intdiv($num, 60);
        $mins = $num % 60;
        $num = intdiv($num, 60);
        $hours = $num % 24;
        $num = intdiv($num, 24);
        $days = $num;
        return "{$days}d {$hours}h {$mins}m";
    }

    private function removeKbSuffix(string $string)
    {
        return str_replace(' kB', '', $string);
    }


}