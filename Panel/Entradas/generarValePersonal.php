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
        $this->Ln(30);
    }

    function Footer()
    {
        $this->SetY(-25);
        $this->Image(__DIR__ . '/../../Logos/footerVale.png', 10, $this->GetY(), 190);
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "
        SELECT ap.descripcion_personal, ap.numero_empleado, ap.nombre_persona, u.nombre_area, u.coordinador
        FROM articulos_personales ap
        INNER JOIN ubicaciones u ON ap.area_id = u.id
        WHERE ap.id = $id
        LIMIT 1
    ";
    $res = mysqli_query($conectar, $query);
    $data = mysqli_fetch_assoc($res);

    if ($data) {
        $coordinadorAF = mysqli_fetch_assoc(
            mysqli_query($conectar, "SELECT coordinador FROM ubicaciones WHERE nombre_area = 'Activo Fijo' LIMIT 1")
        )['coordinador'] ?? 'JEFE DE ACTIVO FIJO';

        $pdf = new ValeEntradaPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        $fecha = date('Y-m-d H:i:s');
        list($y, $m, $d) = explode('-', substr($fecha, 0, 10));
        $mesNombre = mesEspanol(date('F', strtotime($fecha)));
        $fechaTexto = "$d de $mesNombre de $y";

        $folioRes = mysqli_query($conectar, "SELECT COUNT(*) AS total FROM vales_entrada WHERE DATE(fecha_emision) = CURDATE()");
        $folioNum = str_pad(mysqli_fetch_assoc($folioRes)['total'], 2, '0', STR_PAD_LEFT) . '/' . date('Y');

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
        $pdf->Write(6, utf8_decode(strtoupper($data['nombre_persona'])));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode(' TRABAJADORA DE ESTA INSTITUCIÓN CON NÚMERO DE EMPLEADO '));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode($data['numero_empleado']));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode(' EL INGRESO AL HOSPITAL, ESPECÍFICAMENTE AL ÁREA DE '));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Write(6, utf8_decode(strtoupper($data['nombre_area'])));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, utf8_decode(' EL BIEN CUYAS CARACTERÍSTICAS SE RELACIONAN A CONTINUACIÓN, EL CUÁL ES EQUIPO PROPIO CON FACTURA.'));




        $pdf->Ln(15);


        $pdf->SetFont('Arial', 'B', 11);

        // Medidas de columna
        $col1 = 60;
        $col2 = 110;
        $totalAncho = $col1 + $col2;
        $margenIzq = ($pdf->GetPageWidth() - $totalAncho) / 2;

        $pdf->SetX($margenIzq);
        $pdf->Cell($col1, 8, utf8_decode('Descripción del artículo'), 1, 0, 'C');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell($col2, 8, iconv('UTF-8', 'windows-1252//TRANSLIT', $data['descripcion_personal']), 1, 1, 'C');

        // Firmas

        $pdf->Ln(25); // Espacio antes de firmas

        // Títulos de las firmas (solo izquierda y derecha se imprimen ahora)
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(63, 6, utf8_decode('ELABORÓ'), 0, 0, 'C');
        $pdf->Cell(63, 6, '', 0, 0, 'C'); // centro se imprimirá después
        $pdf->Cell(63, 6, utf8_decode('INGRESO'), 0, 1, 'C');

        // 🟩 Definir base y desplazamiento
        $yBase = $pdf->GetY() + 18;
        $yVigilante = $yBase + 35; // bloque del vigilante más abajo

        // Líneas de firma
        $pdf->Line(15, $yBase, 75, $yBase); // Coordinador
        $pdf->Line(141, $yBase, 201, $yBase); // Empleado
        $pdf->Line(78, $yVigilante, 138, $yVigilante); // Vigilante (más abajo)

        // 🔽 Insertar título ENTERADO abajo
        $pdf->SetY($yVigilante - 15); // subir un poco antes de la línea
        $pdf->SetX(75);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(63, 6, utf8_decode('ENTERADO'), 0, 1, 'C');

        // 🔽 Nombres y cargos
        $pdf->SetY($yBase + 3);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(63, 5, utf8_decode(strtoupper($coordinadorAF)), 0, 0, 'C'); // Coordinador
        $pdf->Cell(63, 5, '', 0, 0, 'C'); // espacio en medio
        $pdf->Cell(63, 5, utf8_decode('C. ' . strtoupper($data['nombre_persona'])), 0, 1, 'C'); // Empleado

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(63, 5, utf8_decode('JEFE DE ACTIVO FIJO'), 0, 0, 'C');
        $pdf->Cell(63, 5, '', 0, 0, 'C');
        $pdf->Cell(63, 5, utf8_decode('NUM. DE EMPLEADO ' . $data['numero_empleado']), 0, 1, 'C');

        // 🔽 Nombre del vigilante abajo
        $pdf->SetY($yVigilante + 3);
        $pdf->SetX(75);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(63, 5, utf8_decode('C. ENCARGADO DE VIGILANCIA'), 0, 1, 'C');

        // Vigilante: nombre y cargo en su propia posición (más abajo)
        $pdf->SetY($yVigilante + 3);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(75);
        $pdf->Cell(63, 5, utf8_decode('C. ENCARGADO DE VIGILANCIA'), 0, 1, 'C');

        // Guardar PDF
        $nombreArchivo = "vale_personal_" . date('Ymd_His') . "_id{$id}.pdf";

        $ruta = __DIR__ . "/../../doc/$nombreArchivo";

        mysqli_query($conectar, "
    INSERT INTO vales_entrada (area_id, fecha_emision, archivo_pdf)
    VALUES ((SELECT area_id FROM articulos_personales WHERE id = $id), '$fecha', '$nombreArchivo')
");
        $valeId = mysqli_insert_id($conectar);

        mysqli_query($conectar, "
    INSERT INTO vales_articulos_personales (vale_id, articulo_personal_id)
    VALUES ($valeId, $id)
");

        $pdf->Output('F', $ruta);
        header("Location: historialVales.php?origen=personal");
        exit;
    } else {
        echo "<script>alert('Artículo no encontrado.'); history.back();</script>";
    }
} else {
    echo "<script>alert('ID no válido'); history.back();</script>";
}
