<?php

namespace App\Http\Controllers\Web\Vendor\Returns;

use App\Enums\ReturnStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Return\UpdateReturnRequest;
use App\Models\ReturnRequest;
use App\Services\Returns\ReturnRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VendorReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $vendorId    = (int) auth('vendor')->id();
        $search       = trim((string)$request->input('search', ''));
        $status       = $request->input('status');
        $createdFrom  = $request->input('created_from');
        $createdTo    = $request->input('created_to');
        $approvedFrom = $request->input('approved_from');
        $approvedTo   = $request->input('approved_to');
        $receivedFrom = $request->input('received_from');
        $receivedTo   = $request->input('received_to');
        $refundedFrom = $request->input('refunded_from');
        $refundedTo   = $request->input('refunded_to');

        $statusOptions = collect(ReturnStatusEnum::cases())
            ->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])
            ->values();

        $returns = ReturnRequest::query()
            ->forVendor($vendorId)
            ->with([
                'order:id,user_id,delivered_at',
                'order.user:id,name',
                'orderItem',
                'user:id,name',
                'vendor:id,name'
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    if (ctype_digit($search)) {
                        $id = (int)$search;
                        $qq->orWhere('id', $id)
                            ->orWhere('order_id', $id)
                            ->orWhere('order_item_id', $id);
                    }
                    $qq->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('order.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                        ->orWhere('payout_wallet_phone', 'like', "%{$search}%")
                        ->orWhere('reason_text', 'like', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', fn($q) => $q->where('status', (int)$status))
            ->when($createdFrom,  fn($q) => $q->whereDate('created_at', '>=', $createdFrom))
            ->when($createdTo,    fn($q) => $q->whereDate('created_at', '<=', $createdTo))
            ->when($approvedFrom, fn($q) => $q->whereDate('approved_at', '>=', $approvedFrom))
            ->when($approvedTo,   fn($q) => $q->whereDate('approved_at', '<=', $approvedTo))
            ->when($receivedFrom, fn($q) => $q->whereDate('received_at', '>=', $receivedFrom))
            ->when($receivedTo,   fn($q) => $q->whereDate('received_at', '<=', $receivedTo))
            ->when($refundedFrom, fn($q) => $q->whereDate('refunded_at', '>=', $refundedFrom))
            ->when($refundedTo,   fn($q) => $q->whereDate('refunded_at', '<=', $refundedTo))
            ->latest('id')
            ->paginate(20);

        return view('Vendor.pages.returns.index', compact('returns', 'statusOptions'));
    }

    public function show($id)
    {
        $vendorId = (int) auth('vendor')->id();

        $req = ReturnRequest::query()
            ->forVendor($vendorId)
            ->with(['order.user', 'orderItem.product', 'user', 'vendor'])
            ->findOrFail($id);

        return view('Vendor.pages.returns.show', ['req' => $req]);
    }

    public function edit($id)
    {
        $vendorId = (int) auth('vendor')->id();

        $req = ReturnRequest::query()
            ->forVendor($vendorId)
            ->with(['order.user', 'orderItem.product', 'user', 'vendor'])
            ->findOrFail($id);

        return view('Vendor.pages.returns.edit', ['req' => $req]);
    }

    public function update(
        UpdateReturnRequest $request,
        $id,
        ReturnRequestService $service
    ) {
        $vendorId = (int) auth('vendor')->id();

        $return_request = ReturnRequest::query()
            ->forVendor($vendorId)
            ->findOrFail($id);

        $data = $request->validated();

        $oldStatus = $return_request->status;
        $changed   = false;

        if (!empty($data['new_status'])) {
            $new = ReturnStatusEnum::from((int)$data['new_status']);

            if ($new->value !== (int)$oldStatus->value) {
                $approvedAt = !empty($data['approved_at']) ? Carbon::parse($data['approved_at']) : now();
                $receivedAt = !empty($data['received_at']) ? Carbon::parse($data['received_at']) : now();

                switch ($new) {
                    case ReturnStatusEnum::UnderReturn:
                        $service->approveForReturn($return_request, $approvedAt, $vendorId); // 👈 تمرير vendor
                        $changed = true;
                        break;

                    case ReturnStatusEnum::ReceivedGood:
                        $service->decide($return_request, [
                            'type'            => 'approved_intact',
                            'fee_percent'     => $data['restocking_percent'] ?? null,
                            'refund_shipping' => $data['refund_shipping'] ?? 0,
                            'received_at'     => $receivedAt,
                        ], $vendorId);
                        $changed = true;
                        break;

                    case ReturnStatusEnum::ReceivedDefective:
                        $service->decide($return_request, [
                            'type'            => 'approved_defective',
                            'fee_percent'     => $data['restocking_percent'] ?? null,
                            'refund_shipping' => $data['refund_shipping'] ?? 0,
                            'received_at'     => $receivedAt,
                        ], $vendorId);
                        $changed = true;
                        break;

                    case ReturnStatusEnum::Rejected:
                        $service->decide($return_request, [
                            'type'        => 'rejected',
                            'received_at' => $receivedAt,
                        ], $vendorId);
                        $changed = true;
                        break;
                }

                $return_request->refresh();
            }
        }

        $dirty = false;

        if (array_key_exists('approved_at', $data) && !empty($data['approved_at'])) {
            $return_request->approved_at = Carbon::parse($data['approved_at']);
            $dirty = true;
        }
        if (array_key_exists('received_at', $data) && !empty($data['received_at'])) {
            $return_request->received_at = Carbon::parse($data['received_at']);
            $dirty = true;
        }
        if (array_key_exists('refunded_at', $data) && !empty($data['refunded_at'])) {
            // مبدئيًا بنسيب المارك ريفنديد للأدمن/فاينانس، لكن لو عندك صلاحية للفندور سيبه
            $return_request->refunded_at = Carbon::parse($data['refunded_at']);
            $dirty = true;
        }
        if (array_key_exists('payout_wallet_phone', $data)) {
            $return_request->payout_wallet_phone = $data['payout_wallet_phone'] ?: null;
            $dirty = true;
        }
        if (array_key_exists('refund_shipping', $data) && $data['refund_shipping'] !== null) {
            $return_request->refund_shipping = (float)$data['refund_shipping'];
            $sub  = (float)($return_request->refund_subtotal ?? 0);
            $fee  = (float)($return_request->refund_fee ?? 0);
            $ship = (float)$return_request->refund_shipping;
            $return_request->refund_total = max(0, round($sub - $fee - $ship, 2));
            $dirty = true;
        }

        if ($dirty) {
            $return_request->save();
            $changed = true;
        }

        return redirect()
            ->route('vendor.returns.show', $return_request->id)
            ->with('success', $changed ? __('messages.updated_successfully') : __('messages.nothing_changed'));
    }
}
