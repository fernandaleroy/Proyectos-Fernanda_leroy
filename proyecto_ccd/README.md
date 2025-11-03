# Proyecto ciencia datos: (Sin título por ahora)

## Descripción breve del proyecto 📁:
La motivación para desarrollar este proyecto surge por dos razones principales. En primer lugar, responde a un interés personal por investigar el impacto de las redes sociales en aspectos como la concentración, la creatividad y la protección de los datos personales. En segundo lugar, busca cumplir un propósito académico: aplicar de manera autónoma los conocimientos adquiridos en el curso, especialmente aquellos relacionados con Machine Learning, con el fin de poner en práctica las herramientas aprendidas y fortalecer mi formación profesional.

Asimismo, este proyecto representa una oportunidad para comenzar a construir un portafolio personal que reúna mis trabajos extracurriculares, permitiendo reflejar no solo mis habilidades técnicas en el área, sino también mi visión, mis intereses y la evolución de mi enfoque a lo largo de la carrera.

Para llevarlo a cabo, es necesario primero aterrizar la pregunta de investigación a un nivel cuantificable, transformándola en algo que pueda medirse y analizarse mediante datos. Dado que abarcar todas las redes sociales resultaría un proyecto demasiado amplio, me centraré en una sola plataforma que ofrezca acceso a información mediante APIs abiertas, de manera que los datos disponibles permitan abordar preguntas de investigación vinculadas a la creatividad, la concentración o la protección de la información personal.

Una vez revisadas las posibles fuentes de datos provenientes de distintas redes sociales, se definirá la línea de investigación específica, junto con los objetivos y las preguntas de investigación que orientarán el desarrollo del proyecto.

### Exploración de APIs para el proyecto:
En esta sección detallo las principales APIs de redes sociales que puedo utilizar para obtener datos y realizar análisis relacionados con creatividad, concentración y privacidad.
Me interesa evaluar qué tipo de información ofrece cada una y qué posibilidades de análisis podrían surgir a partir de esos datos.

1. YouTube Data API (v3)

Acceso: Pública con clave gratuita a través de Google Cloud Console

*Tipo de datos disponibles:*

Videos: título, descripción, etiquetas, fecha de publicación, duración, vistas, likes y comentarios.

Canales: nombre, descripción, cantidad de suscriptores y país.

Listas de reproducción (playlists).

Comentarios de usuarios y metadatos de interacción.

Con estos datos puedo analizar distintos aspectos:

Creatividad:
Puedo estudiar la diversidad de títulos y descripciones entre canales o categorías, analizar el lenguaje utilizado (por ejemplo, la presencia de palabras asociadas a emociones o creatividad), y explorar la originalidad temática comparando similitudes semánticas entre videos o la temática en si.
Concentración:
Puedo observar la duración promedio de los videos con mayor cantidad de likes y reacciones para evaluar si los contenidos más breves están asociados a una menor atención, o analizar los patrones de consumo a lo largo del día o la semana.
Privacidad:
Podría examinar los comentarios para detectar cuánta información personal comparten los usuarios de manera voluntaria.

2. Pinterest API (v5)

Acceso: Gratuita, aunque por lo que vi requiere crear una aplicación en el Pinterest Developer Portal

*Tipo de datos disponibles:*

Pines: imágenes, descripciones, enlaces, etiquetas, número de repines y reacciones.

Tableros: nombre, tema y cantidad de pines.

Usuarios: información pública disponible.

Con estos datos podría trabajar en diferentes líneas:

Creatividad visual:
Analizar la clasificación de imágenes por tema o color dominante (hay estudios sobre la pérdida del color en la actualidad) o detectar patrones visuales en contenido creativo (por ejemplo, en áreas de arte, diseño o fotografía).

Concentración:
Estudiar la frecuencia de publicación o interacción según la hora del día o el día de la semana.

Privacidad:
Revisar qué tan personales son las descripciones o títulos de los pines, observando posibles patrones de exposición de información.

No me parece muy buena esta API en lo personal porque siento que ver la creatividad visual involucra mucha subjetividad, con respecto a la concentración en mi impresión personal pinterest no genera tanta adicción como es el caso de tiktok, instagram, youtube ya que son solo fotos o un par de videos pero no hay demasiada interacción en la aplicación por lo que creo que no vería muchas cosas interesantes en los datos. Quizá me centraría más en la privacidad. 

3. X (Twitter) API (v2)

Acceso: Requiere registro y clave gratuita (plan Free o Basic) desde developer.x.com


Tipo de datos disponibles:

Tweets públicos: texto, fecha, hashtags, cantidad de likes, retweets e idioma.

Usuarios: biografía, número de seguidores y ubicación (si es pública sino no).

Búsqueda de tweets por hashtag o palabra clave.

Línea temporal de usuarios (limitada en el plan gratuito).

Con esta información podría realizar varios tipos de análisis:

Creatividad:
Analizar la originalidad o diversidad del lenguaje utilizado en hashtags y tomar ciertos años ejemplo 2012,2013,2014 y 2022,2023,2024 y ver si la forma de redactar ha cambiado identificando cierto patrones, o explorar redes semánticas de usuarios que comparten contenido similar(enfocarme en 1 u 2 contenidos máximos de intéres)

Concentración:
Estudiar los horarios de publicación para identificar ritmos de atención digital, o la frecuencia de tweets por usuario y por tema a lo largo del tiempo. Este es el tema que más me interesa. 

