<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RequestModel;
use App\Models\InvoiceModel;
use App\Models\AuthModel;
use App\Models\HistoryModel;
use \Firebase\JWT\JWT;

// QR Code Components
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class Generate extends BaseController
{
    public function __construct(){
        //Models
        $this->reqModel = new RequestModel();
        $this->invModel = new InvoiceModel();
        $this->authModel = new AuthModel();
        $this->historyModel = new HistoryModel();
    }

    public function createInvoice(){

        $data = $this->request->getJSON();
        $downloadLink = base_url("index.php/mlrs/api/v1/generate/print/invoice/". $data->invoiceId);

        $response = [
            "urlLink" =>  $downloadLink
        ];
            
        return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));

    }

    public function generateInvoicePdf($id){

        $query = $this->invModel->getInvoiceDetails(["id" => $id]);
        $query->customerInfo->contactPerson = json_decode($query->customerInfo->contactPerson);
        $query->customerInfo->addressInfo = json_decode($query->customerInfo->addressInfo);

        // echo "<pre>";
        // print_r($query);

        ob_start();
        $dompdf = new \Dompdf\Dompdf(['enable_font_subsetting' => true]); 
        $dompdf->loadHtml(view('invoice/print', (array)$query));
        $dompdf->setPaper('Legal', 'portrait');
        $dompdf->render();
        $dompdf->stream('Invoice', ["Attachment" => false]);
    }
}