<?php

/**
 * Implementacion del algoritmo Dhont
 *
 * @author Juan Manuel Fernandez <juanmf@gmail.com>
 * @author Angel Gonzalo Ruiz <angoru@gmail.com>
 */
class Dhont
{
    /**
     * Los divisores de las listas/partidos. Estructura:
     * {identificadorLista1 => 1, identificadorLista2 => 1, ...}
     * 
     * @var array
     */
    private $divisores;
    private $listas;
    private $bancas;
    private $reparto_bancas;
    private $resultados;

    /**
     * @param $bancas
     * @param $listas
     */
    function __construct($bancas, $listas) 
    {
        
        $this->listas = $listas;
        $this->bancas = $bancas;

        $this->resultados = array_fill_keys(array_keys($listas), 0);

    }
        
    /**
     * Devuelve un array con cantidad de eltos. = a la cantidad de bancas a repartir
     * en cada elto, el valor es igual al valor correspondiente en array $listas.
     * La metodologia para repartir es el sistema DHONT.
     * 
     * 
     * @return array distribucion de bancas a listas {banca => $lista}
     */
    private function repartirBancas()
    {
        asort($this->listas);
        $this->listas = array_reverse($this->listas, true);
        $reparto_bancas = $this->_createBancasDivisores();
        foreach ($reparto_bancas as $key => $b) {
            $max = $this->_getMaxCociente();
            $reparto_bancas[$key] = $max['identificador'];
        }
        return $reparto_bancas;
    }


    /**
     * Devuelve un array con cantidad de representantes por partido
     * 
     * @return array distribucion de bancas a listas {banca => $lista}
     */
    public function escribeBancas()
    {
        $reparto_bancas = $this->repartirBancas();

        $partidos = array_keys($this->listas);

        $resultados = array_fill_keys($partidos, 0);

        foreach ($reparto_bancas as $i => $banco) {
            foreach ($partidos as $j => $partido) {
                // echo $banco .' --- '. $partido . PHP_EOL;
                if( $banco == $partido ){
                    $resultados[$partido] ++;
                }
            }
        }

        return $resultados;

    }    
    
    /**
     * Devuelve un array de bancas con eltos = false, y cantidad igual a $bancas.
     * y un array de divisores con claves sacadas de las claves de $listas y valores = 1 
     *  
     * @param int   $bancas La cantidad de bancas a repartit.
     * @param array $listas Las listas/partidos que se disputan bancas. Estructura:
     * {identificadorLista1 => $votos1, identificadorLista2 => $votos2, ...}
     * 
     * @return array con estructura [$bancas, $divisores]. en detalle: <pre>
     *    [false, false, ...], // count = $bancas 
     */
    private function _createBancasDivisores()
    {
        $reparto_bancas = array_fill(0, $this->bancas, false);
        $this->divisores = array_combine(
            array_keys($this->listas), 
            array_fill(0, count($this->listas), 1)
        );
        return $reparto_bancas;
    }

    /**
     * Usa los divisores, en el estado en que se encuentren para encontrar el mayor 
     * cociente, incrementa en 1 el divisor del mayor cociente. Esto reduce las 
     * chances de vovler a elegir el mismo partido/lista la proxima vez.
     * 
     * @param array $listas Las listas/partidos que se disputan bancas. Estructura:
     * {identificadorLista1 => $votos1, identificadorLista2 => $votos2, ...}
     * 
     * @return array Estructutra {'identificador' => $lista, 'cociente' => $maxCociente}
     */
    private function _getMaxCociente()
    {
        $max = array('identificador' => null, 'cociente' => -1);
        foreach ($this->listas as $identificador => $votos) {
            $cociente = $votos / $this->divisores[$identificador];
            if ($max['cociente'] < $cociente) {
                // si algun max cociente empata con otro, prevalece el de la lista 
                // con mas votos. Si tambien empatan en votos (chocan con divisor 1), se jode la 2da.
                $max = array('identificador' => $identificador, 'cociente' => $cociente);
            }
        }
        $this->divisores[$max['identificador']]++;
        return $max;
    }
}
