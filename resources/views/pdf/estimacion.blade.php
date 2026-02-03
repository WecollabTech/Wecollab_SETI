<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estimación {{ $estimacion->id }}</title>
    <style>
        /* ======================================================
           VARIABLES CORPORATIVAS
        ====================================================== */
        :root {
            --header-height: 110px;
            --footer-height: 90px;
            --primary: #1a3a6c;
            --primary-dark: #0d2a52;
            --gray: #475569;
            --gray-light: #64748b;
            --light-gray: #f8fafc;
            --blue-soft: #f0f5ff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --shadow-subtle: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* ======================================================
           BASE
        ====================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Calibri, Arial, sans-serif;
            font-size: 11.5pt;
            line-height: 1.5;
            color: var(--text-main);
            background-color: #fff;
            orphans: 3;
            widows: 3;
        }

        @page {
            margin: 0;
            size: letter;
        }

        /* ======================================================
           HEADER
        ====================================================== */
        .header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: #fff;
            padding: 12px 45px 8px;
            box-shadow: var(--shadow-subtle);
            border-bottom: 1px solid var(--border-color);
            z-index: 1000;
        }

        .header-line {
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
            margin-bottom: 8px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .logo-box {
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--blue-soft);
            border-radius: 4px;
            padding: 4px;
        }

        .logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .header-center {
            text-align: center;
            padding: 0 8px;
        }

        .header-company {
            font-size: 16pt;
            font-weight: 700;
            color: var(--primary-dark);
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-doc {
            font-size: 11pt;
            margin-top: 4px;
            color: var(--gray);
            font-weight: 500;
        }

        /* ======================================================
           FOOTER
        ====================================================== */
        .footer-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--footer-height);
            padding: 10px 45px;
            font-size: 9pt;
            color: var(--gray);
            background: #fff;
            border-top: 1px solid var(--border-color);
            z-index: 1000;
        }

        .footer-line {
            height: 3px;
            background: var(--primary);
            border-radius: 1px;
            margin-bottom: 8px;
        }

        .footer-table td {
            padding: 3px 5px;
            vertical-align: middle;
        }

        .footer-title {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 9.5pt;
            margin-bottom: 2px;
        }

        .footer-right {
            text-align: right;
        }

        /* ======================================================
           CONTENIDO
        ====================================================== */
        .content-section {
            margin-top: calc(var(--header-height) + 20px);
            padding-bottom: calc(var(--footer-height) + 20px);
            padding-left: 25mm;
            padding-right: 25mm;
            position: relative;
            z-index: 100;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .summary-table td {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .summary-label {
            width: 26%;
            background: var(--blue-soft);
            font-weight: 700;
            color: var(--primary-dark);
        }

        .summary-value {
            width: 44%;
            background: var(--light-gray);
            color: var(--text-main);
        }

        .summary-period {
            width: 30%;
            background: var(--blue-soft);
            text-align: center;
            vertical-align: middle !important;
        }

        .period-title {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
            font-size: 11.5pt;
        }

        .period-dates {
            font-size: 11pt;
            letter-spacing: 0.5px;
            color: var(--gray);
            font-weight: 500;
        }

        /* ======================================================
           PHASE SECTION
        ====================================================== */
        .phase-section {
            margin-top: 10mm;
            margin-bottom: 12mm;
            page-break-inside: avoid;
        }

        .phase-title {
            font-size: 13pt;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2.5px solid var(--primary);
        }

        /* ======================================================
           INTEGRATIONS SECTION
        ====================================================== */
        .integrations-section {
            margin-top: 10mm;
            margin-bottom: 12mm;
            page-break-inside: avoid;
        }

        .integrations-title {
            font-size: 14pt;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 3px solid var(--primary);
            text-align: center;
        }

        .integration-group {
            margin-bottom: 12mm;
        }

        .integration-name {
            font-size: 12.5pt;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 6px;
            padding-left: 8px;
            border-left: 3px solid var(--primary);
            background: var(--blue-soft);
            padding: 6px 12px;
            border-radius: 0 4px 4px 0;
        }

        /* ======================================================
           DATA TABLE
        ====================================================== */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.8pt;
            margin-top: 6mm;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }

        table.data-table thead th {
            background: var(--primary);
            color: #fff;
            padding: 9px 10px;
            font-weight: 600;
            text-align: left;
        }

        table.data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        table.data-table tr:last-child td {
            border-bottom: none;
        }

        table.data-table tr:nth-child(even) {
            background-color: #fbfcfd;
        }

        table.data-table th:first-child,
        table.data-table td:first-child {
            padding-left: 14px;
        }

        table.data-table th:last-child,
        table.data-table td:last-child {
            padding-right: 14px;
            text-align: right;
        }
    </style>
</head>

<body>
    @php
        $logoIzq = file_exists(public_path('img/wecollab.png'))
            ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('img/wecollab.png')))
            : null;

        $logoDer = file_exists(public_path('img/seti.png'))
            ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('img/seti.png')))
            : null;
    @endphp

    <!-- HEADER -->
    <div class="header-section">
        <div class="header-line"></div>
        <table class="header-table">
            <tr>
                <td width="15%">
                    @if($logoIzq)
                        <div class="logo-box"><img src="{{ $logoIzq }}"></div>
                    @endif
                </td>
                <td width="70%" class="header-center">
                    <div class="header-company">NOMBRE DE LA EMPRESA S.A. DE C.V.</div>
                    <div class="header-doc">Reporte de Estimación del Proyecto</div>
                </td>
                <td width="15%" align="right">
                    @if($logoDer)
                        <div class="logo-box"><img src="{{ $logoDer }}"></div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- CONTENIDO -->
    <div class="content-section">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Nombre del proyecto</td>
                <td class="summary-value">{{ $estimacion->comentarios ?? '-' }}</td>
                <td class="summary-period" rowspan="3">
                    <div class="period-title">Periodo de la Estimación</div>
                    <div class="period-dates">DD/MM/AAAA – DD/MM/AAAA</div>
                </td>
            </tr>
            <tr>
                <td class="summary-label">Responsable</td>
                <td class="summary-value">{{ $estimacion->responsable ?? '-' }}</td>
            </tr>
            <tr>
                <td class="summary-label">Preparado por</td>
                <td class="summary-value">{{ config('app.name') }}</td>
            </tr>
        </table>

        @foreach($estimacion->fases as $fase)
            @if($fase->tareas->count())
                <div class="phase-section">
                    <div class="phase-title">Fase: {{ $fase->fase->nombre }}</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th width="90">Minutos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fase->tareas as $tarea)
                                <tr>
                                    <td>{{ $tarea->nombre_tarea }}</td>
                                    <td>{{ $tarea->duracion_minuto }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach

        @if($estimacion->integraciones->count())
            <div class="integrations-section">
                <div class="integrations-title">Integraciones</div>
                @foreach($estimacion->integraciones as $integracion)
                    @if($integracion->tareas->count())
                        <div class="integration-group">
                            <div class="integration-name">{{ $integracion->integracion->nombre ?? 'Integración' }}</div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Integración</th>
                                        <th>Tarea</th>
                                        <th width="100">Duración (min)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($integracion->tareas as $tarea)
                                        <tr>
                                            <td>{{ $integracion->integracion->nombre ?? '-' }}</td>
                                            <td>{{ $tarea->nombre_tarea_integracion ?? '-' }}</td>
                                            <td>{{ $tarea->duracion_estimada_minutos ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="footer-section">
        <div class="footer-line"></div>
        <table class="footer-table">
            <tr>
                <td width="60%">
                    <div class="footer-title">Información Corporativa</div>
                    Primo Feliciano Velazquez 3930, Guadalajara, Jalisco. México.<br>
                    Tel: +52 33 5121 3546 · contacto@we-collab.tech · https://we-collab.tech/
                </td>
                <td width="40%" class="footer-right">
                    <div class="footer-title">Documento Institucional</div>
                    Uso interno · Estimación<br>
                    Página <span class="pageNumber"></span> de <span class="totalPages"></span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>