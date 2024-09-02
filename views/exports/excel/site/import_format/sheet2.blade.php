<table>
    <tr>
        <td style="font-size: 18;"><b>AREA</b></td>
    </tr>
</table>
<table>
    <tr>
        <td align="center" width="12" valign="center" bgcolor="#EEEEEE" style="border: 1px solid #666" height="30"><b>CODE</b></td>
        <td align="center" width="50" valign="center" bgcolor="#EEEEEE" style="border: 1px solid #666"><b>NAME</b></td>
    </tr>
    @foreach($vendors AS $row)
        <tr>
            <td align="center">{{ $row->id }}</td>
            <td>
                {{ strtoupper($row->name) }}
            </td>
        </tr>
    @endforeach
</table>
