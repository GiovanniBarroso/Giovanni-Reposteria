<?php
interface Resumible
{
    public function muestraResumen(): string;
}






// RESPUESTA MEDIANTE COMENTARIO PREGUNTA 6
// Dado que `muestraResumen()` ya está declarado como abstracto en `Dulce`,
// las clases hijas deben implementarlo debido a la herencia.
// La interfaz sirve como una "regla de contrato" adicional para otras clases
// que no hereden de `Dulce` pero quieran usar el mismo método.



?>