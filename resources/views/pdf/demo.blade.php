<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Plan de Desarrollo del Proyecto</title>

    <style>
        /* ===== FORZAR COLORES EN PDF ===== */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            background: #ffffff !important;
            color: #333;
        }

        /* ===== HEADER FIJO ===== */
        .pdf-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 90px;
            padding: 18px 40px;
            background-color: #5B34C9 !important;
            background-image: linear-gradient(135deg, #5B34C9, #341e68) !important;
            color: #fff !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left img {
            height: 45px;
        }

        .header-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .header-text span {
            font-size: 13px;
            opacity: 0.9;
        }

        .header-right {
            font-size: 13px;
            text-align: right;
        }

        /* ===== FOOTER FIJO ===== */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background-color: #f1f1f1 !important;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            z-index: 1000;
        }

        .pdf-footer strong {
            color: #5B34C9;
        }

        /* ===== CONTENIDO ===== */
        main.container {
            /* padding-top = header + margen extra, padding-bottom = footer */
            padding: 120px 40px 80px;
            max-width: 900px;
            margin: 0 auto;
        }

        h2 {
            color: #5B34C9;
            margin-bottom: 20px;
            page-break-after: avoid;
        }

        .fase {
            margin-bottom: 25px;
            padding: 18px 20px;
            border-radius: 10px;
            background: #f8f7fc;
            border-left: 5px solid #5B34C9;
            page-break-inside: avoid;
            /* evita que la fase se rompa */
        }

        .fase h3 {
            margin-top: 0;
            color: #341e68;
            font-size: 16px;
        }

        ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }

        ul li {
            margin-bottom: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ===== PDF & PAGE SETTINGS ===== */
        @page {
            margin: 0;
            /* header/footer fijos */
        }

        @media print {
            body {
                margin: 0;
            }

            /* fuerza un padding-top constante al inicio de cada página */
            main.container {
                padding-top: 120px;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER FIJO -->
    <header class="pdf-header">
        <div class="header-left">
            <img src="https://via.placeholder.com/120x50?text=LOGO" alt="Logo">
            <div class="header-text">
                <h1>Plan de Desarrollo del Proyecto</h1>
                <span>Fases y Tareas</span>
            </div>
        </div>
        <div class="header-right">
            <span>Fecha:</span><br>
            <strong>29/01/2026</strong>
        </div>
    </header>

    <!-- CONTENIDO -->
    <main class="container">
        <h2>Fases de Desarrollo</h2>

        <div class="fase">
            <h3>1. Análisis y Requerimientos</h3>
            <ul>
                <li>Levantamiento de información con el cliente</li>
                <li>Definición de objetivos y alcance</li>
                <li>Identificación de requerimientos funcionales</li>
                <li>Identificación de requerimientos técnicos</li>
                <li>Documentación de requerimientos</li>
            </ul>
        </div>

        <div class="fase">
            <h3>2. Diseño</h3>
            <ul>
                <li>Diseño de arquitectura del sistema</li>
                <li>Diseño de base de datos</li>
                <li>Diseño de interfaces UI/UX</li>
                <li>Definición de flujos de navegación</li>
                <li>Validación del diseño</li>
            </ul>
        </div>

        <div class="fase">
            <h3>3. Desarrollo</h3>
            <ul>
                <li>Configuración del entorno de desarrollo</li>
                <li>Programación de funcionalidades</li>
                <li>Integración de APIs y servicios</li>
                <li>Control de versiones</li>
                <li>Documentación técnica</li>
            </ul>
        </div>

        <div class="fase">
            <h3>4. Pruebas</h3>
            <ul>
                <li>Pruebas unitarias</li>
                <li>Pruebas funcionales</li>
                <li>Pruebas de integración</li>
                <li>Corrección de errores</li>
                <li>Validación final</li>
            </ul>
        </div>

        <div class="fase">
            <h3>5. Implementación</h3>
            <ul>
                <li>Despliegue en ambiente productivo</li>
                <li>Configuración de servidor</li>
                <li>Pruebas en producción</li>
                <li>Capacitación al usuario</li>
                <li>Entrega del proyecto</li>
            </ul>
        </div>

        <div class="fase">
            <h3>6. Mantenimiento y Soporte</h3>
            <ul>
                <li>Soporte técnico continuo</li>
                <li>Corrección de incidencias</li>
                <li>Mejoras y optimización</li>
                <li>Actualizaciones de seguridad</li>
                <li>Monitoreo del sistema</li>
            </ul>
        </div>
    </main>

    <!-- FOOTER FIJO -->
    <footer class="pdf-footer">
        © 2026 <strong>Soudev Team</strong> • Documento generado en HTML
    </footer>

</body>

</html>