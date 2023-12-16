<!doctype html>
<html lang="en">
<head>
    <style type="text/css">
               
        body{
            font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif; 
            font-size: 8.5pt;           
        }
        .table {
            width: 100%;
        }
        .specialTable{
            width: 100%; 
            font-size: 6.5pt;
        }
    </style>
</head>

<body>
    <div>
       <table class="table">
            <tr>
                <td style="text-align: center;">
                    <img src="<?= $residentId->eSignature ?>" style="width: 100px;" /><br>
                    <span style="margin-top: -20px;font-size:10pt;"><b><?= $residentId->firstName .' '.$residentId->middleName.'. '. $residentId->lastName ?></b></span><br>
                    Resident Pathologist
                </td>
                <?php
                    foreach ($signatory as $key => $value) {
                        $patho = '';
                        $patho .= '<td style="text-align: center;">';
                        $patho .= '<img src="'.$value->eSignature.'" style="width: 100px;" /><br>';
                        $patho .= '<span style="margin-top: -20px;font-size:10pt;"><b>'.$value->firstName .' '.$value->middleName.'. '. $value->lastName .'</b></span> <br>';
                        $patho .= 'Consultant Pathologist';
                        $patho .= '</td>';
                        echo $patho;
                    }
                ?>
            </tr>
       </table>
       <br>
       <br>
       <table class="table">
            <tr>
                <td style="text-align: left;color: grey;">
                    PJG-MED-LAB-Form019-Rev03
                </td>
                <td style="text-align: right;color: grey;">
                    Effective Date: 04/05/20
                </td>
            </tr>
       </table>
    </div>
</body>

</html>