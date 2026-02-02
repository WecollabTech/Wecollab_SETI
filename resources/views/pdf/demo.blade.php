<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estimación {{ $estimacion->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
            position: relative;
        }

        /* ===== Encabezado mejorado ===== */
        header {
            background: linear-gradient(135deg, #0D47A1, #1976D2);
            height: 120px;
            clip-path: polygon(0 0, 100% 0, 100% 70%, 0 100%);
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            padding: 0 40px;
            color: white;
        }

        /* ===== Contenedor texto a la izquierda ===== */
        header .header-text {
            flex: 1;
        }

        header .header-text h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        header .header-text p {
            margin: 4px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        /* ===== Logo a la derecha ===== */
        header .logo {
            flex-shrink: 0;
        }

        header .logo img {
            height: 70px;
        }

        /* ===== Marca de agua ===== */
        .watermark {
            position: absolute;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.05;
            width: 400px;
            height: 400px;
            background: url('/img/logo.png') no-repeat center center;
            background-size: contain;
            z-index: 0;
        }

        /* ===== Contenido ===== */
        .content {
            position: relative;
            z-index: 1;
            padding: 30px 40px 120px;
        }

        .info {
            padding: 15px 0;
            background-color: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .info p {
            margin: 5px 0;
        }

        h2 {
            margin: 30px 0 10px;
            font-size: 14px;
            color: #0D47A1;
            border-bottom: 2px solid #1976D2;
            padding-bottom: 4px;
        }

        /* ===== Tablas ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        th {
            background-color: #1976D2;
            color: #fff;
            font-weight: bold;
        }

        th:nth-child(1),
        td:nth-child(1) {
            width: 30%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 50%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 20%;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #E3F2FD;
        }

        /* ===== Pie de página ===== */
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #E3F2FD;
            padding: 12px 40px;
            border-top: 2px solid #0D47A1;
            display: flex;
            align-items: center;
            font-size: 10px;
            color: #0D47A1;
            justify-content: flex-start;
            gap: 15px;
        }

        footer .arrow {
            display: inline-block;
            width: 15px;
            height: 10px;
            border-right: 10px solid #0D47A1;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
        }

        footer .contact-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media print {

            header,
            footer {
                position: fixed;
            }

            body {
                margin-top: 150px;
                margin-bottom: 80px;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>

<body>

    <!-- ===== Encabezado mejorado ===== -->
    <header>
        <div class="header-text">
            <h1>THYNK UNLIMITED</h1>
            <p>Creative Company</p>
        </div>
        <div class="logo">
            <img src="/img/logo.png" alt="Logo">
        </div>
    </header>

    <!-- ===== Marca de agua ===== -->
    <div class="watermark"></div>

    <!-- ===== Contenido ===== -->
    <div class="content">
        <div class="info">
            <p><strong>Tipo de Implementación:</strong> {{ $estimacion->nombre_tipo_implementacion ?? '-' }}</p>
            <p><strong>Comentarios:</strong> {{ $estimacion->comentarios ?? '-' }}</p>
            <p><strong>Total Horas:</strong> {{ $estimacion->total_horas }} h</p>
            <p><strong>Complejidad:</strong> {{ $estimacion->complejidad->nombre ?? '-' }}</p>
        </div>

        {{-- Fases --}}
        @if($estimacion->fases->count())
            <h2>Fases</h2>
            @foreach($estimacion->fases as $fase)
                <table>
                    <thead>
                        <tr>
                            <th>Fase</th>
                            <th>Tarea</th>
                            <th>Duración (min)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fase->tareas as $tarea)
                            <tr>
                                <td>{{ $fase->fase->nombre ?? '-' }}</td>
                                <td>{{ $tarea->nombre_tarea ?? '-' }}</td>
                                <td>{{ $tarea->duracion_minuto ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif

        {{-- Integraciones --}}
        @if($estimacion->integraciones->count())
            <h2>Integraciones</h2>
            @foreach($estimacion->integraciones as $integracion)
                <table>
                    <thead>
                        <tr>
                            <th>Integración</th>
                            <th>Tarea</th>
                            <th>Duración (min)</th>
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
            @endforeach
        @endif
    </div>

    <!-- ===== Pie de página ===== -->
    <footer>
        <div class="arrow"></div>
        <div class="arrow"></div>
        <div class="arrow"></div>
        <div class="contact-item">🌐 www.reallygreatsite.com</div>
        <div class="contact-item">📞 123-456-7890</div>
        <div class="contact-item">✉ hello@reallygreatsite.com</div>
    </footer>

</body>

</html>