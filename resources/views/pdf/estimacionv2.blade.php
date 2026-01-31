<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Proyecto</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            color: #333;
            background-color: #f5f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== SECCIÓN HEADER ===== */
        .header-section {
            width: 100%;
            background: linear-gradient(135deg, #1a3a6c 0%, #254aa0 50%, #1a3a6c 100%);
            color: white;
            padding: 30px 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #d4af37 0%, #f0e68c 50%, #d4af37 100%);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-left {
            flex: 1;
        }

        .header-right {
            text-align: right;
            margin-left: 20px;
        }

        .project-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header-label {
            font-size: 11px;
            font-weight: 600;
            color: #d4af37;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .header-value {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
            display: block;
        }

        .header-title {
            text-align: center;
            margin: 20px auto;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(212, 175, 55, 0.3);
            max-width: 800px;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            letter-spacing: 1px;
        }

        .header-subtitle {
            font-size: 14px;
            color: #d4af37;
            margin-top: 5px;
            font-weight: 500;
        }

        /* ===== SECCIÓN CONTENIDO ===== */
        .content-section {
            flex: 1;
            width: 100%;
            padding: 25mm 25mm 15mm 25mm;
            position: relative;
            z-index: 10;
        }

        .content-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            min-height: calc(100vh - 300px);
        }

        /* ===== INFO SECTION ===== */
        .info-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px 40px;
            border-bottom: 3px solid #d4af37;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .info-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #254aa0;
            transition: transform 0.3s ease;
        }

        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .info-box .header-label {
            color: #254aa0;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .info-box .header-value {
            color: #1a3a6c;
            font-size: 14px;
            font-weight: 700;
        }

        /* ===== MEJORAS PARA TABLAS ===== */
        .phase-section {
            padding-top: 8mm !important;
            padding-bottom: 8mm !important;
            margin-bottom: 15px;
            position: relative;
        }

        .phase-title {
            background: linear-gradient(135deg, #1a3a6c 0%, #254aa0 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .phase-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            border-radius: 2px;
        }

        /* TABLAS */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        th {
            background: linear-gradient(135deg, #254aa0 0%, #1a3a6c 100%);
            color: white;
            padding: 14px 15px;
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-top-left-radius: 8px;
            border-left: 4px solid #d4af37;
        }

        th:last-child {
            border-top-right-radius: 8px;
        }

        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
            color: #495057;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e7f3ff;
            transition: background-color 0.2s;
        }

        .time-cell {
            font-weight: 700;
            color: #d4af37;
            text-align: center;
            width: 80px;
            background: linear-gradient(135deg, #fff9e6 0%, #fff3cc 100%);
            border-left: 2px solid #d4af37;
        }

        .empty-row td {
            background-color: #f8f9fa;
            color: #6c757d;
            font-style: italic;
            font-size: 12px;
            text-align: center;
            padding: 15px;
        }

        /* ===== FOOTER AJUSTADO ===== */
        .footer-section {
            width: 100%;
            background: linear-gradient(135deg, #1a3a6c 0%, #254aa0 100%);
            color: white;
            padding: 12px 40px;
            text-align: center;
            position: relative;
            margin-top: auto;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
            z-index: 100;
            min-height: 50px;
        }

        .footer-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #f0e68c, #d4af37, #f0e68c, transparent);
            border-radius: 2px;
            z-index: 101;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-left,
        .footer-right {
            flex: 1;
            min-width: 200px;
        }

        .footer-left {
            text-align: left;
        }

        .footer-right {
            text-align: right;
        }

        .footer-logo {
            font-size: 18px;
            font-weight: 800;
            color: #d4af37;
            margin-bottom: 4px;
            display: block;
        }

        .footer-text {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .footer-date {
            font-size: 11px;
            font-weight: 600;
            color: #d4af37;
            background: rgba(255, 255, 255, 0.1);
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 3px;
        }

        .footer-divider {
            height: 35px;
            width: 1px;
            background: linear-gradient(0deg, #d4af37 0%, #f0e68c 100%);
            margin: 0 15px;
            align-self: stretch;
        }

        /* ===== ESTILOS DE IMPRESIÓN CORREGIDOS ===== */
        @media print {

            /* ===== CONFIGURACIÓN GENERAL ===== */
            body {
                background: white;
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                display: block;
                width: 100%;
                height: 100%;
            }

            /* ===== HEADER SOLO EN PRIMERA PÁGINA ===== */
            .header-section {
                position: relative;
                padding: 8mm 20mm 4mm 20mm !important;
                background: linear-gradient(135deg, #1a3a6c 0%, #254aa0 50%, #1a3a6c 100%);
                page-break-after: avoid;
                break-after: avoid;
                height: auto;
                box-shadow: none !important;
                z-index: 1;
            }

            .header-section::before {
                position: relative !important;
            }

            .header-title {
                margin: 1mm 0 !important;
                padding: 1mm !important;
            }

            /* ===== CONTENIDO CON ESPACIO GARANTIZADO ===== */
            .content-section {
                padding: 3mm 25mm 20mm 25mm !important;
                /* 20mm abajo para espacio */
                margin: 0;
                width: 100%;
                position: relative;
                min-height: calc(100vh - 40mm);
            }

            .content-wrapper {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 auto;
                max-width: 100%;
                min-height: auto;
                background: white !important;

                /* IMPORTANTE: Margen inferior en todas las páginas */
                padding-bottom: 25mm !important;
                /* Espacio para evitar footer */
            }

            .info-section {
                padding: 6mm 0 !important;
                margin-bottom: 12px;
                page-break-inside: avoid;
                break-inside: avoid;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            }

            /* ===== CORRECCIÓN CRÍTICA: Evitar contenido debajo del footer ===== */

            /* 1. Zona de exclusión en TODAS las páginas */
            body::after {
                content: '';
                display: block;
                height: 20mm !important;
                /* Zona donde NO puede entrar contenido */
                width: 100%;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 9998;
                background: transparent;
                pointer-events: none;
            }

            /* 2. En nueva página, comenzar con márgen */
            .phase-section {
                padding: 8mm 0 6mm 0 !important;
                /* 8mm arriba en nueva página */
                margin-bottom: 8px !important;

                /* Permitir división si es necesario */
                page-break-inside: auto !important;
                break-inside: auto !important;
            }

            /* 3. Si es la primera sección después de salto, más espacio */
            .phase-section:first-of-type {
                padding-top: 5mm !important;
                /* Menos espacio si es primera */
            }

            /* 4. Si NO es primera, más espacio (porque viene después de salto) */
            .content-section .phase-section:not(:first-of-type) {
                padding-top: 15mm !important;
                /* Más espacio después de salto de página */
            }

            /* 5. Control de tablas */
            thead {
                display: table-header-group !important;
            }

            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            td,
            th {
                page-break-inside: avoid !important;
            }

            /* 6. Si una tabla es muy larga y causa salto, ajustar */
            table {
                page-break-inside: auto !important;
                margin-bottom: 10mm !important;
                /* Espacio después de tabla */
            }

            /* ===== FOOTER FIJO CORREGIDO ===== */
            .footer-section {
                position: fixed !important;
                left: 0;
                right: 0;
                bottom: 5mm !important;
                /* 5mm del borde (más razonable) */
                width: 100%;
                height: 15mm !important;
                min-height: 15mm !important;
                padding: 3mm 20mm !important;
                background: linear-gradient(135deg, #1a3a6c 0%, #254aa0 100%) !important;
                z-index: 9999 !important;
                /* Z-index ALTO para estar encima */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-inside: avoid !important;
                border-top: 2px solid #d4af37 !important;

                /* IMPORTANTE: Solo en última página */
                display: none !important;
            }

            .footer-section::before {
                display: block !important;
                height: 2px !important;
            }

            /* MOSTRAR footer SOLO en la última página */
            body:last-of-type .footer-section {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            /* OCULTAR footer en TODAS las demás páginas */
            body:not(:last-of-type) .footer-section {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                height: 0 !important;
                min-height: 0 !important;
                max-height: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* ===== AJUSTES PARA ÚLTIMA PÁGINA ===== */
            body:last-of-type .content-section {
                padding-bottom: 25mm !important;
                /* Más espacio en última página */
            }

            body:last-of-type .content-wrapper {
                padding-bottom: 10mm !important;
                /* Extra espacio para footer */
            }

            /* Última tabla tiene menos espacio abajo */
            body:last-of-type .phase-section:last-child {
                padding-bottom: 8mm !important;
                margin-bottom: 5mm !important;
            }

            /* ===== REGLA DE SEGURIDAD: NUNCA contenido en últimos 25mm ===== */
            /* Esto previene que cualquier contenido entre en el área del footer */
            * {
                max-height: calc(100% - 25mm) !important;
            }

            /* ===== ESTILOS ADICIONALES ===== */
            .info-box:hover {
                transform: none !important;
                box-shadow: none !important;
            }

            .footer-content {
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                gap: 10px;
            }

            .info-box {
                box-shadow: none !important;
                border: 1px solid #e0e0e0 !important;
                transition: none !important;
            }
        }

        /* ===== CONFIGURACIÓN DE PÁGINA CORREGIDA ===== */
        @page {
            margin: 0 !important;
            padding: 0 !important;
            size: auto;
        }

        @page :first {
            margin-top: 0 !important;
        }

        @page :last {
            margin-bottom: 10mm !important;
            /* Espacio para footer en última página */
        }

        /* Márgen inferior en TODAS las páginas para evitar contenido cerca del borde */
        @page {
            margin-bottom: 20mm !important;
        }
    </style>
</head>

<body>
    <!-- ===== SECCIÓN 1: HEADER ===== -->
    <div class="header-section">
        <div class="header-content">
            <div class="header-left">
                <span class="header-label">Nombre del Proyecto</span>
                <span class="header-value">[Nombre del Proyecto]</span>
                <span class="header-label">Encargado del Proyecto</span>
                <span class="header-value">[Nombre del Encargado]</span>
            </div>
            <div class="header-right">
                <span class="header-label">Periodo del Reporte</span>
                <span class="header-value">DD/MM/AAAA - DD/MM/AAAA</span>
                <span class="header-label">Preparado por</span>
                <span class="header-value">[Nombre]</span>
            </div>
        </div>

        <div class="header-title">
            <h1>📊 REPORTE DE PROYECTO</h1>
            <div class="header-subtitle">Seguimiento y Control de Actividades</div>
        </div>
    </div>

    <!-- ===== SECCIÓN 2: CONTENIDO ===== -->
    <div class="content-section">
        <div class="content-wrapper">
            <!-- ===== INFO SECTION ===== -->
            <div class="info-section">
                <div class="info-box">
                    <span class="header-label">Proyecto</span>
                    <span class="header-value">[Nombre del Proyecto]</span>
                </div>
                <div class="info-box">
                    <span class="header-label">Periodo</span>
                    <span class="header-value">DD/MM/AAAA - DD/MM/AAAA</span>
                </div>
                <div class="info-box">
                    <span class="header-label">Encargado</span>
                    <span class="header-value">[Nombre]</span>
                </div>
                <div class="info-box">
                    <span class="header-label">Preparado por</span>
                    <span class="header-value">[Nombre]</span>
                </div>
            </div>

            <!-- ===== TABLAS MÚLTIPLES PARA PROBAR ===== -->
            <div class="phase-section">
                <div class="phase-title">🔍 Análisis - Página 1</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarea / Actividad</th>
                            <th class="time-cell">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1. Revisión inicial de requerimientos</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>2. Análisis de viabilidad técnica</td>
                            <td class="time-cell">6 h</td>
                        </tr>
                        <tr>
                            <td>3. Diseño de arquitectura preliminar</td>
                            <td class="time-cell">8 h</td>
                        </tr>
                        <tr>
                            <td>4. Documentación de especificaciones</td>
                            <td class="time-cell">6 h</td>
                        </tr>
                        <tr>
                            <td>5. Revisión con equipo técnico</td>
                            <td class="time-cell">5 h</td>
                        </tr>
                        <tr>
                            <td>6. Ajustes basados en retroalimentación</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>7. Validación con stakeholders</td>
                            <td class="time-cell">6 h</td>
                        </tr>
                        <tr>
                            <td>8. Documentación final del análisis</td>
                            <td class="time-cell">8 h</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="phase-section">
                <div class="phase-title">📋 Planificación - Página 2</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarea / Actividad</th>
                            <th class="time-cell">Tiempo</th>
                            <th class="time-cell">Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1. Definición de cronograma detallado</td>
                            <td class="time-cell">5 h</td>
                            <td class="time-cell">Planificador</td>
                        </tr>
                        <tr>
                            <td>2. Asignación de recursos del equipo</td>
                            <td class="time-cell">3 h</td>
                            <td class="time-cell">Jefe Proyecto</td>
                        </tr>
                        <tr>
                            <td>3. Establecimiento de hitos clave</td>
                            <td class="time-cell">4 h</td>
                            <td class="time-cell">Jefe Proyecto</td>
                        </tr>
                        <tr>
                            <td>4. Planificación de riesgos y contingencias</td>
                            <td class="time-cell">6 h</td>
                            <td class="time-cell">Analista</td>
                        </tr>
                        <tr>
                            <td>5. Definición de métricas de seguimiento</td>
                            <td class="time-cell">4 h</td>
                            <td class="time-cell">Analista</td>
                        </tr>
                        <tr>
                            <td>6. Creación de plan de comunicación</td>
                            <td class="time-cell">3 h</td>
                            <td class="time-cell">Comunicaciones</td>
                        </tr>
                        <tr>
                            <td>7. Revisión y aprobación del plan</td>
                            <td class="time-cell">4 h</td>
                            <td class="time-cell">Director</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="phase-section">
                <div class="phase-title">💻 Desarrollo - Página 3</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarea / Actividad</th>
                            <th class="time-cell">Tiempo</th>
                            <th class="time-cell">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1. Configuración del entorno de desarrollo</td>
                            <td class="time-cell">6 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                        <tr>
                            <td>2. Desarrollo de módulo principal</td>
                            <td class="time-cell">20 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                        <tr>
                            <td>3. Desarrollo de interfaz de usuario</td>
                            <td class="time-cell">15 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                        <tr>
                            <td>4. Integración de componentes</td>
                            <td class="time-cell">10 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                        <tr>
                            <td>5. Pruebas unitarias</td>
                            <td class="time-cell">8 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                        <tr>
                            <td>6. Corrección de errores identificados</td>
                            <td class="time-cell">6 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                        <tr>
                            <td>7. Optimización de rendimiento</td>
                            <td class="time-cell">5 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                        <tr>
                            <td>8. Documentación del código</td>
                            <td class="time-cell">4 h</td>
                            <td class="time-cell">✓</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== SECCIÓN 3: FOOTER ===== -->
    <div class="footer-section">
        <div class="footer-content">
            <div class="footer-left">
                <span class="footer-logo">PROYECTO</span>
                <div class="footer-text">© 2026 - Todos los derechos reservados</div>
                <div class="footer-text">Documento confidencial - Uso interno</div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-right">
                <div class="footer-text">📄 Reporte generado el:</div>
                <div class="footer-date">30 de Enero de 2026</div>
                <div class="footer-text">Versión 1.0</div>
            </div>
        </div>
    </div>
</body>

</html>