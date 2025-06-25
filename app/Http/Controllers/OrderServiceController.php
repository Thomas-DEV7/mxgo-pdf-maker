<?php

namespace App\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

class OrderServiceController extends Controller
{
    private function generateSampleData()
    {
        return [
            'header' => [
                'company_name' => 'MTX Aviation Manutenção De Aeronaves Ltda',
                'company_address' => 'Sorocaba/SP - COM 201306-41/ANAC',
                'document_reference' => 'F.TEC.015.REV03',
                'document_date' => '15/07/2024',
                'order_number' => '03372/25',
                'aircraft_prefix' => 'PP-JCA'
            ],
            
            'components' => [
                'AIRFRAME' => $this->buildComponentData(
                    'LA-107', 'F90', 'BEECH', '1981',
                    '9442.7', 'N/A', '10353', 'N/A',
                    'Manual:C0 / Revision:M.M. / PN:109-590010-19'
                ),
                
                'LEFT ENGINE' => $this->buildEngineData(
                    'PCE-92264', 'PT6A-135', 'PRATT & WHITNEY', '2126',
                    '9442.7', '2412', 'CANADA',
                    'Manual:49 / Revision:M.M. / PN:3043512'
                ),
                
                'RIGHT ENGINE' => $this->buildEngineData(
                    'PCE-92269', 'PT6A-135', 'PRATT & WHITNEY', '2126',
                    '9442.7', '2412', 'CANADA',
                    'Manual:49 / Revision:M.M. / PN:3043512'
                ),
                
                'LEFT PROPELLER' => $this->buildPropellerData(
                    'EAA-1533', 'HC-B4TN-3B', 'HARTZELL', 'N/A',
                    '4275.6', '75.7', 'N/A',
                    'Manual:22 / Revision:P.O.M. / PN:139 (61-00-39)'
                ),
                
                'RIGHT PROPELLER' => $this->buildPropellerData(
                    'EAA-1553', 'HC-B4TN-3B', 'HARTZELL', 'N/A',
                    '4275.6', '359.4', 'N/A',
                    'Manual:22 / Revision:P.O.M. / PN:139 (61-00-39)'
                )
            ],
            
            'timeline' => [
                'start_date' => '09/06/2025',
                'end_date' => '20/06/2025'
            ],
            
            'services' => $this->generateServiceItems(12), // Gera 12 itens de serviço
            
            'declaration' => [
                'title' => 'DECLARAÇÃO DE AERONAVEGABILIDADE',
                'content' => 'Declaro que os serviços foram realizados conforme especificações técnicas e registros aplicáveis.',
                'signature_line' => '_______________________________________',
                'signature_title' => 'Assinatura do Inspetor Responsável SDCO'
            ]
        ];
    }

    private function buildComponentData($sn, $model, $manufacturer, $year, $tsn, $tso, $csn, $cso, $revision)
    {
        return [
            'sn' => $sn,
            'model' => $model,
            'manufacturer' => $manufacturer,
            'year' => $year,
            'tsn' => $tsn,
            'tso' => $tso,
            'csn' => $csn,
            'cso' => $cso,
            'revision' => $revision,
            'type' => 'airframe'
        ];
    }

    private function buildEngineData($sn, $model, $manufacturer, $cso, $tsn, $tso, $origin, $revision)
    {
        return [
            'sn' => $sn,
            'model' => $model,
            'manufacturer' => $manufacturer,
            'cso' => $cso,
            'tsn' => $tsn,
            'tso' => $tso,
            'origin' => $origin,
            'revision' => $revision,
            'type' => 'engine'
        ];
    }

    private function buildPropellerData($sn, $model, $manufacturer, $cso, $tsn, $tso, $csn, $revision)
    {
        return [
            'sn' => $sn,
            'model' => $model,
            'manufacturer' => $manufacturer,
            'cso' => $cso,
            'tsn' => $tsn,
            'tso' => $tso,
            'csn' => $csn,
            'revision' => $revision,
            'type' => 'propeller'
        ];
    }

    private function generateServiceItems($count)
    {
        $teams = [
            ['André Segato - inspector', 'Thiago Paulucci Dos Santos - inspector'],
            ['Marcio Messias Silva - inspector', 'Silvio Vicente - mechanic']
        ];
        
        $services = [
            'EFETUAR SUBSTITUIÇÃO DO TRANSMISSOR DE PRESSÃO DE OLEO LADO DIREITO',
            'EFETUAR SUBSTITUIÇÃO PNEU INTERNO DIREITO APRESENTANDO PERDA DE PRESSÃO',
            'AVALIAR JUNTAS DA TAMPA DA NACELE ESQUERDA',
            '(MSR) AIRFRAME > LUBRICATE ITEMS 6M',
            '(MSR) LEFT ENGINE > CHECK AGB INTERNAL SCAVENGE OIL PUMP INLET SCREEN',
            '(MSR) RIGHT ENGINE > CHECK AGB INTERNAL SCAVENGE OIL PUMP INLET SCREEN',
            'TANQUE DA NACELLE LH DANIFICADO',
            'BARRAMENTO BUSTIE DIREITO POR VEZES ABRE',
            'AUDIOS AURAIS DO SISTEMA DE AVIONICS INOPERANTE',
            'EFETUAR SUBSTITUIÇÃO DE UMA PROBE DE COMBUSTIVEL LADO ESQUERDO',
            'EFETUAR SUBSTITUIÇÃO DE INDICADOR DE COMBUSTIVEL LH E AFERIÇÃO DO SISTEMA',
            'VERIFICAR COMANDO DO TRIM QUANTO A INTEGRIDADE'
        ];
        
        $items = [];
        
        for ($i = 0; $i < $count; $i++) {
            $serviceIndex = $i % count($services);
            $teamIndex = $i % count($teams);
            
            $items[] = [
                'number' => $i + 1,
                'description' => $services[$serviceIndex],
                'team' => implode(' | ', $teams[$teamIndex]),
                'interval' => ($i % 4 == 0) ? '6M' : null,
                'hours' => ($i % 3 == 0) ? '200' : null,
                'cycles' => ($i % 5 == 0) ? '50' : null
            ];
        }
        
        return $items;
    }

    public function generatePDF()
    {
        $osData = $this->generateSampleData();
        
        $pdf = SnappyPdf::loadView('pdf.order-service', compact('osData'))
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm')
            ->setOption('margin-right', '10mm')
            ->setOption('encoding', 'UTF-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('footer-right', '[page]/[topage]');
        
        return $pdf->inline("OS-{$osData['header']['order_number']}.pdf");
    }
}