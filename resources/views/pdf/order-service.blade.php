<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço - MXGO</title>
    <style>
        /* Configurações de página */
        @page {
            size: A4;
            margin-top: 10mm;
            margin-bottom: 15mm;
            margin-left: 10mm;
            margin-right: 10mm;
            
            @bottom-right {
                content: counter(page) " / " counter(pages);
                font-family: Arial, sans-serif;
                font-size: 8pt;
            }
        }
        
        /* Estilos base */
        body {
            font-family: "Arial", sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #000000;
        }
        
        /* Cabeçalho da empresa */
        .company-header {
            text-align: center;
            margin-bottom: 4mm;
            border-bottom: 1px solid #000;
            padding-bottom: 2mm;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }
        
        .company-address {
            font-size: 8pt;
        }
        
        /* Informações do documento */
        .document-info {
            text-align: right;
            font-size: 8pt;
            margin-bottom: 5mm;
        }
        
        /* Cabeçalho da OS */
        .order-header {
            text-align: center;
            margin: 5mm 0;
        }
        
        .order-number {
            font-weight: bold;
            font-size: 11pt;
        }
        
        .aircraft-prefix {
            font-weight: bold;
            font-size: 10pt;
        }
        
        /* Tabela de componentes */
        .component-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            font-size: 8pt;
            page-break-inside: avoid;
        }
        
        .component-table th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 2mm;
            border: 1px solid #000;
        }
        
        .component-table td {
            padding: 1mm 2mm;
            border: 1px solid #000;
            vertical-align: top;
        }
        
        /* Período de serviço */
        .service-period {
            margin: 5mm 0;
            font-size: 9pt;
        }
        
        /* Lista de serviços */
        .services-title {
            font-weight: bold;
            text-align: center;
            font-size: 10pt;
            margin: 5mm 0 3mm;
            text-transform: uppercase;
        }
        
        .service-item {
            margin-bottom: 3mm;
            page-break-inside: avoid;
        }
        
        .service-number {
            font-weight: bold;
            display: inline-block;
            width: 4mm;
        }
        
        .service-description {
            display: inline;
        }
        
        .service-team {
            font-size: 8pt;
            margin-left: 6mm;
            color: #555;
        }
        
        .service-interval {
            font-size: 8pt;
            margin-left: 6mm;
            font-style: italic;
        }
        
        /* Declaração de aeronavegabilidade */
        .declaration {
            margin-top: 10mm;
            page-break-inside: avoid;
        }
        
        .declaration-title {
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        
        .declaration-content {
            text-align: center;
            margin-bottom: 10mm;
        }
        
        /* Assinatura */
        .signature {
            text-align: center;
            margin-top: 15mm;
            page-break-inside: avoid;
        }
        
        .signature-line {
            margin-bottom: 1mm;
        }
        
        .signature-title {
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Cabeçalho da empresa -->
<div class="company-header">
    <div class="company-name">{{ $osData['header']['company_name'] }}</div>
    <div class="company-address">{{ $osData['header']['company_address'] }}</div>
</div>

<!-- Informações do documento -->
<div class="document-info">
    {{ $osData['header']['document_reference'] }}<br>
    {{ $osData['header']['document_date'] }}
</div>

<!-- Cabeçalho da Ordem de Serviço -->
<div class="order-header">
    <div class="order-number">OS #{{ $osData['header']['order_number'] }}</div>
    <div class="aircraft-prefix">{{ $osData['header']['aircraft_prefix'] }}</div>
</div>

<!-- Tabelas de componentes -->
@foreach($osData['components'] as $componentName => $component)
<table class="component-table">
    <thead>
        <tr>
            <th colspan="4">{{ strtoupper($componentName) }}</th>
        </tr>
    </thead>
    <tbody>
        <!-- Linha 1 -->
        <tr>
            <td width="20%">SN: {{ $component['sn'] }}</td>
            <td width="25%">Modelo: {{ $component['model'] }}</td>
            <td width="30%">Fabricante: {{ $component['manufacturer'] }}</td>
            <td width="25%">
                @if($component['type'] === 'airframe')
                    Ano de Fabricação: {{ $component['year'] }}
                @else
                    CSO: {{ $component['cso'] }}
                @endif
            </td>
        </tr>
        
        <!-- Linha 2 -->
        <tr>
            <td>TSN: {{ $component['tsn'] }}</td>
            <td>TSO: {{ $component['tso'] }}</td>
            <td>
                @if($component['type'] === 'airframe')
                    CSN: {{ $component['csn'] }}
                @elseif($component['type'] === 'engine')
                    Origem: {{ $component['origin'] }}
                @else
                    CSN: {{ $component['csn'] ?? 'N/A' }}
                @endif
            </td>
            <td>
                @if($component['type'] === 'airframe')
                    CSO: {{ $component['cso'] }}
                @else
                    &nbsp;
                @endif
            </td>
        </tr>
        
        <!-- Linha 3 -->
        <tr>
            <td colspan="4">Revisão: {{ $component['revision'] }}</td>
        </tr>
    </tbody>
</table>
@endforeach

<!-- Período de serviço -->
<div class="service-period">
    Data de Início: {{ $osData['timeline']['start_date'] }}<br>
    Término Previsto: {{ $osData['timeline']['end_date'] }}
</div>

<!-- Lista de serviços executados -->
<div class="services-title">RESUMO DE ITENS EXECUTADOS</div>

@foreach($osData['services'] as $service)
<div class="service-item">
    <div>
        <span class="service-number">{{ $service['number'] }}.</span>
        <span class="service-description">{{ $service['description'] }}</span>
    </div>
    <div class="service-team">Equipe: {{ $service['team'] }}</div>
    @if($service['interval'] || $service['hours'] || $service['cycles'])
    <div class="service-interval">
        @if($service['interval'])Intervalo: {{ $service['interval'] }} |@endif
        @if($service['hours']) Horas: {{ $service['hours'] }} |@endif
        @if($service['cycles']) Ciclos: {{ $service['cycles'] }}@endif
    </div>
    @endif
</div>
@endforeach

<!-- Declaração de aeronavegabilidade -->
<div class="declaration">
    <div class="declaration-title">{{ $osData['declaration']['title'] }}</div>
    <div class="declaration-content">{{ $osData['declaration']['content'] }}</div>
</div>

<!-- Assinatura -->
<div class="signature">
    <div class="signature-line">{{ $osData['declaration']['signature_line'] }}</div>
    <div class="signature-title">{{ $osData['declaration']['signature_title'] }}</div>
</div>

</body>
</html>