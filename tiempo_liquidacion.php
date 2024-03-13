<?php
class TiempoLiquidacion
{
    private $rutaTemporales;
    private $tiempoInicial;

    public function __construct($rutaTemporales)
    {
        $this->rutaTemporales = $rutaTemporales;
    }

    public function iniciar()
    {
        if (file_exists($this->rutaTemporales . "tiempos_liquidacion.txt")) {
            unlink($this->rutaTemporales . "tiempos_liquidacion.txt");
        }
        $this->tiempoInicial = microtime(true);
    }

    public function calcularTiempoTotal($proceso)
    {
        global $argv;
        if (empty($argv)) {
            $file = fopen($this->rutaTemporales . "tiempos_liquidacion.txt", "a");
            $tiempoFinal = microtime(true);
            $segundos = round(($tiempoFinal - $this->tiempoInicial), 2);
            $memUsage = round(memory_get_usage() / 1000);
            $memPeak = round(memory_get_peak_usage() / 1000);
            $this->debug("--Proceso $proceso - tiempo transcurrido(s) $segundos - memoria usada $memUsage KB - pico de memoria $memPeak");
            $this->tiempoInicial = microtime(true);
        }
    }

    private function debug($message)
    {
        // Implement your debug logic here
    }
}

$rutaTemporales = RUTA_TEMPORALES; // Replace with your actual path
$tiempoLiquidacion = new TiempoLiquidacion($rutaTemporales);
$tiempoLiquidacion->iniciar();
$tiempoLiquidacion->calcularTiempoTotal("Proceso1");



?>