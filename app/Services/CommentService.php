<?php



namespace App\Services;

use App\Models\Comment;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function store($data)
    {

        return Comment::create([
            'user_id'   => Auth::id(),
            'order_id' => $data['order_id'],
            'comment'    => $data['comment'],
            'rating'     => $data['rating'] ?? 0,
        ]);
    }

    public function getCommentsForOrder($orderId)
    {

        $order = Order::with('comments')->find($orderId);

        if (!$order) {
            return null;
        }

        return $order->comments->map(function ($comment) {

            return [
                'user'    => $comment->user->name,

                'comment' => $comment->comment,
                'rating'  => $comment->rating,
                'created_at' => $comment->created_at->toDateString()
            ];
        });
    }
}
