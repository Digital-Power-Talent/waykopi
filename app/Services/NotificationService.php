<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Shipment;

class NotificationService
{
    public function __construct(protected WahaService $wahaService)
    {
    }

    /**
     * Send notification when order is created (Pending Payment).
     */
    public function sendOrderCreated(Order $order): void
    {
        $recipientPhone = $order->recipient_phone ?: $order->guest_phone;
        if (empty($recipientPhone)) {
            return;
        }

        $paymentUrl = route('checkout.payment', ['orderNumber' => $order->order_number]);
        $formattedTotal = number_format((float) $order->total, 0, ',', '.');
        $uniqueCode = sprintf('%03d', $order->unique_code);

        $message = "☕ *WAY KOPI - Pesanan Dibuat*\n\n"
            ."Halo *{$order->recipient_name}*, terima kasih sudah memesan kopi Robusta Lampung!\n\n"
            ."Nomor Pesanan: *{$order->order_number}*\n"
            ."Total Transfer (Tepat): *Rp {$formattedTotal}* (Termasuk Kode Unik: {$uniqueCode})\n\n"
            ."*Rekening Tujuan Transfer (a/n PT GUDANG KITA PERKASA):*\n"
            ."• Bank Mandiri: 1330026414847\n"
            ."• Bank BRI: 207401000502300\n\n"
            ."Rincian lengkap & konfirmasi pesanan:\n"
            ."{$paymentUrl}\n\n"
            ."Atau konfirmasi langsung ke WA CS 6282160388791.\n\n"
            .'Salam hangat, Tim Way Kopi Tanggamus.';

        $success = $this->wahaService->sendTextMessage($recipientPhone, $message);

        NotificationLog::create([
            'order_id' => $order->id,
            'channel' => 'whatsapp',
            'recipient' => $recipientPhone,
            'template_key' => 'order_created',
            'status' => $success ? 'sent' : 'failed',
            'sent_at' => $success ? now() : null,
            'error_reason' => $success ? null : 'Gagal mengirim pesan WA via WAHA',
        ]);
    }

    /**
     * Send notification when order is paid.
     */
    public function sendOrderPaid(Order $order): void
    {
        $recipientPhone = $order->recipient_phone ?: $order->guest_phone;
        $formattedTotal = number_format((float) $order->total, 0, ',', '.');

        if (! empty($recipientPhone)) {
            $customerMsg = "✅ *WAY KOPI - Pembayaran Dikonfirmasi*\n\n"
                ."Halo *{$order->recipient_name}*, pembayaran untuk pesanan *#{$order->order_number}* sebesar *Rp {$formattedTotal}* telah kami terima!\n\n"
                ."Biji kopi pilihan kamu sedang diproses & disangrai oleh tim kami untuk segera dikirim.\n\n"
                .'Terima kasih atas kepercayaannya!';

            $success = $this->wahaService->sendTextMessage($recipientPhone, $customerMsg);

            NotificationLog::create([
                'order_id' => $order->id,
                'channel' => 'whatsapp',
                'recipient' => $recipientPhone,
                'template_key' => 'order_paid_customer',
                'status' => $success ? 'sent' : 'failed',
                'sent_at' => $success ? now() : null,
            ]);
        }

        // Send alert to Admin WA
        $adminPhone = (string) config('services.waha.admin_phone', '');
        if (! empty($adminPhone)) {
            $adminMsg = "🔔 *ADMIN WAY KOPI - Pesanan Baru Lunas!*\n\n"
                ."No. Order: *{$order->order_number}*\n"
                ."Pelanggan: {$order->recipient_name} ({$recipientPhone})\n"
                ."Total: Rp {$formattedTotal}\n"
                ."Kurir: {$order->courier_name}\n"
                ."Alamat: {$order->shipping_address}";

            $adminSuccess = $this->wahaService->sendTextMessage($adminPhone, $adminMsg);

            NotificationLog::create([
                'order_id' => $order->id,
                'channel' => 'whatsapp',
                'recipient' => $adminPhone,
                'template_key' => 'order_paid_admin',
                'status' => $adminSuccess ? 'sent' : 'failed',
                'sent_at' => $adminSuccess ? now() : null,
            ]);
        }
    }

    /**
     * Send notification when order is shipped with tracking number.
     */
    public function sendOrderShipped(Order $order): void
    {
        $recipientPhone = $order->recipient_phone ?: $order->guest_phone;
        if (empty($recipientPhone)) {
            return;
        }

        /** @var Shipment|null $shipment */
        $shipment = $order->shipment;
        $trackingNumber = $shipment && $shipment->tracking_number ? (string) $shipment->tracking_number : 'Akan diupdate';

        $message = "🚚 *WAY KOPI - Pesanan Dikirim*\n\n"
            ."Halo *{$order->recipient_name}*, pesanan *#{$order->order_number}* telah dikirim via *{$order->courier_name}*!\n\n"
            ."Nomor Resi: *{$trackingNumber}*\n\n"
            .'Terima kasih sudah mendukung petani kopi Lampung!';

        $success = $this->wahaService->sendTextMessage($recipientPhone, $message);

        NotificationLog::create([
            'order_id' => $order->id,
            'channel' => 'whatsapp',
            'recipient' => $recipientPhone,
            'template_key' => 'order_shipped',
            'status' => $success ? 'sent' : 'failed',
            'sent_at' => $success ? now() : null,
        ]);
    }
}
