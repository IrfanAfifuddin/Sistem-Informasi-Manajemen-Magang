<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Nilai Anak Magang</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #1e1b4b;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #4f46e5;
            font-weight: bold;
        }
        .date {
            text-align: right;
            margin-bottom: 15px;
            font-style: italic;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .score {
            font-size: 13px;
            color: #4f46e5;
            font-weight: bold;
        }
        .status-badge {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            display: inline-block;
        }
        .status-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-gray {
            background-color: #f3f4f6;
            color: #374151;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 11px;
        }
        .signature-space {
            margin-top: 60px;
            border-top: 1px solid #333;
            width: 200px;
            display: inline-block;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Sistem Pencatatan & Evaluasi Magang</h2>
        <p>Laporan Rekapitulasi Nilai Akhir Anak Magang</p>
    </div>

    <div class="date">
        Tanggal Ekspor: {{ date('d F Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%">NIM</th>
                <th style="width: 20%">Nama Anak Magang</th>
                <th style="width: 20%">Universitas / Jurusan</th>
                <th style="width: 15%">Mentor Pendamping</th>
                <th style="width: 10%; text-align: center;">Tugas Dinilai</th>
                <th style="width: 10%; text-align: center;">Rerata Nilai</th>
                <th style="width: 10%; text-align: center;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($interns as $intern)
                <tr>
                    <td class="font-bold">{{ $intern->nim }}</td>
                    <td>{{ $intern->name }}</td>
                    <td>
                        {{ $intern->university }}<br>
                        <span style="color: #666; font-size: 10px;">{{ $intern->major }}</span>
                    </td>
                    <td>{{ $intern->mentor_name }}</td>
                    <td class="text-center font-bold">{{ $intern->graded_count }}</td>
                    <td class="text-center score">{{ $intern->average_score }}</td>
                    <td class="text-center">
                        @if($intern->graded_count > 0)
                            @if($intern->average_score >= 75)
                                <span class="status-badge status-success">{{ $intern->grade_status }}</span>
                            @elseif($intern->average_score >= 60)
                                <span class="status-badge status-warning">{{ $intern->grade_status }}</span>
                            @else
                                <span class="status-badge status-danger">{{ $intern->grade_status }}</span>
                            @endif
                        @else
                            <span class="status-badge status-gray">Belum Ada Nilai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #666;">Belum ada data anak magang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Disetujui Oleh,</p>
        <div style="height: 60px;"></div>
        <div class="signature-space">
            Administrator Sistem
        </div>
    </div>

</body>
</html>
