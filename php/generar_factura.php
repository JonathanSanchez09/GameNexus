<?php
session_start();
require('libs/fpdf.php'); // Asegúrate de que la ruta sea correcta

// Verifica que la sesión 'orden' exista
if (!isset($_SESSION['orden'])) {
    header("Location: ../index.php");
    exit();
}

$orden = $_SESSION['orden'];

class PDF extends FPDF {
    // Cabecera de la factura
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'Factura de Compra',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,5,'GameStore - Tu Tienda de Videojuegos',0,1,'C');
        $this->Cell(0,5,'Telefono: (555) 123-4567 | Email: contacto@gamestore.com',0,1,'C');
        $this->Ln(10);
    }
    
    // Pie de página de la factura
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Creación del objeto PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// ----------------------------------------------------
// DATOS DE LA ORDEN Y FECHA
// ----------------------------------------------------
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Detalles de la Orden',0,1);

// Generar un numero de factura simple y la fecha actual
$numero_factura = 'FAC-' . date('Ymd') . '-' . rand(1000, 9999);
$fecha_factura = date('d/m/Y');

$pdf->SetFont('Arial','',10);
$pdf->Cell(40,7,'Numero de Factura:',0);
$pdf->Cell(0,7,$numero_factura,0,1);

$pdf->Cell(40,7,'Fecha del Pedido:',0);
$pdf->Cell(0,7,$fecha_factura,0,1);

$pdf->Cell(40,7,'Nombre:',0);
$pdf->Cell(0,7,$orden['nombre'],0,1);

$pdf->Cell(40,7,'Email:',0);
$pdf->Cell(0,7,$orden['email'],0,1);

$pdf->Cell(40,7,'Metodo de Pago:',0);
$pdf->Cell(0,7,ucfirst($orden['metodo_pago']),0,1);

$pdf->Ln(10); // Salto de linea

// ----------------------------------------------------
// DIRECCIÓN DE ENVÍO
// ----------------------------------------------------
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Direccion de Envio',0,1);
$pdf->SetFont('Arial','',10);
$direccion = $orden['direccion'];
$pdf->Cell(0,7,$direccion['calle'],0,1);
$pdf->Cell(0,7,$direccion['ciudad'] . ', ' . $direccion['estado'],0,1);
$pdf->Cell(0,7,$direccion['codigo_postal'] . ', ' . $direccion['pais'],0,1);
$pdf->Ln(10); // Salto de linea

// ----------------------------------------------------
// JUEGOS COMPRADOS Y CÓDIGOS
// ----------------------------------------------------
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Juegos Adquiridos',0,1);
$pdf->SetFillColor(200,220,255); // Color de fondo para las cabeceras de la tabla

// Cabeceras de la tabla de juegos
$pdf->SetFont('Arial','B',10);
$pdf->Cell(80,10,'Juego',1,0,'C',true);
$pdf->Cell(110,10,'Codigo de Activacion',1,1,'C',true);

// Filas de la tabla con los datos
$pdf->SetFont('Arial','',10);
foreach ($orden['juegos_comprados_con_qr'] as $juego) {
    $pdf->Cell(80,10,$juego['nombre'],1);
    $pdf->Cell(110,10,$juego['codigo_qr'],1,1); 
}

$pdf->Ln(10); // Salto de linea

// ----------------------------------------------------
// DESGLOSE DE PRECIOS
// ----------------------------------------------------
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Resumen de Pago',0,1);
$pdf->SetFont('Arial','',10);

// Puedes simular un calculo de IVA, por ejemplo, 16%
$subtotal = $orden['total'] / 1.16;
$iva = $orden['total'] - $subtotal;

// Celdas vacias para alinear a la derecha
$pdf->Cell(120,7,'',0,0);
$pdf->Cell(40,7,'Subtotal:',0);
$pdf->Cell(30,7,'$'.number_format($subtotal, 2),0,1,'R');

$pdf->Cell(120,7,'',0,0);
$pdf->Cell(40,7,'IVA (16%):',0);
$pdf->Cell(30,7,'$'.number_format($iva, 2),0,1,'R');

$pdf->SetFont('Arial','B',10);
$pdf->Cell(120,7,'',0,0);
$pdf->Cell(40,7,'Total:',0);
$pdf->Cell(30,7,'$'.number_format($orden['total'], 2),0,1,'R');

$pdf->Ln(20);

// ----------------------------------------------------
// MENSAJE FINAL
// ----------------------------------------------------
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,'Gracias por tu compra. Guarda este documento como comprobante de tus codigos.',0,1,'C');


// Salida del PDF al navegador
$pdf->Output('I', 'factura_compra.pdf');

// Borrar la orden de la sesion despues de generar la factura
unset($_SESSION['orden']);
exit();
?>