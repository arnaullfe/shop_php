<?php
require('../dependencies/fpdf/fpdf.php');
require_once ('../controllers/MainController.php');
class PDF2 extends FPDF
{
    function ImprovedTable($header, $data)
    {
        define('EURO',chr(128));
        $width = $this->GetPageWidth();
        // Anchuras de las columnas
        $w = array($width*0.4 , $width*0.15, $width*0.15, $width*0.15);
        // Cabeceras
        $this->SetFont('Arial' , '' , 10);

        $this->setFillColor(202, 207, 207);
        $this->Cell(20,5,$header[0],1,0,'L',true);
        $this->Cell(100,5,$header[1],1,0,'L',true);
        $this->Cell(25,5,$header[2],1,0,'L',true);
        $this->Cell(15,5,$header[3],1,0,'L',true);
        $this->Cell(30,5,$header[4],1,0,'L',true);

        $this->Ln();
        // Datos
        foreach($data as $row)
        {
            $this->Cell(20,6,utf8_decode($row[0]),'LBR');
            $this->Cell(100,6,utf8_decode($row[1]),'LRB' , 0 );
            $this->Cell(25,6,formatPrice($row[2]) . EURO,'LRB',0);
            $this->Cell(15,6, $row[3],'LRB',0);
            $this->Cell(30,6, formatPrice($row[4]) . EURO,'LRB',0);
            $this->Ln();
        }
        // Línea de cierre

        $this->Ln();
        $this->Ln();

        $this->SetFont('Arial' , '' , 11);

        $this->Cell(80 , 8 , 'SUBTOTAL' , 1 , 'R','R',true);
        $this->Cell(30,8,'IVA' , 1 , 'C','R',true);
        $this->Cell(30,8,'ENVIAMENT' , 1 , 'C','R',true);
        $this->Cell(50,8,'TOTAL' , 1 , 'R','R',true);

        $this->Ln();

        $this->Cell(80 , 8 , formatPrice(700) . EURO, 'LBR' , 'R','R');
        $this->Cell(30,8,'('. 21 .'%) '. formatPrice(20) . EURO , 'LBR' , 'C','R');
        $this->Cell(30,8,formatPrice(0) . EURO , 'LBR' , 'C','R');
        $this->Cell(50,8,formatPrice(1000) . EURO , 'LBR' , 'R','R');
    }
}