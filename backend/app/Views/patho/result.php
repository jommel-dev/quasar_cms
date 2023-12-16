<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title><?= strtoupper($lastName .', '. $firstName .' '. $middleName) ?></title>
  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"> -->

    <style type="text/css">
               
        body{
            font-family: "Times New Roman", Times, serif;            
        }
        .table {
            width: 100%; 
            font-size: 11pt;
        }
        .mt-1 {
            margin-top: 10px;
        }
        .nameContainer {
            border-bottom: 1px solid black;
            font-size: 9pt;
        }
        .name {
            border-bottom: 1px solid black;
        }
        .normalSize {
            font-size: 9pt;
        }
        .classTable{
            width: 100%; 
            font-size: 9pt;
        }
        .specialTable{
            width: 100%; 
            font-size: 6.5pt;
        }
        .procedureContainer {
            width: 100%;
            /* border: 1px solid black; */
            text-indent: 15px;
        }
    </style>
</head>

<body>
    <div class="container mt-1">
    
        <table class="table" style="font-size:10pt;">
            <tr>
                <td style="width: 50%;"><strong>Patient Name:</strong>&nbsp; <?= strtoupper($lastName .', '. $firstName .' '. $middleName) ?></td>
                <td style="width: 33%;"><strong>Accession No.:</strong>&nbsp; <?= $accessionNumber ?></td>
                <td rowspan="5" style="width: 20%;">
                    <img src="<?= $qrCode ?>" alt="QR Code" style="width: 70px;" />
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>Birthdate:</strong>&nbsp; <?= date('m/d/Y', strtotime($birthDate)) ?></td>
                <td style="width: 33%;"><strong>Case No.:</strong>&nbsp; <?= $otherDetails[1]->value ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>Age/Sex:</strong>&nbsp; <?= $age .'/'. $sex ?></td>
                <td style="width: 33%;"><strong>Date Operation:</strong>&nbsp;<?= date('m/d/Y', strtotime($dateOfOperation)) ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>Ward/Institution:</strong>&nbsp; <?= $otherDetails[1]->value ?></td>
                <td style="width: 33%;"><strong>Date Received:</strong>&nbsp; <?= date('m/d/Y', strtotime($createdDate)) ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>Referring Physician:</strong>&nbsp; <?= $otherDetails[2]->value ?></td>
                <td style="width: 33%;"><strong>Date Reported:</strong>&nbsp; <?= date('m/d/Y', strtotime($createdDate)) ?></td>
            </tr>
        </table>
        <br>
        <br>
        <hr />
        <br>
        <div class="procedureContainer">
            <?= $procedureDetails ?>
        </div>
        <br>
        <br>
        <div class="">
            <h5><strong>GROSS AND MICROSCOPIC DESCRIPTION</strong></h5>
            <?= $surgeryResult ?>
        </div>
    </div>
    
</body>

</html>