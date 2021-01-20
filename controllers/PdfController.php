<?php
include_once ('./MainController.php');
include_once ('../modals/Database.php');
require('../modals/PDF2.php');
session_start();
if(isset($_GET["command_id_bill"])){
    $database = new Database();
    $command = $database->executeQuery("SELECT id FROM commands WHERE id=? AND user_id=?",array($_GET["command_id_bill"],$_SESSION["user_id"]));
    $database->closeConnection();
    if(count($command)>0){
        createBill($_GET["command_id_bill"]);
    } else{
        header("location: ../pages/botiga_view/login.php");
    }
}


function createBill($command_id){
    $database = new Database();
    $command = $database->executeQuery("SELECT * FROM commands WHERE id=?",array($command_id))[0];
    $commandItems = $database->executeQuery("SELECT * FROM commandItems WHERE command_id=?",array($command_id));
    $address = $database->executeQuery("SELECT * FROM addressesCommands WHERE id=?",array($command["address_command_id"]))[0];
    $pdf = new PDF2();
    $pdf->AddPage();

    $pdf->SetTitle(utf8_decode('Factura nº '.str_pad($command_id,6,'0',STR_PAD_LEFT)));
    $pdf->SetAuthor('Eshop');
    $pdf->SetCreator('Eshop');

    $pdf->Image('../resouces/images/logo.png' , 10 , 10 , -70);

    $pdf->SetY(8);
    $pdf->SetFont('Arial', '' ,8.5);
    $pdf->Cell($pdf->GetPageWidth()-28 , 3 , utf8_decode('Eshop S.L') , 0 , 'R', 'R');
    $pdf->Ln();
    $pdf->Cell($pdf->GetPageWidth()-28 , 3 , utf8_decode('C/ autista March, s/n') , 0 , 0, 'R');
    $pdf->Ln();
    $pdf->Cell($pdf->GetPageWidth()-28 , 3 , utf8_decode('08570 Torelló, Barcelona' ), 0 , 0, 'R');
    $pdf->Ln();
    $pdf->Cell($pdf->GetPageWidth()-28 , 3 , utf8_decode('CIF: B11111111') , 0 , 0, 'R');

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
    $pdf->Cell($pdf->GetPageWidth()/2.4 , 5 , utf8_decode($address["name"]." ".$address["lastnames"]) , 0 , 0 , 'L' );
    $pdf->Ln();
    $pdf->Cell(18 , 5 , utf8_decode('Direcció:') , 0 , 0 , 'L');
    $pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode($address["postal_code"]) , 0 , 0 , 'L' );
    $pdf->Ln();
    $pdf->Cell(18 , 5 , utf8_decode('Població:') , 0 , 0 , 'L');
    $pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode($address["city"]) , 0 , 0 , 'L' );
    $pdf->Ln();
    $pdf->Cell(18 , 5 , utf8_decode('NIF/CIF:') , 0 , 0 , 'L');
    $pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode($address["nif"]) , 0 , 0 , 'L' );
    $pdf->Ln();
    $pdf->Cell(17 , 5 , utf8_decode('Telèfon:') , 0 , 0 , 'L');
    $pdf->Cell($pdf->GetPageWidth()/2.4 , 5, utf8_decode($address["phone"]) , 0 , 0 , 'L' );


    $pdf->Rect(10,83,($pdf->GetPageWidth()-20),5);

    $pdf->SetY(83);
    $pdf->SetFont('Arial' , '' , 9);
    $pdf->Cell(20 , 5 , utf8_decode('Nº Comanda:') , 0 , 0 , 'L');
    $pdf->Cell( 44, 5 , utf8_decode(str_pad($command_id,6,'0',STR_PAD_LEFT)) , 0 , 0 , 'L' );

    $pdf->Rect(75,83,0,5);
    $pdf->Cell(10 , 5 , utf8_decode(' Data:') , 0 , 0 , 'L');
    $pdf->Cell(40, 5 , utf8_decode(formatDate($command["created_at"])) , 0 , 0 , 'L' );

    $pdf->Rect(125,83,0,5);
    $pdf->Cell(32 , 5 , utf8_decode(' Forma de pagament:') , 0 , 0 , 'L');
    $pdf->Cell(30 , 5 , utf8_decode('Paypal express') , 0 , 0 , 'L' );

    $pdf->Ln();


    $header = array('Codi' , 'Article' , 'Preu' , 'Unitats' ,'Total');
    $data = array();
    foreach ($commandItems as $item){
        array_push($data,array($item["id"],$item["product_name"],$item["price_iva_unit"],$item["units"],$item["total_iva_price"],$item["product_iva"]));
    }


    $pdf->Ln();
    $pdf->SetFillColor(66, 135, 245);
    $pdf->ImprovedTable($header,$data);
    $pdf->Output('I' , 'pdffiles/factura.pdf');

}