<?php
require_once 'C:\xampp\htdocs\VERSION3.0\vendor\autoload.php';


// Your PDF generation code here


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar el cronograma desde el formulario
    $cronograma = unserialize($_POST['cronograma']);
    
    // Crear un nuevo PDF
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Mi Empresa');
    $pdf->SetTitle('Cronograma de Pagos');
    $pdf->SetHeaderData('', 0, 'Cronograma de Pagos', 'Resumen del crédito vehicular');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    // Generar contenido del PDF
    $html = '<h1>Cronograma de Pagos</h1>';
    $html .= '<table border="1" cellpadding="5">';
    $html .= '<tr>
                <th>Mes</th>
                <th>Monto Capital (S/)</th>
                <th>Interés (S/)</th>
                <th>Cuota Mensual (S/)</th>
                <th>Saldo Capital (S/)</th>
                <th>Seguro (S/)</th>
              </tr>';

    foreach ($cronograma as $pago) {
        $html .= '<tr>
                    <td>' . $pago['mes'] . '</td>
                    <td>' . number_format($pago['monto_capital'], 2) . '</td>
                    <td>' . number_format($pago['interes'], 2) . '</td>
                    <td>' . number_format($pago['cuota_mensual'], 2) . '</td>
                    <td>' . number_format($pago['saldo_capital'], 2) . '</td>
                    <td>' . number_format($pago['seguro'], 2) . '</td>
                  </tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Cerrar y crear el PDF
    $pdf->Output('cronograma_pagos.pdf', 'D'); // 'D' fuerza la descarga del PDF
}
?>
