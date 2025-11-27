<?php

namespace App\Http\Controllers\Cetak;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\RawbtPrintConnector;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\CapabilityProfile;

class PenjualanController extends Controller
{
    public function printTest()
    {
        $profilePrinter = CapabilityProfile::load("POS-5890");

        $connector = new DummyPrintConnector();
        $printer = new Printer($connector, $profilePrinter);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("===========PRINT TEST===========\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Name : " . config('app.company.name') . "\n");
        $printer->text("Address : " . config('app.company.address') . "\n");
        $printer->text("Jenis : " . config('app.company.phone') . "\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(Carbon::now()->toDateTimeString() . "\n");
        $printer->text("================================\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->feed(1);
        $printer->text("START KLINIK\n");
        $printer->text("© HardiSoftware - All Rights Reserved\n");
        $printer->feed(2);
        $printer->cut();

        $raw = base64_encode($connector->getData());

        $printer->close();

        return response()->json([
            'data' => $raw
        ]);
    }
}
