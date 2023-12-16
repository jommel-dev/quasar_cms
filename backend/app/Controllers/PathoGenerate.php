<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\PathologyRequestModel;
use App\Models\RequestModel;
use App\Models\PathoHistoryModel;
use TCPDF;

// QR Code Components
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        $headerData = $this->getHeaderData();
        $this->Image('img/dohlogo.png', 35, 15, 20, '', 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
        $this->Image('img/logo.jpg', 155, 15, 20, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
        $this->Image('img/iso.png', 88, 38.5, 3, '', 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
        $this->writeHTML($headerData['string']);
        $this->SetTopMargin(55);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-40);
        // Set font
        
        $pageFooter = view('patho/footer');
        $this->writeHTML($pageFooter, true, false, true, false, '');
        
    }
}

class PathoGenerate extends BaseController
{
    public function __construct(){
        //Models
        $this->pathoModel = new PathologyRequestModel();
        $this->reqModel = new RequestModel();
        $this->historyPathoModel = new PathoHistoryModel();
    }


    public function downloadResult(){
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if(!$authorization){
            $response = [
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            exit();
        }

        $data = $this->request->getJSON();
        $downloadLink = base_url("index.php/mlrs/api/v1/generate/v2/report/pdf/". $data->appId);

         //save application history action
         $history = [
            'appId' => $data->appId,
            'requestData' => json_encode($data),
            'actionStatus' => $data->action,
            'userId' => $data->user,
            'actionTaken' => 'Download Result',
        ];
        $this->historyPathoModel->insert($history);

        $response = [
            "urlLink" =>  $downloadLink
        ];
        
        return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));

    }
    public function generateReportResult($id){
        //Data Gather 
        $query = $this->pathoModel->getDetails(["id" => $id]);
        $query->otherDetails = json_decode($query->otherDetails);
        $query->surgeryResult = json_decode($query->surgeryResult);
        $query->procedureDetails = json_decode($query->procedureDetails);

        // convert the signatory string to array
        $signatories = explode(",", $query->signatory);
        $signData = [];
        
        foreach($signatories as $key => $value){
            $signData[$key] =  $this->reqModel->getUserSignature(["id" => $value]);
        }

        $query->signatory = $signData;
        $query->residentId = $this->reqModel->getUserSignature(["id" => $query->residentId]);
        
        // return $this->response
        //             ->setStatusCode(200)
        //             ->setContentType('application/json')
        //             ->setBody(json_encode($query));
        // exit();

        // QR Code Implementation
        $writer = new PngWriter();
        // Create QR code
        $qrLink = base_url("index.php/mlrs/api/v1/generate/v2/report/pdf/". $id);
        // print_r($qrLink);
        // exit();
        $qrCode = QrCode::create($qrLink)
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
        ->setSize(300)
        ->setMargin(10)
        ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->setForegroundColor(new Color(0, 0, 0))
        ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);

        // header('Content-Type: '.$result->getMimeType());
        // echo $result->getString();
        // echo "<br/>";

        $qrUri = $result->getDataUri();

        $query->qrCode = $qrUri;

        $pageHTML = view('patho/result', (array)$query);
        $pageHeader = view('patho/header');
        
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // Set the Header & Footer
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs=$pageHeader, $tc=array(0,0,0), $lc=array(0,0,0));

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        
        $pdf->AddPage();
        
        // output the HTML content
        $pdf->writeHTML($pageHTML, true, false, true, false, '');
        // reset pointer to the last page
        // $pdf->lastPage();
        
        //Close and output PDF document
        $pdf->Output('test.pdf', 'I');
        exit();
    }



}