<?php
class SistemaLog {
    private $conex;

     function __construct($conex) {
        $this->conex = $conex;
    }

        

        public function error( $mensaje,  $contexto = [], $idUsuario = null){
            $this->conex->log('error', $mensaje, $contexto, $idUsuario);
        }

        public function info( $mensaje,  $contexto = [],  $idUsuario = null){
            $this->conex->log('info', $mensaje, $contexto, $idUsuario);
        }

        public function warning( $mensaje,  $contexto = [], $idUsuario = null){
            $this->conex->log('warning', $mensaje, $contexto, $idUsuario);
        }

        public function evento( $mensaje,  $contexto = [],  $idUsuario = null){
            $this->conex->log('evento', $mensaje, $contexto, $idUsuario);
        }
}//fin de la clase

?>