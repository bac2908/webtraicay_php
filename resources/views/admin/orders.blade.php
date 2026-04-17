@extends('layouts.admin')

@section('title', 'Quan ly don hang | FruitShop Admin')

@section('head')
    <style>
        .status-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .status-card {
            border: 1px solid #dae5d9;
            border-radius: 12px;
            background: #fff;
            padding: 12px;
        }

        .status-card small {
            color: #5f7368;
            display: block;
            margin-bottom: 6px;
        }

        .status-card strong {
            font-family: 'Sora', sans-serif;
            font-size: 24px;
            line-height: 1.1;
        }

        .status-card.pending strong {
            color: #936c00;
        }

        .status-card.confirmed strong {
            color: #2d7040;
        }

        .status-card.shipping strong {
            color: #1d6b90;
        }

        .status-card.done strong {
            color: #255332;
        }

        .status-card.cancelled strong {
            color: #9a3e2c;
        }

        .chip-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .chip-filter button {
            border: 1px solid #d4dfd1;
            border-radius: 999px;
            background: #fff;
            color: #284434;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            padding: 7px 11px;
            cursor: pointer;
        }

        .chip-filter button.active {
            background: #e8f6ee;
            color: #1f7a4a;
            border-color: #8dc9a7;
        }

        @media (max-width: 1150px) {
            .status-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .status-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
@endsection

@section('admin_content')
    <section class="page-head reveal" style="--delay: 0ms;">
        <div>
            <h1 class="page-title">Quan ly don hang</h1>
            <p class="page-subtitle">Theo doi trang thai xu ly, giao van va hieu suat fulfillment.</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button class="btn btn-ghost"><i class="ri-printer-line"></i>In danh sach</button>
            <button class="btn btn-primary"><i class="ri-shield-check-line"></i>Doi soat thanh toan</button>
        </div>
    </section>

    <section class="panel reveal" style="--delay: 90ms; margin-bottom: 14px;">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Trang thai don hang</h2>
                <p class="panel-sub">Snapshot trang thai theo pipeline xu ly trong ngay.</p>
            </div>
            <span class="tag">Operation pulse</span>
        </div>

        <div class="status-grid">
            <div class="status-card pending">
                <small>Pending</small>
                <strong>{{ $orderSummary['pending'] ?? '--' }}</strong>
            </div>
            <div class="status-card confirmed">
                <small>Confirmed</small>
                <strong>{{ $orderSummary['confirmed'] ?? '--' }}</strong>
            </div>
            <div class="status-card shipping">
                <small>Shipping</small>
                <strong>{{ $orderSummary['shipping'] ?? '--' }}</strong>
            </div>
            <div class="status-card done">
                <small>Done</small>
                <strong>{{ $orderSummary['done'] ?? '--' }}</strong>
            </div>
            <div class="status-card cancelled">
                <small>Cancelled</small>
                <strong>{{ $orderSummary['cancelled'] ?? '--' }}</strong>
            </div>
        </div>

        <div class="chip-filter">
            <button type="button" class="active">Tat ca</button>
            <button type="button">Can xac nhan</button>
            <button type="button">Can giao gap</button>
            <button type="button">Rui ro hoan hang</button>
            <button type="button">COD lon hon 2 trieu</button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Ma don</th>
                    <th>Khach hang</th>
                    <th>Lien he</th>
                    <th>Gia tri</th>
                    <th>Coupon</th>
                    <th>Trang thai</th>
                    <th>Tao luc</th>
                </tr>
                </thead>
                <tbody>
                @forelse(($orders ?? []) as $order)
                    <tr>
                        <td><strong>{{ $order->code }}</strong></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone ?? $order->customer_email ?? '-' }}</td>
                        <td>{{ number_format((int) $order->total) }} VND</td>
                        <td>{{ $order->coupon_code ?? '-' }}</td>
                        <td><span class="status-pill {{ $order->status }}">{{ strtoupper($order->status) }}</span></td>
                        <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-box">
                                <i class="ri-truck-line"></i>
                                <div>Bang don hang da hoan tat FE. Se do du lieu that tu MySQL sau khi sang pha BE.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
