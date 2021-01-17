<?php
require('../modals/PDF2.php');
$pdf = new PDF2();
$pdf->AddPage();

$pdf->SetTitle(utf8_decode('Factura nº 12345'));
$pdf->SetAuthor('Eshop');
$pdf->SetCreator('Eshop');

$pdf->Image('../resouces/images/logo.png' , 10 , 10 , -70);

$pdf->SetY(8);
$pdf->SetFont('Arial', '' ,8);
$pdf->Cell($pdf->GetPageWidth()-43 , 3 , utf8_decode('Eshop S.L') , 0 , 0, 'R');
$pdf->Ln();
$pdf->Cell($pdf->GetPageWidth()-30 , 3 , utf8_decode('C/ autista March, s/n') , 0 , 0, 'R');
$pdf->Ln();
$pdf->Cell($pdf->GetPageWidth()-24.5 , 3 , utf8_decode('08570 Torelló, Barcelona' ), 0 , 0, 'R');
$pdf->Ln();
$pdf->Cell($pdf->GetPageWidth()-35.5 , 3 , utf8_decode('CIF: B11111111') , 0 , 0, 'R');

$pdf->SetLineWidth(0.4);
$pdf->SetDrawColor(119,137,138);
$pdf->Line(50,21,$pdf->GetPageWidth()-18,21);
$pdf->SetDrawColor(0,0,0);

$pdf->SetY(15);
$pdf->SetFont('Arial', 'B' ,20);
$pdf->Cell(0 , 40 , utf8_decode('FACTURA DE COMPRA') , 0 , 1, 'C');
$pdf->Ln();


$pdf->SetY(25);
$pdf->SetFont('Arial', '' ,12);
$pdf->Cell(0 , 40 , utf8_decode('Dades de facturació:') , 0 , 1, 'L');
$pdf->Ln();

$pdf->Rect(10,49,($pdf->GetPageWidth()-10)/2,27);
$pdf->SetY(50);
$pdf->SetFont('Arial' , '' , 11);
$pdf->Cell(12 , 5 , utf8_decode('Nom:') , 0 , 0 , 'L');
$pdf->Cell($pdf->GetPageWidth()/2.4 , 5 , utf8_decode('Arnau Llopart') , 0 , 0 , 'L' );
$pdf->Ln();
$pdf->Cell(18 , 5 , utf8_decode('Direcció:') , 0 , 0 , 'L');
$pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode('c/gurri n/13') , 0 , 0 , 'L' );
$pdf->Ln();
$pdf->Cell(18 , 5 , utf8_decode('Població:') , 0 , 0 , 'L');
$pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode('Barcelona') , 0 , 0 , 'L' );
$pdf->Ln();
$pdf->Cell(18 , 5 , utf8_decode('NIF/CIF:') , 0 , 0 , 'L');
$pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode('B22222222') , 0 , 0 , 'L' );
$pdf->Ln();
$pdf->Cell(17 , 5 , utf8_decode('Telèfon:') , 0 , 0 , 'L');
$pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode('608338587') , 0 , 0 , 'L' );


$pdf->Rect(10,83,($pdf->GetPageWidth()-20),5);

$pdf->SetY(83);
$pdf->SetFont('Arial' , '' , 9);
$pdf->Cell(20 , 5 , utf8_decode('Nº Comanda:') , 0 , 0 , 'L');
$pdf->Cell( 44, 5 , utf8_decode('123456789') , 0 , 0 , 'L' );

$pdf->Rect(75,83,0,5);
$pdf->Cell(10 , 5 , utf8_decode(' Data:') , 0 , 0 , 'L');
$pdf->Cell(40, 5 , utf8_decode('03/10/2021') , 0 , 0 , 'L' );

$pdf->Rect(125,83,0,5);
$pdf->Cell(32 , 5 , utf8_decode(' Forma de pagament:') , 0 , 0 , 'L');
$pdf->Cell(30 , 5 , utf8_decode('Paypal express') , 0 , 0 , 'L' );

$pdf->Ln();


$header = array('Codi' , 'Article' , 'Preu' , 'Unitats' ,'Total');
$data = array(
    array('test','dedededededededededededededededed',50000,3000,100000),
    array('test',1,12,6,12,6),
    array('test',1,50,4,50,4)
);


$pdf->Ln();
$pdf->SetFillColor(66, 135, 245);
$pdf->ImprovedTable($header,$data);

$pdf->Output('I' , 'pdffiles/factura.pdf');