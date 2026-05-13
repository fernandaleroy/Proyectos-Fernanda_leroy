<?php
 # FUNCIONES DE APOYO Y LIMPIEZA

function fixEncoding($valor) {
    $replacements = [
        'Ã©' => 'é', 'Ã­' => 'í', 'Ã³' => 'ó', 'Ãº' => 'ú', 'Ã¡' => 'á',
        'Ã±' => 'ñ', 'Ã ' => 'à', 'Ã‰' => 'É', 'Ã"' => 'Ó','Ãš' => 'Ú', 'Ã'  => 'Á', '–'=> 'ñ',
    ];
    return str_replace(array_keys($replacements), array_values($replacements), $valor);
}

function quitarTildesYMinusculas($texto) {
    $originales = array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ');
    $modificados = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'n', 'n');
    $texto = str_replace($originales, $modificados, $texto);
    return strtolower(trim($texto));
}

function normalizarPrecio($valor) {
    $temp = quitarTildesYMinusculas($valor);
    if ($temp === 'nueve mil') return 9000;
    $limpio = preg_replace('/[^0-9]/', '', $valor);
    return is_numeric($limpio) ? (int)$limpio : $valor;
}

function normalizarFecha($fecha) {
    if (empty($fecha)) return null;
    if (preg_match('/^\d{4}-\d{2}$/', $fecha)) return $fecha . "-01";
    
    $partes = explode("-", $fecha);
    if (count($partes) == 3) {
        $anio = (strlen($partes[2]) == 2) ? "20" . $partes[2] : $partes[2];
        $mes = str_pad($partes[1], 2, "0", STR_PAD_LEFT);
        $dia = str_pad($partes[0], 2, "0", STR_PAD_LEFT);
        return "$anio-$mes-$dia";
    }
    return $fecha;
}

function normalizarFechaHora($valor) {
    if (empty($valor)) return null;
    
    $partes_espacio = explode(" ", trim($valor));
    $fecha_sucia = $partes_espacio[0];
    $hora = (isset($partes_espacio[1])) ? $partes_espacio[1] : "00:00";

    $separador = (strpos($fecha_sucia, '-') !== false) ? "-" : "/";
    $p = explode($separador, $fecha_sucia);
    
    if (count($p) == 3) {
        $anio = (strlen($p[2]) == 2) ? "20" . $p[2] : $p[2];
        $mes = str_pad($p[1], 2, "0", STR_PAD_LEFT);
        $dia = str_pad($p[0], 2, "0", STR_PAD_LEFT);
        if (!checkdate((int)$mes, (int)$dia, (int)$anio)) return null;
        return "$anio-$mes-$dia $hora";
    }
    return null;
}

function normalizarFechaReservas($valor, $conHora = false) {
    if (empty($valor)) return null;
    $valor = trim($valor);


    if (preg_match('/[a-zA-Z]/', $valor)) return null;

    
    $valor = preg_replace('/\/+/', '-', $valor);

    
    $partes_espacio = explode(" ", $valor);
    $fecha_str = trim($partes_espacio[0]);
    $hora_str  = isset($partes_espacio[1]) ? trim($partes_espacio[1]) : null;

    
    if ($conHora && $hora_str === null) return null;

    
    $p = explode("-", $fecha_str);
    if (count($p) !== 3) return null;

    
    if (strlen($p[0]) == 4) {
        $anio = $p[0]; $mes = $p[1]; $dia = $p[2];
    } else {
        $dia  = $p[0];
        $mes  = $p[1];
        $anio = (strlen($p[2]) == 2) ? "20" . $p[2] : $p[2];
    }

    
    if (!checkdate((int)$mes, (int)$dia, (int)$anio)) return null;

    $fecha_ok = "$anio-" . str_pad($mes,2,'0',STR_PAD_LEFT) . "-" . str_pad($dia,2,'0',STR_PAD_LEFT);

    return $hora_str ? "$fecha_ok $hora_str" : $fecha_ok;
}

function validarFormatoRUN($run) {
    return preg_match('/^\d{1,8}-[\dkK]$/', trim($run));
}

function normalizarRUN($run) {
    return strtolower(trim($run));
}

function validarEmail($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

function normalizarTelefono($tel) {
    $limpio = preg_replace('/[^0-9]/', '', trim($tel));
    return (strlen($limpio) === 9) ? $limpio : null;
}

function normalizarFechaNacimiento($fecha) {
    $fecha = trim($fecha);
    if (empty($fecha)) return null;
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        $p = explode('-', $fecha);
        if (checkdate((int)$p[1], (int)$p[2], (int)$p[0])) return $fecha;
    }
    return null;
}

function cargarComunas($archivo) {
    $comunas = [];
    $ptr = fopen($archivo, 'r');
    if (!$ptr) return $comunas;
    fgetcsv($ptr, 0, ";"); 
    while (($fila = fgetcsv($ptr, 0, ";")) !== FALSE) {
        if (isset($fila[1])) {
            
            $nombre = trim($fila[1]);
            $codigo_region = trim($fila[2]);
            $comunas[$nombre] = $codigo_region;
        }
    }
    fclose($ptr);
    return $comunas;
}


function normalizarTexto($valor) {
    return quitarTildesYMinusculas(fixEncoding(trim($valor)));
}

function normalizarFechaEvento($valor) {
    $valor = trim($valor);
    if (empty($valor)) return null;

    $valor = preg_replace('/\//', '-', $valor);
    $partes = explode('-', $valor);
    if (count($partes) !== 3) return null;

    if (strlen($partes[0]) === 4) {
        [$anio, $mes, $dia] = $partes;
    } else {
        [$dia, $mes, $anio] = $partes;
        if (strlen($anio) === 2) $anio = '20' . $anio;
    }

    $dia = str_pad($dia, 2, '0', STR_PAD_LEFT);
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);

    if (!checkdate((int)$mes, (int)$dia, (int)$anio)) return null;

    return "$anio-$mes-$dia";
}

function limpiarRUN($run) {
    $run = trim($run);
    $run = strtolower($run);

    return preg_replace('/[^0-9k\-]/', '', $run);
}

function normalizarMonto($valor) {
    $valor = trim($valor);
    if ($valor === '') return null;
    $limpio = preg_replace('/[^0-9]/', '', $valor);
    if ($limpio === '') return false;
    return (int)$limpio;
}

function normalizarTipoCliente($valor) {
    $normalizado = quitarTildesYMinusculas(fixEncoding(trim($valor)));

    if ($normalizado === 'empresa-institucion') {
        return 'empresa';
    }

    $validos = ['socio', 'persona', 'empresa'];

    return in_array($normalizado, $validos)
        ? $normalizado
        : null;
}

/**
 * 1. PROCESAMIENTO DE CARGOS ADMINISTRATIVOS
 */
$cargos_orig = 'cargos_administrativos.csv';
$cargos_ok   = 'cargosOK.csv';
$cargos_err  = 'cargosERR.csv';
$cargos_log  = 'cargosLOG.csv';

$ptr_origen = fopen($cargos_orig, 'r');
$ptr_ok     = fopen($cargos_ok, 'w');
$ptr_err    = fopen($cargos_err, 'w');
$ptr_log    = fopen($cargos_log, 'w');

$encabezado = fgetcsv($ptr_origen, 0, ";");
fputcsv($ptr_ok, $encabezado, ";");
fputcsv($ptr_err, $encabezado, ";");

fputs($ptr_log, "Iniciando limpieza para $cargos_orig\n");
$numero_linea = 1;

while (($fila = fgetcsv($ptr_origen, 0, ";")) !== FALSE) {
    $numero_linea++;
    $es_valido = true;
    foreach ($fila as $key => $valor) {
        $original = trim($valor);
        
        $corregido = quitarTildesYMinusculas(fixEncoding($original));
        if ($original !== $corregido) {
            fputs($ptr_log, "Línea $numero_linea: Col $key normalizada ($original -> $corregido)\n");
        }
        $fila[$key] = $corregido;
    }
    if (empty($fila[0]) || strpos($fila[0], '-') === false) {
        $es_valido = false; fputs($ptr_log, "Línea $numero_linea: ERROR - RUN inválido\n");
    }
    $fila[3] = normalizarFecha($fila[3]);
    $fila[4] = normalizarFecha($fila[4]);

    if ($es_valido) fputcsv($ptr_ok, $fila, ";"); 
    else fputcsv($ptr_err, $fila, ";");
}
fclose($ptr_origen); fclose($ptr_ok); fclose($ptr_err); fclose($ptr_log);

/**
 * 2. PROCESAMIENTO DE REGIONES Y COMUNAS
 */
$regiones_orig = 'regiones_comunas.csv';
$regiones_ok   = 'regiones_comunasOK.csv';
$regiones_err  = 'regiones_comunasERR.csv';
$regiones_log  = 'regiones_comunasLOG.csv';

$ptr_reg_origen = fopen($regiones_orig, 'r');
$ptr_reg_ok     = fopen($regiones_ok, 'w');
$ptr_reg_err    = fopen($regiones_err, 'w');
$ptr_reg_log    = fopen($regiones_log, 'w');

$encabezado_reg = fgetcsv($ptr_reg_origen, 0, ";");
fputcsv($ptr_reg_ok, $encabezado_reg, ";");
fputcsv($ptr_reg_err, $encabezado_reg, ";");

fputs($ptr_reg_log, "Iniciando proceso de limpieza para $regiones_orig\n");
$num_linea_reg = 1;

while (($fila = fgetcsv($ptr_reg_origen, 0, ";")) !== FALSE) {
    $num_linea_reg++;
    $es_valido = true;
    $cambios_en_linea = [];
    if (count($fila) < 4 || empty(trim($fila[0]))) {
        $es_valido = false;
        fputs($ptr_reg_log, "Línea $num_linea_reg: ERROR - Fila incompleta\n");
    } else {
        foreach ($fila as $key => $valor) {
            $original = trim($valor);
            $paso1 = fixEncoding($original);
            if ($key == 1 || $key == 3) { $corregido = quitarTildesYMinusculas($paso1); }
            else { $corregido = $paso1; }
            if ($original !== $corregido) { $cambios_en_linea[] = "($original → $corregido)"; }
            $fila[$key] = $corregido;
        }
        if (!is_numeric($fila[0]) || !is_numeric($fila[2])) {
            $es_valido = false; fputs($ptr_reg_log, "Línea $num_linea_reg: ERROR - Códigos\n");
        }
    }
    if ($es_valido) {
        if (!empty($cambios_en_linea)) fputs($ptr_reg_log, "Línea $num_linea_reg: Corregido " . implode(", ", $cambios_en_linea) . "\n");
        fputcsv($ptr_reg_ok, $fila, ";");
    } else { fputcsv($ptr_reg_err, $fila, ";"); }
}
fclose($ptr_reg_origen); fclose($ptr_reg_ok); fclose($ptr_reg_err); fclose($ptr_reg_log);


/**
 * 3. PROCESAMIENTO DE SUCURSALES Y LUGARES
 */
$suc_orig = 'sucursales_lugares.csv';
$suc_ok   = 'sucursalesOK.csv';
$suc_err  = 'sucursalesERR.csv';
$suc_log  = 'sucursalesLOG.csv';

$ptr_suc_origen = fopen($suc_orig, 'r');
$ptr_suc_ok     = fopen($suc_ok, 'w');
$ptr_suc_err    = fopen($suc_err, 'w');
$ptr_suc_log    = fopen($suc_log, 'w');

$encabezado_suc = fgetcsv($ptr_suc_origen, 0, ";");
fputcsv($ptr_suc_ok, $encabezado_suc, ";");
fputcsv($ptr_suc_err, $encabezado_suc, ";");

fputs($ptr_suc_log, "Iniciando proceso de limpieza para $suc_orig\n");
$num_linea_suc = 1;

while (($fila = fgetcsv($ptr_suc_origen, 0, ";")) !== FALSE) {
    $num_linea_suc++;
    $es_valido = true;
    $log_cambios = [];

    
    for ($i = 0; $i <= 4; $i++) {
        $orig = $fila[$i];
        $fila[$i] = quitarTildesYMinusculas(fixEncoding($fila[$i]));
        if ($orig !== $fila[$i]) $log_cambios[] = "Texto col $i corregido";
    }

    
    $precio_orig = $fila[6];
    if ($precio_orig !== "") {
        $fila[6] = normalizarPrecio($precio_orig);
        if ($precio_orig !== (string)$fila[6]) $log_cambios[] = "Precio corregido ($precio_orig -> {$fila[6]})";
    }

    
    if (!empty($fila[7]) && is_numeric($fila[7]) && $fila[7] > 100) {
        $es_valido = false;
        fputs($ptr_suc_log, "Línea $num_linea_suc: ERROR - Descuento > 100 ({$fila[7]})\n");
    }

    
    foreach([12, 13] as $idx) {
        $f_orig = $fila[$idx];
        $fila[$idx] = normalizarFecha($fila[$idx]);
        if ($f_orig !== $fila[$idx]) $log_cambios[] = "Fecha col $idx corregida";
    }

    
    if (empty($fila[0]) || empty($fila[2]) || empty($fila[3]) || $fila[6] === "") {
        $es_valido = false;
        fputs($ptr_suc_log, "Línea $num_linea_suc: ERROR - Vacíos obligatorios\n");
    }

    if ($es_valido) {
        if (!empty($log_cambios)) fputs($ptr_suc_log, "Línea $num_linea_suc: " . implode(", ", $log_cambios) . "\n");
        fputcsv($ptr_suc_ok, $fila, ";");
    } else { fputcsv($ptr_suc_err, $fila, ";"); }
}
fclose($ptr_suc_origen); fclose($ptr_suc_ok); fclose($ptr_suc_err); fclose($ptr_suc_log);

/**
 * 4. PROCESAMIENTO DE PAGOS MEMBRESIAS
 */
$pagos_orig = 'pagos_membresias.csv';
$pagos_ok   = 'pagosOK.csv';
$pagos_err  = 'pagosERR.csv';
$pagos_log  = 'pagosLOG.csv';

$ptr_pag_origen = fopen($pagos_orig, 'r');
$ptr_pag_ok     = fopen($pagos_ok, 'w');
$ptr_pag_err    = fopen($pagos_err, 'w');
$ptr_pag_log    = fopen($pagos_log, 'w');

$encabezado_pag = fgetcsv($ptr_pag_origen, 0, ";");
fputcsv($ptr_pag_ok, $encabezado_pag, ";");
fputcsv($ptr_pag_err, $encabezado_pag, ";");

fputs($ptr_pag_log, "Iniciando limpieza para $pagos_orig\n");
$num_linea_pag = 1;

while (($fila = fgetcsv($ptr_pag_origen, 0, ";")) !== FALSE) {
    $num_linea_pag++;
    $es_valido = true;
    $log_cambios = [];

    
    foreach ([2, 9, 11] as $idx) {
        if (isset($fila[$idx])) {
            $orig = $fila[$idx];
            $fila[$idx] = quitarTildesYMinusculas(fixEncoding($orig));
            if ($orig !== $fila[$idx]) $log_cambios[] = "Col $idx normalizada";
        }
    }

    foreach ([1, 2, 3, 4, 5, 6, 8, 9] as $idx) {
        if (!isset($fila[$idx]) || trim($fila[$idx]) === "") {
            $es_valido = false;
            fputs($ptr_pag_log, "Línea $num_linea_pag: ERROR - Campo obligatorio nulo (Col $idx)\n");
            break;
        }
    }

    if ($es_valido) {
        
        $venc_orig = $fila[5];
        $fila[5] = normalizarFecha($fila[5]);
        if ($venc_orig !== $fila[5]) $log_cambios[] = "Fecha Vencimiento corregida";

        if (!empty($fila[10])) {
            $fila[10] = normalizarFecha($fila[10]);
        }

        
        if (!is_numeric($fila[6]) || !is_numeric($fila[8])) {
            $es_valido = false;
            fputs($ptr_pag_log, "Línea $num_linea_pag: ERROR - Montos no numéricos\n");
        }

        $fila[1] = limpiarRUN($fila[1]);
        if (!validarFormatoRUN($fila[1])) {
            $es_valido = false;
            fputs($ptr_pag_log, "Línea $num_linea_pag: ERROR - RUN inválido ({$fila[1]})\n");
        }
    }

    
    if ($es_valido) {
        if (!empty($log_cambios)) fputs($ptr_pag_log, "Línea $num_linea_pag: " . implode(", ", $log_cambios) . "\n");
        fputcsv($ptr_pag_ok, $fila, ";");
    } else {
        fputcsv($ptr_pag_err, $fila, ";");
    }
}

fclose($ptr_pag_origen); fclose($ptr_pag_ok); fclose($ptr_pag_err); fclose($ptr_pag_log);

/**
 * 5. PROCESAMIENTO DE RESERVAS ARRIENDOS
 */
$res_orig = 'reservas_arriendos.csv';
$ptr_res_origen = fopen($res_orig, 'r');
stream_filter_append($ptr_res_origen, 'convert.iconv.CP1252/UTF-8');
$ptr_res_ok     = fopen('reservasOK.csv', 'w');
$ptr_res_err    = fopen('reservasERR.csv', 'w');
$ptr_res_log    = fopen('reservasLOG.csv', 'w');

$encabezado_res = fgetcsv($ptr_res_origen, 0, ";");
fputcsv($ptr_res_ok,  $encabezado_res, ";");
fputcsv($ptr_res_err, $encabezado_res, ";");

fputs($ptr_res_log, "Iniciando limpieza para $res_orig\n");
$num_linea_res = 1;
$contador_res  = 1; 

while (($fila = fgetcsv($ptr_res_origen, 0, ";")) !== FALSE) {
    $num_linea_res++;
    $es_valido  = true;
    $log_cambios = [];

    if (!isset($fila[0]) || empty(trim($fila[0]))) {
        $nuevo_codigo = "RES-" . str_pad($contador_res, 5, "0", STR_PAD_LEFT);
        $fila[0] = $nuevo_codigo;
        $log_cambios[] = "Codigo generado ($nuevo_codigo)";
    }
    $contador_res++;

    foreach ($fila as $key => $valor) {
        $original = trim($valor);

        
        $temp = fixEncoding($original);


        if ($key === 8) {
            $temp = preg_replace('/\s+X\s*$/i', '', $temp);
            if ($original !== $temp) $log_cambios[] = "Col 8: eliminado sufijo X ({$original})";
        }

        if (in_array($key, [4, 6, 7, 8, 9])) {
            $fila[$key] = quitarTildesYMinusculas($temp);
        } else {
            $fila[$key] = $temp;
        }

        if ($original !== $fila[$key]) $log_cambios[] = "Col $key normalizada";
    }
    
    $fila[5] = limpiarRUN($fila[5]); 
    if (!validarFormatoRUN($fila[5])) {
        $es_valido = false;
        fputs($ptr_res_log, "Linea $num_linea_res: ERROR - RUN inválido ({$fila[5]})\n");
    }

    $obligatorios = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    foreach ($obligatorios as $idx) {
        if (!isset($fila[$idx]) || trim($fila[$idx]) === '') {
            $es_valido = false;
            fputs($ptr_res_log, "Linea $num_linea_res: ERROR - Campo obligatorio vacío (Col $idx)\n");
            break;
        }
    }

    if ($es_valido) {
        $f_res = normalizarFechaReservas($fila[1], false);
        $f_ini = normalizarFechaReservas($fila[2], true);  
        $f_fin = normalizarFechaReservas($fila[3], true);  
        $f_pag = !empty($fila[13]) ? normalizarFechaReservas($fila[13], false) : null;

        if (!$f_res || !$f_ini || !$f_fin) {
            $es_valido = false;
            fputs($ptr_res_log,
                "Linea $num_linea_res: ERROR - Fecha inválida o sin hora " .
                "(reserva:{$fila[1]} ini:{$fila[2]} fin:{$fila[3]})\n"
            );
        } else {
            $fila[1]  = $f_res;
            $fila[2]  = $f_ini;
            $fila[3]  = $f_fin;
            $fila[13] = $f_pag;
        }
    }

    if ($es_valido) {
        if (!in_array($fila[4], ['reservada', 'ejecutada', 'cancelada'])) {
            $es_valido = false;
            fputs($ptr_res_log, "Linea $num_linea_res: ERROR - Estado inválido ({$fila[4]})\n");
        }
    }

    if ($es_valido) {
        $fila[10] = (int)preg_replace('/[^0-9]/', '', $fila[10]);
        $fila[11] = (isset($fila[11]) && $fila[11] !== '')
            ? (int)preg_replace('/[^0-9]/', '', $fila[11])
            : '';
    }

    if ($es_valido) {
        $palabras = explode(' ', trim($fila[6]));
        if (count($palabras) < 2) {
            fputs($ptr_res_log,
                "Linea $num_linea_res: AVISO - Nombre incompleto ({$fila[6]})\n"
            );
            
        }
    }

    if ($es_valido) {
        if (!empty($log_cambios))
            fputs($ptr_res_log, "Linea $num_linea_res: " . implode(", ", $log_cambios) . "\n");
        fputcsv($ptr_res_ok, $fila, ";");
    } else {
        fputcsv($ptr_res_err, $fila, ";");
    }
}

fclose($ptr_res_origen);
fclose($ptr_res_ok);
fclose($ptr_res_err);
fclose($ptr_res_log);

#  6. PROCESAMIENTO DE PERSONAS SOCIOS

$comunas_validas = cargarComunas('regiones_comunasOK.csv');

$per_orig = 'personas_socios.csv';
$ptr_per_origen = fopen($per_orig, 'r');


stream_filter_append($ptr_per_origen, 'convert.iconv.CP1252/UTF-8');

$ptr_per_ok  = fopen('personasOK.csv', 'w');
$ptr_per_err = fopen('personasERR.csv', 'w');
$ptr_per_log = fopen('personasLOG.csv', 'w');

$encabezado_per = fgetcsv($ptr_per_origen, 0, ";");
fputcsv($ptr_per_ok,  $encabezado_per, ";");
fputcsv($ptr_per_err, $encabezado_per, ";");

fputs($ptr_per_log, "Iniciando limpieza para $per_orig\n");
$num_linea_per = 1;

while (($fila = fgetcsv($ptr_per_origen, 0, ";")) !== FALSE) {
    $num_linea_per++;
    $es_valido   = true;
    $log_cambios = [];


    foreach ($fila as $key => $valor) {
    $fila[$key] = fixEncoding(trim($valor));
}
    $fila[1] = normalizarTexto($fila[1]); // nombre
    $fila[5] = normalizarTexto($fila[5]); // direccion

    $fila[0] = normalizarRUN($fila[0]);
    if (empty($fila[0]) || !validarFormatoRUN($fila[0])) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - RUN inválido ({$fila[0]})\n");
    }

    if (empty(trim($fila[1]))) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - Nombre vacío\n");
    }

    if (!empty($fila[2])) {
        if (!validarEmail($fila[2])) {
            
            fputs($ptr_per_log, "Linea $num_linea_per: AVISO - Email inválido, se deja nulo ({$fila[2]})\n");
            $fila[2] = '';
        }
    }

    foreach ([3, 4] as $col_tel) {
        if (!empty($fila[$col_tel])) {
            $tel_normalizado = normalizarTelefono($fila[$col_tel]);
            if ($tel_normalizado === null) {
                fputs($ptr_per_log,
                    "Linea $num_linea_per: AVISO - Teléfono col $col_tel inválido " .
                    "({$fila[$col_tel]}), se deja nulo\n"
                );
                $fila[$col_tel] = '';
            } else {
                $fila[$col_tel] = $tel_normalizado;
            }
        }
    }

    $comuna_normalizada = quitarTildesYMinusculas($fila[6]);
    if (empty($comuna_normalizada)) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - Comuna vacía\n");
    } elseif (!array_key_exists($comuna_normalizada, $comunas_validas)) {
        $es_valido = false;
        fputs($ptr_per_log,
            "Linea $num_linea_per: ERROR - Comuna no oficial ({$fila[6]})\n"
        );
    } else {
        $fila[6] = $comuna_normalizada;
    }

    if (empty($fila[7]) || !is_numeric($fila[7]) ||
        (int)$fila[7] < 1 || (int)$fila[7] > 16) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - Código región inválido ({$fila[7]})\n");
    }
    if (empty($fila[8])) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - Nombre región vacío\n");
    } else {
        $fila[8] = quitarTildesYMinusculas($fila[8]);
    }

    $tipo_persona = quitarTildesYMinusculas($fila[9]);
    $tipos_validos = ['socio_titular', 'beneficiario', 'adicional', 'invitado', 'administrativo', 'socio'];
    if (empty($tipo_persona) || !in_array($tipo_persona, $tipos_validos)) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - tipo_persona inválido ({$fila[9]})\n");
    } else {
        $fila[9] = $tipo_persona;
    }

    if (!empty($fila[10])) {
        $fila[10] = normalizarRUN($fila[10]);
        if (!validarFormatoRUN($fila[10])) {
            fputs($ptr_per_log,
                "Linea $num_linea_per: AVISO - run_socio_titular inválido, se deja nulo ({$fila[10]})\n"
            );
            $fila[10] = '';
        }
    }

    $parentesco = quitarTildesYMinusculas($fila[11]);
    $parentescos_validos = ['conyuge', 'hijo', 'hija', 'hijo/a'];

    if (!empty($parentesco)) {
        if ($tipo_persona === 'beneficiario') {
            if (!in_array($parentesco, $parentescos_validos)) {
                
                fputs($ptr_per_log,
                    "Linea $num_linea_per: AVISO - Parentesco inválido para beneficiario " .
                    "({$fila[11]}), se deja nulo\n"
                );
                $fila[11] = '';
            } else {
                $fila[11] = $parentesco;
            }
        } else {
            
            fputs($ptr_per_log,
                "Linea $num_linea_per: AVISO - Parentesco en tipo no beneficiario " .
                "({$fila[9]}), se deja nulo\n"
            );
            $fila[11] = '';
        }
    }


    if (!empty($fila[12])) {
        $fecha_nac = normalizarFechaNacimiento($fila[12]);
        if ($fecha_nac === null) {
            fputs($ptr_per_log,
                "Linea $num_linea_per: AVISO - Fecha nacimiento irrecuperable " .
                "({$fila[12]}), se deja nulo\n"
            );
        }
        $fila[12] = $fecha_nac ?? '';
    }


    foreach ([13, 14] as $col_fecha) {
        if (!empty($fila[$col_fecha])) {
            $f = normalizarFecha($fila[$col_fecha]);
            if ($f === null || $f === $fila[$col_fecha]) {
                
                fputs($ptr_per_log,
                    "Linea $num_linea_per: AVISO - Fecha col $col_fecha irrecuperable " .
                    "({$fila[$col_fecha]}), se deja nulo\n"
                );
                $fila[$col_fecha] = '';
            } else {
                $fila[$col_fecha] = $f;
            }
        }
    }

    $es_usuario = strtoupper(trim($fila[15]));
    if (!in_array($es_usuario, ['SI', 'NO'])) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - es_usuario_sistema inválido ({$fila[15]})\n");
    } else {
        $fila[15] = $es_usuario;
    }

    if (!empty($fila[16])) {
        $tipo_usr = quitarTildesYMinusculas($fila[16]);
        $tipos_usuario_validos = ['admin', 'administrativo', 'socio'];
        if (!in_array($tipo_usr, $tipos_usuario_validos)) {
            fputs($ptr_per_log,
                "Linea $num_linea_per: AVISO - tipo_usuario inválido ({$fila[16]}), se deja nulo\n"
            );
            $fila[16] = '';
        } else {
            $fila[16] = $tipo_usr;
        }
    }

    $sucursal = quitarTildesYMinusculas($fila[18]);
    if (empty($sucursal)) {
        $es_valido = false;
        fputs($ptr_per_log, "Linea $num_linea_per: ERROR - sucursal_base_nombre vacío\n");
    } else {
        $fila[18] = $sucursal;
    }

    if ($es_valido) {
        if (!empty($log_cambios))
            fputs($ptr_per_log, "Linea $num_linea_per: " . implode(", ", $log_cambios) . "\n");
        fputcsv($ptr_per_ok, $fila, ";");
    } else {
        fputcsv($ptr_per_err, $fila, ";");
    }
}

fclose($ptr_per_origen);
fclose($ptr_per_ok);
fclose($ptr_per_err);
fclose($ptr_per_log);

/**
 * 7. PROCESAMIENTO DE EVENTOS
 */
$eventos_orig = 'eventos.csv';
$eventos_ok   = 'eventosOK.csv';
$eventos_err  = 'eventosERR.csv';
$eventos_log  = 'eventosLOG.csv';

$ptr_origen = fopen($eventos_orig, 'r');
$ptr_ok     = fopen($eventos_ok,   'w');
$ptr_err    = fopen($eventos_err,  'w');
$ptr_log    = fopen($eventos_log,  'w');



$encabezado = fgetcsv($ptr_origen, 0, ';');
fputcsv($ptr_ok,  $encabezado, ';');
fputcsv($ptr_err, $encabezado, ';');

fputs($ptr_log, "Iniciando limpieza para $eventos_orig\n");
fputs($ptr_log, str_repeat('=', 60) . "\n");

$numero_linea = 1;
$total_ok  = 0;
$total_err = 0;

while (($fila = fgetcsv($ptr_origen, 0, ';')) !== FALSE) {
    $numero_linea++;
    $es_valido = true;
    $cambios   = [];

    $orig = trim($fila[1]);
    $corr = normalizarTexto($orig);
    if ($corr === '') {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - nombre_evento vacío\n");
    } else {
        if ($orig !== $corr) $cambios[] = "nombre_evento ($orig → $corr)";
        $fila[1] = $corr;
    }


    $orig = trim($fila[2]);
    $corr = normalizarFechaEvento($orig);
    if ($orig !== '' && $corr === null) {
        $cambios[] = "fecha_contratacion ($orig → vacía, formato inválido)";
        $fila[2] = '';
    } elseif ($corr !== null && $orig !== $corr) {
        $cambios[] = "fecha_contratacion ($orig → $corr)";
        $fila[2] = $corr;
    }

    $orig = trim($fila[3]);
    $corr = normalizarFechaEvento($orig);
    if ($corr === null) {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - fecha_evento inválida ($orig)\n");
    } else {
        if ($orig !== $corr) $cambios[] = "fecha_evento ($orig → $corr)";
        $fila[3] = $corr;
    }

    $orig = trim($fila[4]);
    $corr = normalizarTexto($orig);
    if ($corr === '') {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - lugar_nombre vacío\n");
    } else {
        if ($orig !== $corr) $cambios[] = "lugar_nombre ($orig → $corr)";
        $fila[4] = $corr;
    }


    $orig = trim($fila[5]);
    $corr = normalizarTexto($orig);
    if ($corr === '') {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - sucursal_nombre vacío\n");
    } else {
        if ($orig !== $corr) $cambios[] = "sucursal_nombre ($orig → $corr)";
        $fila[5] = $corr;
    }

    $orig = trim($fila[6]);
    $corr = normalizarTipoCliente($orig);
    if ($corr === null) {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - tipo_cliente inválido ($orig)\n");
    } else {
        if ($orig !== $corr) $cambios[] = "tipo_cliente ($orig → $corr)";
        $fila[6] = $corr;
    }


    $orig = trim($fila[7]);
    if ($orig === '') {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - run_cliente vacío\n");
    } else {
        $corr = limpiarRUN($orig);
        if (!validarFormatoRUN($corr)) {
            $es_valido = false;
            fputs($ptr_log, "Línea $numero_linea: ERROR - run_cliente formato inválido ($orig → $corr)\n");
        } else {
            if ($orig !== $corr) $cambios[] = "run_cliente ($orig → $corr)";
            $fila[7] = $corr;
        }
    }

    $orig = trim($fila[8]);
    $corr = normalizarTexto($orig);
    if ($corr === '') {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - nombre_cliente vacío\n");
    } else {
        if ($orig !== $corr) $cambios[] = "nombre_cliente ($orig → $corr)";
        $fila[8] = $corr;
    }


    $orig = trim($fila[9]);
    if ($orig !== '') {
        $corr = limpiarRUN($orig);
        if (!validarFormatoRUN($corr)) {
            $es_valido = false;
            fputs($ptr_log, "Línea $numero_linea: ERROR - rut_contacto_empresa formato inválido ($orig → $corr)\n");
        } else {
            if ($orig !== $corr) $cambios[] = "rut_contacto_empresa ($orig → $corr)";
            $fila[9] = $corr;
        }
    }

    
    $orig = trim($fila[10]);
    if ($orig !== '') {
        $corr = normalizarTexto($orig);
        if ($orig !== $corr) $cambios[] = "nombre_contacto_empresa ($orig → $corr)";
        $fila[10] = $corr;
    }


    $orig = trim($fila[11]);
    if ($orig !== '') {
        $corr = normalizarTexto($orig);
        if ($orig !== $corr) $cambios[] = "cargo_contacto ($orig → $corr)";
        $fila[11] = $corr;
    }

    
    $orig = trim($fila[12]);
    if ($orig !== '') {
        $asistentes = explode(';', $orig);
        $asistentes = array_map(fn($a) => normalizarTexto(trim($a)), $asistentes);
        $asistentes = array_filter($asistentes, fn($a) => $a !== '');
        $corr = implode(';', $asistentes);
        if ($orig !== $corr) $cambios[] = "lista_asistentes (normalizada)";
        $fila[12] = $corr;
    }

    
    $orig  = trim($fila[13]);
    $monto = normalizarMonto($orig);
    if ($monto === null || $monto === false) {
        $es_valido = false;
        fputs($ptr_log, "Línea $numero_linea: ERROR - monto_total_evento inválido ($orig)\n");
    } else {
        if ((string)$orig !== (string)$monto) $cambios[] = "monto_total_evento ($orig → $monto)";
        $fila[13] = $monto;
    }


    $orig  = trim($fila[14]);
    $monto = normalizarMonto($orig);
    if ($monto === false) {
        $cambios[] = "monto_pagado_reserva ($orig → vacío, no numérico)";
        $fila[14] = '';
    } elseif ($monto !== null && (string)$orig !== (string)$monto) {
        $cambios[] = "monto_pagado_reserva ($orig → $monto)";
        $fila[14] = $monto;
    }


    $orig  = trim($fila[15]);
    $monto = normalizarMonto($orig);
    if ($monto === false) {
        $cambios[] = "monto_pagado_ejecucion ($orig → vacío, no numérico)";
        $fila[15] = '';
    } elseif ($monto !== null && (string)$orig !== (string)$monto) {
        $cambios[] = "monto_pagado_ejecucion ($orig → $monto)";
        $fila[15] = $monto;
    }

    
    if ($es_valido) {
        fputcsv($ptr_ok, $fila, ';');
        $total_ok++;
        if (!empty($cambios)) {
            fputs($ptr_log, "Línea $numero_linea: OK con correcciones → " . implode(' | ', $cambios) . "\n");
        } else {
            fputs($ptr_log, "Línea $numero_linea: OK sin cambios\n");
        }
    } else {
        fputcsv($ptr_err, $fila, ';');
        $total_err++;
        if (!empty($cambios)) {
            fputs($ptr_log, "Línea $numero_linea: ERR con correcciones aplicadas → " . implode(' | ', $cambios) . "\n");
        }
    }
}

fputs($ptr_log, str_repeat('=', 60) . "\n");
fputs($ptr_log, "RESUMEN: Total=" . ($numero_linea - 1) . " | OK=$total_ok | ERR=$total_err\n");

fclose($ptr_origen);
fclose($ptr_ok);
fclose($ptr_err);
fclose($ptr_log);
?>