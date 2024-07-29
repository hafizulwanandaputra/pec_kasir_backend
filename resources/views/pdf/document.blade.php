<!DOCTYPE html>
<html>

<head>
    <title>KWITANSI PEMBAYARAN No: {{ $transaction['id'] }}</title>
    <style>
        @page {
            margin: 0.25cm;
        }

        body {
            font-family: monospace;
            line-height: 0px;
            font-size: 8px;
        }

        .header {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-0 {
            margin-top: 0;
        }

        table {
            width: 100%;
            line-height: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <p><strong>RUMAH SAKIT MATA PADANG EYE CENTER</strong></p>
        <p>Jl. Pemuda No. 53</p>
        <span>0751-30094</span>
    </div>
    <div class="header">
        <hr>
        <p class="teks-tengah mb-0"><strong>KWITANSI PEMBAYARAN No: {{ $transaction['id'] }}</strong></p>
        <hr>
        <p>Atas Nama:</p>
        <span>{{ $transaction['outpatient']['patient_name'] }}</span>
        <hr>
        <table style="margin-bottom: 5px; line-height: 8px;">
            <tbody style="vertical-align: top;">
                <tr>
                    <td width="45%">No. Registrasi</td>
                    <td width="0%">:</td>
                    <td width="55%">{{ $transaction['outpatient']['no_registration'] }}</td>
                </tr>
                <tr>
                    <td width="45%">Tgl Kunjungan</td>
                    <td width="0%">:</td>
                    <td width="55%">{{ $transaction['outpatient']['date'] }}</td>
                </tr>
                <tr>
                    <td width="45%">Nama Pasien</td>
                    <td width="0%">:</td>
                    <td width="55%">{{ $transaction['outpatient']['patient_name'] }}</td>
                </tr>
                <tr>
                    <td width="45%">Dokter</td>
                    <td width="0%">:</td>
                    <td width="55%">{{ $transaction['outpatient']['patient_name'] }}</td>
                </tr>
                <tr>
                    <td width="45%">Unit</td>
                    <td width="0%">:</td>
                    <td width="55%">{{ $transaction['outpatient']['poli_name'] }}</td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table style="margin-bottom: 5px; line-height: 8px;">
            <thead>
                <th colspan="2" style="text-align: left; border-bottom: 1px dashed black;">
                    Konsultasi dan Tindakan Dokter
                </th>
            </thead>
            <tbody style="vertical-align: top;">
                @foreach ($detailtransaksi as $detailtransaction)
                <tr>
                    <td width="50%">{{ $detailtransaction['service']['name'] }}</td>
                    <td width="50%" style="text-align: right;">Rp{{ number_format($detailtransaction['service']['price'], 0, ',', '.') }} × {{ $detailtransaction['quantity'] }}<br>Rp{{ number_format($detailtransaction['service']['price'] * $detailtransaction['quantity'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <th width="50%" style="text-align: left; border-top: 1px dashed black;">Sub Total:</th>
                    <th width="50%" style="text-align: right; border-top: 1px dashed black;">Rp{{ number_format($totalprice, 0, ',', '.') }}</th>
                </tr>
            </tbody>
        </table>
        <table style="margin-bottom: 5px; line-height: 7px;">
            <thead>
                <th colspan="2" style="text-align: left; border-bottom: 1px dashed black;">
                    Biaya Obat dan Alkes
                </th>
            </thead>
            <tbody>
                <tr>
                    <td width="50%"></td>
                    <td width="50%" style="text-align: right;"></td>
                </tr>
                <tr>
                    <th width="50%" style="text-align: left; border-top: 1px dashed black;">Sub Total Resep:</th>
                    <th width="50%" style="text-align: right; border-top: 1px dashed black;"></th>
                </tr>
            </tbody>
        </table>
        <table style="margin-bottom: 5px; line-height: 8px;">
            <tbody>
                <tr>
                    <td width="50%">Total Tagihan</td>
                    <td width="50%" style="text-align: right;">Rp{{ number_format($totalprice, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td width="50%">Pembayaran</td>
                    <td width="50%" style="text-align: right;">{{ $transaction['payment_methode'] }}</td>
                </tr>
                <tr>
                    <td width="50%">Terima Tunai</td>
                    <td width="50%" style="text-align: right;">Rp{{ number_format($transaction['amount'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td width="50%">Kembali</td>
                    <td width="50%" style="text-align: right;">Rp{{ number_format($transaction['return_amount'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>