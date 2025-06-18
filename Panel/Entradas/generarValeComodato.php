<?php
require_once __DIR__ . '/../../config/conexion.php';
date_default_timezone_set('America/Merida');
define('FPDF_FONTPATH', __DIR__ . '/../../lib/fpdf/font/');
require_once __DIR__ . '/../../lib/fpdf/fpdf.php';

function mesEspanol($mesIngl)
{
    $meses = [
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    ];
    return $meses[$mesIngl] ?? $mesIngl;
}

class ValeEntradaPDF extends FPDF
{
    function Header()
    {
        $this->Image(__DIR__ . '/../../Logos/headerVale.png', 10, 10, 190);
        $this->Ln(30); // Espacio debajo del encabezado
    }

    function Footer()
    {
        $this->SetY(-25);
        $this->Image(__DIR__ . '/../../Logos/footerVale.png', 10, $this->GetY(), 190);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $areaId = intval($_POST['area_id']);
    $articulos = explode(',', $_POST['articulos_ids']);
    $proveedorNombre = isset($_POST['proveedor']) ? trim($_POST['proveedor']) : '';

    if ($areaId > 0 && count($articulos) > 0 && $proveedorNombre !== '') {
        $query = "
            SELECT u.nombre_area, u.coordinador, u.rfc, cp.nombre AS contacto
            FROM articulos a
            INNER JOIN ubicaciones u ON a.ubicacion = u.id
            INNER JOIN proveedores p ON a.proveedor_id = p.id
            INNER JOIN contactos_proveedores cp ON cp.proveedor_id = p.id
            WHERE u.id = $areaId
            ORDER BY a.id DESC LIMIT 1
        ";
        $res = mysqli_query($conectar, $query);
        $info = mysqli_fetch_assoc($res);
        $activoFijo = mysqli_query($conectar, "
    SELECT coordinador FROM ubicaciones WHERE nombre_area = 'Activo Fijo' LIMIT 1
");
        $coordinadorAF = mysqli_fetch_assoc($activoFijo)['coordinador'] ?? 'JEFE DE ACTIVO FIJO';

        $info['razon_social'] = $proveedorNombre;

        $idsIn = implode(',', array_map('intval', $articulos));
        $sqlArticulos = "SELECT descripcion, marca, modelo, serie FROM articulos WHERE id IN ($idsIn)";
        $resArt = mysqli_query($conectar, $sqlArticulos);
        $articulosData = [];
        while ($row = mysqli_fetch_assoc($resArt)) {
            $articulosData[] = $row;
        }

        $pdf = new ValeEntradaPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);


        $today = date('Y-m-d H:i:s');
        list($y, $m, $d) = explode('-', substr($today, 0, 10));
        $mesNombre = mesEspanol(date('F', strtotime($today)));
        $fechaTexto = "$d de $mesNombre de $y";

        $resFolio = mysqli_query($conectar, "SELECT COUNT(*) AS total FROM vales_entrada WHERE DATE(fecha_emision) = CURDATE()");
        $rowFolio = mysqli_fetch_assoc($resFolio);
        $folio = str_pad($rowFolio['total'], 2, '0', STR_PAD_LEFT) . '/' . date('Y');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetY(40);
        $pdf->Cell(190, 8, utf8_decode('VALE DE ENTRADA'), 0, 1, 'C');


        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(120, 50);
        $pdf->Cell(0, 6, utf8_decode("Mérida, Yucatán, $fechaTexto"), 0, 1, 'R');

        $pdf->Ln(8);
        $pdf->Cell(0, 6, utf8_decode('C. ENCARGADO DE VIGILANCIA'), 0, 1);
        $pdf->Cell(0, 6, utf8_decode('HOSPITAL REGIONAL MÉRIDA'), 0, 1);
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 6, utf8_decode('P R E S E N T E'), 0, 1);
        $pdf->Ln(8);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('POR ESTE MEDIO SE AUTORIZA AL '));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode($info['contacto'] . ' '));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('REPRESENTANTE DE LA EMPRESA '));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode($info['razon_social'] . ' '));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('EL INGRESO AL HOSPITAL REGIONAL ESPECÍFICAMENTE AL ÁREA DE '));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode(strtoupper($info['nombre_area']) . '. '));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('EL BIEN CUYAS CARACTERÍSTICAS SE RELACIONAN A CONTINUACIÓN. EL CUAL ENTRA POR COMODATO.'));
        $pdf->Ln(15);

        $wDesc = 60;
        $wMarca = 40;
        $wModelo = 40;
        $wSerie = 50;
        $tablaAncho = $wDesc + $wMarca + $wModelo + $wSerie;
        $margenIzquierdo = (210 - $tablaAncho) / 2;

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetX($margenIzquierdo);
        $pdf->Cell($wDesc, 7, utf8_decode('Descripción'), 1, 0, 'C');
        $pdf->Cell($wMarca, 7, 'Marca', 1, 0, 'C');
        $pdf->Cell($wModelo, 7, 'Modelo', 1, 0, 'C');
        $pdf->Cell($wSerie, 7, 'Serie', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 11);
        foreach ($articulosData as $a) {
            $pdf->SetX($margenIzquierdo);
            $pdf->Cell($wDesc, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['descripcion']), 1, 0, 'C');
            $pdf->Cell($wMarca, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['marca']), 1, 0, 'C');
            $pdf->Cell($wModelo, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['modelo']), 1, 0, 'C');
            $pdf->Cell($wSerie, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['serie']), 1, 1, 'C');
        }


        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(15);
        $pdf->Cell(0, 6, utf8_decode('V.B'), 0, 2, 'C');
        $pdf->Ln(15);

        // ========================
        // Firma del Coordinador
        // ========================

        if ($pdf->GetY() > 230) {
            $pdf->AddPage();
        }

        // Firma del Coordinador (mismo estilo que Jefe de Activo, pero centrado)
        $pdf->SetFont('Arial', '', 10);
        $y = $pdf->GetY();
        $pdf->Line(60, $y, 150, $y); // Línea centrada
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(2);
        $pdf->Cell(0, 6, utf8_decode('COORDINADOR, JEFE O ENCARGADO DEL ÁREA'), 0, 1, 'C');


        // ========================
        // Firma del Jefe de Activo Fijo
        // ========================

        if ($pdf->GetY() > 230) {
            $pdf->AddPage();
        }
        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(15);
        $pdf->Cell(70, 6, utf8_decode('ELABORÓ'), 0, 2, 'C');
        $pdf->Ln(15); // Espacio entre firmas


        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(15);
        $pdf->Cell(70, 6, utf8_decode($coordinadorAF), 0, 2, 'C');
        $pdf->Line(15, $pdf->GetY(), 85, $pdf->GetY());
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(15);
        $pdf->Cell(70, 6, utf8_decode('JEFE DE ACTIVO FIJO'), 0, 1, 'C');

        // ========================
        // Firma del representante de la empresa
        // ========================

        if ($pdf->GetY() > 60) {
            $pdf->SetY($pdf->GetY() - 33);
        } else {
            $pdf->Ln(10); // Agrega algo de espacio sin subir
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(125);
        $pdf->Cell(70, 6, utf8_decode('INGRESO'), 0, 2, 'C');
        $pdf->Ln(15);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(125);
        $pdf->Cell(70, 6, utf8_decode(strtoupper($info['contacto'])), 0, 2, 'C');
        $pdf->Line(125, $pdf->GetY(), 195, $pdf->GetY());

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(125);
        $pdf->Cell(70, 6, utf8_decode('REPRESENTANTE DE LA EMPRESA'), 0, 2, 'C');
        $pdf->Cell(70, 6, utf8_decode(strtoupper($info['razon_social'])), 0, 2, 'C');

        // ========================
        // Firma del encargado de vigilancia
        // ========================
        if ($pdf->GetY() > 230) {
            $pdf->AddPage();
        }
        // Espacio después de firmas anteriores
        $pdf->Ln(10);

        // Firma del C. ENCARGADO DE VIGILANCIA
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, utf8_decode('ENTERADO'), 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Line(60, $pdf->GetY(), 150, $pdf->GetY());
        $pdf->Cell(0, 6, utf8_decode('C. ENCARGADO DE VIGILANCIA'), 0, 1, 'C');



        $nombreArchivo = "vale_comodato_" . date('Ymd_His') . "_id{$id}.pdf";


        $path = __DIR__ . "/../../doc/$nombreArchivo";
        // ✅ Insertar el vale una sola vez
        mysqli_query($conectar, "
    INSERT INTO vales_entrada (area_id, fecha_emision, archivo_pdf)
    VALUES ($areaId, '$today', '$nombreArchivo')
");

        $valeId = mysqli_insert_id($conectar);

        // ✅ Insertar los artículos asociados al vale
        foreach ($articulos as $articuloId) {
            $articuloId = intval($articuloId);
            mysqli_query($conectar, "
        INSERT INTO vales_articulos (vale_id, articulo_id)
        VALUES ($valeId, $articuloId)
    ");
        }


        $pdf->Output('F', $path);

        header("Location: historialVales.php?origen=comodato");
        exit;
    } else {
        echo "<script>alert('Error: Datos incompletos'); history.back();</script>";
    }
}
