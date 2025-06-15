<?php
require_once __DIR__ . '/../../config/conexion.php';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $areaId = intval($_POST['area_id']);
    $articulos = explode(',', $_POST['articulos_ids']);

    $jefeNombre = '';
    $jefeId = null;

    if (!empty($_POST['jefe_existente'])) {
        $jefeNombre = trim($_POST['jefe_existente']);
        $resJefe = mysqli_query($conectar, "SELECT id FROM jefe_activo_fijo WHERE nombre = '" . mysqli_real_escape_string($conectar, $jefeNombre) . "' LIMIT 1");
        if ($fila = mysqli_fetch_assoc($resJefe)) {
            $jefeId = $fila['id'];
        }
    } elseif (!empty($_POST['jefe_manual'])) {
        $jefeNombre = trim($_POST['jefe_manual']);
        $stmt = $conectar->prepare("INSERT INTO jefe_activo_fijo (nombre) VALUES (?)");
        $stmt->bind_param("s", $jefeNombre);
        $stmt->execute();
        $jefeId = $stmt->insert_id;
        $stmt->close();
    }

    if ($areaId > 0 && $jefeId !== null && count($articulos) > 0) {
        $stmt2 = $conectar->prepare("INSERT INTO vales_entrada (articulo_id, area_id, jefe_id, fecha_emision) VALUES (?, ?, ?, NOW())");

        foreach ($articulos as $articuloId) {
            $articuloId = intval($articuloId);
            $stmt2->bind_param("iii", $articuloId, $areaId, $jefeId);
            $stmt2->execute();
        }
        $stmt2->close();

        $query = "
            SELECT u.nombre_area, u.coordinador, u.rfc, p.razon_social, cp.nombre AS contacto
            FROM articulos a
            INNER JOIN ubicaciones u ON a.ubicacion = u.id
            INNER JOIN proveedores p ON a.proveedor_id = p.id
            INNER JOIN contactos_proveedores cp ON cp.proveedor_id = p.id
            WHERE u.id = $areaId
            ORDER BY a.id DESC LIMIT 1
        ";
        $res = mysqli_query($conectar, $query);
        $info = mysqli_fetch_assoc($res);

        $idsIn = implode(',', array_map('intval', $articulos));
        $sqlArticulos = "SELECT descripcion, marca, modelo, serie FROM articulos WHERE id IN ($idsIn)";
        $resArt = mysqli_query($conectar, $sqlArticulos);
        $articulosData = [];
        while ($row = mysqli_fetch_assoc($resArt)) {
            $articulosData[] = $row;
        }

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        // Header
        $pdf->Image(__DIR__ . '/../../Logos/headerVale.png', 10, 10, 190);

        // Vale number and title
        $today = date('Y-m-d');
        list($y, $m, $d) = explode('-', $today);
        $mesNombre = mesEspanol(date('F'));
        $fechaTexto = "$d de $mesNombre de $y";

        $resFolio = mysqli_query($conectar, "SELECT COUNT(*) AS total FROM vales_entrada WHERE DATE(fecha_emision) = CURDATE()");
        $rowFolio = mysqli_fetch_assoc($resFolio);
        $folio = str_pad($rowFolio['total'], 2, '0', STR_PAD_LEFT) . '/' . date('Y');

        // Título VALE DE ENTRADA NORMAL más abajo
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetY(40); // Cambiado a y = 50mm
        $pdf->Cell(190, 8, utf8_decode('VALE DE ENTRADA'), 0, 1, 'C');

        // Fecha más alineada con diseño institucional
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(120, 50); // y = 60mm aprox
        $pdf->Cell(0, 6, utf8_decode("Mérida, Yucatán, $fechaTexto"), 0, 1, 'R');

        // Añadir bloque PRESENTE
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, utf8_decode('C. ENCARGADO DE VIGILANCIA'), 0, 1);
        $pdf->Cell(0, 6, utf8_decode('HOSPITAL REGIONAL MÉRIDA'), 0, 1);
        $pdf->SetFont(family: 'Arial', style: 'BU', size: 10);
        $pdf->Cell(0, 6, utf8_decode('P R E S E N T E'), 0, 1);
        $pdf->Ln(8);


        // Texto principal
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('POR ESTE MEDIO SE AUTORIZA AL C. '));

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode($info['contacto'] . ' '));

        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('DE LA EMPRESA '));

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode($info['razon_social'] . ', '));

        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('EL INGRESO AL HOSPITAL REGIONAL, AL ÁREA DE '));

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode(strtoupper($info['nombre_area']) . ', '));

        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode('EL BIEN CUYAS CARACTERÍSTICAS SE RELACIONAN A CONTINUACIÓN.'));
        $pdf->Ln(15);


        // Tabla
        $wDesc = 60;
        $wMarca = 40;
        $wModelo = 40;
        $wSerie = 50;

        $tablaAncho = $wDesc + $wMarca + $wModelo + $wSerie;
        $margenIzquierdo = (210 - $tablaAncho) / 2; // Centrado en A4 (210mm ancho)

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetX($margenIzquierdo);
        $pdf->Cell($wDesc, 7, utf8_decode('Descripción'), 1, 0, 'C');
        $pdf->Cell($wMarca, 7, 'Marca', 1, 0, 'C');
        $pdf->Cell($wModelo, 7, 'Modelo', 1, 0, 'C');
        $pdf->Cell($wSerie, 7, 'Serie', 1, 1, 'C'); // 1 = salto de línea


        $pdf->SetFont('Arial', '', 11);
        foreach ($articulosData as $a) {
            $pdf->SetX($margenIzquierdo);
            $pdf->Cell($wDesc, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['descripcion']), 1, 0, 'C');
            $pdf->Cell($wMarca, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['marca']), 1, 0, 'C');
            $pdf->Cell($wModelo, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['modelo']), 1, 0, 'C');
            $pdf->Cell($wSerie, 6, iconv('UTF-8', 'windows-1252//TRANSLIT', $a['serie']), 1, 1, 'C');
        }

        $pdf->Ln(25);

        // Firmas
    
        // Nombre del coordinador centrado
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, utf8_decode($info['coordinador']), 0, 1, 'C');

        // Línea de firma opcional (bajo el nombre)
        $y = $pdf->GetY();
        $pdf->Line(60, $y + 3, 150, $y + 3); // Línea horizontal centrada

        // Cargo y firma
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5); // Un poco de espacio antes del texto
        $pdf->Cell(0, 6, utf8_decode('COORDINADOR, JEFE O ENCARGADO DEL ÁREA'), 0, 1, 'C');
        $pdf->Cell(0, 6, utf8_decode('NOMBRE, FIRMA Y SELLO'), 0, 1, 'C');


        $pdf->Image(__DIR__ . '/../../Logos/footerVale.png', 10, 265, 190);

        $nombreArchivo = "vale_entrada_" . str_replace('-', '', $today) . "_" . str_pad($rowFolio['total'], 2, '0', STR_PAD_LEFT) . ".pdf";
        $path = __DIR__ . "/../../doc/$nombreArchivo";
        $pdf->Output('F', $path);

        header("Location: ../../doc/$nombreArchivo");
        exit;
    } else {
        echo "<script>alert('Error: Datos incompletos'); history.back();</script>";
    }
}
