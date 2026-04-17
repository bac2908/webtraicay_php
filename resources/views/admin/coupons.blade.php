@extends('layouts.admin')

@section('title', 'Quan ly coupon | FruitShop Admin')

@section('head')
    <style>
        .campaign-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .campaign {
            border: 1px solid #dfd9c8;
            background: linear-gradient(145deg, #fff7e7, #fffdf7);
            border-radius: 12px;
            padding: 12px;
        }

        .campaign h4 {
            margin: 0;
            font-size: 14px;
            font-family: 'Sora', sans-serif;
        }

        .campaign p {
            margin: 6px 0 0;
            color: #6d725f;
            font-size: 12px;
        }

        @media (max-width: 1000px) {
            .campaign-strip {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('admin_content')
    <section class="page-head reveal" style="--delay: 0ms;">
        <div>
            <h1 class="page-title">Quan ly coupon</h1>
            <p class="page-subtitle">Toi uu khuyen mai theo giai doan campaign va hanh vi mua hang.</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button class="btn btn-ghost"><i class="ri-file-copy-2-line"></i>Clone campaign</button>
            <button class="btn btn-primary"><i class="ri-ticket-2-line"></i>Tao coupon moi</button>
        </div>
    </section>

    <section class="panel reveal" style="--delay: 80ms; margin-bottom: 14px;">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Campaign center</h2>
                <p class="panel-sub">Quan ly coupon theo mua vu, su kien va nhom khach muc tieu.</p>
            </div>
            <span class="tag">Promo ops</span>
        </div>

        <div class="campaign-strip">
            <div class="campaign">
                <h4>New user boost</h4>
                <p>Coupon cho khach mua lan dau, theo doi ROI va repeat rate.</p>
            </div>
            <div class="campaign">
                <h4>Flash sale gio vang</h4>
                <p>Khuyen mai ngan han de day nhanh ton kho ton dong.</p>
            </div>
            <div class="campaign">
                <h4>Retention 30 ngay</h4>
                <p>Gui ma uu dai cho khach da 30 ngay chua quay lai mua.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Tieu de</th>
                    <th>Loai</th>
                    <th>Gia tri</th>
                    <th>Don toi thieu</th>
                    <th>Hieu luc</th>
                    <th>Trang thai</th>
                </tr>
                </thead>
                <tbody>
                @forelse(($coupons ?? []) as $coupon)
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>{{ $coupon->title }}</td>
                        <td>{{ strtoupper($coupon->type) }}</td>
                        <td>
                            @if($coupon->type === 'percent')
                                {{ (int) $coupon->value }}%
                            @elseif($coupon->type === 'fixed')
                                {{ number_format((int) $coupon->value) }} VND
                            @else
                                QUA TANG
                            @endif
                        </td>
                        <td>{{ $coupon->min_order_total ? number_format((int) $coupon->min_order_total) . ' VND' : '-' }}</td>
                        <td>
                            {{ optional($coupon->starts_at)->format('d/m') ?? '--' }}
                            -
                            {{ optional($coupon->ends_at)->format('d/m') ?? '--' }}
                        </td>
                        <td><span class="status-pill {{ $coupon->is_active ? 'done' : 'cancelled' }}">{{ $coupon->is_active ? 'ACTIVE' : 'OFF' }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-box">
                                <i class="ri-coupon-3-line"></i>
                                <div>Danh sach coupon se duoc nap tu MySQL trong pha ket noi BE.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
