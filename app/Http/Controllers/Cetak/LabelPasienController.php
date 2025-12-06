<?php

namespace App\Http\Controllers\Cetak;

use App\Http\Controllers\Controller;
use App\Libraries\EasyTable\exFPDF;
use App\Models\Kunjungan;
use Carbon\Carbon;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;

class LabelPasienController extends Controller
{

    public function index(Kunjungan $kunjungan)
    {
        $kunjungan->load(['pasien']);

        $fileName = 'Label Pasien ' . $kunjungan->pasien->norm;

        $pdf = new Fpdf('L', 'mm', array(76, 50));
        $pdf->SetMargins(0, 0);
        $pdf->AddPage();
        $pdf->setTitle($fileName);

        $pdf->SetFont('Arial', 'B', 8);

        $pdf->SetY(2);
        $pdf->SetX(2);
        $pdf->MultiCell(18, 4, 'No.RM/NRK', 0, 'L');
        $pdf->SetY(2);
        $pdf->SetX(19);
        $pdf->MultiCell(3, 3, ':', 0, 'L');
        $pdf->SetY(2);
        $pdf->SetX(21);
        $pdf->MultiCell(60, 4, $kunjungan->pasien->norm . ' / ' . $kunjungan->noregistrasi, 0, 'L');
        $pdf->SetY(2);
        $pdf->SetX(55);
        $pdf->MultiCell(20, 4, Carbon::parse($kunjungan->tanggal_registrasi)->translatedFormat('d/m/Y'), 1, 'C');


        $pdf->SetY(6);
        $pdf->SetX(2);
        $pdf->MultiCell(15, 4, 'Nama', 0, 'L');
        $pdf->SetY(6);
        $pdf->SetX(19);
        $pdf->MultiCell(3, 3, ':', 0, 'L');
        $pdf->SetY(6);
        $pdf->SetX(21);
        $pdf->MultiCell(48, 4, $kunjungan->pasien->nama, 0, 'L');

        $pdf->SetY(10);
        $pdf->SetX(68);
        $pdf->MultiCell(4, 5, $kunjungan->pasien->jenis_kelamin, 1, 'C');


        $pdf->SetY(10);
        $pdf->SetX(2);
        $pdf->MultiCell(25, 4, 'Tgl Lahir', 0, 'L');
        $pdf->SetY(10);
        $pdf->SetX(19);
        $pdf->MultiCell(3, 3, ':', 0, 'L');
        $pdf->SetY(10);
        $pdf->SetX(21);
        $pdf->MultiCell(45, 4, $kunjungan->pasien->tanggal_lahir . '     (' . $kunjungan->pasien->getUsia() . ') ', 0, 'L');

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetY(14);
        $pdf->SetX(2);
        $pdf->MultiCell(15, 4, 'Alamat', 0, 'L');
        $pdf->SetY(14);
        $pdf->SetX(19);
        $pdf->MultiCell(3, 3, ':', 0, 'L');
        $pdf->SetY(14);
        $pdf->SetX(21);
        $pdf->MultiCell(60, 4, $kunjungan->pasien->alamat, 0, 'L');


        $pdf->Output('I', $fileName);
        exit;
    }
}
