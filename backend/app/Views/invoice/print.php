<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<title>Header and Footer example</title>
<style type="text/css">

@page {
	margin: 0.5cm 1cm 0.5cm 1cm;
}

body {
  font-family: sans-serif;
	margin: 0.5cm 0;
	text-align: justify;
}

#header,
#footer {
  position: fixed;
  left: 0;
	right: 0;
	font-size: 0.9em;
}

#header {
  top: 0;
  margin-bottom: 20px;
  text-align: center;
}

#footer {
  bottom: 0;
  border-top: 0.1pt solid #aaa;
}

#header table,
#footer table {
	width: 100%;
	border-collapse: collapse;
	border: none;
}

#header td,
#footer td {
  padding: 0;
	width: 50%;
}

.page-number {
  text-align: center;
}

.page-number:before {
  content: "Page " counter(page);
}

hr {
  page-break-after: always;
  border: 0;
}

.space-between{
    display: flex;
    flex-direction: row;
}
.title{
    font-size: 19pt;
}
.sub-title{
    font-size: 9pt;
}
.sub-title1{
    font-size: 10pt;
}
.table{
    width: 100%;
}

</style>
  
</head>

<body>

    <div id="header">
        <span class="title"><strong>DVS PET SUPPLIES TRADING</strong></span> <br>
        <span class="sub-title">Brgy. San Ricardo Talavera, Nueva Ecija</span><br>
        <span class="sub-title1">Non-VAT Reg. TIN: 703-965-792-000</span><br>
        <span class="sub-title">Tel No.: (044) 950 9928 - Email Add: dvspetsuppliestrading@gmail.com</span>
    </div>
    <br><br><br>
    <table class="table">
        <tr>
            <td>
                <h3><u>TRUST RECEIPT AGREEMENT</u></h3>
            </td>
            <td>
                No.: <span class="invoice-number" style="font-size: 16pt; color: red;"><?= $invoiceNo ?></span>
            </td>
        </tr>
    </table>
    <table class="table" style="font-size: 10pt;">
        <tr>
            <td style="width: 70%;">
                <br>
                <strong>Customer's Name:</strong> <?= $customerInfo->contactPerson->name ?><br>
                <strong>Customer's Address:</strong> <?= 
                    $customerInfo->address .' '. $customerInfo->addressInfo->barangay->label .' '. $customerInfo->addressInfo->city->label .' '. $customerInfo->addressInfo->province->label
                ?>
            </td>
            <td>
                <strong>TRA Date:</strong> <?= date('Y-m-d', strtotime($createdDate))?><br>
                <strong>Terms:</strong> <?= $termsOfPayment->label .' ('. $otherDetails->transType->label.')' ?><br>
                <strong>Due Date:</strong> 
            </td>
        </tr>
    </table>
    <table border="1" style="width: 100%; border-collapse: collapse;font-size:9.5pt;" >
        <tr style="text-align: center;">
            <td style="width: 10%;"><strong>QTY</strong></td>
            <td style="width: 45%;"><strong>ITEM NAME</strong></td>
            <td style="width: 15%;"><strong>UNIT PRICE</strong></td>
            <td style="width: 15%;"><strong>AMOUNT</strong></td>
        </tr>
        <?php 
            $item = '';
            foreach($orderList as $key => $value){
                $item .= '<tr>';
                    $item .= '<td style="text-align: center;">'. $value->qty .'</td>';
                    $item .= '<td style="text-align: center;">'. $value->itemName .'</td>';
                    $item .= '<td style="text-align: center;">'. number_format((float)$value->price, 2, '.', '') .'</td>';
                    $item .= '<td style="text-align: center;">'. number_format((floatval($value->price) * floatval($value->qty)), 2, '.', '') .'</td>';
                $item .= '</tr>';
            }
            for ($x = 0; $x <= 1; $x++){
                $item .= '<tr>';
                    $item .= '<td style="text-align: center;"><br></td>';
                    $item .= '<td></td>';
                    $item .= '<td style="text-align: center;"> </td>';
                    $item .= '<td style="text-align: center;"> </td>';
                $item .= '</tr>';
            }

            echo $item;
        ?>
    </table>
    <table style="width: 100%; border-collapse: collapse;font-size:9pt;" >
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 35%;">
                <strong>GROSS TOTAL AMOUNT: </strong><br>
                <strong>DISCOUNT: </strong><br>
                <strong>NET TOTAL AMOUNT: </strong>
            </td>
            <td style="width: 15%;">
                <?= number_format((float)$gross, 2, '.', '') ?> <br>
                <?= number_format((float)$discount, 2, '.', '') ?> <br>
                <?= number_format((float)$netAmount, 2, '.', '') ?>
            </td>
        </tr>
    </table>
    <p style="font-size: 8pt;">
        I, ______________________________ as a trustee, Filipino of legal age, Single/Married and a resident of __________________________________ hereby received in trust above described merchandise from
        DVS PET SUPPLIES TRADING as trustor, under the following terms and condition.
        <ol style="font-size: 8pt;">
            <li>I hereby agree to hold in trust; the above properties of the trustor, with the right to sell for cash for trustor's account and to hand the proceeds thereof to the trustor on or beforedue date.</li>
            <li>Should there be unsold merchandise on due date trustor may at her option extend this agreement to another due date the same terms and conditions.</li>
            <li>Commisions on sales shall be paid accordingly by the trustor depending on the amount remitted and at the rate agreed upon by both parties.</li>
            <li>Before due date the trustor may cancel the agreement and take possession of the merchandise of proceeds that may then be found.</li>
            <li>
                In case of my failure to remit on due date without prejudice to my criminal and civil liabilities under Philippine Laws, I shall be liable for the following:
                <ol type="a">
                    <li>24% per annum as interest on the unremitted amount/merchandise.</li>
                    <li>10% collection fee.</li>
                    <li>25% as attorney's fee but not less than Php500.00</li>
                    <li>25% as liquidated damages but not less than Php1000.00</li>
                    <li>In case of suit, the same may filled only in the proper courts in the Municipality of Talavera</li>
                </ol>
            </li>
        </ol>  
    </p>
    
</body>
</html>
