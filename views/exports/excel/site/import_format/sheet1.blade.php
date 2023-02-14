<table>
    <tr>
        <td style="font-size: 18;"><b>IMPORT DATABASE QFEST</b></td>
    </tr>
</table>

<table>
    <tr>
        <td align="center" valign="center" height="30" width="20" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>LINK ID</b></td>
        <td align="center" valign="center" height="30" colspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>AREA</b></td>
        <td align="center" valign="center" height="30" colspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>CLIENT</b></td>
        <td align="center" valign="center" height="30" colspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>SERVICE</b></td>
        <td align="center" valign="center" height="30" width="15" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>ACTIVE DATE</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>NAME</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>PIC</b></td>
        <td align="center" valign="center" height="30" width="20" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>PIC PHONE</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>PIC EMAIL</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>PROVINCE</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>CITY</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>DISTRICT</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>WARD</b></td>
        <td align="center" valign="center" height="30" width="15" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>POSTAL</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>ADDRESS</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>DESCRIPTION</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>LATITUDE</b></td>
        <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>LONGITUDE</b></td>
        <td align="center" valign="center" height="30" colspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>CREATE WO INSTALLATION</b></td>
    </tr>
    <tr>
        <td align="center" valign="center" height="30" width="13" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>ID</b></td>
        <td align="center" valign="center" height="30" width="30" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>NAME</b></td>
        <td align="center" valign="center" height="30" width="13" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>ID</b></td>
        <td align="center" valign="center" height="30" width="30" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>NAME</b></td>
        <td align="center" valign="center" height="30" width="13" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>ID</b></td>
        <td align="center" valign="center" height="30" width="30" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>NAME</b></td>
        <td align="center" valign="center" height="30" width="20" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>TICKET CUSTOMER</b></td>
        <td align="center" valign="center" height="30" width="40" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>DESCRIPTION</b></td>
    </tr>

    @for($i = 0; $i < 100; $i++)
        <tr>
            <td></td>
            <td></td>
            <td>=VLOOKUP(B{{ $i+5 }},AREA!$A$3:$B${{ $vendors->count() + 3 }}, 2, FALSE)</td>
            <td></td>
            <td>=VLOOKUP(D{{ $i+5 }},CLIENT!$A$3:$B${{ $clients->count() + 3 }}, 2, FALSE)</td>
            <td></td>
            <td>=VLOOKUP(F{{ $i+5 }},SERVICE!$A$3:$B${{ $services->count() + 3 }}, 2, FALSE)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endfor
</table>
