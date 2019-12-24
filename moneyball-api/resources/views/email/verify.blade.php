<html>
<body style="background:#2a4565; ">
<div>
    <u></u>
    <div style="margin:0;padding:0">
        <table border="0" cellpadding="0" cellspacing="0" lang="en" style="min-width:348px" width="100%">
            <tbody>
                <tr height="32" style="height:32px">
                    <td></td>
                </tr>
                <tr align="center">
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom:20px;max-width:516px;min-width:220px">
                            <tbody>
                                <tr>
                                    <td style="width:8px" width="8"></td>
                                    <td align="center">
                                        <img src="{{ url('/') }}/img/ico_logo.png" style="margin-bottom:16px" width="180">
                                    </td>
                                    <td style="width:8px" width="8"></td>
                                </tr>
                                <tr>
                                    <td style="width:8px" width="8"></td>
                                    <td>
                                        <div align="center" style="width:360px; background:#ffffff;border-style:solid;border-width:thin;border-color:#dadce0;border-radius:8px;padding:40px 20px">
                                            <div style="font-family:'Google Sans',Roboto,RobotoDraft,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.87);line-height:32px;padding-bottom:24px;text-align:center;word-break:break-word">
                                                <div style="font-size:24px">Authorization Code</div>
                                                <table align="center" style="margin-top:8px">
                                                    <tbody>
                                                        <tr style="line-height:normal">
                                                            <td align="right" style="padding-right:8px">
                                                                <div style="font-size:18px">Your authorization code is <b>{{ $token }}</b></div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width:8px" width="8"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr height="32" style="height:32px">
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>