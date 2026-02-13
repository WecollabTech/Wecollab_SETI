<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estimación {{ $estimacion->id }}</title>

    <style>
        @page {
            size: letter;
            margin: 125px 38px 105px 38px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            color: #2c2c2c;
            line-height: 1.32;
        }

        /* ================= HEADER / FOOTER ================= */
        .header-section {
            position: fixed;
            top: -110px;
            left: 0;
            right: 0;
            height: 110px;
        }

        .footer-section {
            position: fixed;
            bottom: -90px;
            left: 0;
            right: 0;
            height: 90px;
            font-size: 8.5pt;
            color: #666;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #5D2E8C;
            /* línea morado fuerte corporativo */
            /* color morado fuerte corporativo */
            padding-bottom: 6px;
        }

        .logo-box {
            width: 70px;
            vertical-align: middle;
        }

        .logo-box img {
            width: 68px;
        }

        .company-info {
            font-size: 9pt;
            line-height: 1.25;
            text-align: center;
        }

        .header-title {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 1px;
            color: #5D2E8C;
            /* morado fuerte */
            line-height: 1.1;
        }

        .header-subtitle {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10.5pt;
            font-weight: normal;
            letter-spacing: 0.6px;
            color: #A686D7;
            /* morado claro */
            margin-top: 2px;
        }

        .title-box {
            text-align: right;
            font-size: 9pt;
            vertical-align: middle;
        }

        /* ================= SECTIONS ================= */
        .section-title {
            background: #5D2E8C;
            /* morado fuerte */
            color: #fff;
            padding: 6px 10px;
            margin-top: 15px;
            font-weight: bold;
            font-size: 9.7pt;
            border-radius: 2px;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .sub-title {
            font-size: 9.4pt;
            font-weight: bold;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        /* ================= INFO CARD ================= */
        .info-card {
            margin-top: 8px;
            border: 1px solid #ddd;
            background: #fafafa;
            border-radius: 4px;
            padding: 8px;
            page-break-inside: avoid;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 6px 8px;
            vertical-align: top;
        }

        .info-label {
            font-size: 8.5pt;
            color: #777;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .info-value {
            font-size: 10pt;
            font-weight: bold;
            color: #222;
            margin-top: 2px;
        }

        .info-highlight {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            padding: 5px 6px;
        }

        /* ================= TABLES ================= */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .items-table {
            margin-top: 6px;
            border: 1px solid #ddd;
            page-break-inside: auto;
            page-break-before: avoid;
            page-break-after: auto;
        }

        .items-table th {
            background: #F0F0F0;
            /* gris muy claro */
            font-size: 8.6pt;
            padding: 5px;
            text-transform: uppercase;
            letter-spacing: .25px;
            border: 1px solid #ccc;
        }

        .items-table td {
            padding: 4px 6px;
            font-size: 9.2pt;
            border: 1px solid #ddd;
        }

        .items-table tr:nth-child(even) {
            background: #fbfbfb;
        }

        .text-right {
            text-align: right;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .pagenum:before {
            content: counter(page);
        }

        .totalpages:before {
            content: counter(pages);
        }

        .divider {
            border-top: 1px dashed #ccc;
            margin: 7px 0;
        }

        /* ================= FOOTER TABLE ================= */
        .footer-table td {
            vertical-align: top;
            font-size: 8.3pt;
            line-height: 1.3;
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->
    <div class="header-section">
        <table class="header-table">
            <tr>
                <td class="logo-box">
                    <img src="{{ public_path('img/wecollab.jpg') }}">
                </td>

                <td class="company-info">
                    <div class="header-title">WE COLLAB</div>
                    <div class="header-subtitle">SynergyFlow™ PlanCore</div>
                </td>

                <td class="logo-box" style="text-align:right">
                    <img src="{{ public_path('img/seti.jpg') }}">
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= FOOTER ================= -->
    <div class="footer-section">
        <div class="divider"></div>

        <table class="footer-table" width="100%">
            <tr>
                <td width="70%">
                    <strong>We Collab</strong><br>
                    Primo Feliciano Velazquez 3930, Guadalajara, Jalisco, México<br>
                    Tel: +52 33 5121 3546 · contacto@we-collab.tech · https://we-collab.tech/
                </td>

                <td width="30%" style="text-align:right;">
                    <strong>Documento Institucional</strong><br>
                    Uso interno · Estimación<br>
                    Página <span class="pagenum"></span> de <span class="totalpages"></span>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="content-section">

        <!-- Información General -->
        <div class="section-title">Información General</div>

        <table class="items-table" style="margin-top:8px;">
            <tbody>
                <tr>
                    <th width="20%">ID Estimación </th>
                    <td width="30%">#{{ $estimacion->id }}</td>

                    <th width="20%">Fecha</th>
                    <td width="30%">{{ $estimacion->created_at->format('d/m/Y') }}</td>
                </tr>

                <tr>
                    <th width="20%">Compañia </th>
                    <td width="30%">{{ $estimacion->nombre_empresa }}</td>

                    <th width="20%">Id de Negociación</th>
                    <td width="30%">{{ $estimacion->id_negocio }}</td>
                </tr>

                <tr>
                    <th>Responsable</th>
                    <td>{{ $estimacion->user->name ?? $estimacion->responsable ?? 'No asignado' }}</td>

                    <th>Tipo Implementación</th>
                    <td>{{ $estimacion->nombre_tipo_implementacion ?? '-' }}</td>
                </tr>


                <tr>
                    <th>Complejidad</th>
                    <td>{{ $estimacion->complejidad->nombre ?? '-' }}</td>

                    <th>Total Horas</th>
                    <td>{{ number_format($estimacion->total_horas, 2) }} h</td>
                </tr>
            </tbody>
        </table>

        <!-- ================= FASES ================= -->
        @foreach($estimacion->fases as $faseRel)
            <div class="section-title no-break">Fase: {{ $faseRel->fase->nombre ?? '-' }}</div>
            <table class="items-table no-break">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="65%">Tarea</th>
                        <th width="30%">Duración (min)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faseRel->tareas as $i => $tarea)
                        <tr class="no-break">
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $tarea->nombre_tarea ?? $tarea->titulo ?? '-' }}</td>
                            <td class="text-right">{{ $tarea->duracion_minuto ?? ($tarea->horas * 60 ?? '-') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        <!-- ================= INTEGRACIONES ================= -->
        @if($estimacion->integraciones->count())
            <div class="section-title no-break">Integraciones</div>
            @foreach($estimacion->integraciones as $intRel)
                <div class="sub-title no-break">{{ $intRel->integracion->nombre ?? '-' }}</div>
                <table class="items-table no-break">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="65%">Tarea</th>
                            <th width="30%">Duración (min)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($intRel->tareas as $i => $tarea)
                            <tr class="no-break">
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $tarea->nombre_tarea_integracion ?? $tarea->titulo ?? '-' }}</td>
                                <td class="text-right">{{ $tarea->duracion_estimada_minutos ?? ($tarea->horas * 60 ?? '-') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif

        <!-- Resumen Final -->
        <div class="section-title no-break">Resumen Final</div>
        <div class="info-card no-break">
            <table class="info-grid">
                <tr>
                    <td width="35%">
                        <div class="info-label">Factor</div>
                        <div class="info-value info-highlight">{{ $estimacion->complejidad->factor ?? '-' }}</div>
                    </td>
                </tr>
                <tr>
                    <td width="35%">
                        <div class="info-label">Total horas estimadas</div>
                        <div class="info-value info-highlight">{{ number_format($estimacion->total_horas, 2) }} h</div>
                    </td>
                </tr>
            </table>
        </div>



    </div>

</body>

</html>