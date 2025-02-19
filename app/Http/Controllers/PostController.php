<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Constants;
use App\Models\FollowingList;
use App\Models\GlobalFunction;
use App\Models\Like;
use App\Models\Myfunction;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\Report;
use App\Models\Story;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function posts()
    {
        return view('posts');
    }

    public function postsList(Request $request)
    {
        $totalData = Post::count();
        $rows = Post::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'Content',
            2 => 'Thumbnail',
            3 => 'Views',
            4 => 'likes',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Post::offset($start)->limit($limit)->orderBy($order, $dir)->get();
        } else {
            $search = $request->input('search.value');
            $result = Post::Where('name', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = Post::Where('name', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {


            $postContent = PostContent::where('post_id', $item->id)->get();
            $contentType = $postContent->count() == 0 ? 2 : $postContent->first()->content_type;
            $firstContent = $postContent->pluck('content');

            if ($item->description == null) {
                $item->description = 'Note: Post has no description';
            }

            if ($contentType == 0) {
                $viewPost = '<button type="button" class="btn btn-primary viewPost commonViewBtn" data-bs-toggle="modal" data-image=' . $firstContent . ' data-description="' . $item->description . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> View Post</button>';
            } else if ($contentType == 1) {
                $viewPost = '<button type="button" class="btn btn-primary viewVideoPost commonViewBtn" data-bs-toggle="modal" data-image=' . $firstContent . ' data-description="' . $item->description . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> View Post</button>';
            } else if ($contentType == 2)  {
                $viewPost = '<button type="button" class="btn btn-primary viewDescPost commonViewBtn" data-bs-toggle="modal" data-description="' . $item->description . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-type"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg> View Post</button>';
            }

            $userName = '<a href="./viewUserDetails/'.$item->user->id.'"> '. $item->user->fullname .' </a>';

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deletePost d-flex align-items-center" rel=' . $item->id . '>' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-end d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewPost,
                $userName,
                $item->user->identity,
                $item->comments_count,
                $item->likes_count,
                $item->created_at->format('d-m-Y'),
                $action
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function createStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);   
        }

        if ($user->is_block == Constants::blocked) {
            return response()->json([
                'status' => false,
                'message' => 'User is Blocked',
            ]);   
        }

        $story = new Story();
        $story->user_id = (int) $request->user_id;
        $story->duration = (float) $request->duration;
        $story->type = (int) $request->type;
        if ($request->hasFile('content')) {
            $files = $request->file('content');
            $path = GlobalFunction::saveFileAndGivePath($files);
            $story->content = $path;
        }
        $story->save();

        return response()->json([
            'status' => true,
            'message' => 'Story Added Successfully',
            'data' => $story,
        ]);
        
    }

    public function viewStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'story_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('is_block', 0)
                    ->where('id', $request->user_id)
                    ->first();
        if ($user) {

            $viewStory = Story::where('id', $request->story_id)->first();

            if ($viewStory) {
                $viewStoryByUserIds = explode(',', $viewStory->view_by_user_ids);
                
                if (!in_array($request->user_id, $viewStoryByUserIds)) {
                    // If the user ID is not already in the view_by_user_ids
                    if (!empty($viewStory->view_by_user_ids)) {
                        // Add a comma if view_by_user_ids is not empty
                        $viewStory->view_by_user_ids .= ',';
                    }
                    $viewStory->view_by_user_ids .= $request->user_id;
                    $viewStory->save();
                    $message = 'Story Viewed';
                } else {
                    $message = 'Story Already Viewed';
                }

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'data' => $viewStory,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Story not found',
            ]);


        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);

    }

    public function fetchStories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->my_user_id)->first();


        if ($user) {

           $followingUsers = FollowingList::where('my_user_id', $request->my_user_id)
                                            ->whereRelation('story', 'created_at', '>=', now()->subDay()->toDateTimeString())
                                            ->with('user')
                                            ->whereRelation('user', 'is_block', 0)
                                            ->get()
                                            ->pluck('user');

            foreach ($followingUsers as $followingUser) {
                $stories = Story::where('user_id', $followingUser->id)
                                ->where('created_at', '>=', now()->subDay()->toDateTimeString())
                                ->get();
                                
                foreach ($stories as $story) {
                    // Check if my_user_id is in view_by_user_ids
                    $story->storyView = $story->view_by_user_ids ? in_array($request->my_user_id, explode(',', $story->view_by_user_ids)) : false;
                }
                $followingUser->stories = $stories;
                $followingUser->images = $followingUser->images;

            }

            return response()->json([
                'status' => true,
                'message' => 'Story fetch Successfully',
                'data' => $followingUsers,
            ]);
        }
    }

    public function deleteStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'story_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $story = Story::where('id', $request->story_id)->where('user_id', $request->my_user_id)->first();

        if($story) {

            GlobalFunction::deleteFile($story->content);
            $story->delete();

            return response()->json([
                'status' => true,
                'message' => 'Story delete successfully',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Story not found'
        ]);
    }

    public function addPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        if ($user->is_block == Constants::blocked) {
            return response()->json([
                'status' => false,
                'message' => 'User is Blocked',
            ]);
        }

        $post = new Post();
        $post->user_id = (int) $request->user_id;
        if ($request->has('description')) {
            $post->description = $request->description;
        }
        if ($request->has('interest_ids')) {
            $post->interest_ids = $request->interest_ids;
        }
        if ($request->has('hashtags')) {
            $post->hashtags = $request->hashtags;
        }
        $post->save();

        if ($request->hasFile('content')) {
            $files = $request->file('content');
            for ($i = 0; $i < count($files); $i++) {
                $postContent = new PostContent();
                $postContent->post_id = $post->id;
                $path = GlobalFunction::saveFileAndGivePath($files[$i]);
                $postContent->content = $path;
                if ($request->hasFile('thumbnail')) {
                    $thumbnails = $request->file('thumbnail');
                    $path = GlobalFunction::saveFileAndGivePath($thumbnails[$i]);
                    $postContent->thumbnail = $path;
                }
                $postContent->content_type = $request->content_type;
                $postContent->save();
            }
        }

        $post = Post::where('id', $post->id)->with('content', 'user', 'user.images', 'user.stories')->first();

        return response()->json([
            'status' => true,
            'message' => 'Post Uploaded',
            'data' => $post,
        ]);
        
    }

    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->where('is_block', 0)->first();

        if ($user) {
            $post = Post::where('id', $request->post_id)
                        ->with(['user','content', 'user.stories','user.images'])
                        ->first();

            if ($post) {
                $comment = new Comment();
                $comment->user_id = (int) $request->user_id;
                $comment->post_id = (int) $request->post_id;
                $comment->description = $request->description;
                $comment->save();

                $post->comments_count += 1;
                $post->save();


                $toUser = $post->user;

                if ($toUser->id != $user->id) {
                    if ($toUser->is_notification == 1) {
                        $notificationDesc = $user->fullname . ' has commented: ' . $request->description;
                        Myfunction::sendPushToUser(env('APP_NAME'), $notificationDesc, $toUser->device_token);
                    }
                }

                $comment->post = $post;
                 
                if ($user->id != $post->user_id) {
                    $type = Constants::notificationTypeComment;

                    $userNotification = new UserNotification();
                    $userNotification->my_user_id = (int) $request->user_id;
                    $userNotification->user_id = (int) $post->user->id;
                    $userNotification->item_id = (int) $comment->id;
                    $userNotification->type = $type;
                    $userNotification->save();
                }


                return response()->json([
                    'status' => true,
                    'message' => 'Comment Placed',
                    'data' => $comment
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Post Not Found',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function fetchComments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $fetchComment = Comment::where('post_id', $request->post_id)
                                ->with(['user','user.stories','user.images'])
                                ->whereRelation('user', 'is_block', 0)
                                ->orderBy('id', 'DESC')
                                ->offset($request->start)
                                ->limit($request->limit)
                                ->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Fetch Comments',
            'data' => $fetchComment,
        ]);
    }

    public function deleteComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'comment_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = Comment::where('id', $request->comment_id)->where('user_id', $request->user_id)->first();

        if($comment) {

            $commentCount = Post::where('id', $comment->post_id)->first();
            $commentCount->comments_count -= 1;
            $commentCount->save();

            $deleteCommentFromUserNotification = UserNotification::where('my_user_id', $request->user_id)
                                                                    ->where('item_id', $comment->id)
                                                                    ->where('type', Constants::notificationTypeComment)
                                                                    ->get();
            $deleteCommentFromUserNotification->each->delete();

            $comment->delete();
            return response()->json([
                'status' => true,
                'message' => 'Delete Comment Successfully',
                'data' => $comment
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Comment not found'
        ]);

    }

    public function likePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('is_block', 0)
                    ->where('id', $request->user_id)
                    ->first();
        if ($user) {
            $post = Post::where('id', $request->post_id)->with(['user', 'content'])->first();
            if ($post) {
                $likeRecord = Like::where('user_id', $request->user_id)->where('post_id', $request->post_id)->first();
                if ($likeRecord) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Already Liked',
                    ]);
                } else {
                    $like = new Like();
                    $like->user_id = (int) $request->user_id;
                    $like->post_id = (int) $request->post_id;
                    $like->save();

                    $post->likes_count += 1;
                    $post->save();

                    $toUser = $post->user;

                    if ($toUser->id != $user->id) {
                        if($toUser->is_notification == 1) {
                            $notificationDesc = $user->fullname . ' has liked your post.';
                            Myfunction::sendPushToUser("Post Like", $notificationDesc, $toUser->device_token);
                        }
                    }

                    if ($user->id != $post->user->id) {
                        $type = Constants::notificationTypeLike;

                        $userNotification = new UserNotification();
                        $userNotification->my_user_id = (int) $request->user_id;
                        $userNotification->user_id = (int) $post->user->id;
                        $userNotification->item_id = (int) $request->post_id;
                        $userNotification->type = $type;
                        $userNotification->save();
                    }

                    $like->post = $post;

                    return response()->json([
                        'status' => true,
                        'message' => 'Post Liked',
                        'data' => $like,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Post Not Found',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function dislikePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $user = Users::where('is_block', 0)
                    ->where('id', $request->user_id)
                    ->first();
        if ($user) {
            $likedPost = Like::where('user_id', $request->user_id)->where('post_id', $request->post_id)->first();
            if ($likedPost) {
                $likeCount = Post::where('id', $request->post_id)->first();
                $likeCount->likes_count = max(0, $likeCount->likes_count - 1);
                $likeCount->save();
                
                $userNotification = UserNotification::where('my_user_id', $request->user_id)
                                                    ->where('item_id', $request->post_id)
                                                    ->where('type', Constants::notificationTypeLike)
                                                    ->first();
                if($userNotification) {
                    $userNotification->delete();
                }

                
                $likedPost->delete();
                
                return response()->json([
                    'status' => true,
                    'message' => 'Post Dislike',
                    'data' => $likedPost,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Post Already Dislike',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }

    public function reportPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'reason' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $post = Post::where('id', $request->post_id)->first();

        if ($post != null) {
            $reportType = 0;

            $report = new Report();
            $report->type = $reportType;
            $report->post_id = $request->post_id;
            $report->reason = $request->reason;
            $report->description = $request->description;
            $report->save();

            return response()->json([
                'status' => true,
                'message' => 'Report Added Successfully',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ]);
        }
    }

    public function deleteMyPost(Request $request)
    {
        $post = Post::where('id', $request->post_id)->where('user_id', $request->user_id)->first();
        $user = Users::where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
        if ($post && $user) {
            $postComments = Comment::where('post_id', $request->post_id)->get();
            $postComments->each->delete();

            $postLikes = Like::where('post_id', $request->post_id)->get();
            $postLikes->each->delete();
            
            $postReport = Report::where('post_id', $request->post_id)->get();
            $postReport->each->delete();

            $postContents = PostContent::where('post_id', $request->post_id)->get();
            foreach ($postContents as $postContent) {
                GlobalFunction::deleteFile($postContent->content);
                GlobalFunction::deleteFile($postContent->thumbnail);
            }
            $postContents->each->delete();

            $userNotification = UserNotification::where('post_id', $request->post_id)->get();
            $userNotification->each->delete();

            $post->delete();
            return response()->json([
                'status' => true,
                'message' => 'Post Delete Successfully',
                'data' => $post,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Post Not Found',
        ]);
    }

    public function fetchPostByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'user_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();

        if ($user) {

            $blockUserIds = Users::where('id', $request->my_user_id)->pluck('blocked_users')->first();

            $fetchPosts = Post::where('user_id', $request->user_id)
                                ->whereNotIn('user_id', explode(',', $blockUserIds))
                                ->with(['content', 'user', 'user.stories', 'user.images'])
                                ->orderBy('created_at', 'desc')
                                ->offset($request->start)
                                ->limit($request->limit)
                                ->get();

            foreach ($fetchPosts as $fetchPost) {
                $isPostLike = Like::where('user_id', $request->my_user_id)->where('post_id', $fetchPost->id)->first();
                $fetchPost->is_like = $isPostLike ? 1 : 0;

                $blockUserIds = Users::where('is_block', 1)->pluck('id');

                $comments_count = Comment::whereNotIn('user_id', $blockUserIds)->where('post_id', $fetchPost->id)->count();
                $likes_count = Like::whereNotIn('user_id', $blockUserIds)->where('post_id', $fetchPost->id)->count();

                $fetchPost->comments_count = $comments_count;
                $fetchPost->likes_count = $likes_count;

                foreach ($fetchPost->user->stories as $story) {
                    $story->is_viewed = $story->view_by_user_ids ? in_array($request->my_user_id, explode(',', $story->view_by_user_ids)) : false;
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Fetch post successfully',
                'data' => $fetchPosts,
            ]);

        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }

    public function fetchPostsByHashtag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'hashtag' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('is_block', 0)
                    ->where('id', $request->user_id)
                    ->first();

        if ($user) {
            $blockUserIds = explode(',', $user->block_user_ids);

            $hashtagPosts = Post::whereRelation('user', 'is_block', 0)
                            ->whereRaw('find_in_set("' . $request->hashtag . '", hashtags)')
                            ->whereNotIn('user_id', $blockUserIds)
                            ->with(['content','user', 'user.stories', 'user.images'])
                            ->orderBy('created_at', 'desc')
                            ->offset($request->start)
                            ->limit($request->limit)
                            ->get();
            
            foreach ($hashtagPosts as $post) {
                foreach ($post->user->stories as $story) {
                    $story->is_viewed = $story->view_by_user_ids ? in_array($request->user_id, explode(',', $story->view_by_user_ids)) : false;
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Fetch posts by hashtag successfully',
                'data' => $hashtagPosts,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);

    }

    public function fetchPostByPostId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();                                                                                                                                                                                                                                                                                        
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('is_block', 0)
                    ->where('id', $request->user_id)
                    ->first();

        if ($user) {
            $blockUserIds = explode(',', $user->block_user_ids);

            $post = Post::where('id', $request->post_id)
                            ->whereRelation('user', 'is_block', 0)
                            ->whereNotIn('user_id', $blockUserIds)
                            ->with(['content','user', 'user.stories', 'user.images'])
                            ->orderBy('created_at', 'desc')
                            ->first();
                            
            $isPostLike = Like::where('user_id', $request->user_id)->where('post_id', $post->id)->first();
            $post->is_like = $isPostLike ? 1 : 0;
            
                foreach ($post->user->stories as $story) {
                    $story->is_viewed = $story->view_by_user_ids ? in_array($request->user_id, explode(',', $story->view_by_user_ids)) : false;
                }

            return response()->json([
                'status' => true,
                'message' => 'Fetch posts by hashtag successfully',
                'data' => $post,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);

    }

    public function deletePostFromUserPostTable(Request $request)
    {
        $post = Post::where('id', $request->post_id)->first();
        if ($post) {
            $postContents = PostContent::where('post_id', $request->post_id)->get();
            foreach ($postContents as $postContent) {
                GlobalFunction::deleteFile($postContent->content);
                GlobalFunction::deleteFile($postContent->thumbnail);
            }
            $postContents->each->delete();

            $postComments = Comment::where('post_id', $request->post_id)->get();
            $postComments->each->delete();

            $postLikes = Like::where('post_id', $request->post_id)->get();
            $postLikes->each->delete();

            $postReport = Report::where('post_id', $request->post_id)->get();
            $postReport->each->delete();

            $post->delete();

            return response()->json([
                'status' => true,
                'message' => 'Post Delete Successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Post Not Found',
        ]);
    }

    public function userPostList(Request $request)
    {
        $totalData = Post::where('user_id', $request->userId)->count();
        $rows = Post::where('user_id', $request->userId)
                    ->orderBy('id', 'DESC')
                    ->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'Content',
            2 => 'Thumbnail',
            3 => 'Views',
            4 => 'likes',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Post::where('user_id', $request->userId)->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        } else {
            $search = $request->input('search.value');
            $result = Post::where('user_id', $request->userId)->Where('name', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = Post::where('user_id', $request->userId)->Where('name', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {

            $postContent = PostContent::where('post_id', $item->id)->get();
            $contentType = $postContent->count() == 0 ? 2 : $postContent->first()->content_type;
            $firstContent = $postContent->pluck('content');

            if ($item->description == null) {
                $item->description = 'Note: Post has no description';
            }

            if ($contentType == 0) {
                $viewPost = '<button type="button" class="btn btn-primary viewPost commonViewBtn" data-bs-toggle="modal" data-image=' . $firstContent . ' data-description="' . $item->description . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> View Post</button>';
            } else if ($contentType == 1) {
                $viewPost = '<button type="button" class="btn btn-primary viewVideoPost commonViewBtn" data-bs-toggle="modal" data-image=' . $firstContent . ' data-description="' . $item->description . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> View Post</button>';
            } else if ($contentType == 2)  {
                $viewPost = '<button type="button" class="btn btn-primary viewDescPost commonViewBtn" data-bs-toggle="modal" data-description="' . $item->description . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-type"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg> View Post</button>';
            }

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deletePost d-flex align-items-center" rel=' . $item->id . '>' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-end d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewPost,
                $item->comments_count,
                $item->likes_count,
                $item->created_at->format('d-m-Y'),
                $action
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function increasePostViewCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $postContent = PostContent::where('post_id', $request->post_id)->where('content_type', Constants::contentVideo)->first();
        if ($postContent) {
            $postContent->view_count += 1;
            $postContent->save();

            return response()->json([
                'status' => true,
                'message' => 'Add post view count',
                'data' => $postContent,
            ]);    
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ]);    
        }
        
    }

    // CronJob start
    public function deleteStoryFromWeb()
    {
        $stories = Story::where('created_at', '<=', now()->subDay()->toDateTimeString())->get();

        if($stories) {
            foreach ($stories as $story) {
                GlobalFunction::deleteFile($story->content);
                $story->delete();
            }
        }
    }
    // CronJob End

    public function viewStories()
    {
        return view('viewStories');
    }

    public function userStoryList(Request $request)
    {

        $twentyFourHoursAgo = Carbon::now()->subDay();

        $totalData = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('user_id', $request->user_id)
            ->count();

        $rows = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('user_id', $request->user_id)
            ->orderBy('id', 'DESC')
            ->get();

        $result = $rows;

        $columns = [
            0 => 'id'
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $result = Story::where('created_at', '>=', $twentyFourHoursAgo)
                ->where('created_at', '<=', Carbon::now())
                ->where('user_id', $request->user_id)
                ->where(function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                        // Add more conditions for searching other user fields if needed
                    });
                    // Add more conditions for searching other story fields if needed
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $result->count(); // Count filtered result
        } else {
            $result = Story::where('created_at', '>=', $twentyFourHoursAgo)
                ->where('created_at', '<=', Carbon::now())
                ->where('user_id', $request->user_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        }

        $data = [];


        foreach ($result as $item) {
            $contentType = $item->type;
            $contentURL = GlobalFunction::createMediaUrl($item->content);

            $timeAgo = Carbon::parse($item->created_at)->diffForHumans();

            $viewStory = ($contentType == 0) ? '<button type="button" class="btn btn-primary viewStory commonViewBtn" data-bs-toggle="modal" data-image="' . $contentURL . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> View Story</button>'
            : '<button type="button" class="btn btn-primary viewStoryVideo commonViewBtn" data-bs-toggle="modal" data-image="' . $contentURL . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> View Story</button>';

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteStory d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Story">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewStory,
                $timeAgo,
                $action
            ];
        }

        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function deleteStoryFromAdmin(Request $request)
    {
        $story = Story::where('id', $request->story_id)->first();

        if ($story) {

            GlobalFunction::deleteFile($story->content);
            $story->delete();

            return response()->json([
                'status' => true,
                'message' => 'Story delete successfully',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Story not found'
        ]);
    }

    public function allStoriesList(Request $request)
    {

        $twentyFourHoursAgo = Carbon::now()->subDay();

        $totalData = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->count();

        $rows = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->orderBy('id', 'DESC')
            ->get();

        $result = $rows;

        $columns = [
            0 => 'id'
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;

        $searchValue = $request->input('search.value');

        $query = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now());

        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('user', function ($q) use ($searchValue) {
                    $q->where('full_name', 'LIKE', "%{$searchValue}%");
                });
            });
        }

        $result = $query->with('user') // Eager load the user relationship if needed
        ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        if (!empty($searchValue)) {
            $totalFiltered = $result->count();
        }

        $data = [];

        foreach ($result as $item) {
            $userName = '<a href="viewUserDetails/' . $item->user_id . '">' .  $item->user->fullname . '</a>';
            $contentType = $item->type;
            $contentURL = GlobalFunction::createMediaUrl($item->content);

            $timeAgo = Carbon::parse($item->created_at)->diffForHumans();

            $viewStory = ($contentType == 0) ? '<button type="button" class="btn btn-primary viewStory commonViewBtn" data-bs-toggle="modal" data-image="' . $contentURL . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> View Story</button>'
            : '<button type="button" class="btn btn-primary viewStoryVideo commonViewBtn" data-bs-toggle="modal" data-image="' . $contentURL . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> View Story</button>';

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteStory d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Story">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewStory,
                $userName,
                $timeAgo,
                $action
            ];
        }

        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }
    
    
}
