<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 28px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .brand-sub {
            color: #64748b;
            font-size: 9px;
            margin-top: 3px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            text-align: right;
            margin: 0;
        }
        .invoice-number {
            font-family: monospace;
            font-size: 12px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
            margin-top: 3px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            margin-top: 4px;
        }
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .status-unpaid {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
        }
        .info-heading {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .info-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .total-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .total-row td {
            padding: 4px 8px;
        }
        .grand-total {
            background-color: #eff6ff;
            border-top: 2px solid #2563eb;
            font-weight: bold;
            font-size: 13px;
            color: #1e3a8a;
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        .signature-box {
            width: 180px;
            text-align: center;
            float: right;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #0f172a;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header / Letterhead -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 55%;">
                <div class="brand-title">RentFleet Pro</div>
                <div class="brand-sub">
                    <strong>PT ARMADA RENT NUSANTARA</strong><br>
                    Jl. Jend. Sudirman Kav. 52-53, SCBD, Jakarta Selatan<br>
                    Telp: (021) 555-8899 &bull; Email: billing@rentfleet.com
                </div>
            </td>
            <td style="width: 45%; text-align: right;">
                <div class="invoice-title">INVOICE RESMI</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div>
                    <span class="status-badge {{ $invoice->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                        STATUS: {{ strtoupper($invoice->payment_status) }}
                    </span>
                </div>
                <div style="color: #64748b; font-size: 9px; margin-top: 4px;">
                    Tanggal Terbit: {{ $invoice->created_at->format('d/m/Y') }}
                    @if($invoice->paid_at)
                        <br><span style="color: #166534; font-weight: bold;">Lunas: {{ $invoice->paid_at->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Section: Customer & Reservation -->
    <table class="info-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-heading">Ditagihkan Kepada (Pelanggan):</div>
                    <div class="info-name">{{ $invoice->reservation->user->name }}</div>
                    <div>Email: {{ $invoice->reservation->user->email }}</div>
                    <div>Telepon: {{ $invoice->reservation->user->phone ?? '-' }}</div>
                    <div>No. SIM Pengemudi: <strong>{{ $invoice->reservation->user->driver_license_number ?? '-' }}</strong></div>
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-heading">Referensi Reservasi Armada:</div>
                    <div class="info-name" style="font-family: monospace; color: #2563eb;">{{ $invoice->reservation->reservation_code }}</div>
                    <div>Armada: <strong>{{ $invoice->reservation->vehicle->brand }} {{ $invoice->reservation->vehicle->model }}</strong></div>
                    <div>Plat Nomor: <strong>{{ $invoice->reservation->vehicle->license_plate }}</strong> ({{ strtoupper($invoice->reservation->vehicle->transmission) }})</div>
                    <div>Periode Sewa: {{ $invoice->reservation->start_date->format('d/m/Y') }} s/d {{ $invoice->reservation->end_date->format('d/m/Y') }} ({{ $invoice->reservation->total_days }} Hari)</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Deskripsi Layanan</th>
                <th style="width: 15%; text-align: center;">Durasi / Satuan</th>
                <th style="width: 20%; text-align: right;">Tarif Satuan</th>
                <th style="width: 20%; text-align: right;">Jumlah (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Sewa Kendaraan: {{ $invoice->reservation->vehicle->full_name }}</strong><br>
                    <span style="color: #64748b; font-size: 9px;">
                        Plat Polisi: {{ $invoice->reservation->vehicle->license_plate }} &bull; Kapasitas: {{ $invoice->reservation->vehicle->capacity }} Kursi
                    </span>
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $invoice->reservation->total_days }} Hari</td>
                <td style="text-align: right;">{{ $invoice->reservation->vehicle->formatted_rate }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $invoice->formatted_subtotal }}</td>
            </tr>

            @if($invoice->fine_amount > 0)
                <tr style="background-color: #fff1f2;">
                    <td>
                        <strong style="color: #9f1239;">Denda Keterlambatan Pengembalian</strong><br>
                        <span style="color: #be123c; font-size: 9px;">
                            Terlambat {{ $invoice->returnRecord->late_days ?? 0 }} hari dari jadwal pengembalian
                        </span>
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #9f1239;">{{ $invoice->returnRecord->late_days ?? 0 }} Hari</td>
                    <td style="text-align: right; color: #9f1239;">Rp {{ number_format($invoice->returnRecord->fine_per_day ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold; color: #9f1239;">{{ $invoice->formatted_fine_amount }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Totals Table -->
    <table class="total-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px;">
                    <strong style="font-size: 9px; text-transform: uppercase; color: #64748b;">Instruksi Pembayaran:</strong>
                    <div style="margin-top: 4px;">Bank Central Asia (BCA): <strong>8829-0192-33</strong></div>
                    <div>Atas Nama: <strong>PT Armada Rent Nusantara</strong></div>
                    <div style="color: #64748b; font-size: 8px; margin-top: 3px;">
                        * Simpan bukti pembayaran resmi ini sebagai bukti sah transaksi rental kendaraan.
                    </div>
                </div>
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 40%; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr class="total-row">
                        <td style="color: #64748b;">Subtotal Sewa:</td>
                        <td style="text-align: right; font-weight: bold;">{{ $invoice->formatted_subtotal }}</td>
                    </tr>
                    <tr class="total-row">
                        <td style="color: #64748b;">Denda Keterlambatan:</td>
                        <td style="text-align: right; font-weight: bold; color: {{ $invoice->fine_amount > 0 ? '#9f1239' : '#0f172a' }};">
                            {{ $invoice->fine_amount > 0 ? $invoice->formatted_fine_amount : 'Rp 0' }}
                        </td>
                    </tr>
                    <tr class="grand-total">
                        <td style="padding: 8px;">GRAND TOTAL:</td>
                        <td style="padding: 8px; text-align: right; font-size: 13px;">{{ $invoice->formatted_grand_total }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signature / Authorization Box -->
    <table class="footer-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60%; vertical-align: bottom; color: #94a3b8; font-size: 8px;">
                Dokumen ini digenerate secara otomatis oleh Sistem Informasi RentFleet Pro.<br>
                Validasi keaslian invoice tercatat di database sentral: {{ $invoice->invoice_number }}
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div class="signature-box">
                    <div>Jakarta, {{ $invoice->created_at->format('d F Y') }}</div>
                    <div style="font-weight: bold; margin-top: 2px;">Finance & Operational Dept</div>
                    <div class="signature-line">PT Armada Rent Nusantara</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
