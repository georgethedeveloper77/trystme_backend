<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Constants;
use App\Models\FollowingList;
use App\Models\GlobalFunction;
use App\Models\Images;
use App\Models\Interest;
use App\Models\Like;
use App\Models\LikedProfile;
use App\Models\LiveApplications;
use App\Models\LiveHistory;
use App\Models\Myfunction;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\RedeemRequest;
use App\Models\Report;
use App\Models\Story;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Users;
use App\Models\VerifyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class UsersController extends Controller
{
    function addCoinsToUserWalletFromAdmin(Request $request){
        $result = Users::where('id', $request->id)->increment('wallet', $request->coins);
        if ($result) {
			$response['success'] = 1;
		} else {
			$response['success'] = 0;
		}
		echo json_encode($response);
    }

    function logOutUser(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();

        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        $user->device_token = null;
        $user->save();

        return response()->json(['status' => true, 'message' => 'User logged out successfully !']);
    }

    function fetchUsersByCordinates(Request $request)
    {
        $rules = [
            'lat' => 'required',
            'long' => 'required',
            'km' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $users = Users::with('images')->where('is_block', 0)->where('is_fake', 0)->where('show_on_map', 1)->where('anonymous', 0)->get();

        $usersData = [];
        foreach ($users as $user) {

            $distance = Myfunction::point2point_distance($request->lat, $request->long, $user->lattitude, $user->longitude, "K", $request->km);
            if ($distance) {
                array_push($usersData, $user);
            }
        }
        return response()->json(['status' => true, 'message' => 'Data fetched successfully !', 'data' => $usersData]);
    }

    function addUserImage(Request $req)
    {
        $img = new Images();
        $file = $req->file('image');
        $path = GlobalFunction::saveFileAndGivePath($file);
        $img->image = $path;
        $img->user_id = $req->id;
        $img->save();

        return json_encode([
            'status' => true,
            'message' => 'Image Added successfully!',
        ]);
    }

    function deleteUserImage($imgId)
    {
        $img = Images::find($imgId);

        $imgCount = Images::where('user_id', $img->user_id)->count();
        if ($imgCount == 1) {
            return json_encode([
                'status' => false,
                'message' => 'Minimum one image is required !',
            ]);
        }

        unlink(storage_path('app/public/' . $img->image));
        $img->delete();
        return json_encode([
            'status' => true,
            'message' => 'Image Deleted successfully!',
        ]);
    }

    function updateUser(Request $req)
    {
        $result = Users::where('id', $req->id)->update([
            "fullname" => $req->fullname,
            "age" => $req->age,
            "password" => $req->password,
            "bio" => $req->bio,
            "about" => $req->about,
            "instagram" => $req->instagram,
            "youtube" => $req->youtube,
            "facebook" => $req->facebook,
            "live" => $req->live,
        ]);

        return json_encode([
            'status' => true,
            'message' => 'data updates successfully!',
        ]);
    }

    function test(Request $req)
    {

        $user = Users::with('liveApplications')->first();

        $intrestIds = Interest::inRandomOrder()->limit(4)->pluck('id');

        return json_encode(['data' => $intrestIds]);
    }

    function addFakeUserFromAdmin(Request $request)
    {
        $user = new Users();
        $user->identity = Myfunction::generateFakeUserIdentity();
        $user->fullname = $request->fullname;
        $user->youtube = $request->youtube;
        $user->facebook = $request->facebook;
        $user->instagram = $request->instagram;
        $user->age = $request->age;
        $user->live = $request->live;
        $user->about = $request->about;
        $user->bio = $request->bio;
        $user->password = $request->password;
        $user->gender = $request->gender;
        $user->is_verified = 2;
        $user->can_go_live = 2;
        $user->is_fake = 1;

        // Interests
        $interestIds = Interest::inRandomOrder()->limit(4)->pluck('id')->toArray();
        $user->interests = implode(',', $interestIds);

        $user->save();

        if ($request->hasFile('image')) {
            $files = $request->file('image');
            for ($i = 0; $i < count($files); $i++) {
                $image = new Images();
                $image->user_id = $user->id;
                $path = GlobalFunction::saveFileAndGivePath($files[$i]);
                $image->image = $path;
                $image->save();
            }
        }

        return response()->json(['status' => true, 'message' => "Fake user added successfully !"]);
    }

    public function getExplorePageProfileList(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!',
            ]);
        }

        $genderPreference = $user->gender_preferred;
        $ageMin = $user->age_preferred_min;
        $ageMax = $user->age_preferred_max;
        $blockedUsers = array_merge(explode(',', $user->blocked_users), [$user->id]);
        $likedUsers = LikedProfile::where('my_user_id', $request->user_id)->pluck('user_id')->toArray();

        $profilesQuery = Users::with('images')
                                ->has('images')
                                ->whereNotIn('id', $blockedUsers)
                                ->where('is_block', 0)
                                ->when($genderPreference != 3, function ($query) use ($genderPreference) {
                                    $query->where('gender', $genderPreference == 1 ? 1 : 2);
                                })
                                ->when($ageMin && $ageMax, function ($query) use ($ageMin, $ageMax) {
                                    $query->whereBetween('age', [$ageMin, $ageMax]);
                                })
                                ->inRandomOrder()
                                ->limit(15);

        $profiles = $profilesQuery->get()->each(function ($profile) use ($likedUsers) {
            $profile->is_like = in_array($profile->id, $likedUsers);
        });

        return response()->json([
            'status' => true,
            'message' => 'Data found successfully!',
            'data' => $profiles,
        ]);
    }



    function getRandomProfile(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'gender' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!',
            ]);
        }

        $blocked_users = explode(',', $user->blocked_users);
        array_push($blocked_users, $user->id);

        if ($request->gender == 3) {
            $randomUser = Users::with('images')->has('images')->whereNotIn('id', $blocked_users)->where('is_block', 0)->inRandomOrder()->first();
        } else {
            $randomUser = Users::with('images')->has('images')->whereNotIn('id', $blocked_users)->where('is_block', 0)->where('gender', $request->gender)->inRandomOrder()->first();
        }

        if ($randomUser == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!',
            ]);
        }
        
        return response()->json([
            'status' => true,
            'message' => 'data found successfully!',
            'data' => $randomUser,
        ]);
    }

    function updateUserBlockList(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json(['status' => false, 'message' => "User doesn't exists !"]);
        }

        $user->blocked_users = $request->blocked_users;
        $user->save();

        $data = Users::with('images')->where('id', $request->user_id)->first();

        return response()->json(['status' => true, 'message' => "Blocklist updated successfully !", 'data' => $data]);
    }

    function deleteMyAccount(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        Images::where('user_id', $user->id)->delete();

        $likes = Like::where('user_id', $user->id)->get();
        foreach ($likes as $like) {
            $postLikeCount = Post::where('id', $like->post_id)->first();
            $postLikeCount->likes_count -= 1;
            $postLikeCount->save();
        }
        $comments = Comment::where('user_id', $user->id)->get();
        foreach ($comments as $comment) {
            $postCommentCount = Post::where('id', $comment->post_id)->first();
            $postCommentCount->comments_count -= 1;
            $postCommentCount->save();
        }


        $posts = Post::where('user_id', $user->id)->get();
        foreach ($posts as $post) {
            $postContents = PostContent::where('post_id', $post->id)->get();
            foreach ($postContents as $postContent) {
                GlobalFunction::deleteFile($postContent->content);
                GlobalFunction::deleteFile($postContent->thumbnail);
                $postContent->delete();
            }
            UserNotification::where('post_id', $post->id)->delete();
            $post->delete();
        }

        $stories = Story::where('user_id', $user->id)->get();
        foreach ($stories as $story) {
            GlobalFunction::deleteFile($story->content);
            $story->delete();
        }


        UserNotification::where('user_id', $user->id)->delete();
        LiveApplications::where('user_id', $user->id)->delete();
        LiveHistory::where('user_id', $user->id)->delete();
        RedeemRequest::where('user_id', $user->id)->delete();
        VerifyRequest::where('user_id', $user->id)->delete();
        Report::where('user_id', $user->id)->delete();
        UserNotification::where('my_user_id', $user->id)->delete();
        UserNotification::where('my_user_id', $user->id)->orWhere('user_id', $user->id)->delete();
        $user->delete();

        return response()->json(['status' => true, 'message' => "Account Deleted Successfully !"]);
    }

    function rejectVerificationRequest(Request $request)
    {
        $verifyRequest = VerifyRequest::where('id', $request->verification_id)->first();
        $verifyRequest->user->is_verified = 0;
        $verifyRequest->user->save();

        GlobalFunction::deleteFile($verifyRequest->document);
        GlobalFunction::deleteFile($verifyRequest->selfie);

        $verifyRequest->delete();

        return response()->json([
            'status' => true,
            'message' => 'Reject Verification Request',
        ]);
    }

    function approveVerificationRequest(Request $request)
    {
        $verifyRequest = VerifyRequest::where('id', $request->verification_id)->first();
        $verifyRequest->user->is_verified = 2;
        $verifyRequest->user->save();

        GlobalFunction::deleteFile($verifyRequest->document);
        GlobalFunction::deleteFile($verifyRequest->selfie);

        $verifyRequest->delete();

        return response()->json([
            'status' => true,
            'message' => 'Approve Verification Request',
        ]);
    }

    public function fetchverificationRequests(Request $request)
    {
        $totalData = VerifyRequest::count();
        $rows = VerifyRequest::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = array(
            0 => 'id'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = VerifyRequest::count();
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = VerifyRequest::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  VerifyRequest::with('user')
                                    ->whereHas('user', function ($query) use ($search) {
                                        $query->Where('fullname', 'LIKE', "%{$search}%")
                                            ->orWhere('identity', 'LIKE', "%{$search}%");
                                    })
                                    ->offset($start)
                                    ->limit($limit)
                                    ->orderBy($order, $dir)
                                    ->get();
            $totalFiltered = VerifyRequest::with('user')
                                            ->whereHas('user', function ($query) use ($search) {
                                                $query->Where('fullname', 'LIKE', "%{$search}%")
                                                    ->orWhere('identity', 'LIKE', "%{$search}%");
                                            })
                                            ->count();
        }
        $data = array();
        foreach ($result as $item) {
 
            $imgUrl = "http://placehold.jp/150x150.png"; // Default placeholder image URL
    
            if ($item->user->images->isNotEmpty() && $item->user->images[0]->image != null) {
                $imgUrl = asset('storage/' . $item->user->images[0]->image);
            }

            $image = '<img src="'.$imgUrl.'" width="50" height="50">';

            $selfieUrl = "public/storage/" . $item->selfie;
            $selfie = '<img style="cursor: pointer;" class="img-preview" rel="' . $selfieUrl . '" src="' . $selfieUrl . '" width="50" height="50">';

            $docUrl = "public/storage/" . ($item->document);
            $document = '<img style="cursor: pointer;" class="img-preview" rel="' . $docUrl . '" src="' . $docUrl . '" width="50" height="50">';

            $approve = '<a href=""class=" btn btn-success text-white approve ml-2" rel=' . $item->id . ' >' . __("Approve") . '</a>';
            $reject = '<a href=""class=" btn btn-danger text-white reject ml-2" rel=' . $item->id . ' >' . __("Reject") . '</a>';

            $action = '<span class="float-end d-flex">' . $approve . $reject . ' </span>';
           
            $data[] = array(
                $image,
                $selfie,
                $document,
                $item->document_type,
                $item->fullname,
                $item->user->identity,
                $action
            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }

    function verificationrequests()
    {
        return view('verificationrequests');
    }

    function applyForVerification(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'document' => 'required',
            'document_type' => 'required',
            'selfie' => 'required',
            'fullname' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        if ($user->is_verified == 1) {
            return response()->json([
                'status' => false,
                'message' => 'The request has been submitted already!',
            ]);
        }
        if ($user->is_verified == 2) {
            return response()->json([
                'status' => false,
                'message' => 'This user is already verified !',
            ]);
        }

        $verifyReq = new VerifyRequest();
        $verifyReq->user_id = $request->user_id;
        $verifyReq->document_type = $request->document_type;
        $verifyReq->fullname = $request->fullname;
        $verifyReq->status = 0;

        $verifyReq->document = GlobalFunction::saveFileAndGivePath($request->document);
        $verifyReq->selfie = GlobalFunction::saveFileAndGivePath($request->selfie);

        $verifyReq->save();

        $user->is_verified = 1;
        $user->save();

        $user['images'] = Images::where('user_id', $request->user_id)->get();

        return response()->json([
            'status' => true,
            'message' => "Verification request submitted successfully !",
            'data' => $user
        ]);
    }

    public function updateLikedProfile(Request $request)
    {
        $rules = [
            'my_user_id' => 'required',
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $user = Users::where('id', $request->user_id)->first();
        $my_user = Users::where('id', $request->my_user_id)->first();

        if (!$user || !$my_user) {
            return response()->json([
                'status' => false,
                'message' => !$user ? 'User not found!' : 'Data user not found!',
            ]);
        }

        $fetchLikedProfile = LikedProfile::where('my_user_id', $request->my_user_id)
                                        ->where('user_id', $request->user_id)
                                        ->first();

        $notificationExists = UserNotification::where('user_id', $request->user_id)
                                            ->where('my_user_id', $request->my_user_id)
                                            ->where('type', Constants::notificationTypeLikeProfile)
                                            ->first();

        if ($fetchLikedProfile) {
            $fetchLikedProfile->delete();
            $notificationExists?->delete();

            return response()->json(['status' => true, 'message' => 'Profile disliked!']);
        } else {
            $likedProfile = new LikedProfile();
            $likedProfile->my_user_id = (int) $request->my_user_id;
            $likedProfile->user_id = (int) $request->user_id;
            $likedProfile->save();

            if (!$notificationExists) {
                $userNotification = new UserNotification();
                $userNotification->user_id = (int) $user->id;
                $userNotification->my_user_id = (int) $my_user->id;
                $userNotification->type = Constants::notificationTypeLikeProfile;
                $userNotification->save();

                if ($user->id != $my_user->id && $user->is_notification) {
                    $message = "{$my_user->fullname} has liked your profile, you should check their profile!";
                    Myfunction::sendPushToUser(env('APP_NAME'), $message, $user->device_token);
                }

            }

            return response()->json([
                'status' => true,
                'message' => 'Update Liked Profile Successfully!',
                'data' => $likedProfile
            ]);
        }
    }

    function fetchBlockedProfiles(Request $request)
    {

        $rules = [
            'user_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();

        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        $array = explode(',', $user->blocked_users);
        $data = Users::whereIn('id', $array)->where('is_block', 0)->with('images')->has('images')->get();
        $data = $data->reverse()->values();

        return json_encode([
            'status' => true,
            'message' => 'blocked profiles fetched successfully!',
            'data' => $data
        ]);
    }

    function fetchLikedProfiles(Request $request)
    {
        $rules = [
            'user_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        $likedProfiles = LikedProfile::where('my_user_id', $request->user_id)
                                    ->with('user')
                                    ->whereRelation('user' ,'is_block', 0)
                                    ->has('user.images')
                                    ->with('user.images')
                                    ->orderBy('id', 'DESC')
                                    ->get()
                                    ->pluck('user');

        foreach ($likedProfiles as $likedProfile) {
            $likedProfile->is_like = true;
        }

        return response()->json([
            'status' => true,
            'message' => 'profiles fetched successfully!',
            'data' => $likedProfiles
        ]);
    }

    function fetchSavedProfiles(Request $request)
    {

        $rules = [
            'user_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        $array = explode(',', $user->savedprofile);
        $data =  Users::whereIn('id', $array)->where('is_block', 0)->has('images')->with('images')->get();
        $data = $data->reverse()->values();

        return response()->json([
            'status' => true,
            'message' => 'Fetched Saved Profiles Successfully!',
            'data' => $data
        ]);
    }

    function allowLiveToUser(Request $request)
    {
        $user = Users::where('id', $request->user_id)->first();

        if ($user) {
            $user->can_go_live = 2;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => "This user is allowed to go live.",
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

    }

    function restrictLiveToUser(Request $request)
    {
        $user = Users::where('id', $request->user_id)->first();

        if ($user) {
            $user->can_go_live = 0;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => "Restrict Live Access to User.",
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

    }

    function increaseStreamCountOfUser(Request $request)
    {
        $rules = [
            'user_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();

        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        $user->total_streams += 1;
        $result = $user->save();

        if ($result) {
            return json_encode([
                'status' => true,
                'message' => 'Stream count increased successfully',
                'total_streams' => $user->total_streams
            ]);
        } else {
            return json_encode([
                'status' => false,
                'message' => 'something went wrong!',

            ]);
        }
    }

    function minusCoinsFromWallet(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'amount' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();

        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        if ($user->wallet < $request->amount) {
            return json_encode([
                'status' => false,
                'message' => 'No enough coins in the wallet!',
                'wallet' => $user->wallet,
            ]);
        }

        $user->wallet -= $request->amount;
        $result = $user->save();

        if ($result) {
            return json_encode([
                'status' => true,
                'message' => 'coins deducted from wallet successfully',
                'wallet' => $user->wallet,
                'total_collected' => $user->total_collected,
            ]);
        } else {
            return json_encode([
                'status' => false,
                'message' => 'something went wrong!',

            ]);
        }
    }

    function addCoinsToWallet(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'amount' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();

        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        $user->wallet  += $request->amount;
        $user->total_collected += $request->amount;
        $result = $user->save();

        if ($result) {
            return json_encode([
                'status' => true,
                'message' => 'coins added to wallet successfully',
                'wallet' => $user->wallet,
                'total_collected' => $user->total_collected,
            ]);
        } else {
            return json_encode([
                'status' => false,
                'message' => 'something went wrong!',

            ]);
        }
    }

    function updateLiveStatus(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'state' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        $user->is_live_now = $request->state;
        $user->save();

        $data = Users::with('images')->has('images')->where('id', $request->user_id)->first();

        return json_encode([
            'status' => true,
            'message' => 'is_live_now state updated successfully',
            'data' => $data
        ]);
    }

    function onOffVideoCalls(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'state' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        $user->is_video_call = $request->state;
        $user->save();

        $data = Users::with('images')->has('images')->where('id', $request->user_id)->first();

        return json_encode([
            'status' => true,
            'message' => 'is_video_call state updated successfully',
            'data' => $data
        ]);
    }

    function onOffAnonymous(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'state' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        $user->anonymous = $request->state;
        $user->save();

        $data = Users::with('images')->has('images')->where('id', $request->user_id)->first();

        return json_encode([
            'status' => true,
            'message' => 'anonymous state updated successfully',
            'data' => $data
        ]);
    }

    function onOffShowMeOnMap(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'state' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        $user->show_on_map = $request->state;
        $user->save();

        $data = Users::with('images')->has('images')->where('id', $request->user_id)->first();

        return json_encode([
            'status' => true,
            'message' => 'show_on_map state updated successfully',
            'data' => $data
        ]);
    }

    function onOffNotification(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'state' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        $user->is_notification = $request->state;
        $user->save();

        $data = Users::with('images')->has('images')->where('id', $request->user_id)->first();

        return json_encode([
            'status' => true,
            'message' => 'notification state updated successfully',
            'data' => $data
        ]);
    }

    function fetchAllUsers(Request $request)
    {

        $totalData =  Users::count();
        $rows = Users::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname'
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Users::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Users::Where('fullname', 'LIKE', "%{$search}%")
                ->orWhere('identity', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Users::where('identity', 'LIKE', "%{$search}%")
                ->orWhere('fullname', 'LIKE', "%{$search}%")
                ->count();
        }
        $data = array();
        foreach ($result as $item) {

            if ($item->is_block == 0) {
                $block  =  '<a class=" btn btn-danger text-white block" rel=' . $item->id . ' >' . __('app.Block') . '</a>';
            } else {
                $block  =  '<a class=" btn btn-success  text-white unblock " rel=' . $item->id . ' >' . __('app.Unblock') . '</a>';
            }

            if ($item->gender == 1) {
                $gender = ' <span  class="badge bg-dark text-white  ">' . __('app.Male') . '</span>';
            } else {
                $gender = '  <span  class="badge bg-dark text-white  ">' . __('app.Female') . '</span>';
            }

            if (count($item->images) > 0) {
                $image = '<img src="public/storage/' . $item->images[0]->image . '" width="50" height="50">';
            } else {
                $image = '<img src="http://placehold.jp/150x150.png" width="50" height="50">';
            }

            if ($item->can_go_live == 2) {
                $liveEligible = ' <span class="badge bg-success text-white  ">Yes</span>';;
            } else {
                $liveEligible = ' <span class="badge bg-danger text-white  ">No</span>';;
            }

            $action = '<a href="' . route('viewUserDetails', $item->id) . '"class=" btn btn-primary text-white " rel=' . $item->id . ' ><i class="fas fa-eye"></i></a>';
            $addCoin = '<a href="" data-id="' . $item->id . '" class="addCoins"><i class="i-cl-3 fas fa-plus-circle primary font-20 pointer p-l-5 p-r-5 me-2"></i></a>';

            $data[] = array(

                $image,
                $item->identity,
                $item->fullname,
                $addCoin.$item->wallet,
                $liveEligible,
                $item->age,
                $gender,
                $block,
                $action,

            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }

    function fetchStreamerUsers(Request $request)
    {
        $totalData =  Users::where('can_go_live', '=', 2)->count();
        $rows = Users::where('can_go_live', '=', 2)->orderBy('id', 'DESC')->get();


        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname'
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Users::where('can_go_live', '=', 2)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Users::where(function ($query) use ($search) {
                $query->Where('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('identity', 'LIKE', "%{$search}%");
            })
                ->where('can_go_live', '=', 2)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Users::where(function ($query) use ($search) {
                $query->Where('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('identity', 'LIKE', "%{$search}%");
            })
                ->where('can_go_live', '=', 2)
                ->orWhere('fullname', 'LIKE', "%{$search}%")
                ->count();
        }
        $data = array();
        foreach ($result as $item) {

            if ($item->is_block == 0) {
                $block  =  '<a class=" btn btn-danger text-white block" rel=' . $item->id . ' >' . __('app.Block') . '</a>';
            } else {
                $block  =  '<a class=" btn btn-success  text-white unblock " rel=' . $item->id . ' >' . __('app.Unblock') . '</a>';
            }

            if ($item->gender == 1) {
                $gender = ' <span  class="badge bg-dark text-white  ">' . __('app.Male') . '</span>';
            } else {
                $gender = '  <span  class="badge bg-dark text-white  ">' . __('app.Female') . '</span>';
            }

            if (count($item->images) > 0) {
                $image = '<img src="public/storage/' . $item->images[0]->image . '" width="50" height="50">';
            } else {
                $image = '<img src="http://placehold.jp/150x150.png" width="50" height="50">';
            }

            if ($item->can_go_live == 2) {
                $liveEligible = ' <span class="badge bg-success text-white  ">Yes</span>';;
            } else {
                $liveEligible = ' <span class="badge bg-danger text-white  ">No</span>';;
            }

            $action = '<a href="' . route('viewUserDetails', $item->id) . '"class=" btn btn-primary text-white " rel=' . $item->id . ' ><i class="fas fa-eye"></i></a>';

            $data[] = array(


                $image,
                $item->identity,
                $item->fullname,
                $liveEligible,
                $item->age,
                $gender,
                $block,
                $action,

            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }

    function fetchFakeUsers(Request $request)
    {
        $totalData =  Users::where('is_fake', '=', 1)->count();
        $rows = Users::where('is_fake', '=', 1)->orderBy('id', 'DESC')->get();


        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'fullname'
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Users::where('is_fake', '=', 1)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Users::where(function ($query) use ($search) {
                $query->Where('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('identity', 'LIKE', "%{$search}%");
            })
                ->where('is_fake', '=', 1)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Users::where(function ($query) use ($search) {
                $query->Where('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('identity', 'LIKE', "%{$search}%");
            })
                ->where('is_fake', '=', 1)
                ->orWhere('fullname', 'LIKE', "%{$search}%")
                ->count();
        }
        $data = array();
        foreach ($result as $item) {

            if ($item->is_block == 0) {
                $block  =  '<a class=" btn btn-danger text-white block" rel=' . $item->id . ' >' . __('app.Block') . '</a>';
            } else {
                $block  =  '<a class=" btn btn-success  text-white unblock " rel=' . $item->id . ' >' . __('app.Unblock') . '</a>';
            }

            if ($item->gender == 1) {
                $gender = ' <span  class="badge bg-dark text-white  ">' . __('app.Male') . '</span>';
            } else {
                $gender = '  <span  class="badge bg-dark text-white  ">' . __('app.Female') . '</span>';
            }

            if (count($item->images) > 0) {
                $image = '<img src="public/storage/' . $item->images[0]->image . '" width="50" height="50">';
            } else {
                $image = '<img src="http://placehold.jp/150x150.png" width="50" height="50">';
            }

            $action = '<a href="' . route('viewUserDetails', $item->id) . '"class=" btn btn-primary text-white " rel=' . $item->id . ' ><i class="fas fa-eye"></i></a>';

            $data[] = array(
                $image,
                $item->fullname,
                $item->identity,
                $item->password,
                $item->age,
                $gender,
                $block,
                $action,

            );
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        );
        echo json_encode($json_data);
        exit();
    }

    function generateUniqueUsername()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $username = '';
        $length = 8; 

        do {
            for ($i = 0; $i < $length; $i++) {
                $username .= $characters[rand(0, strlen($characters) - 1)];
            }

            $existingUser = Users::where('username', $username)->first();
        } while ($existingUser); 

        return $username;
    }

    function addUserDetails(Request $req)
    {

        if ($req->has('password')) {
            $data = Users::where('identity', $req->identity)->where('password', $req->password)->first();
            if ($data == null) {
                return json_encode(['status' => false, 'message' => "Incorrect Identity and Password combination"]);
            }
        }
        
        $data = Users::where('identity', $req->identity)->first();

        if ($data == null) {
            $user = new Users;
            $user->fullname = Myfunction::customReplace($req->fullname);
            $user->identity = $req->identity;
            $user->device_token = $req->device_token;
            $user->device_type = $req->device_type;
            $user->login_type = $req->login_type;
            $user->username = $this->generateUniqueUsername();

            $user->save();

            $data =  Users::with('images')->where('id', $user->id)->first();

            return response()->json([
                'status' => true, 
                'message' => __('app.UserAddSuccessful'), 
                'data' => $data
            ]);
        } else {
            Users::where('identity', $req->identity)->update([
                'device_token' => $req->device_token,
                'device_type' => $req->device_type,
                'login_type' => $req->login_type,

            ]);

            $data = Users::with('images')->where('id', $data['id'])->first();

            return response()->json(['status' => true, 'message' => __('app.UserAllReadyExists'), 'data' => $data]);
        }
    }

    function searchUsersForInterest(Request $req)
    {

        $rules = [
            'start' => 'required',
            'count' => 'required',
            'interest_id' => 'required',
        ];

        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $interestID = $req->interest_id;

        $result =  Users::with('images')
            ->Where('fullname', 'LIKE', "%{$req->keyword}%")
            ->whereRaw("find_in_set($interestID , interests)")
            ->has('images')
            ->where('is_block', 0)
            ->where('anonymous', 0)
            ->offset($req->start)
            ->limit($req->count)
            ->get();

        if (isEmpty($result)) {
            return response()->json([
                'status' => true,
                'message' => 'No data found',
                'data' => $result
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $result
        ]);
    }

    function searchUsers(Request $req)
    {

        $rules = [
            'start' => 'required',
            'count' => 'required',
        ];

        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $result =  Users::with('images')
            ->Where('fullname', 'LIKE', "%{$req->keyword}%")
            ->Where('username', 'LIKE', "%{$req->keyword}%")
            ->has('images')
            ->where('is_block', 0)
            ->where('anonymous', 0)
            ->offset($req->start)
            ->limit($req->count)
            ->get();

        if (isEmpty($result)) {
            return response()->json([
                'status' => true,
                'message' => 'No data found',
                'data' => $result
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $result
        ]);
    }

    function updateProfile(Request $req)
    {
        $user = Users::where('id', $req->user_id)->first();

        if (!$user) {
            return json_encode(['status' => false, 'message' => __('app.UserNotFound')]);
        }

        if ($req->deleteimagestitle != null) {
            foreach ($req->deleteimagestitle as $oneImageData) {
                unlink(storage_path('app/public/' . $oneImageData));
            }
        }

        if ($req->has("deleteimageids")) {
            Images::whereIn('id', $req->deleteimageids)->delete();
        }
        
        if ($req->has("fullname")) {
            $user->fullname = Myfunction::customReplace($req->fullname);
        }
        if ($req->has("username")) {
            $existingUser = Users::where('username', $req->username)
                                    ->where('id', '!=', $req->user_id)
                                    ->first();
            if ($existingUser !== null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Username is already taken',
                ]);
            }
            $user->username = Myfunction::customReplace($req->username);
        }
        if ($req->has("gender")) {
            $user->gender = $req->gender;
        }
        if ($req->has('youtube')) {
            $user->youtube = $req->youtube;
        }
        if ($req->has("instagram")) {
            $user->instagram = $req->instagram;
        }
        if ($req->has("facebook")) {
            $user->facebook = $req->facebook;
        }
        if ($req->has("live")) {
            $user->live =  Myfunction::customReplace($req->live);
        }
        if ($req->has("bio")) {
            $user->bio = Myfunction::customReplace($req->bio);
        }
        if ($req->has("about")) {
            $user->about = Myfunction::customReplace($req->about);
        }
        if ($req->has("lattitude")) {
            $user->lattitude = $req->lattitude;
        }
        if ($req->has("longitude")) {
            $user->longitude = $req->longitude;
        }
        if ($req->has("age")) {
            $user->age = $req->age;
        }
        if ($req->has("interests")) {
            $user->interests = $req->interests;
        }
        if ($req->has("gender_preferred")) {
            $user->gender_preferred = $req->gender_preferred;
        }
        if ($req->has("age_preferred_min")) {
            $user->age_preferred_min = $req->age_preferred_min;
        }
        if ($req->has("age_preferred_max")) {
            $user->age_preferred_max = $req->age_preferred_max;
        }
        $user->save();


        if ($req->hasFile('image')) {
            $files = $req->file('image');
            for ($i = 0; $i < count($files); $i++) {
                $image = new Images();
                $image->user_id = $user->id;
                $path = GlobalFunction::saveFileAndGivePath($files[$i]);
                $image->image = $path;
                $image->save();
            }
        }

        $updatedUser = Users::where('id', $user->id)->with('images')->first();

        return response()->json(['status' => true, 'message' => __('app.Updatesuccessful'), 'data' => $updatedUser]);
       
    }

    function blockUser(Request $request)
    {
        $user = Users::where('id', $request->user_id)->first();
        
        if ($user) {
            $user->is_block = Constants::blocked;
            $user->save();

            Report::where('user_id', $request->user_id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'This user has been blocked',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }

    function unblockUser(Request $request)
    {
        $user = Users::where('id', $request->user_id)->first();

        if ($user) {
            $user->is_block = Constants::unblocked;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'This user has been blocked',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }

    function viewUserDetails($id)
    {

        $data = Users::where('id', $id)->with('images')->first();

        return view('viewuser', ['data' => $data]);
    }

    function getProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::with(['images', 'stories'])->has('images')->where('id', $request->user_id)->first();
        $myUser = Users::with('images')->has('images')->where('id', $request->my_user_id)->first();
        if ($user == null || $myUser == null) {
            return response()->json([
                'status' => false,
                'message' =>  'User Not Found!',
            ]);
        }

        $followingStatus = FollowingList::whereRelation('user', 'is_block', 0)->where('user_id', $request->my_user_id)->where('my_user_id', $request->user_id)->first();
        $followingStatus2 = FollowingList::whereRelation('user', 'is_block', 0)->where('my_user_id', $request->my_user_id)->where('user_id', $request->user_id)->first();

        // koi ek bija ne follow nathi kartu to 0
        if ($followingStatus == null && $followingStatus2 == null) {
            $user->followingStatus = 0;
        }
        // same valo mane follow kar che to 1
        if ($followingStatus != null) {
            $user->followingStatus = 1;
        }
        // hu same vala ne follow karu chu to 2
        if ($followingStatus2) {
            $user->followingStatus = 2;
        }
        // banne ek bija ne follow kare to 3
        if ($followingStatus && $followingStatus2) {
            $user->followingStatus = 3;
        }

        $fetchUserisLiked = UserNotification::where('my_user_id', $request->my_user_id)
                                            ->where('user_id', $request->user_id)
                                            ->where('type', Constants::notificationTypeLikeProfile)
                                            ->first();

        if ($fetchUserisLiked) {
            $user->is_like = true;
        } else {
            $user->is_like = false;
        }
        
        return response()->json([
            'status' => true,
            'message' =>  __('app.fetchSuccessful'),
            'data' => $user,
        ]);
    }

    public function updateSavedProfile(Request $req)
    {
        $user = Users::with('images')->where('id', $req->user_id)->first();
        $user->savedprofile = $req->profiles;
        $user->save();

        return response()->json(['status' => true, 'message' => __('app.Updatesuccessful'), 'data' => $user]);
    }

    function getUserDetails(Request $request)
    {

        $data =  Users::where('identity', $request->email)->first();

        if ($data != null) {
            $data['image']  = Images::where('user_id', $data['id'])->first();
        } else {
            return response()->json(['status' => false, 'message' => __('app.UserNotFound')]);
        }
        $data['password'] = '';
        return response()->json(['status' => true, 'message' => __('app.fetchSuccessful'), 'data' => $data]);
    }

    public function followUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $fromUserQuery = Users::query();
        $toUserQuery = Users::query();

        $fromUser = $fromUserQuery->where('id', $request->my_user_id)->first();
        $toUser = $toUserQuery->where('id', $request->user_id)->first();
       
        if ($fromUser && $toUser) {
            if ($fromUser == $toUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lol you did not follow yourself',
                ]);
            } else {
                $followingList = FollowingList::where('my_user_id', $request->my_user_id)->where('user_id', $request->user_id)->first();
                if ($followingList) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User is Already in following list',
                    ]);
                } 

                    $blockUserIds = explode(',', $fromUser->blocked_users);

                    foreach ($blockUserIds as $blockUserId) {
                        if ($blockUserId == $request->user_id) {
                            return response()->json([
                                'status' => false,
                                'message' => 'You blocked this User',
                            ]);
                        }
                    }

                    $following = new FollowingList();
                    $following->my_user_id = (int) $request->my_user_id;
                    $following->user_id = (int) $request->user_id;
                    $following->save();

                    $followingCount = $fromUserQuery->where('id', $request->my_user_id)->first();
                    $followingCount->following += 1;
                    $followingCount->save();

                    $followersCount = $toUserQuery->where('id', $request->user_id)->first();
                    $followersCount->followers += 1;
                    $followersCount->save();
 
                    if ($toUser->is_notification == 1) {
                        $notificationDesc = $fromUser->fullname . ' has stared following you.';
                        Myfunction::sendPushToUser(env('APP_NAME'), $notificationDesc, $toUser->device_token);
                    }
                    
                    $updatedUser = Users::where('id', $request->user_id)->first();
                    
                    $updatedUser->images;
                    
                    $following->user = $updatedUser;
                    
                    $type = Constants::notificationTypeFollow;

                    $userNotification = new UserNotification();
                    $userNotification->my_user_id = (int) $request->my_user_id;
                    $userNotification->user_id = (int) $request->user_id;
                    $userNotification->type = $type;
                    $userNotification->save();

                    return response()->json([
                        'status' => true,
                        'message' => 'User Added in Following List',
                        'data' => $following, 
                    ]);
                
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
     
    }

    public function fetchFollowingList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->my_user_id)->first();
        $blockUserIds = explode(',', $user->blocked_users);

        $fetchFollowingList = FollowingList::whereRelation('user', 'is_block', 0)
                                            ->whereNotIn('user_id', $blockUserIds)
                                            ->where('my_user_id', $request->my_user_id)
                                            // ->with('user', 'user.images')
                                            ->with(['user' => function ($query) {
                                                $query->whereHas('images');
                                            }, 'user.images'])
                                            ->offset($request->start)
                                            ->limit($request->limit)
                                            ->get()
                                            ->pluck('user');
 
        return response()->json([
            'status' => true,
            'message' => 'Fetch Following List',
            'data' => $fetchFollowingList,
        ]);
    }

    public function fetchFollowersList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $fetchFollowersList = FollowingList::where('user_id', $request->user_id)
                                            ->whereNotIn('my_user_id', function ($query) use ($request) {
                                                $query->select('id')
                                                    ->from('users')
                                                    ->whereRaw("FIND_IN_SET(?, blocked_users)", [$request->user_id]);
                                            })
                                            ->with('followerUser', 'followerUser.images')
                                            ->offset($request->start)
                                            ->limit($request->limit)
                                            ->get()
                                            ->pluck('followerUser');

            return response()->json([
                'status' => true,
                'message' => 'Fetch Followers List',
                'data' => $fetchFollowersList,
            ]);
    }

    public function unfollowUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }


        $fromUserQuery = Users::query();
        $toUserQuery = Users::query();

        $fromUser = $fromUserQuery->where('id', $request->my_user_id)->first();
        $toUser = $toUserQuery->where('id', $request->user_id)->first();

        if ($fromUser && $toUser) {
            if ($fromUser == $toUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lol You did not Remove yourself, Bcz You dont follow yourself',
                ]);
            } else {
                $followingList = FollowingList::where('my_user_id', $request->my_user_id)->where('user_id', $request->user_id)->first();
                if ($followingList) {
                    $followingCount = $fromUserQuery->where('id', $request->my_user_id)->first();
                    $followingCount->following = max(0, $followingCount->following - 1);
                    $followingCount->save();

                    $followersCount = $toUserQuery->where('id', $request->user_id)->first();
                    $followersCount->followers = max(0, $followersCount->followers - 1);;
                    $followersCount->save();

                    $userNotification = UserNotification::where('my_user_id', $request->my_user_id)
                                                            ->where('user_id', $request->user_id)
                                                            ->where('type', Constants::notificationTypeFollow)
                                                            ->get();
                    $userNotification->each->delete();

                    $followingList->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Unfollow user',
                        'data' => $followingList,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'User Not Found',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function fetchHomePageData(Request $request)
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
            
            $blockUserIds = explode(',', $user->block_user_ids);

            $followingUsers = FollowingList::where('my_user_id', $request->my_user_id)
                                        ->whereRelation('story', 'created_at', '>=', now()->subDay()->toDateTimeString())
                                        ->with('user', 'user.images')
                                        ->whereRelation('user', 'is_block', 0)
                                        ->get()
                                        ->pluck('user');

            foreach($followingUsers as $followingUser) {
                $stories = Story::where('user_id', $followingUser->id)
                                ->where('created_at', '>=', now()->subDay()->toDateTimeString())
                                ->get();
                                
                foreach ($stories as $story) {
                    $story->storyView = $story->view_by_user_ids ? in_array($request->my_user_id, explode(',', $story->view_by_user_ids)) : false;
                }
                $followingUser->stories = $stories;
            }

            $fetchPosts = Post::with('content')
                                ->inRandomOrder()
                                ->with(['user','user.stories','user.images'])
                                ->whereRelation('user', 'is_block', 0)
                                ->whereNotIn('user_id', array_merge($blockUserIds))
                                ->limit(10)
                                ->get();
           

            if (!$fetchPosts->isEmpty()) {

                foreach ($fetchPosts as $fetchPost) {
                    $isPostLike = Like::where('user_id', $request->my_user_id)->where('post_id', $fetchPost->id)->first();
                    if ($isPostLike) {
                        $fetchPost->is_like = 1;
                    } else {
                        $fetchPost->is_like = 0;
                    }
                }
                
                return response()->json([
                    'status' => true,
                    'message' => 'Fetch posts',
                    'data' =>  [
                        'users_stories' => $followingUsers,
                        'posts' => $fetchPosts,
                    ]

                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Posts not Available',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Fetch Home Page Data Successfully',
                'data' =>  [
                    'users_stories' => $followingUser,
                    'posts' => $fetchPosts,
                ]
            ]);



        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }


    }

    public function deleteUserFromAdmin(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();
        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        Images::where('user_id', $user->id)->delete();

        $likes = Like::where('user_id', $user->id)->get();
        foreach ($likes as $like) {
            $postLikeCount = Post::where('id', $like->post_id)->first();
            $postLikeCount->likes_count -= 1;
            $postLikeCount->save();
            $like->delete();
        }
        $comments = Comment::where('user_id', $user->id)->get();
        foreach ($comments as $comment) {
            $postCommentCount = Post::where('id', $comment->post_id)->first();
            $postCommentCount->comments_count -= 1;
            $postCommentCount->save();
            $comment->delete();
        }

        $posts = Post::where('user_id', $user->id)->get();
        foreach ($posts as $post) {
            $postContents = PostContent::where('post_id', $post->id)->get();
            foreach ($postContents as $postContent) {
                GlobalFunction::deleteFile($postContent->content);
                GlobalFunction::deleteFile($postContent->thumbnail);
                $postContent->delete();
            }

            UserNotification::where('post_id', $post->id)->delete();

            $post->delete();
        }
        
        $stories = Story::where('user_id', $user->id)->get();
        foreach ($stories as $story) {
            GlobalFunction::deleteFile($story->content);
            $story->delete();
        }

        $followerList = FollowingList::where('my_user_id', $user->id)->get();
        foreach ($followerList as $follower) {
            $followerUser = User::where('id', $follower->user_id)->first();
            $followerUser->followers -= 1;
            $followerUser->save();

            $follower->delete();
        }

        $followingList = FollowingList::where('user_id', $user->id)->get();
        foreach ($followingList as $following) {
            $followingUser = User::where('id', $following->user_id)->first();
            $followingUser->following -= 1;
            $followingUser->save();

            $following->delete();
        }

        
        UserNotification::where('user_id', $user->id)->delete();
        LiveApplications::where('user_id', $user->id)->delete();
        LiveHistory::where('user_id', $user->id)->delete();
        RedeemRequest::where('user_id', $user->id)->delete();
        VerifyRequest::where('user_id', $user->id)->delete();
        Report::where('user_id', $user->id)->delete();
        UserNotification::where('my_user_id', $user->id)->delete();
        UserNotification::where('my_user_id', $user->id)->orWhere('user_id', $user->id)->delete();
        $user->delete();

        return response()->json(['status' => true, 'message' => "Account Deleted Successfully !"]);
    }

}
