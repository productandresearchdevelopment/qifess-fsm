<table>
  <tr>
    <td style="font-size: 18;"><b>IMPORT TEAM</b></td>
  </tr>
</table>

<table>
  <tr>
    <td align="center" valign="center" height="30" colspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666">
      <b>AREA</b>
    </td>
    <td align="center" valign="center" height="30" width="15" rowspan="2" bgcolor="#EEEEEE"
      style="border: 1px solid #666"><b>NIK</b></td>
    <td align="center" valign="center" height="30" width="15" rowspan="2" bgcolor="#EEEEEE"
      style="border: 1px solid #666"><b>NAME</b></td>
    <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE"
      style="border: 1px solid #666"><b>ADDRESS</b></td>
    <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE"
      style="border: 1px solid #666"><b>EMAIL</b></td>
    <td align="center" valign="center" height="30" colspan="2" bgcolor="#EEEEEE" style="border: 1px solid #666">
      <b>FIELDTECH</b>
    </td>
    <td align="center" valign="center" height="30" rowspan="2" width="30" bgcolor="#EEEEEE"
      style="border: 1px solid #666">
      <b>VENDOR NAME</b>
    </td>
    <td align="center" valign="center" height="30" width="30" rowspan="2" bgcolor="#EEEEEE"
      style="border: 1px solid #666"><b>PHONE</b></td>
  </tr>

  <tr>
    <td align="center" valign="center" height="30" width="13" bgcolor="#EEEEEE" style="border: 1px solid #666">
      <b>ID</b>
    </td>
    <td align="center" valign="center" height="30" width="30" bgcolor="#EEEEEE" style="border: 1px solid #666">
      <b>NAME</b>
    </td>

    <td align="center" valign="center" height="30" width="30" bgcolor="#EEEEEE" style="border: 1px solid #666">
      <b>SATU</b>
    </td>
    <td align="center" valign="center" height="30" width="30" bgcolor="#EEEEEE" style="border: 1px solid #666">
      <b>DUA</b>
    </td>
  </tr>

  @for ($i = 0; $i < 100; $i++)
    <tr>
      <td></td>
      <td>=VLOOKUP(A{{ $i + 5 }},AREA!$A$3:$B${{ $vendors->count() + 3 }}, 2, FALSE)</td>
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
