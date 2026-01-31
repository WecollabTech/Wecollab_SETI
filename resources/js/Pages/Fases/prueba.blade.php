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
            padding: 0;
            margin: 0;
            color: #333;
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 40px auto 60px;
            background: white;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== HEADER ===== */
        .header {
            background: linear-gradient(135deg, #0d2a52 0%, #1a3a6c 50%, #0d2a52 100%);
            color: white;
            padding: 40px 50px 30px;
            position: relative;
            margin-bottom: 30px;
            flex-shrink: 0;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #d4af37 0%, #f0e68c 30%, #d4af37 70%, #d4af37 100%);
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50px;
            right: 50px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .header-left,
        .header-right {
            flex: 1;
        }

        .header-left {
            padding-right: 20px;
        }

        .header-right {
            text-align: right;
            padding-left: 20px;
            border-left: 2px solid rgba(255, 255, 255, 0.2);
        }

        .header-item {
            margin-bottom: 15px;
        }

        .header-label {
            font-size: 11px;
            font-weight: 600;
            color: #d4af37;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: block;
        }

        .header-value {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            display: block;
            line-height: 1.4;
        }

        .header-title {
            text-align: center;
            margin-top: 10px;
            padding: 20px;
            background: rgba(212, 175, 55, 0.15);
            border-radius: 10px;
            border: 2px solid rgba(212, 175, 55, 0.4);
        }

        .header-title h1 {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .header-subtitle {
            font-size: 15px;
            color: #d4af37;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            padding: 0 50px 40px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* ===== PHASE SECTION ===== */
        .phase-section {
            margin-bottom: 35px;
            break-inside: avoid;
        }

        .phase-title-box {
            background: linear-gradient(135deg, #0d2a52 0%, #1a3a6c 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
            position: relative;
            border-left: 4px solid #d4af37;
        }

        .phase-title-box::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            border-radius: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
            break-inside: avoid;
        }

        th {
            background: linear-gradient(135deg, #1a3a6c 0%, #0d2a52 100%);
            color: white;
            padding: 14px 18px;
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-top-left-radius: 8px;
        }

        th:last-child {
            border-top-right-radius: 8px;
        }

        td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
            color: #495057;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e7f3ff;
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
            padding: 18px;
        }

        /* ===== FOOTER - Mejorado con margen superior ===== */
        .footer {
            background: linear-gradient(135deg, #0d2a52 0%, #1a3a6c 100%);
            color: white;
            padding: 30px 50px;
            text-align: center;
            position: relative;
            width: 100%;
            margin-top: 60px;
            /* Margen superior para separar del contenido */
            flex-shrink: 0;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
        }

        .footer::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #d4af37 0%, #f0e68c 30%, #d4af37 70%, #d4af37 100%);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .footer-left,
        .footer-right {
            flex: 1;
            min-width: 250px;
        }

        .footer-left {
            text-align: left;
        }

        .footer-right {
            text-align: right;
        }

        .footer-logo {
            font-size: 26px;
            font-weight: 800;
            color: #d4af37;
            margin-bottom: 10px;
            display: block;
            letter-spacing: 1px;
        }

        .footer-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 6px;
            line-height: 1.6;
        }

        .footer-date {
            font-size: 14px;
            font-weight: 700;
            color: #d4af37;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 12px;
            border: 2px solid rgba(212, 175, 55, 0.3);
        }

        .footer-divider {
            height: 70px;
            width: 3px;
            background: linear-gradient(0deg, #d4af37 0%, #f0e68c 100%);
            margin: 0 40px;
            align-self: stretch;
            border-radius: 2px;
        }

        /* ===== DECORATIVE ELEMENTS ===== */
        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin: 40px 0;
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                min-height: auto;
            }

            .container {
                box-shadow: none;
                margin: 0;
                min-height: auto;
                display: block;
                /* Flexbox puede causar problemas en impresión */
            }

            .header {
                break-inside: avoid;
                margin-bottom: 25px;
            }

            .page-content {
                padding-top: 30px !important;
                padding-bottom: 20px !important;
                display: block;
            }

            /* Footer con margen superior y pegado al final */
            .footer {
                margin-top: 60px !important;
                /* Margen superior generoso */
                padding: 25px 50px;
                position: relative;
                page-break-before: auto;
                break-before: auto;
            }

            .phase-section {
                break-inside: avoid;
                margin-bottom: 25px;
                margin-top: 30px;
            }

            h1,
            h2,
            h3,
            .phase-title-box,
            table {
                break-after: avoid;
            }

            tr,
            td,
            th {
                break-inside: avoid;
            }

            /* Añadir espacio extra al final del contenido */
            .page-content::after {
                content: '';
                display: block;
                height: 80px;
                /* Espacio adicional para empujar footer */
            }
        }

        /* ===== PAGE MARGINS FOR PRINT - Actualizados ===== */
        @page {
            margin: 2.5cm 2cm 0cm 2cm;
            /* Top, right, bottom, left */
            size: A4;
        }

        @page :first {
            margin-top: 0.5cm;
            /* Margen superior mínimo para primera página */
            margin-right: 2cm;
            margin-bottom: 0cm;
            margin-left: 2cm;
        }

        @page :left {
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 0cm;
            margin-top: 2.5cm;
        }

        @page :right {
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 0cm;
            margin-top: 2.5cm;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- ===== HEADER ===== -->
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    <div class="header-item">
                        <span class="header-label">Nombre del Proyecto</span>
                        <span class="header-value">[Nombre del Proyecto]</span>
                    </div>
                    <div class="header-item">
                        <span class="header-label">Encargado del Proyecto</span>
                        <span class="header-value">[Nombre del Encargado]</span>
                    </div>
                </div>

                <div class="header-right">
                    <div class="header-item">
                        <span class="header-label">Periodo del Reporte</span>
                        <span class="header-value">DD/MM/AAAA - DD/MM/AAAA</span>
                    </div>
                    <div class="header-item">
                        <span class="header-label">Preparado por</span>
                        <span class="header-value">[Nombre]</span>
                    </div>
                </div>
            </div>

            <div class="header-title">
                <h1>📊 REPORTE DE PROYECTO</h1>
                <div class="header-subtitle">Seguimiento y Control de Actividades</div>
            </div>
        </div>

        <!-- ===== PAGE CONTENT ===== -->
        <div class="page-content">

            <!-- Fase: Análisis -->
            <div class="phase-section">
                <div class="phase-title-box">🔍 Análisis</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarea / Actividad</th>
                            <th class="time-cell">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">2 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr class="empty-row">
                            <td colspan="2">Agregar más tareas según sea necesario...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section-divider"></div>

            <!-- Fase: Planificación -->
            <div class="phase-section">
                <div class="phase-title-box">📋 Planificación</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarea / Actividad</th>
                            <th class="time-cell">Tiempo</th>
                            <th class="time-cell">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">2 h</td>
                            <td class="time-cell">2 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                            <td class="time-cell"></td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell"></td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr class="empty-row">
                            <td colspan="3">Agregar más tareas según sea necesario...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section-divider"></div>

            <!-- Fase: Desarrollo Frontend -->
            <div class="phase-section">
                <div class="phase-title-box">💻 Desarrollo Frontend</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarea / Actividad</th>
                            <th class="time-cell">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">2 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr class="empty-row">
                            <td colspan="2">Agregar más tareas según sea necesario...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section-divider"></div>

            <!-- Fase: Desarrollo Backend -->
            <div class="phase-section">
                <div class="phase-title-box">⚙️ Desarrollo Backend</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarea / Actividad</th>
                            <th class="time-cell">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">2 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr>
                            <td>Agrega cualquier tarea completada o actualización durante el periodo reportado</td>
                            <td class="time-cell">4 h</td>
                        </tr>
                        <tr class="empty-row">
                            <td colspan="2">Agregar más tareas según sea necesario...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== FOOTER - Con margen superior y pegado al final ===== -->
        <div class="footer">
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
    </div>
</body>

</html>