<?php


namespace App\Http\Controllers\Api\User\Comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Comments\CommentRequest;
use App\Services\CommentService;
use App\Helpers\ApiResponseHelper;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function store(CommentRequest $request)
    {
        $comment = $this->commentService->store($request->validated());

        return ApiResponseHelper::success('messages.comment_added_success', $comment);
    }

    public function show($orderId)
    {

        $comments = $this->commentService->getCommentsForOrder($orderId);

        if (!$comments) {
            return ApiResponseHelper::error('messages.order_not_found', 404);
        }

        return ApiResponseHelper::success('messages.comment_list_success', $comments);
    }
}
