Fichas Profesionales - Plugin de WordPress
📋 Descripción
Plugin completo para crear y gestionar fichas profesionales en WordPress. Permite a los usuarios crear perfiles profesionales detallados con información de contacto, experiencia, educación, habilidades y redes sociales.
Versión: 2.0
Autor: Sergio LN
Sitio Web: https://www.publikate.cl

✨ Características

✅ Custom Post Type para fichas profesionales
✅ Meta Boxes personalizados con campos estructurados
✅ Galería de imágenes con imagen principal y secundarias
✅ Lightbox/Slideshow con navegación entre imágenes
✅ Diseño moderno y responsive
✅ Múltiples estilos de visualización
✅ Shortcodes flexibles
✅ Integración con redes sociales
✅ Restricción de permisos (usuarios solo pueden editar su propia ficha)
✅ Dashboard widget con estadísticas
✅ Validación de formularios
✅ Upload de imagen de perfil
✅ Template personalizado para single
✅ Internacionalización (i18n)
✅ Navegación con teclado (flechas, ESC)
✅ Soporte táctil para móviles (swipe)


📁 Estructura de Archivos
fichas-profesionales/
├── fichas-profesionales.php     # Archivo principal
├── css/
│   ├── style.css                # Estilos frontend
│   └── admin-style.css          # Estilos admin
├── js/
│   └── admin-scripts.js         # JavaScript admin
├── includes/
│   ├── meta-boxes.php           # Meta boxes personalizados
│   └── template-display.php     # Funciones de visualización
├── templates/
│   └── single-ficha.php         # Template single personalizado
├── languages/                   # Archivos de traducción
└── README.md                    # Esta documentación

🚀 Instalación
Método 1: Manual

Descarga todos los archivos del plugin
Crea la estructura de carpetas según el esquema anterior
Sube la carpeta fichas-profesionales a /wp-content/plugins/
Activa el plugin desde el menú Plugins de WordPress
Ve a Fichas en el menú de administración

Método 2: ZIP

Comprime todos los archivos en fichas-profesionales.zip
Ve a Plugins > Añadir nuevo > Subir plugin
Selecciona el archivo ZIP
Activa el plugin


📝 Uso
Crear una Ficha

Ve a Fichas > Añadir nueva
Completa los campos:

Información Personal: Foto, nombre, título, biografía
Información Profesional: Especialidad, experiencia, empresa, habilidades
Contacto: Email, teléfono, ubicación, sitio web
Redes Sociales: LinkedIn, Twitter, Instagram, Facebook, GitHub


Publica tu ficha

Shortcodes Disponibles
1. [ficha_completa]
Muestra la ficha completa del usuario actual.
php[ficha_completa]
Parámetros:

user_id - ID del usuario (opcional, por defecto el usuario actual)
style - Estilo de visualización: default, minimal, detailed

Ejemplos:
php[ficha_completa]
[ficha_completa user_id="5"]
[ficha_completa style="minimal"]
2. [ficha_tarjeta]
Muestra una tarjeta pequeña de una ficha.
php[ficha_tarjeta ficha_id="123"]
[ficha_tarjeta user_id="5"]
3. [fichas_listado]
Muestra un grid con múltiples fichas.
php[fichas_listado]
[fichas_listado numero="12" columnas="3"]
Parámetros:

numero - Cantidad de fichas a mostrar (default: 12)
columnas - Número de columnas en el grid (default: 3)


🎨 Personalización
Estilos CSS
Puedes personalizar los colores editando las variables CSS en style.css:
css:root {
    --publikate-red: #d32f2f;
    --publikate-red-dark: #b71c1c;
    --publikate-red-light: #e57373;
    /* ... más variables ... */
}
Templates
Para personalizar el template single, copia templates/single-ficha.php a tu tema activo:
tu-tema/single-ficha.php

🔧 Funciones de Desarrollo
Obtener ID de ficha de un usuario
php$user_id = 5;
$ficha_id = get_user_meta($user_id, 'ficha_id', true);
Obtener datos de una ficha
php$ficha_id = 123;
$nombre = get_post_meta($ficha_id, '_ficha_nombre_completo', true);
$email = get_post_meta($ficha_id, '_ficha_email', true);
$habilidades = get_post_meta($ficha_id, '_ficha_habilidades', true);
Renderizar una ficha programáticamente
php$ficha = get_post(123);
echo fichas_render_default($ficha);

📱 Responsive
El plugin es completamente responsive y se adapta a:

✅ Desktop (1200px+)
✅ Tablet (768px - 1199px)
✅ Mobile (< 768px)


🔒 Seguridad
El plugin implementa:

✅ Validación de nonces
✅ Sanitización de datos
✅ Escape de outputs
✅ Verificación de permisos
✅ Protección contra CSRF
✅ Validación de tipos de post


🌍 Internacionalización
El plugin está preparado para traducciones:
Text Domain: fichas-profesionales
Domain Path: /languages
Para crear una traducción:

Usa Poedit o Loco Translate
Crea archivo .po con el código de idioma
Guarda en /languages/


⚙️ Requisitos

WordPress 5.0+
PHP 7.2+
MySQL 5.6+


🐛 Solución de Problemas
Las fichas no se muestran en el frontend

Ve a Ajustes > Enlaces permanentes
Haz clic en "Guardar cambios" (esto regenera los rewrite rules)

Los estilos no se cargan

Verifica que la carpeta /css/ existe
Limpia la caché del navegador
Verifica permisos de archivos (644)

Error al subir imagen

Verifica permisos de la carpeta /wp-content/uploads/
Aumenta el límite de subida en php.ini:

   upload_max_filesize = 10M
   post_max_size = 10M

📄 Campos Disponibles
Información Personal

Foto de perfil
Nombre completo *
Título profesional
Biografía
Fecha de nacimiento
Nacionalidad

Información Profesional

Especialidad
Años de experiencia
Empresa actual
Cargo actual
Habilidades (separadas por comas)
Educación (multilínea)

Contacto

Email
Teléfono
Ciudad
País
Sitio web

Redes Sociales

LinkedIn
Twitter
Instagram
Facebook
GitHub


🔄 Changelog
Versión 2.0 (2025-01-03)

✅ Reescritura completa del plugin
✅ Agregados meta boxes personalizados
✅ Nuevos estilos modernos
✅ Sistema de shortcodes mejorado
✅ JavaScript para validación
✅ Template single personalizado
✅ Dashboard widget
✅ Mejor seguridad y sanitización

Versión 1.0 (Original)

Funcionalidad básica


📞 Soporte
Para soporte o consultas:

Sitio Web: https://www.publikate.cl
Email: soporte@publikate.cl


📜 Licencia
Este plugin es propiedad de Publikate.cl

👨‍💻 Créditos
Desarrollado por: Sergio LN
Para: Publikate.cl
Año: 2025
