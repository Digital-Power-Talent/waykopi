<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Pengiriman #{{ $order->order_number }} — Way Kopi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .shipping-card {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4 font-mono text-xs text-gray-900">

    <!-- Print / Close Controls -->
    <div class="max-w-2xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded font-bold hover:bg-gray-700 transition">
            &larr; Kembali ke Kelola Pesanan
        </a>
        <button onclick="window.print()" class="px-6 py-2.5 bg-amber-500 text-black font-extrabold rounded hover:bg-amber-400 shadow-md transition flex items-center gap-2">
            🖨️ Cetak / Download PDF Resi
        </button>
    </div>

    <!-- Shipping Label Card -->
    <div class="max-w-2xl mx-auto bg-white border-2 border-black rounded-lg shadow-xl overflow-hidden shipping-card">
        
        <!-- Header toko & Courier -->
        <div class="border-b-2 border-black p-4 bg-amber-50 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-black text-amber-400 font-extrabold flex items-center justify-center text-lg rounded-full border border-black">
                    WK
                </div>
                <div>
                    <h1 class="font-extrabold text-base tracking-wider uppercase">WAY KOPI ROASTERY</h1>
                    <p class="text-[10px] text-gray-600">Spesialis Kopi Robusta & Arabika Tanggamus Lampung</p>
                </div>
            </div>

            <div class="text-right">
                <span class="px-3 py-1 bg-black text-white font-extrabold text-sm uppercase rounded tracking-widest block">
                    {{ $order->courier_name }}
                </span>
                @if($order->shipment?->tracking_number)
                    <span class="text-[11px] font-bold text-gray-800 mt-1 block">Resi: {{ $order->shipment->tracking_number }}</span>
                @endif
            </div>
        </div>

        <!-- Order & Courier Barcode Header -->
        <div class="border-b-2 border-black p-4 bg-white text-center space-y-2">
            <span class="text-[10px] text-gray-500 uppercase font-extrabold tracking-wider block">SCAN BARCODE KURIR (CODE128)</span>
            <div class="flex justify-center bg-white py-1">
                <svg id="barcode"></svg>
            </div>
            <div class="flex items-center justify-between text-xs border-t border-gray-300 pt-2 px-2">
                <div>
                    <span class="text-[10px] text-gray-500 block uppercase font-bold">No. Pesanan</span>
                    <span class="font-extrabold text-sm text-black tracking-wide">#{{ $order->order_number }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-500 block uppercase font-bold">Tanggal Pesan</span>
                    <span class="font-bold text-xs text-gray-800">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-500 block uppercase font-bold">Berat Total</span>
                    @php
                        $totalWeightGrams = $order->items->sum(fn($i) => ($i->productVariant?->weight_grams ?? 200) * $i->quantity);
                    @endphp
                    <span class="font-bold text-xs text-gray-800">{{ number_format($totalWeightGrams / 1000, 2, ',', '.') }} kg</span>
                </div>
            </div>
        </div>

        <!-- COD or Payment Method Banner -->
        @php
            $paymentMethod = $order->payment?->method ?? ($order->status === 'paid' ? 'bank_transfer' : 'cod');
        @endphp
        @if($paymentMethod === 'cod' || $order->status === 'processing')
            <div class="bg-amber-400 text-black p-3 border-b-2 border-black text-center font-extrabold text-sm uppercase tracking-wide">
                ⚠️ PESANAN COD (BAYAR DI TEMPAT) — WAJIB TAGIH TUNAI: Rp {{ number_format($order->total, 0, ',', '.') }}
            </div>
        @else
            <div class="bg-emerald-700 text-white p-2.5 border-b-2 border-black text-center font-bold text-xs uppercase tracking-wider">
                ✓ LUNAS — PEMBAYARAN TRANSFER BANK (RP {{ number_format($order->total, 0, ',', '.') }})
            </div>
        @endif

        <!-- Sender & Recipient Grid -->
        <div class="grid grid-cols-2 divide-x-2 divide-black border-b-2 border-black">
            <!-- PENGIRIM -->
            <div class="p-4 space-y-1">
                <span class="text-[10px] uppercase font-extrabold text-gray-500 block underline">PENGIRIM (FROM):</span>
                <p class="font-extrabold text-sm">WAY KOPI ROASTERY</p>
                <p class="text-[11px] text-gray-700">0821-6038-8791</p>
                <p class="text-[11px] text-gray-700 leading-tight">
                    Kantor Way Kopi, Perumahan Greenwood, Jl. Kalisuren, Desa Kalisuren, Kec. Tajurhalang, Kab. Bogor
                </p>
            </div>

            <!-- PENERIMA -->
            <div class="p-4 space-y-1 bg-yellow-50/40">
                <span class="text-[10px] uppercase font-extrabold text-gray-500 block underline">PENERIMA (TO):</span>
                <p class="font-extrabold text-base text-black uppercase">{{ $order->recipient_name }}</p>
                <p class="font-bold text-xs text-gray-900">{{ $order->recipient_phone }}</p>
                <p class="text-[11px] text-gray-800 font-medium leading-normal mt-1">
                    {{ $order->shipping_address }}
                </p>
            </div>
        </div>

        <!-- Notes if any -->
        @if($order->notes)
            <div class="p-3 bg-amber-50 border-b-2 border-black text-xs">
                <span class="font-extrabold uppercase text-[10px] text-amber-800">Catatan Pembeli:</span>
                <p class="italic text-gray-800 mt-0.5">"{{ $order->notes }}"</p>
            </div>
        @endif

        <!-- Item Table -->
        <div class="p-4 space-y-3">
            <span class="text-[10px] uppercase font-extrabold text-gray-500 block">DAFTAR ISI PAKET KOPI:</span>
            <table class="w-full text-left border-collapse border border-black text-xs">
                <thead>
                    <tr class="bg-gray-200 border-b border-black font-bold uppercase text-[10px]">
                        <th class="p-2 border-r border-black">No</th>
                        <th class="p-2 border-r border-black">Nama Produk & Varian</th>
                        <th class="p-2 border-r border-black text-center">Qty</th>
                        <th class="p-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black">
                    @foreach($order->items as $idx => $item)
                        <tr>
                            <td class="p-2 border-r border-black font-bold text-center">{{ $idx + 1 }}</td>
                            <td class="p-2 border-r border-black">
                                <span class="font-extrabold text-gray-900 block">{{ $item->product_name }}</span>
                                <span class="text-[10px] text-gray-600">{{ $item->variant_label }}</span>
                            </td>
                            <td class="p-2 border-r border-black text-center font-extrabold text-sm">{{ $item->quantity }}</td>
                            <td class="p-2 text-right font-bold">Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Totals -->
        <div class="border-t-2 border-black p-4 bg-gray-50 flex items-center justify-between text-xs font-bold">
            <div class="text-[10px] text-gray-500">
                <span>Terima kasih telah berbelanja di Way Kopi Tanggamus!</span>
            </div>
            <div class="text-right space-y-0.5">
                <div class="flex justify-between gap-6 text-gray-600 text-[11px]">
                    <span>Subtotal Produk:</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-6 text-gray-600 text-[11px]">
                    <span>Ongkos Kirim:</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-6 text-base text-black font-extrabold pt-1 border-t border-gray-400">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var barcodeValue = "{{ $order->shipment?->tracking_number ?: $order->order_number }}";
            JsBarcode("#barcode", barcodeValue, {
                format: "CODE128",
                lineColor: "#000000",
                width: 2,
                height: 55,
                displayValue: true,
                fontOptions: "bold",
                fontSize: 14,
                textMargin: 4
            });
        });
    </script>
</body>
</html>
