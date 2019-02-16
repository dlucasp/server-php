<?php

use \Firebase\JWT\JWT;
require 'reportsControllers.php';
require_once 'tcpdf_include.php';

$app->group('/pdf', function () use ($app) {

    //localizar processos de uma OS
    $app->post('/orcamentoVendas', function ($request, $response, $args) {

        $input = $request->getParsedBody();
        $produtos = array();
        $produtos = json_encode($input['produtos'], true);
        $produtos = json_decode($produtos);

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Romancini');
        $pdf->SetTitle('Orçamento Vendas');
        $pdf->SetSubject('Orçamento Vendas');
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Write(0, 'ORÇAMENTO DE VENDAS - Nº: ' . str_pad('10', 5, "0", STR_PAD_LEFT), '', 0, 'C', true, 0, false, false, 0);
        $pdf->Ln();

        $HLinha = 4.5;

        $pdf->setX(4);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(30, $HLinha, 'DATA EMISSÃO: ', 0, 0, 'L', 0, '', 0, true, 'T', 'M');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(25, $HLinha, $input['data'], 0, 0, 'L', 0, '', 0, true, 'T', 'M');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(18, $HLinha, 'CLIENTE: ', 0, 0, 'L', 0, '', 0, true, 'T', 'M');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(100, $HLinha, $input['cliente'], 0, 1, 'L', 0, '', 0, true, 'T', 'M');
        
        $pdf->Ln();
        $ajusteH = $pdf->getY();
        foreach ($produtos as $key => $produto) {
            $y = $pdf->getY();
            $h = 0;
            if ($produto->image) {
              $image_file = $produto->image;
              list($width, $height, $type, $attr) = getimagesize($image_file);
              $array = explode('.', $image_file);
              $extension = end($array);
              $pdf->Image($image_file, 100, $y+4, 84, '', $extension, '', 'T', false, 300, '', false, false, 0, false, false, false);             

              $ajusteH += $height*100/$width;
              $h = $height*100/$width;
            }
            
            $pdf->Ln();
            $pdf->setX(4);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, $HLinha, 'PRODUTO: ', 0, 0, 'L', 0, '', 0, true, 'T', 'M');
            $pdf->SetFont('helvetica', '', 10);
            // $pdf->Cell(50, $HLinha, $produto->title, 0, 1, 'L', 0, '', 0, true, 'T', 'M');
            $pdf->MultiCell(50, '', $produto->title, '', 'L', 0, 1, '', '', true, 0, false, true, 0, 'M', true);

            $pdf->setX(4);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, $HLinha, 'QUANTIDADE: ', 0, 0, 'L', 0, '', 0, true, 'T', 'M');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(50, $HLinha, str_pad($produto->quant, 2, "0", STR_PAD_LEFT), 0, 1, 'L', 0, '', 0, true, 'T', 'M');

            $pdf->setX(4);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, $HLinha, 'UNITÁRIO: R$ ', 0, 0, 'L', 0, '', 0, true, 'T', 'M');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(50, $HLinha, $produto->valor, 0, 1, 'L', 0, '', 0, true, 'T', 'M');

            $pdf->setX(4);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, $HLinha, 'SUBTOTAL: R$ ', 0, 0, 'L', 0, '', 0, true, 'T', 'M');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(50, $HLinha, $produto->subTot, 0, 1, 'L', 0, '', 0, true, 'T', 'M');

            if ($ajusteH) {
              if ($ajusteH > 295 ) {
                 $pdf->AddPage();
              } else {
                $pdf->setY($ajusteH);
              }

            }
            
            if ($key > 0) {
              // $pdf->Ln();
            }
            $pdf->RoundedRect(4, $y, 200, $h-5, 2);

        }

        return $this->response->withHeader('Content-type', 'application/pdf')->write($pdf->Output('orcamentoVendas'));
    });

});
