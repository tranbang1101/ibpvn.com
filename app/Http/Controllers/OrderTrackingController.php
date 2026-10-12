<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(config('store.demo_mode'), 404);

        return view('storefront.order-tracking', [
            'order' => $request->session()->get('tracked_order'),
            'error' => $request->session()->get('tracking_error'),
            'orderCode' => $request->session()->get('tracking_order_code', ''),
            'phone' => $request->session()->get('tracking_phone', ''),
        ]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        abort_if(config('store.demo_mode'), 404);

        $data = $request->validate([
            'order_code' => ['required', 'string', 'max:32'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+().\-\s]+$/'],
        ]);
        $orderCode = strtoupper(trim($data['order_code']));
        $phone = trim($data['phone']);

        try {
            $order = Order::query()
                ->with(['items' => static fn ($query) => $query->orderBy('id')])
                ->where('ma_don', $orderCode)
                ->where('so_dien_thoai', $phone)
                ->first();
        } catch (QueryException $exception) {
            Log::error('Laravel order lookup failed.', [
                'exception' => $exception->getMessage(),
            ]);

            return redirect()->route('orders.track')
                ->with('tracking_error', 'Chưa thể tra cứu đơn hàng lúc này. Vui lòng thử lại sau.')
                ->with('tracking_order_code', $orderCode)
                ->with('tracking_phone', $phone);
        }

        if (! $order) {
            return redirect()->route('orders.track')
                ->with('tracking_error', 'Không tìm thấy đơn hàng với thông tin đã nhập.')
                ->with('tracking_order_code', $orderCode)
                ->with('tracking_phone', $phone);
        }

        return redirect()->route('orders.track')
            ->with('tracked_order', $order)
            ->with('tracking_order_code', $orderCode)
            ->with('tracking_phone', $phone);
    }
}
