# Informe Entrega 2 - Bases de datos IIC2413

## Datos del Alumno
| **Apellidos**       | **Nombres**          | **Número de Alumno** |
|---------------------|----------------------|----------------------|
| Le Roy Aros         | Fernanda José        |2466250J              |


## 1. Descripción y análisis del problema
 
	Describe aquí el planteamiento del problema y el análisis de la solución global

## 2. Solución aplicada

	Describe aquí la solución al problema

## 2.1 Limpieza de datos con PHP

Para la limpieza de datos con PHP, primero se crearon funciones con las cuales fue más sencillo revisar los CSV después. A continuación se explica brevemente cada una:

---

## Funciones de encoding y texto

### `fixEncoding($valor)`
Recibe una variable de tipo texto y la transforma con la función `str_replace()`. Sirve para eliminar caracteres que venían con la codificación Windows-1252 y que al leer el archivo con UTF-8 no se corregían.

### `quitarTildesYMinusculas($texto)`
Recibe una variable de tipo texto y, en caso de que contenga una tilde o una letra en mayúscula, las cambia dejándola sin tilde y en minúscula. Fue creada para evitar el problema de encoding roto en caracteres, y se aplica a todos los CSV, ya que así se permite el correcto cruce entre tablas sin errores de tipeo.

### `normalizarTexto($valor)`
Creada para aplicar las dos funciones anteriores —`fixEncoding` y `quitarTildesYMinusculas`— en una sola llamada. La razón es que en la mayoría de los CSV se invocaban ambas de forma consecutiva y, como las dos se encargan de normalizar variables de tipo texto, tenía sentido unirlas.

---

## Funciones de fechas

### `normalizarFecha($fecha)`
Recibe una variable de tipo texto que corresponde a una fecha y se encarga de retornarla en el formato correcto pedido por el enunciado: `AAAA-MM-DD`.

### `normalizarFechaHora($valor)`
Hace exactamente lo mismo que la función anterior, con la diferencia de que recibe una variable que contiene tanto la fecha como la hora. Se encarga de separarlas, normalizar cada parte por separado y luego juntarlas. Además, usa `checkdate()`, que verifica si la fecha es válida (que no sea un 35 de abril, por ejemplo).

### `normalizarFechaReservas($valor, $conHora = false)`
Caso particular para el CSV de reservas. Se encarga de verificar que tanto la fecha como la hora existan y, si ambas están presentes, retorna la variable normalizada en el formato pedido.

### `normalizarFechaEvento($valor)`
Creada para normalizar fechas del CSV de eventos, donde se encontraron casos con `//`, con `26` en vez de `2026`, o con el orden invertido en formato latino `DD-MM-AAAA`. La función se encarga de corregir esos casos y dejar la fecha en el formato requerido.

### `normalizarFechaNacimiento($fecha)`
Se encarga de normalizar la fecha de nacimiento de las personas.

> **Nota:** Algunas funciones son redundantes entre sí (especialmente las de fecha), pero fueron creadas así porque al revisar cada CSV se encontraban casos específicos distintos —fechas en formato latino, con `//`, entre otros—. Además, el código lo retomaba de un día para otro y cada vez quedaba más largo, por lo que a veces perdía el hilo de qué funciones ya existían y creaba una nueva. Al terminar la limpieza no quedó tiempo suficiente para organizar de mejor manera.

---

## Funciones de validación

### `validarFormatoRUN($run)`
Se encarga de quitar todos los espacios que pueda tener un RUT con `preg_match()` y verifica que la variable ingresada tenga un guion obligatorio, entre 1 y 8 dígitos en total, y que el último carácter sea un número o la letra `k`. Si el RUT viene en formato `xx.xxx.xxx-0`, elimina todos los puntos.

### `validarEmail($email)`
Quita todos los espacios que la variable ingresada pueda tener y luego aplica `filter_var()` con `FILTER_VALIDATE_EMAIL`, que es una función nativa de PHP para validar un correo electrónico.

---

## Funciones de normalización numérica y de contacto

### `normalizarPrecio($valor)`
Convierte cualquier variable —texto o número sucio— en un número entero limpio.

### `normalizarMonto($valor)`
Recibe una variable que corresponde a un monto y la normaliza; por ejemplo, si viene como `USD 100`, se queda solo con `100`, tal como lo pedía el enunciado.

### `normalizarTelefono($tel)`
Verifica que un número telefónico contenga solo dígitos —aunque la variable sea de tipo `string`— y que cumpla con el formato chileno de nueve dígitos. Si tiene menos o más, la función retorna `null`.

---

## Funciones de RUT y cliente

### `limpiarRun($run)`
Recibe una variable de tipo texto que representa un RUT, elimina los espacios al inicio y al final, y lo deja en minúscula (en caso de que tenga `K`, pasa a `k`). La idea es que, como todas las variables se dejan en minúscula, al cruzar tablas no haya discrepancias entre `xx.xxx.xxx-K` en un CSV y `xx.xxx.xxx-k` en otro.

### `normalizarTipoCliente($valor)`
Se encarga de que los tipos de cliente válidos sean únicamente: `"socio"`, `"persona"` y `"empresa"`; y en el caso de que venga como `"empresa-institucion"`, lo deja solo como `"empresa"`, tal como lo pedía el enunciado.

---

## Funciones auxiliares

### `cargarComunas($archivo)`
Lee el archivo `regiones_comunas.csv` y carga todas las comunas con sus respectivas regiones. Fue creada con la finalidad de comparar, en `personas_socios.csv`, que el atributo `comuna_nombre` pertenezca a la lista oficial de comunas.

---------------------------------------------------------------

Procesamiento de los 7 csv dados:
El primer archivo .csv que procesé fue cargos_administrativos.csv ya que era el que tenía la menor cantidad de datos, por ende se me hizo más fácil visualizar cuáles eran los problemas del csv. El csv original presenta solo 2 errores: en la fecha, el año no estaba escrito como AAAA sino en 2 dígitos, y además había un error de caracteres por una mala codificación de Windows-1252. En este caso, estos errores eran corregibles, por ende se corrigen y se van todas las filas del csv original a cargosOK.csv, quedando cargosERR.csv vacío (ya que no hubo ningún error incorregible), y en cargosLOG.csv se documentó todo.
El código en main.php en la parte de procesar cargos_administrativos.csv lo que hace a grandes rasgos es: primero se preparan las rutas de los archivos y punteros para leer y escribir, luego se extrae la primera fila para obtener los atributos y se copian a cada archivo csv creado, se recorre el archivo original fila por fila hasta llegar al final, y por cada fila se revisan las columnas, se quitan espacios en blanco y se aplican las funciones quitarTildesYMinusculas(fixEncoding($original)) en conjunto. Si el texto cambia después de eso, se escribe en el log para dar aviso, y luego se aplica normalizarFecha(). Si la fila pasa las pruebas se va al archivo OK, sino al ERR. En este caso ninguna fila se va al ERR, pero todo queda documentado en el log. Finalmente se cierran todos los archivos con fclose().

El segundo archivo .csv que procesé fue regiones_comunas.csv. Para limpiarlo se prepararon los archivos igual que el csv anterior y se validó la estructura de los datos: se verifica que cada fila tenga 4 columnas y que la primera no esté vacía; si no cumple, la fila se manda directamente a errores. Se recorre cada fila para reparar los problemas de encoding (Windows-1252), y si la columna es de índice 1 o 3 (nombres de región o comuna) se eliminan tildes y se convierte todo a minúscula. Si la fila es válida se registran sus correcciones en el log y se guarda en el archivo OK; si no, se escribe la razón en el log y se guarda en el ERR. En este caso ninguna fila era imposible de reparar, así que todas van al OK. Las funciones utilizadas son fixEncoding($valor) para todas las columnas, y quitarTildesYMinusculas($texto) aplicada condicionalmente solo en los índices 1 y 3.

El tercer archivo que procesé fue **sucursales_lugares.csv**. En él se usan las funciones `fixEncoding(valor)‘y‘quitarTildesYMinusculas(valor)` y `quitarTildesYMinusculas(
valor)‘y‘quitarTildesYMinusculas(texto)` dentro del bucle que recorre las primeras 5 columnas (índices 0 al 4) para reparar el encoding y estandarizar los textos del local. Para la columna 6 se usa `normalizarPrecio(valor)‘,quetomaelvalordelprecio,detectasiestaˊescritoentexto(como∗"nuevemil"∗)yloconvierteanuˊmero,obieneliminacualquiersıˊmbolononumeˊrico(como‘valor)`, que toma el valor del precio, detecta si está escrito en texto (como *"nueve mil"*) y lo convierte a número, o bien elimina cualquier símbolo no numérico (como `
valor)‘,quetomaelvalordelprecio,detectasiestaˊescritoentexto(como∗"nuevemil"∗)yloconvierteanuˊmero,obieneliminacualquiersıˊmbolononumeˊrico(como‘` o puntos) para dejar solo el entero. Finalmente se usa `normalizarFecha($fecha)` para las columnas 12 y 13.
También se validan los descuentos: si el valor es mayor a 100, la fila es inválida porque un descuento no puede superar el 100% del precio original. Los campos que el enunciado especifica como no nulos y que llegan vacíos se mandan al ERR, ya que no corresponde inventar datos que pueden afectar el esquema. Al final quedaron 492 líneas en sucursalesERR.csv y 1510 en sucursalesOK.csv, lo que significa que se limpiaron correctamente más de la mitad de las filas.

El cuarto archivo que procesé fue pagos_membresias.csv. En este csv usé 6 funciones en total, 5 propias y 1 de PHP. Se aplican fixEncoding($orig) y quitarTildesYMinusculas() en las columnas 2, 9 y 11 para reparar el encoding y normalizar el texto. Se usa normalizarFecha() en las columnas 5 y 10 para asegurar que las fechas de pago y vencimiento tengan el formato estándar. Para las columnas 6 y 8 se usa is_numeric() de PHP, y si el valor no es un número puro se lanza un error. Para la columna 1 se valida que el RUT incluya el guion usando las funciones mencionadas.
El archivo OK se lleva todas las líneas ya que todos los errores eran corregibles, aunque de todas formas quedan documentados en el log. Los problemas principales de este csv eran el formato de las fechas, el encoding roto, y campos nulos que el enunciado exigía que tuvieran datos.

El quinto archivo que procesé fue reservas_arriendos.csv. Las funciones utilizadas fueron normalizarFechaReservas(), quitarTildesYMinusculas(), limpieza numérica manual, limpiarRUN() y validarFormatoRUN(). Se reparan los caracteres corruptos para que el texto sea legible, se convierten nombres y estados a minúscula sin tildes para evitar inconsistencias al consultar la base de datos, y se limpia y valida el RUN contra el formato legal chileno. Si el RUN no cumple la estructura esperada, la fila se rechaza por falta de integridad.
Para las fechas se verifica que tanto fecha_inicio como fecha_fin incluyan el componente de hora. Si falta, la fila se rechaza porque no es posible inventar el momento exacto del arriendo. El alto volumen de filas en reservasERR.csv no se debe a un fallo del algoritmo, sino a la baja calidad de los datos de origen, los cuales no satisfacen los requisitos mínimos de consistencia definidos para el sistema. Todo queda documentado en el log.

El sexto archivo que procesé fue personas_socios.csv, uno de los más complejos ya que las personas tienen muchas reglas de negocio entrelazadas. Antes de entrar al bucle principal, el código carga en memoria el archivo regiones_comunasOK.csv usando la función cargarComunas(), de modo que no se acepta a ninguna persona que viva en una comuna que no exista en la lista oficial. Esto podría haberse hecho en la carga SQL, pero se decidió adelantarlo aquí para simplificar esa etapa.
Dentro del procesamiento se realizan las siguientes validaciones y correcciones:

RUN: Se normaliza y valida. Si es inválido, la fila se rechaza.
Email y teléfono: Si tienen mal formato, el código no rechaza la fila sino que deja el campo vacío y anota el problema en el log.
Categoría: Solo se aceptan tipos de persona válidos (socio_titular, beneficiario, etc.).
Validación cruzada de beneficiarios: Si una persona es beneficiario, se verifica que su parentesco sea válido (hijo, conyuge, etc.). Si alguien que no es beneficiario tiene un parentesco registrado, el campo se borra por ser información contradictoria.
RUN del titular vinculado: Se valida cuando corresponde.
Fecha de nacimiento: Se usa una función específica para verificar que sea una fecha real. Si es irrecuperable, se deja nula en lugar de rechazar la fila completa.
Fecha de incorporación: Se usa la normalización estándar.
Acceso al sistema: Si la persona tiene acceso (SI/NO), se normaliza su nivel de privilegios (admin, socio, etc.).

Los campos críticos que, si están vacíos, implican el rechazo directo de la fila son: RUN, Nombre, Comuna Oficial, Región y Sucursal.

El séptimo archivo que procesé fue eventos.csv. Este script funciona como un filtro de calidad que revisa cada aspecto de un evento —fechas, clientes, montos y asistentes— antes de que los datos entren a la base de datos.
Las validaciones y correcciones realizadas son:

RUN del cliente: Es obligatorio. Se usa limpiarRUN() y validarFormatoRUN() para asegurar que el identificador sea correcto; si el formato es inválido, la fila se rechaza.
Datos de empresa: Si el evento es para una empresa, se valida el RUT del contacto y se normalizan su nombre y cargo. Un RUT de contacto mal escrito también implica el rechazo de la fila.
Tipo de cliente: Se normaliza usando normalizarTipoCliente().
Fecha del evento: Es obligatoria y debe tener un formato válido. Si no se puede recuperar, la fila se rechaza.
Fecha de contratación: Si viene con errores, el código es más flexible: deja la fecha vacía en lugar de descartar el evento completo, asumiendo que es preferible tener el registro sin esa fecha a perderlo del todo.
Lista de asistentes: Se espera una lista separada por punto y coma (;). Cada nombre se limpia individualmente (se quitan espacios y tildes) y se eliminan las entradas vacías para que la lista quede compacta.
Monto total: Es obligatorio. Si no es un número válido, la fila se descarta.
Pagos parciales (reserva y ejecución): Si los montos traen caracteres inválidos, se limpian. Si de plano no son números, se dejan vacíos en lugar de rechazar el evento, permitiendo que esos valores se revisen después.

Al final del proceso, eventosLOG.csv incluye un resumen estadístico con el total de líneas procesadas, cuántas fueron exitosas, cuántas fallaron, y el detalle de qué cambió en cada fila.


### 2.2 Carga de datos con Psql
	Incluir el detalle de la distribución de los datos en las tablas del esquema

### 2.3 Consultas SQL


## 3. Referencias y bibliografía externa
