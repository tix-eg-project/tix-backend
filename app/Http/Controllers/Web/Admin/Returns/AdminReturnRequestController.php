<?php

namespace App\Http\Controllers\Web\Admin\Returns;

use App\Enums\ReturnStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Return\UpdateReturnRequest;
use App\Models\ReturnRequest;
use App\Services\Returns\ReturnRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $search       = trim((string)$request->input('search', ''));
        $status       = $request->input('status');              // 1..4,6 حسب ReturnStatusEnum (بعد الحذف)
        $createdFrom  = $request->input('created_from');        // Y-m-d
        $createdTo    = $request->input('created_to');          // Y-m-d
        $approvedFrom = $request->input('approved_from');       // Y-m-d
        $approvedTo   = $request->input('approved_to');         // Y-m-d
        $receivedFrom = $request->input('received_from');       // Y-m-d
        $receivedTo   = $request->input('received_to');         // Y-m-d
        $refundedFrom = $request->input('refunded_from');       // Y-m-d
        $refundedTo   = $request->input('refunded_to');         // Y-m-d

        $statusOptions = collect(ReturnStatusEnum::cases())
            ->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])
            ->values();

        $returns = ReturnRequest::query()
            ->with(['order:id,user_id,delivered_at', 'order.user:id,name', 'orderItem', 'user:id,name', 'vendor:id,name'])
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

        return view('Admin.pages.returns.index', compact('returns', 'statusOptions'));
    }

    public function show(ReturnRequest $return_request)
    {
        $return_request->loadMissing([
            'order.user',
            'orderItem.product',
            'user',
            'vendor',
        ]);

        return view('Admin.pages.returns.show', ['req' => $return_request]);
    }

    public function edit(ReturnRequest $return_request)
    {
        $return_request->loadMissing([
            'order.user',
            'orderItem.product',
            'user',
            'vendor',
        ]);

        return view('Admin.pages.returns.edit', ['req' => $return_request]);
    }

    public function update(
        UpdateReturnRequest $request,
       ReturnRequest $return_request,
        ReturnRequestService $service
    ) {
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
                        $service->approveForReturn($return_request, $approvedAt);
                        $changed = true;
                        break;

                    case ReturnStatusEnum::ReceivedGood:
                        $service->decide($return_request, [
                            'type'            => 'approved_intact',
                            'fee_percent'     => $data['restocking_percent'] ?? null,
                            'refund_shipping' => $data['refund_shipping'] ?? 0,
                            'received_at'     => $receivedAt,
                        ]);
                        $changed = true;
                        break;

                    case ReturnStatusEnum::ReceivedDefective:
                        $service->decide($return_request, [
                            'type'            => 'approved_defective',
                            'fee_percent'     => $data['restocking_percent'] ?? null,
                            'refund_shipping' => $data['refund_shipping'] ?? 0,
                            'received_at'     => $receivedAt,
                        ]);
                        $changed = true;
                        break;

                    case ReturnStatusEnum::Rejected:
                        $service->decide($return_request, [
                            'type'        => 'rejected',
                            'received_at' => $receivedAt,
                        ]);
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
            $return_request->refunded_at = Carbon::parse($data['refunded_at']);
            $dirty = true;
        }
        if (array_key_exists('payout_wallet_phone', $data)) {
            $return_request->payout_wallet_phone = $data['payout_wallet_phone'] ?: null;
            $dirty = true;
        }
        if (array_key_exists('refund_shipping', $data) && $data['refund_shipping'] !== null) {
            $return_request->refund_shipping = (float)$data['refund_shipping'];
            $sub = (float)($return_request->refund_subtotal ?? 0);
            $fee = (float)($return_request->refund_fee ?? 0);
            $ship = (float)$return_request->refund_shipping;
            $return_request->refund_total = max(0, round($sub - $fee - $ship, 2));
            $dirty = true;
        }
        if (array_key_exists('restocking_percent', $data) && $data['restocking_percent'] !== null) {
            // لو عايز تربطها بالـfee: هنا بنسيبها ملاحظة فقط؛
            // الحساب الاصلي للـfee بيتم داخل decide().
            // ممكن نحفظها في admin_note لو محتاج تتبع.
            $note = trim((string)($return_request->admin_note ?? ''));
            $tag  = '[restock%=' . (int)$data['restocking_percent'] . ']';
            if (!str_contains($note, $tag)) {
                $return_request->admin_note = trim($tag . ' ' . $note);
                $dirty = true;
            }
        }

        if ($dirty) {
            $return_request->save();
            $changed = true;
        }

        return redirect()
            ->route('admin.returns.show', $return_request->id)
            ->with('success', $changed ? __('messages.updated_successfully') : __('messages.nothing_changed'));
    }
}
