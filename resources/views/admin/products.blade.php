@extends('layouts.admin')

@section('title', 'Quan ly san pham | FruitShop Admin')

@section('head')
    <style>
        .filter-grid {
            display: grid;
            grid-template-columns: 1.3fr repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .field {
            display: grid;
            gap: 6px;
        }

        .field label {
            font-size: 12px;
            color: #5f7368;
            font-weight: 600;
        }

        .input,
        .select {
            width: 100%;
            border: 1px solid #d6dfd4;
            border-radius: 11px;
            padding: 10px 11px;
            font-size: 13px;
            font-family: inherit;
            background: #fff;
            color: #1a3729;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-thumb {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            border: 1px solid #dce6da;
            background: #f4f8f2;
            display: grid;
            place-items: center;
            color: #597264;
            font-size: 18px;
        }

        .product-meta strong {
            display: block;
            font-size: 13px;
        }

        .product-meta span {
            color: #5f7368;
            font-size: 12px;
        }

        .hero-note {
            border: 1px dashed #c5d6c8;
            border-radius: 12px;
            padding: 10px 12px;
            color: #42594b;
            font-size: 13px;
            background: #f8fbf7;
        }

        @media (max-width: 1080px) {
            .filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('admin_content')
    @php
        $productsData = collect($products ?? []);
    @endphp

    <section class="page-head reveal" style="--delay: 0ms;">
        <div>
            <h1 class="page-title">Quan ly san pham</h1>
            <p class="page-subtitle">Quan ly catalog, gia ban, ton kho va trang thai hien thi tren storefront.</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button class="btn btn-ghost"><i class="ri-upload-cloud-2-line"></i>Import</button>
            <button class="btn btn-primary"><i class="ri-add-line"></i>Them san pham</button>
        </div>
    </section>

    <section class="panel reveal" style="--delay: 80ms; margin-bottom: 14px;">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Bo loc va tim kiem</h2>
                <p class="panel-sub">Loc nhanh theo danh muc, ton kho, trang thai va gia ban.</p>
            </div>
        </div>

        <form class="filter-grid" method="get" action="javascript:void(0)">
            <div class="field">
                <label>Tim theo ten/sku</label>
                <input class="input" type="text" placeholder="Vi du: nho xanh, tao Envy...">
            </div>
            <div class="field">
                <label>Danh muc</label>
                <select class="select">
                    <option>Tat ca danh muc</option>
                    <option>Trai cay nhap khau</option>
                    <option>Combo qua tuoi</option>
                </select>
            </div>
            <div class="field">
                <label>Trang thai</label>
                <select class="select">
                    <option>Tat ca trang thai</option>
                    <option>Dang hien thi</option>
                    <option>Tam an</option>
                </select>
            </div>
            <div class="field">
                <label>Ton kho</label>
                <select class="select">
                    <option>Tat ca</option>
                    <option>Sap het hang (&lt; 10)</option>
                    <option>Con hang</option>
                    <option>Het hang</option>
                </select>
            </div>
        </form>
    </section>

    <section class="grid-2" style="grid-template-columns: 1.6fr .9fr;">
        <article class="panel reveal" style="--delay: 140ms;">
            <div class="toolbar">
                <div class="toolbar-left">
                    <button class="btn btn-ghost"><i class="ri-checkbox-multiple-line"></i>Chon tat ca</button>
                    <button class="btn btn-ghost"><i class="ri-edit-2-line"></i>Sua hang loat</button>
                    <button class="btn btn-ghost"><i class="ri-delete-bin-6-line"></i>An san pham</button>
                </div>
                <span class="tag">Bang san pham</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>San pham</th>
                        <th>Danh muc</th>
                        <th>Gia</th>
                        <th>Ton kho</th>
                        <th>Trang thai</th>
                        <th>Cap nhat</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($productsData->isNotEmpty())
                        @foreach($productsData as $product)
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumb"><i class="ri-image-line"></i></div>
                                        <div class="product-meta">
                                            <strong>{{ $product->name }}</strong>
                                            <span>{{ $product->slug }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name ?? 'Chua phan loai' }}</td>
                                <td>{{ number_format((int) ($product->sale_price ?? $product->price)) }} VND</td>
                                <td>{{ (int) $product->stock }} {{ $product->unit ?? 'sp' }}</td>
                                <td>
                                    <span class="status-pill {{ $product->is_active ? 'done' : 'cancelled' }}">
                                        {{ $product->is_active ? 'ACTIVE' : 'HIDDEN' }}
                                    </span>
                                </td>
                                <td>{{ optional($product->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">
                                <div class="empty-box">
                                    <i class="ri-layout-grid-line"></i>
                                    <div>Chua ket noi danh sach san pham tu MySQL. FE table da san sang map du lieu.</div>
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel reveal" style="--delay: 210ms;">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Tac vu nhanh</h2>
                    <p class="panel-sub">Workflow thuong dung cho team merchandising.</p>
                </div>
            </div>

            <div class="stack">
                <button class="btn btn-accent" style="justify-content:flex-start;"><i class="ri-price-tag-3-line"></i>Cap nhat gia theo danh muc</button>
                <button class="btn btn-ghost" style="justify-content:flex-start;"><i class="ri-archive-line"></i>Dong bo ton kho tu kho tong</button>
                <button class="btn btn-ghost" style="justify-content:flex-start;"><i class="ri-image-2-line"></i>Kiem tra anh dai dien thieu</button>
                <button class="btn btn-ghost" style="justify-content:flex-start;"><i class="ri-link"></i>Rang buoc san pham lien quan</button>
            </div>

            <div class="hero-note" style="margin-top: 12px; margin-bottom: 0;">
                Module nay da hoan thien bo cuc FE cho CRUD, bo loc va thao tac hang loat. Buoc tiep theo la ket noi data provider tu model Product/Category.
            </div>
        </article>
    </section>
@endsection
