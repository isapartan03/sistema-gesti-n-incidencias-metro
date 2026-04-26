<?php

class Filtro {
    private $palabrasProhibidas;
    private $variantes;

    public function __construct(){
        $this->palabrasProhibidas = [
            'insultos' => [
                'leves' =>[
                    'idiota', 'lerdo', 'lerda', 'mameluco','mameluca', 'mentecato','mentecata', 'imbécil', 'imbecil', 'estúpido', 'estupido', 'estúpida', 'estupida', 'atontao', 'atontaa', 'orate', 'subnormal', 'majadero','majadera', 'zoquete', 'analfabeto', 'analfabeta','ignorante', 'palurdo', 'palurda', 'sinvergüenza', 'sinverguenza', 'ladrón', 'ladron','ladrona', 'degenerado', 'degenerada', 'canalla', 'cagueta', 'abusador', 'abusadora', 'abusivo', 'abusiva', 'acosador', 'acosadora', 'adefesio', 'adefesia', 'adúltero', 'adultero','adúltera','adúltero', 'alzado', 'alzada', 'malandro', 'tuki', 'niche', 'cabeza de rodilla', 'chichón de piso', 'chichon de piso', 'chichó e piso', 'chichón e piso'
                ],
                'graves' =>[
                    'pendejo', 'maldito', 'retrasado', 'retrasada', 'hijo de puta', 'hijodeputa', 'hijo e puta', 'hijoeputa', 'mardito', 'coño e madre','coñoe madre', "coñoe' tú madre", 'coñoe tú madre', 'coño e tu madre', 'coño e tu pepa', 'coñoe tu pepa', "coñoe' tu pepa", 'come mierda', 'guevon', 'huevon', 'guevón', 'huevón', 'malparío', 'malpario', 'hijo er diablo', 'maldiciento', 'pandorga', 'marico', 'marica', 'maricon', 'maricón', 'maricona', 'maricóna', 'tri maldito', 'trimaldito', 'coño e su pepa', 'coñoe su pepa', 'mojonero', 'piripicho', 'recontra coñisimo de su madre', 'recontra coñisima de su madre', 'recontracoñisimo de su madre', 'recontracoñisima de su madre', 'rolitranco e mamaguebo', 'rolitranco e mamagueba', 'rolitrancoe mamaguebo', 'rolitrancoe mamagueba', 'rolitranco e mamahuevo', 'rolitranco e mamahueva', 'rolitrancoe mamahuevo', 'rolitrancoe mamahueva', 'maricota', 'mariquin', 'mariposon', 'mariposón', 'pargo', 'puto', 'puta', 'tragasables', 'traga sables', 'piroba', 'pirobo', 'mamalo', 'coñoesumadre', 'chupador de penes', 'chupame las bolas', 'maricos', 'me cago en la pinga', 'mecagoenlapinga', 'concha', 'perra', 'c0ñ0', 'm13rd4', ''
                ]
                ],
             'lenguaje_vulgar' =>[
                'sexual' =>[
                    'coño', 'pene', 'vagina', 'sexo', 'coito', 'violación', 'violacion', 'teta', 'toto','chupa teta', 'chupa culo', 'chupa pene', 'mamaguebo','mamagueba', 'mamahuevo', 'mamahueva', 'relambeverga', 'simelona', 'cara e teta', 'cabezae guevo','cabeza e guevo', 'cabezae huevo', 'cabeza e huevo', 'carae verga', "cara e verga", 'cuca caliente' ,'muerdealmohadas', 'muerde almohadas', 'mamate un guevo', 'violar'
                    ],
                    'corporal' =>[
                    'mierda', 'cagar', 'defecar', 'orinar', 'comemierda'
                    ]
                ],
                'discriminacion'=>[
                    'racismo' =>[
                        'achinado', 'choto', 'colepato', 'cole e pato', 'gay', 'lesbiana', 'lesbi', 'homosexual', 'marimacha'
                    ],
                    'genero' =>[
                        'machista', 'feminista', 'hembrista' ,'machirulo', 'machirula', 'onvre'
                    ]
                ],
                'violencia' =>[
                    'amenazar' =>[
                        'matar', 'asesinar', 'descuartizar', 'degollar', 'apuñalar', 'disparar',  
                    ],
                    'delitos' =>[
                        'asesino', 'secuestro'
                    ]
                ]
        ];

         $this->variantes = [
            'a' => ['4', '@'],
            'e' => ['3'],
            'i' => ['1', '!'],
            'o' => ['0'],
            's' => ['5', '$'],
            'l' => ['1'],
            't' => ['7']
        ];

        $this->patronesComunes = [
            '/[4@]/',
            '/[3]/',
            '/[1!]/',
            '/[0]/',
            '/[5$]/',
            '/[7]/'
        ];
    }

     public function verificarContenido($texto){
        // Si no hay palabras prohibidas definidas, retornar que está limpio
        $totalPalabras = 0;
        foreach ($this->palabrasProhibidas as $categoria => $subcategorias) {
            foreach ($subcategorias as $nivel => $palabras) {
                $totalPalabras += count($palabras);
            }
        }
        
        if ($totalPalabras === 0) {
            return [
                'inapropiado' => false,
                'palabras' => [],
                'categoria' => null,
                'nivel' => null
            ];
        }

        $texto = mb_strtolower($texto, 'UTF-8');
        $resultado = [
            'inapropiado' => false,
            'palabras' => [],
            'categoria' => null,
            'nivel' => null
        ];

        foreach ($this->palabrasProhibidas as $categoria => $subcategorias){
            foreach($subcategorias as $nivel => $palabras){
                // Verificar que el array de palabras no esté vacío
                if (empty($palabras)) {
                    continue;
                }
                
                foreach($palabras as $palabra){
                    // Saltar si la palabra está vacía
                    if (empty(trim($palabra))) {
                        continue;
                    }

                    // Verificación directa
                    if ($this->contienePalabra($texto, $palabra)){
                        $resultado['inapropiado'] = true;
                        $resultado['palabras'][] = $palabra;
                        $resultado['categoria'] = $categoria;
                        $resultado['nivel'] = $nivel;
                        continue;
                    }

                    // Verificación con variantes básicas
                    $patronVariante = $this->crearPatronVariante($palabra);
                    if ($patronVariante && preg_match($patronVariante, $texto)){
                        $resultado['inapropiado'] = true;
                        $resultado['palabras'][] = $palabra;
                        $resultado['categoria'] = $categoria;
                        $resultado['nivel'] = $nivel;
                        continue;
                    }
                }
            }
        }

        return $resultado;
    }
    
    public function filtrarTexto($texto){
        $textoFiltrado = $texto;
        
        // Verificar si hay palabras prohibidas definidas
        $totalPalabras = 0;
        foreach ($this->palabrasProhibidas as $categoria => $subcategorias) {
            foreach ($subcategorias as $nivel => $palabras) {
                $totalPalabras += count($palabras);
            }
        }
        
        if ($totalPalabras === 0) {
            return $textoFiltrado; // No hay palabras para filtrar
        }
        
        foreach ($this->palabrasProhibidas as $categoria => $subcategorias){
            foreach ($subcategorias as $nivel => $palabras){
                if (empty($palabras)) {
                    continue;
                }
                
                foreach ($palabras as $palabra){
                    if (empty(trim($palabra))) {
                        continue;
                    }
                    
                    // Reemplazar palabra directa
                    $textoFiltrado = $this->reemplazarPalabra($textoFiltrado, $palabra);
                    
                    // Reemplazar con variantes usando patrón
                    $patronVariante = $this->crearPatronVariante($palabra);
                    if ($patronVariante) {
                        $textoFiltrado = preg_replace_callback($patronVariante, 
                            function($matches) use ($palabra) {
                                return str_repeat('*', mb_strlen($matches[0]));
                            }, 
                            $textoFiltrado
                        );
                    }
                }
            }
        }

        return $textoFiltrado;
    }

    private function crearPatronVariante($palabra) {
        if (empty(trim($palabra))) {
            return false;
        }
        
        $caracteres = preg_split('//u', mb_strtolower($palabra), -1, PREG_SPLIT_NO_EMPTY);
        $patron = '';
        
        foreach ($caracteres as $char) {
            if (isset($this->variantes[$char])) {
                // Crear clase de caracteres: [a4@] por ejemplo
                $clase = array_merge([$char], $this->variantes[$char]);
                $patron .= '[' . implode('', array_map('preg_quote', $clase)) . ']';
            } else {
                $patron .= preg_quote($char, '/');
            }
        }
        
        return '/\b' . $patron . '\b/iu';
    }

    private function contienePalabra($texto, $palabra) {
        if (empty(trim($palabra))) {
            return false;
        }
        
        $patron = '/\b' . preg_quote($palabra, '/') . '\b/iu';
        return preg_match($patron, $texto) === 1;
    }

    private function reemplazarPalabra($texto, $palabra) {
        if (empty(trim($palabra))) {
            return $texto;
        }
        
        $patron = '/\b' . preg_quote($palabra, '/') . '\b/iu';
        return preg_replace($patron, str_repeat('*', mb_strlen($palabra)), $texto);
    }
    
    // Método para agregar palabras prohibidas dinámicamente
    public function agregarPalabraProhibida($categoria, $nivel, $palabra) {
        if (isset($this->palabrasProhibidas[$categoria][$nivel])) {
            $this->palabrasProhibidas[$categoria][$nivel][] = $palabra;
        }
    }
}


?>