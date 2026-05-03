@php
$pickupDeadline = optional($req->approved_at)->copy()->addDays(2);
$productName = $orderItem->name ?? $orderItem->product->name ?? __('messages.product');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>{{ __('messages.return_approved_subject', ['order_number' => $order->order_number ?? ('#'.$req->order_id)]) }}</title>
</head>

<body style="font-family: Tahoma, Arial, sans-serif; background:#f7f7f7; margin:0; padding:20px;">
    <table width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; margin:auto; background:#fff; border-radius:12px; overflow:hidden;">
        <tr>
            <td style="padding:24px;">
                <h2 style="margin:0 0 12px;">{{ __('messages.hello_name', ['name' => $user->name ?? 'عميلنا العزيز']) }}</h2>
                <p style="margin:0 0 8px;">
                    {{ __('messages.return_approved_line1', ['order_number' => $order->order_number ?? ('#'.$req->order_id)]) }}
                </p>
                <p style="margin:0 0 8px;">
                    {{ __('messages.return_approved_line2') }}
                    @if($pickupDeadline)
                    {{ __('messages.within_days_until', ['date' => $pickupDeadline->format('Y-m-d')]) }}
                    @else
                    {{ __('messages.within_two_business_days') }}
                    @endif
                </p>

                <hr style="border:none; border-top:1px solid #eee; margin:16px 0;">

                <h4 style="margin:0 0 8px;">{{ __('messages.return_summary') }}</h4>
                <ul style="margin:0; padding:0 18px;">
                    <li>{{ __('messages.return_id') }}: #{{ $req->id }}</li>
                    <li>{{ __('messages.product') }}: {{ $productName }}</li>
                    <li>{{ __('messages.quantity') }}: {{ $req->quantity }}</li>
                    @if($req->reason_label)
                    <li>{{ __('messages.reason') }}: {{ $req->reason_label }}</li>
                    @endif
                </ul>

                @if(!empty($req->return_address))
                <h4 style="margin:16px 0 8px;">{{ __('messages.pickup_address') }}</h4>
                <p style="margin:0;">
                    @php $ra = is_array($req->return_address) ? $req->return_address : []; @endphp
                    {{ $ra['name'] ?? '' }} {{ !empty($ra['phone']) ? ' - '.$ra['phone'] : '' }}<br>
                    {{ $ra['city'] ?? '' }}<br>
                    {{ $ra['address1'] ?? ($ra['address'] ?? '') }} {{ !empty($ra['address2']) ? ', '.$ra['address2'] : '' }}
                </p>
                @endif

                <p style="margin:16px 0 0; color:#666; font-size:13px;">
                    {{ __('messages.support_footer') }}
                </p>
            </td>
        </tr>
    </table>
</body>

</html>