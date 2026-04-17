@extends('layouts.admin')

@section('title', 'Bao cao kinh doanh | FruitShop Admin')

@section('head')
    <style>
        .report-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .heatmap {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
        }

        .heat-cell {
            border-radius: 10px;
            border: 1px solid #d8e4d8;
            min-height: 56px;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 600;
            color: #335141;
            background: #f6faf5;
        }

        .heat-cell.lv1 { background: #edf7ef; }
        .heat-cell.lv2 { background: #d8f0df; }
        .heat-cell.lv3 { background: #bce7c9; }
        .heat-cell.lv4 { background: #94d8ab; }

        @media (max-width: 1040px) {
            .report-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('admin_content')
    <section class="page-head reveal" style="--delay: 0ms;">
        <div>
            <h1 class="page-title">Bao cao kinh doanh</h1>
            <p class="page-subtitle">Theo doi doanh thu, bien loi nhuan va hieu qua theo kenh/san pham.</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button class="btn btn-ghost"><i class="ri-calendar-check-line"></i>Chon ky bao cao</button>
            <button class="btn btn-primary"><i class="ri-file-excel-2-line"></i>Xuat file</button>
        </div>
    </section>

    <section class="report-grid">
        <article class="panel reveal" style="--delay: 80ms;">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Doanh thu theo kenh</h2>
                    <p class="panel-sub">Theo doi ti trong kenh va muc dong gop doanh thu.</p>
                </div>
                <span class="tag">Attribution view</span>
            </div>

            <div class="stack">
                <div class="metric-line">
                    <div class="metric-line-head"><span>Organic Search</span><strong>{{ $report['organic'] ?? '--' }}</strong></div>
                    <div class="progress"><span style="width: 68%;"></span></div>
                </div>
                <div class="metric-line">
                    <div class="metric-line-head"><span>Paid Social</span><strong>{{ $report['paid_social'] ?? '--' }}</strong></div>
                    <div class="progress"><span style="width: 44%;"></span></div>
                </div>
                <div class="metric-line">
                    <div class="metric-line-head"><span>CRM / Email</span><strong>{{ $report['crm'] ?? '--' }}</strong></div>
                    <div class="progress"><span style="width: 37%;"></span></div>
                </div>
                <div class="metric-line">
                    <div class="metric-line-head"><span>Affiliate</span><strong>{{ $report['affiliate'] ?? '--' }}</strong></div>
                    <div class="progress"><span style="width: 23%;"></span></div>
                </div>
            </div>
        </article>

        <article class="panel reveal" style="--delay: 130ms;">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Heatmap khung gio</h2>
                    <p class="panel-sub">Khung gio co nhieu don nhat trong tuan.</p>
                </div>
            </div>

            <div class="heatmap">
                <div class="heat-cell lv1">08:00</div>
                <div class="heat-cell lv2">09:00</div>
                <div class="heat-cell lv1">10:00</div>
                <div class="heat-cell lv3">11:00</div>
                <div class="heat-cell lv4">12:00</div>
                <div class="heat-cell lv3">13:00</div>
                <div class="heat-cell lv2">14:00</div>
                <div class="heat-cell lv1">15:00</div>
                <div class="heat-cell lv2">16:00</div>
                <div class="heat-cell lv3">17:00</div>
                <div class="heat-cell lv4">18:00</div>
                <div class="heat-cell lv3">19:00</div>
                <div class="heat-cell lv2">20:00</div>
                <div class="heat-cell lv1">21:00</div>
            </div>
        </article>
    </section>

    <section class="panel reveal" style="--delay: 180ms;">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Top danh muc / san pham</h2>
                <p class="panel-sub">Danh sach top seller theo doanh thu va bien loi nhuan.</p>
            </div>
            <span class="tag">P&L ready</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Hang muc</th>
                    <th>Doanh thu</th>
                    <th>So don</th>
                    <th>AOV</th>
                    <th>Tang truong</th>
                </tr>
                </thead>
                <tbody>
                @forelse(($reportRows ?? []) as $row)
                    <tr>
                        <td><strong>{{ $row['name'] ?? '-' }}</strong></td>
                        <td>{{ isset($row['revenue']) ? number_format((int) $row['revenue']) . ' VND' : '--' }}</td>
                        <td>{{ $row['orders'] ?? '--' }}</td>
                        <td>{{ isset($row['aov']) ? number_format((int) $row['aov']) . ' VND' : '--' }}</td>
                        <td>{{ $row['growth'] ?? '--' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-box">
                                <i class="ri-line-chart-line"></i>
                                <div>Module report FE da san sang. Du lieu se lay truc tiep MySQL trong pha BE.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
