<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CryptoStore;
use App\Models\Dislike;
use App\Models\Like;
use App\Models\News;
use App\Models\NewsRead;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use ParagonIE\Sodium\Crypto;
use Str;
use Throwable;

class NewsController extends Controller
{



    public function combine_search(Request $request)
    {
        try {

            $request->validate([
                'current_page' => 'sometimes|integer|min:1',
                'per_page'     => 'sometimes|integer|min:1|max:100',
                'title'        => 'nullable|string',
                'type'         => 'nullable|string',
            ]);

            $search  = trim($request->title);
            $type    = $request->type;
            $page    = $request->input('current_page', 1);
            $perPage = $request->input('per_page', 10);

            if (empty($search)) {
                return response()->json([
                    'status' => true,
                    'code'   => 200,
                    'message' => 'Search results fetched successfully',
                    'data'   => [
                        'results' => [],
                        'pagination' => [
                            'total_page'   => 0,
                            'per_page'     => (int) $perPage,
                            'total_item'   => 0,
                            'current_page' => (int) $page,
                        ],
                    ],
                ]);
            }

            $userId = auth('api')->id();

            // ================= NEWS =================
            $news = collect();
            if (!$type || $type === 'news' || ($type !== 'crypto_store')) {
                $newsQuery = News::where('status', 'publish')
                    ->where('title', 'like', "%{$search}%")
                    ->withCount(['likes', 'dislikes', 'comments'])
                    ->withExists(['readers as is_read' => function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    }]);

                // If type is not a table identifier, treat it as a category filter
                if ($type && !in_array($type, ['news', 'crypto_store'])) {
                    $newsQuery->where('type', $type);
                }

                // Only search News if type is null, 'news', or matches a news category
                if (!$type || $type === 'news' || !in_array($type, ['crypto_store'])) {
                    $news = $newsQuery->get()->map(function ($news) use ($userId) {
                        return [
                            'type' => 'news',
                            'id' => $news->id,
                            'slug' => $news->slug,
                            'title' => $news->title,
                            'likes_count' => $news->likes_count,
                            'dislikes_count' => $news->dislikes_count,
                            'comments_count' => $news->comments_count,
                            'description' => Str::limit($news->short_description, 100),
                            'thumbnail' => $news->thumbnail ? asset($news->thumbnail) : null,
                            'date' => $news->created_at->format('l, F d Y'),
                            'is_read' => (bool) $news->is_read,
                            'created_at' => $news->created_at,
                        ];
                    });
                }
            }

            // ================= CRYPTO STORE =================
            $crypto = collect();
            if (!$type || $type === 'crypto_store' || ($type !== 'news')) {
                $cryptoQuery = CryptoStore::where('title', 'like', "%{$search}%")
                    ->withCount('ratings')
                    ->withAvg('ratings as avg_rating', 'rating');

                // If type is not a table identifier, treat it as a category filter
                if ($type && !in_array($type, ['news', 'crypto_store'])) {
                    $cryptoQuery->where('type', $type);
                }

                // Only search CryptoStore if type is null, 'crypto_store', or matches a crypto category
                if (!$type || $type === 'crypto_store' || !in_array($type, ['news'])) {
                    $crypto = $cryptoQuery->get()->map(function ($store) {
                        return [
                            'type' => 'crypto_store',
                            'id' => $store->id,
                            'name' => $store->name,
                            'slug' => $store->slug,
                            'short_description' => $store->short_description,
                            'image' => $store->image ? asset($store->image) : null,
                            'avg_rating' => number_format($store->avg_rating ?? 0, 1),
                            'ratings_count' => $store->ratings_count,
                            'created_at' => $store->created_at,
                        ];
                    });
                }
            }

            // ================= MERGE + SORT =================
            $merged = $news->merge($crypto)
                ->sortByDesc('created_at')
                ->values();

            // ================= PAGINATION =================
            $total = $merged->count();

            $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

            $paginator = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url()]
            );

            return response()->json([
                'status' => true,
                'code'   => 200,
                'message' => 'Search results fetched successfully',
                'data'   => [
                    'results' => $paginator->items(),
                    'pagination' => [
                        'total_page'   => $paginator->lastPage(),
                        'per_page'     => $paginator->perPage(),
                        'total_item'   => $paginator->total(),
                        'current_page' => $paginator->currentPage(),
                    ],
                ],
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function news(Request $request)
    {
        try {

            $request->validate([
                'current_page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'title' => 'sometimes|string',
                'type' => 'sometimes',
            ]);

            $page = $request->input('current_page', 1);
            $perPage = $request->input('per_page', 10);
            $type = $request->type;
            if (is_string($type)) {
                $type = array_filter(explode(',', $type));
            }
            $type = (array) $type;

            $userId = auth('api')->id();
            $query = News::where('status', 'publish')
                ->withCount(['likes', 'dislikes', 'comments'])
                ->withExists(['readers as is_read' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }])
                ->latest('id');

            Cache::flush();


            if ($request->filled('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }

            if (!empty($type)) {
                $query->whereIn('type', $type);
            }
            $newsPaginator = $query->paginate($perPage, ['*'], 'page', $page);

            $totalNews = News::where('status', 'publish')->count();

            $readNews = NewsRead::where('user_id', $userId)->count();



            $unreadCount = $totalNews - $readNews;

            $newsData = $newsPaginator->getCollection()->map(function ($news) use ($userId) {
                return [
                    'id' => $news->id,
                    'type' => $news->type,
                    'slug' => $news->slug,
                    'title' => $news->title,
                    'likes_count' => $news->likes_count,
                    'dislikes_count' => $news->dislikes_count,
                    'comments_count' => $news->comments_count,
                    'description' => Str::limit($news->short_description, 100),
                    'thumbnail' => $news->thumbnail ? asset($news->thumbnail) : null,
                    'date' => $news->created_at->format('l, F d Y'),
                    'is_read' => (bool) $news->is_read,
                ];
            });

            $newsPaginator->setCollection($newsData);

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'News fetched successfully',
                'data' => [
                    'unread_count' => $unreadCount,
                    'newslist' => $newsPaginator->items(),
                    'pagination' => [
                        'total_page' => $newsPaginator->lastPage(),
                        'per_page' => $newsPaginator->perPage(),
                        'total_item' => $newsPaginator->total(),
                        'current_page' => $newsPaginator->currentPage(),
                    ],
                ],
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function news_details()
    {
        $slug = request('slug');

        try {
            $user = auth('api')->user();

            $news = News::with(['details.images'])
                ->withCount(['comments', 'likes', 'dislikes'])
                ->withExists(['likes as is_liked' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
                ->withExists(['dislikes as is_disliked' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
                ->where('slug', $slug)
                ->first();

            // ❗ check first (IMPORTANT)
            if (!$news) {
                return Helper::jsonErrorResponse('News not found', 404);
            }

            // ✅ mark as read (BEST WAY)
            $user->readNews()->syncWithoutDetaching([
                $news->id => ['read_at' => now()]
            ]);

            // ✅ reading time calculation
            $contentText = strip_tags($news->short_description ?? '');

            foreach ($news->details as $detail) {
                $contentText .= ' ' . strip_tags($detail->description ?? '');
            }

            $wordCount = str_word_count($contentText);
            $readingTime = max(1, ceil($wordCount / 200));

            // ✅ response data
            $newsData = [
                'id' => $news->id,
                'is_subscribed' => $user->is_subscribed,
                'title' => $news->title,
                'type' => $news->type,
                'description' => $news->short_description,
                'date' => $news->created_at->format('F d Y'),
                'reading_time' => $readingTime . ' min read',
                'thumbnail' => $news->thumbnail ? asset($news->thumbnail) : null,

                'details' => $news->details->map(function ($detail) {
                    return [
                        'title' => $detail->title,
                        'description' => $detail->description,
                        'images' => $detail->images->map(function ($image) {
                            return [
                                'image' => $image->image ? asset($image->image) : null,
                            ];
                        }),
                    ];
                }),

                'total_comments' => (int) $news->comments_count,
                'total_likes' => (int) $news->likes_count,
                'total_dislike' => (int) $news->dislikes_count,

                'is_liked' => (bool) $news->is_liked,
                'is_disliked' => (bool) $news->is_disliked,
            ];

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'News fetched successfully',
                'data' => $newsData,
            ]);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }
    public function addComment(Request $request)
    {
        try {
            $request->validate([
                'slug' => 'required|string|exists:news,slug', // validate slug instead of news_id
                'comment' => 'required|string|min:3',
            ]);

            // Find the news by slug
            $news = News::where('slug', $request->slug)->first();
            if (!$news) {
                return response()->json([
                    'status' => false,
                    'message' => 'News not found',
                ], 404);
            }

            // Create the comment
            $comment = Comment::create([
                'news_id' => $news->id, // use news ID internally
                'user_id' => auth('api')->id(),
                'parent_id' => $request->parent_id ?? null,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Comment added successfully',
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function reaction(Request $request)
    {
        try {
            $request->validate([
                'slug' => 'required|string|exists:news,slug', // validate slug
                'action' => 'required|in:like,dislike',
            ]);

            $userId = auth('api')->id();
            $slug = $request->slug;

            // Find the news by slug
            $news = News::where('slug', $slug)->firstOrFail();
            $newsId = $news->id;

            if ($request->action === 'like') {
                // Remove existing dislike if any
                Dislike::where('user_id', $userId)
                    ->where('news_id', $newsId)
                    ->delete();

                // Check if user already liked
                $like = Like::where('user_id', $userId)
                    ->where('news_id', $newsId)
                    ->first();

                if ($like) {
                    $like->delete();
                    $status = 'unliked';
                } else {
                    Like::create([
                        'news_id' => $newsId,
                        'user_id' => $userId,
                    ]);
                    $status = 'liked';
                }
            }

            if ($request->action === 'dislike') {
                // Remove existing like if any
                Like::where('user_id', $userId)
                    ->where('news_id', $newsId)
                    ->delete();

                // Check if user already disliked
                $dislike = Dislike::where('user_id', $userId)
                    ->where('news_id', $newsId)
                    ->first();

                if ($dislike) {
                    $dislike->delete();
                    $status = 'undisliked';
                } else {
                    Dislike::create([
                        'news_id' => $newsId,
                        'user_id' => $userId,
                    ]);
                    $status = 'disliked';
                }
            }

            return response()->json([
                'status' => true,
                'code' => 200,
                'reaction_status' => $status,
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function comments(Request $request)
    {
        $page = $request->input('current_page', 1);
        $perPage = $request->input('per_page', 10);
        $slug = $request->input('slug');

        if (!$slug) {
            return response()->json([
                'status' => false,
                'message' => 'News slug is required',
            ], 400);
        }

        // Find news by slug
        $news = News::where('slug', $slug)->first();
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'News not found',
            ], 404);
        }

        $authUserId = auth('api')->id();

        $comments = Comment::with([
            'user',
            'likes' => function ($query) use ($authUserId) {
                $query->where('user_id', $authUserId);
            },
            'replies.user',
            'replies.likes' => function ($query) use ($authUserId) {
                $query->where('user_id', $authUserId);
            },
            'replies.replies.user',
            'replies.replies.likes' => function ($query) use ($authUserId) {
                $query->where('user_id', $authUserId);
            },
        ])
            ->where('news_id', $news->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedData = $comments->getCollection()->map(function ($comment) use ($authUserId) {
            return [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'is_mine' => $comment->user_id == $authUserId,
                'is_liked' => $comment->likes->isNotEmpty(),
                'avatar' => $comment->user?->avatar ? url($comment->user->avatar) : null,
                'name' => $comment->user?->name,
                'comment' => $comment->comment,
                'commented_at' => $comment->created_at->diffForHumans(),
                'replies' => $comment->replies->map(function ($reply) use ($authUserId) {
                    return [
                        'id' => $reply->id,
                        'user_id' => $reply->user_id,
                        'is_mine' => $reply->user_id == $authUserId,
                        'is_liked' => $reply->likes->isNotEmpty(),
                        'avatar' => $reply->user?->avatar ? url($reply->user->avatar) : null,
                        'name' => $reply->user?->name,
                        'comment' => $reply->comment,
                        'commented_at' => $reply->created_at->diffForHumans(),
                        'replies' => $reply->replies->map(function ($subReply) use ($authUserId) {
                            return [
                                'id' => $subReply->id,
                                'user_id' => $subReply->user_id,
                                'is_mine' => $subReply->user_id == $authUserId,
                                'is_liked' => $subReply->likes->isNotEmpty(),
                                'avatar' => $subReply->user?->avatar ? url($subReply->user->avatar) : null,
                                'name' => $subReply->user?->name,
                                'comment' => $subReply->comment,
                                'commented_at' => $subReply->created_at->diffForHumans(),
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values();

        // Replace paginator collection
        $comments->setCollection($transformedData);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Comments fetched successfully',
            'data' => $comments->items(), // or $transformedData
            'pagination' => [
                'total_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total_item' => $comments->total(),
                'current_page' => $comments->currentPage(),
            ],
        ]);
    }

    public function news_type(Request $request)
    {
        $page = $request->input('current_page', 1);
        $perPage = $request->input('per_page', 10);

        $newsType = Cache::remember('news_types_page_' . $page, 3600, function () use ($perPage, $page) {
            return News::select('type')->where('status', 'publish')->distinct()->paginate($perPage, ['*'], 'page', $page);
        });

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'News Type fetched successfully',
            'data' => collect($newsType->items())->map(function ($newsType) {
                return [
                    'type' => $newsType->type,
                ];
            }),
            'pagination' => [
                'total_page' => $newsType->lastPage(),
                'per_page' => $newsType->perPage(),
                'total_item' => $newsType->total(),
                'current_page' => $newsType->currentPage(),
            ],
        ]);
    }

    public function subscribe(Request $request)
    {
        $user = auth('api')->user();
        $user->is_subscribed = !$user->is_subscribed;

        $user->save();
        // dd($user->is_subscribed);
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $user->is_subscribed ? 'Subscribed' : 'Unsubscribed',
        ]);
    }

    public function subscribed_list(Request $request)
    {

        $providedToken = $request->header('Authorization');


        if (str_starts_with($providedToken, 'Bearer ')) {
            $providedToken = substr($providedToken, 7);
        }

        $secretToken = env('API_SECRET_TOKEN');

        if ($providedToken !== $secretToken) {
            return Helper::jsonResponse(false, 'Unauthorized', 401);
        }





        $user = User::select('email')->where('is_subscribed', 1)->get();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Subscribed list fetched successfully',
            'data' => $user,
        ]);
    }

    public function most_popular(Request $request)
    {
        $page = $request->input('current_page', 1);
        $perPage = $request->input('per_page', 10);

        $userId = auth('api')->id();

        $newsall = News::withCount(['likes', 'dislikes', 'comments'])
            ->withExists(['readers as is_read' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->where('status', 'publish')
            ->orderBy('likes_count', 'desc')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Most popular news fetched successfully',
            'data' => [
                'newslist' => $newsall->map(function ($news) {
                    return [
                        'id' => $news->id,
                        'likes_count' => $news->likes_count,
                        'dislikes_count' => $news->dislikes_count,
                        'comments_count' => $news->comments_count,
                        'type' => $news->type,
                        'slug' => $news->slug,
                        'title' => $news->title,
                        'description' => Str::limit($news->short_description, 100),
                        'thumbnail' => $news->thumbnail ? asset($news->thumbnail) : null,
                        'date' => $news->created_at->format('l F d Y'),
                        'is_read' => (bool) $news->is_read,

                    ];
                }),
                'pagination' => [
                    'total_page' => $newsall->lastPage(),
                    'per_page' => $newsall->perPage(),
                    'total_item' => $newsall->total(),
                    'current_page' => $newsall->currentPage(),
                ],
            ],
        ]);
    }

    public function edit_comment(Request $request)
    {
        try {

            $request->validate([
                'comment_id' => 'required|exists:comments,id',
                'comment' => 'required|string|min:3',
            ]);

            // Find the comment
            $comment = Comment::find($request->input('comment_id'));
            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found',
                ], 404);
            }

            // Update the comment
            $comment->comment = $request->input('comment');
            $comment->save();

            return response()->json([
                'status' => true,
                'message' => 'Comment updated successfully',
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function delete_comment(Request $request)
    {
        try {
            $request->validate([
                'comment_id' => 'required|exists:comments,id',
            ]);

            $comment = Comment::find($request->input('comment_id'));
            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found',
                ], 404);
            }

            $comment->delete();

            return response()->json([
                'status' => true,
                'message' => 'Comment deleted successfully',
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function like_comment(Request $request)
    {
        try {
            $request->validate([
                'comment_id' => 'required|exists:comments,id',
            ]);

            $commentId = $request->input('comment_id');
            $userId = auth('api')->id();

            // Check if the user has already liked the comment
            $existingLike = CommentLike::where('comment_id', $commentId)
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                // If the like already exists, remove it (unlike)
                $existingLike->delete();
                $status = 'unliked';
            } else {
                // Otherwise, create a new like
                CommentLike::create([
                    'comment_id' => $commentId,
                    'user_id' => $userId,
                ]);
                $status = 'liked';
            }

            return response()->json([
                'status' => true,
                'message' => "Comment {$status} successfully",
            ]);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function unread_count(Request $request)
    {
        try {
            $userId = auth('api')->id();

            $totalNews = News::where('status', 'publish')->count();

            $readNews = NewsRead::where('user_id', $userId)->count();

            $unreadCount = $totalNews - $readNews;

            return response()->json([
                'status' => true,
                'message' => 'Unread count fetched successfully',
                'data' => $unreadCount,
            ]);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }
}
